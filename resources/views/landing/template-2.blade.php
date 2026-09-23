<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $product->name }}</title>
<meta name="description" content="{{ $product->short_description }} {{ $product->description }}">
@php $faviconSettings = \App\Models\Setting::current(); @endphp
@if (!empty($faviconSettings->favicon))
  <link rel="icon" href="{{ $faviconSettings->favicon_url ?: asset($faviconSettings->favicon) }}">
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: '#2f7d32',
          brandDark: '#1f5c22',
          brandLight: '#eef7ec',
          cream: '#fbf9f4',
        },
        fontFamily: {
          sans: ['"Hind Siliguri"', 'sans-serif'],
        },
      },
    },
  }
</script>
<style>
  html { scroll-behavior: smooth; }
  body { font-family: 'Hind Siliguri', sans-serif; }
  .circled { position: relative; display: inline-block; padding: 0 10px; }
  .reveal { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; }
  .reveal.in { opacity: 1; transform: translateY(0); }
  .benefit-card { transition: transform .25s ease, box-shadow .25s ease; }
  .benefit-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -8px rgba(47,125,50,.25); }

  .cta-btn, .shine-btn { position: relative; overflow: hidden; isolation: isolate; }
  .cta-btn::after, .shine-btn::after {
    content: '';
    position: absolute; inset: 0 auto 0 -60%;
    width: 40%;
    background: linear-gradient(120deg, transparent, rgba(255,255,255,.55), transparent);
    transform: skewX(-20deg);
    transition: left .7s ease;
  }
  .cta-btn:hover::after, .shine-btn:hover::after { left: 130%; }
  .cta-btn svg, .shine-btn svg { transition: transform .35s ease; }
  .cta-btn:hover svg, .shine-btn:hover svg { transform: scale(1.2) rotate(-10deg); }
  .cta-btn { animation: ctaPulse 2.2s ease-in-out infinite; }
  .cta-btn:hover { animation-play-state: paused; }
  @keyframes ctaPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,.45), 0 10px 25px -8px rgba(220,38,38,.35); }
    50% { box-shadow: 0 0 0 10px rgba(220,38,38,0), 0 10px 25px -8px rgba(220,38,38,.35); }
  }
</style>
@include('partials.tracking-scripts')
</head>

<body class="bg-cream text-gray-800 antialiased">

@php
    $siteSettings = \App\Models\Setting::current();
    $mainImage = $product->image_url ?: ($product->image ? asset($product->image) : 'https://placehold.co/900x720/eef7ec/2f7d32?text='.urlencode($product->name));
    $discount = $product->discountPercent();
    $unitPrice = (float) ($product->sale_price ?? $product->regular_price);
    $avgRating = $product->reviews->count() ? round($product->reviews->avg('rating'), 1) : null;
@endphp

<!-- Sticky header matching home.blade.php -->
@include('partials.site-header', ['orderBtnTarget' => '#order', 'orderBtnLabel' => 'অর্ডার করুন'])

<!-- Hero -->
<section class="max-w-2xl mx-auto px-4 sm:px-6 pt-8 sm:pt-12 pb-6 text-center">

  <div class="reveal">
    <h1 class="text-3xl sm:text-4xl font-bold leading-tight mb-2">{{ $product->name }}</h1>
    @if ($product->short_description)
      <p class="text-brand font-semibold text-lg mb-5">{{ $product->short_description }}</p>
    @endif
  </div>

  <div class="reveal">
    <div class="relative aspect-[4/3.2] overflow-hidden rounded-2xl shadow-xl ring-1 ring-brand/10">
      <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
      <span class="absolute top-4 left-4 bg-brand text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">১০০% অর্গানিক</span>
      @if ($discount)
        <span class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">{{ $discount }}% ছাড়</span>
      @endif
    </div>
  </div>

  <div class="reveal mt-5">
    <div class="bg-white border border-brand/15 shadow-sm rounded-xl p-5 mb-5 inline-block">
      <div class="flex items-baseline justify-center gap-3 flex-wrap">
        <span class="text-3xl font-bold text-brand">৳{{ number_format($unitPrice, 0) }}</span>
        @if ($discount)
          <span class="text-gray-400 line-through text-lg">৳{{ number_format((float) $product->regular_price, 0) }}</span>
        @endif
      </div>
      <p class="text-xs text-gray-400 mt-1">সীমিত সময়ের বিশেষ ছাড়, অফার শেষ হবে খুব শীঘ্রই</p>
    </div>

    <div class="flex flex-wrap justify-center gap-4">
      <a href="#order" class="cta-btn inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3.5 rounded-full transition-all hover:-translate-y-0.5 hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.98-4.684 2.57-7.135.106-.44-.239-.865-.694-.865H5.106M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
        অর্ডার করতে চাই
      </a>
    </div>
  </div>
