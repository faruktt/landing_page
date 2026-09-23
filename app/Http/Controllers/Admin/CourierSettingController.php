<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierSetting;
use App\Services\PathaoCourierClient;
use App\Services\SteadfastCourierClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CourierSettingController extends Controller
{
    public function index(): View
    {
        $steadfast = CourierSetting::getSetting(CourierSetting::COURIER_STEADFAST);
        $pathao = CourierSetting::getSetting(CourierSetting::COURIER_PATHAO);

        return view('admin.couriers.index', compact('steadfast', 'pathao'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Steadfast
            'steadfast_is_active' => ['nullable', 'boolean'],
            'steadfast_api_key' => ['nullable', 'string', 'max:500'],
            'steadfast_secret_key' => ['nullable', 'string', 'max:500'],

            // Pathao
            'pathao_is_active' => ['nullable', 'boolean'],
            'pathao_client_id' => ['nullable', 'string', 'max:255'],
            'pathao_client_secret' => ['nullable', 'string', 'max:500'],
            'pathao_username' => ['nullable', 'string', 'max:255'],
            'pathao_password' => ['nullable', 'string', 'max:255'],
            'pathao_store_id' => ['nullable', 'string', 'max:100'],
            'pathao_environment' => ['nullable', 'in:production,sandbox'],
        ]);

        // 1. Update Steadfast
        $steadfast = CourierSetting::getSetting(CourierSetting::COURIER_STEADFAST);
        $steadfast->update([
            'is_active' => $request->boolean('steadfast_is_active'),
            'api_key' => $validated['steadfast_api_key'] ?? null,
            'secret_key' => $validated['steadfast_secret_key'] ?? null,
        ]);

        // 2. Update Pathao
        $pathao = CourierSetting::getSetting(CourierSetting::COURIER_PATHAO);
        $pathao->update([
            'is_active' => $request->boolean('pathao_is_active'),
            'client_id' => $validated['pathao_client_id'] ?? null,
            'client_secret' => $validated['pathao_client_secret'] ?? null,
            'username' => $validated['pathao_username'] ?? null,
            'password' => $validated['pathao_password'] ?? null,
            'store_id' => $validated['pathao_store_id'] ?? null,
            'environment' => $validated['pathao_environment'] ?? 'production',
        ]);

        return redirect()->route('admin.couriers.index')->with('status', 'কুরিয়ার সেটিংস সফলভাবে আপডেট হয়েছে।');
    }

    /**
     * Test and fetch Pathao stores for easy store_id configuration.
     */
    public function fetchPathaoStores(Request $request, PathaoCourierClient $client): JsonResponse
    {
        try {
            $stores = $client->getStores();
            return response()->json([
                'success' => true,
                'stores' => $stores,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Test Steadfast credentials.
     */
    public function testSteadfast(Request $request, SteadfastCourierClient $client): JsonResponse
    {
        try {
            $balance = $client->getBalance();
            return response()->json([
                'success' => true,
                'data' => $balance,
                'message' => 'Steadfast সংযোগ সফল হয়েছে!',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
