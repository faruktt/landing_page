<!DOCTYPE html>
<html lang="bn" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  $siteSettings = \App\Models\Setting::current();
  $pageTitle = $siteSettings->site_name ?? 'Electora';
@endphp
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $siteSettings->meta_description }}">

@if (!empty($siteSettings->favicon))
  <link rel="icon" href="{{ $siteSettings->favicon_url ?: asset($siteSettings->favicon) }}">
@endif

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700;800;900&family=Hind+Siliguri:wght@400;500;600;700&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans: ['"Hind Siliguri"', '"Roboto"', 'sans-serif'],
          heading: ['"Anek Bangla"', '"Hind Siliguri"', 'sans-serif'],
        },
        colors: {
          electoraPink: '#ec2f6f',
          electoraMagenta: '#e61e6e',
          electoraCyan: '#23a6f0',
          electoraGreen: '#009e4f',
          electoraCallGreen: '#00c853',
          electoraBlueBtn: '#17a2b8',
        }
      },
    },
  }
</script>

<style>
  html { scroll-behavior: smooth; }
  body { font-family: 'Hind Siliguri', sans-serif; background-color: #ffffff; color: #111827; }
  h1, h2, h3, h4, .font-heading { font-family: 'Anek Bangla', sans-serif; }

  .electora-order-btn {
    background-color: #17a2b8;
    color: white;
    transition: all 0.2s ease;
  }
  .electora-order-btn:hover {
    background-color: #138496;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
  }

  .wcf-card-selected {
    border-color: #ec2f6f !important;
    background-color: #fff9fb !important;
  }
</style>

@include('partials.tracking-scripts')
</head>

<body class="antialiased selection:bg-electoraPink selection:text-white pb-24">
@include('partials.site-header', ['orderBtnTarget' => '#order-section', 'orderBtnLabel' => 'অর্ডার করুন'])
@php
  // Fetch values from settings or fallback to exact screenshot texts
  $txtTopBanner = $siteSettings->landing_announcement ?: '২৪ ঘন্টা গ্যাস থাকবে আপনার রান্নাঘরে গ্যারান্টি দিয়ে বলছি।';
  $phoneNum = $siteSettings->phone ?: '01712748655';
  $dhakaCharge = (float) ($siteSettings->dhaka_delivery_charge ?: 70);
  $outsideDhakaCharge = (float) ($siteSettings->default_delivery_charge ?: 130);

  $waNum = $siteSettings->whatsapp ?: $siteSettings->phone;
  $waDigits = preg_replace('/\D/', '', $waNum ?? '');
  if (strlen($waDigits) === 11 && str_starts_with($waDigits, '01')) {
      $waDigits = '88' . $waDigits;
  }
  $waBlockedMsg = rawurlencode('আসসালামু আলাইকুম, আমি অর্ডার করতে চাই।');
  $waBlockedUrl = $waDigits ? 'https://wa.me/' . $waDigits . '?text=' . $waBlockedMsg : '#';

  // Model 1 (TX-3000G++) Data
  $m1Title = $siteSettings->landing_p1_title ?: 'গ্যাস কম্প্রেসর Model: TX-3000G++';
  $m1Badge1 = $siteSettings->landing_p1_badge_1 ?: '2 YEAR SERVICE WARRANTY';
  $m1Badge2 = $siteSettings->landing_p1_badge_2 ?: '১ বছরের রিপ্লেসমেন্ট গ্যারান্টি';
  $m1Offer = $siteSettings->landing_p1_offer_text ?: 'আজকের জন্য ধামাকা অফার!!!';
  $m1RegPrice = $siteSettings->landing_p1_regular_price ?: '২৮০০';
  $m1SalePrice = $siteSettings->landing_p1_sale_price ?: '২২০০';
  $m1BulletsText = $siteSettings->landing_p1_bullets ?: "TX-3000G++ মডেলটি মূলত বাসা-বাড়ির দৈনন্দিন রান্নার কাজের জন্য উপযোগী।
TX-3000G++ মডেল যথেষ্ট কার্যকর এবং দৈনন্দিন ব্যবহারে পারফেক্ট।
গ্যাস লাইনে স্পার্ক করলে ও মটরটি নিরাপদ থাকবে। এ কারণে লাইনে গ্যাসে বা চুলায় আগুন ছড়িয়ে পড়ার কোনো সম্ভাবনা নেই।
লাইফ টাইম ১৪,০০০ঘন্টা
2300 RPM
7.5W, প্রতিদিন ৫ ঘন্টা ব্যবহারে ৩০ দিনে বিদ্যুৎ বিল আসবে ২৫-২৭ টাকা";
  $m1Bullets = array_filter(array_map('trim', explode("\n", $m1BulletsText)));

  // Model 2 (MAX) Data
  $m2Title = $siteSettings->landing_p2_title ?: 'গ্যাস কম্প্রেসর Model: MAX';
  $m2Badge1 = $siteSettings->landing_p2_badge_1 ?: '5 YEARS SERVICE WARRANTY';
  $m2Badge2 = $siteSettings->landing_p2_badge_2 ?: '৩০ দিনের রিপ্লেসমেন্ট গ্যারান্টি';
  $m2RegPrice = $siteSettings->landing_p2_regular_price ?: '৪৯০০';
  $m2SalePrice = $siteSettings->landing_p2_sale_price ?: '৪৩০০';
  $m2BulletsText = $siteSettings->landing_p2_bullets ?: "MAX মডেলটি বাসা-বাড়ি সহ কমার্শিয়াল ব্যবহারের জন্য রেস্টুরেন্ট, হোটেল, মেসের সাইজের জন্য তৈরি।
এই মডেলটি বিশেষ প্রযুক্তির মাধ্যমে তৈরি করা হয়েছে বিধায় এটি অনেক বেশি কার্যকর।
ওভারহিট প্রটেকশন মেকানিজম থাকায় অতিরিক্ত ব্যবহারে এ ডিভাইসটি নষ্ট হবে না। ফলে লাইফটাইম পাবেন দীর্ঘস্থায়ী সার্ভিস।
লাইফ টাইম ৪০,০০০ঘন্টা
3000 RPM
12W, প্রতিদিন ৫ ঘন্টা ব্যবহারে ৩০ দিনে বিদ্যুৎ বিল আসবে ৫০-৬০ টাকা";
  $m2Bullets = array_filter(array_map('trim', explode("\n", $m2BulletsText)));

  // Benefits
  $benefitsTitle = $siteSettings->landing_benefits_title ?: 'গ্যাস কম্প্রেসর এর উপকারিতাঃ';
  $benefitsText = $siteSettings->landing_benefits_list ?: "এই স্মার্ট ডিভাইটি সম্পূর্ন ঝুঁকিমুক্ত ও নিরাপদ মেসিন লাইনে স্পার্ক করলেও মেসিন বা গ্যাস লাইনের কোনো ক্ষতি হবে না। যার ফলে গ্যাস লাইনে কোনো প্রকার দুর্ঘটনা ঘটার সুযোগ নেই। তাই ব্যবহার করতে পারেন সম্পূর্ণ ঝুঁকিমুক্তভাবে।
অটোমেটিক ও শক্তিশালী কপার কয়েল, বিধায় দীর্ঘক্ষণ ব্যবহারে ও মেসিন অতিরিক্ত গরম হয় না। ফলে ডিভাইসটি বছরের পর বছর কোনো প্রকার সার্ভিসিং ছাড়া ব্যবহার করা যাবে অত্যন্ত সহজে।
দীর্ঘস্থায়ী ডিসি মোটর ও উন্নত সেফটি গ্যারান্টি।
গ্যাস কমে গেলে স্বয়ংক্রিয় প্রেশার বাড়িয়ে পর্যাপ্ত গ্যাস সরবরাহ নিশ্চিত করে।
একাধিক চুলায় ব্যবহার সুবিধা।
নিরাপদ কপার ও ভারী তার।
লাইনে গ্যাস থাকলে, মেসিন চালু না করলেও চুলায় স্বাভাবিক গ্যাস আসবে।";
  $benefitsList = array_filter(array_map('trim', explode("\n", $benefitsText)));

  // Buttons
  $btnOutlookText = $siteSettings->landing_outlook_btn_text ?: 'প্রোডাক্টের আউটলুক';
  $btnReviewText = $siteSettings->landing_review_btn_text ?: 'Review';
  $orderSectionTitle = $siteSettings->landing_order_title ?: 'অর্ডার করতে নিচের ফর্ম টি পূরণ করুন 👇';
  $orderConfirmBtnText = $siteSettings->landing_order_btn_text ?: 'আপনার অর্ডারটি কনফার্ম করুন';

  // Products from DB
  $product1 = $products->get(0);
  $product2 = $products->get(1);

  // Priority: 1. Admin uploaded landing image, 2. Product uploaded image, 3. Electora high-res photo
  $p1ImgA = !empty($siteSettings->landing_p1_image_1)
    ? asset('storage/'.$siteSettings->landing_p1_image_1)
    : ($product1 && ($product1->image_url ?: $product1->image) ? ($product1->image_url ?: asset($product1->image)) : 'https://electora.top/wp-content/uploads/2025/09/electora-1-1-300x300.png');

  $p1ImgB = !empty($siteSettings->landing_p1_image_2)
    ? asset('storage/'.$siteSettings->landing_p1_image_2)
    : ($product1 && $product1->images->count() ? ($product1->images->first()->image_url ?: asset($product1->images->first()->path)) : 'https://electora.top/wp-content/uploads/2025/09/electora-2-300x300.png');
  
  $p2Img = !empty($siteSettings->landing_p2_image)
    ? asset('storage/'.$siteSettings->landing_p2_image)
    : ($product2 && ($product2->image_url ?: $product2->image) ? ($product2->image_url ?: asset($product2->image)) : 'https://electora.top/wp-content/uploads/2025/11/Generated-Image-November-04-2025-3_54AM-1-min-e1762237717188-300x300.png');
@endphp

<!-- Container matching electora.top centered column (max-w-[720px] on desktop) -->
<div class="max-w-[900px] mx-auto px-3 sm:px-4 py-4 sm:py-6 space-y-6">

  <!-- 1. TOP ANNOUNCEMENT BAR (Matching screenshot exactly) -->
  <div class="text-center py-2 px-1">
    <h2 class="font-heading font-extrabold text-xl sm:text-2xl text-gray-900 tracking-tight leading-snug">
      {!! str_replace(
        ['২৪ ঘন্টা গ্যাস', 'গ্যারান্টি দিয়ে বলছি।'],
        ['<span class="text-[#ec2f6f]">২৪ ঘন্টা গ্যাস</span>', '<span class="text-[#ec2f6f]">গ্যারান্টি দিয়ে বলছি।</span>'],
        e($txtTopBanner)
      ) !!}
    </h2>
  </div>
  
<div class="bg-white border-2 border-[#ec2f6f] rounded-2xl p-5 sm:p-6 shadow-sm">

    @if (!empty($siteSettings->short_text))
        <p class="text-base sm:text-lg text-gray-800 leading-relaxed font-medium mb-5">
            {{ $siteSettings->short_text }}
        </p>
    @endif

    <div class="text-center mt-5">
        <style>
            @keyframes orderPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.12); }
            }
        </style>

        <a href="#order-section"
           onclick="selectProductInForm({{ $product1->id ?? 0 }})"
           style="animation: orderPulse 1.2s ease-in-out infinite; transform-origin: center;"
           class="electora-order-btn inline-flex items-center gap-2 font-heading font-bold text-sm sm:text-base px-8 py-2 rounded-full shadow-md">
            <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
            <span>অর্ডার করুন</span>
        </a>
    </div>
