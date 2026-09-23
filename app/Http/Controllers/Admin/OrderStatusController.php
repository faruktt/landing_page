<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    public function index(): View
    {
        $orderStatuses = OrderStatus::ordered()->get()->map(function ($status) {
            $status->orders_count = Order::where('status', $status->key)->count();

            return $status;
        });

        return view('admin.order-statuses.index', compact('orderStatuses'));
    }

    public function create(): View
    {
        return view('admin.order-statuses.create', ['orderStatus' => new OrderStatus]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            if (! empty($data['is_default'])) {
                OrderStatus::where('is_default', true)->update(['is_default' => false]);
            }

            $data['sort_order'] = (int) OrderStatus::max('sort_order') + 1;
            OrderStatus::create($data);
        });

        return redirect()->route('admin.order-statuses.index')->with('status', 'স্ট্যাটাস তৈরি হয়েছে।');
    }

    public function edit(OrderStatus $orderStatus): View
    {
        return view('admin.order-statuses.edit', compact('orderStatus'));
    }

    public function update(Request $request, OrderStatus $orderStatus): RedirectResponse
    {
        $data = $this->validateData($request, $orderStatus->id);

        DB::transaction(function () use ($data, $orderStatus) {
            if (! empty($data['is_default'])) {
                OrderStatus::where('id', '!=', $orderStatus->id)->where('is_default', true)->update(['is_default' => false]);
            }

            $orderStatus->update($data);
        });

        return redirect()->route('admin.order-statuses.index')->with('status', 'স্ট্যাটাস আপডেট হয়েছে।');
    }

    public function destroy(OrderStatus $orderStatus): RedirectResponse
    {
        if (Order::where('status', $orderStatus->key)->exists()) {
            return back()->with('error', 'এই স্ট্যাটাসে অর্ডার আছে বলে মুছে ফেলা যাবে না।');
        }

        if ($orderStatus->is_default) {
            return back()->with('error', 'ডিফল্ট স্ট্যাটাস মুছে ফেলার আগে অন্য একটি স্ট্যাটাসকে ডিফল্ট করুন।');
        }

        if (OrderStatus::count() <= 1) {
            return back()->with('error', 'অন্তত একটি স্ট্যাটাস থাকা আবশ্যক।');
        }

        $orderStatus->delete();

        return redirect()->route('admin.order-statuses.index')->with('status', 'স্ট্যাটাস মুছে ফেলা হয়েছে।');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'key' => [
                'required', 'string', 'max:30', 'regex:/^[a-z0-9_]+$/',
                'unique:order_statuses,key'.($ignoreId ? ",$ignoreId" : ''),
            ],
            'label' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'in:'.implode(',', array_keys(OrderStatus::COLORS))],
        ], [
            'key.regex' => 'কী শুধু ছোট হাতের ইংরেজি অক্ষর, সংখ্যা ও আন্ডারস্কোর দিয়ে লিখতে হবে।',
        ]);

        $data['is_default'] = $request->boolean('is_default');

        return $data;
    }
}
