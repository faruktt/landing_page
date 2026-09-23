@php
  $phoneNoticeSettings = \App\Models\Setting::current();
  $phoneNoticeWaNumber = preg_replace('/\D/', '', $phoneNoticeSettings->whatsapp ?: $phoneNoticeSettings->phone);
  $phoneNoticeWaNumber = $phoneNoticeWaNumber ? '880'.ltrim($phoneNoticeWaNumber, '0') : null;
  $phoneNoticeWaMessage = rawurlencode('আমি '.$product->name.' অর্ডার করতে চাই।');
@endphp
<div id="phoneBlockedNotice" class="hidden mt-3 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-center space-y-2">
  <div class="flex items-center justify-center gap-2 text-rose-700">
    <i class="fa-solid fa-ban text-sm"></i>
    <p class="text-xs sm:text-sm font-bold">এই নম্বরটি অ্যাডমিন কর্তৃক ব্লক করা হয়েছে</p>
  </div>
  <p class="text-xs text-rose-600">অর্ডারের জন্য সরাসরি আমাদের হোয়াটসঅ্যাপে কথা বলুন।</p>
  @if ($phoneNoticeWaNumber)
    <a href="https://wa.me/{{ $phoneNoticeWaNumber }}?text={{ $phoneNoticeWaMessage }}" target="_blank"
       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold rounded-lg shadow-sm transition text-xs sm:text-sm">
      <i class="fa-brands fa-whatsapp text-base"></i>
      <span>হোয়াটসঅ্যাপে কথা বলুন</span>
    </a>
  @endif
</div>