</div>
  <!-- 2. PRODUCT 1 BOX (Magenta / Pink Box - Exact Match to Screenshot) -->
  <div class="bg-[#e61e6e] rounded-2xl p-5 sm:p-6 text-white text-center shadow-lg relative">
    
    <!-- Title -->
    <h2 class="font-heading font-black text-2xl sm:text-3xl tracking-wide mb-3">
      {{ $m1Title }}
    </h2>

    <!-- Blue Warranty Badge -->
    @if ($m1Badge1)
      <div class="inline-block bg-[#23a6f0] text-white font-black text-sm sm:text-base px-6 py-1.5 rounded-md shadow-xs mb-2 uppercase tracking-wide">
        {{ $m1Badge1 }}
      </div>
    @endif

    <!-- Green Replacement Badge -->
    @if ($m1Badge2)
      <div class="bg-[#009e4f] text-[#ffeb3b] font-heading font-black text-base sm:text-lg py-1.5 px-4 rounded-md shadow-xs mb-5">
        {{ $m1Badge2 }}
      </div>
    @endif
<div class="text-center mt-5">
        <style>
            @keyframes orderPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.12); }
            }
        </style>
    
        <a href="#order-section"
           onclick="selectProductInForm({{ $product1->id ?? 0 }})"
           style="animation: orderPulse 1.2s ease-in-out infinite; transform-origin: center;"
           class="electora-order-btn inline-flex items-center gap-2 font-heading font-bold text-sm sm:text-base px-8 py-2 rounded-full shadow-md">
            <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
            <span>অর্ডার করুন</span>
        </a>
    </div>
    <br>
    <!-- Two Product Photos Side-by-Side in white boxes -->
    <div class="grid grid-cols-2 gap-4 sm:gap-6 mb-6 max-w-2xl mx-auto">
      <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-lg border border-white/30 aspect-square flex items-center justify-center overflow-hidden group">
        <img src="{{ $p1ImgA }}" alt="TX-3000G++ Angle 1" class="w-full h-full object-contain group-hover:scale-105 transition duration-300">
      </div>
      <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-lg border border-white/30 aspect-square flex items-center justify-center overflow-hidden group">
        <img src="{{ $p1ImgB }}" alt="TX-3000G++ Angle 2" class="w-full h-full object-contain group-hover:scale-105 transition duration-300">
      </div>
    </div>

    <!-- White Pill Offer Banner -->
    @if ($m1Offer)
      <div class="bg-white text-gray-900 font-heading font-black text-lg sm:text-xl py-2 px-6 rounded-full shadow-md max-w-sm mx-auto mb-4">
        {{ $m1Offer }}
      </div>
    @endif

    <!-- Pricing -->
    <div class="space-y-1 mb-4">
      <p class="font-heading text-base sm:text-lg font-bold text-white/90">
        রেগুলার প্রাইস <span class="line-through decoration-white/80 decoration-2">{{ $m1RegPrice }} টাকা</span>
      </p>
      <p class="font-heading font-black text-2xl sm:text-3xl text-white">
        আজকের অফার প্রাইস {{ $m1SalePrice }} টাকা
      </p>
    </div>

    <!-- Green Phone Call Button -->
    <div class="mb-4">
      <a href="tel:{{ $phoneNum }}" class="inline-flex items-center gap-2 bg-[#00c853] hover:bg-[#00b248] text-white font-heading font-bold text-sm sm:text-base px-6 py-2.5 rounded-full shadow-md transition">
        <i class="fa-brands fa-whatsapp text-lg"></i>
        <span>যেকোনো প্রয়োজনে কল করুন {{ $phoneNum }}</span>
      </a>
    </div>

    <!-- Cyan Order Button (Floating / Below) -->
    <div>
        <style>
            @keyframes orderPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.12); }
            }
        </style>
    
        <a href="#order-section"
           onclick="selectProductInForm({{ $product1->id ?? 0 }})"
           style="animation: orderPulse 1.2s ease-in-out infinite; transform-origin: center;"
           class="electora-order-btn inline-flex items-center gap-2 font-heading font-bold text-sm sm:text-base px-8 py-2 rounded-full shadow-md">
            <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
            <span>অর্ডার করুন</span>
        </a>
    </div>

  </div>

  <!-- 3. PRODUCT 1 BULLETS BOX (White with Pink Border) -->
  <div class="bg-white border-2 border-[#ec2f6f] rounded-2xl p-5 sm:p-6 shadow-sm">
     <div class="py-3 px-4 text-center border-b border-rose-100 bg-white">
      <h2 class="font-heading font-black text-xl sm:text-2xl text-gray-900">
        প্রধান বৈশিষ্ট্যসমূহ:
      </h2>
    </div>

    <ul class="space-y-2.5 p-2 text-base sm:text-lg text-gray-800 font-medium">
      @foreach ($m1Bullets as $bullet)
        <li class="flex items-start gap-2.5">
          <span class="text-[#ec2f6f] text-base sm:text-lg shrink-0 mt-0.5"><i class="fa-solid fa-circle-check"></i></span>
          <span class="leading-relaxed">{{ $bullet }}</span>
        </li>
      @endforeach
    </ul>

    <!-- Cyan Order Button at Bottom of Bullets -->
   <div class="text-center mt-5">
    <style>
        @keyframes orderPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.12); }
        }
    </style>

    <a href="#order-section"
       onclick="selectProductInForm({{ $product1->id ?? 0 }})"
       style="animation: orderPulse 1.2s ease-in-out infinite; transform-origin: center;"
       class="electora-order-btn inline-flex items-center gap-2 font-heading font-bold text-sm sm:text-base px-8 py-2 rounded-full shadow-md">
        <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
        <span>অর্ডার করুন</span>
    </a>
