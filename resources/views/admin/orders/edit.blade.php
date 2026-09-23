@extends('layouts.admin')

@section('title', 'অর্ডার এডিট #'.$order->order_number)

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">অর্ডার এডিট করুন - {{ $order->order_number }}</h2>
    <p class="text-slate-500 text-sm mt-1">{{ $order->created_at->format('d M Y, h:i A') }}</p>
  </div>
  <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">&larr; ফিরে যান</a>
</div>

@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-6">
  @csrf
  @method('PUT')

  <div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <h3 class="font-semibold text-slate-900 mb-4">প্রোডাক্ট সমূহ</h3>
        <table class="w-full text-sm">
          <thead>
            <tr class="text-slate-400 text-xs border-b border-slate-100">
              <th class="text-left font-normal py-2">প্রোডাক্ট</th>
              <th class="text-left font-normal py-2">ভ্যারিয়েন্ট</th>
              <th class="text-center font-normal py-2 w-24">পরিমাণ</th>
              <th class="text-right font-normal py-2 w-32">দাম</th>
              <th class="text-right font-normal py-2 w-32">সাবটোটাল</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order->items as $index => $item)
              <tr class="border-b border-slate-50">
                <td class="py-3">
                  <div class="flex items-center gap-3">
                    @if ($item->product && $item->product->image)
                      <img src="{{ $item->product->image_url ?: asset($item->product->image) }}" class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                    @endif
                    <span class="text-slate-800">{{ $item->product_name }}</span>
                  </div>
                  <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                </td>
                <td class="py-3 text-slate-500">{{ collect([$item->size, $item->color])->filter()->implode(' / ') ?: '-' }}</td>
                <td class="py-3">
                  <input type="number" min="1" step="1" name="items[{{ $index }}][quantity]" value="{{ old("items.$index.quantity", $item->quantity) }}"
                         data-item-qty data-index="{{ $index }}"
                         class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
                </td>
                <td class="py-3">
                  <input type="number" min="0" step="0.01" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", (float) $item->unit_price) }}"
                         data-item-price data-index="{{ $index }}"
                         class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-right focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
                </td>
                <td class="py-3 text-right text-slate-800 font-medium">
                  <span data-item-subtotal="{{ $index }}">৳{{ number_format((float) $item->subtotal, 2) }}</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="mt-5 pt-4 border-t border-slate-100 space-y-1.5 max-w-xs ml-auto text-sm">
          <div class="flex justify-between text-slate-500"><span>সাবটোটাল</span><span data-order-subtotal>৳{{ number_format((float) $order->subtotal, 2) }}</span></div>
          <div class="flex justify-between text-slate-500"><span>ডেলিভারি চার্জ</span><span>৳{{ number_format((float) $order->delivery_charge, 2) }}</span></div>
          <div class="flex justify-between font-semibold text-slate-900 pt-1.5 border-t border-slate-100"><span>আনুমানিক সর্বমোট</span><span class="text-brand" data-order-total>৳{{ number_format((float) $order->total, 2) }}</span></div>
        </div>
        <p class="text-xs text-slate-400 mt-3">* ডেলিভারি চার্জ জেলার উপর ভিত্তি করে সাবমিট করার পর পুনরায় হিসাব হবে।</p>
      </div>

      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <h3 class="font-semibold text-slate-900 mb-4">কাস্টমার তথ্য</h3>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">নাম *</label>
            <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required
                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">ফোন *</label>
            <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" required
                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">ঠিকানা *</label>
            <textarea name="address" rows="2" required
                      class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">{{ old('address', $order->address) }}</textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">জেলা *</label>
            <select name="district" required
                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
              @include('admin.orders.partials.district-select', ['selected' => old('district', $order->district)])
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">পেমেন্ট পদ্ধতি *</label>
            @php $paymentMethods = ['cod' => 'ক্যাশ অন ডেলিভারি', 'bkash' => 'বিকাশ', 'rocket' => 'রকেট', 'nagad' => 'নগদ']; @endphp
            <select name="payment_method" required
                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
              @foreach ($paymentMethods as $key => $label)
                <option value="{{ $key }}" {{ old('payment_method', $order->payment_method) === $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <label class="block text-sm font-medium text-slate-700 mb-2">অর্ডার স্ট্যাটাস</label>
        <select name="status" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition mb-4">
          @foreach ($statusLabels as $key => $label)
            <option value="{{ $key }}" {{ old('status', $order->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        <button type="submit" class="w-full bg-brand hover:bg-brandDark text-white font-semibold py-2.5 rounded-lg transition-colors">পরিবর্তন সংরক্ষণ করুন</button>
        <a href="{{ route('admin.orders.show', $order) }}" class="block text-center text-sm font-medium text-slate-500 hover:text-slate-800 mt-3">বাতিল করুন</a>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
  function bnFormat(n) {
    return '৳' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalcTotals() {
    var grandSubtotal = 0;
    document.querySelectorAll('[data-item-qty]').forEach(function (qtyInput) {
      var index = qtyInput.getAttribute('data-index');
      var priceInput = document.querySelector('[data-item-price][data-index="' + index + '"]');
      var qty = parseFloat(qtyInput.value) || 0;
      var price = parseFloat(priceInput.value) || 0;
      var subtotal = qty * price;
      grandSubtotal += subtotal;

      var subtotalEl = document.querySelector('[data-item-subtotal="' + index + '"]');
      if (subtotalEl) subtotalEl.textContent = bnFormat(subtotal);
    });

    var subtotalTotalEl = document.querySelector('[data-order-subtotal]');
    var grandTotalEl = document.querySelector('[data-order-total]');
    if (subtotalTotalEl) subtotalTotalEl.textContent = bnFormat(grandSubtotal);
    if (grandTotalEl) grandTotalEl.textContent = bnFormat(grandSubtotal + {{ (float) $order->delivery_charge }});
  }

  document.querySelectorAll('[data-item-qty], [data-item-price]').forEach(function (el) {
    el.addEventListener('input', recalcTotals);
  });
</script>
@endpush
