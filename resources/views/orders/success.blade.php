<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  $siteSettings = \App\Models\Setting::current();
  $pageTitle = 'অর্ডার নিশ্চিতকরণ — ' . $order->order_number . ' | ' . ($siteSettings->site_name ?? 'Electora');
@endphp
<title>{{ $pageTitle }}</title>
@if (!empty($siteSettings->favicon))
  <link rel="icon" href="{{ $siteSettings->favicon_url ?: asset($siteSettings->favicon) }}">
@endif

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Anek+Bangla:wght@600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
  :root {
    --primary-color: #10b981;
    --heading-blue: #1d4ed8;
    --text-main: #1f2937;
    --whatsapp-color: #25D366;
    --whatsapp-hover: #1eb956;
    --fb-page-color: #1877F2;
    --fb-group-color: #7c3aed;
    --danger-color: #dc2626;
    --radius: 14px;
  }
  body {
    font-family: 'Hind Siliguri', sans-serif, -apple-system;
    background-color: #f8fafc;
    color: var(--text-main);
  }
  .custom-thankyou-wrapper {
    max-width: 860px;
    margin: 24px auto 40px auto;
    padding: 0 16px;
  }
  .custom-thankyou-card {
    background: #ffffff;
    border-radius: var(--radius);
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.06);
    padding: 32px 20px;
    text-align: center;
    margin-bottom: 22px;
    border: 1px solid #e5e7eb;
  }
  .custom-success-icon {
    width: 70px;
    height: 70px;
    background-color: #ecfdf5;
    color: var(--primary-color);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    border: 3px solid #a7f3d0;
  }
  .custom-success-icon svg { width: 36px; height: 36px; }
  .custom-main-title { font-size: 26px; font-weight: 700; color: var(--heading-blue); margin-bottom: 4px; }
  .custom-sub-title { font-size: 18px; font-weight: 600; color: #059669; margin-bottom: 14px; }
  .custom-notice-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-left: 4px solid var(--heading-blue);
    border-radius: 8px;
    padding: 14px 18px;
    margin: 14px auto;
    max-width: 650px;
    font-size: 15px;
    color: #1e3a8a;
    font-weight: 500;
    text-align: center;
    line-height: 1.5;
  }
  .custom-summary-box {
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 16px;
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 14px;
    text-align: left;
  }
  .custom-summary-item { border-right: 1px dashed #d1d5db; padding-right: 8px; }
  .custom-summary-item:last-child { border-right: none; }
  .custom-summary-label { font-size: 12px; color: #6b7280; display: block; }
  .custom-summary-val { font-size: 15px; font-weight: 700; color: #111827; }

  /* Animated Next Order Free Delivery Offer */
  @keyframes freeDeliveryPulse {
    0%, 100% {
      box-shadow: 0 4px 18px -2px rgba(16, 185, 129, 0.25), 0 0 0 1px rgba(16, 185, 129, 0.3);
      border-color: #10b981;
      transform: scale(1);
    }
    50% {
      box-shadow: 0 8px 30px rgba(16, 185, 129, 0.45), 0 0 0 3px rgba(16, 185, 129, 0.5);
      border-color: #059669;
      transform: scale(1.015);
    }
  }
  @keyframes freeDeliveryBounce {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-3px) scale(1.06); }
  }
  @keyframes truckShift {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(5px); }
  }
  @keyframes shimmerGradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  @keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.08); }
  }
  .custom-free-delivery-card {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 45%, #eff6ff 100%);
    background-size: 200% 200%;
    animation: shimmerGradient 6s ease infinite, freeDeliveryPulse 2.4s ease-in-out infinite;
    border: 2px dashed #059669;
    border-radius: var(--radius);
    padding: 18px 24px;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .free-delivery-icon-box {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
    animation: freeDeliveryBounce 2s ease-in-out infinite;
  }
  .free-delivery-icon-box i {
    animation: truckShift 1.4s ease-in-out infinite;
  }
  .free-delivery-content-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
  }
  .free-delivery-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    padding: 3px 12px;
    border-radius: 50px;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.35);
    animation: badgePulse 1.8s ease-in-out infinite;
  }
  .free-delivery-text {
    font-size: 20px;
    font-weight: 800;
    color: #065f46;
    margin: 0;
    line-height: 1.4;
    font-family: 'Anek Bangla', 'Hind Siliguri', sans-serif;
  }
  .free-delivery-highlight {
    color: #dc2626;
    background: #fef2f2;
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px dashed #f87171;
    display: inline-block;
    margin: 0 4px;
  }
  @media (max-width: 768px) {
    .custom-free-delivery-card {
      flex-direction: column;
      padding: 16px 14px;
      gap: 10px;
    }
    .free-delivery-text {
      font-size: 17px;
    }
  }

  /* WhatsApp Direct Contact */
  .custom-wa-card {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 2px solid #86efac;
    border-radius: var(--radius);
    padding: 22px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }
  .custom-wa-text { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 260px; }
  .custom-wa-icon {
    width: 50px;
    height: 50px;
    background: var(--whatsapp-color);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .custom-wa-icon svg { width: 28px; height: 28px; }
  .custom-wa-btn {
    background: var(--whatsapp-color);
    color: #fff !important;
    text-decoration: none !important;
    padding: 12px 22px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
    transition: all 0.2s ease;
  }
  .custom-wa-btn:hover {
    background: var(--whatsapp-hover);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
  }

  /* Upsell Products Section */
  .custom-upsell-section {
    background: #ffffff;
    border-radius: var(--radius);
    padding: 26px 18px;
    border: 1px solid #e5e7eb;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
  }
  .custom-upsell-header { text-align: center; margin-bottom: 22px; }
  .custom-offer-pill {
    display: inline-block;
    background: linear-gradient(135deg, #f43f5e, #e11d48);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 12px;
    border-radius: 50px;
    margin-bottom: 6px;
  }
  .custom-upsell-title { font-size: 22px; font-weight: 700; color: #111827; }
  .custom-upsell-title span { color: #dc2626; background: #fef2f2; padding: 2px 8px; border-radius: 6px; border: 1px dashed #f87171; }
  .custom-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 16px;
  }
  @media (max-width: 768px) {
    .custom-products-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .custom-wa-card { flex-direction: column; text-align: center; }
    .custom-wa-text { flex-direction: column; }
  }
  .custom-prod-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
    background: #fff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .custom-prod-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.08);
  }
  .custom-disc-tag {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--danger-color);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 7px;
    border-radius: 4px;
    z-index: 2;
  }
  .custom-prod-img { height: 180px; background: #f8fafc; overflow: hidden; display: flex; align-items: center; justify-content: center; }
  .custom-prod-img img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }
  .custom-prod-body { padding: 14px; display: flex; flex-direction: column; flex-grow: 1; }
  .custom-prod-title { font-size: 14px; font-weight: 600; margin-bottom: 8px; min-height: 40px; color: #1f2937; line-height: 1.35; }
  .custom-prod-price { display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
  .price-off { font-size: 17px; font-weight: 700; color: #dc2626; font-family: 'Roboto', sans-serif; }
  .price-reg { font-size: 13px; color: #94a3b8; text-decoration: line-through; font-family: 'Roboto', sans-serif; }
  .btn-buy {
    margin-top: auto;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff !important;
    text-decoration: none !important;
    padding: 9px 12px;
    border-radius: 6px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: opacity 0.2s ease;
  }
  .btn-buy:hover { opacity: 0.95; }

  /* Facebook Page Follow Card */
  .custom-fb-card {
    background: linear-gradient(135deg, #f0f7ff 0%, #e8f2fe 100%);
    border: 2px solid #bfdbfe;
    border-radius: var(--radius);
    padding: 22px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    box-shadow: 0 4px 16px -2px rgba(24, 119, 242, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .custom-fb-card:hover {
    box-shadow: 0 6px 20px -2px rgba(24, 119, 242, 0.14);
  }
  .custom-fb-text {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    min-width: 260px;
  }
  .custom-fb-icon {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #1877F2, #0d65d9);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3);
  }
  .custom-fb-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #dbeafe;
    color: #1d4ed8;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    margin-bottom: 4px;
  }
  .custom-fb-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
  }
  .custom-fb-desc {
    font-size: 13px;
    color: #475569;
    line-height: 1.4;
  }
  .custom-fb-btn {
    background: linear-gradient(135deg, #1877F2, #0d65d9);
    color: #fff !important;
    text-decoration: none !important;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(24, 119, 242, 0.35);
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  .custom-fb-btn:hover {
    background: linear-gradient(135deg, #166fe5, #0c5bc4);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(24, 119, 242, 0.45);
  }
  @media (max-width: 768px) {
    .custom-fb-card {
      flex-direction: column;
      text-align: center;
      padding: 20px 16px;
    }
    .custom-fb-text {
      flex-direction: column;
      text-align: center;
    }
    .custom-fb-btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>

@include('partials.tracking-scripts')
</head>

<body>

<!-- Header (Matching home.blade.php exactly) -->
@include('partials.site-header', ['orderBtnTarget' => route('home'), 'orderBtnLabel' => 'শピング চালিয়ে যান'])

<div class="custom-thankyou-wrapper">

  <!-- 1. Header & Dynamic Order Info -->
  <div class="custom-thankyou-card">
    <div class="custom-success-icon">
      <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
      </svg>
    </div>

    <h1 class="custom-main-title">আপনার অর্ডার টি সফল হয়েছে</h1>
    <h2 class="custom-sub-title">অর্ডার করার জন্য ধন্যবাদ</h2>

    <div class="custom-notice-box">
      আমাদের এখান থেকে একজন কাস্টমার প্রতিনিধি আপনাকে ফোন দিয়ে, আপনার অর্ডারটি কনফার্ম করবে। দয়া করে আপনার ফোন নাম্বারটি সচল রাখবেন।
    </div>

    <p style="font-size: 13px; color: #6b7280; margin-top: 8px;">Thank you. Your order has been received.</p>

    <!-- Summary box -->
    <div class="custom-summary-box">
      <div class="custom-summary-item">
        <span class="custom-summary-label">Order number:</span>
        <span class="custom-summary-val">{{ $order->order_number }}</span>
      </div>
      <div class="custom-summary-item">
        <span class="custom-summary-label">Date:</span>
        <span class="custom-summary-val">{{ $order->created_at->format('M d, Y') }}</span>
      </div>
      <div class="custom-summary-item">
        <span class="custom-summary-label">Phone:</span>
        <span class="custom-summary-val font-numeric">{{ $order->phone }}</span>
      </div>
      <div class="custom-summary-item">
        <span class="custom-summary-label">Total:</span>
        <span class="custom-summary-val font-numeric" style="color: #059669;">{{ number_format($order->total, 2) }}&#2547;</span>
      </div>
      <div class="custom-summary-item">
        <span class="custom-summary-label">Payment method:</span>
        <span class="custom-summary-val">{{ strtoupper($order->payment_method ?? 'COD') === 'COD' ? 'Cash on delivery' : ucfirst($order->payment_method) }}</span>
      </div>
    </div>

    <!-- Ordered items breakdown table -->
    @if($order->items && $order->items->count())
      <div style="margin-top: 22px; border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: left;">
        <h4 style="font-size: 14px; font-weight: 700; color: #374151; margin-bottom: 10px;">অর্ডারকৃত প্রোডাক্ট ও বিবরণ:</h4>
        <div style="display: flex; flex-direction: column; gap: 8px;">
          @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #4b5563; padding: 6px 0; border-bottom: 1px dashed #f3f4f6;">
              <div>
                <span class="font-medium text-gray-900">{{ $item->product_name }}</span>
                <span class="text-gray-500 font-numeric">&times; {{ $item->quantity }}</span>
                @if($item->size || $item->color)
                  <span class="text-xs text-gray-400">({{ implode(', ', array_filter([$item->size, $item->color])) }})</span>
                @endif
              </div>
              <span style="font-weight: 700; color: #111827;" class="font-numeric">{{ number_format($item->subtotal, 2) }}&#2547;</span>
            </div>
          @endforeach

          <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; padding-top: 4px;">
            <span>সাবটোটাল</span>
            <span style="font-weight: 600; color: #111827;" class="font-numeric">{{ number_format($order->subtotal, 2) }}&#2547;</span>
          </div>

          <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280;">
            <span>শিপিং চার্জ ({{ $order->district }})</span>
            <span style="font-weight: 600; color: #111827;" class="font-numeric">{{ number_format($order->delivery_charge, 2) }}&#2547;</span>
          </div>

          @if($order->discount_amount > 0)
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #dc2626;">
              <span>ডিসকাউন্ট</span>
              <span style="font-weight: 600;" class="font-numeric">-{{ number_format($order->discount_amount, 2) }}&#2547;</span>
            </div>
          @endif

          <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; color: #111827; border-top: 1px solid #e5e7eb; padding-top: 8px;">
            <span>সর্বমোট বিল (Total)</span>
            <span style="color: #059669;" class="font-numeric text-base">{{ number_format($order->total, 2) }}&#2547;</span>
          </div>
        </div>
      </div>
    @endif
  </div>

  <!-- Free Delivery on Next Order Banner with Eye-Catching Animation (Placed between Invoice & WhatsApp Button) -->
  <div class="custom-free-delivery-card">
    <div class="free-delivery-icon-box">
      <i class="fa-solid fa-truck-fast"></i>
    </div>
    <div class="free-delivery-content-box">
      <div class="free-delivery-tag">
        <i class="fa-solid fa-gift"></i>
        <span>স্পেশাল অফার</span>
      </div>
      <h3 class="free-delivery-text">
        পরবর্তী অর্ডারে আপনার জন্য ডেলিভারি চার্জ <span class="free-delivery-highlight">সম্পূর্ণ ফ্রি</span>!
      </h3>
    </div>
  </div>

  <!-- 2. WhatsApp Direct Contact (Placed Right After Confirmation) -->
  @php
    $orderWaNum = $siteSettings->whatsapp ?: $siteSettings->phone;
    $orderWaDigits = preg_replace('/[^0-9]/', '', $orderWaNum ?? '');
    if (strlen($orderWaDigits) === 11 && str_starts_with($orderWaDigits, '01')) {
        $orderWaDigits = '88' . $orderWaDigits;
    }
    $waText = 'আসসালামু আলাইকুম, আমার অর্ডার নম্বর #' . $order->order_number . ' সম্পর্কে জানতে চাই।';
  @endphp
  <div class="custom-wa-card">
    <div class="custom-wa-text">
      <div class="custom-wa-icon">
        <i class="fa-brands fa-whatsapp text-2xl"></i>
      </div>
      <div>
        <h3 style="font-size: 17px; font-weight: 700; color: #065f46; margin-bottom: 2px;">কোনো পরামর্শ বা জানার থাকলে হোয়াটসঅ্যাপে যোগাযোগ করুন</h3>
        <p style="font-size: 13px; color: #047857;">অর্ডার সংক্রান্ত দ্রুত সহায়তা পেতে সরাসরি মেসেজ দিন</p>
      </div>
    </div>
    @if (!empty($orderWaDigits))
      <a href="https://wa.me/{{ $orderWaDigits }}?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener noreferrer" class="custom-wa-btn">
        <i class="fa-brands fa-whatsapp text-lg"></i>
        <span>হোয়াটসঅ্যাপে SMS করুন</span>
      </a>
    @endif
  </div>

  <!-- 3. Upsell Discount Products (Showing all products uploaded from admin) -->
  @if(isset($moreProducts) && $moreProducts->count())
    <div class="custom-upsell-section">
      <div class="custom-upsell-header">
        <span class="custom-offer-pill">🔥 বিশেষ ছাড়</span>
        <h2 class="custom-upsell-title">আপনার জন্য আমাদের বিশেষ অফার! <span>স্পেশাল ডিসকাউন্ট</span></h2>
        <p style="font-size: 14px; color: #64748b; margin-top: 4px;">অর্ডারকারী স্পেশাল অফার - আমাদের অন্যান্য জনপ্রিয় প্রোডাক্টগুলোতে আজকেই পাচ্ছেন আকর্ষণীয় মূল্যছাড়!</p>
      </div>

      <div class="custom-products-grid">
        @foreach($moreProducts as $prod)
          @php
            $prodImg = $prod->image_url ?: ($prod->image ? asset($prod->image) : 'https://placehold.co/400x400/f8fafc/334155?text='.urlencode($prod->name));
            $discPct = $prod->discountPercent();
            if (!$discPct && $prod->regular_price > $prod->sale_price && $prod->regular_price > 0) {
                $discPct = (int) round((($prod->regular_price - $prod->sale_price) / $prod->regular_price) * 100);
            }
            $saleP = (float) ($prod->sale_price ?? $prod->regular_price);
            $regP = (float) $prod->regular_price;
          @endphp

          <div class="custom-prod-card">
            @if($discPct)
              <span class="custom-disc-tag">{{ $discPct }}% ছাড়</span>
            @else
              <span class="custom-disc-tag">বিশেষ অফার</span>
            @endif

            <div class="custom-prod-img">
              <img src="{{ $prodImg }}" alt="{{ $prod->name }}" loading="lazy">
            </div>

            <div class="custom-prod-body">
              <h4 class="custom-prod-title">{{ $prod->name }}</h4>
              
              <div class="custom-prod-price">
                <span class="price-off font-numeric">{{ number_format($saleP, 0) }}&#2547;</span>
                @if($regP > $saleP)
                  <span class="price-reg font-numeric">{{ number_format($regP, 0) }}&#2547;</span>
                @endif
              </div>

              <a href="{{ route('landing.show', $prod->slug) }}" class="btn-buy">
                <i class="fa-solid fa-cart-shopping text-xs"></i>
                <span>Buy Now (কিনুন)</span>
              </a>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- 4. Facebook Page Follow Section -->
  @php
    $fbUrl = $siteSettings->facebook_url ?: 'https://facebook.com';
  @endphp
  <div class="custom-fb-card">
    <div class="custom-fb-text">
      <div class="custom-fb-icon">
        <i class="fa-brands fa-facebook-f text-2xl"></i>
      </div>
      <div>
        <div class="custom-fb-badge">
          <i class="fa-solid fa-circle-check text-xs"></i>
          <span>অফিসিয়াল ফেসবুক পেজ</span>
        </div>
        <h3 class="custom-fb-title">আমাদের ফেসবুক পেজ ফলো করুন</h3>
        <p class="custom-fb-desc">নতুন অফার, প্রডাক্ট আপডেট এবং যেকোনো প্রয়োজনে আমাদের ফেসবুক পেজে যুক্ত থাকুন</p>
      </div>
    </div>
    <a href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer" class="custom-fb-btn">
      <i class="fa-brands fa-facebook text-lg"></i>
      <span>ফেসবুক পেজ ফলো করুন</span>
      <i class="fa-solid fa-arrow-right text-xs"></i>
    </a>
  </div>

</div>

</body>
</html>
