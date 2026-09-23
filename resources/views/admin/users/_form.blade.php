@php $isEdit = $user->exists; @endphp

@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 max-w-xl space-y-5">
  @csrf
  @if ($isEdit) @method('PUT') @endif

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">নাম *</label>
    <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="ইউজারের নাম লিখুন"
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">ইমেইল *</label>
    <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="user@example.com"
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">
      পাসওয়ার্ড {{ $isEdit ? '' : '*' }}
      @if ($isEdit)
        <span class="text-xs text-slate-400 font-normal">— খালি রাখলে আগের পাসওয়ার্ড অপরিবর্তিত থাকবে</span>
      @endif
    </label>
    <input type="password" name="password" {{ $isEdit ? '' : 'required' }} minlength="6" placeholder="{{ $isEdit ? 'নতুন পাসওয়ার্ড (ঐচ্ছিক)' : 'কমপক্ষে ৬ ক্যারেক্টার' }}"
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-2">রোল *</label>
    <div class="grid sm:grid-cols-3 gap-3">
      @foreach (\App\Models\User::ROLES as $key => $label)
        <label class="relative flex items-center justify-center border-2 rounded-lg px-4 py-3 cursor-pointer transition-colors has-[:checked]:border-brand has-[:checked]:bg-brand/5 border-slate-200">
          <input type="radio" name="role" value="{{ $key }}" class="peer sr-only" {{ old('role', $user->role ?: 'staff') === $key ? 'checked' : '' }}
                 {{ $isEdit && $user->id === auth()->id() ? 'disabled' : '' }}>
          <span class="text-sm font-medium text-slate-700 peer-checked:text-brand">{{ $label }}</span>
        </label>
      @endforeach
    </div>
    @if ($isEdit && $user->id === auth()->id())
      <input type="hidden" name="role" value="{{ $user->role }}">
      <p class="text-xs text-slate-400 mt-1.5">নিজের রোল নিজে পরিবর্তন করা যাবে না।</p>
    @endif
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors">
      {{ $isEdit ? 'আপডেট করুন' : 'ইউজার তৈরি করুন' }}
    </button>
    <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">বাতিল করুন</a>
  </div>
</form>
