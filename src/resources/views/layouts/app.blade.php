<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#00034a">
    <title>@yield('title', 'PEDULI YPKM')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#00034a',green:'#017723',gold:'#e5a820'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
    <style>
        :root{--navy:#00034a;--green:#017723;--gold:#e5a820;--canvas:#f5f6fa;--line:#e5e7eb;--muted:#667085;--sidebar-width:260px}
        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif;background:var(--canvas);color:#151728;margin:0;overflow-x:hidden}
        .skip-link{position:fixed;left:16px;top:-80px;z-index:100;background:white;color:var(--navy);padding:10px 14px;border-radius:8px;font-weight:700;box-shadow:0 8px 24px rgba(0,3,74,.18)}
        .skip-link:focus{top:16px}
        .sidebar{background:var(--navy);position:fixed;inset:0 auto 0 0;width:var(--sidebar-width);height:100vh;height:100dvh;z-index:60;display:flex;flex-direction:column;overflow:hidden;transition:transform .24s ease;box-shadow:8px 0 28px rgba(0,3,74,.08)}
        .sidebar-brand{flex:0 0 auto;padding:20px;border-bottom:1px solid rgba(255,255,255,.1)}
        .sidebar-nav{flex:1 1 auto;min-height:0;overflow-y:auto;overscroll-behavior:contain;padding:12px 12px 20px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.14) transparent}
        .sidebar-nav::-webkit-scrollbar{width:4px}.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.14);border-radius:2px}
        .sidebar-footer{flex:0 0 auto;padding:14px 16px calc(14px + env(safe-area-inset-bottom));border-top:1px solid rgba(255,255,255,.08);background:var(--navy);box-shadow:0 -10px 24px rgba(0,3,74,.2)}
        .sidebar-close{display:none;position:absolute;right:12px;top:14px;width:40px;height:40px;border:0;border-radius:9px;background:rgba(255,255,255,.08);color:white;font-size:22px;cursor:pointer}
        .main-content{margin-left:var(--sidebar-width);min-height:100vh;transition:margin-left .24s ease}
        .content-shell{padding:24px;max-width:1600px;margin:0 auto}
        .topbar{background:white;border-bottom:1px solid var(--line);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:40;min-height:82px}
        .topbar-leading,.topbar-user{display:flex;align-items:center;gap:12px;min-width:0}
        .mobile-menu-button{display:none;width:44px;height:44px;flex:0 0 44px;border:1px solid var(--line);border-radius:10px;background:white;color:var(--navy);font-size:21px;cursor:pointer}
        .sidebar-overlay{position:fixed;inset:0;background:rgba(0,3,74,.52);backdrop-filter:blur(2px);z-index:55;opacity:0;visibility:hidden;transition:opacity .2s ease,visibility .2s ease}
        .nav-item{display:flex;align-items:center;gap:11px;min-height:44px;padding:10px 12px;margin:2px 0;border-radius:9px;color:rgba(255,255,255,.72);font-size:14px;font-weight:500;transition:background .15s,color .15s,transform .15s}
        .ui-icon{display:inline-block;vertical-align:middle;flex:0 0 auto}
        .nav-item:hover{color:white;background:rgba(255,255,255,.07);transform:translateX(2px)}
        .nav-item.active{color:var(--gold);background:rgba(255,255,255,.1);font-weight:600}
        .nav-item:focus-visible,.btn:focus-visible,a:focus-visible,button:focus-visible{outline:3px solid rgba(229,168,32,.8);outline-offset:2px}
        .card{background:white;border-radius:12px;border:1px solid var(--line);box-shadow:0 1px 3px rgba(0,3,74,.05);min-width:0;max-width:100%}
        .mobile-stack>*,.mobile-two>*,.peta-grid>*{min-width:0}
        .card-header{padding:16px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
        .card-body{padding:16px 20px}
        .stat-card{padding:20px;border-radius:12px;border:1px solid var(--line);background:white;min-width:0}
        .stat-value{font-size:28px;font-weight:800;color:var(--navy);letter-spacing:-.5px;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}
        .stat-label{font-size:13px;color:var(--muted);margin-top:2px}
        .badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap}.badge-green{background:#e8f5ec;color:var(--green)}.badge-gold{background:#fef7e6;color:#9a6b0d}.badge-navy{background:#e8e8f0;color:var(--navy)}
        .table-wrap{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-data{width:100%;border-collapse:collapse;font-size:14px}.table-data th{text-align:left;padding:12px 8px;border-bottom:2px solid var(--line);color:var(--muted);font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}.table-data td{padding:12px 8px;border-bottom:1px solid var(--line);vertical-align:middle}.table-data tr:hover td{background:#f8f9fc}
        table.dataTable thead th{background:var(--navy)!important;color:white!important;border-bottom:3px solid var(--gold)!important}table.dataTable tbody tr:nth-child(even){background:#f5f7fb}table.dataTable tbody tr:hover{background:#fff8e8}.dt-container{padding:12px 4px}.dt-container .dt-layout-row{gap:12px}.dt-container .dt-search input,.dt-container .dt-length select{border:1.5px solid #cbd5e1!important;border-radius:8px!important;padding:7px 10px!important;background:white!important}.dt-container .dt-search input:focus{outline:none;border-color:var(--navy)!important;box-shadow:0 0 0 3px rgba(0,3,74,.08)}.dt-container .dt-paging .dt-paging-button{border-radius:7px!important}.dt-container .dt-paging .dt-paging-button.current{background:var(--navy)!important;color:white!important;border-color:var(--navy)!important}.dt-info{font-size:12px;color:var(--muted)}
        .table-data td:last-child a,.table-data td:last-child button,.transaction-table td:last-child a,.transaction-table td:last-child button{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:6px 11px;margin:2px 3px;border:1px solid #d8dce6;border-radius:8px;background:white;color:var(--navy);font-size:12px;font-weight:700;text-decoration:none;cursor:pointer;transition:.15s}.table-data td:last-child a:hover,.table-data td:last-child button:hover,.transaction-table td:last-child a:hover,.transaction-table td:last-child button:hover{border-color:var(--navy);background:#f0f1f8;transform:translateY(-1px)}.table-data td:last-child form:last-child button,.transaction-table td:last-child form:last-child button{border-color:#fecaca;color:#b42318;background:#fff7f7}.table-data td:last-child form:last-child button:hover,.transaction-table td:last-child form:last-child button:hover{background:#fee2e2;border-color:#ef4444}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:40px;padding:9px 18px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:background .15s,color .15s,transform .15s;font-family:inherit;text-decoration:none}.btn:active{transform:translateY(1px)}.btn-primary{background:var(--navy);color:white}.btn-primary:hover{background:#171b63}.btn-outline{background:white;border:1.5px solid var(--line);color:#1a1a2e}.btn-outline:hover{border-color:var(--navy);color:var(--navy)}.btn-sm{padding:6px 13px;font-size:13px;min-height:36px}
        .form-input{width:100%;min-height:42px;padding:10px 14px;border:1.5px solid var(--line);border-radius:8px;font-size:14px;font-family:inherit;transition:border-color .15s,box-shadow .15s;background:white}.form-input:focus{outline:none;border-color:var(--navy);box-shadow:0 0 0 3px rgba(0,3,74,.08)}.form-label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#1a1a2e}
        .progress-bar{height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden}.progress-fill{height:100%;border-radius:4px;transition:width .3s}.alert{padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:16px}.alert-success{background:#e8f5ec;border:1px solid #c6e6d0;color:var(--green)}
        .mobile-card-list{display:none}.desktop-table{display:block}.filter-grid{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.filter-grid .form-input{width:auto;min-width:145px}.filter-advanced summary{list-style:none;display:inline-flex;align-items:center;min-height:40px;padding:8px 14px;border:1.5px solid var(--line);border-radius:8px;background:white;color:#1a1a2e;font-size:13px;font-weight:600;cursor:pointer}.filter-advanced summary::-webkit-details-marker{display:none}.filter-advanced summary::after{content:'+';margin-left:8px;color:var(--muted)}.filter-advanced[open]{flex-basis:100%}.filter-advanced[open] summary::after{content:'−'}.filter-advanced__grid{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;padding:12px;background:#f8f9fc;border:1px solid var(--line);border-radius:10px}.button-row{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.mobile-only{display:none}
        @media (max-width: 1023px){
            .sidebar{width:min(300px,88vw);transform:translateX(-105%);box-shadow:18px 0 48px rgba(0,3,74,.3)}body.sidebar-open .sidebar{transform:translateX(0)}body.sidebar-open .sidebar-overlay{opacity:1;visibility:visible}body.sidebar-open{overflow:hidden;touch-action:none}
            .sidebar-close,.mobile-menu-button{display:inline-flex;align-items:center;justify-content:center}.main-content{margin-left:0}.desktop-user-name{display:none}.content-shell{padding:18px}.topbar{padding:12px 18px;min-height:70px}
            .mobile-stack{grid-template-columns:repeat(2,minmax(0,1fr))!important}
            .desktop-table{display:none}.mobile-card-list{display:grid;gap:10px}
            .mobile-data-card{border:1px solid var(--line);border-radius:10px;padding:14px;background:white}
            .mobile-data-card__title{font-weight:700;color:var(--navy);margin-bottom:5px}.mobile-data-card__meta{font-size:13px;color:var(--muted);line-height:1.55}.mobile-data-card__actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:12px;padding-top:10px;border-top:1px solid var(--line)}
        }
        @media (max-width: 767px){
            .content-shell{padding:14px}.content-shell [style*="display:grid"]{grid-template-columns:1fr!important}.topbar{padding:10px 14px}.topbar h1{font-size:16px!important;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px}.topbar p{display:none}.topbar-user>div{width:32px!important;height:32px!important}.mobile-stack{grid-template-columns:1fr!important}.content-shell .mobile-two{grid-template-columns:repeat(2,minmax(0,1fr))!important}.stat-card{padding:16px}.stat-value{font-size:24px}.card-header,.card-body{padding:14px}.filter-grid{display:grid;grid-template-columns:1fr}.filter-grid .form-input{width:100%!important;min-width:0}.filter-advanced{width:100%}.filter-advanced summary{width:100%;justify-content:space-between;min-height:44px}.filter-advanced__grid{display:grid;grid-template-columns:1fr;padding:10px}.button-row .btn{flex:1}.table-data{min-width:720px}.desktop-table{display:none}.mobile-card-list{display:grid;gap:10px}.mobile-data-card{border:1px solid var(--line);border-radius:10px;padding:14px;background:white}.mobile-data-card__title{font-weight:700;color:var(--navy);margin-bottom:5px}.mobile-data-card__meta{font-size:13px;color:var(--muted);line-height:1.55}.mobile-data-card__actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:12px;padding-top:10px;border-top:1px solid var(--line)}.mobile-only{display:block}.hide-mobile{display:none!important}.btn{min-height:44px}.form-input{min-height:44px}
        }
        @media (max-width: 420px){.topbar h1{max-width:185px}.stat-value{font-size:22px}}
        @media (prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;scroll-behavior:auto!important;transition-duration:.01ms!important}}
    </style>
    @yield('styles')
</head>
<body>
<a href="#main-content" class="skip-link">Lewati ke konten utama</a>
<div id="sidebar-overlay" class="sidebar-overlay" aria-hidden="true"></div>
<aside id="app-sidebar" class="sidebar" aria-label="Navigasi utama" aria-hidden="true" inert>
    <button id="sidebar-close" class="sidebar-close" type="button" aria-label="Tutup navigasi">×</button>
    <div class="sidebar-brand"><div class="flex items-center gap-3"><img src="{{ asset('img/logo-ypkm-transparent.png') }}" alt="Logo YPKM" class="w-10 h-10" style="object-fit:contain;"><div><div class="font-bold text-lg text-white">PEDULI</div><div class="text-xs text-white/50">YPKM</div></div></div></div>
    <nav class="sidebar-nav">
        @if(auth()->check() && auth()->user()->isRelawan())
        <a href="{{ route('relawan.verifikasi') }}" class="nav-item {{ request()->routeIs('relawan*') ? 'active' : '' }}" @if(request()->routeIs('relawan*')) aria-current="page" @endif><x-icon name="users"/><span>Data & Validasi Penerima</span></a>
        <a href="{{ route('peta.index') }}" class="nav-item {{ request()->routeIs('peta*') ? 'active' : '' }}" @if(request()->routeIs('peta*')) aria-current="page" @endif><x-icon name="map"/><span>Peta Distribusi</span></a>
        @else
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif><x-icon name="dashboard"/><span>Dashboard</span></a>
        <a href="{{ route('penerima.index') }}" class="nav-item {{ request()->routeIs('penerima*') ? 'active' : '' }}" @if(request()->routeIs('penerima*')) aria-current="page" @endif><x-icon name="users"/><span>Penerima</span></a>
        <a href="{{ route('kelompok.index') }}" class="nav-item {{ request()->routeIs('kelompok*') ? 'active' : '' }}" @if(request()->routeIs('kelompok*')) aria-current="page" @endif><x-icon name="group"/><span>Kelompok</span></a>
        <a href="{{ route('distribusi.index') }}" class="nav-item {{ request()->routeIs('distribusi*') ? 'active' : '' }}" @if(request()->routeIs('distribusi*')) aria-current="page" @endif><x-icon name="truck"/><span>Distribusi</span></a>
        <a href="{{ route('peta.index') }}" class="nav-item {{ request()->routeIs('peta*') ? 'active' : '' }}" @if(request()->routeIs('peta*')) aria-current="page" @endif><x-icon name="map"/><span>Peta</span></a>
        @if(auth()->check() && auth()->user()->canViewKeuangan())
        <div style="margin:14px 8px 5px;font-size:11px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;">Keuangan</div>
        @if(auth()->user()->isBendahara())
        <a href="{{ route('keuangan.laporan-saya') }}" class="nav-item {{ request()->routeIs('keuangan.laporan-saya*') ? 'active' : '' }}"><x-icon name="wallet"/><span>Laporan Keuangan Saya</span></a>
        @else
        <a href="{{ route('keuangan.index') }}" class="nav-item {{ request()->routeIs('keuangan.index') ? 'active' : '' }}"><x-icon name="wallet"/><span>Keuangan</span></a>
        <a href="{{ route('keuangan.topup.index') }}" class="nav-item {{ request()->routeIs('keuangan.topup*') ? 'active' : '' }}"><x-icon name="wallet"/><span>Top-up Anggaran</span></a>
        <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan*') ? 'active' : '' }}"><x-icon name="report"/><span>Laporan</span></a>
        @endif
        @endif
        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
        <div style="margin:14px 8px 5px;font-size:11px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;">Administrasi</div>
        <a href="{{ route('barang.index') }}" class="nav-item {{ request()->routeIs('barang*') ? 'active' : '' }}"><x-icon name="box"/><span>Barang &amp; Kegiatan</span></a>
        <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.index') ? 'active' : '' }}"><x-icon name="users"/><span>Pengguna</span></a>
        <a href="{{ route('crm.index') }}" class="nav-item {{ request()->routeIs('crm*') ? 'active' : '' }}"><x-icon name="group"/><span>CRM Yayasan</span></a>
        @endif
        @endauth
    <div class="sidebar-footer">
        @auth<div style="padding:0 8px 10px;display:flex;align-items:center;gap:8px;"><div style="width:30px;height:30px;background:rgba(255,255,255,.1);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:13px;color:white;">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div><div style="min-width:0;"><div style="font-size:13px;color:white;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div><div style="font-size:11px;color:var(--gold);">{{ ['super_admin'=>'Super Admin','admin'=>'Admin','pengurus'=>'Pengurus','bendahara'=>'Bendahara','staff'=>'Staff','staff_keuangan'=>'Staf Keuangan','relawan'=>'Relawan','ketua_kelompok'=>'Ketua Kelompok'][auth()->user()->role] ?? auth()->user()->role }}</div></div></div>@endauth
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="flex items-center gap-3 text-white/60 hover:text-white w-full" style="padding:8px;border-radius:8px;font-size:14px;" type="submit"><x-icon name="logout"/><span>Keluar</span></button></form>
    </div>
</aside>
<div class="main-content">
    <header class="topbar">
        <div class="topbar-leading"><button id="mobile-menu-button" class="mobile-menu-button" type="button" aria-label="Buka navigasi" aria-controls="app-sidebar" aria-expanded="false">☰</button><div style="min-width:0;"><h1 style="font-size:18px;font-weight:700;color:var(--navy);margin:0;">@yield('title', 'Dashboard')</h1><p style="font-size:13px;color:var(--muted);margin:2px 0 0;">@yield('subtitle', '')</p></div></div>
        <div class="topbar-user">@auth<span class="desktop-user-name" style="font-size:14px;color:var(--muted);">{{ auth()->user()->name }}</span><div style="width:36px;height:36px;background:#e8e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--navy);font-weight:700;font-size:14px;">{{ substr(auth()->user()->name,0,1) }}</div>@endauth</div>
    </header>
    <main id="main-content" class="content-shell" tabindex="-1">
        @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
        @yield('content')
    </main>
</div>
<script>
(() => {
    const body = document.body;
    const menuButton = document.getElementById('mobile-menu-button');
    const closeButton = document.getElementById('sidebar-close');
    const overlay = document.getElementById('sidebar-overlay');
    const sidebar = document.getElementById('app-sidebar');
    const mainArea = document.querySelector('.main-content');
    const mobileQuery = window.matchMedia('(max-width: 1023px)');
    let drawerOpen = false;

    const focusable = () => Array.from(sidebar?.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])') ?? [])
        .filter(element => !element.hasAttribute('hidden'));

    const syncAccessibility = (value = false, restoreFocus = false) => {
        const mobile = mobileQuery.matches;
        drawerOpen = mobile && value;
        body.classList.toggle('sidebar-open', drawerOpen);
        menuButton?.setAttribute('aria-expanded', String(drawerOpen));
        overlay?.setAttribute('aria-hidden', String(!drawerOpen));

        if (mobile) {
            sidebar?.setAttribute('aria-hidden', String(!drawerOpen));
            if (sidebar) sidebar.inert = !drawerOpen;
            if (mainArea) mainArea.inert = drawerOpen;
        } else {
            sidebar?.removeAttribute('aria-hidden');
            if (sidebar) sidebar.inert = false;
            if (mainArea) mainArea.inert = false;
        }

        if (drawerOpen) {
            (focusable()[0] ?? closeButton)?.focus();
        } else if (restoreFocus && mobile) {
            menuButton?.focus();
        }
    };

    menuButton?.addEventListener('click', () => syncAccessibility(true));
    closeButton?.addEventListener('click', () => syncAccessibility(false, true));
    overlay?.addEventListener('click', () => syncAccessibility(false, true));
    sidebar?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        if (mobileQuery.matches) syncAccessibility(false);
    }));

    document.addEventListener('keydown', event => {
        if (!drawerOpen) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            syncAccessibility(false, true);
            return;
        }
        if (event.key !== 'Tab') return;

        const items = focusable();
        if (!items.length) return;
        const first = items[0], last = items[items.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    const handleViewportChange = () => syncAccessibility(false);
    mobileQuery.addEventListener?.('change', handleViewportChange);
    syncAccessibility(false);
})();
</script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const excluded = new Set(['penerimaTable','topupTable','pengeluaranTable']);
    document.querySelectorAll('table.table-data,table.transaction-table').forEach(function(table, index) {
        if (excluded.has(table.id) || table.dataset.noDatatable === 'true') return;
        if (!table.id) table.id = 'globalDataTable' + index;
        const bodyRows = table.querySelectorAll('tbody tr');
        if (!bodyRows.length || (bodyRows.length === 1 && bodyRows[0].querySelector('td[colspan]'))) return;
        const columnCount = table.querySelectorAll('thead tr:first-child th').length;
        const lastHeader = table.querySelector('thead tr:first-child th:last-child');
        const actionColumn = lastHeader && /aksi|action/i.test(lastHeader.textContent.trim()) ? [columnCount - 1] : [];
        try {
            new DataTable(table, {
                pageLength: 20,
                lengthMenu: [[10,20,50,-1],[10,20,50,'Semua']],
                order: [],
                columnDefs: actionColumn.length ? [{targets: actionColumn, orderable:false, searchable:false}] : [],
                language: {
                    search:'Cari semua:', lengthMenu:'Tampilkan _MENU_ data',
                    info:'Menampilkan _START_–_END_ dari _TOTAL_ data', infoEmpty:'Tidak ada data',
                    infoFiltered:'(disaring dari _MAX_ data)', zeroRecords:'Data tidak ditemukan', emptyTable:'Belum ada data',
                    paginate:{first:'Pertama',last:'Terakhir',next:'Berikutnya',previous:'Sebelumnya'}
                }
            });
        } catch (error) { console.warn('DataTables dilewati:', table.id, error.message); }
    });
});
</script>
@yield('scripts')
</body>
</html>
