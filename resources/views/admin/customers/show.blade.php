@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">
      {{ $customer->name }}
      @if ($customer->is_blocked)
        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-600 align-middle">ব্লকড</span>
      @endif
    </h2>
    <p class="text-slate-500 text-sm mt-1">{{ $customer->phone }}</p>
    @if ($customer->is_blocked && $customer->blocked_reason)
      <p class="text-xs text-red-500 mt-1">কারণ: {{ $customer->blocked_reason }}</p>
    @endif
  </div>
  <div class="flex items-center gap-4">
    <a href="{{ route('admin.customers.edit', $customer) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-amber-600 hover:text-amber-700 border border-amber-200 hover:bg-amber-50 px-4 py-2 rounded-lg transition-colors">
      <i class="fa-solid fa-pen text-sm"></i>
      এডিট করুন
    </a>
    <form method="POST" action="{{ route('admin.customers.toggleBlock', $customer) }}" class="js-toggle-block" data-blocked="{{ $customer->is_blocked ? '1' : '0' }}" data-name="{{ $customer->name }}">
      @csrf
      @method('PATCH')
      <input type="hidden" name="reason" class="reason-input">
      <button type="submit"
              class="inline-flex items-center gap-2 text-sm font-medium {{ $customer->is_blocked ? 'text-emerald-600 hover:text-emerald-700 border-emerald-200 hover:bg-emerald-50' : 'text-red-500 hover:text-red-600 border-red-200 hover:bg-red-50' }} border px-4 py-2 rounded-lg transition-colors">
        <i class="fa-solid {{ $customer->is_blocked ? 'fa-unlock' : 'fa-ban' }} text-sm"></i>
        {{ $customer->is_blocked ? 'আনব্লক করুন' : 'ব্লক করুন' }}
      </button>
    </form>
    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="js-confirm-delete" data-confirm-text="'{{ $customer->name }}' কাস্টমারটি স্থায়ীভাবে মুছে যাবে।">
      @csrf
      @method('DELETE')
      <button type="submit"
              class="inline-flex items-center gap-2 text-sm font-medium text-red-500 hover:text-red-600 border border-red-200 hover:bg-red-50 px-4 py-2 rounded-lg transition-colors">
        <i class="fa-solid fa-trash text-sm"></i>
        মুছুন
      </button>
    </form>
    <a href="{{ route('admin.customers.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">&larr; সব কাস্টমার</a>
  </div>
</div>

<div class="grid sm:grid-cols-3 gap-5 mb-6">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <p class="text-2xl font-bold text-slate-900">{{ $customer->orders_count }}</p>
    <p class="text-sm text-slate-500 mt-1">মোট অর্ডার</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <p class="text-2xl font-bold text-slate-900">৳{{ number_format((float) $orders->sum('total'), 2) }}</p>
    <p class="text-sm text-slate-500 mt-1">মোট খরচ</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <p class="text-sm text-slate-500 mb-1">সর্বশেষ ঠিকানা</p>
    <p class="text-sm text-slate-800">{{ $customer->address }}, {{ $customer->district }}</p>
  </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100">
    <h3 class="font-semibold text-slate-900">অর্ডার হিস্টোরি</h3>
  </div>
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">অর্ডার নম্বর</th>
        <th class="text-left font-medium px-5 py-3">প্রোডাক্ট</th>
        <th class="text-left font-medium px-5 py-3">মোট</th>
        <th class="text-left font-medium px-5 py-3">স্ট্যাটাস</th>
        <th class="text-left font-medium px-5 py-3">তারিখ</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($orders as $order)
        <tr>
          <td class="px-5 py-3 font-medium text-slate-800">{{ $order->order_number }}</td>
          <td class="px-5 py-3 text-slate-500">{{ $order->items->pluck('product_name')->implode(', ') }}</td>
          <td class="px-5 py-3 text-slate-700">৳{{ number_format((float) $order->total, 2) }}</td>
          <td class="px-5 py-3">
            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-500' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
          </td>
          <td class="px-5 py-3 text-slate-400 text-xs">{{ $order->created_at->format('d M, h:i A') }}</td>
          <td class="px-5 py-3 text-right">
            <a href="{{ route('admin.orders.show', $order) }}" title="বিস্তারিত"
               class="inline-flex text-brand hover:bg-brand/5 w-8 h-8 items-center justify-center rounded-lg border border-brand/20 transition-colors">
              <i class="fa-solid fa-eye text-sm"></i>
            </a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="px-5 py-10 text-center text-slate-400">কোনো অর্ডার নেই।</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection

@push('scripts')
@include('admin.partials.block-scripts')
@endpush
