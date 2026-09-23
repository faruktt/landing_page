@php $isEdit = $orderStatus->exists; @endphp

@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ $isEdit ? route('admin.order-statuses.update', $orderStatus) : route('admin.order-statuses.store') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 max-w-xl space-y-5">
  @csrf
  @if ($isEdit) @method('PUT') @endif

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">স্ট্যাটাসের নাম (লেবেল) *</label>
    <input type="text" name="label" value="{{ old('label', $orderStatus->label) }}" required placeholder="যেমনঃ প্যাকিং হচ্ছে"
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">
      কী (Key) *
      @if ($isEdit)
        <span class="text-xs text-slate-400 font-normal">— বিদ্যমান অর্ডারের সাথে যুক্ত থাকায় পরিবর্তন করা যাবে না</span>
      @endif
    </label>
    @if ($isEdit)
      <input type="text" value="{{ $orderStatus->key }}" disabled
             class="w-full border border-slate-200 bg-slate-50 text-slate-400 rounded-lg px-4 py-2.5 text-sm font-mono">
      <input type="hidden" name="key" value="{{ $orderStatus->key }}">
    @else
      <input type="text" name="key" value="{{ old('key') }}" required placeholder="যেমনঃ packing" pattern="[a-z0-9_]+"
             class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      <p class="text-xs text-slate-400 mt-1">শুধু ছোট হাতের ইংরেজি অক্ষর, সংখ্যা ও আন্ডারস্কোর (_) ব্যবহার করুন।</p>
    @endif
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-2">রঙ *</label>
    <div class="flex flex-wrap gap-3">
      @foreach (\App\Models\OrderStatus::COLORS as $key => $classes)
        <label class="relative cursor-pointer">
          <input type="radio" name="color" value="{{ $key }}" class="peer sr-only"
                 {{ old('color', $orderStatus->color ?: 'slate') === $key ? 'checked' : '' }}>
          <span class="block h-9 w-9 rounded-full border-2 border-transparent peer-checked:border-brand transition-colors {{ $classes }}"></span>
          <i class="fa-solid fa-check text-xs absolute inset-0 flex items-center justify-center opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
        </label>
      @endforeach
    </div>
  </div>

  <div class="flex items-start gap-2.5 pt-1">
    <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $orderStatus->is_default) ? 'checked' : '' }}
           class="mt-0.5 accent-brand rounded">
    <label for="is_default" class="text-sm text-slate-600">
      <span class="font-medium text-slate-800">ডিফল্ট স্ট্যাটাস</span><br>
      <span class="text-xs text-slate-400">নতুন অর্ডার তৈরি হলে স্বয়ংক্রিয়ভাবে এই স্ট্যাটাস বসবে।</span>
    </label>
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors">
      {{ $isEdit ? 'আপডেট করুন' : 'স্ট্যাটাস সেভ করুন' }}
    </button>
    <a href="{{ route('admin.order-statuses.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">বাতিল করুন</a>
  </div>
</form>
