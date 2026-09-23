@extends('layouts.admin')

@section('title', 'ক্যাটাগরি সমূহ')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">ক্যাটাগরি সমূহ</h2>
    <p class="text-slate-500 text-sm mt-1">প্রোডাক্ট ক্যাটাগরি ম্যানেজ করুন।</p>
  </div>
  <a href="{{ route('admin.categories.create') }}" class="bg-brand hover:bg-brandDark text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors inline-flex items-center gap-2">
    <i class="fa-solid fa-plus text-sm"></i>
    নতুন ক্যাটাগরি
  </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">ক্যাটাগরি</th>
        <th class="text-left font-medium px-5 py-3">প্রোডাক্ট সংখ্যা</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($categories as $category)
        <tr>
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              @if ($category->image)
                <img src="{{ asset('storage/'.$category->image) }}" class="h-10 w-10 rounded-lg object-cover border border-slate-200">
              @else
                <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300">
                  <i class="fa-solid fa-image text-lg"></i>
                </div>
              @endif
              <div>
                <p class="font-medium text-slate-800">{{ $category->name }}</p>
                <p class="text-xs text-slate-400">/{{ $category->slug }}</p>
              </div>
            </div>
          </td>
          <td class="px-5 py-3 text-slate-500">{{ $category->products_count }}</td>
          <td class="px-5 py-3 text-right">
            <div class="inline-flex items-center gap-1.5">
              <a href="{{ route('admin.categories.edit', $category) }}" title="এডিট করুন"
                 class="inline-flex text-amber-600 hover:bg-amber-50 w-8 h-8 items-center justify-center rounded-lg border border-amber-200 transition-colors">
                <i class="fa-solid fa-pen text-sm"></i>
              </a>
              <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="js-confirm-delete" data-confirm-text="'{{ $category->name }}' ক্যাটাগরিটি স্থায়ীভাবে মুছে যাবে।">
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
          <td colspan="3" class="px-5 py-10 text-center text-slate-400">
            এখনো কোনো ক্যাটাগরি যোগ করা হয়নি। <a href="{{ route('admin.categories.create') }}" class="text-brand font-medium hover:underline">প্রথম ক্যাটাগরি যোগ করুন</a>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if ($categories->hasPages())
  <div class="mt-5">{{ $categories->links() }}</div>
@endif
@endsection
