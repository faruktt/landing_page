@extends('layouts.admin')

@section('title', 'প্রোফাইল সেটিংস')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">প্রোফাইল সেটিংস</h2>
  <p class="text-slate-500 text-sm mt-1">আপনার নিজের নাম, ইমেইল ও পাসওয়ার্ড পরিবর্তন করুন।</p>
</div>

@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600 max-w-xl">
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.profile.update') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 max-w-xl space-y-5">
  @csrf
  @method('PUT')

  <div class="flex items-center gap-3 pb-2">
    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-brand to-violet-600 flex items-center justify-center text-white font-bold shrink-0">
      {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
    <div>
      <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
      <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</span>
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">নাম *</label>
    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">ইমেইল *</label>
    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">
      নতুন পাসওয়ার্ড
      <span class="text-xs text-slate-400 font-normal">— খালি রাখলে আগের পাসওয়ার্ড অপরিবর্তিত থাকবে</span>
    </label>
    <input type="password" name="password" minlength="6" placeholder="কমপক্ষে ৬ ক্যারেক্টার"
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <button type="submit" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors">
    আপডেট করুন
  </button>
</form>
@endsection
