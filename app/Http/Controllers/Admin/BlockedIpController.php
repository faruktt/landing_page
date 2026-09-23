<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockedIpController extends Controller
{
    public function index(): View
    {
        $blockedIps = BlockedIp::latest()->paginate(20);

        $activeBlocks = BlockedIp::where('expires_at', '>', now())->get()->keyBy('ip_address');

        $ipOverview = Order::query()
            ->select('ip_address', 'phone', 'customer_name', 'order_number', 'status', 'created_at')
            ->whereNotNull('ip_address')
            ->latest()
            ->get()
            ->groupBy('ip_address')
            ->map(function ($orders, $ip) use ($activeBlocks) {
                return (object) [
                    'ip_address' => $ip,
                    'orders_count' => $orders->count(),
                    'phones' => $orders->pluck('phone')->unique()->values(),
                    'last_order_at' => $orders->max('created_at'),
                    'last_customer_name' => $orders->first()->customer_name,
                    'blocked_ip' => $activeBlocks->get($ip),
                ];
            })
            ->sortByDesc('orders_count')
            ->values();

        return view('admin.blocked-ips.index', compact('blockedIps', 'ipOverview'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ip_address' => ['required', 'string', 'max:45'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        BlockedIp::updateOrCreate(
            ['ip_address' => $data['ip_address']],
            ['reason' => $data['reason'] ?? null, 'expires_at' => now()->addMinutes(30)]
        );

        return back()->with('status', $data['ip_address'].' আইপি ৩০ মিনিটের জন্য ব্লক করা হয়েছে।');
    }

    public function destroy(BlockedIp $blockedIp): RedirectResponse
    {
        $blockedIp->delete();

        return back()->with('status', $blockedIp->ip_address.' আইপি আনব্লক করা হয়েছে।');
    }
}
