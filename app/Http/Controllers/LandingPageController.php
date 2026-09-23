<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function show(string $slug, Request $request): View
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['images', 'benefits', 'reviews', 'specs', 'trustPoints', 'cartOffers'])
            ->with(['relatedProducts' => fn ($q) => $q->where('status', 'active')])
            ->firstOrFail();

        $orderBlocked = session('order_blocked') || BlockedIp::isBlocked($request->ip());

        return view('landing.template-'.$product->template, compact('product', 'orderBlocked'));
    }
}
