@extends('layouts.admin')

@section('title', 'অর্ডার #'.$order->order_number)

@php
    $paymentStatusColors = [
        'pending' => 'bg-red-100 text-red-600',
        'partial' => 'bg-amber-100 text-amber-700',
        'paid' => 'bg-emerald-100 text-emerald-700',
    ];
    $paymentStatusLabels = [
        'pending' => 'বকেয়া',
        'partial' => 'আংশিক পরিশোধ',
        'paid' => 'পরিশোধিত',
    ];
    $paymentMethods = [
        'cash' => 'ক্যাশ / হাতে হাতে',
        'bkash' => 'বিকাশ',
        'rocket' => 'রকেট',
        'nagad' => 'নগদ',
        'bank' => 'ব্যাংক ট্রান্সফার',
    ];
@endphp

@section('content')
@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-display text-xl font-bold text-slate-900">অর্ডার {{ $order->order_number }}</h2>
    <p class="text-slate-500 text-sm mt-1">{{ $order->created_at->format('d M Y, h:i A') }}</p>
  </div>
  <div class="flex items-center gap-4">
    <a href="{{ route('admin.orders.edit', $order) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-amber-600 hover:text-amber-700 border border-amber-200 hover:bg-amber-50 px-4 py-2 rounded-lg transition-colors">
      <i class="fa-solid fa-pen text-sm"></i>
      এডিট করুন
    </a>
    <a href="{{ route('admin.orders.invoice', $order) }}?auto_print=1" target="_blank"
       class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 border border-slate-200 hover:bg-slate-50 px-4 py-2 rounded-lg transition-colors">
      <i class="fa-solid fa-print text-sm"></i>
      ইনভয়েস প্রিন্ট
    </a>
    <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">&larr; সব অর্ডার</a>
  </div>
</div>


