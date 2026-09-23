@php $phoneHintSettings = \App\Models\Setting::current(); @endphp
<div id="phoneFormatHint" class="hidden relative bg-emerald-600 text-white text-xs rounded-lg px-3 py-2 mb-2.5 inline-block">
  উদাহরণ: {{ $phoneHintSettings->phone ?: '01712345678' }} (১১ টি ডিজিট, ০১৩-০১৯ দিয়ে শুরু)
  <div class="absolute left-6 -bottom-1 w-2.5 h-2.5 bg-emerald-600 rotate-45"></div>
</div>
