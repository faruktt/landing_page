@extends('layouts.admin')

@section('title', 'ইউজার সমূহ')

@php
    $roleBadges = [
        'admin' => 'bg-indigo-100 text-indigo-700',
        'manager' => 'bg-blue-100 text-blue-700',
        'staff' => 'bg-slate-100 text-slate-600',
    ];
@endphp

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">ইউজার সমূহ</h2>
    <p class="text-slate-500 text-sm mt-1">রোল অনুযায়ী অ্যাডমিন প্যানেলের ইউজার ম্যানেজ করুন।</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="bg-brand hover:bg-brandDark text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors inline-flex items-center gap-2">
    <i class="fa-solid fa-plus text-sm"></i>
    নতুন ইউজার
  </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-5 py-3">নাম</th>
        <th class="text-left font-medium px-5 py-3">ইমেইল</th>
        <th class="text-center font-medium px-5 py-3">রোল</th>
        <th class="text-right font-medium px-5 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @forelse ($users as $user)
        <tr>
          <td class="px-5 py-3 font-medium text-slate-800">
            {{ $user->name }}
            @if ($user->id === auth()->id())
              <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 align-middle">আপনি</span>
            @endif
          </td>
          <td class="px-5 py-3 text-slate-500">{{ $user->email }}</td>
          <td class="px-5 py-3 text-center">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $roleBadges[$user->role] ?? 'bg-slate-100 text-slate-600' }}">{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</span>
          </td>
          <td class="px-5 py-3 text-right">
            <div class="inline-flex items-center gap-1.5">
              <a href="{{ route('admin.users.edit', $user) }}" title="এডিট করুন"
                 class="inline-flex text-amber-600 hover:bg-amber-50 w-8 h-8 items-center justify-center rounded-lg border border-amber-200 transition-colors">
                <i class="fa-solid fa-pen text-sm"></i>
              </a>
              @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="js-confirm-delete" data-confirm-text="'{{ $user->name }}' ইউজারটি স্থায়ীভাবে মুছে যাবে।">
                  @csrf
                  @method('DELETE')
                  <button type="submit" title="মুছুন"
                          class="inline-flex text-red-500 hover:bg-red-50 w-8 h-8 items-center justify-center rounded-lg border border-red-200 transition-colors">
                    <i class="fa-solid fa-trash text-sm"></i>
                  </button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="px-5 py-10 text-center text-slate-400">এখনো কোনো ইউজার যোগ করা হয়নি।</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
