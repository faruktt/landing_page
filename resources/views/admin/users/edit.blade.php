@extends('layouts.admin')

@section('title', 'ইউজার এডিট')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">ইউজার এডিট করুন</h2>
</div>

@include('admin.users._form')
@endsection
