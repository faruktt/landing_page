<?php

namespace App\Services;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SteadfastFraudChecker
{
    private const BASE_URL = 'https://steadfast.com.bd';

    private const SESSION_CACHE_KEY = 'steadfast_session_cookies';

    private const SESSION_TTL_SECONDS = 60 * 60 * 24 * 7; // 7 days, "remember me" login

    public function check(string $phone): array
    {
        $jar = $this->restoreSession() ?? $this->login();

        $data = $this->fetchFraudCheck($jar, $phone);

        if ($data === null) {
            // Cached session no longer valid on Steadfast's side, log in fresh and retry once.
            $data = $this->fetchFraudCheck($this->login(), $phone);
        }

        if ($data === null) {
            throw new RuntimeException('Steadfast থেকে তথ্য আনা যায়নি।');
        }

        return $this->formatResult($phone, $data);
    }

    private function restoreSession(): ?CookieJar
    {
        $cookies = Cache::get(self::SESSION_CACHE_KEY);

        return $cookies ? new CookieJar(false, $cookies) : null;
    }

    private function login(): CookieJar
    {
        $jar = new CookieJar();

        $loginPage = Http::withOptions(['cookies' => $jar])->get(self::BASE_URL.'/login');

        if (! $loginPage->successful()) {
            throw new RuntimeException('Steadfast লগইন পেজ লোড করা যায়নি।');
        }

        if (! preg_match('/name="_token" value="([^"]+)"/', $loginPage->body(), $matches)) {
            throw new RuntimeException('Steadfast CSRF টোকেন খুঁজে পাওয়া যায়নি।');
        }

        $loginResponse = Http::withOptions(['cookies' => $jar])
            ->asForm()
            ->withHeaders(['Referer' => self::BASE_URL.'/login'])
            ->post(self::BASE_URL.'/login', [
                '_token' => $matches[1],
                'email' => config('services.steadfast.email'),
                'password' => config('services.steadfast.password'),
                'remember' => 'on',
            ]);

        if (! $loginResponse->successful() || str_contains((string) $loginResponse->effectiveUri(), '/login')) {
            throw new RuntimeException('Steadfast লগইন ব্যর্থ হয়েছে। ইমেইল/পাসওয়ার্ড ঠিক আছে কিনা চেক করুন।');
        }

        Cache::put(self::SESSION_CACHE_KEY, $jar->toArray(), self::SESSION_TTL_SECONDS);

        return $jar;
    }

    private function fetchFraudCheck(CookieJar $jar, string $phone): ?array
    {
        $response = Http::withOptions(['cookies' => $jar])
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->get(self::BASE_URL.'/user/frauds/check/'.$phone);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        return is_array($data) ? $data : null;
    }

    private function formatResult(string $phone, array $data): array
    {
        $delivered = (int) ($data['total_delivered'] ?? 0);
        $cancelled = (int) ($data['total_cancelled'] ?? 0);
        $total = $delivered + $cancelled;
        $successRatio = $total > 0 ? round(($delivered / $total) * 100, 1) : null;

        return [
            'phone' => $phone,
            'total_delivered' => $delivered,
            'total_cancelled' => $cancelled,
            'total_parcels' => $total,
            'success_ratio' => $successRatio,
            'frauds' => $data['frauds'] ?? [],
            'risk_level' => $this->riskLevel($total, $successRatio),
        ];
    }

    private function riskLevel(int $total, ?float $successRatio): string
    {
        if ($total === 0) {
            return 'unknown';
        }

        if ($successRatio >= 80) {
            return 'low';
        }

        if ($successRatio >= 50) {
            return 'medium';
        }

        return 'high';
    }
}
