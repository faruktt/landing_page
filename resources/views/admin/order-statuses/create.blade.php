@extends('layouts.admin')

@section('title', 'নতুন অর্ডার স্ট্যাটাস')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">নতুন অর্ডার স্ট্যাটাস যোগ করুন</h2>
</div>

@include('admin.order-statuses._form')
@endsection
