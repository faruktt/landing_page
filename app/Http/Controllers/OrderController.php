<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\Customer;
use App\Models\IncompleteOrder;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Services\SteadfastCourierClient;
use App\Services\SteadfastFraudChecker;
use App\Support\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    private const FRAUD_SUCCESS_THRESHOLD = 60;

    private const PHONE_REGEX = '/^01[3-9]\d{8}$/';

    public function __construct(
        private readonly SteadfastFraudChecker $fraudChecker,
        private readonly SteadfastCourierClient $steadfastCourier
    ) {
    }

    public function checkPhone(Request $request): JsonResponse
    {
        $phone = preg_replace('/\D/', '', (string) $request->query('phone'));

        $blocked = preg_match(self::PHONE_REGEX, $phone) === 1 && $this->isPhoneBlocked($phone);

        return response()->json(['blocked' => $blocked]);
    }

    private function isPhoneBlocked(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone);
        $clean11 = preg_match('/(01[3-9]\d{8})$/', $digits, $m) ? $m[1] : $digits;

        if (Customer::where(function ($q) use ($clean11) {
            $q->where('phone', $clean11)
                ->orWhere('phone', '88'.$clean11)
                ->orWhere('phone', '+88'.$clean11);
        })->where('is_blocked', true)->exists()) {
            return true;
        }

        try {
            $result = $this->fraudChecker->check($clean11);
        } catch (Throwable $e) {
            // Fail open: never block checkout because Steadfast is unreachable/erroring.
            return false;
        }

        return $result['total_parcels'] > 0
            && $result['success_ratio'] !== null
            && $result['success_ratio'] < self::FRAUD_SUCCESS_THRESHOLD;
    }

    /**
     * Fired by the checkout form as the customer fills fields, so an abandoned
     * checkout (phone given, order never placed) can still be followed up on.
     */
    public function saveProgress(Request $request): JsonResponse
    {
        if ($request->filled('selected_products')) {
            $selected = array_values(array_filter((array) $request->input('selected_products')));
            if (! empty($selected)) {
                $firstId = array_shift($selected);
                $quantities = (array) $request->input('quantities', []);
                $request->merge([
                    'product_id' => $firstId,
                    'quantity' => max(1, (int) ($quantities[$firstId] ?? 1)),
                    'extra_products' => $selected,
                    'extra_quantities' => $quantities,
                ]);
            }
        } elseif ($request->filled('product_id') && $request->filled('quantities.'.$request->input('product_id')) && ! $request->filled('quantity')) {
            $request->merge([
                'quantity' => max(1, (int) $request->input('quantities.'.$request->input('product_id'))),
            ]);
        }

        $request->validate([
            'product_id' => ['nullable', 'integer'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:20'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'extra_products' => ['nullable', 'array'],
            'extra_products.*' => ['integer'],
            'extra_quantities' => ['nullable', 'array'],
        ]);

        $phone = trim((string) $request->input('phone', ''));

        if ($phone === '') {
            return response()->json(['ok' => false]);
        }

        $product = Product::find($request->input('product_id'));

        if (! $product) {
            return response()->json(['ok' => false]);
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        $unitPrice = (float) ($product->sale_price ?? $product->regular_price);

        $cartSnapshot = [[
            'product_id' => $product->id,
            'name' => $product->name,
            'size' => $request->input('size') ?: null,
            'color' => $request->input('color') ?: null,
            'qty' => $quantity,
            'price' => $unitPrice,
            'subtotal' => $unitPrice * $quantity,
        ]];
        $subtotal = $unitPrice * $quantity;

        $extraProductIds = array_filter((array) $request->input('extra_products', []));
        $extraQuantities = (array) $request->input('extra_quantities', []);

        if ($extraProductIds) {
            foreach (Product::whereIn('id', $extraProductIds)->get() as $extra) {
                $extraQty = max(1, (int) ($extraQuantities[$extra->id] ?? 1));
                $extraPrice = (float) ($extra->sale_price ?? $extra->regular_price);

                $cartSnapshot[] = [
                    'product_id' => $extra->id,
                    'name' => $extra->name,
                    'qty' => $extraQty,
                    'price' => $extraPrice,
                    'subtotal' => $extraPrice * $extraQty,
                ];
                $subtotal += $extraPrice * $extraQty;
            }
        }

        $data = [
            'customer_name' => $request->input('customer_name'),
            'phone' => $phone,
            'address' => $request->input('address'),
            'district' => $request->input('district'),
            'payment_method' => $request->input('payment_method'),
            'cart_snapshot' => $cartSnapshot,
            'subtotal' => $subtotal,
            'ip_address' => $request->ip(),
        ];

        $existing = ($id = $request->session()->get('incomplete_order_id'))
            ? IncompleteOrder::find($id)
            : null;

        if ($existing) {
            $existing->update($data);
        } else {
            $existing = IncompleteOrder::create($data);
            $request->session()->put('incomplete_order_id', $existing->id);
        }

        return response()->json(['ok' => true]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('selected_products')) {
            $selected = array_values(array_filter((array) $request->input('selected_products')));
            if (! empty($selected)) {
                $firstId = array_shift($selected);
                $quantities = (array) $request->input('quantities', []);
                $request->merge([
                    'product_id' => $firstId,
                    'quantity' => max(1, (int) ($quantities[$firstId] ?? 1)),
                    'extra_products' => $selected,
                    'extra_quantities' => $quantities,
                ]);
            }
        } elseif ($request->filled('product_id') && $request->filled('quantities.'.$request->input('product_id')) && ! $request->filled('quantity')) {
            $request->merge([
                'quantity' => max(1, (int) $request->input('quantities.'.$request->input('product_id'))),
            ]);
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:'.self::PHONE_REGEX],
            'address' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cod,bkash,rocket,nagad'],
            'extra_products' => ['nullable', 'array'],
            'extra_products.*' => ['integer', 'exists:products,id'],
            'extra_quantities' => ['nullable', 'array'],
            'extra_quantities.*' => ['integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        $phoneBlocked = $this->isPhoneBlocked($data['phone']);
        $ipBlocked = BlockedIp::isBlocked($request->ip());

        if ($phoneBlocked || $ipBlocked) {
            return back()->with('order_blocked', true);
        }

        $unitPrice = (float) ($product->sale_price ?? $product->regular_price);
        $mainSubtotal = $unitPrice * $data['quantity'];

        $extraProducts = Product::whereIn('id', $data['extra_products'] ?? [])
            ->where('status', 'active')
            ->where('id', '!=', $product->id)
            ->get();

        $extraQuantities = $data['extra_quantities'] ?? [];
        $addonSubtotal = $extraProducts->sum(function ($extra) use ($extraQuantities) {
            $qty = max(1, (int) ($extraQuantities[$extra->id] ?? 1));

            return $qty * (float) ($extra->sale_price ?? $extra->regular_price);
        });
        $subtotal = $mainSubtotal + $addonSubtotal;
        $deliveryCharge = Delivery::chargeFor($data['district']);

        $discountAmount = 0;
        if (! $request->filled('selected_products')) {
            $offer = $product->bestCartOffer($subtotal);
            if ($offer && $offer->reward_type !== 'free_delivery') {
                $discountAmount = min((float) $offer->discount_amount, $subtotal + $deliveryCharge);
            }
        }

        $total = $subtotal + $deliveryCharge - $discountAmount;

        $customer = Customer::updateOrCreate(
            ['phone' => $data['phone']],
            [
                'name' => $data['customer_name'],
                'address' => $data['address'],
                'district' => $data['district'],
            ]
        );

        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => 'BN-'.strtoupper(Str::random(6)),
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'district' => $data['district'],
            'ip_address' => $request->ip(),
            'payment_method' => $data['payment_method'],
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'paid_amount' => 0,
            'due_amount' => $total,
            'payment_status' => 'pending',
            'status' => OrderStatus::defaultKey(),
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'size' => $data['size'] ?? null,
            'color' => $data['color'] ?? null,
            'quantity' => $data['quantity'],
            'unit_price' => $unitPrice,
            'subtotal' => $mainSubtotal,
        ]);

        if ($product->stock > 0) {
            $product->decrement('stock', min($data['quantity'], $product->stock));
        }

        foreach ($extraProducts as $extra) {
            $extraQty = max(1, (int) ($extraQuantities[$extra->id] ?? 1));
            $extraPrice = (float) ($extra->sale_price ?? $extra->regular_price);

            $order->items()->create([
                'product_id' => $extra->id,
                'product_name' => $extra->name,
                'quantity' => $extraQty,
                'unit_price' => $extraPrice,
                'subtotal' => $extraQty * $extraPrice,
            ]);

            if ($extra->stock > 0) {
                $extra->decrement('stock', min($extraQty, $extra->stock));
            }
        }

        if ($incompleteId = $request->session()->get('incomplete_order_id')) {
            IncompleteOrder::where('id', $incompleteId)->delete();
            $request->session()->forget('incomplete_order_id');
        }

        return redirect()->route('orders.success', $order->order_number);
    }

    public function success(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->firstOrFail();

        $orderStatuses = OrderStatus::ordered()->get();
        $statusLabels = $orderStatuses->pluck('label', 'key');
        $statusColors = $orderStatuses->pluck('badge_class', 'key');

        $liveTracking = null;

        if ($order->steadfast_tracking_code) {
            try {
                $liveTracking = $this->steadfastCourier->trackByCode($order->steadfast_tracking_code);
            } catch (Throwable $e) {
                $liveTracking = null;
            }
        }

        $moreProducts = Product::where('status', 'active')
            ->latest()
            ->get();

        if ($moreProducts->isEmpty()) {
            $moreProducts = Product::latest()->get();
        }

        return view('orders.success', compact('order', 'statusLabels', 'statusColors', 'liveTracking', 'moreProducts'));
    }
}