<div class="grid lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 space-y-6">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
      <h3 class="font-semibold text-slate-900 mb-4">প্রোডাক্ট সমূহ</h3>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-slate-400 text-xs border-b border-slate-100">
            <th class="text-left font-normal py-2">প্রোডাক্ট</th>
            <th class="text-left font-normal py-2">ভ্যারিয়েন্ট</th>
            <th class="text-center font-normal py-2">পরিমাণ</th>
            <th class="text-right font-normal py-2">দাম</th>
            <th class="text-right font-normal py-2">সাবটোটাল</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($order->items as $item)
            <tr class="border-b border-slate-50">
              <td class="py-3">
                <div class="flex items-center gap-3">
                  @if ($item->product && $item->product->image)
                    <img src="{{ $item->product->image_url ?: asset($item->product->image) }}" class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                  @endif
                  <span class="text-slate-800">{{ $item->product_name }}</span>
                </div>
              </td>
              <td class="py-3 text-slate-500">
                {{ collect([$item->size, $item->color])->filter()->implode(' / ') ?: '-' }}
              </td>
              <td class="py-3 text-center text-slate-600">{{ $item->quantity }}</td>
              <td class="py-3 text-right text-slate-600">৳{{ number_format((float) $item->unit_price, 2) }}</td>
              <td class="py-3 text-right text-slate-800 font-medium">৳{{ number_format((float) $item->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-5 pt-4 border-t border-slate-100 space-y-1.5 max-w-xs ml-auto text-sm">
        <div class="flex justify-between text-slate-500"><span>সাবটোটাল</span><span>৳{{ number_format((float) $order->subtotal, 2) }}</span></div>
        <div class="flex justify-between text-slate-500"><span>ডেলিভারি চার্জ</span><span>{{ (float) $order->delivery_charge <= 0 ? 'ফ্রি' : '৳'.number_format((float) $order->delivery_charge, 2) }}</span></div>
        @if ((float) $order->discount_amount > 0)
          <div class="flex justify-between text-emerald-600"><span>অফার ছাড়</span><span>-৳{{ number_format((float) $order->discount_amount, 2) }}</span></div>
        @endif
        <div class="flex justify-between font-semibold text-slate-900 pt-1.5 border-t border-slate-100"><span>সর্বমোট</span><span class="text-brand">৳{{ number_format((float) $order->total, 2) }}</span></div>
        <div class="flex justify-between text-emerald-600"><span>পরিশোধিত</span><span>৳{{ number_format((float) $order->paid_amount, 2) }}</span></div>
        <div class="flex justify-between font-semibold {{ (float) $order->due_amount > 0 ? 'text-red-600' : 'text-slate-400' }}"><span>বকেয়া</span><span>৳{{ number_format((float) $order->due_amount, 2) }}</span></div>
      </div>
    </div>

    @if ($order->payments->isNotEmpty())
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <h3 class="font-semibold text-slate-900 mb-4">পেমেন্ট হিস্টোরি</h3>
        <table class="w-full text-sm">
          <thead>
            <tr class="text-slate-400 text-xs border-b border-slate-100">
              <th class="text-left font-normal py-2">তারিখ</th>
              <th class="text-left font-normal py-2">মাধ্যম</th>
              <th class="text-left font-normal py-2">নোট</th>
              <th class="text-right font-normal py-2">পরিমাণ</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order->payments as $payment)
              <tr class="border-b border-slate-50">
                <td class="py-2.5 text-slate-500">{{ $payment->paid_at->format('d M Y, h:i A') }}</td>
                <td class="py-2.5 text-slate-600">{{ $paymentMethods[$payment->method] ?? $payment->method }}</td>
                <td class="py-2.5 text-slate-400">{{ $payment->note ?: '-' }}</td>
                <td class="py-2.5 text-right text-emerald-600 font-medium">৳{{ number_format((float) $payment->amount, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
      <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <h3 class="font-semibold text-slate-900">কাস্টমার তথ্য</h3>
        <button type="button"
                onclick="openFraudModal('{{ $order->phone }}', '{{ addslashes($order->customer_name) }}')"
                class="js-fraud-check-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-all shadow-xs"
                data-phone="{{ $order->phone }}">
          <i class="fa-solid fa-shield-halved text-slate-500"></i>
          <span>কুরিয়ার ফ্রড চেক করুন</span>
        </button>
      </div>
      <dl class="grid sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-slate-400 mb-0.5">নাম</dt><dd class="text-slate-800 font-medium">{{ $order->customer_name }}</dd></div>
        <div>
          <dt class="text-slate-400 mb-0.5">ফোন</dt>
          <dd class="text-slate-800 font-medium flex items-center gap-2">
            <span>{{ $order->phone }}</span>
            <a href="tel:{{ $order->phone }}" class="text-slate-400 hover:text-brand" title="কল করুন"><i class="fas fa-phone text-xs"></i></a>
            @php $waNum = '880'.ltrim(preg_replace('/\D/', '', $order->phone), '0'); @endphp
            <a href="https://wa.me/{{ $waNum }}" target="_blank" class="text-emerald-500 hover:text-emerald-600" title="হোয়াটসঅ্যাপ"><i class="fab fa-whatsapp text-sm"></i></a>
          </dd>
        </div>
        <div class="sm:col-span-2"><dt class="text-slate-400 mb-0.5">ঠিকানা</dt><dd class="text-slate-800">{{ $order->address }}, {{ $order->district }}</dd></div>
        <div><dt class="text-slate-400 mb-0.5">পেমেন্ট পদ্ধতি</dt><dd class="text-slate-800 uppercase">{{ $order->payment_method }}</dd></div>
        @if ($order->ip_address)
          <div><dt class="text-slate-400 mb-0.5">IP ঠিকানা</dt><dd class="text-slate-800 font-mono text-xs">{{ $order->ip_address }}</dd></div>
        @endif
      </dl>
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-900">পেমেন্ট স্ট্যাটাস</h3>
        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $paymentStatusColors[$order->payment_status] ?? 'bg-slate-100 text-slate-600' }}">{{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}</span>
      </div>
      @if ((float) $order->due_amount > 0)
        <form method="POST" action="{{ route('admin.orders.addPayment', $order) }}" class="space-y-3">
          @csrf
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">পরিমাণ (বকেয়া ৳{{ number_format((float) $order->due_amount, 2) }})</label>
            <input type="number" name="amount" min="0.01" step="0.01" max="{{ (float) $order->due_amount }}" value="{{ (float) $order->due_amount }}" required
                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">মাধ্যম</label>
            <select name="method" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
              @foreach ($paymentMethods as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">নোট</label>
            <input type="text" name="note" maxlength="255" placeholder="ঐচ্ছিক"
                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          </div>
          <button type="submit" class="w-full bg-brand hover:bg-brandDark text-white font-semibold py-2.5 rounded-lg transition-colors">টাকা গ্রহণ করুন</button>
        </form>
      @else
        <p class="text-sm text-emerald-600 font-medium">সম্পূর্ণ পরিশোধ হয়েছে। আর কোনো বকেয়া নেই।</p>
      @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
      <h3 class="font-semibold text-slate-900 mb-4">অর্ডার স্ট্যাটাস</h3>
      <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="space-y-3">
        @csrf
        @method('PATCH')
        <select name="status" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          @foreach ($statusLabels as $key => $label)
            <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        <button type="submit" class="w-full bg-brand hover:bg-brandDark text-white font-semibold py-2.5 rounded-lg transition-colors">আপডেট করুন</button>
      </form>
    </div>

    {{-- Courier Dispatch Section --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-900 flex items-center gap-2">
          <i class="fa-solid fa-truck-fast text-brand"></i>
          <span>কুরিয়ার ডেলিভারি</span>
        </h3>
        @if ($order->hasCourierConsignment())
          <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $order->active_courier_name === 'pathao' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
            <i class="fa-solid fa-circle-check text-[10px]"></i>
            {{ $order->active_courier_name === 'pathao' ? 'Pathao' : 'Steadfast' }}
          </span>
        @endif
      </div>

      @if ($order->hasCourierConsignment())
        <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-4 space-y-2.5 text-xs">
          <div class="flex justify-between items-center">
            <span class="text-slate-500">কুরিয়ার:</span>
            <span class="font-bold {{ $order->active_courier_name === 'pathao' ? 'text-red-600' : 'text-emerald-700' }} uppercase">
              {{ $order->active_courier_name === 'pathao' ? 'Pathao Courier' : 'Steadfast Courier' }}
            </span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-slate-500">কনসাইনমেন্ট ID:</span>
            <span class="font-mono font-medium text-slate-800">{{ $order->active_consignment_id ?: '-' }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-slate-500">ট্র্যাকিং কোড:</span>
            <span class="font-mono font-bold text-slate-900">{{ $order->active_tracking_code ?: '-' }}</span>
          </div>
          @php
            $deliveryStatus = $order->active_courier_name === 'pathao' ? $order->pathao_delivery_status : $order->steadfast_delivery_status;
          @endphp
          @if ($deliveryStatus)
            <div class="flex justify-between items-center">
              <span class="text-slate-500">বর্তমান স্ট্যাটাস:</span>
              <span class="font-semibold px-2 py-0.5 rounded bg-white text-slate-700 border border-slate-200">{{ ucfirst(str_replace('_', ' ', $deliveryStatus)) }}</span>
            </div>
          @endif
        </div>

        <div class="mt-4 flex flex-col gap-2">
          <a href="{{ route('admin.orders.tracking', $order) }}"
             class="w-full inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2.5 px-4 rounded-lg transition-colors shadow-xs">
            <i class="fa-solid fa-location-crosshairs text-xs"></i>
            <span>লাইভ ট্র্যাকিং দেখুন</span>
          </a>

          @if (!empty($activeCouriers))
            <div class="pt-2 border-t border-slate-100 text-center">
              <p class="text-[11px] text-slate-400 mb-2">অন্য কোনো কুরিয়ারে আবার পাঠাতে চান?</p>
              <div class="grid grid-cols-{{ count($activeCouriers) }} gap-2">
                @foreach ($activeCouriers as $slug => $c)
                  <button type="button"
                          onclick="confirmSendCourier('{{ $slug }}', '{{ $c['name'] }}')"
                          class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 hover:bg-slate-50 text-slate-700 transition">
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    <span>{{ $c['name'] }}</span>
                  </button>
                @endforeach
              </div>
            </div>
          @endif
        </div>
      @else
        @if (empty($activeCouriers))
          <div class="rounded-xl bg-amber-50 border border-amber-200 p-3.5 text-xs text-amber-800">
            <div class="flex items-start gap-2">
              <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
              <div>
                <p class="font-semibold">কোনো কুরিয়ার চালু (ON) নেই</p>
                <p class="text-[11px] text-amber-700 mt-0.5">অর্ডার পাঠাতে সাইডবার থেকে কুরিয়ার সেটিংস চালু করুন।</p>
                <a href="{{ route('admin.couriers.index') }}" class="inline-flex items-center gap-1 mt-2 font-medium text-amber-900 underline hover:no-underline">
                  <span>কুরিয়ার সেটিংস যান</span> &rarr;
                </a>
              </div>
            </div>
          </div>
        @else
          <p class="text-xs text-slate-500 mb-3">এই অর্ডারটি সরাসরি সক্রিয় কুরিয়ারে পাঠিয়ে দিন:</p>
          <div class="space-y-2">
            @if (isset($activeCouriers['steadfast']))
              <button type="button"
                      onclick="confirmSendCourier('steadfast', '{{ $activeCouriers['steadfast']['name'] }}')"
                      class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2.5 px-4 rounded-lg transition-all shadow-xs hover:shadow">
                <i class="fa-solid fa-truck-fast"></i>
                <span>{{ $activeCouriers['steadfast']['name'] }} এ পাঠান</span>
              </button>
            @endif

            @if (isset($activeCouriers['pathao']))
              <button type="button"
                      onclick="confirmSendCourier('pathao', '{{ $activeCouriers['pathao']['name'] }}')"
                      class="w-full inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-2.5 px-4 rounded-lg transition-all shadow-xs hover:shadow">
                <i class="fa-solid fa-motorcycle"></i>
                <span>{{ $activeCouriers['pathao']['name'] }} এ পাঠান</span>
              </button>
            @endif
          </div>
        @endif
      @endif
    </div>
  </div>
</div>

@include('admin.orders.partials.fraud-modal')
@endsection

@push('scripts')
<script>
  function confirmSendCourier(courierSlug, courierName) {
    Swal.fire({
      title: courierName + ' এ পাঠাতে চান?',
      text: 'অর্ডার #{{ $order->order_number }} কনসাইনমেন্ট তৈরি করে কুরিয়ারে পাঠানো হবে।',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'হ্যাঁ, পাঠান',
      cancelButtonText: 'বাতিল',
      confirmButtonColor: '#4f46e5',
      showLoaderOnConfirm: true,
      preConfirm: function () {
        return fetch('{{ route('admin.orders.send-courier', $order) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ courier: courierSlug })
        })
        .then(function (res) {
          return res.json().then(function (data) {
            if (!res.ok) {
              throw new Error(data.message || 'কুরিয়ারে পাঠাতে সমস্যা হয়েছে।');
            }
            return data;
          });
        })
        .catch(function (error) {
          Swal.showValidationMessage(error.message);
        });
      },
      allowOutsideClick: function () { return !Swal.isLoading(); }
    }).then(function (result) {
      if (result.isConfirmed && result.value && result.value.success) {
        Swal.fire({
          icon: 'success',
          title: 'সফল!',
          text: result.value.message,
          timer: 2000,
          showConfirmButton: false
        }).then(function () {
          window.location.reload();
        });
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var phone = '{{ $order->phone }}';
    if (!phone) return;

    fetch('{{ route('admin.fraud-check.lookup') }}?phone=' + encodeURIComponent(phone), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
      if (json && json.success && json.data && typeof updateOrderRowBadges === 'function') {
        updateOrderRowBadges(phone, json.data);
      }
    })
    .catch(function () {});
  });
</script>
@endpush
