<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — {{ $siteSettings->site_name ?? 'BUNON' }}</title>
@if (!empty($siteSettings?->favicon))
  <link rel="icon" href="{{ $siteSettings?->favicon_url ?: asset($siteSettings->favicon) }}">
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: '#4f46e5',
          brandDark: '#3730a3',
        },
        fontFamily: {
          display: ['Poppins', 'sans-serif'],
          sans: ['"Hind Siliguri"', 'sans-serif'],
        },
      },
    },
  }
</script>
<style>
  body { font-family: 'Hind Siliguri', sans-serif; }
  .font-display { font-family: 'Poppins', sans-serif; }
  .glow { background: radial-gradient(closest-side, rgba(255,255,255,.18), transparent); filter: blur(30px); }
</style>
</head>

<body class="min-h-screen bg-slate-50 antialiased">

<div class="min-h-screen flex">

  <!-- Left brand panel -->
  <div class="hidden lg:flex lg:w-[45%] relative overflow-hidden bg-gradient-to-br from-brandDark via-brand to-indigo-500 text-white flex-col justify-between p-12">
    <div class="glow absolute -top-20 -left-16 h-72 w-72 rounded-full pointer-events-none"></div>
    <div class="glow absolute bottom-0 right-0 h-96 w-96 rounded-full pointer-events-none"></div>

    <div class="relative flex items-center gap-3">
      <span class="font-display text-xl font-bold">{{ $siteSettings->site_name ?? 'BUNON' }}</span>
    </div>

    <div class="relative">
      <h1 class="font-display text-3xl xl:text-4xl font-bold leading-tight mb-4">আপনার ইকমার্স ব্যবসা,<br>এক জায়গা থেকে নিয়ন্ত্রণ করুন।</h1>
      <p class="text-white/70 max-w-sm">প্রোডাক্ট, অর্ডার, ল্যান্ডিং পেজ, কাস্টমার — সবকিছু ম্যানেজ করুন একটি সহজ, শক্তিশালী অ্যাডমিন প্যানেল থেকে।</p>
    </div>

    <p class="relative text-xs text-white/50">&copy; {{ date('Y') }} {{ $siteSettings->site_name ?? 'BUNON' }}. All rights reserved.</p>
  </div>

  <!-- Right form panel -->
  <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
    <div class="w-full max-w-sm">

      <div class="lg:hidden flex items-center gap-3 mb-10 justify-center">
        <span class="font-display text-xl font-bold text-slate-900">{{ $siteSettings->site_name ?? 'BUNON' }}</span>
      </div>

      <h2 class="font-display text-2xl font-bold text-slate-900 mb-1">অ্যাডমিন লগইন</h2>
      <p class="text-slate-500 text-sm mb-8">আপনার অ্যাকাউন্টে সাইন ইন করুন</p>

      @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
        @csrf

        <div>
          <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">ইমেইল</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition"
                 placeholder="you@example.com">
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">পাসওয়ার্ড</label>
          <input id="password" type="password" name="password" required
                 class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition"
                 placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
            <input type="checkbox" name="remember" class="accent-brand rounded">
            মনে রাখুন
          </label>
        </div>

        <button type="submit" class="w-full bg-brand hover:bg-brandDark text-white font-semibold py-3 rounded-lg transition-colors shadow-lg shadow-brand/25">
          সাইন ইন
        </button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
