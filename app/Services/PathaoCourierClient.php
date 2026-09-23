<?php

namespace App\Services;

use App\Models\CourierSetting;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class PathaoCourierClient
{
    private const PROD_URL = 'https://api-hermes.pathao.com';
    private const SANDBOX_URL = 'https://hermes-api.pathao.com';

    private function getSetting(): CourierSetting
    {
        $setting = CourierSetting::where('courier', CourierSetting::COURIER_PATHAO)->first();

        if (! $setting) {
            throw new RuntimeException('Pathao কুরিয়ার কনফিগারেশন পাওয়া যায়নি।');
        }

        if (empty($setting->client_id) || empty($setting->client_secret) || empty($setting->username) || empty($setting->password)) {
            throw new RuntimeException('Pathao Client ID, Client Secret, Username বা Password কনফিগার করা নেই। কুরিয়ার সেটিংস চেক করুন।');
        }

        return $setting;
    }

    public function getBaseUrl(): string
    {
        $setting = CourierSetting::where('courier', CourierSetting::COURIER_PATHAO)->first();
        return ($setting?->environment === 'sandbox') ? self::SANDBOX_URL : self::PROD_URL;
    }

    /**
     * Get or refresh Pathao access token.
     */
    public function getAccessToken(bool $forceFresh = false): string
    {
        $setting = $this->getSetting();
        $cacheKey = 'pathao_access_token_'.md5($setting->client_id.$setting->username);

        if ($forceFresh) {
            Cache::forget($cacheKey);
        }

        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken && ! $forceFresh) {
            return $cachedToken;
        }

        $url = $this->getBaseUrl().'/aladdin/api/v1/issue-token';

        try {
            $response = Http::withoutVerifying()
                ->asJson()
                ->acceptJson()
                ->timeout(12)
                ->post($url, [
                    'client_id' => $setting->client_id,
                    'client_secret' => $setting->client_secret,
                    'username' => $setting->username,
                    'password' => $setting->password,
                    'grant_type' => 'password',
                ]);
        } catch (Throwable $e) {
            throw new RuntimeException('Pathao সার্ভারে যোগাযোগ ব্যর্থ হয়েছে: '.$e->getMessage());
        }

        if (! $response->successful()) {
            $err = $response->json();
            $msg = $err['message'] ?? $err['error_description'] ?? $err['error'] ?? $response->body();
            if (is_array($msg)) {
                $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            }
            throw new RuntimeException('Pathao লগইন/টোকেন ব্যর্থ হয়েছে: '.$msg);
        }

        $data = $response->json();
        $token = $data['access_token'] ?? null;
        $expiresIn = (int) ($data['expires_in'] ?? 86400);

        if (! $token) {
            throw new RuntimeException('Pathao রেসপন্সে access_token পাওয়া যায়নি।');
        }

        // Cache for almost the whole duration minus safety buffer
        $ttl = max(60, $expiresIn - 3600);
        Cache::put($cacheKey, $token, $ttl);

        return $token;
    }

    private function authenticatedRequest()
    {
        $token = $this->getAccessToken();

        return Http::withoutVerifying()
            ->withToken($token)
            ->asJson()
            ->acceptJson()
            ->timeout(15);
    }

    /**
     * Get list of stores in merchant's Pathao account.
     */
    public function getStores(): array
    {
        $url = $this->getBaseUrl().'/aladdin/api/v1/stores';
        $response = $this->authenticatedRequest()->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Pathao স্টোর তালিকা আনা যায়নি: '.$response->body());
        }

        $data = $response->json();
        return $data['data']['data'] ?? $data['data'] ?? [];
    }

    /**
     * Get list of cities in Bangladesh from Pathao.
     */
    public function getCities(): array
    {
        return Cache::remember('pathao_cities_list', 86400, function () {
            $url = $this->getBaseUrl().'/aladdin/api/v1/countries/1/city-list';
            $response = $this->authenticatedRequest()->get($url);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            return $data['data']['data'] ?? $data['data'] ?? [];
        });
    }

    /**
     * Get zones for a given city ID.
     */
    public function getZones(int $cityId): array
    {
        $cacheKey = 'pathao_zones_city_'.$cityId;
        return Cache::remember($cacheKey, 86400, function () use ($cityId) {
            $url = $this->getBaseUrl().'/aladdin/api/v1/cities/'.$cityId.'/zone-list';
            $response = $this->authenticatedRequest()->get($url);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            return $data['data']['data'] ?? $data['data'] ?? [];
        });
    }

    /**
     * Create parcel order in Pathao.
     */
    public function createOrder(Order $order, array $params = []): array
    {
        $setting = $this->getSetting();
        $storeId = $params['store_id'] ?? $setting->store_id;

        if (empty($storeId)) {
            // Try fetching default store
            $stores = $this->getStores();
            if (! empty($stores[0]['store_id'])) {
                $storeId = $stores[0]['store_id'];
            } else {
                throw new RuntimeException('Pathao Store ID নির্দিষ্ট করা নেই। কুরিয়ার সেটিংসে Store ID প্রদান করুন।');
            }
        }

        // City & Zone resolution
        $cityId = $params['city_id'] ?? $params['recipient_city'] ?? null;
        $zoneId = $params['zone_id'] ?? $params['recipient_zone'] ?? null;

        // If not provided, try to match by order district
        if (! $cityId || ! $zoneId) {
            $matched = $this->matchCityAndZone($order->district);
            $cityId = $cityId ?: ($matched['city_id'] ?? 1); // 1 = Dhaka default
            $zoneId = $zoneId ?: ($matched['zone_id'] ?? 1);
        }

        $itemsSummary = $order->items->pluck('product_name')->implode(', ') ?: 'Goods';
        $itemsCount = max(1, (int) $order->items->sum('quantity'));

        $payload = [
            'store_id' => (int) $storeId,
            'merchant_order_id' => $order->order_number,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $order->phone,
            'recipient_address' => $order->address.', '.$order->district,
            'recipient_city' => (int) $cityId,
            'recipient_zone' => (int) $zoneId,
            'delivery_type' => 48, // 48: Standard delivery
            'item_type' => 2, // 2: Parcel
            'special_instruction' => 'Order: '.$order->order_number,
            'item_quantity' => $itemsCount,
            'item_weight' => 0.5,
            'amount_to_collect' => (float) $order->due_amount,
            'item_description' => substr($itemsSummary, 0, 200),
        ];

        $url = $this->getBaseUrl().'/aladdin/api/v1/orders';

        try {
            $response = $this->authenticatedRequest()->post($url, $payload);
        } catch (Throwable $e) {
            throw new RuntimeException('Pathao-এ অর্ডার পাঠানোর সময় ত্রুটি: '.$e->getMessage());
        }

        if (! $response->successful()) {
            $err = $response->json();
            $msg = $err['message'] ?? $err['errors'] ?? $response->body();
            if (is_array($msg)) {
                $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            }
            throw new RuntimeException('Pathao-এ অর্ডার পাঠানো যায়নি: '.$msg);
        }

        $data = $response->json();
        $consignment = $data['data'] ?? [];

        if (empty($consignment['consignment_id'])) {
            throw new RuntimeException('Pathao রেসপন্সে consignment_id পাওয়া যায়নি।');
        }

        return $consignment;
    }

    /**
     * Best-effort city & zone matching by district name.
     */
    private function matchCityAndZone(?string $district): array
    {
        if (empty($district)) {
            return ['city_id' => 1, 'zone_id' => 1];
        }

        $districtClean = mb_strtolower(trim($district));
        $cities = $this->getCities();

        $matchedCityId = 1; // Dhaka fallback
        foreach ($cities as $city) {
            $cityName = mb_strtolower($city['city_name'] ?? '');
            if (str_contains($cityName, $districtClean) || str_contains($districtClean, $cityName)) {
                $matchedCityId = (int) $city['city_id'];
                break;
            }
        }

        $zones = $this->getZones($matchedCityId);
        $matchedZoneId = ! empty($zones[0]['zone_id']) ? (int) $zones[0]['zone_id'] : 1;

        return [
            'city_id' => $matchedCityId,
            'zone_id' => $matchedZoneId,
        ];
    }

    /**
     * Track Pathao order by consignment ID.
     */
    public function trackByConsignment(string $consignmentId): array
    {
        $url = $this->getBaseUrl().'/aladdin/api/v1/orders/'.$consignmentId.'/info';
        $response = $this->authenticatedRequest()->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Pathao থেকে ট্র্যাকিং তথ্য আনা যায়নি।');
        }

        $data = $response->json();
        return $data['data'] ?? $data;
    }
}
