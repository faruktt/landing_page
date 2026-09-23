@extends('layouts.admin')

@section('title', 'অসম্পূর্ণ অর্ডার')

@section('content')
<div class="mb-6">
  <h2 class="font-display text-xl font-bold text-slate-900">অসম্পূর্ণ অর্ডার</h2>
  <p class="text-slate-500 text-sm mt-1">যেসব কাস্টমার চেকআউট ফর্ম পূরণ করেছেন কিন্তু অর্ডার সম্পন্ন করেননি — ফলো-আপ করে অর্ডারটা কনভার্ট করুন।</p>
</div>

<div class="flex gap-2 mb-4">
  <a href="{{ route('admin.incomplete-orders.index') }}"
     class="px-3.5 py-1.5 rounded-full text-sm font-medium border transition
       {{ ! request()->filled('filter') ? 'bg-brand text-white border-brand' : 'bg-white text-slate-600 border-slate-200 hover:border-brand/40' }}">
    সব
  </a>
  <a href="{{ route('admin.incomplete-orders.index', ['filter' => 'uncontacted']) }}"
     class="px-3.5 py-1.5 rounded-full text-sm font-medium border transition
       {{ request('filter') === 'uncontacted' ? 'bg-brand text-white border-brand' : 'bg-white text-slate-600 border-slate-200 hover:border-brand/40' }}">
    যোগাযোগ করা হয়নি
  </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
      <tr>
        <th class="text-left font-medium px-4 py-3">সময়</th>
        <th class="text-left font-medium px-4 py-3">কাস্টমার</th>
        <th class="text-left font-medium px-4 py-3">প্রোডাক্ট</th>
        <th class="text-right font-medium px-4 py-3">সম্ভাব্য মোট</th>
        <th class="text-center font-medium px-4 py-3">স্ট্যাটাস</th>
        <th class="text-center font-medium px-4 py-3">অ্যাকশন</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      @forelse ($incompleteOrders as $incompleteOrder)
        @php $waNumber = '880'.ltrim(preg_replace('/\D/', '', $incompleteOrder->phone ?? ''), '0'); @endphp
        <tr class="hover:bg-slate-50/50 transition-colors">
          <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
            {{ $incompleteOrder->created_at->format('d M Y') }}<br>
            <span class="text-slate-400">{{ $incompleteOrder->created_at->format('h:i A') }}</span>
            @if ($incompleteOrder->updated_at->ne($incompleteOrder->created_at))
              <p class="text-[10px] text-amber-500 mt-0.5">আপডেট {{ $incompleteOrder->updated_at->diffForHumans() }}</p>
            @endif
          </td>
          <td class="px-4 py-3">
            <p class="text-[13px] font-medium text-slate-700">{{ $incompleteOrder->customer_name ?: '—' }}</p>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="text-[11px] text-slate-400">{{ $incompleteOrder->phone }}</span>
              @if ($incompleteOrder->phone)
                <a href="tel:{{ $incompleteOrder->phone }}" title="কল করুন" class="text-slate-400 hover:text-brand">
                  <i class="fas fa-phone text-[10px]"></i>
                </a>
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" title="হোয়াটসঅ্যাপ" class="text-slate-400 hover:text-emerald-500">
                  <i class="fab fa-whatsapp text-[11px]"></i>
                </a>
              @endif
            </div>
            @if ($incompleteOrder->address || $incompleteOrder->district)
              <p class="text-[11px] text-slate-400 mt-0.5 max-w-[200px] truncate">{{ collect([$incompleteOrder->address, $incompleteOrder->district])->filter()->implode(', ') }}</p>
            @endif
          </td>
          <td class="px-4 py-3">
            @if ($incompleteOrder->cart_snapshot)
              <ul class="text-xs text-slate-600 space-y-0.5">
                @foreach ($incompleteOrder->cart_snapshot as $item)
                  <li>{{ $item['qty'] }}× {{ $item['name'] }}{{ !empty($item['size']) ? ' ('.$item['size'].')' : '' }}{{ !empty($item['color']) ? ' ('.$item['color'].')' : '' }}</li>
                @endforeach
              </ul>
            @else
              <span class="text-slate-300 text-xs">—</span>
            @endif
          </td>
          <td class="px-4 py-3 text-right font-bold text-slate-800">৳{{ number_format((float) $incompleteOrder->subtotal, 0) }}</td>
          <td class="px-4 py-3 text-center">
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $incompleteOrder->contacted_at ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
              {{ $incompleteOrder->contacted_at ? 'যোগাযোগ হয়েছে' : 'যোগাযোগ হয়নি' }}
            </span>
          </td>
          <td class="px-4 py-3 text-center">
            <div class="inline-flex items-center gap-1.5">
              <form method="POST" action="{{ route('admin.incomplete-orders.toggleContacted', $incompleteOrder) }}">
                @csrf
                <button type="submit" title="{{ $incompleteOrder->contacted_at ? 'যোগাযোগ হয়নি চিহ্নিত করুন' : 'যোগাযোগ হয়েছে চিহ্নিত করুন' }}"
                        class="inline-flex text-emerald-600 hover:bg-emerald-50 w-8 h-8 items-center justify-center rounded-lg border border-emerald-200 transition-colors">
                  <i class="fa-solid {{ $incompleteOrder->contacted_at ? 'fa-rotate-left' : 'fa-check' }} text-sm"></i>
                </button>
              </form>
              <form method="POST" action="{{ route('admin.incomplete-orders.destroy', $incompleteOrder) }}" class="js-confirm-delete" data-confirm-text="এই এন্ট্রিটি স্থায়ীভাবে মুছে যাবে।">
                @csrf
                @method('DELETE')
                <button type="submit" title="মুছুন"
                        class="inline-flex text-red-500 hover:bg-red-50 w-8 h-8 items-center justify-center rounded-lg border border-red-200 transition-colors">
                  <i class="fa-solid fa-trash text-sm"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="px-4 py-16 text-center text-slate-400">
            <i class="fa-solid fa-cart-shopping text-4xl mb-3 opacity-40"></i>
            <p class="font-medium">কোনো অসম্পূর্ণ অর্ডার নেই।</p>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
  @if ($incompleteOrders->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $incompleteOrders->links() }}</div>
  @endif
</div>
@endsection
