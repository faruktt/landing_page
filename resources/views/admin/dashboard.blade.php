@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
  $rangePresets = [
    'today' => 'Today',
    'yesterday' => 'Yesterday',
    'this_week' => 'This Week',
    'last_week' => 'Last Week',
    'this_month' => 'This Month',
    'last_month' => 'Last Month',
    'this_year' => 'This Year',
    'all_time' => 'All Time',
  ];
@endphp
<div class="mb-6 bg-white rounded-2xl border border-slate-100 shadow-sm p-3">
  <div class="flex flex-wrap items-center gap-2">
    <div class="flex flex-wrap items-center gap-2">
      @foreach ($rangePresets as $key => $label)
        <a href="{{ route('admin.dashboard', ['range' => $key]) }}"
           class="px-3.5 py-1.5 rounded-full text-sm font-medium border transition
             {{ $range === $key ? 'bg-brand text-white border-brand' : 'bg-white text-slate-600 border-slate-200 hover:border-brand/40' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2 ml-auto text-sm">
      <span class="text-slate-400 hidden sm:inline">Custom:</span>
      <input type="date" name="from" value="{{ $from?->format('Y-m-d') }}" class="border border-slate-200 rounded-lg px-2.5 py-1.5 text-sm text-slate-600">
      <span class="text-slate-300">&ndash;</span>
      <input type="date" name="to" value="{{ $to?->format('Y-m-d') }}" class="border border-slate-200 rounded-lg px-2.5 py-1.5 text-sm text-slate-600">
      <button type="submit" class="px-4 py-1.5 rounded-full bg-brand text-white text-sm font-semibold hover:opacity-90 transition">Apply</button>
    </form>
  </div>

  <div class="flex items-center gap-1.5 mt-2.5 pt-2.5 border-t border-slate-50 text-xs text-slate-400">
    <i class="fa-regular fa-calendar"></i>
    <span>Showing: <span class="text-brand font-medium">{{ $from ? $from->format('d M Y').' — '.$to->format('d M Y') : 'All time' }}</span></span>
  </div>
</div>

<!-- Stat cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <div class="flex items-start justify-between mb-2">
      <span class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
        <i class="fa-solid fa-sack-dollar text-lg"></i>
      </span>
      @if ($salesTrend !== null)
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-lg {{ $salesTrend >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">{{ $salesTrend >= 0 ? '+' : '' }}{{ $salesTrend }}%</span>
      @endif
    </div>
    <p class="text-xl font-bold text-slate-900">৳{{ number_format($sales, 0) }}</p>
    <p class="text-xs text-slate-400 mt-0.5">বিক্রয়</p>
    <p class="text-[11px] text-slate-400 mt-1">{{ $rangeOrders }}টি অর্ডার এই সময়ে</p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <div class="flex items-start justify-between mb-2">
      <span class="h-10 w-10 rounded-xl bg-indigo-100 text-brand flex items-center justify-center">
        <i class="fa-solid fa-cart-shopping text-lg"></i>
      </span>
      @if ($pendingOrders > 0)
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-lg bg-yellow-100 text-yellow-700">{{ $pendingOrders }} pending</span>
      @endif
    </div>
    <p class="text-xl font-bold text-slate-900">{{ $totalOrders }}</p>
    <p class="text-xs text-slate-400 mt-0.5">মোট অর্ডার</p>
    <p class="text-[11px] text-slate-400 mt-1">সর্বমোট এই পর্যন্ত</p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <div class="flex items-start justify-between mb-2">
      <span class="h-10 w-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
        <i class="fa-solid fa-bag-shopping text-lg"></i>
      </span>
      @if ($lowStockCount > 0)
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-lg bg-red-100 text-red-600">{{ $lowStockCount }} low</span>
      @endif
    </div>
    <p class="text-xl font-bold text-slate-900">{{ $totalProducts }}</p>
    <p class="text-xs text-slate-400 mt-0.5">মোট প্রোডাক্ট</p>
    <p class="text-[11px] text-slate-400 mt-1">সব ক্যাটাগরি মিলিয়ে</p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
    <div class="flex items-start justify-between mb-2">
      <span class="h-10 w-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
        <i class="fa-solid fa-users text-lg"></i>
      </span>
    </div>
    <p class="text-xl font-bold text-slate-900">{{ $totalCustomers }}</p>
    <p class="text-xs text-slate-400 mt-0.5">মোট কাস্টমার</p>
    <p class="text-[11px] text-slate-400 mt-1">ইউনিক ফোন নাম্বার অনুযায়ী</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <h3 class="font-semibold text-slate-900 mb-4">বিক্রয় ট্রেন্ড (গত ১৪ দিন)</h3>
    <div class="h-64">
      <canvas id="salesTrendChart"></canvas>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <h3 class="font-semibold text-slate-900 mb-4">অর্ডার স্ট্যাটাস অনুযায়ী</h3>
    <div class="h-64">
      @if ($orderStatusChart->isEmpty())
        <div class="h-full flex items-center justify-center text-sm text-slate-400">এখনো কোনো অর্ডার নেই।</div>
      @else
        <canvas id="orderStatusChart"></canvas>
      @endif
    </div>
  </div>
</div>

<div class="grid grid-cols-1 gap-5">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-900">সাম্প্রতিক অর্ডার</h3>
      <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand font-medium hover:underline">সব দেখুন</a>
    </div>
    @forelse ($recentOrders as $order)
      <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between px-5 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 transition">
        <div>
          <p class="text-sm font-medium text-slate-800">{{ $order->order_number }} &middot; {{ $order->customer_name }}</p>
          <p class="text-xs text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-sm text-slate-700">৳{{ number_format((float) $order->total, 2) }}</span>
          <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-500' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
        </div>
      </a>
    @empty
      <div class="p-10 text-center text-sm text-slate-400">
        এখনো কোনো অর্ডার আসেনি।
      </div>
    @endforelse
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
  new Chart(document.getElementById('salesTrendChart'), {
    type: 'line',
    data: {
      labels: @json($salesTrendChart->pluck('label')),
      datasets: [{
        label: 'বিক্রয়',
        data: @json($salesTrendChart->pluck('total')),
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79,70,229,0.08)',
        fill: true,
        tension: 0.35,
        pointRadius: 2,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } },
    },
  });

  @if ($orderStatusChart->isNotEmpty())
  new Chart(document.getElementById('orderStatusChart'), {
    type: 'doughnut',
    data: {
      labels: @json($orderStatusChart->pluck('label')),
      datasets: [{
        data: @json($orderStatusChart->pluck('count')),
        backgroundColor: @json($orderStatusChart->pluck('color')),
        borderWidth: 2,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } },
    },
  });
  @endif
</script>
@endpush
