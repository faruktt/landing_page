@extends('layouts.admin')

@section('title', 'নতুন প্রোডাক্ট')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">নতুন প্রোডাক্ট যোগ করুন</h2>
  <p class="text-slate-500 text-sm mt-1">টেমপ্লেট বাছাই করুন, এরপর প্রোডাক্টের তথ্য পূরণ করুন।</p>
</div>

@include('admin.products._form')
@endsection
