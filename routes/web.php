<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlockedIpController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourierSettingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FraudCheckController;
use App\Http\Controllers\Admin\IncompleteOrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/p/{slug}', [LandingPageController::class, 'show'])->name('landing.show');
Route::get('/check-phone', [OrderController::class, 'checkPhone'])->name('check-phone');
Route::post('/order', [OrderController::class, 'store'])->name('orders.store');
Route::post('/order/save-progress', [OrderController::class, 'saveProgress'])->name('orders.save-progress');
Route::get('/order/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');
Route::delete('/admin/products/gallery/{image}', [ProductController::class, 'destroyGalleryImage'])->name('admin.products.gallery.destroy');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('orders/{order}/tracking', [AdminOrderController::class, 'tracking'])->name('orders.tracking');
        Route::get('orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('orders/{order}/payments', [AdminOrderController::class, 'addPayment'])->name('orders.addPayment');
        Route::post('orders/{order}/send-courier', [AdminOrderController::class, 'sendToCourier'])->name('orders.send-courier');
        Route::resource('order-statuses', OrderStatusController::class)->except('show');
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::patch('customers/{customer}/toggle-block', [CustomerController::class, 'toggleBlock'])->name('customers.toggleBlock');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('incomplete-orders', [IncompleteOrderController::class, 'index'])->name('incomplete-orders.index');
        Route::post('incomplete-orders/{incompleteOrder}/contacted', [IncompleteOrderController::class, 'toggleContacted'])->name('incomplete-orders.toggleContacted');
        Route::delete('incomplete-orders/{incompleteOrder}', [IncompleteOrderController::class, 'destroy'])->name('incomplete-orders.destroy');
        Route::get('fraud-check/lookup', [FraudCheckController::class, 'lookup'])->name('fraud-check.lookup');

        Route::middleware('can:elevated-access')->group(function () {
            Route::resource('products', ProductController::class)->except('show');
            Route::resource('categories', CategoryController::class)->except('show');
            Route::get('fraud-checker', [FraudCheckController::class, 'index'])->name('fraud-check.index');
            Route::get('couriers', [CourierSettingController::class, 'index'])->name('couriers.index');
            Route::post('couriers', [CourierSettingController::class, 'update'])->name('couriers.update');
            Route::post('couriers/pathao-stores', [CourierSettingController::class, 'fetchPathaoStores'])->name('couriers.pathao-stores');
            Route::post('couriers/test-steadfast', [CourierSettingController::class, 'testSteadfast'])->name('couriers.test-steadfast');
            Route::get('blocked-ips', [BlockedIpController::class, 'index'])->name('blocked-ips.index');
            Route::post('blocked-ips', [BlockedIpController::class, 'store'])->name('blocked-ips.store');
            Route::delete('blocked-ips/{blockedIp}', [BlockedIpController::class, 'destroy'])->name('blocked-ips.destroy');
        });

        Route::middleware('can:admin-access')->group(function () {
            Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
            Route::resource('users', UserController::class)->except('show');
        });
    });

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
});
