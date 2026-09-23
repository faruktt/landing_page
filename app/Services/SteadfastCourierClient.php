<?php

namespace App\Services;

use App\Models\CourierSetting;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SteadfastCourierClient
{
    private const BASE_URL = 'https://portal.packzy.com/api/v1';

    private function getCredentials(): array
    {
        $setting = CourierSetting::where('courier', CourierSetting::COURIER_STEADFAST)->first();

        $apiKey = $setting?->api_key ?: config('services.steadfast.api_key');
        $secretKey = $setting?->secret_key ?: config('services.steadfast.secret_key');

        if (empty($apiKey) || empty($secretKey)) {
            throw new RuntimeException('Steadfast API Key বা Secret Key কনফিগার করা নেই। কুরিয়ার সেটিংস চেক করুন।');
        }

        return [
            'api_key' => $apiKey,
            'secret_key' => $secretKey,
        ];
    }

    private function headers(): array
    {
        $creds = $this->getCredentials();

        return [
            'Api-Key' => $creds['api_key'],
            'Secret-Key' => $creds['secret_key'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function createOrder(Order $order): array
    {
        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->timeout(12)
            ->post(self::BASE_URL.'/create_order', [
                'invoice' => $order->order_number,
                'recipient_name' => $order->customer_name,
                'recipient_phone' => $order->phone,
                'recipient_address' => $order->address.', '.$order->district,
                'cod_amount' => (float) $order->due_amount,
                'note' => 'Payment method: '.strtoupper($order->payment_method),
            ]);

        if (! $response->successful()) {
            $err = $response->json();
            $msg = $err['message'] ?? $err['errors'] ?? $response->body();
            if (is_array($msg)) {
                $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            }
            throw new RuntimeException('Steadfast-এ অর্ডার পাঠানো যায়নি: '.$msg);
        }

        $data = $response->json();

        if (! isset($data['consignment'])) {
            throw new RuntimeException('Steadfast থেকে অপ্রত্যাশিত রেসপন্স পাওয়া গেছে।');
        }

        return $data['consignment'];
    }

    public function trackByCode(string $trackingCode): array
    {
        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->timeout(10)
            ->get(self::BASE_URL.'/status_by_trackingcode/'.$trackingCode);

        if (! $response->successful()) {
            throw new RuntimeException('Steadfast থেকে ট্র্যাকিং তথ্য আনা যায়নি।');
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Steadfast থেকে অপ্রত্যাশিত রেসপন্স পাওয়া গেছে।');
        }

        return $data;
    }

    /**
     * Check current balance on Steadfast account.
     */
    public function getBalance(): array
    {
        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->timeout(10)
            ->get(self::BASE_URL.'/get_balance');

        if (! $response->successful()) {
            throw new RuntimeException('Steadfast ব্যালেন্স বা ক্রেডেনশিয়াল যাচাই করা যায়নি।');
        }

        return $response->json() ?? [];
    }
}
