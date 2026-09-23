@extends('layouts.admin')

@section('title', 'অর্ডার সমূহ')

@php
    $paymentStatusColors = [
        'pending' => 'bg-red-100 text-red-600',
        'partial' => 'bg-amber-100 text-amber-700',
        'paid' => 'bg-emerald-100 text-emerald-700',
    ];
    $paymentStatusLabels = [
        'pending' => 'বকেয়া',
        'partial' => 'আংশিক পরিশোধ',
        'paid' => 'পরিশোধিত',
    ];
@endphp

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">অর্ডার সমূহ</h2>
    <p class="text-slate-500 text-sm mt-1">সব কাস্টমার অর্ডার ম্যানেজ করুন।</p>
  </div>
  <div class="flex items-center gap-2.5">
    <a href="{{ route('admin.blocked-ips.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 px-4 py-2 rounded-lg transition-colors">
      <i class="fa-solid fa-network-wired text-sm"></i>
      ব্লকড আইপি
    </a>
    <a href="{{ route('admin.order-statuses.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 px-4 py-2 rounded-lg transition-colors">
      <i class="fa-solid fa-tags text-sm"></i>
      স্ট্যাটাস ম্যানেজ করুন
    </a>
  </div>
</div>

<!-- Filters -->
<form method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4 flex flex-wrap gap-2.5 items-center">
  <div class="relative">
    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="অর্ডার নম্বর, নাম বা ফোন..."
           class="pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand w-56">
  </div>
  <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 text-slate-600">
    <option value="all" {{ ($currentStatus ?? '') === 'all' ? 'selected' : '' }}>সব স্ট্যাটাস</option>
    @foreach ($statusLabels as $key => $label)
      <option value="{{ $key }}" {{ ($currentStatus ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
  <select name="payment_method" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 text-slate-600">
    <option value="">সব পেমেন্ট মেথড</option>
    <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>COD</option>
    <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
    <option value="rocket" {{ request('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
    <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
  </select>
  <select name="payment_status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 text-slate-600">
    <option value="">সব পেমেন্ট স্ট্যাটাস</option>
    @foreach ($paymentStatusLabels as $key => $label)
      <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
  <input type="date" name="from" value="{{ request('from') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 text-slate-600">
  <input type="date" name="to" value="{{ request('to') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 text-slate-600">
  <button type="submit" class="bg-brand hover:bg-brandDark text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1.5 transition-colors">
    <i class="fa-solid fa-filter text-sm"></i>
    ফিল্টার
  </button>
  @if (request()->hasAny(['search', 'payment_method', 'payment_status', 'from', 'to']) || (request('status') && request('status') !== 'pending'))
    <a href="{{ route('admin.orders.index') }}" class="text-slate-500 border border-slate-200 px-4 py-2 rounded-lg text-sm hover:bg-slate-50 transition-colors">রিসেট</a>
  @endif
</form>

{{-- Status Filter Tabs / Boxes --}}
@php
    $orderedStatusList = $orderStatuses ?? \App\Models\OrderStatus::ordered()->get();
    $pendingItem = $orderedStatusList->firstWhere('key', 'pending');
    $remainingItems = $orderedStatusList->reject(fn ($item) => $item->key === 'pending');

    $statusMetaMap = [
        'all' => [
            'label' => 'সব অর্ডার',
            'icon' => 'fa-layer-group',
            'active_class' => 'bg-slate-900 text-white border-slate-900 shadow-sm ring-2 ring-slate-900/10',
            'inactive_class' => 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 hover:border-slate-300 shadow-2xs',
            'active_badge' => 'bg-white/20 text-white',
            'inactive_badge' => 'bg-slate-100 text-slate-600',
        ],
        'pending' => [
            'label' => $pendingItem?->label ?? 'পেন্ডিং',
            'icon' => 'fa-clock',
            'active_class' => 'bg-amber-500 text-white border-amber-500 shadow-sm ring-2 ring-amber-400/25',
            'inactive_class' => 'bg-white text-slate-700 border-slate-200 hover:bg-amber-50/50 hover:border-amber-300 shadow-2xs',
            'active_badge' => 'bg-white/25 text-white font-bold',
            'inactive_badge' => 'bg-amber-100 text-amber-800 font-bold',
        ],
        'confirmed' => [
            'label' => 'কনফার্মড',
            'icon' => 'fa-circle-check',
            'active_class' => 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-500/25',
            'inactive_class' => 'bg-white text-slate-700 border-slate-200 hover:bg-blue-50/50 hover:border-blue-300 shadow-2xs',
            'active_badge' => 'bg-white/25 text-white font-bold',
            'inactive_badge' => 'bg-blue-100 text-blue-800 font-bold',
        ],
        'delivered' => [
            'label' => 'ডেলিভারড',
            'icon' => 'fa-truck-ramp-box',
            'active_class' => 'bg-emerald-600 text-white border-emerald-600 shadow-sm ring-2 ring-emerald-500/25',
            'inactive_class' => 'bg-white text-slate-700 border-slate-200 hover:bg-emerald-50/50 hover:border-emerald-300 shadow-2xs',
            'active_badge' => 'bg-white/25 text-white font-bold',
            'inactive_badge' => 'bg-emerald-100 text-emerald-800 font-bold',
        ],
        'cancelled' => [
            'label' => 'বাতিল',
            'icon' => 'fa-circle-xmark',
            'active_class' => 'bg-red-600 text-white border-red-600 shadow-sm ring-2 ring-red-500/25',
            'inactive_class' => 'bg-white text-slate-700 border-slate-200 hover:bg-red-50/50 hover:border-red-300 shadow-2xs',
            'active_badge' => 'bg-white/25 text-white font-bold',
            'inactive_badge' => 'bg-red-100 text-red-800 font-bold',
        ],
        'send_to_steadfast' => [
            'label' => 'সেন্ড টু স্টেডফাস্ট',
            'icon' => 'fa-truck-fast',
            'active_class' => 'bg-cyan-600 text-white border-cyan-600 shadow-sm ring-2 ring-cyan-500/25',
            'inactive_class' => 'bg-white text-slate-700 border-slate-200 hover:bg-cyan-50/50 hover:border-cyan-300 shadow-2xs',
            'active_badge' => 'bg-white/25 text-white font-bold',
            'inactive_badge' => 'bg-cyan-100 text-cyan-800 font-bold',
        ],
    ];

    $tabs = [];

    // 1. All Tab (ALWAYS first as requested: "pending ar age all thakbe oitai click korle sob show hobe")
    $tabs[] = [
        'key' => 'all',
        'label' => 'সব অর্ডার',
        'count' => $totalOrdersCount ?? 0,
        'meta' => $statusMetaMap['all'],
    ];

    // 2. Pending Tab (ALWAYS second, right after All)
    $tabs[] = [
        'key' => 'pending',
        'label' => $pendingItem?->label ?? 'পেন্ডিং',
        'count' => $statusCounts['pending'] ?? 0,
        'meta' => $statusMetaMap['pending'],
    ];

    // 3. Remaining statuses
    foreach ($remainingItems as $st) {
        $meta = $statusMetaMap[$st->key] ?? [
            'label' => $st->label,
            'icon' => 'fa-tag',
            'active_class' => 'bg-brand text-white border-brand shadow-sm ring-2 ring-brand/25',
            'inactive_class' => 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 hover:border-slate-300 shadow-2xs',
            'active_badge' => 'bg-white/25 text-white font-bold',
            'inactive_badge' => 'bg-slate-100 text-slate-700 font-bold',
        ];
        $tabs[] = [
            'key' => $st->key,
            'label' => $st->label,
            'count' => $statusCounts[$st->key] ?? 0,
            'meta' => $meta,
        ];
    }
@endphp

<div class="flex items-center gap-2 sm:gap-2.5 overflow-x-auto pb-2 mb-4 scrollbar-none">
  @foreach ($tabs as $tab)
    @php
      $isActive = (($currentStatus ?? 'pending') === $tab['key']);
      $meta = $tab['meta'];
    @endphp
    <a href="{{ route('admin.orders.index', array_merge(request()->except(['page', 'status']), ['status' => $tab['key']])) }}"
       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all duration-150 shrink-0 select-none {{ $isActive ? $meta['active_class'] : $meta['inactive_class'] }}">
      <i class="fa-solid {{ $meta['icon'] }} text-xs"></i>
      <span>{{ $tab['label'] }}</span>
      <span class="px-2 py-0.5 rounded-full text-xs transition-colors {{ $isActive ? $meta['active_badge'] : $meta['inactive_badge'] }}">
        {{ number_format($tab['count']) }}
      </span>
    </a>
  @endforeach
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-4 py-3">তারিখ</th>
        <th class="text-left font-medium px-4 py-3">অর্ডার</th>
        <th class="text-left font-medium px-4 py-3">কাস্টমার</th>
        <th class="text-right font-medium px-4 py-3">মোট</th>
        <th class="text-center font-medium px-4 py-3">পেমেন্ট স্ট্যাটাস</th>
        <th class="text-center font-medium px-4 py-3">স্ট্যাটাস</th>
        <th class="text-center font-medium px-4 py-3">কুরিয়ার</th>
        <th class="text-center font-medium px-4 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      @forelse ($orders as $order)
        <tr class="hover:bg-slate-50/50 transition-colors">
          <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
            {{ $order->created_at->format('d M Y') }}<br>
            <span class="text-slate-400">{{ $order->created_at->format('h:i A') }}</span>
          </td>
          <td class="px-4 py-3">
            <p class="font-mono font-semibold text-slate-800 text-xs">{{ $order->order_number }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $order->district }}</p>
            @if ($order->ip_address)
              <div class="flex items-center gap-1 mt-0.5">
                <p class="text-[10px] text-slate-300" title="কাস্টমারের IP ঠিকানা">IP: {{ $order->ip_address }}</p>
                @php $blockedIpRow = $activeBlockedIps->get($order->ip_address); @endphp
                @if ($blockedIpRow)
                  <form method="POST" action="{{ route('admin.blocked-ips.destroy', $blockedIpRow) }}" class="js-confirm-delete inline-block" data-confirm-text="'{{ $order->ip_address }}' এখনই আনব্লক হয়ে যাবে।">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="এই IP আনব্লক করুন" class="text-[10px] text-emerald-600 hover:text-emerald-700 hover:underline">আনব্লক</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.blocked-ips.store') }}" class="js-block-ip inline-block" data-ip="{{ $order->ip_address }}">
                    @csrf
                    <input type="hidden" name="ip_address" value="{{ $order->ip_address }}">
                    <input type="hidden" name="reason" value="অর্ডার {{ $order->order_number }} থেকে ব্লক করা হয়েছে">
                    <button type="submit" title="এই IP ৩০ মিনিটের জন্য ব্লক করুন" class="text-[10px] text-red-400 hover:text-red-600 hover:underline">ব্লক</button>
                  </form>
                @endif
              </div>
            @endif
            @if ($order->steadfast_tracking_code)
              <p class="text-[10px] text-cyan-600 mt-0.5" title="Steadfast ট্র্যাকিং কোড">SF: {{ $order->steadfast_tracking_code }}</p>
            @endif
          </td>
          <td class="px-4 py-3">
            @php $waNumber = '880'.ltrim(preg_replace('/\D/', '', $order->phone), '0'); @endphp
            <p class="text-[13px] font-medium text-slate-700">
              {{ $order->customer_name }}
              @if ($order->customer && $order->customer->is_blocked)
                <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-600 align-middle">ব্লকড</span>
              @endif
            </p>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="text-[11px] text-slate-400">{{ $order->phone }}</span>
              <a href="tel:{{ $order->phone }}" title="কল করুন" class="text-slate-400 hover:text-brand">
                <i class="fas fa-phone text-[10px]"></i>
              </a>
              <a href="https://wa.me/{{ $waNumber }}" target="_blank" title="হোয়াটসঅ্যাপ" class="text-slate-400 hover:text-emerald-500">
                <i class="fab fa-whatsapp text-[11px]"></i>
              </a>
              @if ($order->customer)
                <form method="POST" action="{{ route('admin.customers.toggleBlock', $order->customer) }}" class="js-toggle-block inline-block" data-blocked="{{ $order->customer->is_blocked ? '1' : '0' }}" data-name="{{ $order->customer_name }}">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="reason" class="reason-input">
                  <button type="submit" title="{{ $order->customer->is_blocked ? 'আনব্লক করুন' : 'কাস্টমার ব্লক করুন' }}"
                          class="{{ $order->customer->is_blocked ? 'text-emerald-500 hover:text-emerald-700' : 'text-slate-400 hover:text-red-500' }}">
                    <i class="fa-solid {{ $order->customer->is_blocked ? 'fa-unlock' : 'fa-ban' }} text-[10px]"></i>
                  </button>
                </form>
              @endif
            </div>
            <!-- BD Courier Fraud Checker Trigger -->
            <div class="mt-1.5">
              <button type="button"
                      class="js-fraud-check-btn inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-all shadow-xs"
                      data-phone="{{ $order->phone }}"
                      data-customer-name="{{ $order->customer_name }}"
                      onclick="openFraudModal('{{ $order->phone }}', '{{ addslashes($order->customer_name) }}')"
                      title="কুরিয়ার ডেলিভারি ও ফ্রড রিপোর্ট দেখুন">
                <i class="fa-solid fa-shield-halved text-[10px] text-slate-400"></i>
                <span>ফ্রড চেক</span>
              </button>
            </div>
          </td>
          <td class="px-4 py-3 text-right font-bold text-slate-800">৳{{ number_format((float) $order->total, 0) }}</td>
          <td class="px-4 py-3 text-center">
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $paymentStatusColors[$order->payment_status] ?? 'bg-slate-100 text-slate-600' }}">{{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}</span>
            @if ((float) $order->due_amount > 0)
              <button type="button" class="due-payment-btn block mx-auto mt-1 text-[11px] font-medium text-brand hover:underline"
                      data-due="{{ number_format((float) $order->due_amount, 2, '.', '') }}"
                      data-url="{{ route('admin.orders.addPayment', $order) }}">
                বাকি ৳{{ number_format((float) $order->due_amount, 0) }}
              </button>
            @endif
          </td>

          <!-- Inline status dropdown -->
          <td class="px-4 py-3 text-center">
            <div class="relative inline-block text-center">
              <select class="js-order-status-select cursor-pointer text-[11px] font-semibold py-1 pl-3 pr-6 rounded-full border border-black/5 focus:outline-none focus:ring-2 focus:ring-brand/40 transition-all shadow-xs appearance-none {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600' }}"
                      data-order-id="{{ $order->id }}"
                      data-current="{{ $order->status }}"
                      data-url="{{ route('admin.orders.updateStatus', $order) }}"
                      title="অর্ডার স্ট্যাটাস পরিবর্তন করুন">
                @foreach ($statusLabels as $key => $label)
                  <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }} class="bg-white text-slate-700 font-normal">
                    {{ $label }}
                  </option>
                @endforeach
              </select>
              <i class="fa-solid fa-chevron-down text-[8px] pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 opacity-60"></i>
            </div>
          </td>

          <!-- Courier Dispatch / Status Column -->
          <td class="px-4 py-3 text-center" id="order-courier-cell-{{ $order->id }}">
            @if ($order->hasCourierConsignment())
              <div class="inline-flex flex-col items-center">
                <a href="{{ route('admin.orders.tracking', $order) }}"
                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->active_courier_name === 'pathao' ? 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' : 'bg-cyan-50 text-cyan-700 border border-cyan-200 hover:bg-cyan-100' }} transition shadow-xs"
                   title="কুরিয়ার ট্র্যাকিং দেখুন">
                  <i class="fa-solid {{ $order->active_courier_name === 'pathao' ? 'fa-motorcycle' : 'fa-box' }} text-[10px]"></i>
                  <span>{{ ucfirst($order->active_courier_name) }}</span>
                </a>
                <span class="text-[10px] text-slate-400 font-mono mt-0.5 max-w-[120px] truncate" title="{{ $order->active_tracking_code }}">
                  {{ $order->active_tracking_code }}
                </span>
              </div>
            @elseif ($activeCouriers->isEmpty())
              <span class="text-[11px] text-slate-300 font-medium" title="কোনো কুরিয়ার অন নেই">-</span>
            @elseif ($activeCouriers->count() === 1)
              @php $onlyCourier = $activeCouriers->first(); @endphp
              <button type="button"
                      class="js-send-courier-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-white transition shadow-xs {{ $onlyCourier->courier === 'pathao' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-cyan-600 hover:bg-cyan-700' }}"
                      data-order-id="{{ $order->id }}"
                      data-courier="{{ $onlyCourier->courier }}"
                      data-courier-name="{{ $onlyCourier->name }}"
                      data-url="{{ route('admin.orders.send-courier', $order) }}"
                      data-order-number="{{ $order->order_number }}">
                <i class="fa-solid fa-paper-plane text-[9px]"></i>
                <span>{{ $onlyCourier->courier === 'pathao' ? 'Pathao' : 'Steadfast' }}</span>
              </button>
            @else
              <!-- Multiple Active Couriers: Dropdown -->
              <div class="relative inline-block text-left" data-dropdown>
                <button type="button" data-dropdown-toggle
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-brand hover:bg-brandDark text-white shadow-xs transition">
                  <i class="fa-solid fa-paper-plane text-[9px]"></i>
                  <span>পাঠান</span>
                  <i class="fa-solid fa-chevron-down text-[8px]"></i>
                </button>
                <div data-dropdown-panel class="hidden absolute right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 w-44 overflow-hidden py-1 text-left">
                  @foreach ($activeCouriers as $c)
                    <button type="button"
                            class="js-send-courier-btn w-full text-left px-3.5 py-2 text-xs flex items-center gap-2 hover:bg-slate-50 transition {{ $c->courier === 'pathao' ? 'text-rose-700 hover:text-rose-800' : 'text-cyan-700 hover:text-cyan-800' }}"
                            data-order-id="{{ $order->id }}"
                            data-courier="{{ $c->courier }}"
                            data-courier-name="{{ $c->name }}"
                            data-url="{{ route('admin.orders.send-courier', $order) }}"
                            data-order-number="{{ $order->order_number }}">
                      <i class="fa-solid {{ $c->courier === 'pathao' ? 'fa-motorcycle' : 'fa-box' }}"></i>
                      <span>{{ $c->name }}</span>
                    </button>
                  @endforeach
                </div>
              </div>
            @endif
          </td>

          <td class="px-4 py-3 text-center">
            <div class="inline-flex items-center gap-1.5">
              <a href="{{ route('admin.orders.show', $order) }}" title="বিস্তারিত"
                 class="inline-flex text-brand hover:bg-brand/5 w-8 h-8 items-center justify-center rounded-lg border border-brand/20 transition-colors">
                <i class="fa-solid fa-eye text-sm"></i>
              </a>
              <a href="{{ route('admin.orders.invoice', $order) }}?auto_print=1" target="_blank" title="ইনভয়েস প্রিন্ট"
                 class="inline-flex text-slate-500 hover:bg-slate-50 w-8 h-8 items-center justify-center rounded-lg border border-slate-200 transition-colors">
                <i class="fa-solid fa-print text-sm"></i>
              </a>
              <a href="{{ route('admin.orders.edit', $order) }}" title="এডিট করুন"
                 class="inline-flex text-amber-600 hover:bg-amber-50 w-8 h-8 items-center justify-center rounded-lg border border-amber-200 transition-colors">
                <i class="fa-solid fa-pen text-sm"></i>
              </a>
              @if ($order->hasCourierConsignment())
                <a href="{{ route('admin.orders.tracking', $order) }}" title="কুরিয়ার ট্র্যাকিং"
                   class="inline-flex {{ $order->active_courier_name === 'pathao' ? 'text-rose-600 hover:bg-rose-50 border-rose-200' : 'text-cyan-600 hover:bg-cyan-50 border-cyan-200' }} w-8 h-8 items-center justify-center rounded-lg border transition-colors">
                  <i class="fa-solid {{ $order->active_courier_name === 'pathao' ? 'fa-motorcycle' : 'fa-truck-fast' }} text-sm"></i>
                </a>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="px-4 py-16 text-center text-slate-400">
            <i class="fa-solid fa-cart-shopping text-4xl mb-3 opacity-40"></i>
            <p class="font-medium">কোনো অর্ডার পাওয়া যায়নি।</p>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
  @if ($orders->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $orders->links() }}</div>
  @endif
