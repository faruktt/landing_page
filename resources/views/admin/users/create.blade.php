@extends('layouts.admin')

@section('title', 'নতুন ইউজার')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">নতুন ইউজার যোগ করুন</h2>
</div>

@include('admin.users._form')
@endsection
