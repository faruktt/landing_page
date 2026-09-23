@php
  $blockSettings = \App\Models\Setting::current();
  $waNumber = preg_replace('/\D/', '', $blockSettings->whatsapp ?: $blockSettings->phone);
  $waNumber = $waNumber ? '880'.ltrim($waNumber, '0') : null;
  $waMessage = rawurlencode('আমি '.$product->name.' অর্ডার করতে চাই।');
@endphp
<div class="text-center py-8 sm:py-10 px-4">
  <div class="h-14 w-14 sm:h-16 sm:w-16 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center mx-auto mb-3 sm:mb-4">
    <i class="fa-solid fa-ban text-2xl sm:text-3xl"></i>
  </div>
  <h3 class="text-base sm:text-lg font-bold text-rose-700 mb-1.5 sm:mb-2">এই নম্বরটি অ্যাডমিন কর্তৃক ব্লক করা হয়েছে</h3>
  <p class="text-xs sm:text-sm text-gray-600 mb-5 max-w-sm mx-auto">অর্ডারের জন্য সরাসরি আমাদের হোয়াটসঅ্যাপে যোগাযোগ করুন, আমরা সাহায্য করবো।</p>
  @if ($waNumber)
    <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" target="_blank"
       class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold px-6 py-2.5 sm:py-3 rounded-full shadow-md transition-colors text-xs sm:text-sm">
      <i class="fa-brands fa-whatsapp text-lg sm:text-xl"></i>
      হোয়াটসঅ্যাপে কথা বলুন
    </a>
  @endif
</div>
