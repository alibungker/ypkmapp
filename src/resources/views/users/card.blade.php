@extends('layouts.app')
@section('title', 'Kartu Anggota YPKM')
@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
body{background:#0f172a;font-family:'Plus Jakarta Sans',system-ui,sans-serif}
.print-area{width:320px;height:500px;background:white;color:#0f172a;border-radius:20px;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,.35);position:relative;display:flex;flex-direction:column;border:1px solid #e2e8f0;flex-shrink:0}
.rainbow-bar{height:8px;background:linear-gradient(90deg,#B71C1C 0%,#D32F2F 35%,#E65100 50%,#2E7D32 65%,#1565C0 85%,#5E35B1 100%);flex-shrink:0}
.holo{position:absolute;inset:0;z-index:5;pointer-events:none;background:linear-gradient(115deg,transparent 40%,rgba(255,255,255,.22) 48%,rgba(255,255,255,.05) 52%,transparent 60%)}
.logo-7petal{width:34px;height:34px;flex-shrink:0;overflow:hidden}
.logo-7petal svg{width:100%!important;height:100%!important;max-width:34px;max-height:34px;display:block}
/* ID Front */
.id-header{padding:10px 14px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #f1f5f9;position:relative;z-index:10}
.id-header h2{font-size:7.5px;font-weight:800;text-transform:uppercase;color:#0f172a;line-height:1.15;margin:0}
.id-header h2:first-child{letter-spacing:.2px}
.id-role{background:#fee2e2;color:#991b1b;font-size:7.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:4px 12px;text-align:center;position:relative;z-index:10;border-bottom:1px solid #fecaca}
.id-body{padding:16px;flex:1;display:flex;flex-direction:column;align-items:center;position:relative;z-index:10}
.photo{width:96px;height:108px;border-radius:16px;padding:3px;background:linear-gradient(135deg,#b91c1c,#e65100,#1565c0);margin-bottom:12px}
.photo img{width:100%;height:100%;object-fit:cover;border-radius:14px}
.photo-ph{width:100%;height:100%;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:28px;background:#e5e7eb}
.id-body h3{font-size:12px;font-weight:800;text-transform:uppercase;color:#0f172a;letter-spacing:.3px;margin:0 0 2px}
.id-kode{font-family:'JetBrains Mono',monospace;font-size:8.5px;font-weight:700;color:#b91c1c;letter-spacing:.5px;margin:0 0 10px}
.id-meta{display:flex;gap:6px;align-items:center;font-size:7px;color:#64748a;font-weight:500}
.id-meta b{color:#0f172a}
.id-footer{padding:8px 14px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #f1f5f9;position:relative;z-index:10}
.id-footer small{font-size:5.5px;color:#94a3b8;font-weight:600;line-height:1.3}
.qr-wrap{width:36px;height:36px;border:2px solid #e2e8f0;border-radius:6px;overflow:hidden;flex-shrink:0}
.qr-wrap img{width:100%;height:100%}
/* ID Back */
.id-back-header{padding:10px 14px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #f1f5f9;position:relative;z-index:10}
.id-back-body{padding:12px 16px;flex:1;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:10}
.id-back-body h4{font-size:8px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;text-align:center;color:#0f172a;margin:0 0 8px}
.id-back-body p{font-size:7px;color:#475569;line-height:1.5;margin:0 0 4px}
.id-back-footer{padding:10px 14px;border-top:1px solid #e2e8f0;text-align:center;position:relative;z-index:10}
.id-back-footer p{font-size:6px;color:#94a3b8;margin:0;font-weight:600}
.id-back-footer p:first-child{font-size:7px;font-weight:800;color:#0f172a}
/* Kartu Nama */
.biz-area{width:400px;height:240px;background:white;border-radius:20px;overflow:hidden;box-shadow:0 20px 40px -10px rgba(0,0,0,.3);position:relative;border:1px solid #e2e8f0;flex-shrink:0}
.biz-header{padding:14px 16px;display:flex;align-items:flex-start;gap:10px;position:relative;z-index:10}
.biz-header h3{font-size:11px;font-weight:800;margin:0;color:#0f172a}
.biz-header p{font-size:8px;color:#b91c1b;font-weight:600;margin:1px 0}
.biz-header small{font-family:'JetBrains Mono',monospace;font-size:7px;color:#94a3b8;font-weight:500}
.biz-badge{background:#b91c1c;color:white;font-size:7px;font-weight:800;letter-spacing:.3px;padding:3px 8px;border-radius:4px;position:relative;z-index:10;flex-shrink:0}
.biz-body{padding:0 16px;flex:1;display:flex;flex-direction:column;gap:3px;position:relative;z-index:10}
.biz-row{display:flex;align-items:center;gap:6px;font-size:7.5px;color:#475569}
.biz-row svg{width:12px!important;height:12px!important;flex-shrink:0;color:#b91c1c}
.biz-footer{padding:8px 16px;border-top:1px solid #f1f5f9;display:flex;align-items:center;gap:6px;position:relative;z-index:10}
.biz-footer small{font-size:6px;color:#94a3b8;font-weight:600}
/* Biz Back */
.biz-back-header{padding:10px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:6px;position:relative;z-index:10}
.biz-back-body{padding:14px 16px;flex:1;display:flex;flex-direction:column;justify-content:center;gap:4px;position:relative;z-index:10}
.biz-back-body p{font-size:7.5px;color:#475569;margin:0;line-height:1.4}
.biz-back-body p b{color:#0f172a}
/* Print */
.btn-print{background:#0f172a;color:white;border:none;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
.btn-print:hover{background:#1e293b}
.btn-print svg{width:16px;height:16px}
@media print{body{background:white!important;padding:0!important;margin:0!important} .no-print{display:none!important} .print-area,.biz-area{box-shadow:none;border:1px solid #e5e7eb}}
</style>
@endsection

@section('content')
@php
    $user = auth()->user();
    $fotoUrl = $user->foto ? asset('storage/'.$user->foto) : '';
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='.urlencode('https://crm.peduli.ypkm.info/verify/'.$user->kode_keanggotaan);
    $validThru = date('d M Y', strtotime('+1 year'));
    $roleLabel = match($user->role) {
        'super_admin' => 'Super Admin', 'pengurus' => 'Pengurus',
        'bendahara' => 'Bendahara', 'staff' => 'Staf',
        'staff_keuangan' => 'Staf Keuangan', 'relawan' => 'Relawan Kemanusiaan',
        'ketua_kelompok' => 'Ketua Kelompok', default => ucwords(str_replace('_',' ',$user->role))
    };
@endphp

<div style="max-width:960px;margin:0 auto;padding:32px 20px">
    <div class="no-print" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:20px;font-weight:800;color:white;margin:0">Kartu Anggota YPKM</h1>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0">Cetak kartu keanggotaan dan kartu nama</p>
        </div>
        <div style="display:flex;gap:8px">
            <button onclick="printAll()" class="btn-print">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak Semua
            </button>
        </div>
    </div>

    <div id="printContent">
    <!-- ========== KARTU KEANGGOTAAN DEPAN ========== -->
    <div class="no-print" style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin:0 0 8px">Kartu Keanggotaan — Depan</div>
    <div class="print-area" style="margin-bottom:40px">
        <div class="rainbow-bar"></div>
        <div class="holo"></div>

        <div class="id-header">
            <div class="logo-7petal" style="width:34px;height:34px;flex-shrink:0;overflow:hidden">
                <svg viewBox="0 0 100 100" style="width:100%;height:100%;max-width:34px;max-height:34px;display:block">
                    <g transform="translate(50,50)">
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#D32F2F"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#1565C0" transform="rotate(51.43)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#2E7D32" transform="rotate(102.86)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#FBC02D" transform="rotate(154.29)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#0288D1" transform="rotate(205.71)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#5E35B1" transform="rotate(257.14)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#E65100" transform="rotate(308.57)"/>
                        <circle cx="0" cy="0" r="9" fill="#FFF"/>
                    </g>
                </svg>
            </div>
            <div style="flex:1">
                <h2 style="margin:0">YAYASAN PELANGI</h2>
                <h2 style="margin:0">KESEJAHTERAAN MASYARAKAT</h2>
                <div style="height:2px;background:linear-gradient(90deg,#b91c1c,#16a34a,#1565c0);border-radius:4px;margin-top:5px"></div>
            </div>
        </div>

        <div class="id-role">{{ strtoupper($roleLabel) }}</div>

        <div class="id-body">
            <div class="photo">
                @if($user->foto)
                    <img src="{{ $fotoUrl }}" alt="{{ $user->name }}">
                @else
                    <div class="photo-ph">{{ strtoupper(substr($user->name,0,1)) }}</div>
                @endif
            </div>
            <h3>{{ $user->name }}</h3>
            <p class="id-kode">{{ $user->kode_keanggotaan ?? '-' }}</p>
            <div class="id-meta">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <b style="color:{{ $user->is_active ? '#10b981' : '#dc2626' }}">{{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}</b>
                <span style="color:#e2e8f0">|</span>
                Exp: <b>{{ $validThru }}</b>
            </div>
        </div>

        <div class="id-footer">
            <div>
                <small>PINDAI VERIFIKASI</small><br>
                <small style="font-weight:800;color:#0f172a">crm.peduli.ypkm.info</small>
            </div>
            <div class="qr-wrap">
                <img src="{{ $qrUrl }}" alt="QR">
            </div>
        </div>
    </div>

    <!-- ========== KARTU KEANGGOTAAN BELAKANG ========== -->
    <div class="no-print" style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin:0 0 8px">Kartu Keanggotaan — Belakang</div>
    <div class="print-area" style="margin-bottom:50px">
        <div class="rainbow-bar"></div>
        <div class="holo"></div>

        <div class="id-back-header" style="justify-content:center">
            <div class="logo-7petal" style="width:34px;height:34px;flex-shrink:0;overflow:hidden">
                <svg viewBox="0 0 100 100" style="width:100%;height:100%;max-width:34px;max-height:34px;display:block">
                    <g transform="translate(50,50)">
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#D32F2F"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#1565C0" transform="rotate(51.43)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#2E7D32" transform="rotate(102.86)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#FBC02D" transform="rotate(154.29)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#0288D1" transform="rotate(205.71)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#5E35B1" transform="rotate(257.14)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#E65100" transform="rotate(308.57)"/>
                        <circle cx="0" cy="0" r="9" fill="#FFF"/>
                    </g>
                </svg>
            </div>
            <h4>KETENTUAN KEANGGOTAAN</h4>
        </div>

        <div class="id-back-body">
            <p>1. Kartu ini merupakan bukti sah keanggotaan Yayasan Pelangi Kesejahteraan Masyarakat (YPKM).</p>
            <p>2. Wajib dibawa saat bertugas dalam aksi resmi kemanusiaan.</p>
            <p>3. Dilarang menyalahgunakan kartu untuk kepentingan pribadi.</p>
            <p>4. Apabila menemukan kartu ini, harap kembalikan ke sekretariat YPKM terdekat.</p>
        </div>

        <div class="id-back-footer">
            <p>YAYASAN PELANGI KESEJAHTERAAN MASYARAKAT</p>
            <p>Jl. Jenderal Sudirman No. 10, Banda Aceh — Call Center: 0852-6082-8894</p>
        </div>
    </div>

    <!-- ========== KARTU NAMA DEPAN ========== -->
    <div class="no-print" style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin:0 0 8px">Kartu Nama — Depan</div>
    <div class="biz-area" style="margin-bottom:40px">
        <div class="rainbow-bar"></div>
        <div class="holo"></div>
        <div class="biz-header">
            <div class="logo-7petal" style="width:34px;height:34px;flex-shrink:0;overflow:hidden">
                <svg viewBox="0 0 100 100" style="width:100%;height:100%;max-width:34px;max-height:34px;display:block">
                    <g transform="translate(50,50)">
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#D32F2F"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#1565C0" transform="rotate(51.43)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#2E7D32" transform="rotate(102.86)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#FBC02D" transform="rotate(154.29)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#0288D1" transform="rotate(205.71)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#5E35B1" transform="rotate(257.14)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#E65100" transform="rotate(308.57)"/>
                        <circle cx="0" cy="0" r="9" fill="#FFF"/>
                    </g>
                </svg>
            </div>
            <div style="flex:1">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->jabatan ?? $roleLabel }}</p>
                <small>ID: {{ $user->kode_keanggotaan ?? '-' }}</small>
            </div>
            <span class="biz-badge">YPKM</span>
        </div>
        <div class="biz-body" style="padding-bottom:8px">
            <div class="biz-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                <span>{{ $user->phone ?: '-' }}</span>
            </div>
            <div class="biz-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span>{{ $user->email }}</span>
            </div>
            <div class="biz-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
                <span>{{ $user->wilayahLabel() }}</span>
            </div>
        </div>
    </div>

    <!-- ========== KARTU NAMA BELAKANG ========== -->
    <div class="no-print" style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin:0 0 8px">Kartu Nama — Belakang</div>
    <div class="biz-area" style="margin-bottom:24px">
        <div class="rainbow-bar"></div>
        <div class="holo"></div>
        <div class="biz-back-header">
            <div class="logo-7petal" style="width:34px;height:34px;flex-shrink:0;overflow:hidden">
                <svg viewBox="0 0 100 100" style="width:100%;height:100%;max-width:34px;max-height:34px;display:block">
                    <g transform="translate(50,50)">
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#D32F2F"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#1565C0" transform="rotate(51.43)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#2E7D32" transform="rotate(102.86)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#FBC02D" transform="rotate(154.29)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#0288D1" transform="rotate(205.71)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#5E35B1" transform="rotate(257.14)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#E65100" transform="rotate(308.57)"/>
                        <circle cx="0" cy="0" r="9" fill="#FFF"/>
                    </g>
                </svg>
            </div>
            <h4>YAYASAN PELANGI KESEJAHTERAAN MASYARAKAT</h4>
        </div>
        <div class="biz-back-body">
            <p>Bergerak di bidang sosial kemanusiaan, pemberdayaan, dan keagamaan.</p>
            <p>Transparansi dan akuntabilitas adalah komitmen utama kami.</p>
            <p>Kunjungi portal resmi untuk laporan kegiatan & donasi: <b>peduli.ypkm.info</b></p>
        </div>
        <div class="biz-footer">
            <div class="logo-7petal" style="width:20px;height:20px;flex-shrink:0;overflow:hidden">
                <svg viewBox="0 0 100 100" style="width:100%;height:100%;max-width:34px;max-height:34px;display:block">
                    <g transform="translate(50,50)">
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#D32F2F"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#1565C0" transform="rotate(51.43)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#2E7D32" transform="rotate(102.86)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#FBC02D" transform="rotate(154.29)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#0288D1" transform="rotate(205.71)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#5E35B1" transform="rotate(257.14)"/>
                        <path d="M 0,-7 C 8,-20 22,-36 34,-28 C 40,-24 35,-14 30,-10 C 36,-6 32,2 24,6 C 14,10 6,2 0,-7 Z" fill="#E65100" transform="rotate(308.57)"/>
                        <circle cx="0" cy="0" r="9" fill="#FFF"/>
                    </g>
                </svg>
            </div>
            <small>peduli.ypkm.info · BSI 734 471 1897 · YPKM</small>
        </div>
    </div>

    </div>
</div>

<script>
function printAll() {
    window.print();
}
</script>
@endsection