</div>
  </div>

  <!-- 4. PRODUCT 2 BOX (Model: MAX - White with Pink Border) -->
  <div class="bg-white border-2 border-[#ec2f6f] rounded-2xl p-5 sm:p-6 text-center shadow-sm">
    
    <!-- Title -->
    <h2 class="font-heading font-black text-2xl sm:text-3xl text-gray-900 mb-3">
      {{ $m2Title }}
    </h2>

    <!-- Blue Warranty Badge -->
    @if ($m2Badge1)
      <div class="inline-block bg-[#23a6f0] text-white font-black text-sm sm:text-base px-6 py-1.5 rounded-md shadow-xs mb-2 uppercase tracking-wide">
        {{ $m2Badge1 }}
      </div>
    @endif

    <!-- Pink Replacement Badge -->
    @if ($m2Badge2)
      <div class="bg-[#ec2f6f] text-white font-heading font-black text-base sm:text-lg py-1.5 px-4 rounded-md shadow-xs mb-5">
        {{ $m2Badge2 }}
      </div>
    @endif

    <!-- Product 2 Image Showcase -->
    <div class="relative max-w-xl mx-auto mb-5 bg-white p-3 sm:p-4 rounded-2xl flex items-center justify-center shadow-sm border border-gray-100">
      <img src="{{ $p2Img }}" alt="MAX Compressor" class="w-full h-auto max-h-80 sm:max-h-96 object-contain hover:scale-105 transition duration-300">
    </div>

    <!-- Pricing -->
    <div class="space-y-1 mb-5">
      <p class="font-heading text-base sm:text-lg font-bold text-gray-800">
        রেগুলার প্রাইস <span class="line-through decoration-red-500 decoration-2 text-gray-600">{{ $m2RegPrice }} টাকা</span>
      </p>
      <p class="font-heading font-black text-2xl sm:text-3xl text-gray-900">
        আজকের অফার প্রাইস <span class="relative inline-block border-2 border-red-500 rounded-[50%] px-4 py-0.5 text-red-600 font-extrabold -rotate-2">{{ $m2SalePrice }}</span> টাকা
      </p>
    </div>

    <!-- Cyan Order Button -->
   <div>
        <style>
            @keyframes orderPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.12); }
            }
        </style>
    
        <a href="#order-section"
           onclick="selectProductInForm({{ $product2->id ?? ($product1->id ?? 0) }})"
           style="animation: orderPulse 1.2s ease-in-out infinite; transform-origin: center;"
           class="electora-order-btn inline-flex items-center gap-2 font-heading font-bold text-sm sm:text-base px-8 py-2 rounded-full shadow-md">
            <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
            <span>অর্ডার করুন</span>
        </a>
    </div>

  </div>

  <!-- 5. PRODUCT 2 BULLETS BOX (White with Pink Border) -->
  <div class="bg-white border-2 border-[#ec2f6f] rounded-2xl p-5 sm:p-6 shadow-sm">
    <ul class="space-y-2.5 text-base sm:text-lg text-gray-800 font-medium">
      @foreach ($m2Bullets as $bullet)
        <li class="flex items-start gap-2.5">
          <span class="text-[#ec2f6f] text-base sm:text-lg shrink-0 mt-0.5"><i class="fa-solid fa-circle-check"></i></span>
          <span class="leading-relaxed">{{ $bullet }}</span>
        </li>
      @endforeach
    </ul>

    <!-- Cyan Order Button at Bottom of Bullets -->
    <div class="text-center mt-5">
        <style>
            @keyframes orderPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.12); }
            }
        </style>
    
        <a href="#order-section"
           onclick="selectProductInForm({{ $product2->id ?? ($product1->id ?? 0) }})"
           style="animation: orderPulse 1.2s ease-in-out infinite; transform-origin: center;"
           class="electora-order-btn inline-flex items-center gap-2 font-heading font-bold text-sm sm:text-base px-8 py-2 rounded-full shadow-md">
            <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
            <span>অর্ডার করুন</span>
        </a>
    </div>
  </div>

  <!-- 6. BENEFITS SECTION (Pink Background Container - Exact Match to Screenshot) -->
  <div class="bg-white border-2 border-[#ec2f6f] rounded-2xl overflow-hidden shadow-sm">
    <!-- Header title -->
    <div class="py-3 px-4 text-center border-b border-rose-100 bg-white">
      <h2 class="font-heading font-black text-xl sm:text-2xl text-gray-900">
        {{ $benefitsTitle }}
      </h2>
    </div>

    <!-- Magenta/Pink body with bullets -->
    <div class="bg-[#e61e6e] text-white p-5 sm:p-6">
      <ul class="space-y-3 text-base sm:text-lg leading-relaxed font-normal">
        @foreach ($benefitsList as $bItem)
          <li class="flex items-start gap-2.5 border-b border-white/15 pb-2.5 last:border-0 last:pb-0">
            <span class="text-white text-base sm:text-lg shrink-0 mt-0.5"><i class="fa-solid fa-circle-check"></i></span>
            <span>{{ $bItem }}</span>
          </li>
        @endforeach
      </ul>
    </div>
  </div>

  <!-- 7. TWO RECTANGULAR PINK BUTTONS (প্রোডাক্টের আউটলুক / Review) -->
  <div class="space-y-3">
    <button type="button" onclick="document.getElementById('order-section').scrollIntoView({behavior: 'smooth'})" class="w-full bg-[#e61e6e] hover:bg-[#d81563] text-white font-heading font-extrabold text-base sm:text-lg py-3 rounded-xl shadow-sm transition">
      {{ $btnOutlookText }}
    </button>
    <button type="button" onclick="document.getElementById('order-section').scrollIntoView({behavior: 'smooth'})" class="w-full bg-[#e61e6e] hover:bg-[#d81563] text-white font-heading font-extrabold text-base sm:text-lg py-3 rounded-xl shadow-sm transition">
      {{ $btnReviewText }}
    </button>
  </div>

  <!-- 8. GREEN CALL BUTTON -->
  <div class="text-center pt-2">
    <a href="tel:{{ $phoneNum }}" class="inline-flex items-center gap-2 bg-[#00c853] hover:bg-[#00b248] text-white font-heading font-bold text-sm sm:text-base px-8 py-3 rounded-full shadow-md transition">
      <i class="fa-brands fa-whatsapp text-lg"></i>
      <span>যেকোনো প্রয়োজনে কল করুন {{ $phoneNum }}</span>
    </a>
  </div>

  <!-- 9. ORDER FORM TITLE (With Downward Hand Emoji 👇) -->
  <div class="text-center pt-4 pb-2" id="order-section">
    <h2 class="font-heading font-black text-2xl sm:text-3xl text-gray-900">
      {{ $orderSectionTitle }}
    </h2>
  </div>

  <!-- 10. THE ORDER FORM (Electora / CartFlows 2-Column Exact Layout) -->
  @if ($orderBlocked)
    <div class="bg-white rounded-2xl p-6 border border-gray-200 text-center shadow-md">
      @include('landing.partials.order-blocked', ['product' => $product1])
    </div>
  @else
    <form id="checkoutForm" method="POST" action="{{ route('orders.store') }}" class="space-y-6">
      @csrf

      <!-- Part A: Your Products (Two Cards Side-by-Side matching Screenshot) -->
      <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200 shadow-xs">
        <h3 class="font-heading font-bold text-base sm:text-lg text-gray-900 mb-3 pb-2 border-b border-gray-100 flex items-center justify-between">
          <span>Your Products</span>
          <span class="text-sm sm:text-base text-gray-600 font-semibold">যেকোনো একটি প্রোডাক্ট সিলেক্ট করুন</span>
        </h3>

        <!-- Product Cards Grid: 2 columns matching screenshot -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="productsOptionsGrid">
          @forelse ($products as $k => $item)
            @php
              $itemPrice = (float) ($item->sale_price ?? $item->regular_price);
              $itemImg = ($item->image_url ?: ($item->image ? asset($item->image) : null)) ?: ($k === 0 ? $p1ImgA : $p2Img);
              $isFirstChecked = ($k === 0);
            @endphp

            <div id="card-row-{{ $item->id }}"
                 class="wcf-product-card border-2 {{ $isFirstChecked ? 'wcf-card-selected' : 'border-gray-200 bg-white' }} rounded-xl p-3 flex items-center justify-between gap-2.5 cursor-pointer hover:border-electoraPink transition"
                 onclick="selectProduct({{ $item->id }})">
              
              <!-- Left: Radio + Thumbnail + Title -->
              <div class="flex items-center gap-2.5 min-w-0 flex-1">
                <!-- Native Radio for Single Product Selection -->
                <input type="radio"
                       name="selected_products[]"
                       value="{{ $item->id }}"
                       id="check_item_{{ $item->id }}"
                       {{ $isFirstChecked ? 'checked' : '' }}
                       data-price="{{ $itemPrice }}"
                       data-name="{{ $item->name }}"
                       data-img="{{ $itemImg }}"
                       class="h-4 w-4 text-electoraPink focus:ring-electoraPink accent-electoraPink cursor-pointer shrink-0"
                       onchange="selectProduct({{ $item->id }})">

                <!-- Thumbnail (electora.top 300x300 thumbnail style) -->
                <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                  <img src="{{ $itemImg }}" alt="{{ $item->name }}" class="w-full h-full object-contain">
                </div>

                <!-- Title & Price -->
                <div class="min-w-0 flex-1">
                  <h4 class="font-heading font-bold text-sm sm:text-base text-gray-900 truncate">
                    {{ $item->name }}
                  </h4>
                  <div class="font-numeric font-black text-sm sm:text-base text-gray-900 mt-0.5">
                    {{ number_format($itemPrice, 2) }}&#2547;
                  </div>
                </div>
              </div>

              <!-- Right: Quantity Selector [-] 1 [+] -->
              <div class="shrink-0 flex items-center border border-gray-200 rounded-lg bg-white overflow-hidden text-sm" onclick="event.stopPropagation()">
                <button type="button" onclick="stepQty({{ $item->id }}, -1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-rose-50 hover:text-electoraPink font-bold">
                  &minus;
                </button>
                <input type="number"
                       name="quantities[{{ $item->id }}]"
                       id="qty_field_{{ $item->id }}"
                       value="1"
                       min="1"
                       readonly
                       class="w-7 text-center font-numeric font-bold border-0 bg-transparent text-gray-900 p-0 focus:outline-none">
                <button type="button" onclick="stepQty({{ $item->id }}, 1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-rose-50 hover:text-electoraPink font-bold">
                  &plus;
                </button>
              </div>

            </div>
          @empty
            <p class="text-gray-500 text-sm col-span-full text-center py-3">কোনো প্রোডাক্ট পাওয়া যায়নি।</p>
          @endforelse
        </div>
      </div>

      <!-- Part B: 2 Columns below Your Products (Billing Details on Left, Your Order on Right) -->
      <div class="grid md:grid-cols-2 gap-5 items-start">
        
        <!-- Left Sub-column: Billing Details & Shipping -->
        <div class="space-y-4">
          <!-- Billing details -->
          <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200 shadow-xs space-y-3.5">
            <h3 class="font-heading font-bold text-base sm:text-lg text-gray-900 pb-2 border-b border-gray-100">
              Billing details
            </h3>

            <div>
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">আপনার নাম লিখুন <span class="text-red-500">*</span></label>
              <input type="text" name="customer_name" required placeholder="আপনার নাম"
                     class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm sm:text-base focus:outline-none focus:ring-1 focus:ring-electoraPink focus:border-electoraPink">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">আপনার মোবাইল নম্বর লিখুন <span class="text-red-500">*</span></label>
              <input type="tel" name="phone" id="custPhone" required maxlength="11" inputmode="numeric" placeholder="01XXXXXXXXX"
                     class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm sm:text-base font-numeric focus:outline-none focus:ring-1 focus:ring-electoraPink focus:border-electoraPink">
              <p id="phoneErrLabel" class="hidden text-sm sm:text-base text-red-600 mt-1 font-medium">সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (০১৩-০১৯)।</p>

              <!-- Real-time Admin Blocked Phone Notice -->
              <div id="phoneBlockedAlert" class="hidden mt-2 p-3 bg-rose-50 border border-rose-200 rounded-xl space-y-2 text-left">
                <div class="flex items-start gap-2 text-rose-700">
                  <i class="fa-solid fa-ban text-base shrink-0 mt-0.5 text-rose-600"></i>
                  <div>
                    <p class="font-bold text-sm sm:text-base text-rose-800">এই নম্বরটি অ্যাডমিন কর্তৃক ব্লক করা হয়েছে</p>
                    <p class="text-xs sm:text-sm text-rose-700 mt-0.5 font-medium">অর্ডারের জন্য অনুগ্রহ করে সরাসরি আমাদের হোয়াটসঅ্যাপে কথা বলুন।</p>
                  </div>
                </div>
                <a href="{{ $waBlockedUrl }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-3 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold text-sm sm:text-base rounded-lg shadow-xs transition active:scale-95">
                  <i class="fa-brands fa-whatsapp text-base sm:text-lg"></i>
                  <span>হোয়াটসঅ্যাপে কথা বলুন</span>
                </a>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-800 mb-1.5">আপনার পূর্ণ ঠিকানা লিখুন <span class="text-red-500">*</span></label>
              <input type="text" name="address" required placeholder="বাসা নং, রোড নং, থানা, জেলা"
                     class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm sm:text-base focus:outline-none focus:ring-1 focus:ring-electoraPink focus:border-electoraPink">
            </div>
          </div>

          <!-- Shipping (Electora Style) -->
          <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200 shadow-xs space-y-2.5">
            <h3 class="font-heading font-bold text-base sm:text-lg text-gray-900 pb-2 border-b border-gray-100">
              Shipping
            </h3>

            <div class="grid grid-cols-2 gap-2 text-sm sm:text-base">
              <label class="flex items-center justify-between p-2.5 sm:p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-electoraPink transition has-[:checked]:border-electoraPink has-[:checked]:bg-rose-50/30 whitespace-nowrap">
                <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                  <input type="radio" name="district" value="ঢাকা" checked onchange="calculateOrderTotals()" class="accent-electoraPink h-4 w-4 shrink-0">
                  <span class="font-medium truncate text-sm sm:text-base">Inside Dhaka</span>
                </div>
                <span class="font-numeric font-bold text-sm sm:text-base ml-1 shrink-0">{{ number_format($dhakaCharge, 0) }}&#2547;</span>
              </label>

              <label class="flex items-center justify-between p-2.5 sm:p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-electoraPink transition has-[:checked]:border-electoraPink has-[:checked]:bg-rose-50/30 whitespace-nowrap">
                <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                  <input type="radio" name="district" value="ঢাকার বাইরে" onchange="calculateOrderTotals()" class="accent-electoraPink h-4 w-4 shrink-0">
                  <span class="font-medium truncate text-sm sm:text-base">Outside Dhaka</span>
                </div>
                <span class="font-numeric font-bold text-sm sm:text-base ml-1 shrink-0">{{ number_format($outsideDhakaCharge, 0) }}&#2547;</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Right Sub-column: Your Order Breakdown & Confirmation Button -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200 shadow-xs space-y-4">
          <h3 class="font-heading font-bold text-base sm:text-lg text-gray-900 pb-2 border-b border-gray-100">
            Your order
          </h3>

          <!-- Table Header -->
          <div class="flex justify-between text-sm sm:text-base font-semibold text-gray-700 pb-1.5 border-b border-gray-100">
            <span>Product</span>
            <span>Subtotal</span>
          </div>

          <!-- Items Rows (Populated live via JS) -->
          <div id="orderItemsSummaryTable" class="space-y-2 text-sm sm:text-base">
            <!-- Populated via JS -->
          </div>

          <!-- Subtotal & Shipping Rows -->
          <div class="pt-2 border-t border-gray-100 space-y-2 text-sm sm:text-base">
            <div class="flex justify-between text-gray-700">
              <span>Subtotal</span>
              <span id="txtSubtotal" class="font-numeric font-bold text-gray-900">0.00&#2547;</span>
            </div>
            <div class="flex justify-between text-gray-700">
              <span>Shipping</span>
              <span id="txtShipping" class="font-numeric font-bold text-gray-900">{{ number_format($dhakaCharge, 2) }}&#2547;</span>
            </div>
            <div class="flex justify-between items-baseline pt-2 border-t border-gray-200 font-bold text-base sm:text-lg text-gray-900">
              <span>Total</span>
              <span id="txtGrandTotal" class="font-numeric font-black text-xl sm:text-2xl text-gray-900">0.00&#2547;</span>
            </div>
          </div>

          <!-- Cash on delivery hidden field (default COD) -->
          <input type="hidden" name="payment_method" value="cod">

          <!-- Privacy Notice matching electora.top -->
          <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
            Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our privacy policy.
          </p>

          <!-- Place Order Button (Matching electora.top Black Button Style) -->
          <button type="submit" id="btnConfirmOrder" class="w-full bg-black hover:bg-gray-900 text-white font-heading font-black text-sm sm:text-base py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-lock text-xs sm:text-sm"></i>
            <span id="btnConfirmLabel">{{ $orderConfirmBtnText }}</span>
          </button>
        </div>

      </div>

    </form>
  @endif

