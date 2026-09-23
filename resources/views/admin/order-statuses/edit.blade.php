@extends('layouts.admin')

@section('title', 'অর্ডার স্ট্যাটাস এডিট')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">অর্ডার স্ট্যাটাস এডিট করুন</h2>
</div>

@include('admin.order-statuses._form')
@endsection
