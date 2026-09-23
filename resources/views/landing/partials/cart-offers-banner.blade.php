@if ($product->cartOffers->count())
<div class="mb-5 bg-emerald-50 border border-emerald-200 rounded-xl p-4">
  <p class="text-sm font-semibold text-emerald-700 mb-2">🎁 কার্ট অফার — যত বেশি কার্ট করবেন, তত বেশি বাঁচাবেন!</p>
  <div class="space-y-1.5">
    @foreach ($product->cartOffers as $offer)
      <div class="cart-offer-tier flex items-center gap-2 text-sm" data-offer-min="{{ (float) $offer->min_cart_amount }}">
        <span class="offer-tier-icon h-4 w-4 rounded-full border-2 border-emerald-300 flex items-center justify-center shrink-0 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="offer-tier-check h-2.5 w-2.5 text-white hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
        </span>
        <span class="text-gray-600">
          ৳{{ number_format((float) $offer->min_cart_amount, 0) }}+ কার্টে —
          @if ($offer->reward_type === 'free_delivery')
            <span class="font-medium text-emerald-700">ফ্রি ডেলিভারি</span>
          @else
            <span class="font-medium text-emerald-700">৳{{ number_format((float) $offer->discount_amount, 0) }} ছাড়</span>
          @endif
        </span>
      </div>
    @endforeach
  </div>
  <p id="offerProgressText" class="text-xs text-emerald-600 mt-2 font-medium"></p>
</div>
@endif
