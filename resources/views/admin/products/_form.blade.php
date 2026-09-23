@php
    $isEdit = $product->exists;
    $selectedTemplate = old('template', $product->template ?? 1);
    $existingBenefits = old('benefits', $product->exists ? $product->benefits->map(fn ($b) => ['title' => $b->title, 'text' => $b->text])->values()->all() : []);
    $existingReviews = old('reviews', $product->exists ? $product->reviews->map(fn ($r) => ['customer_name' => $r->customer_name, 'location' => $r->location, 'rating' => $r->rating, 'comment' => $r->comment])->values()->all() : []);
    $existingSpecs = old('specs', $product->exists ? $product->specs->map(fn ($s) => ['label' => $s->label, 'value' => $s->value])->values()->all() : []);
    $defaultTrustPoints = [
        ['text' => 'সরাসরি সোর্স থেকে ফ্রেশ প্রোডাক্ট সংগ্রহ করে আপনার হাতে পৌঁছে দিই।'],
        ['text' => 'কোয়ালিটি চেক করার পরই প্রতিটি প্রোডাক্ট প্যাকেজিং করা হয়।'],
        ['text' => 'অর্ডার করতে অগ্রিম ১ টাকাও পেমেন্ট করতে হবে না।'],
        ['text' => 'সারা বাংলাদেশে ক্যাশ অন হোম ডেলিভারী সুবিধা।'],
        ['text' => 'পণ্য হাতে পাওয়ার পর প্রয়োজনে ফেরত/রিফান্ড নেওয়া যাবে।'],
        ['text' => 'প্রত্যাশা অনুযায়ী প্রোডাক্ট না পেলে সাথে সাথে রিটার্ন সুবিধা থাকছে।'],
    ];
    $existingTrustPoints = old('trust_points', $product->exists ? $product->trustPoints->map(fn ($t) => ['text' => $t->text])->values()->all() : $defaultTrustPoints);
    $selectedRelatedIds = old('related_products', $product->exists ? $product->relatedProducts->pluck('id')->all() : []);
    $existingCartOffers = old('cart_offers', $product->exists ? $product->cartOffers->map(fn ($o) => [
        'min_cart_amount' => (float) $o->min_cart_amount,
        'reward_type' => $o->reward_type,
        'discount_amount' => $o->discount_amount ? (float) $o->discount_amount : null,
    ])->values()->all() : []);
@endphp