</div>

<!-- Black Minimal Footer matching screenshot -->
<footer class="bg-black text-gray-400 py-5 text-center text-sm sm:text-base border-t border-gray-900 mt-10">
  <p>&copy; {{ date('Y') }} {{ $siteSettings->site_name ?? 'Electora' }}. All rights reserved.</p>
</footer>

<!-- Single Product Select & Live Pricing Script -->
<script>
  const shipDhaka = {{ $dhakaCharge }};
  const shipOutside = {{ $outsideDhakaCharge }};

  // Select a single product (radio selection)
  function selectProduct(productId) {
    const id = parseInt(productId);
    document.querySelectorAll('input[name="selected_products[]"]').forEach(radio => {
      const pid = parseInt(radio.value);
      const isSelected = (pid === id);
      radio.checked = isSelected;

      const card = document.getElementById('card-row-' + pid);
      if (card) {
        if (isSelected) {
          card.classList.add('wcf-card-selected');
          card.classList.remove('border-gray-200', 'bg-white');
        } else {
          card.classList.remove('wcf-card-selected');
          card.classList.add('border-gray-200', 'bg-white');
        }
      }
    });

    calculateOrderTotals();
    saveProgressDebounced();
  }

  // Pre-select product when clicking "অর্ডার করুন" buttons
  function selectProductInForm(productId) {
    if (productId > 0) {
      selectProduct(productId);
    }
    const target = document.getElementById('order-section');
    if (target) target.scrollIntoView({ behavior: 'smooth' });
  }

  // Quantity step adjustment
  function stepQty(productId, step) {
    const input = document.getElementById('qty_field_' + productId);
    if (!input) return;

    let val = parseInt(input.value) || 1;
    val = Math.max(1, val + step);
    input.value = val;

    // Automatically select this product when changing quantity
    selectProduct(productId);
  }

  // Live order calculations
  function calculateOrderTotals() {
    const container = document.getElementById('orderItemsSummaryTable');
    if (!container) return;

    container.innerHTML = '';
    let subtotal = 0;

    const checkedInputs = document.querySelectorAll('input[name="selected_products[]"]:checked');

    checkedInputs.forEach(cb => {
      const id = cb.value;
      const price = parseFloat(cb.dataset.price) || 0;
      const name = cb.dataset.name || 'Product';
      const img = cb.dataset.img || '';
      const qtyInput = document.getElementById('qty_field_' + id);
      const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
      const lineTotal = price * qty;
      subtotal += lineTotal;

      const row = document.createElement('div');
      row.className = 'flex items-center justify-between gap-2 text-gray-800';
      row.innerHTML = `
        <div class="flex items-center gap-2 min-w-0 flex-1">
          <img src="${img}" class="w-8 h-8 rounded object-contain border border-gray-100 shrink-0" alt="">
          <span class="truncate text-sm sm:text-base">${name} &times; ${qty}</span>
        </div>
        <span class="font-numeric font-bold text-sm sm:text-base shrink-0">${lineTotal.toFixed(2)}&#2547;</span>
      `;
      container.appendChild(row);
    });

    const isDhaka = document.querySelector('input[name="district"]:checked')?.value === 'ঢাকা';
    const shipping = isDhaka ? shipDhaka : shipOutside;
    const grandTotal = subtotal + shipping;

    document.getElementById('txtSubtotal').innerHTML = subtotal.toFixed(2) + '&#2547;';
    document.getElementById('txtShipping').innerHTML = shipping.toFixed(2) + '&#2547;';
    document.getElementById('txtGrandTotal').innerHTML = grandTotal.toFixed(2) + '&#2547;';

    const baseText = "{{ $orderConfirmBtnText }}";
    document.getElementById('btnConfirmLabel').innerHTML = baseText + '&nbsp;&nbsp;' + grandTotal.toFixed(2) + '&#2547;';

    if (typeof updateFloatingButtonState === 'function') {
      updateFloatingButtonState();
    }
  }

  // Debounced save progress for abandoned cart recovery
  let debounceTimer = null;
  function saveProgressDebounced() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      const phoneInput = document.getElementById('custPhone');
      if (!phoneInput) return;
      const phone = phoneInput.value.trim();
      if (!/^01[3-9]\d{8}$/.test(phone)) return;

      const form = document.getElementById('checkoutForm');
      if (!form) return;

      const formData = new FormData(form);
      fetch("{{ route('orders.save-progress') }}", {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
          'Accept': 'application/json'
        },
        body: formData
      }).catch(() => {});
    }, 600);
  }

  // Real-time phone block tracking
  let isCurrentPhoneBlocked = false;
  let phoneCheckTimer = null;

  function checkPhoneBlockStatus(phone) {
    clearTimeout(phoneCheckTimer);
    phoneCheckTimer = setTimeout(() => {
      fetch("{{ route('check-phone') }}?phone=" + encodeURIComponent(phone))
        .then(res => res.json())
        .then(data => {
          const blockAlert = document.getElementById('phoneBlockedAlert');
          if (data && data.blocked) {
            isCurrentPhoneBlocked = true;
            if (blockAlert) {
              blockAlert.classList.remove('hidden');
              blockAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          } else {
            isCurrentPhoneBlocked = false;
            if (blockAlert) blockAlert.classList.add('hidden');
          }
          updateFloatingButtonState();
        })
        .catch(() => {});
    }, 250);
  }

  // Handle click on floating order button in footer
  function handleFloatingOrderClick(e) {
    if (e) e.preventDefault();
    const form = document.getElementById('checkoutForm');
    if (!form) {
      document.getElementById('order-section')?.scrollIntoView({ behavior: 'smooth' });
      return;
    }

    const nameInput = form.querySelector('input[name="customer_name"]');
    const phoneInput = document.getElementById('custPhone');
    const addressInput = form.querySelector('input[name="address"]');

    const name = nameInput ? nameInput.value.trim() : '';
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const address = addressInput ? addressInput.value.trim() : '';

    // If completely empty, scroll down to order section and focus name
    if (!name && !phone && !address) {
      document.getElementById('order-section')?.scrollIntoView({ behavior: 'smooth' });
      setTimeout(() => nameInput?.focus(), 400);
      return;
    }

    // If blocked by admin, show alert and open WhatsApp
    if (isCurrentPhoneBlocked) {
      const blockAlert = document.getElementById('phoneBlockedAlert');
      if (blockAlert) {
        blockAlert.classList.remove('hidden');
        blockAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      alert('এই নম্বরটি অ্যাডমিন কর্তৃক ব্লক করা হয়েছে। অর্ডারের জন্য অনুগ্রহ করে হোয়াটসঅ্যাপে কথা বলুন।');
      return;
    }

    // If any required field is missing, scroll to that specific input and focus
    if (!name) {
      nameInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      nameInput?.focus();
      return;
    }

    if (!phone) {
      phoneInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      phoneInput?.focus();
      return;
    }

    const phoneErr = document.getElementById('phoneErrLabel');
    if (!/^01[3-9]\d{8}$/.test(phone)) {
      if (phoneErr) phoneErr.classList.remove('hidden');
      phoneInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      phoneInput?.focus();
      return;
    }

    if (!address) {
      addressInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      addressInput?.focus();
      return;
    }

    // Product selection check
    const checked = document.querySelectorAll('input[name="selected_products[]"]:checked');
    if (checked.length === 0) {
      alert('অনুগ্রহ করে একটি প্রোডাক্ট নির্বাচন করুন!');
      document.getElementById('order-section')?.scrollIntoView({ behavior: 'smooth' });
      return;
    }

    // All fields are filled and valid! Submit the order form
    const submitBtn = document.getElementById('btnConfirmOrder');
    if (submitBtn) {
      submitBtn.click();
    } else {
      form.requestSubmit();
    }
  }

  // Dynamically update floating button appearance when form is filled
  function updateFloatingButtonState() {
    const form = document.getElementById('checkoutForm');
    const btn = document.getElementById('floatingOrderBtn');
    const btnLabel = document.getElementById('floatingOrderBtnText');
    const btnIcon = document.getElementById('floatingOrderBtnIcon');
    if (!form || !btn || !btnLabel) return;

    if (isCurrentPhoneBlocked) {
      btnLabel.innerHTML = 'অ্যাডমিন কর্তৃক ব্লক করা হয়েছে';
      if (btnIcon) {
        btnIcon.className = 'fa-solid fa-ban text-base sm:text-lg';
      }
      btn.classList.remove('ring-4', 'ring-pink-300/60');
      return;
    }

    const name = form.querySelector('input[name="customer_name"]')?.value.trim();
    const phone = document.getElementById('custPhone')?.value.trim();
    const address = form.querySelector('input[name="address"]')?.value.trim();

    const isFilled = Boolean(name && phone && address);

    if (isFilled) {
      const grandTotal = document.getElementById('txtGrandTotal')?.innerText || '';
      btnLabel.innerHTML = 'অর্ডার কনফার্ম করুন' + (grandTotal ? ' &bull; ' + grandTotal : '');
      if (btnIcon) {
        btnIcon.className = 'fa-solid fa-circle-check text-base sm:text-lg transition-transform';
      }
      btn.classList.add('ring-4', 'ring-pink-300/60');
    } else {
      btnLabel.innerText = 'অর্ডার করুন';
      if (btnIcon) {
        btnIcon.className = 'fa-solid fa-cart-shopping text-base sm:text-lg group-hover:rotate-12 transition-transform';
      }
      btn.classList.remove('ring-4', 'ring-pink-300/60');
    }
  }

  // Initialization on DOM load
  document.addEventListener('DOMContentLoaded', () => {
    calculateOrderTotals();

    const phoneInput = document.getElementById('custPhone');
    const phoneErr = document.getElementById('phoneErrLabel');

    if (phoneInput) {
      phoneInput.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 11);
        const val = e.target.value;

        // Reset block alert while typing
        const blockAlert = document.getElementById('phoneBlockedAlert');
        if (blockAlert) blockAlert.classList.add('hidden');
        isCurrentPhoneBlocked = false;

        if (val.length === 11) {
          if (!/^01[3-9]\d{8}$/.test(val)) {
            if (phoneErr) phoneErr.classList.remove('hidden');
          } else {
            if (phoneErr) phoneErr.classList.add('hidden');
            checkPhoneBlockStatus(val);
          }
        } else {
          if (phoneErr) phoneErr.classList.add('hidden');
        }
        saveProgressDebounced();
      });
    }

    const form = document.getElementById('checkoutForm');
    if (form) {
      form.addEventListener('input', updateFloatingButtonState);
      form.addEventListener('change', updateFloatingButtonState);
      updateFloatingButtonState();

      form.addEventListener('submit', (e) => {
        if (isCurrentPhoneBlocked) {
          e.preventDefault();
          const blockAlert = document.getElementById('phoneBlockedAlert');
          if (blockAlert) {
            blockAlert.classList.remove('hidden');
            blockAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          alert('এই নম্বরটি অ্যাডমিন কর্তৃক ব্লক করা হয়েছে। অর্ডারের জন্য অনুগ্রহ করে হোয়াটসঅ্যাপে কথা বলুন।');
          return;
        }

        const checked = document.querySelectorAll('input[name="selected_products[]"]:checked');
        if (checked.length === 0) {
          e.preventDefault();
          alert('অনুগ্রহ করে একটি প্রোডাক্ট নির্বাচন করুন!');
          return;
        }

        const phone = phoneInput ? phoneInput.value.trim() : '';
        if (!/^01[3-9]\d{8}$/.test(phone)) {
          e.preventDefault();
          if (phoneErr) phoneErr.classList.remove('hidden');
          phoneInput.focus();
          return;
        }

        const btn = document.getElementById('btnConfirmOrder');
        if (btn) {
          btn.disabled = true;
          btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> <span>অর্ডার সম্পন্ন হচ্ছে...</span>';
        }

        const floatingBtn = document.getElementById('floatingOrderBtn');
        if (floatingBtn) {
          floatingBtn.style.pointerEvents = 'none';
          floatingBtn.classList.add('opacity-80');
          const floatingText = document.getElementById('floatingOrderBtnText');
          if (floatingText) {
            floatingText.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm mr-1"></i> <span>অর্ডার সম্পন্ন হচ্ছে...</span>';
          }
        }
      });
    }
  });
