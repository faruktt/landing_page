@extends('layouts.admin')

@section('title', 'ওয়েবসাইট সেটিংস')

@php
    $tabs = [
        'identity' => ['label' => 'সাইট পরিচিতি', 'icon' => 'fa-store'],
        'landing' => ['label' => 'ল্যান্ডিং পেজ টেক্সট', 'icon' => 'fa-laptop-code'],
        'contact' => ['label' => 'যোগাযোগ তথ্য', 'icon' => 'fa-address-book'],
        'social' => ['label' => 'সোশ্যাল মিডিয়া', 'icon' => 'fa-share-nodes'],
        'seo' => ['label' => 'SEO / মেটা', 'icon' => 'fa-magnifying-glass-chart'],
        'delivery' => ['label' => 'ডেলিভারি চার্জ', 'icon' => 'fa-truck-fast'],
        'tracking' => ['label' => 'ট্র্যাকিং কোড', 'icon' => 'fa-chart-line'],
    ];
    $activeTab = old('_active_tab', 'identity');
@endphp

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">ওয়েবসাইট সেটিংস</h2>
  <p class="text-slate-500 text-sm mt-1">সাইটের সাধারণ তথ্য, যোগাযোগ, SEO, ট্র্যাকিং ও ডেলিভারি চার্জ ম্যানেজ করুন।</p>
</div>

