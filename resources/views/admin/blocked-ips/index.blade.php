@extends('layouts.admin')

@section('title', 'ব্লকড আইপি')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">ব্লকড আইপি</h2>
    <p class="text-slate-500 text-sm mt-1">প্রতিটি আইপি ব্লক ৩০ মিনিট পর নিজে থেকেই আনব্লক হয়ে যায়।</p>
  </div>
  <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">&larr; অর্ডার সমূহ</a>
</div>

<div class="grid lg:grid-cols-2 gap-5 items-start">

<!-- IP-wise order overview -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100">
    <h3 class="font-semibold text-slate-900">আইপি অনুযায়ী অর্ডার তথ্য</h3>
    <p class="text-slate-400 text-xs mt-0.5">কোন আইপি থেকে কতগুলো অর্ডার ও কয়টি ভিন্ন ফোন নাম্বার ব্যবহার হয়েছে দেখুন, সন্দেহজনক হলে ম্যানুয়ালি ব্লক করুন।</p>
  </div>
  <div class="overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">আইপি অ্যাড্রেস</th>
        <th class="text-center font-medium px-5 py-3">অর্ডার সংখ্যা</th>
        <th class="text-left font-medium px-5 py-3">ব্যবহৃত ফোন নাম্বার</th>
        <th class="text-left font-medium px-5 py-3">সর্বশেষ অর্ডার</th>
        <th class="text-center font-medium px-5 py-3">স্ট্যাটাস</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($ipOverview as $row)
        <tr>
          <td class="px-5 py-3 font-mono text-slate-800">{{ $row->ip_address }}</td>
          <td class="px-5 py-3 text-center">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $row->orders_count > 1 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">{{ $row->orders_count }}</span>
          </td>
          <td class="px-5 py-3 text-slate-600">
            <div class="flex flex-wrap gap-1 max-w-xs">
              @foreach ($row->phones as $phone)
                <span class="text-[11px] bg-slate-50 border border-slate-200 rounded px-1.5 py-0.5">{{ $phone }}</span>
              @endforeach
            </div>
            @if ($row->phones->count() > 1)
              <p class="text-[10px] text-amber-600 mt-1"><i class="fa-solid fa-triangle-exclamation"></i> একাধিক ফোন নাম্বার ব্যবহৃত হয়েছে</p>
            @endif
          </td>
          <td class="px-5 py-3 text-slate-500 text-xs">
            {{ $row->last_order_at->format('d M Y, h:i A') }}<br>
            <span class="text-slate-400">{{ $row->last_customer_name }}</span>
          </td>
          <td class="px-5 py-3 text-center">
            @if ($row->blocked_ip)
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-600">ব্লকড</span>
            @else
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">সচল</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right">
            @if ($row->blocked_ip)
              <form method="POST" action="{{ route('admin.blocked-ips.destroy', $row->blocked_ip) }}" class="js-confirm-delete inline-block" data-confirm-text="'{{ $row->ip_address }}' এখনই আনব্লক হয়ে যাবে।">
                @csrf
                @method('DELETE')
                <button type="submit" title="এখনই আনব্লক করুন"
                        class="inline-flex text-emerald-600 hover:bg-emerald-50 w-8 h-8 items-center justify-center rounded-lg border border-emerald-200 transition-colors">
                  <i class="fa-solid fa-unlock text-sm"></i>
                </button>
              </form>
            @else
              <form method="POST" action="{{ route('admin.blocked-ips.store') }}" class="js-block-ip inline-block" data-ip="{{ $row->ip_address }}">
                @csrf
                <input type="hidden" name="ip_address" value="{{ $row->ip_address }}">
                <input type="hidden" name="reason" value="সন্দেহজনক অর্ডার প্যাটার্ন (ম্যানুয়াল)">
                <button type="submit" title="৩০ মিনিটের জন্য ব্লক করুন"
                        class="inline-flex text-red-500 hover:bg-red-50 w-8 h-8 items-center justify-center rounded-lg border border-red-200 transition-colors">
                  <i class="fa-solid fa-ban text-sm"></i>
                </button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="px-5 py-10 text-center text-slate-400">এখনো কোনো অর্ডার আসেনি।</td>
        </tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>

<div>
  <p class="text-sm font-semibold text-slate-700 mb-2">আইপি দিয়ে ম্যানুয়ালি ব্লক করুন</p>
  <form method="POST" action="{{ route('admin.blocked-ips.store') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4 flex flex-wrap gap-2.5 items-center">
    @csrf
    <input type="text" name="ip_address" required placeholder="আইপি অ্যাড্রেস (যেমন: 103.10.20.30)"
           class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand w-56">
    <input type="text" name="reason" placeholder="কারণ (ঐচ্ছিক)"
           class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand w-64">
    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1.5 transition-colors">
      <i class="fa-solid fa-ban text-sm"></i>
      ৩০ মিনিটের জন্য ব্লক করুন
    </button>
  </form>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">আইপি অ্যাড্রেস</th>
        <th class="text-left font-medium px-5 py-3">কারণ</th>
        <th class="text-center font-medium px-5 py-3">স্ট্যাটাস</th>
        <th class="text-left font-medium px-5 py-3">মেয়াদ শেষ</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($blockedIps as $blockedIp)
        @php $active = $blockedIp->expires_at->isFuture(); @endphp
        <tr>
          <td class="px-5 py-3 font-mono text-slate-800">{{ $blockedIp->ip_address }}</td>
          <td class="px-5 py-3 text-slate-500">{{ $blockedIp->reason ?: '-' }}</td>
          <td class="px-5 py-3 text-center">
            @if ($active)
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-600">সক্রিয়</span>
            @else
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">মেয়াদ শেষ</span>
            @endif
          </td>
          <td class="px-5 py-3 text-slate-500 text-xs">{{ $blockedIp->expires_at->format('d M Y, h:i A') }}</td>
          <td class="px-5 py-3 text-right">
            @if ($active)
              <form method="POST" action="{{ route('admin.blocked-ips.destroy', $blockedIp) }}" class="js-confirm-delete inline-block" data-confirm-text="'{{ $blockedIp->ip_address }}' এখনই আনব্লক হয়ে যাবে।">
                @csrf
                @method('DELETE')
                <button type="submit" title="এখনই আনব্লক করুন"
                        class="inline-flex text-emerald-600 hover:bg-emerald-50 w-8 h-8 items-center justify-center rounded-lg border border-emerald-200 transition-colors">
                  <i class="fa-solid fa-unlock text-sm"></i>
                </button>
              </form>
            @else
              <span class="text-slate-300">-</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-5 py-10 text-center text-slate-400">কোনো আইপি ব্লক করা হয়নি।</td>
        </tr>
      @endforelse
    </tbody>
  </table>
  </div>
  @if ($blockedIps->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">{{ $blockedIps->links() }}</div>
  @endif
  </div>
</div>

</div>
@endsection

@push('scripts')
@include('admin.partials.block-scripts')
@endpush
