@foreach ($product->relatedProducts as $addon)
  @php $addonPrice = (float) ($addon->sale_price ?? $addon->regular_price); @endphp
  <tr id="addon-order-row-{{ $addon->id }}" class="hidden border-t border-gray-200">
    <td class="px-4 py-3">
      <div class="flex items-center gap-2">
        @if ($addon->image)
          <img src="{{ $addon->image_url ?: asset($addon->image) }}" class="h-10 w-10 rounded-md object-cover">
        @endif
        <div>
          <span class="block">{{ $addon->name }}</span>
          <div class="flex items-center border border-gray-200 rounded-lg w-fit mt-1">
            <button type="button" onclick="changeAddonQty({{ $addon->id }}, -1)" class="px-2 py-0.5 text-gray-500 hover:text-brand text-xs">-</button>
            <span id="addon-qty-label-{{ $addon->id }}" class="px-2 text-xs">1</span>
            <button type="button" onclick="changeAddonQty({{ $addon->id }}, 1)" class="px-2 py-0.5 text-gray-500 hover:text-brand text-xs">+</button>
          </div>
        </div>
      </div>
    </td>
    <td class="px-4 py-3 text-right">
      ৳<span id="addon-order-subtotal-{{ $addon->id }}">{{ number_format($addonPrice, 2, '.', '') }}</span>
      <input type="hidden" name="extra_quantities[{{ $addon->id }}]" id="extra-qty-input-{{ $addon->id }}" value="1">
    </td>
  </tr>
@endforeach
