@if ($product->relatedProducts->count())
<div class="md:col-span-2">
  <h3 class="font-semibold text-lg mb-3">আরও যুক্ত করতে চান?</h3>
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    @foreach ($product->relatedProducts as $addon)
      @php $addonPrice = (float) ($addon->sale_price ?? $addon->regular_price); @endphp
      <label class="has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 flex items-center gap-2 border-2 border-gray-200 rounded-xl p-2.5 cursor-pointer transition-colors">
        <input type="checkbox" name="extra_products[]" value="{{ $addon->id }}"
               data-addon-price="{{ $addonPrice }}"
               onchange="toggleAddonRow(this)" class="accent-emerald-500 shrink-0">
        @if ($addon->image)
          <img src="{{ $addon->image_url ?: asset($addon->image) }}" class="h-10 w-10 rounded-lg object-cover shrink-0">
        @endif
        <div class="min-w-0 flex-1">
          <p class="text-xs font-medium text-gray-800 line-clamp-2">{{ $addon->name }}</p>
          <p class="text-xs font-semibold text-emerald-600">+৳{{ number_format($addonPrice, 0) }}</p>
        </div>
      </label>
    @endforeach
  </div>
</div>
@endif
