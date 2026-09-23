@php $deliverySettings = \App\Models\Setting::current(); @endphp
<div class="grid grid-cols-2 gap-2">
  <label class="has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 flex items-center justify-between gap-2 border-2 border-gray-200 rounded-xl px-3 py-2.5 cursor-pointer transition-colors">
    <span class="flex items-center gap-2">
      <input type="radio" name="district" value="ঢাকার বাইরে" onchange="updateTotal()" class="h-4 w-4 accent-emerald-500 shrink-0">
      <span class="text-sm font-medium text-gray-800">ঢাকার বাইরে</span>
    </span>
    <span class="text-sm font-semibold text-emerald-600 whitespace-nowrap">৳{{ number_format((float) $deliverySettings->default_delivery_charge, 0) }}</span>
  </label>
  <label class="has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 flex items-center justify-between gap-2 border-2 border-gray-200 rounded-xl px-3 py-2.5 cursor-pointer transition-colors">
    <span class="flex items-center gap-2">
      <input type="radio" name="district" value="ঢাকা" checked onchange="updateTotal()" class="h-4 w-4 accent-emerald-500 shrink-0">
      <span class="text-sm font-medium text-gray-800">ঢাকার ভিতরে</span>
    </span>
    <span class="text-sm font-semibold text-emerald-600 whitespace-nowrap">৳{{ number_format((float) $deliverySettings->dhaka_delivery_charge, 0) }}</span>
  </label>
</div>
