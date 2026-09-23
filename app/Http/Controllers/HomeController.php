<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::where('status', 'active')
            ->with(['images', 'benefits', 'reviews', 'specs', 'trustPoints', 'cartOffers'])
            ->latest()
            ->get();

        $featuredProduct = $products->first();
        $orderBlocked = session('order_blocked') || BlockedIp::isBlocked($request->ip());
        $siteSettings = Setting::current();

        return view('home', compact('products', 'featuredProduct', 'orderBlocked', 'siteSettings'));
    }
}
