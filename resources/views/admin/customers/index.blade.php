@extends('layouts.admin')

@section('title', 'কাস্টমার সমূহ')

@php
    $avatarColors = ['bg-blue-500', 'bg-indigo-500', 'bg-purple-500', 'bg-pink-500', 'bg-emerald-500', 'bg-amber-500', 'bg-cyan-500'];
@endphp

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">কাস্টমার সমূহ</h2>
    <p class="text-slate-500 text-sm mt-1">সব কাস্টমার ও তাদের অর্ডার হিস্টোরি দেখুন।</p>
  </div>
</div>

<!-- Stat cards -->
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
      <i class="fa-solid fa-users text-lg"></i>
    </div>
    <div>
      <p class="text-xs text-slate-400">Total Customers</p>
      <p class="text-xl font-bold text-slate-900">{{ $stats['totalCustomers'] }}</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="h-11 w-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
      <i class="fa-solid fa-receipt text-lg"></i>
    </div>
    <div>
      <p class="text-xs text-slate-400">Total Sales</p>
      <p class="text-xl font-bold text-slate-900">{{ $stats['totalSales'] }}</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
      <i class="fa-solid fa-sack-dollar text-lg"></i>
    </div>
    <div>
      <p class="text-xs text-slate-400">Total Revenue</p>
      <p class="text-xl font-bold text-slate-900">৳{{ number_format($stats['totalRevenue'], 0) }}</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="h-11 w-11 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
      <i class="fa-regular fa-clock text-lg"></i>
    </div>
    <div>
      <p class="text-xs text-slate-400">Total Due</p>
      <p class="text-xl font-bold text-slate-900">৳{{ number_format($stats['totalDue'], 0) }}</p>
    </div>
  </div>
</div>

<!-- Search -->
<form method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4 flex flex-wrap gap-2.5 items-center">
  <div class="relative">
    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম, ফোন বা ঠিকানা দিয়ে খুঁজুন..."
           class="pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand w-72">
  </div>
  <button type="submit" class="bg-brand hover:bg-brandDark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">খুঁজুন</button>
  @if (request()->hasAny(['search']))
    <a href="{{ route('admin.customers.index') }}" class="text-slate-500 border border-slate-200 px-4 py-2 rounded-lg text-sm hover:bg-slate-50 transition-colors">রিসেট</a>
  @endif
</form>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">#</th>
        <th class="text-left font-medium px-5 py-3">কাস্টমার</th>
        <th class="text-left font-medium px-5 py-3">ঠিকানা</th>
        <th class="text-center font-medium px-5 py-3">অর্ডার</th>
        <th class="text-left font-medium px-5 py-3">মোট খরচ</th>
        <th class="text-left font-medium px-5 py-3">বকেয়া</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($customers as $customer)
        @php
          $due = (float) ($customer->total_due ?? 0);
          $avatarColor = $avatarColors[$customer->id % count($avatarColors)];
          $waNumber = '880'.ltrim(preg_replace('/\D/', '', $customer->phone), '0');
        @endphp
        <tr>
          <td class="px-5 py-3 text-slate-400">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-full {{ $avatarColor }} text-white flex items-center justify-center font-semibold text-sm shrink-0">
                {{ mb_substr($customer->name, 0, 1) }}
              </div>
              <div>
                <p class="font-medium text-slate-800">
                  {{ $customer->name }}
                  @if ($customer->is_blocked)
                    <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-600 align-middle">ব্লকড</span>
                  @endif
                </p>
                <div class="flex items-center gap-1.5 mt-0.5">
                  <span class="text-xs text-slate-400">{{ $customer->phone }}</span>
                  <a href="tel:{{ $customer->phone }}" title="কল করুন" class="text-slate-400 hover:text-brand">
                    <i class="fas fa-phone text-[10px]"></i>
                  </a>
                  <a href="https://wa.me/{{ $waNumber }}" target="_blank" title="হোয়াটসঅ্যাপ" class="text-slate-400 hover:text-emerald-500">
                    <i class="fab fa-whatsapp text-[11px]"></i>
                  </a>
                </div>
              </div>
            </div>
          </td>
          <td class="px-5 py-3 text-slate-500 max-w-[220px] truncate">
            @if ($customer->address || $customer->district)
              <span class="inline-flex items-center gap-1">
                <i class="fa-solid fa-location-dot text-slate-400 shrink-0 text-xs"></i>
                {{ collect([$customer->address, $customer->district])->filter()->implode(', ') }}
              </span>
            @else
              -
            @endif
          </td>
          <td class="px-5 py-3 text-center">
            <span class="text-xs font-semibold bg-brand/10 text-brand px-2.5 py-1 rounded-full">{{ $customer->orders_count }}</span>
          </td>
          <td class="px-5 py-3 text-slate-700 font-medium">৳{{ number_format((float) ($customer->total_spent ?? 0), 0) }}</td>
          <td class="px-5 py-3">
            @if ($due > 0)
              <span class="text-red-600 font-semibold">৳{{ number_format($due, 0) }}</span>
            @else
              <span class="text-emerald-600 font-medium">পরিশোধিত</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right">
            <div class="inline-flex items-center gap-1.5">
              <a href="{{ route('admin.customers.show', $customer) }}" title="বিস্তারিত"
                 class="inline-flex text-brand hover:bg-brand/5 w-8 h-8 items-center justify-center rounded-lg border border-brand/20 transition-colors">
                <i class="fa-solid fa-eye text-sm"></i>
              </a>
              <a href="{{ route('admin.customers.edit', $customer) }}" title="এডিট করুন"
                 class="inline-flex text-amber-600 hover:bg-amber-50 w-8 h-8 items-center justify-center rounded-lg border border-amber-200 transition-colors">
                <i class="fa-solid fa-pen text-sm"></i>
              </a>
              <form method="POST" action="{{ route('admin.customers.toggleBlock', $customer) }}" class="js-toggle-block" data-blocked="{{ $customer->is_blocked ? '1' : '0' }}" data-name="{{ $customer->name }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="reason" class="reason-input">
                <button type="submit" title="{{ $customer->is_blocked ? 'আনব্লক করুন' : 'ব্লক করুন' }}"
                        class="inline-flex {{ $customer->is_blocked ? 'text-emerald-600 hover:bg-emerald-50 border-emerald-200' : 'text-red-500 hover:bg-red-50 border-red-200' }} w-8 h-8 items-center justify-center rounded-lg border transition-colors">
                  <i class="fa-solid {{ $customer->is_blocked ? 'fa-unlock' : 'fa-ban' }} text-sm"></i>
                </button>
              </form>
              <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="js-confirm-delete" data-confirm-text="'{{ $customer->name }}' কাস্টমারটি স্থায়ীভাবে মুছে যাবে।">
                @csrf
                @method('DELETE')
                <button type="submit" title="মুছুন"
                        class="inline-flex text-red-500 hover:bg-red-50 w-8 h-8 items-center justify-center rounded-lg border border-red-200 transition-colors">
                  <i class="fa-solid fa-trash text-sm"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="px-5 py-10 text-center text-slate-400">এখনো কোনো কাস্টমার নেই।</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if ($customers->hasPages())
  <div class="mt-5">{{ $customers->links() }}</div>
@endif
@endsection

@push('scripts')
@include('admin.partials.block-scripts')
@endpush
