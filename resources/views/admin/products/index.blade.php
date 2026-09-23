@extends('layouts.admin')

@section('title', 'প্রোডাক্ট সমূহ')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">প্রোডাক্ট সমূহ</h2>
    <p class="text-slate-500 text-sm mt-1">সব প্রোডাক্ট ও তাদের ল্যান্ডিং পেজ ম্যানেজ করুন।</p>
  </div>
  <a href="{{ route('admin.products.create') }}" class="bg-brand hover:bg-brandDark text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors inline-flex items-center gap-2">
    <i class="fa-solid fa-plus text-sm"></i>
    নতুন প্রোডাক্ট
  </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">প্রোডাক্ট</th>
        <th class="text-left font-medium px-5 py-3">ক্যাটাগরি</th>
        <th class="text-left font-medium px-5 py-3">টেমপ্লেট</th>
        <th class="text-left font-medium px-5 py-3">দাম</th>
        <th class="text-left font-medium px-5 py-3">স্টক</th>
        <th class="text-left font-medium px-5 py-3">স্ট্যাটাস</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($products as $product)
        <tr>
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
                @if ($product->image)
                    <img src="{{ $product->image_url ?: asset($product->image) }}" class="h-10 w-10 rounded-lg object-cover border border-slate-200" alt="{{ $product->name }}">
                @else
                    <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300">
                        <i class="fa-solid fa-image text-lg"></i>
                    </div>
                @endif
              <div class="min-w-0">
                <p class="font-medium text-slate-800 truncate">{{ $product->name }}</p>
                <p class="text-xs text-slate-400 truncate">/{{ $product->slug }}</p>
              </div>
            </div>
          </td>
          <td class="px-5 py-3 text-slate-500">{{ $product->category->name ?? '-' }}</td>
          <td class="px-5 py-3 text-slate-500">টেমপ্লেট {{ $product->template }}</td>
          <td class="px-5 py-3 text-slate-700">
            ৳{{ number_format((float) $product->regular_price, 0) }}
            @if ($product->sale_price)
              <span class="text-emerald-600 font-medium">→ ৳{{ number_format((float) $product->sale_price, 0) }}</span>
            @endif
          </td>
          <td class="px-5 py-3 text-slate-500">{{ $product->stock }}</td>
          <td class="px-5 py-3">
            @if ($product->status === 'active')
              <span class="text-xs font-medium bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full">একটিভ</span>
            @else
              <span class="text-xs font-medium bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full">ড্রাফট</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right">
            <div class="inline-flex items-center gap-1.5">
              @if ($product->status === 'active')
                <a href="{{ route('landing.show', $product->slug) }}" target="_blank" title="ওয়েবসাইটে দেখুন"
                   class="inline-flex text-emerald-600 hover:bg-emerald-50 w-8 h-8 items-center justify-center rounded-lg border border-emerald-200 transition-colors">
                  <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                </a>
              @else
                <span title="ড্রাফট অবস্থায় প্রিভিউ করা যাবে না"
                      class="inline-flex text-slate-300 w-8 h-8 items-center justify-center rounded-lg border border-slate-200 cursor-not-allowed">
                  <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                </span>
              @endif
              <a href="{{ route('admin.products.edit', $product) }}" title="এডিট করুন"
                 class="inline-flex text-amber-600 hover:bg-amber-50 w-8 h-8 items-center justify-center rounded-lg border border-amber-200 transition-colors">
                <i class="fa-solid fa-pen text-sm"></i>
              </a>
              <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="js-confirm-delete" data-confirm-text="'{{ $product->name }}' প্রোডাক্টটি স্থায়ীভাবে মুছে যাবে।">
                @csrf
                @method('DELETE')
                <button type="submit" title="মুছুন"
                        class="inline-flex text-red-500 hover:bg-red-50 w-8 h-8 items-center justify-center rounded-lg border border-red-200 transition-colors">
                  <i class="fa-solid fa-trash text-sm"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="px-5 py-10 text-center text-slate-400">
            এখনো কোনো প্রোডাক্ট যোগ করা হয়নি। <a href="{{ route('admin.products.create') }}" class="text-brand font-medium hover:underline">প্রথম প্রোডাক্ট যোগ করুন</a>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if ($products->hasPages())
  <div class="mt-5">{{ $products->links() }}</div>
@endif
@endsection
