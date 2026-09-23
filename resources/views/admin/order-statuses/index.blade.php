@extends('layouts.admin')

@section('title', 'অর্ডার স্ট্যাটাস')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">অর্ডার স্ট্যাটাস</h2>
    <p class="text-slate-500 text-sm mt-1">অর্ডারের স্ট্যাটাস তৈরি, এডিট ও মুছে ফেলুন।</p>
  </div>
  <a href="{{ route('admin.order-statuses.create') }}" class="bg-brand hover:bg-brandDark text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors inline-flex items-center gap-2">
    <i class="fa-solid fa-plus text-sm"></i>
    নতুন স্ট্যাটাস
  </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">স্ট্যাটাস</th>
        <th class="text-left font-medium px-5 py-3">কী (Key)</th>
        <th class="text-center font-medium px-5 py-3">অর্ডার সংখ্যা</th>
        <th class="text-center font-medium px-5 py-3">ডিফল্ট</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($orderStatuses as $orderStatus)
        <tr>
          <td class="px-5 py-3">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $orderStatus->badge_class }}">{{ $orderStatus->label }}</span>
          </td>
          <td class="px-5 py-3 text-slate-500 font-mono text-xs">{{ $orderStatus->key }}</td>
          <td class="px-5 py-3 text-center text-slate-600">{{ $orderStatus->orders_count }}</td>
          <td class="px-5 py-3 text-center">
            @if ($orderStatus->is_default)
              <i class="fa-solid fa-circle-check text-emerald-500"></i>
            @else
              <span class="text-slate-300">-</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right">
            <div class="inline-flex items-center gap-1.5">
              <a href="{{ route('admin.order-statuses.edit', $orderStatus) }}" title="এডিট করুন"
                 class="inline-flex text-amber-600 hover:bg-amber-50 w-8 h-8 items-center justify-center rounded-lg border border-amber-200 transition-colors">
                <i class="fa-solid fa-pen text-sm"></i>
              </a>
              <form method="POST" action="{{ route('admin.order-statuses.destroy', $orderStatus) }}" class="js-confirm-delete" data-confirm-text="'{{ $orderStatus->label }}' স্ট্যাটাসটি স্থায়ীভাবে মুছে যাবে।">
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
          <td colspan="5" class="px-5 py-10 text-center text-slate-400">
            এখনো কোনো স্ট্যাটাস যোগ করা হয়নি। <a href="{{ route('admin.order-statuses.create') }}" class="text-brand font-medium hover:underline">প্রথম স্ট্যাটাস যোগ করুন</a>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
