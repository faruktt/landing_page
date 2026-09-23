<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const PRESETS = ['today', 'yesterday', 'this_week', 'last_week', 'this_month', 'last_month', 'this_year', 'all_time'];

    private const CHART_COLORS = [
        'slate' => '#64748b',
        'yellow' => '#eab308',
        'amber' => '#f59e0b',
        'blue' => '#3b82f6',
        'indigo' => '#6366f1',
        'purple' => '#a855f7',
        'pink' => '#ec4899',
        'emerald' => '#10b981',
        'cyan' => '#06b6d4',
        'red' => '#ef4444',
    ];

    public function index(Request $request): View
    {
        $customFrom = $request->query('from');
        $customTo = $request->query('to');
        $range = $request->query('range');

        if ($customFrom && $customTo) {
            $range = 'custom';
        } elseif (! in_array($range, self::PRESETS, true)) {
            $range = 'today';
        }

        [$from, $to, $prevFrom, $prevTo] = match ($range) {
            'today' => [today()->startOfDay(), today()->endOfDay(), today()->subDay()->startOfDay(), today()->subDay()->endOfDay()],
            'yesterday' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay(), today()->subDays(2)->startOfDay(), today()->subDays(2)->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek(), now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek(), now()->subWeeks(2)->startOfWeek(), now()->subWeeks(2)->endOfWeek()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth(), now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth(), now()->subMonthsNoOverflow(2)->startOfMonth(), now()->subMonthsNoOverflow(2)->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfYear(), now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
            'all_time' => [null, null, null, null],
            'custom' => [Carbon::parse($customFrom)->startOfDay(), Carbon::parse($customTo)->endOfDay(), null, null],
        };

        $sales = (float) Order::when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        $rangeOrders = Order::when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]))->count();

        $prevSales = $prevFrom
            ? (float) Order::whereBetween('created_at', [$prevFrom, $prevTo])->whereNotIn('status', ['cancelled'])->sum('total')
            : null;

        $salesTrend = ($prevSales !== null && $prevSales > 0)
            ? (int) round((($sales - $prevSales) / $prevSales) * 100)
            : null;

        $pendingOrders = Order::where('status', OrderStatus::defaultKey())->count();
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $lowStockCount = Product::where('stock', '<=', 10)->count();
        $totalCustomers = Customer::count();

        $recentOrders = Order::latest()->take(5)->get();

        $salesTrendChart = collect(range(13, 0))->map(function ($daysAgo) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => $date->format('d M'),
                'total' => (float) Order::whereDate('created_at', $date)->whereNotIn('status', ['cancelled'])->sum('total'),
            ];
        })->values();

        $orderStatusChart = OrderStatus::ordered()->get()
            ->map(fn ($status) => [
                'label' => $status->label,
                'count' => Order::where('status', $status->key)->count(),
                'color' => self::CHART_COLORS[$status->color] ?? self::CHART_COLORS['slate'],
            ])
            ->filter(fn ($row) => $row['count'] > 0)
            ->values();

        return view('admin.dashboard', compact(
            'sales', 'salesTrend', 'rangeOrders', 'pendingOrders',
            'totalOrders', 'totalProducts', 'lowStockCount', 'totalCustomers', 'recentOrders',
            'range', 'from', 'to', 'salesTrendChart', 'orderStatusChart'
        ));
    }
}
