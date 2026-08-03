@extends('layouts.app')
@section('title', 'Kartu Anggota YPKM')
@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ===== Reset & Base ===== */
.card-page{--navy:#00034a;--navy-light:#1a1a5e;--gold:#e5a820;--green:#017723;--red:#D32F2F;max-width:900px;margin:0 auto;padding:32px 20px;font-family:'Plus Jakarta Sans',system-ui,sans-serif}
.card-page *{box-sizing:border-box}

/* ===== Toolbar ===== */
.card-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px}
.card-toolbar h1{font-size:22px;font-weight:800;color:#fff;margin:0}
.card-toolbar p{font-size:13px;color:#94a3b8;margin:4px 0 0}
.btn-card-print{background:linear-gradient(135deg,#00034a,#1a1a5e);color:#fff;border:none;padding:10px 22px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:transform .15s,box-shadow .15s}
.btn-card-print:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(0,3,74,.3)}
.btn-card-print svg{width:16px;height:16px}

/* ===== Card Specs ===== */
.id-card{width:320px;height:500px;background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 25px 60px -12px rgba(0,0,0,.4);position:relative;display:flex;flex-direction:column;border:1px solid #e2e8f0;flex-shrink:0}
.biz-card{width:400px;height:230px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 20px 50px -10px rgba(0,0,0,.35);position:relative;display:flex;flex-direction:column;border:1px solid #e2e8f0;flex-shrink:0}

/* ===== Rainbow Accent Bar ===== */
.accent-bar{height:6px;background:linear-gradient(90deg,#B71C1C 0%,#D32F2F 30%,#E65100 45%,#2E7D32 60%,#1565C0 80%,#5E35B1 100%);flex-shrink:0}

/* ===== Holographic Sheen ===== */
.holo{position:absolute;inset:0;z-index:5;pointer-events:none;background:linear-gradient(125deg,transparent 35%,rgba(255,255,255,.18) 47%,rgba(255,255,255,.04) 53%,transparent 65%)}

/* ===== Logo ===== */
.ypkm-logo{width:38px;height:38px;border-radius:8px;object-fit:cover;background:#fff;flex-shrink:0;border:1px solid #e2e8f0}
.ypkm-logo-sm{width:28px;height:28px;border-radius:6px;object-fit:cover;background:#fff;flex-shrink:0;border:1px solid #e2e8f0}

/* ===== ID Card: Header ===== */
.id-hdr{padding:12px 16px;display:flex;align-items:center;gap:10px;position:relative;z-index:10;background:linear-gradient(135deg,#00034a 0%,#1a1a5e 100%);color:#fff}
.id-hdr-text h2{font-size:7px;font-weight:800;text-transform:uppercase;letter-spacing:.3px;line-height:1.2;margin:0}
.id-hdr-text h2+div{height:2px;background:linear-gradient(90deg,#e5a820,transparent);margin-top:4px;width:80px;border-radius:2px}

/* ===== ID Card: Role Strip ===== */
.id-role-strip{background:#00034a;color:#e5a820;font-size:8px;font-weight:800;text-transform:uppercase;letter-spacing:1px;padding:5px 16px;text-align:center;position:relative;z-index:10}

/* ===== ID Card: Body ===== */
.id-body{padding:16px 16px 8px;flex:1;display:flex;flex-direction:column;align-items:center;position:relative;z-index:10}
.photo-frame{width:90px;height:104px;border-radius:14px;padding:3px;background:linear-gradient(135deg,#00034a,#e5a820);margin-bottom:10px;box-shadow:0 4px 12px rgba(0,3,74,.15)}
.photo-frame img{width:100%;height:100%;object-fit:cover;border-radius:12px}
.photo-ph{width:100%;height:100%;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:#00034a;background:#e5e7eb}
.id-name{font-size:13px;font-weight:800;text-transform:uppercase;color:#00034a;letter-spacing:.3px;margin:0}
.id-code{font-family:'JetBrains Mono',monospace;font-size:9px;font-weight:700;color:#D32F2F;letter-spacing:.8px;margin:3px 0 10px}
.id-status-row{display:flex;gap:8px;align-items:center;font-size:7.5px;color:#64748b;font-weight:500}
.id-status-badge{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:999px;font-size:7px;font-weight:700}
.id-status-active{background:#dcfce7;color:#166534}
.id-status-inactive{background:#fee2e2;color:#991b1b}
.id-dot{width:2px;height:2px;border-radius:50%;background:#cbd5e1}

/* ===== ID Card: Footer / QR ===== */
.id-ftr{padding:8px 16px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-top:1px solid #e2e8f0;position:relative;z-index:10}
.id-ftr-left{display:flex;flex-direction:column;gap:1px}
.id-ftr-left small{font-size:5.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.id-ftr-left b{font-size:6.5px;color:#00034a;font-weight:800}
.qr-box{width:34px;height:34px;border:1.5px solid #e2e8f0;border-radius:5px;overflow:hidden;flex-shrink:0}
.qr-box img{width:100%;height:100%}

/* ===== ID Card: Back ===== */
.id-back-hdr{padding:12px 16px;display:flex;align-items:center;gap:8px;position:relative;z-index:10;background:linear-gradient(135deg,#00034a,#1a1a5e);color:#fff}
.id-back-hdr h4{font-size:8px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;margin:0}
.id-back-body{padding:16px;flex:1;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:10}
.id-back-body .rule{font-size:7px;color:#475569;line-height:1.6;margin:0 0 6px;display:flex;gap:5px}
.id-back-body .rule-num{font-weight:800;color:#e5a820;flex-shrink:0}
.id-back-ftr{padding:8px 16px;background:#00034a;color:#94a3b8;position:relative;z-index:10;text-align:center}
.id-back-ftr p{font-size:6px;margin:0;font-weight:600;line-height:1.4}
.id-back-ftr p:first-child{font-size:7px;font-weight:800;color:#e5a820;margin-bottom:2px}

/* ===== Business Card: Front ===== */
.biz-hdr{padding:14px 18px;display:flex;align-items:flex-start;gap:10px;position:relative;z-index:10}
.biz-hdr-info{flex:1;min-width:0}
.biz-hdr-info h3{font-size:12px;font-weight:800;color:#00034a;margin:0;letter-spacing:.2px}
.biz-hdr-info p{font-size:8px;color:#D32F2F;font-weight:700;margin:1px 0;letter-spacing:.3px}
.biz-hdr-info small{font-family:'JetBrains Mono',monospace;font-size:7px;color:#94a3b8;font-weight:500;display:block;margin-top:1px}
.biz-badge{background:linear-gradient(135deg,#00034a,#1a1a5e);color:#e5a820;font-size:7px;font-weight:800;letter-spacing:.5px;padding:4px 10px;border-radius:6px;position:relative;z-index:10;flex-shrink:0}
.biz-body{padding:4px 18px 8px;flex:1;display:flex;flex-direction:column;gap:4px;position:relative;z-index:10}
.biz-contact-row{display:flex;align-items:center;gap:6px;font-size:8px;color:#475569}
.biz-contact-row svg{width:12px!important;height:12px!important;flex-shrink:0;color:#D32F2F}
.biz-contact-row span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* ===== Business Card: Back ===== */
.biz-back-hdr{padding:10px 18px;display:flex;align-items:center;gap:8px;border-bottom:1px solid #f1f5f9;position:relative;z-index:10}
.biz-back-hdr h4{font-size:8px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:#00034a;margin:0}
.biz-back-body{padding:14px 18px;flex:1;display:flex;flex-direction:column;justify-content:center;gap:5px;position:relative;z-index:10}
.biz-back-body p{font-size:8px;color:#475569;margin:0;line-height:1.5}
.biz-back-body p b{color:#00034a}
.biz-back-ftr{padding:6px 18px;background:linear-gradient(135deg,#00034a,#1a1a5e);display:flex;align-items:center;justify-content:space-between;position:relative;z-index:10}
.biz-back-ftr small{font-size:6px;color:#94a3b8;font-weight:600}
.biz-back-ftr small b{color:#e5a820}

/* ===== Section Labels ===== */
.card-label{font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin:0 0 8px;display:flex;align-items:center;gap:6px}
.card-label::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,#e2e8f0,transparent)}

/* ===== Layout ===== */
.card-stack{display:flex;flex-direction:column;align-items:center;gap:12px}

/* ===== Print ===== */
@media print{
  body{background:#fff!important;margin:0!important;padding:0!important}
  .card-page{padding:0!important;max-width:none!important}
  .no-print{display:none!important}
  .id-card,.biz-card{box-shadow:none!important;border:1px solid #e5e7eb!important;page-break-inside:avoid}
  .card-stack{gap:0!important}
  .card-label{display:none!important}
}
</style>
@endsection

@section('content')
@php
    $u = auth()->user();
    $logoUrl = asset('img/logo-ypkm-card.jpg');
    $fotoUrl = $u->foto ? asset('storage/'.$u->foto) : '';
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data='.urlencode('https://peduli.ypkm.info/verify/'.$u->kode_keanggotaan);
    $validThru = \Carbon\Carbon::now()->addYear()->locale('id')->translatedFormat('d M Y');
    $roleLabel = match($u->role) {
        'super_admin' => 'Super Admin', 'pengurus' => 'Pengurus',
        'bendahara' => 'Bendahara', 'staff' => 'Staf',
        'staff_keuangan' => 'Staf Keuangan', 'relawan' => 'Relawan Kemanusiaan',
        'ketua_kelompok' => 'Ketua Kelompok', default => ucwords(str_replace('_',' ',$u->role))
    };
@endphp

<div class="card-page">
    {{-- Toolbar --}}
    <div class="no-print card-toolbar">
        <div>
            <h1>Kartu Anggota YPKM</h1>
            <p>Cetak kartu keanggotaan &amp; kartu nama</p>
        </div>
        <button onclick="window.print()" class="btn-card-print">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Semua
        </button>
    </div>

    <div class="card-stack">

        {{-- ===== ID CARD: FRONT ===== --}}
        <div class="card-label">Kartu Keanggotaan — Depan</div>
        <div class="id-card">
            <div class="accent-bar"></div>
            <div class="holo"></div>

            <div class="id-hdr">
                <img src="{{ $logoUrl }}" alt="YPKM" class="ypkm-logo">
                <div class="id-hdr-text">
                    <h2>Yayasan Pelangi</h2>
                    <h2>Kesejahteraan Masyarakat</h2>
                    <div></div>
                </div>
            </div>

            <div class="id-role-strip">{{ strtoupper($roleLabel) }}</div>

            <div class="id-body">
                <div class="photo-frame">
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="{{ $u->name }}">
                    @else
                        <div class="photo-ph">{{ strtoupper(substr($u->name,0,1)) }}</div>
                    @endif
                </div>
                <p class="id-name">{{ $u->name }}</p>
                <p class="id-code">{{ $u->kode_keanggotaan ?? '-' }}</p>
                <div class="id-status-row">
                    <span class="id-status-badge {{ $u->is_active ? 'id-status-active' : 'id-status-inactive' }}">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><circle cx="12" cy="12" r="10" fill="currentColor"/></svg>
                        {{ $u->is_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                    <span class="id-dot"></span>
                    <span>Berlaku s/d <b style="color:#00034a">{{ $validThru }}</b></span>
                </div>
            </div>

            <div class="id-ftr">
                <div class="id-ftr-left">
                    <small>Pindai untuk verifikasi</small>
                    <b>peduli.ypkm.info/verify</b>
                </div>
                <div class="qr-box">
                    <img src="{{ $qrUrl }}" alt="QR Code">
                </div>
            </div>
        </div>

        {{-- ===== ID CARD: BACK ===== --}}
        <div class="card-label">Kartu Keanggotaan — Belakang</div>
        <div class="id-card">
            <div class="holo"></div>

            <div class="id-back-hdr">
                <img src="{{ $logoUrl }}" alt="YPKM" class="ypkm-logo-sm" style="width:28px;height:28px;border-radius:6px;background:#fff;border:1px solid #334">
                <h4>Ketentuan Keanggotaan</h4>
            </div>

            <div class="id-back-body">
                <p class="rule"><span class="rule-num">1.</span><span>Kartu ini merupakan bukti sah keanggotaan Yayasan Pelangi Kesejahteraan Masyarakat (YPKM).</span></p>
                <p class="rule"><span class="rule-num">2.</span><span>Wajib dibawa saat bertugas dalam aksi resmi kemanusiaan.</span></p>
                <p class="rule"><span class="rule-num">3.</span><span>Dilarang menyalahgunakan kartu untuk kepentingan pribadi.</span></p>
                <p class="rule"><span class="rule-num">4.</span><span>Apabila menemukan kartu ini, harap kembalikan ke sekretariat YPKM terdekat.</span></p>
            </div>

            <div class="id-back-ftr">
                <p>YAYASAN PELANGI KESEJAHTERAAN MASYARAKAT</p>
                <p>Banda Aceh — Call Center: 0852-6082-8894 — peduli.ypkm.info</p>
            </div>
        </div>

        {{-- ===== BUSINESS CARD: FRONT ===== --}}
        <div class="card-label">Kartu Nama — Depan</div>
        <div class="biz-card">
            <div class="accent-bar"></div>
            <div class="holo"></div>

            <div class="biz-hdr">
                <img src="{{ $logoUrl }}" alt="YPKM" class="ypkm-logo">
                <div class="biz-hdr-info">
                    <h3>{{ $u->name }}</h3>
                    <p>{{ $u->jabatan ?? $roleLabel }}</p>
                    <small>{{ $u->kode_keanggotaan ?? '-' }}</small>
                </div>
                <span class="biz-badge">YPKM</span>
            </div>

            <div class="biz-body">
                <div class="biz-contact-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $u->phone ?: '-' }}</span>
                </div>
                <div class="biz-contact-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
                    <span>{{ $u->email }}</span>
                </div>
                <div class="biz-contact-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
                    <span>{{ $u->wilayahLabel() }}</span>
                </div>
            </div>
        </div>

        {{-- ===== BUSINESS CARD: BACK ===== --}}
        <div class="card-label">Kartu Nama — Belakang</div>
        <div class="biz-card">
            <div class="holo"></div>

            <div class="biz-back-hdr">
                <img src="{{ $logoUrl }}" alt="YPKM" class="ypkm-logo-sm">
                <h4>Yayasan Pelangi Kesejahteraan Masyarakat</h4>
            </div>

            <div class="biz-back-body">
                <p>Bergerak di bidang sosial kemanusiaan, pemberdayaan, dan keagamaan.</p>
                <p>Transparansi dan akuntabilitas adalah komitmen utama kami.</p>
                <p>Portal resmi: <b>peduli.ypkm.info</b></p>
            </div>

            <div class="biz-back-ftr">
                <img src="{{ $logoUrl }}" alt="YPKM" class="ypkm-logo-sm" style="width:18px;height:18px;border-radius:4px">
                <small><b>BSI</b> 734 471 1897 · peduli.ypkm.info</small>
            </div>
        </div>

    </div>
</div>
@endsection
