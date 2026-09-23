@extends('layouts.admin')

@section('title', 'নতুন ক্যাটাগরি')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">নতুন ক্যাটাগরি যোগ করুন</h2>
</div>

@include('admin.categories._form')
@endsection