</section>

<!-- Countdown strip -->
<section class="reveal">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 -mt-2 mb-14">
    <div class="bg-gradient-to-r from-brandDark to-brand rounded-2xl px-6 sm:px-10 py-6 shadow-lg shadow-brand/20 flex flex-col sm:flex-row items-center justify-between gap-5">
      <p class="text-white font-semibold text-sm sm:text-base flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        সীমিত সময়ের অফার শেষ হবে
      </p>
      <div class="flex gap-2.5 sm:gap-3">
        <div class="bg-white/15 rounded-lg w-14 sm:w-16 py-2 text-center">
          <p id="cd-days" class="text-lg sm:text-xl font-bold text-white leading-none">03</p>
          <p class="text-[10px] text-white/80 mt-1">Days</p>
        </div>
        <div class="bg-white/15 rounded-lg w-14 sm:w-16 py-2 text-center">
          <p id="cd-hours" class="text-lg sm:text-xl font-bold text-white leading-none">00</p>
          <p class="text-[10px] text-white/80 mt-1">Hours</p>
        </div>
        <div class="bg-white/15 rounded-lg w-14 sm:w-16 py-2 text-center">
          <p id="cd-mins" class="text-lg sm:text-xl font-bold text-white leading-none">00</p>
          <p class="text-[10px] text-white/80 mt-1">Minutes</p>
        </div>
        <div class="bg-white/15 rounded-lg w-14 sm:w-16 py-2 text-center">
          <p id="cd-secs" class="text-lg sm:text-xl font-bold text-white leading-none">00</p>
          <p class="text-[10px] text-white/80 mt-1">Seconds</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Trust badges -->
<section class="border-y border-brand/10 bg-brandLight/60 reveal">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-7 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
    <div class="flex flex-col items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
      <p class="text-sm font-medium">১০০% অর্গানিক</p>
    </div>
    <div class="flex flex-col items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a1.5 1.5 0 0 0 1.5-1.5V6.75a1.5 1.5 0 0 0-1.5-1.5h-15a1.5 1.5 0 0 0-1.5 1.5v10.5a1.5 1.5 0 0 0 1.5 1.5Z" /></svg>
      <p class="text-sm font-medium">ক্যাশ অন ডেলিভারি</p>
    </div>
    <div class="flex flex-col items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
      <p class="text-sm font-medium">দ্রুত ডেলিভারি</p>
    </div>
    <div class="flex flex-col items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 0h-12" /></svg>
      <p class="text-sm font-medium">সহজ রিটার্ন</p>
    </div>
  </div>
</section>

<!-- Why choose -->
@if ($product->benefits->count())
<section class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-10 reveal">
  <div class="text-center mb-4">
    <p class="uppercase tracking-[0.2em] text-xs text-brand font-semibold mb-2">বৈশিষ্ট্য</p>
    <h2 class="text-2xl sm:text-3xl font-bold">{{ $product->name }} কেন নিবেন?</h2>
  </div>
  @if ($product->description)
    <p class="text-red-600 text-center text-sm sm:text-base leading-relaxed max-w-2xl mx-auto mb-10">
      {{ $product->description }}
    </p>
  @endif

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($product->benefits as $benefit)
      <div class="benefit-card bg-white border border-gray-100 rounded-xl p-5 flex gap-3">
        <span class="h-9 w-9 rounded-full bg-brandLight text-brand flex items-center justify-center shrink-0 font-bold">✔</span>
        <p class="text-sm text-gray-600">{{ $benefit->text }}</p>
      </div>
    @endforeach
  </div>

  <p class="text-brand text-center text-xl sm:text-2xl font-bold mt-10">এখন মাত্র ৳{{ number_format($unitPrice, 0) }}</p>
  <div class="text-center mt-6">
    <a href="#order" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3.5 rounded-full shadow-lg shadow-red-600/20 transition-all hover:-translate-y-0.5">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.98-4.684 2.57-7.135.106-.44-.239-.865-.694-.865H5.106M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
      অর্ডার করতে চাই
    </a>
  </div>
