@extends('layouts.admin')

@section('title', 'প্রোডাক্ট এডিট')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">প্রোডাক্ট এডিট করুন</h2>
  <p class="text-slate-500 text-sm mt-1">{{ $product->name }}</p>
</div>

@include('admin.products._form')
@endsection
