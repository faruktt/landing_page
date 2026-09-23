@extends('layouts.admin')

@section('title', 'ক্যাটাগরি এডিট')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">ক্যাটাগরি এডিট করুন</h2>
  <p class="text-slate-500 text-sm mt-1">{{ $category->name }}</p>
</div>

@include('admin.categories._form')
@endsection
