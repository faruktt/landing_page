@extends('layouts.admin')

@section('title', 'কাস্টমার এডিট - '.$customer->name)

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">কাস্টমার এডিট করুন</h2>
    <p class="text-slate-500 text-sm mt-1">{{ $customer->name }}</p>
  </div>
  <a href="{{ route('admin.customers.show', $customer) }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">&larr; ফিরে যান</a>
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

<form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 max-w-xl space-y-5">
  @csrf
  @method('PUT')

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">নাম *</label>
    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">ফোন *</label>
    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">ঠিকানা</label>
    <textarea name="address" rows="2"
              class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">{{ old('address', $customer->address) }}</textarea>
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">জেলা</label>
    <select name="district" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      @include('admin.orders.partials.district-select', ['selected' => old('district', $customer->district)])
    </select>
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors">
      আপডেট করুন
    </button>
    <a href="{{ route('admin.customers.show', $customer) }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">বাতিল করুন</a>
  </div>
</form>
@endsection
