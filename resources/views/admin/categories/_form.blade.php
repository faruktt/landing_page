@php $isEdit = $category->exists; @endphp

@if ($errors->any())
  <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
    <ul class="list-disc list-inside space-y-0.5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 max-w-xl space-y-5">
  @csrf
  @if ($isEdit) @method('PUT') @endif

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">ক্যাটাগরির নাম *</label>
    <input type="text" name="name" id="nameInput" value="{{ old('name', $category->name) }}" required
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">স্লাগ (URL)</label>
    <input type="text" name="slug" id="slugInput" value="{{ old('slug', $category->slug) }}" placeholder="স্বয়ংক্রিয়ভাবে তৈরি হবে"
           class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">ছবি</label>
    @if ($category->image)
      <img src="{{ asset('storage/'.$category->image) }}" class="h-16 w-16 rounded-lg object-cover mb-2 border border-slate-200">
    @endif
    <input type="file" name="image" accept="image/*"
           class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-brand file:text-white file:text-xs">
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-brand hover:bg-brandDark text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm transition-colors">
      {{ $isEdit ? 'আপডেট করুন' : 'ক্যাটাগরি সেভ করুন' }}
    </button>
    <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">বাতিল করুন</a>
  </div>
</form>

<script>
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
</script>