@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
    <p class="font-medium mb-1">ফর্মে কিছু ভুল আছে:</p>
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @if ($isEdit) @method('PUT') @endif

  <!-- Template selector -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <h3 class="font-semibold text-slate-900 mb-1">ল্যান্ডিং পেজ টেমপ্লেট বাছাই করুন</h3>
    <p class="text-sm text-slate-500 mb-4">যে ডিজাইনে এই প্রোডাক্টের ল্যান্ডিং পেজ তৈরি হবে।</p>

    <div class="grid sm:grid-cols-3 gap-4" id="templateOptions">
      <label class="template-option relative cursor-pointer">
        <input type="radio" name="template" value="1" class="peer sr-only" {{ (string) $selectedTemplate === '1' ? 'checked' : '' }}>
        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-brand peer-checked:ring-2 peer-checked:ring-brand/20 p-4 transition">
          <div class="h-20 rounded-lg bg-gradient-to-br from-amber-50 to-orange-100 flex items-center justify-center mb-3">
            <span class="text-xs font-semibold text-orange-700">Fashion Style</span>
          </div>
          <p class="text-sm font-medium text-slate-800">টেমপ্লেট ১</p>
          <p class="text-xs text-slate-500">সাইজ/রঙসহ ফ্যাশন প্রোডাক্ট</p>
        </div>
      </label>

      <label class="template-option relative cursor-pointer">
        <input type="radio" name="template" value="2" class="peer sr-only" {{ (string) $selectedTemplate === '2' ? 'checked' : '' }}>
        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-brand peer-checked:ring-2 peer-checked:ring-brand/20 p-4 transition">
          <div class="h-20 rounded-lg bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center mb-3">
            <span class="text-xs font-semibold text-emerald-700">COD Funnel Style</span>
          </div>
          <p class="text-sm font-medium text-slate-800">টেমপ্লেট ২</p>
          <p class="text-xs text-slate-500">কাউন্টডাউন অফার সহ সিঙ্গেল প্রোডাক্ট</p>
        </div>
      </label>

      <label class="template-option relative cursor-pointer">
        <input type="radio" name="template" value="3" class="peer sr-only" {{ (string) $selectedTemplate === '3' ? 'checked' : '' }}>
        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-brand peer-checked:ring-2 peer-checked:ring-brand/20 p-4 transition">
          <div class="h-20 rounded-lg bg-gradient-to-br from-blue-50 to-violet-100 flex items-center justify-center mb-3">
            <span class="text-xs font-semibold text-blue-700">Tech Spec Style</span>
          </div>
          <p class="text-sm font-medium text-slate-800">টেমপ্লেট ৩</p>
          <p class="text-xs text-slate-500">স্পেসিফিকেশনসহ গ্যাজেট/ইলেকট্রনিক্স</p>
        </div>
      </label>
    </div>
  </div>

  <!-- Basic info -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <h3 class="font-semibold text-slate-900 mb-4">বেসিক তথ্য</h3>
    <div class="grid sm:grid-cols-2 gap-5">
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">প্রোডাক্টের নাম *</label>
        <input type="text" name="name" id="nameInput" value="{{ old('name', $product->name) }}" required
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">স্লাগ (URL)</label>
        <input type="text" name="slug" id="slugInput" value="{{ old('slug', $product->slug) }}" placeholder="স্বয়ংক্রিয়ভাবে তৈরি হবে"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">ক্যাটাগরি</label>
        <select name="category_id" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          <option value="">ক্যাটাগরি বাছাই করুন (ঐচ্ছিক)</option>
          @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
            <option value="{{ $cat->id }}" {{ (string) old('category_id', $product->category_id) === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">শর্ট সাবটাইটেল</label>
        <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" placeholder="যেমন: আভিজাত্যের প্রতীক"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">বিস্তারিত বিবরণ</label>
        <textarea name="description" rows="3"
                  class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">{{ old('description', $product->description) }}</textarea>
      </div>
    </div>
  </div>

  <!-- Pricing -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <h3 class="font-semibold text-slate-900 mb-4">প্রাইসিং ও অফার</h3>
    <div class="grid sm:grid-cols-3 gap-5">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">রেগুলার মূল্য (৳) *</label>
        <input type="number" step="0.01" name="regular_price" value="{{ old('regular_price', $product->regular_price) }}" required
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">অফার মূল্য (৳)</label>
        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">স্টক (পিস)</label>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">ফ্রি গিফট টেক্সট (ঐচ্ছিক)</label>
        <input type="text" name="free_gift_text" value="{{ old('free_gift_text', $product->free_gift_text) }}" placeholder="যেমন: ফ্রি গিফট: ম্যাচিং পকেট স্কয়ার"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">অফার শেষ হবে (কাউন্টডাউন)</label>
        <input type="datetime-local" name="offer_ends_at" value="{{ old('offer_ends_at', optional($product->offer_ends_at)->format('Y-m-d\TH:i')) }}"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div class="sm:col-span-3">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">স্ট্যাটাস</label>
        <select name="status" class="w-full sm:w-64 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
          <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>ড্রাফট</option>
          <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>একটিভ (লাইভ)</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Images -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <h3 class="font-semibold text-slate-900 mb-4">ছবি</h3>
    <div class="grid sm:grid-cols-2 gap-5">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">মেইন ছবি</label>
        @if ($product->image)
          <img src="{{ $product->image_url ?: asset($product->image) }}" class="h-20 w-20 rounded-lg object-cover mb-2 border border-slate-200">
        @endif
        <input type="file" name="image" accept="image/*"
               class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">গ্যালারি (একাধিক ছবি)</label>
        @if ($product->exists && $product->images->count())
          <div class="flex flex-wrap gap-3 mb-2">
            @foreach ($product->images as $img)
              <div class="relative group">
                <img src="{{ $img->image_url ?: asset($img->path) }}" class="h-14 w-14 rounded-lg object-cover border border-slate-200">
                <button type="submit" form="delete-gallery-img-{{ $img->id }}"
                        onclick="return confirm('এই গ্যালারির ছবিটি মুছে ফেলতে চান?')"
                        class="absolute -top-1.5 -right-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow"
                        title="মুছুন">
                  &times;
                </button>
              </div>
            @endforeach
          </div>
        @endif
        <input type="file" name="gallery[]" accept="image/*" multiple
               class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
        <p class="text-xs text-slate-400 mt-1">নতুন ছবি অ্যাড করলে আগের গ্যালারিতে যোগ হবে।</p>
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5"><i class="fa-brands fa-youtube text-red-600 text-xs mr-1"></i> ইউটিউব ভিডিও লিংক (ঐচ্ছিক)</label>
        <input type="url" name="youtube_url" value="{{ old('youtube_url', $product->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=..."
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        <p class="text-xs text-slate-400 mt-1">লিংক দিলে ল্যান্ডিং পেজে প্রোডাক্টের ভিডিও দেখা যাবে।</p>
      </div>
    </div>
  </div>

  <!-- Variants (template 1 & 3) -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 template-only-1 template-only-3">
    <h3 class="font-semibold text-slate-900 mb-4">সাইজ ও রঙ</h3>
    <div class="grid sm:grid-cols-2 gap-5">
      <div class="template-only-1">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">সাইজ (কমা দিয়ে আলাদা করুন)</label>
        <input type="text" name="sizes" value="{{ old('sizes', $product->sizes ? implode(',', $product->sizes) : '') }}" placeholder="M,L,XL,XXL,3XL"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">রঙ (কমা দিয়ে আলাদা করুন)</label>
        <input type="text" name="colors" value="{{ old('colors', $product->colors ? implode(',', $product->colors) : '') }}" placeholder="কালো,মেরুন,নেভি ব্লু"
               class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
      </div>
    </div>
  </div>

  <!-- Benefits (template 1 & 2) -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 template-only-1 template-only-2">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-900">বেনিফিট / ফিচার পয়েন্ট</h3>
      <button type="button" onclick="addBenefitRow()" class="text-sm font-medium text-brand hover:underline">+ পয়েন্ট যোগ করুন</button>
    </div>
    <div id="benefitsWrapper" class="space-y-3"></div>
  </div>

  <!-- Specs (template 3) -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 template-only-3">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-900">স্পেসিফিকেশন</h3>
      <button type="button" onclick="addSpecRow()" class="text-sm font-medium text-brand hover:underline">+ স্পেক যোগ করুন</button>
    </div>
    <div id="specsWrapper" class="space-y-3"></div>
  </div>

  <!-- Why buy from us (all templates) -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <div class="flex items-center justify-between mb-1">
      <h3 class="font-semibold text-slate-900">কেন আমাদের কাছে কিনবেন</h3>
      <button type="button" onclick="addTrustPointRow()" class="text-sm font-medium text-brand hover:underline">+ পয়েন্ট যোগ করুন</button>
    </div>
    <p class="text-xs text-slate-400 mb-4">এই প্রোডাক্টের ল্যান্ডিং পেজে দেখানো হবে এমন আস্থার পয়েন্ট।</p>
    <div id="trustPointsWrapper" class="space-y-3"></div>
  </div>

  <!-- Reviews (all templates) -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-900">কাস্টমার রিভিউ</h3>
      <button type="button" onclick="addReviewRow()" class="text-sm font-medium text-brand hover:underline">+ রিভিউ যোগ করুন</button>
    </div>
    <div id="reviewsWrapper" class="space-y-3"></div>
  </div>

  <!-- Related products (all templates) -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <h3 class="font-semibold text-slate-900 mb-1">সম্পর্কিত প্রোডাক্ট</h3>
    <p class="text-xs text-slate-400 mb-4">এই প্রোডাক্টের ল্যান্ডিং পেজে যেসব প্রোডাক্ট "সম্পর্কিত প্রোডাক্ট" হিসেবে দেখানো হবে, সেগুলো বাছাই করুন।</p>

    @if ($availableProducts->isEmpty())
      <p class="text-sm text-slate-400">আর কোনো প্রোডাক্ট নেই।</p>
    @else
      <input type="text" id="relatedProductSearch" placeholder="প্রোডাক্টের নাম দিয়ে খুঁজুন..."
             class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">

      <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-lg divide-y divide-slate-100">
        @foreach ($availableProducts as $related)
          <label class="related-product-row flex items-center gap-3 px-3 py-2.5 hover:bg-slate-50 cursor-pointer" data-name="{{ mb_strtolower($related->name) }}">
            <input type="checkbox" name="related_products[]" value="{{ $related->id }}" class="accent-brand rounded"
                   {{ in_array($related->id, $selectedRelatedIds) ? 'checked' : '' }}>
            @if ($related->image)
              <img src="{{ $related->image_url ?: asset($related->image) }}" class="h-9 w-9 rounded-lg object-cover border border-slate-200">
            @else
              <div class="h-9 w-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300">
                <i class="fa-solid fa-image text-sm"></i>
              </div>
            @endif
            <div class="min-w-0 flex-1">
              <p class="text-sm text-slate-800 truncate">{{ $related->name }}</p>
              <p class="text-xs text-slate-400">৳{{ number_format((float) ($related->sale_price ?? $related->regular_price), 0) }}</p>
            </div>
          </label>
        @endforeach
      </div>
    @endif
  </div>

  <!-- Cart offers (all templates) -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
    <div class="flex items-center justify-between mb-1">
      <h3 class="font-semibold text-slate-900">কার্ট অফার (Spend &amp; Save)</h3>
      <button type="button" onclick="addCartOfferRow()" class="text-sm font-medium text-brand hover:underline">+ অফার যোগ করুন</button>
    </div>
    <p class="text-xs text-slate-400 mb-4">কাস্টমার কার্টে একটা নির্দিষ্ট পরিমাণ টাকা যুক্ত করলে ফ্রি ডেলিভারি অথবা ছাড় দেখাতে চাইলে এখানে যুক্ত করুন। যত বেশি অ্যামাউন্টের অফার, তত বড় ছাড় প্রযোজ্য হবে।</p>
    <div id="cartOffersWrapper" class="space-y-3"></div>
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors">
      {{ $isEdit ? 'আপডেট করুন' : 'প্রোডাক্ট সেভ করুন' }}
    </button>
    <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">বাতিল করুন</a>
  </div>
</form>

@if ($product->exists && $product->images->count())
  @foreach ($product->images as $img)
    <form id="delete-gallery-img-{{ $img->id }}" method="POST" action="{{ route('admin.products.gallery.destroy', $img) }}" class="hidden">
      @csrf
      @method('DELETE')
    </form>
  @endforeach
@endif

@push('scripts')
<script>
  // ---- Template toggle ----
  function applyTemplateVisibility() {
    var selected = document.querySelector('input[name="template"]:checked');
    var value = selected ? selected.value : '1';
    document.querySelectorAll('[class*="template-only-"]').forEach(function (el) {
      var visible = el.classList.contains('template-only-' + value);
      el.classList.toggle('hidden', !visible);
    });
  }
  document.querySelectorAll('input[name="template"]').forEach(function (radio) {
    radio.addEventListener('change', applyTemplateVisibility);
  });
  applyTemplateVisibility();

  // ---- Related products search filter ----
  var relatedSearch = document.getElementById('relatedProductSearch');
  if (relatedSearch) {
    relatedSearch.addEventListener('input', function () {
      var term = relatedSearch.value.trim().toLowerCase();
      document.querySelectorAll('.related-product-row').forEach(function (row) {
        row.classList.toggle('hidden', term !== '' && row.dataset.name.indexOf(term) === -1);
      });
    });
  }

  // ---- Slug auto-fill ----
  var nameInput = document.getElementById('nameInput');
  var slugInput = document.getElementById('slugInput');
  var slugEdited = {{ $isEdit ? 'true' : 'false' }};
  slugInput.addEventListener('input', function () { slugEdited = true; });
  nameInput.addEventListener('input', function () {
    if (slugEdited) return;
    slugInput.value = nameInput.value
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9ঀ-৿\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-');
  });

  // ---- Repeaters ----
  var benefitIndex = 0;
  function addBenefitRow(title, text) {
    var i = benefitIndex++;
    var wrap = document.getElementById('benefitsWrapper');
    var row = document.createElement('div');
    row.className = 'grid sm:grid-cols-5 gap-3 items-start border border-slate-100 rounded-lg p-3';
    row.innerHTML =
      '<input type="text" name="benefits['+i+'][title]" value="'+(title ? title.replace(/"/g,'&quot;') : '')+'" placeholder="টাইটেল (ঐচ্ছিক)" class="sm:col-span-1 border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '<input type="text" name="benefits['+i+'][text]" value="'+(text ? text.replace(/"/g,'&quot;') : '')+'" placeholder="বেনিফিট টেক্সট" class="sm:col-span-3 border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '<button type="button" onclick="this.closest(\'div.grid\').remove()" class="text-red-500 hover:text-red-700 text-sm justify-self-start sm:justify-self-center">মুছুন</button>';
    wrap.appendChild(row);
  }

  var specIndex = 0;
  function addSpecRow(label, value) {
    var i = specIndex++;
    var wrap = document.getElementById('specsWrapper');
    var row = document.createElement('div');
    row.className = 'grid sm:grid-cols-5 gap-3 items-start border border-slate-100 rounded-lg p-3';
    row.innerHTML =
      '<input type="text" name="specs['+i+'][label]" value="'+(label ? label.replace(/"/g,'&quot;') : '')+'" placeholder="যেমন: ব্যাটারি ব্যাকআপ" class="sm:col-span-2 border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '<input type="text" name="specs['+i+'][value]" value="'+(value ? value.replace(/"/g,'&quot;') : '')+'" placeholder="যেমন: ৩০ ঘন্টা" class="sm:col-span-2 border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '<button type="button" onclick="this.closest(\'div.grid\').remove()" class="text-red-500 hover:text-red-700 text-sm justify-self-start sm:justify-self-center">মুছুন</button>';
    wrap.appendChild(row);
  }

  var reviewIndex = 0;
  function addReviewRow(name, location, rating, comment) {
    var i = reviewIndex++;
    rating = rating || 5;
    var wrap = document.getElementById('reviewsWrapper');
    var row = document.createElement('div');
    row.className = 'border border-slate-100 rounded-lg p-3 space-y-2';
    row.innerHTML =
      '<div class="grid sm:grid-cols-4 gap-3">' +
      '<input type="text" name="reviews['+i+'][customer_name]" value="'+(name ? name.replace(/"/g,'&quot;') : '')+'" placeholder="কাস্টমারের নাম" class="sm:col-span-2 border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '<input type="text" name="reviews['+i+'][location]" value="'+(location ? location.replace(/"/g,'&quot;') : '')+'" placeholder="এলাকা" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '<select name="reviews['+i+'][rating]" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
        [5,4,3,2,1].map(function(r){ return '<option value="'+r+'" '+(String(rating)===String(r)?'selected':'')+'>'+r+' স্টার</option>'; }).join('') +
      '</select>' +
      '</div>' +
      '<div class="flex gap-3">' +
      '<textarea name="reviews['+i+'][comment]" rows="2" placeholder="রিভিউ কমেন্ট" class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm">'+(comment || '')+'</textarea>' +
      '<button type="button" onclick="this.closest(\'div.space-y-2\').remove()" class="text-red-500 hover:text-red-700 text-sm">মুছুন</button>' +
      '</div>';
    wrap.appendChild(row);
  }

  var trustPointIndex = 0;
  function addTrustPointRow(text) {
    var i = trustPointIndex++;
    var wrap = document.getElementById('trustPointsWrapper');
    var row = document.createElement('div');
    row.className = 'flex gap-3 items-start border border-slate-100 rounded-lg p-3';
    row.innerHTML =
      '<input type="text" name="trust_points['+i+'][text]" value="'+(text ? text.replace(/"/g,'&quot;') : '')+'" placeholder="যেমন: সারা বাংলাদেশে ক্যাশ অন হোম ডেলিভারী সুবিধা।" class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '<button type="button" onclick="this.closest(\'div.flex\').remove()" class="text-red-500 hover:text-red-700 text-sm shrink-0">মুছুন</button>';
    wrap.appendChild(row);
  }

  var cartOfferIndex = 0;
  function addCartOfferRow(minAmount, rewardType, discountAmount) {
    var i = cartOfferIndex++;
    rewardType = rewardType || 'free_delivery';
    var wrap = document.getElementById('cartOffersWrapper');
    var row = document.createElement('div');
    row.className = 'grid sm:grid-cols-5 gap-3 items-start border border-slate-100 rounded-lg p-3';
    row.innerHTML =
      '<div class="sm:col-span-2">' +
        '<label class="block text-xs text-slate-500 mb-1">কার্ট অ্যামাউন্ট (৳ এর উপরে)</label>' +
        '<input type="number" step="0.01" min="0" name="cart_offers['+i+'][min_cart_amount]" value="'+(minAmount || '')+'" placeholder="যেমন: 500" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '</div>' +
      '<div class="sm:col-span-1">' +
        '<label class="block text-xs text-slate-500 mb-1">পুরষ্কার</label>' +
        '<select name="cart_offers['+i+'][reward_type]" onchange="toggleCartOfferDiscount(this)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
          '<option value="free_delivery"'+(rewardType === 'free_delivery' ? ' selected' : '')+'>ফ্রি ডেলিভারি</option>' +
          '<option value="discount"'+(rewardType === 'discount' ? ' selected' : '')+'>ফিক্সড ছাড়</option>' +
        '</select>' +
      '</div>' +
      '<div class="sm:col-span-1 discount-amount-wrap" style="'+(rewardType === 'discount' ? '' : 'display:none')+'">' +
        '<label class="block text-xs text-slate-500 mb-1">ছাড়ের পরিমাণ (৳)</label>' +
        '<input type="number" step="0.01" min="0" name="cart_offers['+i+'][discount_amount]" value="'+(discountAmount || '')+'" placeholder="যেমন: 100" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">' +
      '</div>' +
      '<div class="sm:col-span-1 flex items-end h-full">' +
        '<button type="button" onclick="this.closest(\'div.grid\').remove()" class="text-red-500 hover:text-red-700 text-sm">মুছুন</button>' +
      '</div>';
    wrap.appendChild(row);
  }

  function toggleCartOfferDiscount(select) {
    var wrap = select.closest('div.grid').querySelector('.discount-amount-wrap');
    wrap.style.display = select.value === 'discount' ? '' : 'none';
  }

  // ---- Preload existing data ----
  var existingBenefits = @json($existingBenefits);
  var existingReviews = @json($existingReviews);
  var existingTrustPoints = @json($existingTrustPoints);
  var existingSpecs = @json($existingSpecs);
  var existingCartOffers = @json($existingCartOffers);

  existingBenefits.forEach(function (b) { addBenefitRow(b.title, b.text); });
  existingReviews.forEach(function (r) { addReviewRow(r.customer_name, r.location, r.rating, r.comment); });
  existingSpecs.forEach(function (s) { addSpecRow(s.label, s.value); });
  existingTrustPoints.forEach(function (t) { addTrustPointRow(t.text); });
  existingCartOffers.forEach(function (o) { addCartOfferRow(o.min_cart_amount, o.reward_type, o.discount_amount); });

  if (existingReviews.length === 0) addReviewRow();
</script>
@endpush
