<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class BdCourierService
{
    private const CACHE_TTL_SECONDS = 1800; // 30 minutes

    /**
     * Check phone number against BD Courier API.
     *
     * @param string $phone
     * @param bool $fresh If true, bypasses cached data
     * @return array
     */
    public function check(string $phone, bool $fresh = false): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if (! $this->isValidPhone($normalizedPhone)) {
            throw new RuntimeException('সঠিক ১১ ডিজিটের বাংলাদেশি মোবাইল নম্বর দিন (যেমন: 017XXXXXXXX)।');
        }

        $cacheKey = 'bdcourier_check_'.$normalizedPhone;

        if ($fresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($normalizedPhone) {
            return $this->fetchFromApi($normalizedPhone);
        });
    }

    /**
     * Standardize phone number to 11 digits (01XXXXXXXXX).
     */
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (preg_match('/(01[3-9]\d{8})$/', $digits, $matches)) {
            return $matches[1];
        }

        return $digits;
    }

    /**
     * Validate 11-digit Bangladeshi mobile number.
     */
    public function isValidPhone(string $phone): bool
    {
        return (bool) preg_match('/^01[3-9]\d{8}$/', $phone);
    }

    /**
     * Call BD Courier API and format response.
     */
    private function fetchFromApi(string $phone): array
    {
        $rawUrl = config('services.bdcourier.url') ?: 'https://api.bdcourier.com/courier-check';

        // Auto append /courier-check if user provided base URL
        if (! str_contains($rawUrl, '/courier-check')) {
            $url = rtrim($rawUrl, '/') . '/courier-check';
        } else {
            $url = $rawUrl;
        }

        $apiKey = config('services.bdcourier.api_key') ?: env('BDCOURIER_API_KEY');

        if (empty($apiKey)) {
            throw new RuntimeException('BD Courier API Key কনফিগার করা নেই। .env ফাইলে BDCOURIER_API_KEY দিন।');
        }

        try {
            $response = Http::withToken($apiKey)
                ->withoutVerifying()
                ->acceptJson()
                ->timeout(12)
                ->get($url, ['phone' => $phone]);
        } catch (Throwable $e) {
            throw new RuntimeException('BD Courier সার্ভারের সাথে যোগাযোগ করা যায়নি: '.$e->getMessage());
        }

        if (! $response->successful()) {
            $errorData = $response->json();
            $message = $errorData['message'] ?? 'BD Courier থেকে তথ্য আনা যায়নি (Status: '.$response->status().')';
            throw new RuntimeException($message);
        }

        $payload = $response->json();

        if (($payload['status'] ?? '') !== 'success' && ! isset($payload['data'])) {
            throw new RuntimeException($payload['message'] ?? 'কোনো ফলাফল পাওয়া যায়নি।');
        }

        return $this->formatResult($phone, $payload);
    }

    /**
     * Format and normalize the response payload.
     */
    private function formatResult(string $phone, array $payload): array
    {
        $rawData = $payload['data'] ?? [];
        $rawReports = $payload['reports'] ?? [];
        $rawVerdict = $payload['risk_verdict'] ?? [];

        // Extract Summary
        $summary = $rawData['summary'] ?? [
            'total_parcel' => 0,
            'success_parcel' => 0,
            'cancelled_parcel' => 0,
            'success_ratio' => 0,
        ];

        $totalParcel = (int) ($summary['total_parcel'] ?? 0);
        $successParcel = (int) ($summary['success_parcel'] ?? 0);
        $cancelledParcel = (int) ($summary['cancelled_parcel'] ?? 0);
        $successRatio = (float) ($summary['success_ratio'] ?? 0);

        // Separate couriers breakdown (everything except summary)
        $couriers = [];
        foreach ($rawData as $key => $courierData) {
            if ($key === 'summary' || ! is_array($courierData)) {
                continue;
            }

            $courierTotal = (int) ($courierData['total_parcel'] ?? 0);
            $couriers[$key] = [
                'key' => $key,
                'name' => $courierData['name'] ?? ucfirst($key),
                'logo' => $courierData['logo'] ?? null,
                'total_parcel' => $courierTotal,
                'success_parcel' => (int) ($courierData['success_parcel'] ?? 0),
                'cancelled_parcel' => (int) ($courierData['cancelled_parcel'] ?? 0),
                'success_ratio' => (float) ($courierData['success_ratio'] ?? 0),
                'has_activity' => $courierTotal > 0,
            ];
        }

        // Format reports
        $reports = [];
        foreach ($rawReports as $report) {
            $reports[] = [
                'id' => $report['id'] ?? null,
                'name' => $report['name'] ?? 'অজ্ঞাত মার্চেন্ট',
                'details' => $report['details'] ?? '',
                'created_at' => isset($report['created_at']) ? date('d M Y, h:i A', strtotime($report['created_at'])) : '',
                'courier_name' => $report['courierName'] ?? '',
                'courier_logo' => $report['courierLogo'] ?? '',
            ];
        }

        // Determine Risk Assessment
        $reportsCount = count($reports);
        $verdict = $this->determineVerdict($totalParcel, $successParcel, $cancelledParcel, $successRatio, $reportsCount, $rawVerdict);

        return [
            'phone' => $phone,
            'total_parcel' => $totalParcel,
            'success_parcel' => $successParcel,
            'cancelled_parcel' => $cancelledParcel,
            'success_ratio' => $successRatio,
            'reports_count' => $reportsCount,
            'reports' => $reports,
            'couriers' => $couriers,
            'verdict' => $verdict,
            'checked_at' => now()->format('d M Y, h:i A'),
        ];
    }

    /**
     * Determine risk verdict with contextual tags and colors.
     */
    private function determineVerdict(int $total, int $success, int $cancelled, float $ratio, int $reportsCount, array $apiVerdict): array
    {
        // If customer has 0 parcels in records
        if ($total === 0) {
            return [
                'level' => 'neutral',
                'badge' => 'নতুন কাস্টমার',
                'badge_color' => 'bg-slate-100 text-slate-700 border-slate-200',
                'tag_color' => 'text-slate-600 bg-slate-50',
                'icon' => 'fa-circle-question',
                'color' => 'slate',
                'action' => 'পূর্বে কোনো কুরিয়ার ডেলিভারি রেকর্ড নেই (নতুন ক্রেতা)',
                'reasons' => ['কোনো পূর্ববর্তী পার্সেল হিস্ট্রি পাওয়া যায়নি'],
            ];
        }

        // Has fraud reports
        if ($reportsCount > 0) {
            return [
                'level' => 'high_risk',
                'badge' => 'ঝুঁকিপূর্ণ / ফ্রড রেকর্ড',
                'badge_color' => 'bg-rose-100 text-rose-800 border-rose-200',
                'tag_color' => 'text-rose-700 bg-rose-50',
                'icon' => 'fa-triangle-exclamation',
                'color' => 'rose',
                'action' => $apiVerdict['action'] ?? 'অগ্রিম ডেলিভারি চার্জ নিয়ে নিশ্চিত হন অথবা সতর্ক থাকুন',
                'reasons' => array_merge(
                    [$reportsCount.'টি ফ্রড/অভিযোগ রিপোর্ট আছে'],
                    $ratio < 60 ? ['ডেলিভারি সফলতার হার কম ('.$ratio.'%)'] : []
                ),
            ];
        }

        // Low success ratio (< 60%)
        if ($ratio < 60) {
            return [
                'level' => 'high_risk',
                'badge' => 'উচ্চ ঝুঁকি (বেশি বাতিল)',
                'badge_color' => 'bg-red-100 text-red-700 border-red-200',
                'tag_color' => 'text-red-700 bg-red-50',
                'icon' => 'fa-circle-exclamation',
                'color' => 'red',
                'action' => 'বাতিলের হার বেশি — ফোনে ভালোভাবে যাচাই করে অর্ডার কনফার্ম করুন',
                'reasons' => [
                    'মোট '.$cancelled.'টি পার্সেল বাতিল বা রিটার্ন হয়েছে',
                    'ডেলিভারি সফলতার হার মাত্র '.$ratio.'%',
                ],
            ];
        }

        // Moderate success ratio (60% - 79%)
        if ($ratio < 80) {
            return [
                'level' => 'medium_risk',
                'badge' => 'সতর্কতার সাথে অগ্রসর হন',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'tag_color' => 'text-amber-700 bg-amber-50',
                'icon' => 'fa-circle-info',
                'color' => 'amber',
                'action' => 'কিছু পার্সেল বাতিল হয়েছে — কনফার্ম করার আগে নিশ্চিত হন',
                'reasons' => [
                    'সফলতার হার '.$ratio.'%',
                    $cancelled.'টি পার্সেল পূর্বে বাতিল হয়েছে',
                ],
            ];
        }

        // High success ratio (>= 80%)
        return [
            'level' => 'safe',
            'badge' => 'নিরাপদ কাস্টমার',
            'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'tag_color' => 'text-emerald-700 bg-emerald-50',
            'icon' => 'fa-shield-check',
            'color' => 'emerald',
            'action' => 'ডেলিভারি হিস্ট্রি চমৎকার — নিশ্চিন্তে অর্ডার পাঠানো যেতে পারে',
            'reasons' => [
                'ডেলিভারি সফলতার হার '.$ratio.'%',
                'কোনো ফ্রড রিপোর্ট নেই',
            ],
        ];
    }
}
