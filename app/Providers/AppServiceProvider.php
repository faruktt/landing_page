<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('elevated-access', fn (User $user) => in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER], true));
        Gate::define('admin-access', fn (User $user) => $user->role === User::ROLE_ADMIN);

        View::composer('layouts.admin', function ($view) {
            $defaultStatusKey = OrderStatus::defaultKey();

            $pendingOrders = Order::where('status', $defaultStatusKey)->latest()->limit(5)->get();
            $pendingOrdersCount = Order::where('status', $defaultStatusKey)->count();

            $lowStockProducts = Product::where('stock', '<=', 10)->orderBy('stock')->limit(5)->get();
            $lowStockCount = Product::where('stock', '<=', 10)->count();

            $view->with([
                'navPendingOrders' => $pendingOrders,
                'navPendingOrdersCount' => $pendingOrdersCount,
                'navLowStockProducts' => $lowStockProducts,
                'navLowStockCount' => $lowStockCount,
                'navTotalNotifications' => $pendingOrdersCount + $lowStockCount,
            ]);
        });

        View::composer(['layouts.admin', 'admin.auth.login'], function ($view) {
            $view->with('siteSettings', Setting::current());
        });

        View::composer(
            ['admin.orders.index', 'admin.orders.show', 'admin.orders.edit', 'admin.orders.invoice', 'admin.customers.show', 'admin.dashboard'],
            function ($view) {
                $orderStatuses = OrderStatus::ordered()->get();

                $view->with([
                    'statusLabels' => $orderStatuses->pluck('label', 'key'),
                    'statusColors' => $orderStatuses->pluck('badge_class', 'key'),
                ]);
            }
        );
    }
}
