@php
  $siteSettings = $siteSettings ?? \App\Models\Setting::current();
  $headerWaNum = $siteSettings->whatsapp ?: $siteSettings->phone;
  $headerWaDigits = preg_replace('/[^0-9]/', '', $headerWaNum ?? '');
  if (strlen($headerWaDigits) === 11 && str_starts_with($headerWaDigits, '01')) {
      $headerWaDigits = '88' . $headerWaDigits;
  }
  $headerOrderTarget = $orderBtnTarget ?? '#order-section';
  $headerOrderLabel = $orderBtnLabel ?? 'অর্ডার করুন';
  $headerHomeUrl = url('/');
@endphp

<header class="sticky top-0 z-50 bg-blue-600/95 backdrop-blur border-b border-blue-700 shadow-sm">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 h-14 sm:h-16 flex items-center justify-between gap-3">
    <!-- Brand / Logo -->
    <div class="flex items-center gap-2.5 min-w-0 flex-1">
      <a href="{{ $headerHomeUrl }}" class="flex items-center min-w-0 hover:opacity-90 transition-opacity">
        @if (!empty($siteSettings->logo))
          <img src="{{ $siteSettings->logo_url ?: asset($siteSettings->logo) }}" class="h-9 sm:h-10 w-auto max-w-[160px] object-contain shrink-0" alt="{{ $siteSettings->site_name }}">
        @else
          <p class="font-display font-bold text-lg sm:text-xl text-white truncate">{{ $siteSettings->site_name ?? 'Shop' }}</p>
        @endif
      </a>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-2.5 sm:gap-4 shrink-0">
      <!-- WhatsApp (Clean big icon on mobile, styled badge with number on desktop) -->
      @if (!empty($headerWaNum))
        <a href="https://wa.me/{{ $headerWaDigits }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center p-1 sm:py-1.5 sm:px-3 sm:gap-1.5 text-xs sm:text-sm font-semibold text-white sm:rounded-full sm:bg-emerald-500/30 sm:hover:bg-emerald-500/40 sm:border sm:border-emerald-400/40 sm:shadow-xs transition-transform active:scale-90"
           title="WhatsApp: {{ $headerWaNum }}">
          <i class="fa-brands fa-whatsapp text-[26px] sm:text-base text-[#25D366] sm:text-emerald-300 hover:brightness-110 transition-all"></i>
          <span class="font-numeric hidden sm:inline">{{ $headerWaNum }}</span>
        </a>
      @endif

      <!-- Order / Action Button -->
      <a href="{{ $headerOrderTarget }}" class="shrink-0 whitespace-nowrap bg-white hover:bg-amber-50 text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-bold px-3.5 sm:px-5 py-1.5 sm:py-2.5 rounded-full shadow-sm transition-all hover:scale-105 inline-flex items-center gap-1.5">
        <i class="fa-solid fa-cart-shopping text-xs"></i>
        <span>{{ $headerOrderLabel }}</span>
      </a>
    </div>
  </div>
</header>
