<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>ইনভয়েস {{ $order->order_number }} - {{ \App\Models\Setting::current()->site_name }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans: ['Hind Siliguri', 'sans-serif'],
          display: ['Poppins', 'sans-serif'],
        },
        colors: {
          brand: '#4f46e5',
          brandDark: '#3730a3',
        },
      },
    },
  }
</script>
<style>
  body { font-family: 'Hind Siliguri', sans-serif; }
  @media print {
    .no-print { display: none !important; }
    body { padding: 0 !important; margin: 0 !important; }
    .invoice-box { box-shadow: none !important; border: none !important; padding: 0 !important; }
  }
</style>
</head>
<body class="bg-slate-100 py-10">

  @php $siteSettings = \App\Models\Setting::current(); @endphp

  <div class="max-w-3xl mx-auto mb-4 flex justify-end no-print">
    <button onclick="window.print()" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors inline-flex items-center gap-2">
      <i class="fa-solid fa-print text-sm"></i>
      প্রিন্ট করুন
    </button>
  </div>

  <div class="invoice-box max-w-3xl mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-8 sm:p-10">
    <div class="flex items-start justify-between border-b border-slate-100 pb-6 mb-6">
      <div>
        @if ($siteSettings->logo)
          <img src="{{ $siteSettings->logo_url ?: asset($siteSettings->logo) }}" class="h-10 w-auto max-w-[160px] object-contain" alt="{{ $siteSettings->site_name }}">
        @else
          <h1 class="font-display text-2xl font-extrabold text-brand">{{ $siteSettings->site_name }}</h1>
        @endif
      </div>
      <div class="text-right">
        <h2 class="font-display text-lg font-bold text-slate-900 uppercase tracking-wide">Invoice</h2>
        <p class="text-slate-500 text-sm mt-1">অর্ডার নং: <span class="font-medium text-slate-800">{{ $order->order_number }}</span></p>
        <p class="text-slate-500 text-sm">তারিখ: {{ $order->created_at->format('d M Y, h:i A') }}</p>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-6 mb-8">
      <div>
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">বিল টু</h3>
        <p class="text-slate-800 font-medium">{{ $order->customer_name }}</p>
        <p class="text-slate-600 text-sm mt-0.5">{{ $order->phone }}</p>
        <p class="text-slate-600 text-sm mt-0.5">{{ $order->address }}, {{ $order->district }}</p>
      </div>
      <div class="sm:text-right">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">পেমেন্ট তথ্য</h3>
        <p class="text-slate-600 text-sm">পদ্ধতি: <span class="font-medium text-slate-800 uppercase">{{ $order->payment_method }}</span></p>
        <p class="text-slate-600 text-sm mt-0.5">স্ট্যাটাস:
          <span class="font-medium text-slate-800">
            {{ $statusLabels[$order->status] ?? $order->status }}
          </span>
        </p>
      </div>
    </div>

    <table class="w-full text-sm mb-6">
      <thead>
        <tr class="text-slate-400 text-xs border-b border-slate-200">
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
            <td class="py-3 text-slate-800">{{ $item->product_name }}</td>
            <td class="py-3 text-slate-500">{{ collect([$item->size, $item->color])->filter()->implode(' / ') ?: '-' }}</td>
            <td class="py-3 text-center text-slate-600">{{ $item->quantity }}</td>
            <td class="py-3 text-right text-slate-600">৳{{ number_format((float) $item->unit_price, 2) }}</td>
            <td class="py-3 text-right text-slate-800 font-medium">৳{{ number_format((float) $item->subtotal, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="pt-4 border-t border-slate-100 space-y-1.5 max-w-xs ml-auto text-sm mb-8">
      <div class="flex justify-between text-slate-500"><span>সাবটোটাল</span><span>৳{{ number_format((float) $order->subtotal, 2) }}</span></div>
      <div class="flex justify-between text-slate-500"><span>ডেলিভারি চার্জ</span><span>{{ (float) $order->delivery_charge <= 0 ? 'ফ্রি' : '৳'.number_format((float) $order->delivery_charge, 2) }}</span></div>
      @if ((float) $order->discount_amount > 0)
        <div class="flex justify-between text-emerald-600"><span>অফার ছাড়</span><span>-৳{{ number_format((float) $order->discount_amount, 2) }}</span></div>
      @endif
      <div class="flex justify-between font-semibold text-slate-900 pt-1.5 border-t border-slate-100 text-base"><span>সর্বমোট</span><span class="text-brand">৳{{ number_format((float) $order->total, 2) }}</span></div>
      <div class="flex justify-between text-emerald-600"><span>পরিশোধিত</span><span>৳{{ number_format((float) $order->paid_amount, 2) }}</span></div>
      <div class="flex justify-between font-semibold {{ (float) $order->due_amount > 0 ? 'text-red-600' : 'text-slate-400' }}"><span>বকেয়া</span><span>৳{{ number_format((float) $order->due_amount, 2) }}</span></div>
    </div>

    <div class="text-center text-slate-400 text-xs border-t border-slate-100 pt-6">
      আমাদের সাথে কেনাকাটা করার জন্য ধন্যবাদ। কোনো প্রশ্ন থাকলে যোগাযোগ করুন।
    </div>
  </div>

  @if (request()->boolean('auto_print'))
    <script>
      window.onload = function () { window.print(); };
    </script>
  @endif

</body>
</html>