</section>
@endif

@if ($product->youtube_embed_url)
<!-- Product video -->
<section class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-10 reveal">
  <div class="text-center mb-10">
    <p class="uppercase tracking-[0.2em] text-xs text-brand font-semibold mb-2">প্রোডাক্ট ভিডিও</p>
    <h2 class="font-display text-2xl sm:text-3xl font-bold">দেখে নিন কিভাবে কাজ করে</h2>
  </div>
  <div class="aspect-video rounded-2xl overflow-hidden shadow-xl border border-gray-100">
    <iframe class="w-full h-full" src="{{ $product->youtube_embed_url }}" title="{{ $product->name }}" loading="lazy"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
  </div>
</section>
@endif

@if ($product->trustPoints->count())
<!-- Why buy from us -->
<section class="bg-brandLight/60 border-y border-brand/10 py-6 sm:py-10 reveal">
  <div class="max-w-5xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10">
      <p class="uppercase tracking-[0.2em] text-xs text-brand font-semibold mb-2">আমাদের প্রতিশ্রুতি</p>
      <h2 class="text-2xl sm:text-3xl font-bold">আমাদের কাছে কেনো কিনবেন?</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach ($product->trustPoints as $point)
        <div class="benefit-card bg-white rounded-xl p-5 flex gap-3">
          <span class="h-9 w-9 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0">▶</span>
          <p class="text-sm text-gray-600">{{ $point->text }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- Promo banner -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-10 reveal">
  <div class="grid md:grid-cols-2 gap-0 items-stretch bg-brandDark text-white rounded-2xl overflow-hidden shadow-xl">
    <div class="aspect-[4/3] md:aspect-auto">
      <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
    </div>
    <div class="p-8 sm:p-12 flex flex-col justify-center">
      <p class="uppercase tracking-[0.2em] text-xs text-white/70 font-semibold mb-4">{{ $siteSettings->site_name }} ORGANIC</p>
      <h2 class="text-2xl sm:text-3xl font-bold mb-4 leading-tight">{{ $product->name }}</h2>
      <p class="text-white/70 mb-8 max-w-sm">{{ $product->short_description ?: $product->description }}</p>
      <a href="#order" class="shine-btn inline-block bg-white text-brandDark px-7 py-3.5 text-sm font-semibold tracking-wide rounded-full hover:bg-brandLight transition-all hover:scale-105 hover:-translate-y-0.5 w-fit shadow-lg shadow-black/10">এখনই অর্ডার করুন</a>
    </div>
  </div>
</section>

<!-- Reviews -->
@if ($product->reviews->count())
<section class="bg-brandLight/60 border-y border-brand/10 py-6 sm:py-10 reveal">
  <div class="max-w-5xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-4">
      <p class="uppercase tracking-[0.2em] text-xs text-brand font-semibold mb-2">গ্রাহকের মতামত</p>
      <h2 class="text-2xl sm:text-3xl font-bold mb-3">গ্রাহকের আস্থা</h2>
      <div class="flex items-center justify-center gap-2">
        <div class="flex text-yellow-400">
          @for ($i = 1; $i <= 5; $i++)
            <svg class="h-5 w-5 {{ $i <= round($avgRating) ? 'fill-current' : 'fill-current text-gray-200' }}" viewBox="0 0 20 20"><path d="M10 15.27 16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
          @endfor
        </div>
        <span class="text-sm text-gray-500">{{ $avgRating }}/৫ — {{ $product->reviews->count() }}টি রিভিউ থেকে</span>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5 mt-10">
      @foreach ($product->reviews as $review)
        <div class="benefit-card bg-white rounded-xl p-6 shadow-sm">
          <div class="flex text-yellow-400 mb-3">
            @for ($i = 1; $i <= 5; $i++)
              <svg class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'fill-current text-gray-200' }}" viewBox="0 0 20 20"><path d="M10 15.27 16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
            @endfor
          </div>
          <p class="text-sm text-gray-600 mb-5">"{{ $review->comment }}"</p>
          <div class="flex items-center gap-3">
            <img src="https://i.pravatar.cc/100?img={{ ($review->id % 70) + 1 }}" class="h-9 w-9 rounded-full object-cover" alt="গ্রাহক">
            <div>
              <p class="text-sm font-medium">{{ $review->customer_name }}</p>
              @if ($review->location)
                <p class="text-xs text-gray-400">{{ $review->location }} · Verified Buyer</p>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif


