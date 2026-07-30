<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PEDULI YPKM')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#00034a',green:'#017723',gold:'#e5a820'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 bg-navy text-white flex-shrink-0 overflow-y-auto">
            <div class="p-5 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gold rounded-lg flex items-center justify-center font-black text-navy">P</div>
                    <div>
                        <div class="font-bold text-lg">PEDULI</div>
                        <div class="text-xs text-white/60">YPKM</div>
                    </div>
                </div>
            </div>
            <nav class="p-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white/10 text-gold font-semibold' : 'text-white/70 hover:bg-white/5' }}">
                    📊 <span>Dashboard</span>
                </a>
                <a href="{{ route('penerima.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('penerima*') ? 'bg-white/10 text-gold font-semibold' : 'text-white/70 hover:bg-white/5' }}">
                    👥 <span>Penerima</span>
                </a>
                <a href="{{ route('kelompok.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('kelompok*') ? 'bg-white/10 text-gold font-semibold' : 'text-white/70 hover:bg-white/5' }}">
                    📋 <span>Kelompok</span>
                </a>
                <a href="{{ route('distribusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('distribusi*') ? 'bg-white/10 text-gold font-semibold' : 'text-white/70 hover:bg-white/5' }}">
                    🚚 <span>Distribusi</span>
                </a>
                <a href="" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5">
                    📦 <span>Barang</span>
                </a>
                <a href="{{ route('keuangan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('keuangan*') ? 'bg-white/10 text-gold font-semibold' : 'text-white/70 hover:bg-white/5' }}">
                    💰 <span>Keuangan</span>
                </a>
                <a href="{{ route('peta.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('peta*') ? 'bg-white/10 text-gold font-semibold' : 'text-white/70 hover:bg-white/5' }}">
                    🗺️ <span>Peta</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('laporan*') ? 'bg-white/10 text-gold font-semibold' : 'text-white/70 hover:bg-white/5' }}">
                    📄 <span>Laporan</span>
                </a>
            </nav>
            <div class="absolute bottom-0 w-64 p-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex items-center gap-3 text-white/50 hover:text-white w-full">
                        🚪 <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto">
            <header class="bg-white border-b px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-navy">@yield('title', 'Dashboard')</h1>
                    <p class="text-sm text-gray-500">@yield('subtitle', '')</p>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <div class="w-8 h-8 bg-navy/10 rounded-full flex items-center justify-center text-navy font-semibold text-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    @endauth
                </div>
            </header>

            {{-- Alert --}}
            @if(session('success'))
            <div class="mx-6 mt-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
