@extends('layouts.admin')

@section('title', 'কুরিয়ার সেটিংস')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  <!-- Header -->
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div>
      <h2 class="font-display text-2xl font-bold text-slate-900 flex items-center gap-2.5">
        <i class="fa-solid fa-truck-fast text-brand"></i>
        কুরিয়ার সেটিংস (Courier Configuration)
      </h2>
      <p class="text-slate-500 text-sm mt-1">
        Steadfast এবং Pathao কুরিয়ারের ক্রেডেনশিয়াল দিয়ে স্ট্যাটাস অন করুন। যে কুরিয়ারের স্ট্যাটাস চালু থাকবে, অর্ডার টেবিলে শুধুমাত্র সেটাই দেখাবে।
      </p>
    </div>
  </div>

  @if (session('status'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl p-4 flex items-center gap-3">
      <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl p-4">
      <p class="font-semibold mb-1">কিছু তথ্য সংশোধন করা প্রয়োজন:</p>
      <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.couriers.update') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- ================= 1. STEADFAST COURIER ================= -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
          <!-- Card Header -->
          <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 flex items-center justify-center font-bold text-lg">
                <i class="fa-solid fa-box"></i>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base">Steadfast Courier</h3>
                <p class="text-xs text-slate-400">স্টেডফাস্ট পার্সেল ডেলিভারি API</p>
              </div>
            </div>

            <!-- Status Toggle Switch -->
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" name="steadfast_is_active" value="1" class="sr-only peer" {{ $steadfast->is_active ? 'checked' : '' }}>
              <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
              <span class="ml-2 text-xs font-semibold {{ $steadfast->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                {{ $steadfast->is_active ? 'চালু (ON)' : 'বন্ধ (OFF)' }}
              </span>
            </label>
          </div>

          <!-- Card Body -->
          <div class="p-5 space-y-4 text-sm">
            <div>
              <label class="block font-medium text-slate-700 text-xs mb-1.5">API Key *</label>
              <input type="text" name="steadfast_api_key" value="{{ old('steadfast_api_key', $steadfast->api_key) }}"
                     placeholder="e.g. apsizdya800nmltfr8q5jwfbc00zhq5z"
                     class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>

            <div>
              <label class="block font-medium text-slate-700 text-xs mb-1.5">Secret Key *</label>
              <input type="text" name="steadfast_secret_key" value="{{ old('steadfast_secret_key', $steadfast->secret_key) }}"
                     placeholder="e.g. xxxxxxxxxxxxxxxxxxxxx"
                     class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
            </div>

            <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100 text-xs text-slate-500 space-y-1">
              <p class="font-semibold text-slate-700">কোথা থেকে পাবেন:</p>
              <p>Steadfast মার্চেন্ট পোর্টালে লগইন করে Settings &rarr; API Information মেনু থেকে API Key ও Secret Key সংগ্রহ করুন।</p>
            </div>
          </div>
        </div>

        <!-- Card Footer with Test button -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/40 flex items-center justify-between">
          <span class="text-[11px] text-slate-400">
            @if ($steadfast->is_active)
              <span class="text-emerald-600 font-medium">● অর্ডার টেবিলে দৃশ্যমান</span>
            @else
              <span class="text-slate-400">○ অর্ডার টেবিলে লুকানো থাকবে</span>
            @endif
          </span>
          <button type="button" id="testSteadfastBtn" onclick="testSteadfastConn()"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 rounded-lg transition border border-cyan-200">
            <i class="fa-solid fa-plug"></i>
            <span>সংযোগ টেস্ট করুন</span>
          </button>
        </div>
      </div>


      <!-- ================= 2. PATHAO COURIER ================= -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
          <!-- Card Header -->
          <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-lg">
                <i class="fa-solid fa-motorcycle"></i>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base">Pathao Courier</h3>
                <p class="text-xs text-slate-400">পাঠাও মার্চেন্ট ডেলিভারি API</p>
              </div>
            </div>

            <!-- Status Toggle Switch -->
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" name="pathao_is_active" value="1" class="sr-only peer" {{ $pathao->is_active ? 'checked' : '' }}>
              <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
              <span class="ml-2 text-xs font-semibold {{ $pathao->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                {{ $pathao->is_active ? 'চালু (ON)' : 'বন্ধ (OFF)' }}
              </span>
            </label>
          </div>

          <!-- Card Body -->
          <div class="p-5 space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-slate-700 text-xs mb-1.5">Environment</label>
                <select name="pathao_environment" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
                  <option value="production" {{ old('pathao_environment', $pathao->environment) === 'production' ? 'selected' : '' }}>Production (Live)</option>
                  <option value="sandbox" {{ old('pathao_environment', $pathao->environment) === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                </select>
              </div>

              <div>
                <label class="block font-medium text-slate-700 text-xs mb-1.5">Store ID</label>
                <div class="flex gap-1.5">
                  <input type="text" name="pathao_store_id" id="pathaoStoreIdInput" value="{{ old('pathao_store_id', $pathao->store_id) }}"
                         placeholder="e.g. 12345"
                         class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
                  <button type="button" onclick="fetchPathaoStores()" title="স্টোর তালিকা থেকে লোড করুন"
                          class="px-2.5 py-2 text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition shrink-0">
                    <i class="fa-solid fa-download"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-slate-700 text-xs mb-1.5">Client ID *</label>
                <input type="text" name="pathao_client_id" value="{{ old('pathao_client_id', $pathao->client_id) }}"
                       placeholder="Pathao Client ID"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
              </div>

              <div>
                <label class="block font-medium text-slate-700 text-xs mb-1.5">Client Secret *</label>
                <input type="password" name="pathao_client_secret" value="{{ old('pathao_client_secret', $pathao->client_secret) }}"
                       placeholder="Pathao Client Secret"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-slate-700 text-xs mb-1.5">Username (Email) *</label>
                <input type="text" name="pathao_username" value="{{ old('pathao_username', $pathao->username) }}"
                       placeholder="e.g. merchant@mail.com"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
              </div>

              <div>
                <label class="block font-medium text-slate-700 text-xs mb-1.5">Password *</label>
                <input type="password" name="pathao_password" value="{{ old('pathao_password', $pathao->password) }}"
                       placeholder="Pathao Password"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
              </div>
            </div>

            <div id="pathaoStoresContainer" class="hidden rounded-xl bg-slate-50 p-3 border border-slate-200 text-xs space-y-1.5">
              <p class="font-bold text-slate-700">উপলব্ধ স্টোর সমূহ:</p>
              <div id="pathaoStoresList" class="space-y-1"></div>
            </div>

            <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100 text-xs text-slate-500 space-y-1">
              <p class="font-semibold text-slate-700">কোথা থেকে পাবেন:</p>
              <p>Pathao Merchant পোর্টালে Developers &rarr; API Settings থেকে Client ID ও Secret সংগ্রহ করুন।</p>
            </div>
          </div>
        </div>

        <!-- Card Footer with Status indicator -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/40 flex items-center justify-between">
          <span class="text-[11px] text-slate-400">
            @if ($pathao->is_active)
              <span class="text-emerald-600 font-medium">● অর্ডার টেবিলে দৃশ্যমান</span>
            @else
              <span class="text-slate-400">○ অর্ডার টেবিলে লুকানো থাকবে</span>
            @endif
          </span>
          <button type="button" onclick="fetchPathaoStores()"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition border border-rose-200">
            <i class="fa-solid fa-store"></i>
            <span>স্টোর ও টোকেন টেস্ট</span>
          </button>
        </div>
      </div>

    </div>

    <!-- Submit Button Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
      <p class="text-xs text-slate-500">
        <i class="fa-solid fa-circle-info text-brand mr-1"></i>
        পরিবর্তন নিশ্চিত করতে নিচের বাটনে ক্লিক করে সেটিংস সেভ করুন।
      </p>
      <button type="submit" class="bg-gradient-to-r from-brand to-brandDark hover:opacity-90 text-white font-semibold px-7 py-2.5 rounded-xl text-sm flex items-center gap-2 shadow-md shadow-brand/20 transition-all">
        <i class="fa-solid fa-floppy-disk"></i>
        <span>সেটিংস সংরক্ষণ করুন</span>
      </button>
    </div>

  </form>

</div>

@push('scripts')
<script>
  function testSteadfastConn() {
    const btn = document.getElementById('testSteadfastBtn');
    btn.classList.add('opacity-60', 'pointer-events-none');

    fetch('{{ route('admin.couriers.test-steadfast') }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(res => {
      btn.classList.remove('opacity-60', 'pointer-events-none');
      if (res.ok && res.data.success) {
        Swal.fire({
          icon: 'success',
          title: 'সফল!',
          text: res.data.message || 'Steadfast API সংযোগ সঠিক আছে!',
          confirmButtonColor: '#4f46e5'
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'ব্যর্থ হয়েছে',
          text: res.data.message || 'সংযোগ স্থাপন করা যায়নি। API Key ও Secret Key চেক করুন।',
          confirmButtonColor: '#4f46e5'
        });
      }
    })
    .catch(err => {
      btn.classList.remove('opacity-60', 'pointer-events-none');
      Swal.fire({
        icon: 'error',
        title: 'ত্রুটি',
        text: 'সার্ভারে অনুরোধ পাঠানো যায়নি।',
        confirmButtonColor: '#4f46e5'
      });
    });
  }

  function fetchPathaoStores() {
    Swal.fire({
      title: 'পাঠাও স্টোর খোঁজা হচ্ছে...',
      text: 'অনুগ্রহ করে কিছুক্ষণ অপেক্ষা করুন',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    fetch('{{ route('admin.couriers.pathao-stores') }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(res => {
      if (res.ok && res.data.success && res.data.stores) {
        const stores = res.data.stores;
        const container = document.getElementById('pathaoStoresContainer');
        const list = document.getElementById('pathaoStoresList');
        list.innerHTML = '';

        if (stores.length > 0) {
          stores.forEach(s => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 text-xs';
            row.innerHTML = `
              <div>
                <span class="font-bold text-slate-800">${escapeHtml(s.store_name || 'Store')}</span>
                <span class="text-slate-400 font-mono text-[10px] ml-1">(ID: ${s.store_id})</span>
                <p class="text-[10px] text-slate-500">${escapeHtml(s.store_address || '')}</p>
              </div>
              <button type="button" onclick="selectPathaoStore('${s.store_id}')" class="px-2 py-1 bg-brand text-white rounded text-[10px] font-semibold hover:bg-brandDark transition">
                সিলেক্ট
              </button>
            `;
            list.appendChild(row);
          });
          container.classList.remove('hidden');

          Swal.fire({
            icon: 'success',
            title: 'স্টোর পাওয়া গেছে!',
            text: `${stores.length}টি স্টোর লোড হয়েছে। উপযুক্ত স্টোর সিলেক্ট করুন।`,
            confirmButtonColor: '#4f46e5'
          });
        } else {
          Swal.fire({
            icon: 'info',
            title: 'কোনো স্টোর নেই',
            text: 'আপনার পাঠাও অ্যাকাউন্টে কোনো স্টোর তৈরি করা নেই।',
            confirmButtonColor: '#4f46e5'
          });
        }
      } else {
        Swal.fire({
          icon: 'error',
          title: 'ব্যর্থ হয়েছে',
          text: res.data.message || 'পাঠাও সংযোগ করা যায়নি। ক্রেডেনশিয়াল চেক করুন।',
          confirmButtonColor: '#4f46e5'
        });
      }
    })
    .catch(err => {
      Swal.fire({
        icon: 'error',
        title: 'ত্রুটি',
        text: 'পাঠাও সার্ভারের সাথে যোগাযোগ ব্যর্থ হয়েছে।',
        confirmButtonColor: '#4f46e5'
      });
    });
  }

  function selectPathaoStore(storeId) {
    document.getElementById('pathaoStoreIdInput').value = storeId;
    Swal.fire({
      icon: 'success',
      title: 'Store ID সিলেক্ট করা হয়েছে!',
      text: 'Store ID: ' + storeId,
      timer: 1500,
      showConfirmButton: false
    });
  }

  function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
  }
</script>
@endpush
@endsection
