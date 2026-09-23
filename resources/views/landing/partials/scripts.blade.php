@php
    $unitPrice = (float) ($product->sale_price ?? $product->regular_price);
    $deliverySettings = \App\Models\Setting::current();
    $cartOffersData = $product->cartOffers->map(function ($o) {
        return [
            'min' => (float) $o->min_cart_amount,
            'type' => $o->reward_type,
            'discount' => (float) ($o->discount_amount ?? 0),
        ];
    })->values();
@endphp
<script>
  @if ($product->offer_ends_at)
    var target = new Date('{{ $product->offer_ends_at->toIso8601String() }}');
  @else
    var target = new Date();
    target.setDate(target.getDate() + 3);
  @endif
  function pad(n) { return String(n).padStart(2, '0'); }
  function updateCountdown() {
    var diff = target - new Date();
    if (diff < 0) diff = 0;
    document.getElementById('cd-days').textContent = pad(Math.floor(diff / 86400000));
    document.getElementById('cd-hours').textContent = pad(Math.floor((diff % 86400000) / 3600000));
    document.getElementById('cd-mins').textContent = pad(Math.floor((diff % 3600000) / 60000));
    document.getElementById('cd-secs').textContent = pad(Math.floor((diff % 60000) / 1000));
  }
  updateCountdown();
  setInterval(updateCountdown, 1000);

  document.addEventListener('DOMContentLoaded', updateTotal);

  var unitPrice = {{ $unitPrice }};
  var qty = 1;
  var dhakaLabel = 'ঢাকা';
  var dhakaRate = {{ (float) $deliverySettings->dhaka_delivery_charge }};
  var outsideDhakaRate = {{ (float) $deliverySettings->default_delivery_charge }};

  var addonQty = {};
  var cartOffers = @json($cartOffersData);

  function getBestOffer(cartTotal) {
    var best = null;
    cartOffers.forEach(function (o) {
      if (cartTotal >= o.min && (!best || o.min > best.min)) best = o;
    });
    return best;
  }

  function getNextOffer(cartTotal) {
    var next = null;
    cartOffers.forEach(function (o) {
      if (cartTotal < o.min && (!next || o.min < next.min)) next = o;
    });
    return next;
  }

  function changeQty(delta) {
    qty = Math.max(1, qty + delta);
    updateTotal();
  }

  function toggleAddonRow(checkbox) {
    var id = checkbox.value;
    var productsRow = document.getElementById('addon-products-row-' + id);
    var orderRow = document.getElementById('addon-order-row-' + id);
    if (productsRow) productsRow.classList.toggle('hidden', !checkbox.checked);
    if (orderRow) orderRow.classList.toggle('hidden', !checkbox.checked);
    updateTotal();
  }

  function changeAddonQty(id, delta) {
    var newQty = Math.max(1, (addonQty[id] || 1) + delta);
    addonQty[id] = newQty;

    var label = document.getElementById('addon-qty-label-' + id);
    if (label) label.textContent = newQty;

    var input = document.getElementById('extra-qty-input-' + id);
    if (input) input.value = newQty;

    updateTotal();
  }

  function updateTotal() {
    var districtEl = document.querySelector('input[name="district"]:checked');
    var district = (districtEl && districtEl.value) || dhakaLabel;
    var delivery = district === dhakaLabel ? dhakaRate : outsideDhakaRate;
    var mainSubtotal = unitPrice * qty;
    var addonSubtotal = 0;
    document.querySelectorAll('input[name="extra_products[]"]:checked').forEach(function (cb) {
      var id = cb.value;
      var price = parseFloat(cb.dataset.addonPrice) || 0;
      var addonQuantity = addonQty[id] || 1;
      var lineTotal = price * addonQuantity;
      addonSubtotal += lineTotal;

      var lineSpan = document.getElementById('addon-order-subtotal-' + id);
      if (lineSpan) lineSpan.textContent = lineTotal.toFixed(2);
    });
    var grandSubtotal = mainSubtotal + addonSubtotal;

    var offer = getBestOffer(grandSubtotal);
    var deliveryFree = !!(offer && offer.type === 'free_delivery');
    var offerDiscount = (offer && offer.type === 'discount') ? offer.discount : 0;
    var effectiveDelivery = deliveryFree ? 0 : delivery;
    var total = grandSubtotal + effectiveDelivery - offerDiscount;

    document.getElementById('qtyLabel').textContent = qty;
    document.getElementById('rowSubtotal').textContent = mainSubtotal.toFixed(2);
    document.getElementById('subtotal').textContent = grandSubtotal.toFixed(2);
    document.getElementById('delivery').textContent = delivery.toFixed(2);
    document.getElementById('deliveryStruck').textContent = delivery.toFixed(2);
    document.getElementById('deliveryNormal').classList.toggle('hidden', deliveryFree);
    document.getElementById('deliveryFreeText').classList.toggle('hidden', !deliveryFree);
    document.getElementById('deliveryZone').textContent = '(' + district + ')';

    var offerRow = document.getElementById('offerDiscountRow');
    if (offerRow) {
      offerRow.classList.toggle('hidden', offerDiscount <= 0);
      document.getElementById('offerDiscountAmount').textContent = offerDiscount.toFixed(2);
    }

    document.getElementById('total').textContent = total.toFixed(2);
    document.getElementById('placeOrderTotal').textContent = 'Place Order ৳' + total.toFixed(2);

    document.querySelectorAll('.cart-offer-tier').forEach(function (tier) {
      var min = parseFloat(tier.dataset.offerMin);
      var achieved = grandSubtotal >= min;
      var icon = tier.querySelector('.offer-tier-icon');
      icon.classList.toggle('bg-emerald-500', achieved);
      icon.classList.toggle('border-emerald-500', achieved);
      icon.classList.toggle('border-emerald-300', !achieved);
      tier.querySelector('.offer-tier-check').classList.toggle('hidden', !achieved);
    });

    var progressText = document.getElementById('offerProgressText');
    if (progressText) {
      var next = getNextOffer(grandSubtotal);
      if (next) {
        var remaining = (next.min - grandSubtotal).toFixed(2);
        progressText.textContent = 'আর ৳' + remaining + ' যুক্ত করলেই ' + (next.type === 'free_delivery' ? 'ফ্রি ডেলিভারি' : '৳' + next.discount.toFixed(0) + ' ছাড়') + ' পাবেন!';
      } else if (cartOffers.length) {
        progressText.textContent = 'অভিনন্দন! আপনি সর্বোচ্চ অফারটি পেয়ে গেছেন।';
      }
    }

    var qtyInput = document.getElementById('qtyInput');
    if (qtyInput) qtyInput.value = qty;
  }

  // ---- Phone: BD mobile format validation + live block check ----
  var phoneInput = document.getElementById('phoneInput');
  var phoneLengthError = document.getElementById('phoneLengthError');
  var phoneFormatHint = document.getElementById('phoneFormatHint');
  var phoneBlockedNotice = document.getElementById('phoneBlockedNotice');
  var submitOrderBtn = document.getElementById('submitOrderBtn');
  var phoneCheckTimer = null;
  var phoneIsBlocked = false;
  var PHONE_REGEX = /^01[3-9]\d{8}$/;
  var PHONE_ERROR_TEXT = 'সঠিক মোবাইল নম্বর দিন — ০১৩ থেকে ০১৯ দিয়ে শুরু হবে, মোট ১১ ডিজিট।';

  function sanitizePhoneInput(raw) {
    var digits = raw.replace(/\D/g, '');
    var out = '';
    for (var i = 0; i < digits.length && out.length < 11; i++) {
      var ch = digits[i];
      if (out.length === 0 && ch !== '0') continue;
      if (out.length === 1 && ch !== '1') continue;
      if (out.length === 2 && ch < '3') continue;
      out += ch;
    }
    return out;
  }

  if (phoneInput) {
    phoneInput.addEventListener('focus', function () {
      if (phoneFormatHint) phoneFormatHint.classList.remove('hidden');
    });
    phoneInput.addEventListener('blur', function () {
      if (phoneFormatHint) phoneFormatHint.classList.add('hidden');
    });
  }

  function setPhoneBlockedUi(blocked) {
    phoneIsBlocked = blocked;
    if (phoneBlockedNotice) phoneBlockedNotice.classList.toggle('hidden', !blocked);
    if (submitOrderBtn) {
      submitOrderBtn.classList.toggle('hidden', blocked);
      submitOrderBtn.disabled = blocked;
    }
  }

  function showPhoneCheckingState() {
    if (!phoneLengthError) return;
    phoneLengthError.textContent = 'যাচাই করা হচ্ছে...';
    phoneLengthError.classList.remove('hidden', 'text-red-500');
    phoneLengthError.classList.add('text-slate-400');
  }

  function clearPhoneCheckingState() {
    if (!phoneLengthError) return;
    phoneLengthError.textContent = PHONE_ERROR_TEXT;
    phoneLengthError.classList.add('hidden');
    phoneLengthError.classList.remove('text-slate-400');
    phoneLengthError.classList.add('text-red-500');
  }

  function checkPhoneBlock(phone) {
    showPhoneCheckingState();
    if (submitOrderBtn) submitOrderBtn.disabled = true;

    fetch('{{ route('check-phone') }}?phone=' + encodeURIComponent(phone))
      .then(function (res) { return res.json(); })
      .then(function (data) {
        clearPhoneCheckingState();
        setPhoneBlockedUi(!!data.blocked);
      })
      .catch(function () {
        clearPhoneCheckingState();
        setPhoneBlockedUi(false);
      });
  }

  if (phoneInput) {
    phoneInput.addEventListener('input', function () {
      var digits = sanitizePhoneInput(phoneInput.value);
      phoneInput.value = digits;
      setPhoneBlockedUi(false);
      clearTimeout(phoneCheckTimer);

      if (digits.length === 11) {
        if (PHONE_REGEX.test(digits)) {
          if (phoneLengthError) phoneLengthError.classList.add('hidden');
          phoneCheckTimer = setTimeout(function () { checkPhoneBlock(digits); }, 300);
        } else if (phoneLengthError) {
          phoneLengthError.textContent = PHONE_ERROR_TEXT;
          phoneLengthError.classList.remove('hidden', 'text-slate-400');
          phoneLengthError.classList.add('text-red-500');
        }
      } else if (phoneLengthError) {
        phoneLengthError.classList.toggle('hidden', digits.length === 0);
      }
    });
  }

  var checkoutForm = document.getElementById('checkoutForm');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function (e) {
      var digits = phoneInput ? phoneInput.value.replace(/\D/g, '') : '';
      if (!PHONE_REGEX.test(digits)) {
        e.preventDefault();
        if (phoneLengthError) {
          phoneLengthError.textContent = PHONE_ERROR_TEXT;
          phoneLengthError.classList.remove('hidden', 'text-slate-400');
          phoneLengthError.classList.add('text-red-500');
        }
        if (phoneInput) phoneInput.focus();
        return;
      }
      if (phoneIsBlocked) {
        e.preventDefault();
      }
    });
  }

  // ---- Incomplete order capture ----
  // Saves whatever the customer has entered so far as soon as a phone number
  // is present, so an abandoned checkout can still be followed up on later.
  // Fires immediately (no debounce) and uses sendBeacon so the request still
  // goes out even if the customer submits the form right away and the page
  // navigates before a delayed request would have fired.
  var saveProgressUrl = '{{ route('orders.save-progress') }}';

  function saveProgress() {
    var phone = phoneInput ? phoneInput.value.replace(/\D/g, '') : '';
    if (!phone) return;

    var tokenInput = checkoutForm ? checkoutForm.querySelector('input[name="_token"]') : null;
    var districtEl = document.querySelector('input[name="district"]:checked');
    var paymentEl = document.querySelector('input[name="payment_method"]:checked');
    var sizeEl = document.querySelector('select[name="size"]');
    var colorEl = document.querySelector('select[name="color"]');
    var nameEl = document.querySelector('input[name="customer_name"]');
    var addressEl = document.querySelector('input[name="address"]');
    var qtyEl = document.getElementById('qtyInput');

    var payload = new URLSearchParams();
    payload.append('_token', tokenInput ? tokenInput.value : '');
    payload.append('product_id', '{{ $product->id }}');
    payload.append('customer_name', nameEl ? nameEl.value : '');
    payload.append('phone', phone);
    payload.append('address', addressEl ? addressEl.value : '');
    payload.append('district', districtEl ? districtEl.value : '');
    payload.append('payment_method', paymentEl ? paymentEl.value : '');
    payload.append('quantity', qtyEl ? qtyEl.value : '1');
    if (sizeEl) payload.append('size', sizeEl.value);
    if (colorEl) payload.append('color', colorEl.value);

    document.querySelectorAll('input[name="extra_products[]"]:checked').forEach(function (cb) {
      payload.append('extra_products[]', cb.value);
      var extraQtyEl = document.getElementById('extra-qty-input-' + cb.value);
      payload.append('extra_quantities[' + cb.value + ']', extraQtyEl ? extraQtyEl.value : '1');
    });

    if (navigator.sendBeacon) {
      navigator.sendBeacon(saveProgressUrl, payload);
    } else {
      fetch(saveProgressUrl, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: payload,
        keepalive: true,
      }).catch(function () {});
    }
  }

  ['customer_name', 'address'].forEach(function (name) {
    var el = document.querySelector('input[name="' + name + '"]');
    if (el) el.addEventListener('blur', saveProgress);
  });
  if (phoneInput) phoneInput.addEventListener('blur', saveProgress);
  document.querySelectorAll('input[name="district"], input[name="payment_method"]').forEach(function (input) {
    input.addEventListener('change', saveProgress);
  });

  // Scroll reveal
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) entry.target.classList.add('in');
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(function (el) { observer.observe(el); });

  // Sticky mobile bar toggle
  var stickyBar = document.getElementById('stickyBar');
  var heroEnd = document.querySelector('#order');
  window.addEventListener('scroll', function () {
    var show = window.scrollY > 500 && window.scrollY < (heroEnd.offsetTop - 200);
    stickyBar.classList.toggle('translate-y-full', !show);
  });
</script>
