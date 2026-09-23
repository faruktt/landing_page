@extends('layouts.admin')

@section('title', ($order->active_courier_name === 'pathao' ? 'পাঠাও' : 'Steadfast').' ট্র্যাকিং')

@php
    $statusMeta = [
        'pending' => ['label' => 'পেন্ডিং', 'class' => 'bg-slate-100 text-slate-600', 'icon' => 'fa-clock'],
        'in_review' => ['label' => 'পর্যালোচনাধীন', 'class' => 'bg-blue-100 text-blue-700', 'icon' => 'fa-magnifying-glass'],
        'hold' => ['label' => 'হোল্ডে আছে', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-hand'],
        'delivered' => ['label' => 'ডেলিভারড', 'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fa-circle-check'],
        'partial_delivered' => ['label' => 'আংশিক ডেলিভারড', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-circle-half-stroke'],
        'cancelled' => ['label' => 'বাতিল', 'class' => 'bg-red-100 text-red-600', 'icon' => 'fa-circle-xmark'],
        'delivered_approval_pending' => ['label' => 'ডেলিভারি অনুমোদনের অপেক্ষায়', 'class' => 'bg-blue-100 text-blue-700', 'icon' => 'fa-hourglass-half'],
        'partial_delivered_approval_pending' => ['label' => 'আংশিক ডেলিভারি অনুমোদনের অপেক্ষায়', 'class' => 'bg-blue-100 text-blue-700', 'icon' => 'fa-hourglass-half'],
        'cancelled_approval_pending' => ['label' => 'বাতিল অনুমোদনের অপেক্ষায়', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-hourglass-half'],
        'unknown_approval_pending' => ['label' => 'অজানা, অনুমোদনের অপেক্ষায়', 'class' => 'bg-slate-100 text-slate-500', 'icon' => 'fa-circle-question'],
        'unknown' => ['label' => 'অজানা', 'class' => 'bg-slate-100 text-slate-500', 'icon' => 'fa-circle-question'],
    ];

    $courierTitle = $order->active_courier_name === 'pathao' ? 'Pathao Courier' : 'Steadfast Courier';
@endphp

@section('content')
<div class="max-w-xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $order->active_courier_name === 'pathao' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
          <i class="fa-solid fa-truck mr-1 text-[10px]"></i>{{ $courierTitle }}
        </span>
        <h2 class="font-display text-xl font-bold text-slate-900">কুরিয়ার ট্র্যাকিং</h2>
      </div>
      <p class="text-slate-500 text-sm mt-1">অর্ডার নং: <span class="font-medium text-slate-700">{{ $order->order_number }}</span></p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">&larr; অর্ডার সমূহ</a>
  </div>

  @if ($error)
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-2xl p-4 mb-5 flex items-center gap-2.5">
      <i class="fa-solid fa-triangle-exclamation text-base shrink-0"></i>
      <span>{{ $error }}</span>
    </div>
  @endif

  @if ($tracking)
    @php
      $status = strtolower($tracking['delivery_status'] ?? 'unknown');
      $meta = $statusMeta[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-slate-100 text-slate-700', 'icon' => 'fa-info-circle'];
    @endphp
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div class="flex items-center justify-between mb-5">
        <div>
          <p class="text-xs text-slate-400">ট্র্যাকিং কোড</p>
          <p class="text-lg font-bold text-slate-900 font-mono">{{ $order->active_tracking_code ?: ($order->active_consignment_id ?: 'N/A') }}</p>
        </div>
        <span class="text-xs font-semibold px-3.5 py-2 rounded-full {{ $meta['class'] }} flex items-center gap-1.5 whitespace-nowrap">
          <i class="fa-solid {{ $meta['icon'] }}"></i>
          {{ $meta['label'] }}
        </span>
      </div>

      <div class="space-y-2 text-sm">
        <div class="flex justify-between py-2 border-t border-slate-100">
          <span class="text-slate-400">কুরিয়ার মাধ্যম</span>
          <span class="font-semibold {{ $order->active_courier_name === 'pathao' ? 'text-red-600' : 'text-emerald-600' }}">{{ $courierTitle }}</span>
        </div>
        <div class="flex justify-between py-2 border-t border-slate-100">
          <span class="text-slate-400">কনসাইনমেন্ট আইডি</span>
          <span class="font-medium text-slate-700 font-mono">{{ $order->active_consignment_id ?: '-' }}</span>
        </div>
        <div class="flex justify-between py-2 border-t border-slate-100">
          <span class="text-slate-400">কাস্টমার</span>
          <span class="font-medium text-slate-700">{{ $order->customer_name }} ({{ $order->phone }})</span>
        </div>
        <div class="flex justify-between py-2 border-t border-slate-100 border-b">
          <span class="text-slate-400">ঠিকানা</span>
          <span class="font-medium text-slate-700 text-right max-w-[60%]">{{ $order->address }}, {{ $order->district }}</span>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
