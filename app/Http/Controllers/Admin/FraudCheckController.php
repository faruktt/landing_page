<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BdCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class FraudCheckController extends Controller
{
    /**
     * AJAX endpoint for quick lookups from Order List and Order Details.
     */
    public function lookup(Request $request, BdCourierService $service): JsonResponse
    {
        $phone = trim((string) $request->input('phone', $request->query('phone')));
        $fresh = $request->boolean('fresh', false);

        if ($phone === '') {
            return response()->json([
                'success' => false,
                'message' => 'মোবাইল নম্বর প্রদান করুন।',
            ], 422);
        }

        try {
            $data = $service->check($phone, $fresh);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Dedicated standalone Fraud Checker page.
     */
    public function index(Request $request, BdCourierService $service): View
    {
        $result = null;
        $error = null;
        $phone = trim((string) $request->query('phone'));
        $fresh = $request->boolean('fresh', false);

        if ($phone !== '') {
            try {
                $result = $service->check($phone, $fresh);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return view('admin.fraud-check.index', compact('phone', 'result', 'error'));
    }
}