<!-- Contact banner -->
<section class="bg-gradient-to-r from-brandDark to-brand reveal">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex items-center justify-center gap-2 text-white">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a1.5 1.5 0 0 0 1.5-1.5v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.5 1.5 0 0 0-1.529.44l-.973 1.168a12.06 12.06 0 0 1-5.62-5.62l1.168-.973a1.5 1.5 0 0 0 .44-1.529L8.313 3.852a1.125 1.125 0 0 0-1.091-.852H5.75a1.5 1.5 0 0 0-1.5 1.5Z" /></svg>
    <h2 class="text-base sm:text-lg font-semibold">যে কোন প্রয়োজনে যোগাযোগ করুন @if ($siteSettings->phone) — {{ $siteSettings->phone }} @endif</h2>
  </div>
</section>

<!-- Order form -->
<section id="order" class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-10 reveal">
  <div class="text-center mb-10">
    <p class="uppercase tracking-[0.2em] text-xs text-brand font-semibold mb-2">চেকআউট</p>
    <h2 class="text-2xl sm:text-3xl font-bold">অর্ডার করতে নিচের ফর্মটি পূরন করুন</h2>
  </div>

  <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-5 sm:p-10">
    @if ($orderBlocked)
      @include('landing.partials.order-blocked')
    @else
    <form id="checkoutForm" method="POST" action="{{ route('orders.store') }}" class="grid md:grid-cols-2 gap-10">
      @csrf
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <input type="hidden" name="quantity" id="qtyInput" value="1">

      @include('landing.partials.related-addons-picker')

      <!-- Billing details -->
      <div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1.5">আপনার নাম *</label>
            <input type="text" name="customer_name" required placeholder="আপনার নাম লিখুন" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1.5">আপনার ফোন নাম্বারটি লিখুন *</label>
            @include('landing.partials.phone-format-hint')
            <input type="tel" name="phone" id="phoneInput" required maxlength="11" inputmode="numeric" pattern="01[3-9][0-9]{8}" autocomplete="off" placeholder="01XXXXXXXXX" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            <p id="phoneLengthError" class="hidden text-xs text-red-500 mt-1">সঠিক মোবাইল নম্বর দিন — ০১৩ থেকে ০১৯ দিয়ে শুরু হবে, মোট ১১ ডিজিট।</p>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1.5">আপনার সম্পূর্ণ ঠিকানা *</label>
            <input type="text" name="address" required placeholder="সম্পূর্ণ ঠিকানা থানা এবং জেলার নাম সহ লিখুন" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1.5">ডেলিভারি এলাকা *</label>
            @include('landing.partials.district-options')
          </div>
          <div>
            <label class="flex flex-col border border-brand bg-brandLight/50 rounded-lg px-4 py-3 cursor-pointer">
              <span class="flex items-center gap-2 font-medium text-sm"><input type="radio" name="payment_method" value="cod" checked class="accent-brand"> Cash on delivery</span>
              <span class="text-xs text-gray-500 mt-1 ml-6">Pay with cash upon delivery.</span>
            </label>
          </div>
        </div>

      </div>

      <!-- Your order -->
      <div>
        @include('landing.partials.cart-offers-banner')
        <div class="border border-gray-200 rounded-lg overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-brandLight/70">
              <tr>
                <th class="text-left font-medium px-4 py-2.5">Product</th>
                <th class="text-right font-medium px-4 py-2.5">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-t border-gray-200">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <img src="{{ $mainImage }}" class="h-10 w-10 rounded-md object-cover" alt="{{ $product->name }}">
                    <div>
                      <span class="block">{{ $product->name }}</span>
                      <div class="flex items-center border border-gray-200 rounded-lg w-fit mt-1">
                        <button type="button" onclick="changeQty(-1)" class="px-2 py-0.5 text-gray-500 hover:text-brand text-xs">-</button>
                        <span id="qtyLabel" class="px-2 text-xs">1</span>
                        <button type="button" onclick="changeQty(1)" class="px-2 py-0.5 text-gray-500 hover:text-brand text-xs">+</button>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-right">৳<span id="rowSubtotal">{{ number_format($unitPrice, 2, '.', '') }}</span></td>
              </tr>
              @include('landing.partials.related-addon-order-rows')
              <tr class="border-t border-gray-200">
                <td class="px-4 py-2.5 text-gray-500">Subtotal</td>
                <td class="px-4 py-2.5 text-right">৳<span id="subtotal">{{ number_format($unitPrice, 2, '.', '') }}</span></td>
              </tr>
              <tr class="border-t border-gray-200">
                <td class="px-4 py-2.5 text-gray-500">ডেলিভারি চার্জ <span id="deliveryZone" class="text-xs text-gray-400">(ঢাকা)</span></td>
                <td class="px-4 py-2.5 text-right">
                  <span id="deliveryNormal">৳<span id="delivery">70.00</span></span>
                  <span id="deliveryFreeText" class="hidden text-emerald-600 font-semibold">ফ্রি <span class="text-gray-400 line-through text-xs font-normal">৳<span id="deliveryStruck">70.00</span></span></span>
                </td>
              </tr>
              <tr id="offerDiscountRow" class="hidden border-t border-gray-200 text-emerald-600">
                <td class="px-4 py-2.5">অফার ছাড়</td>
                <td class="px-4 py-2.5 text-right">-৳<span id="offerDiscountAmount">0.00</span></td>
              </tr>
              <tr class="border-t border-gray-200 font-semibold">
                <td class="px-4 py-3">Total</td>
                <td class="px-4 py-3 text-right text-brand">৳<span id="total">{{ number_format($unitPrice + 70, 2, '.', '') }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <p class="text-xs text-gray-400 mt-4">Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="#" class="underline">privacy policy</a>.</p>

        @include('landing.partials.phone-block-notice')

        <button type="submit" id="submitOrderBtn" class="w-full mt-5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold py-3.5 rounded-lg flex items-center justify-center gap-2 shadow-lg shadow-orange-500/20 transition-all hover:-translate-y-0.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
          <span id="placeOrderTotal">Place Order ৳{{ number_format($unitPrice + 70, 2, '.', '') }}</span>
        </button>
      </div>
    </form>
    @endif
  </div>
</section>

<!-- Footer -->
<footer class="bg-brandDark">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-5 text-center">
    <p class="text-xs text-white/60">&copy; {{ date('Y') }} {{ $siteSettings->site_name }}. All rights reserved.</p>
  </div>
</footer>

<!-- Sticky mobile order bar -->
<div id="stickyBar" class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 shadow-[0_-4px_12px_rgba(0,0,0,0.06)] px-4 py-3 flex items-center justify-between gap-4 translate-y-full transition-transform duration-300 md:hidden">
  <div>
    @if ($discount)
      <p class="text-[11px] text-gray-400 line-through">৳{{ number_format((float) $product->regular_price, 0) }}</p>
    @endif
    <p class="text-lg font-bold text-brand leading-none">৳{{ number_format($unitPrice, 0) }}</p>
  </div>
  <a href="#order" class="cta-btn flex-1 max-w-[220px] text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-full text-sm transition-all hover:scale-105">অর্ডার করতে চাই</a>
</div>

@include('landing.partials.scripts')

</body>
</html>