</script>

<!-- Floating Order Now Sticky Footer Bar (Centered, Compact Height) -->
<div class="fixed bottom-0 inset-x-0 z-50 pb-2 sm:pb-3 pt-1.5 bg-gradient-to-t from-white via-white/95 to-transparent pointer-events-none">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 pointer-events-auto">
    <button type="button"
       id="floatingOrderBtn"
       onclick="handleFloatingOrderClick(event)"
       class="w-full inline-flex items-center justify-center gap-2.5 bg-gradient-to-r from-[#ec2f6f] to-[#e61e6e] hover:from-[#db1b5d] hover:to-[#d0135e] text-white font-heading font-bold text-sm sm:text-base py-2.5 sm:py-3 px-5 sm:px-6 rounded-xl sm:rounded-full shadow-[0_4px_20px_rgba(236,47,111,0.5)] transition-all duration-300 hover:scale-[1.01] active:scale-[0.99] group cursor-pointer"
       title="অর্ডার করুন">
      <span class="relative flex h-2.5 w-2.5">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
      </span>
      <i id="floatingOrderBtnIcon" class="fa-solid fa-cart-shopping text-base sm:text-lg group-hover:rotate-12 transition-transform"></i>
      <span id="floatingOrderBtnText">অর্ডার করুন</span>
    </button>
  </div>
</div>

</body>
</html>