</div>

<!-- Due Payment Modal -->
<div id="duePaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-900">বকেয়া টাকা গ্রহণ</h3>
      <button type="button" onclick="closeDueModal()" class="text-slate-400 hover:text-slate-600">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>
    <p class="text-sm text-slate-500 mb-4">মোট বকেয়া: <span id="dueModalDue" class="font-semibold text-red-600"></span></p>
    <form id="duePaymentForm" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">পরিমাণ *</label>
        <input type="number" name="amount" id="dueModalAmount" min="0.01" step="0.01" required
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">মাধ্যম *</label>
        <select name="method" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          <option value="cash">ক্যাশ / হাতে হাতে</option>
          <option value="bkash">বিকাশ</option>
          <option value="rocket">রকেট</option>
          <option value="nagad">নগদ</option>
          <option value="bank">ব্যাংক ট্রান্সফার</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">নোট</label>
        <input type="text" name="note" maxlength="255" placeholder="ঐচ্ছিক"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <button type="submit" class="w-full bg-brand hover:bg-brandDark text-white font-semibold py-2.5 rounded-lg transition-colors">টাকা গ্রহণ নিশ্চিত করুন</button>
    </form>
  </div>
</div>

@include('admin.orders.partials.fraud-modal')
@endsection

@push('scripts')
@include('admin.partials.block-scripts')
<script>
  var statusLabels = @json($statusLabels);
  var statusColors = @json($statusColors);

  // ---- Inline order status select handler ----
  document.querySelectorAll('.js-order-status-select').forEach(function (selectEl) {
    selectEl.addEventListener('change', function () {
      var newStatus = selectEl.value;
      var previousStatus = selectEl.dataset.current;
      var url = selectEl.dataset.url;
      var orderId = selectEl.dataset.orderId;

      selectEl.disabled = true;

      fetch(url, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus }),
      })
      .then(function (res) {
        return res.json().then(function (data) { return { ok: res.ok, data: data }; });
      })
      .then(function (result) {
        selectEl.disabled = false;
        if (result.ok && result.data.success) {
          selectEl.dataset.current = newStatus;
          if (statusColors[newStatus]) {
            // Remove previous bg/text color classes and assign new ones
            selectEl.className = selectEl.className.replace(/bg-\S+|text-\S+/g, '').trim();
            selectEl.className += ' ' + statusColors[newStatus];
          }
          showToast('অর্ডার স্ট্যাটাস আপডেট হয়েছে।', 'success');
          setTimeout(function () {
            window.location.reload();
          }, 600);
        } else {
          selectEl.value = previousStatus;
          var msg = (result.data && result.data.message) || 'স্ট্যাটাস আপডেট ব্যর্থ হয়েছে।';
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'আপডেট ব্যর্থ হয়েছে',
              text: msg,
              confirmButtonColor: '#4f46e5'
            });
          } else {
            showToast(msg, 'error');
          }
        }
      })
      .catch(function () {
        selectEl.disabled = false;
        selectEl.value = previousStatus;
        showToast('কানেকশন সমস্যা হয়েছে।', 'error');
      });
    });
  });

  // ---- Due payment modal ----
  var duePaymentForm = document.getElementById('duePaymentForm');

  function openDueModal(due, url) {
    document.getElementById('dueModalDue').textContent = '৳' + Number(due).toLocaleString('en-US', { minimumFractionDigits: 2 });
    var amountInput = document.getElementById('dueModalAmount');
    amountInput.value = due;
    amountInput.max = due;
    duePaymentForm.dataset.url = url;
    document.getElementById('duePaymentModal').classList.remove('hidden');
  }

  function closeDueModal() {
    document.getElementById('duePaymentModal').classList.add('hidden');
  }

  document.querySelectorAll('.due-payment-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openDueModal(btn.dataset.due, btn.dataset.url);
    });
  });

  duePaymentForm.addEventListener('submit', function (e) {
    e.preventDefault();
    fetch(duePaymentForm.dataset.url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: new FormData(duePaymentForm),
    })
      .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
      .then(function (result) {
        if (result.ok && result.data.success) {
          closeDueModal();
          showToast('পেমেন্ট গ্রহণ করা হয়েছে।', 'success');
          setTimeout(function () { window.location.reload(); }, 700);
        } else {
          var message = (result.data.errors && Object.values(result.data.errors)[0][0]) || result.data.message || 'পেমেন্ট ব্যর্থ হয়েছে।';
          showToast(message, 'error');
        }
      })
      .catch(function () {
        showToast('কানেকশন সমস্যা হয়েছে।', 'error');
      });
  });

  // Automatically check visible orders with slight staggering
  document.addEventListener('DOMContentLoaded', function () {
    var fraudBtns = document.querySelectorAll('.js-fraud-check-btn');
    var checkedNumbers = new Set();

    fraudBtns.forEach(function (btn, idx) {
      var phone = btn.getAttribute('data-phone');
      if (!phone || checkedNumbers.has(phone)) return;
      checkedNumbers.add(phone);

      setTimeout(function () {
        fetch('{{ route('admin.fraud-check.lookup') }}?phone=' + encodeURIComponent(phone), {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
          if (json && json.success && json.data) {
            if (typeof updateOrderRowBadges === 'function') {
              updateOrderRowBadges(phone, json.data);
            }
          }
        })
        .catch(function () {});
      }, idx * 150);
    });
  });

  // Send to Courier Action
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.js-send-courier-btn');
    if (!btn) return;

    e.preventDefault();
    var courier = btn.getAttribute('data-courier');
    var courierName = btn.getAttribute('data-courier-name') || (courier === 'pathao' ? 'Pathao Courier' : 'Steadfast Courier');
    var orderNumber = btn.getAttribute('data-order-number') || '';
    var url = btn.getAttribute('data-url');

    Swal.fire({
      title: courierName + '-এ অর্ডার পাঠাতে চান?',
      text: 'অর্ডার নম্বর: ' + orderNumber,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'হ্যাঁ, কুরিয়ারে পাঠান',
      cancelButtonText: 'বাতিল',
      confirmButtonColor: '#4f46e5'
    }).then(function (result) {
      if (result.isConfirmed) {
        Swal.fire({
          title: courierName + '-এ পাঠানো হচ্ছে...',
          text: 'অনুগ্রহ করে অপেক্ষা করুন',
          allowOutsideClick: false,
          didOpen: function () { Swal.showLoading(); }
        });

        fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ courier: courier })
        })
        .then(function (res) {
          return res.json().then(function (data) { return { ok: res.ok, data: data }; });
        })
        .then(function (res) {
          if (res.ok && res.data.success) {
            Swal.fire({
              icon: 'success',
              title: 'সফল!',
              text: res.data.message,
              confirmButtonColor: '#4f46e5'
            }).then(function () {
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'ব্যর্থ হয়েছে',
              text: res.data.message || 'কুরিয়ারে অর্ডার পাঠানো যায়নি।',
              confirmButtonColor: '#4f46e5'
            });
          }
        })
        .catch(function () {
          Swal.fire({
            icon: 'error',
            title: 'ত্রুটি',
            text: 'সার্ভার অনুরোধ প্রক্রিয়াকরণে ব্যর্থ হয়েছে।',
            confirmButtonColor: '#4f46e5'
          });
        });
      }
    });
  });
</script>
@endpush