@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" id="settingsForm">
  @csrf
  @method('PUT')
  <input type="hidden" name="_active_tab" id="activeTabInput" value="{{ $activeTab }}">

  <!-- Tab nav -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-1.5 mb-6 flex flex-wrap gap-1">
    @foreach ($tabs as $key => $tab)
      <button type="button" data-tab-btn="{{ $key }}"
              class="tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $activeTab === $key ? 'bg-brand text-white' : 'text-slate-500 hover:bg-slate-50' }}">
        <i class="fa-solid {{ $tab['icon'] }} text-xs"></i>
        {{ $tab['label'] }}
      </button>
    @endforeach
  </div>

  <!-- Site identity -->
  <div data-tab-panel="identity" class="tab-panel {{ $activeTab === 'identity' ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 mb-6">
    <h3 class="font-semibold text-slate-900 mb-4">সাইট পরিচিতি</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">সাইটের নাম *</label>
        <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name) }}" required
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">ট্যাগলাইন</label>
        <input type="text" name="tagline" value="{{ old('tagline', $setting->tagline) }}" placeholder="যেমনঃ ফ্যাশন ও লাইফস্টাইল"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">লোগো</label>
        <div class="flex items-center gap-3">
          @if ($setting->logo)
            <img src="{{ $setting->logo_url ?: asset($setting->logo) }}" class="h-12 w-12 rounded-lg object-cover border border-slate-200 bg-slate-50">
          @endif
          <input type="file" name="logo" accept="image/*"
                 class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">ফ্যাভিকন</label>
        <div class="flex items-center gap-3">
          @if ($setting->favicon)
            <img src="{{ $setting->favicon_url ?: asset($setting->favicon) }}" class="h-12 w-12 rounded-lg object-cover border border-slate-200 bg-slate-50">
          @endif
          <input type="file" name="favicon" accept="image/*"
                 class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
        </div>
      </div>
    </div>
  </div>

  <!-- Landing Page Texts -->
  <div data-tab-panel="landing" class="tab-panel {{ $activeTab === 'landing' ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 mb-6">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-5">
      <div>
        <h3 class="font-semibold text-slate-900">ল্যান্ডিং পেজ কন্টেন্ট ও টেক্সট</h3>
        <p class="text-xs text-slate-400 mt-0.5">হোম ল্যান্ডিং পেজের সকল টাইটেল, ব্যানার ও অফার টেক্সট এখান থেকে পরিবর্তন করুন।</p>
      </div>
      <span class="text-xs font-bold bg-orange-50 text-brand px-3 py-1 rounded-full border border-orange-200">
        Electora Style
      </span>
    </div>

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">টপ নোটিফিকেশন বার (Top Announcement)</label>
        <input type="text" name="landing_announcement" value="{{ old('landing_announcement', $setting->landing_announcement) }}" placeholder="যেমনঃ ২৪ ঘন্টা গ্যাস থাকবে আপনার রান্নাঘরে গ্যারান্টি দিয়ে বলছি।"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Short Text</label>
        <input type="text" name="short_text" value="{{ old('short_text', $setting->short_text) }}" placeholder="যেমনঃ ২৪ ঘন্টা গ্যাস থাকবে আপনার রান্নাঘরে গ্যারান্টি দিয়ে বলছি।"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>

      <div class="grid sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">ওয়ারেন্টি ব্যাজ ১</label>
          <input type="text" name="landing_badge_1" value="{{ old('landing_badge_1', $setting->landing_badge_1) }}" placeholder="2 Year Service Warranty"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">ওয়ারেন্টি ব্যাজ ২</label>
          <input type="text" name="landing_badge_2" value="{{ old('landing_badge_2', $setting->landing_badge_2) }}" placeholder="১ বছরের রিপ্লেসমেন্ট গ্যারান্টি"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">ডেলিভারি ব্যাজ ৩</label>
          <input type="text" name="landing_badge_3" value="{{ old('landing_badge_3', $setting->landing_badge_3) }}" placeholder="ক্যাশ অন ডেলিভারি"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">অফার ব্যানার টাইটেল (Countdown Banner)</label>
          <input type="text" name="landing_offer_title" value="{{ old('landing_offer_title', $setting->landing_offer_title) }}" placeholder="আজকের জন্য ধামাকা অফার!!!"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">কাউন্টডাউন সাবটাইটেল</label>
          <input type="text" name="landing_offer_subtitle" value="{{ old('landing_offer_subtitle', $setting->landing_offer_subtitle) }}" placeholder="অফারটি শেষ হতে বাকি"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">উপকারিতা সেকশন টাইটেল</label>
          <input type="text" name="landing_benefits_title" value="{{ old('landing_benefits_title', $setting->landing_benefits_title) }}" placeholder="গ্যাস কম্প্রেসর এর বিশেষ উপকারিতাঃ"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">রিভিউ সেকশন টাইটেল</label>
          <input type="text" name="landing_reviews_title" value="{{ old('landing_reviews_title', $setting->landing_reviews_title) }}" placeholder="গ্রাহকদের আস্থা ও ফিডব্যাক"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">অর্ডার ফর্মের টাইটেল (Order Section)</label>
          <input type="text" name="landing_order_title" value="{{ old('landing_order_title', $setting->landing_order_title) }}" placeholder="অর্ডার করতে নিচের ফর্ম টি পূরণ করুন👇"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">অর্ডার বাটন টেক্সট</label>
          <input type="text" name="landing_order_btn_text" value="{{ old('landing_order_btn_text', $setting->landing_order_btn_text) }}" placeholder="আপনার অর্ডারটি কনফার্ম করুন"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
      </div>

      <!-- Model 1 (TX-3000G++) Section -->
      <div class="border-t border-slate-100 pt-5 mt-5">
        <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-brand text-white flex items-center justify-center text-xs">১</span>
          <span>প্রথম প্রোডাক্ট মডেল (TX-3000G++ বা মূল মডেল)</span>
        </h4>
        <div class="space-y-4">
          <div class="grid sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">মডেলের নাম / টাইটেল</label>
              <input type="text" name="landing_p1_title" value="{{ old('landing_p1_title', $setting->landing_p1_title) }}" placeholder="গ্যাস কম্প্রেসর Model: TX-3000G++"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">ওয়ারেন্টি ব্যাজ (ব্লু)</label>
              <input type="text" name="landing_p1_badge_1" value="{{ old('landing_p1_badge_1', $setting->landing_p1_badge_1) }}" placeholder="2 YEAR SERVICE WARRANTY"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">রিপ্লেসমেন্ট ব্যাজ (গ্রিন)</label>
              <input type="text" name="landing_p1_badge_2" value="{{ old('landing_p1_badge_2', $setting->landing_p1_badge_2) }}" placeholder="১ বছরের রিপ্লেসমেন্ট গ্যারান্টি"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">রেগুলার প্রাইস (টাকা)</label>
              <input type="text" name="landing_p1_regular_price" value="{{ old('landing_p1_regular_price', $setting->landing_p1_regular_price) }}" placeholder="২৮০০"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">আজকের অফার প্রাইস (টাকা)</label>
              <input type="text" name="landing_p1_sale_price" value="{{ old('landing_p1_sale_price', $setting->landing_p1_sale_price) }}" placeholder="২২০০"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">মডেল ১ এর বুলেট পয়েন্টসমূহ (প্রতি লাইনে ১টি পয়েন্ট)</label>
            <textarea name="landing_p1_bullets" rows="4" placeholder="প্রতি লাইনে একটি করে বুলেট পয়েন্ট লিখুন..."
                      class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">{{ old('landing_p1_bullets', $setting->landing_p1_bullets) }}</textarea>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">মডেল ১ ছবি ১ (বামের ছবি)</label>
              <div class="flex items-center gap-3">
                @if ($setting->landing_p1_image_1)
                  <img src="{{ asset('storage/'.$setting->landing_p1_image_1) }}" class="h-12 w-12 rounded-lg object-contain border border-slate-200 bg-white">
                @endif
                <input type="file" name="landing_p1_image_1" accept="image/*"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">মডেল ১ ছবি ২ (ডানের ছবি)</label>
              <div class="flex items-center gap-3">
                @if ($setting->landing_p1_image_2)
                  <img src="{{ asset('storage/'.$setting->landing_p1_image_2) }}" class="h-12 w-12 rounded-lg object-contain border border-slate-200 bg-white">
                @endif
                <input type="file" name="landing_p1_image_2" accept="image/*"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Model 2 (MAX) Section -->
      <div class="border-t border-slate-100 pt-5 mt-5">
        <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-brand text-white flex items-center justify-center text-xs">২</span>
          <span>দ্বিতীয় প্রোডাক্ট মডেল (MAX মডেল)</span>
        </h4>
        <div class="space-y-4">
          <div class="grid sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">মডেলের নাম / টাইটেল</label>
              <input type="text" name="landing_p2_title" value="{{ old('landing_p2_title', $setting->landing_p2_title) }}" placeholder="গ্যাস কম্প্রেসর Model: MAX"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">ওয়ারেন্টি ব্যাজ (ব্লু)</label>
              <input type="text" name="landing_p2_badge_1" value="{{ old('landing_p2_badge_1', $setting->landing_p2_badge_1) }}" placeholder="5 YEARS SERVICE WARRANTY"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">রিপ্লেসমেন্ট ব্যাজ (পিঙ্ক)</label>
              <input type="text" name="landing_p2_badge_2" value="{{ old('landing_p2_badge_2', $setting->landing_p2_badge_2) }}" placeholder="৩০ দিনের রিপ্লেসমেন্ট গ্যারান্টি"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">রেগুলার প্রাইস (টাকা)</label>
              <input type="text" name="landing_p2_regular_price" value="{{ old('landing_p2_regular_price', $setting->landing_p2_regular_price) }}" placeholder="৪৯০০"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">আজকের অফার প্রাইস (টাকা)</label>
              <input type="text" name="landing_p2_sale_price" value="{{ old('landing_p2_sale_price', $setting->landing_p2_sale_price) }}" placeholder="৪৩০০"
                     class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">মডেল ২ এর বুলেট পয়েন্টসমূহ (প্রতি লাইনে ১টি পয়েন্ট)</label>
            <textarea name="landing_p2_bullets" rows="4" placeholder="প্রতি লাইনে একটি করে বুলেট পয়েন্ট লিখুন..."
                      class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">{{ old('landing_p2_bullets', $setting->landing_p2_bullets) }}</textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">মডেল ২ শোকেস ছবি</label>
            <div class="flex items-center gap-3">
              @if ($setting->landing_p2_image)
                <img src="{{ asset('storage/'.$setting->landing_p2_image) }}" class="h-12 w-12 rounded-lg object-contain border border-slate-200 bg-white">
              @endif
              <input type="file" name="landing_p2_image" accept="image/*"
                     class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
            </div>
          </div>
        </div>
      </div>

      <!-- Benefits Section -->
      <div class="border-t border-slate-100 pt-5 mt-5">
        <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-brand text-white flex items-center justify-center text-xs">৩</span>
          <span>উপকারিতা সেকশন তালিকা (প্রতি লাইনে ১টি উপকারিতা)</span>
        </h4>
        <textarea name="landing_benefits_list" rows="5" placeholder="প্রতি লাইনে একটি করে উপকারিতা লিখুন..."
                  class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">{{ old('landing_benefits_list', $setting->landing_benefits_list) }}</textarea>
      </div>

      <!-- Outlook & Review Buttons -->
      <div class="border-t border-slate-100 pt-5 mt-5">
        <h4 class="text-sm font-bold text-slate-800 mb-3">বাটন টেক্সট</h4>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">আউটলুক বাটন টেক্সট</label>
            <input type="text" name="landing_outlook_btn_text" value="{{ old('landing_outlook_btn_text', $setting->landing_outlook_btn_text) }}" placeholder="প্রোডাক্টের আউটলুক"
                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">রিভিউ বাটন টেক্সট</label>
            <input type="text" name="landing_review_btn_text" value="{{ old('landing_review_btn_text', $setting->landing_review_btn_text) }}" placeholder="Review"
                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Contact -->
  <div data-tab-panel="contact" class="tab-panel {{ $activeTab === 'contact' ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 mb-6">
    <h3 class="font-semibold text-slate-900 mb-4">যোগাযোগ তথ্য</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-solid fa-phone text-slate-400 text-xs mr-1"></i> ফোন নম্বর</label>
        <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-brands fa-whatsapp text-emerald-500 text-xs mr-1"></i> হোয়াটসঅ্যাপ নম্বর</label>
        <input type="text" name="whatsapp" value="{{ old('whatsapp', $setting->whatsapp) }}"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-solid fa-envelope text-slate-400 text-xs mr-1"></i> ইমেইল</label>
        <input type="email" name="email" value="{{ old('email', $setting->email) }}"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-solid fa-location-dot text-slate-400 text-xs mr-1"></i> ঠিকানা</label>
        <input type="text" name="address" value="{{ old('address', $setting->address) }}"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
    </div>
  </div>

  <!-- Social -->
  <div data-tab-panel="social" class="tab-panel {{ $activeTab === 'social' ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 mb-6">
    <h3 class="font-semibold text-slate-900 mb-4">সোশ্যাল মিডিয়া লিংক</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-brands fa-facebook text-blue-600 text-xs mr-1"></i> ফেসবুক পেজ</label>
        <input type="url" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}" placeholder="https://facebook.com/yourpage"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-brands fa-instagram text-pink-600 text-xs mr-1"></i> ইনস্টাগ্রাম</label>
        <input type="url" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}" placeholder="https://instagram.com/yourpage"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-brands fa-youtube text-red-600 text-xs mr-1"></i> ইউটিউব</label>
        <input type="url" name="youtube_url" value="{{ old('youtube_url', $setting->youtube_url) }}" placeholder="https://youtube.com/@yourchannel"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
    </div>
  </div>

  <!-- SEO -->
  <div data-tab-panel="seo" class="tab-panel {{ $activeTab === 'seo' ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 mb-6">
    <h3 class="font-semibold text-slate-900 mb-4">SEO / মেটা তথ্য</h3>
    <div class="space-y-4">
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">মেটা টাইটেল</label>
          <input type="text" name="meta_title" value="{{ old('meta_title', $setting->meta_title) }}"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">মেটা কিওয়ার্ড</label>
          <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $setting->meta_keywords) }}" placeholder="কমা দিয়ে আলাদা করুন"
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">মেটা ডেসক্রিপশন</label>
        <textarea name="meta_description" rows="2" maxlength="500"
                  class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">{{ old('meta_description', $setting->meta_description) }}</textarea>
      </div>
    </div>
  </div>

  <!-- Delivery -->
  <div data-tab-panel="delivery" class="tab-panel {{ $activeTab === 'delivery' ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 mb-6">
    <h3 class="font-semibold text-slate-900 mb-1">ডেলিভারি চার্জ</h3>
    <p class="text-xs text-slate-400 mb-4">ঢাকা ও ঢাকার বাইরে অনুযায়ী ডেলিভারি চার্জের হার নির্ধারণ করুন।</p>
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">ঢাকা (৳)</label>
        <input type="number" name="dhaka_delivery_charge" value="{{ old('dhaka_delivery_charge', (float) $setting->dhaka_delivery_charge) }}" step="0.01" min="0" required
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">ঢাকার বাইরে (৳)</label>
        <input type="number" name="default_delivery_charge" value="{{ old('default_delivery_charge', (float) $setting->default_delivery_charge) }}" step="0.01" min="0" required
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
    </div>
  </div>

  <!-- Tracking -->
  <div data-tab-panel="tracking" class="tab-panel {{ $activeTab === 'tracking' ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 mb-6">
    <h3 class="font-semibold text-slate-900 mb-1">ট্র্যাকিং কোড</h3>
    <p class="text-xs text-slate-400 mb-4">শুধু আইডি বসান — প্রয়োজনীয় স্ক্রিপ্ট স্বয়ংক্রিয়ভাবে সব ল্যান্ডিং পেজ ও অর্ডার সাকসেস পেজে যুক্ত হয়ে যাবে।</p>
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-brands fa-facebook text-blue-600 text-xs mr-1"></i> Facebook Pixel ID</label>
        <input type="text" name="facebook_pixel_id" value="{{ old('facebook_pixel_id', $setting->facebook_pixel_id) }}" placeholder="যেমনঃ 1234567890123456"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-brands fa-google text-amber-500 text-xs mr-1"></i> Google Analytics ID</label>
        <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $setting->google_analytics_id) }}" placeholder="যেমনঃ G-XXXXXXXXXX"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors">
      সেভ করুন
    </button>
  </div>
</form>
@endsection

@push('scripts')
<script>
  var tabButtons = document.querySelectorAll('[data-tab-btn]');
  var tabPanels = document.querySelectorAll('[data-tab-panel]');
  var activeTabInput = document.getElementById('activeTabInput');

  tabButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = btn.dataset.tabBtn;

      tabButtons.forEach(function (b) {
        b.classList.toggle('bg-brand', b === btn);
        b.classList.toggle('text-white', b === btn);
        b.classList.toggle('text-slate-500', b !== btn);
        b.classList.toggle('hover:bg-slate-50', b !== btn);
      });

      tabPanels.forEach(function (panel) {
        panel.classList.toggle('hidden', panel.dataset.tabPanel !== target);
      });

      activeTabInput.value = target;
    });
  });
</script>
@endpush
