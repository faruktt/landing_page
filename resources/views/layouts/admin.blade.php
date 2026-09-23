<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — {{ $siteSettings->site_name ?? 'BUNON' }} Admin</title>
@if (!empty($siteSettings?->favicon))
  <link rel="icon" href="{{ $siteSettings->favicon_url ?: asset($siteSettings->favicon) }}">
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: '#4f46e5',
          brandDark: '#3730a3',
        },
        fontFamily: {
          display: ['Poppins', 'sans-serif'],
          sans: ['"Hind Siliguri"', 'sans-serif'],
        },
      },
    },
  }
</script>
<style>
  body { font-family: 'Hind Siliguri', sans-serif; }
  .font-display { font-family: 'Poppins', sans-serif; }
  .sidebar-gradient { background: linear-gradient(180deg, #1e1b4b 0%, #171433 100%); }
  .nav-link.is-active { box-shadow: 0 2px 10px rgba(79,70,229,.35); }
  .sidebar-scroll::-webkit-scrollbar { width: 3px; }
  .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }
</style>
@stack('styles')
</head>

<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar-gradient fixed inset-y-0 left-0 z-40 w-64 text-slate-300 -translate-x-full lg:translate-x-0 transition-transform duration-200 flex flex-col">
    <div class="h-16 flex items-center gap-3 px-6 border-b border-white/10 shrink-0">
      <div class="h-9 w-9 rounded-xl bg-brand flex items-center justify-center shadow-lg shadow-brand/30 overflow-hidden">
        @if (!empty($siteSettings?->logo))
          <img src="{{ $siteSettings->logo_url ?: asset($siteSettings->logo) }}" class="h-full w-full object-cover">
        @else
          <i class="fa-solid fa-bag-shopping text-white text-base"></i>
        @endif
      </div>
      <span class="font-display text-lg font-bold text-white">{{ $siteSettings->site_name ?? 'BUNON' }}</span>
    </div>

    <nav class="sidebar-scroll flex-1 overflow-y-auto px-3 py-5 space-y-1">
      <p class="px-3 text-[11px] uppercase tracking-widest text-slate-500 font-semibold mb-2">Main</p>

      <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-house fa-fw shrink-0"></i>
        Dashboard
      </a>

      @can('elevated-access')
      <div class="h-px bg-white/[.06] my-3"></div>
      <p class="px-3 text-[11px] uppercase tracking-widest text-slate-500 font-semibold mb-2">Catalog</p>

      <a href="{{ route('admin.products.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-bag-shopping fa-fw shrink-0"></i>
        Products
        @if ($navLowStockCount > 0 && request()->routeIs('admin.products.*') === false)
          <span class="ml-auto text-[10px] font-bold bg-amber-400/20 text-amber-300 px-1.5 py-0.5 rounded-full">{{ $navLowStockCount }}</span>
        @endif
      </a>
      <a href="{{ route('admin.categories.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.categories.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-table-cells fa-fw shrink-0"></i>
        Categories
      </a>
      @endcan

      <div class="h-px bg-white/[.06] my-3"></div>
      <p class="px-3 text-[11px] uppercase tracking-widest text-slate-500 font-semibold mb-2">Sales</p>

      <a href="{{ route('admin.orders.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.orders.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-cart-shopping fa-fw shrink-0"></i>
        Orders
        @if ($navPendingOrdersCount > 0 && request()->routeIs('admin.orders.*') === false)
          <span class="ml-auto text-[10px] font-bold bg-red-400/20 text-red-300 px-1.5 py-0.5 rounded-full">{{ $navPendingOrdersCount }}</span>
        @endif
      </a>
      <a href="{{ route('admin.order-statuses.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.order-statuses.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-tags fa-fw shrink-0"></i>
        Order Status
      </a>
      <a href="{{ route('admin.customers.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.customers.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-users fa-fw shrink-0"></i>
        Customers
      </a>
      <a href="{{ route('admin.incomplete-orders.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.incomplete-orders.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-cart-shopping fa-fw shrink-0"></i>
        Incomplete Orders
      </a>
      @can('elevated-access')
      <a href="{{ route('admin.couriers.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.couriers.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-truck-fast fa-fw shrink-0"></i>
        Courier Settings
      </a>
      <a href="{{ route('admin.fraud-check.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.fraud-check.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-shield-halved fa-fw shrink-0"></i>
        Fraud Checker
      </a>
      <a href="{{ route('admin.blocked-ips.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.blocked-ips.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-network-wired fa-fw shrink-0"></i>
        IP Address
      </a>
      @endcan

      @can('admin-access')
      <div class="h-px bg-white/[.06] my-3"></div>
      <p class="px-3 text-[11px] uppercase tracking-widest text-slate-500 font-semibold mb-2">System</p>
      <a href="{{ route('admin.users.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-user-shield fa-fw shrink-0"></i>
        Users
      </a>
      <a href="{{ route('admin.settings.edit') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.settings.*') ? 'is-active bg-brand text-white' : 'hover:bg-white/5 text-slate-300' }}">
        <i class="fa-solid fa-gear fa-fw shrink-0"></i>
        Settings
      </a>
      @endcan
    </nav>

    <div class="p-4 border-t border-white/10">
      <div class="flex items-center gap-3 px-2">
        <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-brand to-violet-600 flex items-center justify-center text-white text-sm font-bold shrink-0 shadow-sm">
          {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>
        <div class="min-w-0">
          <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
          <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- Sidebar overlay (mobile) -->
  <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

  <!-- Main -->
  <div class="flex-1 flex flex-col lg:pl-64 min-w-0">

    <!-- Topbar -->
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">
      <div class="flex items-center gap-3 min-w-0">
        <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-800 shrink-0">
          <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <h1 class="font-display text-lg font-semibold text-slate-900 truncate">@yield('title', 'Dashboard')</h1>
      </div>

      <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">

        <!-- Live clock -->
        <div class="hidden md:flex items-center gap-1.5 text-xs text-slate-400 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl select-none">
          <i class="fa-regular fa-clock text-xs"></i>
          <span id="liveClock"></span>
        </div>

        <!-- Notification bell -->
        <div class="relative" data-dropdown>
          <button data-dropdown-toggle class="relative w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ $navTotalNotifications > 0 ? 'bg-red-50 hover:bg-red-100 text-red-500' : 'bg-slate-50 hover:bg-slate-100 text-slate-500' }}">
            <i class="fa-regular fa-bell text-lg"></i>
            @if ($navTotalNotifications > 0)
              <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1 leading-none shadow-sm">{{ $navTotalNotifications > 99 ? '99+' : $navTotalNotifications }}</span>
            @endif
          </button>

          <div data-dropdown-panel class="hidden absolute right-0 top-[calc(100%+8px)] w-80 max-w-[90vw] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
              <span class="text-sm font-bold text-slate-800">Notifications</span>
              @if ($navTotalNotifications > 0)
                <span class="text-[10px] font-bold bg-red-500 text-white px-2 py-0.5 rounded-full">{{ $navTotalNotifications }} alerts</span>
              @else
                <span class="text-[10px] text-slate-400">All clear</span>
              @endif
            </div>

            <div class="max-h-72 overflow-y-auto">
              <p class="px-4 pt-3 pb-1 text-[10px] uppercase tracking-wide font-semibold text-slate-400">পেন্ডিং অর্ডার</p>
              @forelse ($navPendingOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors">
                  <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-700 truncate">{{ $order->order_number }} &middot; {{ $order->customer_name }}</p>
                    <p class="text-[11px] text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                  </div>
                  <span class="text-xs font-bold text-slate-700 shrink-0">৳{{ number_format((float) $order->total, 0) }}</span>
                </a>
              @empty
                <p class="px-4 py-4 text-xs text-slate-400">কোনো পেন্ডিং অর্ডার নেই।</p>
              @endforelse

              @can('elevated-access')
              <p class="px-4 pt-3 pb-1 text-[10px] uppercase tracking-wide font-semibold text-slate-400 border-t border-slate-50 mt-1">লো স্টক প্রোডাক্ট</p>
              @forelse ($navLowStockProducts as $product)
                <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors">
                  <div class="w-8 h-8 rounded-lg {{ $product->stock <= 0 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-bag-shopping text-sm"></i>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-700 truncate">{{ $product->name }}</p>
                    <p class="text-[11px] text-slate-400">{{ $product->stock <= 0 ? 'স্টক শেষ' : $product->stock.' পিস বাকি' }}</p>
                  </div>
                </a>
              @empty
                <p class="px-4 py-4 text-xs text-slate-400">সব প্রোডাক্টে স্টক পর্যাপ্ত আছে।</p>
              @endforelse
              @endcan
            </div>
          </div>
        </div>

        <!-- Profile dropdown -->
        <div class="relative" data-dropdown>
          <button data-dropdown-toggle class="flex items-center gap-2 pl-1.5 pr-2.5 py-1.5 rounded-xl hover:bg-slate-100 transition-colors">
            <div class="h-7 w-7 rounded-lg bg-gradient-to-br from-brand to-violet-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
              {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <span class="hidden sm:block text-sm font-medium text-slate-700">{{ Str::limit(auth()->user()->name ?? '', 14) }}</span>
            <i class="fa-solid fa-chevron-down text-xs text-slate-400 hidden sm:block"></i>
          </button>

          <div data-dropdown-panel class="hidden absolute right-0 top-[calc(100%+8px)] w-56 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">
            <div class="px-4 py-3.5 bg-gradient-to-br from-brand to-violet-600">
              <p class="text-white text-sm font-bold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
              <p class="text-indigo-200 text-xs truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
            <div class="py-1.5">
              <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-user-gear text-slate-400 fa-fw"></i>
                প্রোফাইল সেটিংস
              </a>
              <div class="h-px bg-slate-100 my-1 mx-3"></div>
              <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                  <i class="fa-solid fa-right-from-bracket fa-fw"></i>
                  লগআউট
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Page content -->
    <main class="flex-1 p-4 sm:p-6">
      @yield('content')
    </main>

    <footer class="text-center text-xs text-slate-400 py-4 border-t border-slate-200 bg-white/60">
      {{ $siteSettings->site_name ?? 'BUNON' }} Admin &mdash; &copy; {{ date('Y') }} {{ $siteSettings->site_name ?? 'BUNON' }}. All rights reserved.
    </footer>
  </div>
</div>

<script>
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  var toggle = document.getElementById('sidebarToggle');
  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  }
  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  }
  toggle?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);

  // ---- Live clock ----
  (function () {
    var el = document.getElementById('liveClock');
    if (!el) return;
    var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    function tick() {
      var now = new Date();
      var h = String(now.getHours()).padStart(2, '0');
      var m = String(now.getMinutes()).padStart(2, '0');
      el.textContent = days[now.getDay()] + ' ' + String(now.getDate()).padStart(2, '0') + ' ' + months[now.getMonth()] + ' · ' + h + ':' + m;
    }
    tick();
    setInterval(tick, 1000 * 30);
  })();

  // ---- Generic dropdown toggle (notifications, profile) ----
  document.querySelectorAll('[data-dropdown]').forEach(function (dd) {
    var toggleBtn = dd.querySelector('[data-dropdown-toggle]');
    var panel = dd.querySelector('[data-dropdown-panel]');
    toggleBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = !panel.classList.contains('hidden');
      document.querySelectorAll('[data-dropdown-panel]').forEach(function (p) { p.classList.add('hidden'); });
      panel.classList.toggle('hidden', isOpen);
    });
  });
  document.addEventListener('click', function () {
    document.querySelectorAll('[data-dropdown-panel]').forEach(function (p) { p.classList.add('hidden'); });
  });

  // ---- Toast helper (top-right) ----
  function showToast(message, icon) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: icon || 'success',
      title: message,
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
  }

  @if (session('status'))
    showToast(@json(session('status')), 'success');
  @endif

  @if (session('error'))
    showToast(@json(session('error')), 'error');
  @endif

  // ---- SweetAlert2 confirmation for delete forms ----
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.classList.contains('js-confirm-delete')) return;

    e.preventDefault();
    Swal.fire({
      title: form.dataset.confirmTitle || 'আপনি কি নিশ্চিত?',
      text: form.dataset.confirmText || 'এই কাজটি ফিরিয়ে আনা যাবে না।',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'হ্যাঁ, মুছে ফেলুন',
      cancelButtonText: 'বাতিল',
      confirmButtonColor: '#dc2626',
      reverseButtons: true,
    }).then(function (result) {
      if (result.isConfirmed) form.submit();
    });
  });
</script>
@stack('scripts')
</body>
</html>
