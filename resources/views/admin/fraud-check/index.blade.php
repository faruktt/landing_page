@extends('layouts.admin')

@section('title', 'কুরিয়ার ফ্রড চেকার (BD Courier)')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <!-- Page Header -->
  <div class="text-center mb-6">
    <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-brand to-brandDark text-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-brand/30">
      <i class="fa-solid fa-shield-halved text-2xl"></i>
    </div>
    <h2 class="font-display text-2xl font-bold text-slate-900">Courier Fraud Checker</h2>
    <p class="text-slate-500 text-sm mt-1">
      BD Courier এর মাধ্যমে Pathao, SteadFast, RedX, PaperFly সহ সব কুরিয়ারের ডেলিভারি হিস্ট্রি ও ফ্রড রিপোর্ট যাচাই করুন।
    </p>
  </div>

  <!-- Search Form -->
  <form method="GET" action="{{ route('admin.fraud-check.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-3 flex items-center gap-2.5">
    <div class="relative flex-1">
      <i class="fa-solid fa-mobile-screen absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
      <input type="text" name="phone" value="{{ $phone }}" required maxlength="11" inputmode="numeric" placeholder="১১ ডিজিটের মোবাইল নম্বর লিখুন (যেমন: 01712345678)"
             class="w-full border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition font-medium">
    </div>
    <button type="submit" class="bg-gradient-to-r from-brand to-brandDark hover:opacity-90 text-white px-6 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 transition-all shadow-md shadow-brand/20 shrink-0">
      <i class="fa-solid fa-magnifying-glass text-sm"></i>
      যাচাই করুন
    </button>
  </form>

  <!-- Error Alert -->
  @if ($error)
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl p-4 flex items-center gap-3">
      <i class="fa-solid fa-triangle-exclamation text-lg shrink-0 text-red-500"></i>
      <div>
        <p class="font-semibold">{{ $error }}</p>
        <p class="text-xs text-red-600 mt-0.5">সঠিক ১১ ডিজিটের বাংলাদেশি মোবাইল নম্বর প্রদান করুন।</p>
      </div>
    </div>
  @endif

  <!-- Result Container -->
  @if ($result)
    @php
      $verdict = $result['verdict'] ?? [];
      $level = $verdict['level'] ?? 'neutral';

      $barGradients = [
          'safe' => 'from-emerald-400 to-emerald-600',
          'medium_risk' => 'from-amber-400 to-amber-600',
          'high_risk' => 'from-rose-500 to-red-700',
          'neutral' => 'from-slate-400 to-slate-600',
      ];
      $ringColors = [
          'safe' => 'ring-emerald-100',
          'medium_risk' => 'ring-amber-100',
          'high_risk' => 'ring-rose-100',
          'neutral' => 'ring-slate-100',
      ];
      $iconBg = [
          'safe' => 'bg-emerald-100 text-emerald-600',
          'medium_risk' => 'bg-amber-100 text-amber-600',
          'high_risk' => 'bg-rose-100 text-rose-600',
          'neutral' => 'bg-slate-100 text-slate-600',
      ];
    @endphp

    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden ring-4 {{ $ringColors[$level] ?? 'ring-slate-100' }}">
      <div class="h-1.5 bg-gradient-to-r {{ $barGradients[$level] ?? 'from-slate-400 to-slate-600' }}"></div>

      <div class="p-6 sm:p-7 space-y-6">

        <!-- Top Header: Phone & Verdict Badge -->
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-3.5">
            <div class="h-12 w-12 rounded-2xl {{ $iconBg[$level] ?? 'bg-slate-100 text-slate-600' }} flex items-center justify-center shrink-0 shadow-xs">
              <i class="fa-solid {{ $verdict['icon'] ?? 'fa-shield-halved' }} text-xl"></i>
            </div>
            <div>
              <p class="text-xs text-slate-400 font-medium">মোবাইল নম্বর</p>
              <div class="flex items-center gap-2">
                <p class="text-xl font-bold text-slate-900 font-mono tracking-wide">{{ $result['phone'] }}</p>
                @php $cleanWa = '880'.ltrim(preg_replace('/\D/', '', $result['phone']), '0'); @endphp
                <a href="tel:{{ $result['phone'] }}" class="text-slate-400 hover:text-brand transition" title="কল করুন">
                  <i class="fas fa-phone text-xs"></i>
                </a>
                <a href="https://wa.me/{{ $cleanWa }}" target="_blank" class="text-emerald-500 hover:text-emerald-600 transition" title="হোয়াটসঅ্যাপ">
                  <i class="fab fa-whatsapp text-sm"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ $verdict['badge_color'] ?? 'bg-slate-100 text-slate-700' }} flex items-center gap-1.5 shadow-xs">
              <i class="fa-solid {{ $verdict['icon'] ?? 'fa-info' }}"></i>
              {{ $verdict['badge'] ?? 'যাচাইকৃত' }}
            </span>
            <a href="{{ route('admin.fraud-check.index', ['phone' => $result['phone'], 'fresh' => 1]) }}"
               class="p-2 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition" title="ক্যাশে বাদ দিয়ে নতুন করে চেক করুন">
              <i class="fa-solid fa-arrows-rotate text-xs"></i>
            </a>
          </div>
        </div>

        <!-- Action / Advice Notice -->
        <div class="p-4 rounded-xl border bg-slate-50 border-slate-200 flex items-start gap-3 text-slate-800">
          <i class="fa-solid fa-circle-info text-lg mt-0.5 text-slate-500 shrink-0"></i>
          <div class="flex-1">
            <div class="flex items-center justify-between gap-2">
              <p class="text-sm font-bold">{{ $verdict['action'] ?? 'পরামর্শ' }}</p>
              <span class="text-[11px] text-slate-400">চেক: {{ $result['checked_at'] }}</span>
            </div>
            @if (!empty($verdict['reasons']))
              <ul class="mt-1 text-xs text-slate-600 list-disc list-inside space-y-0.5">
                @foreach ($verdict['reasons'] as $reason)
                  <li>{{ $reason }}</li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>

        <!-- Overall Metrics (4 cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
            <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-600 mx-auto flex items-center justify-center mb-1.5">
              <i class="fa-solid fa-boxes-stacked text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-slate-800 font-mono">{{ $result['total_parcel'] }}</p>
            <p class="text-xs font-medium text-slate-500 mt-0.5">মোট পার্সেল</p>
          </div>

          <div class="bg-emerald-50/70 rounded-xl p-4 text-center border border-emerald-100">
            <div class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-1.5">
              <i class="fa-solid fa-circle-check text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-700 font-mono">{{ $result['success_parcel'] }}</p>
            <p class="text-xs font-medium text-emerald-600 mt-0.5">সফল ডেলিভারি</p>
          </div>

          <div class="bg-rose-50/70 rounded-xl p-4 text-center border border-rose-100">
            <div class="h-8 w-8 rounded-lg bg-rose-100 text-rose-600 mx-auto flex items-center justify-center mb-1.5">
              <i class="fa-solid fa-circle-xmark text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-rose-700 font-mono">{{ $result['cancelled_parcel'] }}</p>
            <p class="text-xs font-medium text-rose-600 mt-0.5">বাতিল / ফেরত</p>
          </div>

          <div class="bg-indigo-50/70 rounded-xl p-4 text-center border border-indigo-100">
            <div class="h-8 w-8 rounded-lg bg-indigo-100 text-indigo-600 mx-auto flex items-center justify-center mb-1.5">
              <i class="fa-solid fa-chart-pie text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-700 font-mono">{{ $result['success_ratio'] }}%</p>
            <p class="text-xs font-medium text-indigo-600 mt-0.5">সফলতার হার</p>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100">
          <div class="flex items-center justify-between text-xs text-slate-600 mb-1.5 font-medium">
            <span>ডেলিভারি সফলতার অনুপাত</span>
            <span class="font-bold text-slate-800">{{ $result['success_ratio'] }}% সফলতা</span>
          </div>
          @php
            $successWidth = $result['total_parcel'] > 0 ? ($result['success_parcel'] / $result['total_parcel']) * 100 : 0;
            $cancelWidth = $result['total_parcel'] > 0 ? ($result['cancelled_parcel'] / $result['total_parcel']) * 100 : 0;
          @endphp
          <div class="h-3 bg-slate-200 rounded-full overflow-hidden flex">
            <div class="bg-emerald-500 h-full transition-all" style="width: {{ $successWidth }}%"></div>
            <div class="bg-rose-400 h-full transition-all" style="width: {{ $cancelWidth }}%"></div>
          </div>
        </div>

        <!-- Fraud Reports / Complaints -->
        @if (!empty($result['reports']))
          <div class="border-t border-slate-100 pt-5">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                মার্চেন্ট ফ্রড ও অভিযোগ হিস্ট্রি
              </h3>
              <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">
                {{ count($result['reports']) }}টি রিপোর্ট
              </span>
            </div>

            <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
              @foreach ($result['reports'] as $report)
                <div class="p-3.5 rounded-xl bg-rose-50/60 border border-rose-100 text-left">
                  <div class="flex items-center justify-between text-xs mb-1">
                    <div class="flex items-center gap-2">
                      <span class="font-bold text-rose-900">{{ $report['name'] }}</span>
                      @if (!empty($report['courier_name']))
                        <span class="px-1.5 py-0.5 rounded bg-white text-[10px] font-medium text-slate-600 border border-rose-200">
                          {{ $report['courier_name'] }}
                        </span>
                      @endif
                    </div>
                    <span class="text-[11px] text-slate-400">{{ $report['created_at'] }}</span>
                  </div>
                  <p class="text-xs text-rose-800 leading-relaxed font-sans">{{ $report['details'] }}</p>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- Couriers Breakdown (Pathao, SteadFast, RedX, PaperFly, CarryBee, etc.) -->
        <div class="border-t border-slate-100 pt-5">
          <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-truck text-slate-500"></i>
            কুরিয়ারভিত্তিক পার্সেল ও ডেলিভারি হিসেব
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($result['couriers'] as $courier)
              @php
                $hasParcels = $courier['total_parcel'] > 0;
                $ratioBadge = 'bg-slate-100 text-slate-500';
                if ($hasParcels) {
                    if ($courier['success_ratio'] >= 80) $ratioBadge = 'bg-emerald-100 text-emerald-700';
                    elseif ($courier['success_ratio'] >= 50) $ratioBadge = 'bg-amber-100 text-amber-700';
                    else $ratioBadge = 'bg-rose-100 text-rose-700';
                }
              @endphp
              <div class="p-3 rounded-xl border transition-all {{ $hasParcels ? 'bg-white border-slate-200 shadow-xs' : 'bg-slate-50/60 border-slate-100 opacity-60' }}">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    @if (!empty($courier['logo']))
                      <img src="{{ $courier['logo'] }}" alt="{{ $courier['name'] }}" class="h-6 w-6 object-contain rounded-md">
                    @else
                      <div class="h-6 w-6 bg-slate-200 rounded flex items-center justify-center text-[10px] font-bold text-slate-600">
                        {{ strtoupper(substr($courier['name'], 0, 2)) }}
                      </div>
                    @endif
                    <span class="text-xs font-bold text-slate-800">{{ $courier['name'] }}</span>
                  </div>
                  <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $ratioBadge }}">
                    {{ $hasParcels ? $courier['success_ratio'].'%' : '০' }}
                  </span>
                </div>
                <div class="grid grid-cols-3 text-center text-[11px] gap-1 pt-1.5 border-t border-slate-100">
                  <div>
                    <p class="text-[10px] text-slate-400">মোট</p>
                    <p class="font-bold text-slate-700">{{ $courier['total_parcel'] }}</p>
                  </div>
                  <div>
                    <p class="text-[10px] text-emerald-500">সফল</p>
                    <p class="font-bold text-emerald-700">{{ $courier['success_parcel'] }}</p>
                  </div>
                  <div>
                    <p class="text-[10px] text-rose-400">বাতিল</p>
                    <p class="font-bold text-rose-700">{{ $courier['cancelled_parcel'] }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

      </div>
    </div>
  @endif

</div>
@endsection
