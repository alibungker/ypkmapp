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
    <style>
        body{font-family:'Inter',sans-serif;background:#f5f6fa;}
        .sidebar{background:#00034a;position:fixed;top:0;left:0;bottom:0;width:260px;z-index:50;overflow-y:auto;}
        .sidebar::-webkit-scrollbar{width:4px;}
        .sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.1);border-radius:2px;}
        .main-content{margin-left:260px;min-height:100vh;}
        .nav-item{display:flex;align-items:center;gap:10px;padding:10px 16px;margin:2px 8px;border-radius:8px;color:rgba(255,255,255,0.6);font-size:14px;font-weight:500;transition:all 0.15s;}
        .nav-item:hover{color:white;background:rgba(255,255,255,0.05);}
        .nav-item.active{color:#e5a820;background:rgba(255,255,255,0.08);font-weight:600;}
        .card{background:white;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,3,74,0.05);}
        .stat-card{padding:20px;border-radius:12px;border:1px solid #e5e7eb;background:white;}
        .stat-value{font-size:28px;font-weight:800;color:#00034a;letter-spacing:-0.5px;}
        .stat-label{font-size:13px;color:#6b7280;margin-top:2px;}
        .badge{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;}
        .badge-green{background:#e8f5ec;color:#017723;}
        .badge-gold{background:#fef7e6;color:#b07d14;}
        .badge-navy{background:#e8e8f0;color:#00034a;}
        .table-data{width:100%;border-collapse:collapse;font-size:14px;}
        .table-data th{text-align:left;padding:12px 8px;border-bottom:2px solid #e5e7eb;color:#6b7280;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;}
        .table-data td{padding:12px 8px;border-bottom:1px solid #e5e7eb;}
        .table-data tr:hover td{background:#f5f6fa;}
        .btn {display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:all 0.15s;font-family:inherit;}
        .btn-primary{background:#00034a;color:white;}
        .btn-primary:hover{background:#1a1e5e;}
        .btn-outline{background:white;border:1.5px solid #e5e7eb;color:#1a1a2e;}
        .btn-outline:hover{border-color:#00034a;color:#00034a;}
        .btn-sm{padding:6px 14px;font-size:13px;}
        .form-input{width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;transition:border-color 0.15s;background:white;}
        .form-input:focus{outline:none;border-color:#00034a;box-shadow:0 0 0 3px rgba(0,3,74,0.08);}
        .form-label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:#1a1a2e;}
        .progress-bar{height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden;}
        .progress-fill{height:100%;border-radius:4px;transition:width 0.3s;}
        .alert{padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:16px;}
        .alert-success{background:#e8f5ec;border:1px solid #c6e6d0;color:#017723;}
        @@media(max-width:768px){.sidebar{width:200px;}.main-content{margin-left:200px;}}
    </style>
    @yield('styles')
</head>
<body>
    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="p-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/logo-ypkm.jpg') }}" alt="Logo YPKM" class="w-10 h-10 rounded-lg" style="object-fit:cover;background:white;">
                <div>
                    <div class="font-bold text-lg text-white">PEDULI</div>
                    <div class="text-xs text-white/50">YPKM</div>
                </div>
            </div>
        </div>
        <nav class="p-3" style="padding-bottom:80px;">
            @if(auth()->check() && auth()->user()->isRelawan())
            {{-- RELAWAN: Verifikasi & Validasi digabung --}}
            <a href="{{ route('relawan.verifikasi') }}" class="nav-item {{ request()->routeIs('relawan*') ? 'active' : '' }}">📋 <span>Data & Validasi Penerima</span></a>
            <a href="{{ route('peta.index') }}" class="nav-item {{ request()->routeIs('peta*') ? 'active' : '' }}">🗺️ <span>Peta Distribusi</span></a>
            @else
            {{-- ADMIN & KETUA KELOMPOK: full navigasi --}}
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 <span>Dashboard</span></a>
            <a href="{{ route('penerima.index') }}" class="nav-item {{ request()->routeIs('penerima*') ? 'active' : '' }}">👥 <span>Penerima</span></a>
            <a href="{{ route('kelompok.index') }}" class="nav-item {{ request()->routeIs('kelompok*') ? 'active' : '' }}">📋 <span>Kelompok</span></a>
            <a href="{{ route('distribusi.index') }}" class="nav-item {{ request()->routeIs('distribusi*') ? 'active' : '' }}">🚚 <span>Distribusi</span></a>
            <a href="{{ route('peta.index') }}" class="nav-item {{ request()->routeIs('peta*') ? 'active' : '' }}">🗺️ <span>Peta</span></a>
            @if(auth()->check() && auth()->user()->isAdmin())
            <div style="margin:12px 8px 4px;font-size:11px;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:1px;">Admin</div>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">👥 <span>Users</span></a>
            <a href="{{ route('keuangan.index') }}" class="nav-item {{ request()->routeIs('keuangan*') ? 'active' : '' }}">💰 <span>Keuangan</span></a>
            <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan*') ? 'active' : '' }}">📄 <span>Laporan</span></a>
            @endif
            @endif
        </nav>
        <div style="position:absolute;bottom:0;left:0;right:0;padding:16px;border-top:1px solid rgba(255,255,255,0.08);">
            @auth
            <div style="padding:0 8px 10px;display:flex;align-items:center;gap:8px;">
                <div style="width:30px;height:30px;background:rgba(255,255,255,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;color:white;">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                <div style="min-width:0;">
                    <div style="font-size:13px;color:white;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div style="font-size:11px;color:#e5a820;">{{ ['admin'=>'👑 Admin','relawan'=>'🤝 Relawan','ketua_kelompok'=>'👤 Ketua Kelompok'][auth()->user()->role] ?? auth()->user()->role }}</div>
                </div>
            </div>
            @endauth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 text-white/40 hover:text-white w-full" style="padding:8px;border-radius:8px;font-size:14px;">
                    🚪 <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="main-content">
        {{-- Header --}}
        <div style="background:white;border-bottom:1px solid #e5e7eb;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:40;">
            <div>
                <h1 style="font-size:18px;font-weight:700;color:#00034a;">@yield('title', 'Dashboard')</h1>
                <p style="font-size:13px;color:#6b7280;margin-top:2px;">@yield('subtitle', '')</p>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                @auth
                <span style="font-size:14px;color:#6b7280;">{{ auth()->user()->name }}</span>
                <div style="width:36px;height:36px;background:#e8e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#00034a;font-weight:700;font-size:14px;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                @endauth
            </div>
        </div>

        {{-- Content --}}
        <div style="padding:24px;">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>
</html>
