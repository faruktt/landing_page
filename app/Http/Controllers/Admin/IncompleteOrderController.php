<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncompleteOrder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IncompleteOrderController extends Controller
{
    public function index(Request $request): View
    {
        $incompleteOrders = IncompleteOrder::query()
            ->when($request->get('filter') === 'uncontacted', fn ($q) => $q->whereNull('contacted_at'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.incomplete-orders.index', compact('incompleteOrders'));
    }

    public function toggleContacted(IncompleteOrder $incompleteOrder): RedirectResponse
    {
        $incompleteOrder->update([
            'contacted_at' => $incompleteOrder->contacted_at ? null : now(),
        ]);

        return back()->with('status', $incompleteOrder->contacted_at ? 'যোগাযোগ করা হয়েছে হিসেবে চিহ্নিত করা হয়েছে।' : 'যোগাযোগ করা হয়নি হিসেবে চিহ্নিত করা হয়েছে।');
    }

    public function destroy(IncompleteOrder $incompleteOrder): RedirectResponse
    {
        $incompleteOrder->delete();

        return back()->with('status', 'মুছে ফেলা হয়েছে।');
    }
}
