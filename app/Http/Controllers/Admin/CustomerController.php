<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->withSum('orders as total_due', 'due_amount')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%")
                        ->orWhere('address', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'totalCustomers' => Customer::count(),
            'totalSales' => Order::count(),
            'totalRevenue' => (float) Order::sum('total'),
            'totalDue' => (float) Order::sum('due_amount'),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    public function show(Customer $customer): View
    {
        $customer->loadCount('orders');
        $orders = $customer->orders()->with('items')->latest()->get();

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:customers,phone,'.$customer->id],
            'address' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:100'],
        ]);

        $customer->update($data);

        return redirect()->route('admin.customers.show', $customer)->with('status', 'কাস্টমার তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('status', 'কাস্টমার মুছে ফেলা হয়েছে।');
    }

    public function toggleBlock(Request $request, Customer $customer): RedirectResponse
    {
        if ($customer->is_blocked) {
            $customer->update([
                'is_blocked' => false,
                'blocked_reason' => null,
                'blocked_at' => null,
            ]);

            return back()->with('status', $customer->name.' কে আনব্লক করা হয়েছে।');
        }

        $customer->update([
            'is_blocked' => true,
            'blocked_reason' => $request->input('reason'),
            'blocked_at' => now(),
        ]);

        return back()->with('status', $customer->name.' কে ব্লক করা হয়েছে।');
    }
}
