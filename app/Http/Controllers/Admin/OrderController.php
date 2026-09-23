<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use App\Models\CourierSetting;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\PathaoCourierClient;
use App\Services\SteadfastCourierClient;
use App\Support\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    private const STEADFAST_STATUS_KEY = 'send_to_steadfast';

    public function __construct(
        private readonly SteadfastCourierClient $steadfastCourier,
        private readonly PathaoCourierClient $pathaoCourier
    ) {
    }

    public function index(Request $request): View
    {
        if (! $request->has('status')) {
            $request->merge(['status' => 'pending']);
            $currentStatus = 'pending';
        } else {
            $statusInput = (string) $request->input('status');
            $currentStatus = ($statusInput === '' || $statusInput === 'all') ? 'all' : $statusInput;
        }

        $orders = Order::query()
            ->with('customer')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('order_number', 'like', "%{$request->search}%")
                        ->orWhere('customer_name', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%");
                });
            })
            ->when($currentStatus !== 'all', function ($q) use ($currentStatus) {
                $q->where('status', $currentStatus);
            })
            ->when($request->payment_method, fn ($q) => $q->where('payment_method', $request->payment_method))
            ->when($request->payment_status, fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Order::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $totalOrdersCount = array_sum($statusCounts);

        $orderStatuses = OrderStatus::ordered()->get();
        $activeBlockedIps = BlockedIp::where('expires_at', '>', now())->get()->keyBy('ip_address');
        $activeCouriers = CourierSetting::activeCouriers();

        return view('admin.orders.index', compact(
            'orders',
            'currentStatus',
            'statusCounts',
            'totalOrdersCount',
            'orderStatuses',
            'activeBlockedIps',
            'activeCouriers'
        ));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'payments' => fn ($q) => $q->latest('paid_at')]);
        $activeCouriers = CourierSetting::activeCouriers();

        return view('admin.orders.show', compact('order', 'activeCouriers'));
    }

    public function invoice(Order $order): View
    {
        $order->load('items.product');

        return view('admin.orders.invoice', compact('order'));
    }

    public function tracking(Order $order): View
    {
        $tracking = null;
        $error = null;
        $courier = $order->active_courier_name;

        if (! $order->hasCourierConsignment()) {
            $error = 'এই অর্ডারটি এখনো কোনো কুরিয়ারে পাঠানো হয়নি।';
        } elseif ($courier === 'pathao' || $order->pathao_consignment_id) {
            try {
                $tracking = $this->pathaoCourier->trackByConsignment($order->pathao_consignment_id);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        } else {
            try {
                $tracking = $this->steadfastCourier->trackByCode($order->steadfast_tracking_code);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return view('admin.orders.tracking', compact('order', 'tracking', 'error'));
    }

    public function sendToCourier(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'courier' => ['required', 'in:steadfast,pathao'],
        ]);

        $courier = $validated['courier'];

        if (! CourierSetting::isCourierActive($courier)) {
            $msg = ucfirst($courier).' কুরিয়ার বর্তমানে সক্রিয় (ON) নেই। কুরিয়ার সেটিংস থেকে চালু করুন।';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        try {
            if ($courier === CourierSetting::COURIER_STEADFAST) {
                $consignment = $this->steadfastCourier->createOrder($order);
                $trackingCode = $consignment['tracking_code'] ?? null;
                $consignmentId = $consignment['consignment_id'] ?? null;

                $order->update([
                    'courier_name' => 'steadfast',
                    'steadfast_consignment_id' => $consignmentId,
                    'steadfast_tracking_code' => $trackingCode,
                    'steadfast_delivery_status' => $consignment['status'] ?? 'in_review',
                ]);
            } else {
                $consignment = $this->pathaoCourier->createOrder($order, $request->all());
                $consignmentId = $consignment['consignment_id'] ?? null;
                $trackingCode = $consignmentId;

                $order->update([
                    'courier_name' => 'pathao',
                    'pathao_consignment_id' => $consignmentId,
                    'pathao_tracking_code' => $trackingCode,
                    'pathao_delivery_status' => $consignment['order_status'] ?? 'Pending',
                ]);
            }
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        $courierTitle = $courier === 'pathao' ? 'Pathao' : 'Steadfast';
        $message = "অর্ডারটি সফলভাবে {$courierTitle}-এ পাঠানো হয়েছে! ট্র্যাকিং কোড: {$trackingCode}";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'courier' => $courier,
                'tracking_code' => $trackingCode,
                'consignment_id' => $consignmentId,
            ]);
        }

        return back()->with('status', $message);
    }

    public function edit(Order $order): View
    {
        $order->load('items.product');

        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cod,bkash,rocket,nagad'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:'.OrderStatus::ordered()->pluck('key')->implode(',')],
        ]);

        $order->load('items.product');
        $subtotal = 0;

        foreach ($data['items'] as $itemData) {
            $item = $order->items->firstWhere('id', (int) $itemData['id']);

            if (! $item) {
                continue;
            }

            $newQuantity = (int) $itemData['quantity'];
            $newUnitPrice = (float) $itemData['unit_price'];
            $newSubtotal = $newQuantity * $newUnitPrice;

            if ($item->product && $newQuantity !== $item->quantity) {
                $diff = $item->quantity - $newQuantity;
                $item->product->update(['stock' => max(0, $item->product->stock + $diff)]);
            }

            $item->update([
                'quantity' => $newQuantity,
                'unit_price' => $newUnitPrice,
                'subtotal' => $newSubtotal,
            ]);

            $subtotal += $newSubtotal;
        }

        $deliveryCharge = Delivery::chargeFor($data['district']);
        $total = max(0, $subtotal + $deliveryCharge - (float) $order->discount_amount);
        $dueAmount = max(0, $total - (float) $order->paid_amount);
        $paymentStatus = $dueAmount <= 0 ? 'paid' : (((float) $order->paid_amount) > 0 ? 'partial' : 'pending');

        $customer = Customer::updateOrCreate(
            ['phone' => $data['phone']],
            [
                'name' => $data['customer_name'],
                'address' => $data['address'],
                'district' => $data['district'],
            ]
        );

        $order->update([
            'customer_id' => $customer->id,
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'district' => $data['district'],
            'payment_method' => $data['payment_method'],
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'total' => $total,
            'due_amount' => $dueAmount,
            'payment_status' => $paymentStatus,
            'status' => $data['status'] ?? $order->status,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('status', 'অর্ডার তথ্য আপডেট হয়েছে।');
    }

    public function addPayment(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        if ((float) $order->due_amount <= 0) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'এই অর্ডারে কোনো বকেয়া নেই।'], 422);
            }

            return back()->with('error', 'এই অর্ডারে কোনো বকেয়া নেই।');
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.(float) $order->due_amount],
            'method' => ['required', 'in:cash,bkash,rocket,nagad,bank'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $order) {
            $order->payments()->create([
                'amount' => $data['amount'],
                'method' => $data['method'],
                'note' => $data['note'] ?? null,
                'paid_at' => now(),
            ]);

            $newPaid = (float) $order->paid_amount + (float) $data['amount'];
            $newDue = max(0, (float) $order->total - $newPaid);

            $order->update([
                'paid_amount' => $newPaid,
                'due_amount' => $newDue,
                'payment_status' => $newDue <= 0 ? 'paid' : 'partial',
            ]);
        });

        $order->refresh();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'paid_amount' => (float) $order->paid_amount,
                'due_amount' => (float) $order->due_amount,
                'payment_status' => $order->payment_status,
            ]);
        }

        return back()->with('status', 'পেমেন্ট গ্রহণ করা হয়েছে।');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.OrderStatus::ordered()->pluck('key')->implode(',')],
        ]);

        if ($data['status'] === self::STEADFAST_STATUS_KEY && ! $order->steadfast_consignment_id) {
            try {
                $consignment = $this->steadfastCourier->createOrder($order);
            } catch (Throwable $e) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
                }

                return back()->with('error', $e->getMessage());
            }

            $order->update([
                'steadfast_consignment_id' => $consignment['consignment_id'] ?? null,
                'steadfast_tracking_code' => $consignment['tracking_code'] ?? null,
                'steadfast_delivery_status' => $consignment['status'] ?? null,
            ]);
        }

        $order->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $order->status]);
        }

        return back()->with('status', 'অর্ডার স্ট্যাটাস আপডেট হয়েছে।');
    }
}
