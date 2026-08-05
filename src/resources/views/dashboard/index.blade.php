@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan aktivitas penyaluran bantuan')

@section('content')
<style>
:root{--navy:#00034a;--gold:#d6b665;--green:#017723;--red:#b42318;--ink:#111827;--muted:#6b7280;--line:#e5e7eb}
.admin-kpis{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:24px}.admin-kpi{position:relative;overflow:hidden;background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px 18px 18px 22px;box-shadow:0 8px 24px rgba(0,3,74,.06);display:flex;align-items:center;gap:14px;animation:cardIn .55s both;transition:.22s}.admin-kpi:hover{transform:translateY(-4px);box-shadow:0 14px 32px rgba(0,3,74,.12)}.admin-kpi:before{content:"";position:absolute;inset:0 auto 0 0;width:5px;background:var(--accent)}.admin-kpi:nth-child(2){animation-delay:.06s}.admin-kpi:nth-child(3){animation-delay:.12s}.admin-kpi:nth-child(4){animation-delay:.18s}.admin-kpi:nth-child(5){animation-delay:.24s}.admin-kpi:nth-child(6){animation-delay:.3s}.kpi-icon{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;flex:none}.kpi-copy{min-width:0;flex:1}.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700}.kpi-value{font-size:22px;line-height:1.2;font-weight:800;color:var(--ink);margin-top:5px;white-space:nowrap}.mini-ring,.impact-ring{--p:0;position:relative;border-radius:50%;background:conic-gradient(var(--accent) calc(var(--p)*1%),#edf0f5 0);animation:ringIn 1.1s ease-out both}.mini-ring{width:42px;height:42px}.mini-ring:after,.impact-ring:after{content:"";position:absolute;inset:5px;border-radius:50%;background:#fff}.mini-ring span,.impact-ring span{position:absolute;inset:0;display:grid;place-items:center;z-index:1;font-size:9px;font-weight:800;color:var(--accent)}.viz-grid{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:20px;margin-bottom:24px}.premium-card{background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 8px 28px rgba(0,3,74,.06);overflow:hidden}.viz-head{padding:18px 20px;border-bottom:1px solid var(--line)}.viz-head h3{font-size:15px;font-weight:750;margin:0}.viz-head p{font-size:11px;color:var(--muted);margin:4px 0 0}.donut-layout{padding:22px;display:flex;align-items:center;justify-content:center;gap:28px}.donut{width:176px;height:176px;border-radius:50%;position:relative;flex:none;background:var(--donut);box-shadow:inset 0 0 0 1px rgba(0,0,0,.04);animation:ringIn 1.2s ease-out both}.donut:after{content:"";position:absolute;inset:35px;border-radius:50%;background:#fff;box-shadow:0 0 0 1px var(--line)}.donut-center{position:absolute;inset:0;display:grid;place-content:center;text-align:center;z-index:1;font-size:11px;color:var(--muted)}.donut-center strong{font-size:20px;color:var(--navy)}.legend{display:grid;gap:12px;min-width:190px}.legend-row{display:grid;grid-template-columns:10px 1fr;gap:9px;align-items:start}.legend-dot{width:9px;height:9px;border-radius:50%;margin-top:4px}.legend-label{font-size:11px;color:var(--muted)}.legend-value{font-size:13px;font-weight:700;color:var(--ink)}.bar-list{padding:18px 20px}.bar-row{display:grid;grid-template-columns:120px minmax(80px,1fr) 115px;gap:12px;align-items:center;margin:14px 0}.bar-name{font-size:12px;font-weight:650;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.bar-track{height:10px;background:#eef0f5;border-radius:20px;overflow:hidden}.bar-fill{height:100%;border-radius:20px;background:linear-gradient(90deg,#00034a,#686036,#d6b665);transform-origin:left;animation:grow 1s cubic-bezier(.2,.8,.2,1) both}.bar-value{text-align:right;font-size:11px;color:var(--muted);font-weight:650}.impact-ring{width:66px;height:66px;background:conic-gradient(#d6b665 calc(var(--p)*1%),rgba(255,255,255,.14) 0)}.impact-ring:after{inset:6px;background:#00034a}.impact-ring span{font-size:18px;color:#fff}.impact-ring small{display:block;font-size:8px;color:#d6b665}.animated-fill{transform-origin:left;animation:grow 1.1s ease-out both}.finance-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:20px}.finance-tile{border-radius:11px;padding:17px;text-align:center;border:1px solid rgba(255,255,255,.5);box-shadow:inset 0 1px rgba(255,255,255,.6)}
@keyframes grow{from{transform:scaleX(0)}to{transform:scaleX(1)}}@keyframes cardIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}@keyframes ringIn{from{opacity:.2;transform:rotate(-80deg) scale(.8)}to{opacity:1;transform:none}}
@media(max-width:1000px){.admin-kpis{grid-template-columns:repeat(2,1fr)}.viz-grid{grid-template-columns:1fr}.finance-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:640px){.admin-kpis{grid-template-columns:1fr}.donut-layout{flex-direction:column}.bar-row{grid-template-columns:90px 1fr}.bar-value{grid-column:2}.finance-grid{grid-template-columns:1fr}.kpi-value{font-size:19px}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
<div class="card" style="margin-bottom:20px;border-left:4px solid {{ $role === 'admin' ? '#00034a' : ($role === 'relawan' ? '#017723' : '#b07d14') }};">
    <div class="card-body" style="padding:14px 18px;">
        <div style="font-size:16px;font-weight:700;">
            @if($role === 'admin') Dashboard Admin
                @elseif($role === 'keuangan') Dashboard Keuangan
                @elseif($role === 'relawan') Dashboard Relawan
                @else Dashboard Staf
                @endif
        </div>
        <div style="font-size:13px;color:#667085;margin-top:3px;">
            @if($role === 'admin') Ringkasan seluruh data operasional YPKM.
            @elseif($role === 'keuangan') Ringkasan akuntabilitas dana dan pengeluaran pribadi Anda.
            @elseif($role === 'relawan') Data wilayah kerja: {{ $wilayahLabel ?: 'belum ditetapkan' }}.
            @else Data khusus kelompok: {{ $kelompokNama ?: 'belum terhubung' }}.
            @endif
        </div>
    </div>
</div>
{{-- Stats --}}
@if($role === 'keuangan')
@php
    $danaAwalSaya = ($stats['saldo_topup_saya'] ?? 0) + ($stats['total_biaya_saya'] ?? 0);
    $pemakaianSaya = $stats['pemakaian_dana_saya'] ?? 0;
@endphp
<div class="mobile-stack" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:20px;">
    <a href="{{ route('keuangan.laporan-saya') }}" class="stat-card" style="text-decoration:none;background:#00034a;color:white;border-top:3px solid #d6b665;">
        <div style="font-size:12px;color:rgba(255,255,255,.66);text-transform:uppercase;letter-spacing:.06em;">Saldo Tersedia</div>
        <div class="stat-value" style="color:white;margin-top:10px;font-size:25px;">Rp {{ number_format($stats['saldo_topup_saya'] ?? 0,0,',','.') }}</div>
        <div style="font-size:12px;color:#d6b665;margin-top:6px;">Dari Rp {{ number_format($danaAwalSaya,0,',','.') }} dana disetujui</div>
    </a>
    <a href="{{ route('keuangan.laporan-saya') }}" class="stat-card" style="text-decoration:none;border-top:3px solid #b07d14;">
        <div style="font-size:12px;color:#667085;text-transform:uppercase;letter-spacing:.06em;">Total Pengeluaran</div>
        <div class="stat-value" style="color:#00034a;margin-top:10px;font-size:25px;">Rp {{ number_format($stats['total_biaya_saya'] ?? 0,0,',','.') }}</div>
        <div style="font-size:12px;color:#9a6b0d;margin-top:6px;">{{ number_format($pemakaianSaya,1,',','.') }}% dana telah digunakan</div>
    </a>
    <a href="{{ route('keuangan.laporan-saya') }}" class="stat-card" style="text-decoration:none;border-top:3px solid #017723;">
        <div style="font-size:12px;color:#667085;text-transform:uppercase;letter-spacing:.06em;">Transaksi Saya</div>
        <div class="stat-value" style="color:#017723;margin-top:10px;font-size:25px;">{{ number_format($stats['transaksi_saya'] ?? 0) }}</div>
        <div style="font-size:12px;color:#667085;margin-top:6px;">Pengeluaran tercatat</div>
    </a>
    <a href="{{ route('keuangan.laporan-saya') }}#riwayat-pengeluaran" class="stat-card" style="text-decoration:none;border-top:3px solid {{ ($stats['tanpa_bukti_saya'] ?? 0) > 0 ? '#b42318' : '#017723' }};">
        <div style="font-size:12px;color:#667085;text-transform:uppercase;letter-spacing:.06em;">Tanpa Bukti</div>
        <div class="stat-value" style="color:{{ ($stats['tanpa_bukti_saya'] ?? 0) > 0 ? '#b42318' : '#017723' }};margin-top:10px;font-size:25px;">{{ number_format($stats['tanpa_bukti_saya'] ?? 0) }}</div>
        <div style="font-size:12px;color:#667085;margin-top:6px;">Perlu dilengkapi</div>
    </a>
</div>
<div class="card" style="margin-bottom:24px;overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;gap:16px;align-items:center;">
        <div><h3 style="font-size:15px;font-weight:700;">Pemakaian Dana</h3><p style="font-size:12px;color:#667085;margin-top:3px;">Pengeluaran pribadi terhadap total dana yang disetujui.</p></div>
        <strong style="color:#00034a;font-size:18px;">{{ number_format($pemakaianSaya,1,',','.') }}%</strong>
    </div>
    <div style="padding:18px 20px;">
        <div class="progress-bar" style="height:10px;"><div class="progress-fill" style="width:{{ min(100,$pemakaianSaya) }}%;background:linear-gradient(90deg,#017723,#b07d14);"></div></div>
        <div style="display:flex;justify-content:space-between;gap:16px;margin-top:9px;font-size:12px;color:#667085;"><span>Terpakai Rp {{ number_format($stats['total_biaya_saya'] ?? 0,0,',','.') }}</span><span>Tersisa Rp {{ number_format($stats['saldo_topup_saya'] ?? 0,0,',','.') }}</span></div>
    </div>
</div>
@elseif($role === 'admin')
@php
  $sisaDana = $stats['total_dana_masuk'] - $stats['total_biaya'];
  $kpiRing = [
    'penerima' => $stats['penerima'] > 0 ? round($stats['penerima_terverifikasi']/$stats['penerima']*100) : 0,
    'kelompok' => $stats['kelompok'] > 0 ? round($stats['kelompok_aktif']/$stats['kelompok']*100) : 0,
    'distribusi' => $stats['distribusi'] > 0 ? round($stats['distribusi_selesai']/$stats['distribusi']*100) : 0,
  ];
@endphp
<div class="admin-kpis">
  <div class="admin-kpi" style="--accent:#00034a;">
    <div class="kpi-icon" style="background:#e8e8f0;color:#00034a;"><x-icon name="users" size="20"/></div>
    <div class="kpi-copy">
      <div class="kpi-label">Total Penerima</div>
      <div class="kpi-value">{{ number_format($stats['penerima']) }}</div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px;"><span style="color:#017723;font-weight:700;">{{ $stats['penerima_terverifikasi'] }}</span> terverifikasi · <span style="color:#b07d14;font-weight:700;">{{ $stats['penerima_pending'] }}</span> pending</div>
    </div>
    <div class="mini-ring" style="--accent:#00034a;--p:{{ $kpiRing['penerima'] }};"><span>{{ $kpiRing['penerima'] }}%</span></div>
  </div>
  <div class="admin-kpi" style="--accent:#017723;">
    <div class="kpi-icon" style="background:#e8f5ec;color:#017723;"><x-icon name="group" size="20"/></div>
    <div class="kpi-copy">
      <div class="kpi-label">Kelompok Aktif</div>
      <div class="kpi-value">{{ number_format($stats['kelompok']) }}</div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px;"><span style="color:#017723;font-weight:700;">{{ $stats['kelompok_aktif'] }}</span> kelompok dengan penerima</div>
    </div>
    <div class="mini-ring" style="--accent:#017723;--p:{{ $kpiRing['kelompok'] }};"><span>{{ $kpiRing['kelompok'] }}%</span></div>
  </div>
  <div class="admin-kpi" style="--accent:#b07d14;">
    <div class="kpi-icon" style="background:#fef7e6;color:#b07d14;"><x-icon name="truck" size="20"/></div>
    <div class="kpi-copy">
      <div class="kpi-label">Distribusi Selesai</div>
      <div class="kpi-value">{{ $stats['distribusi_selesai'] }}<span style="font-size:14px;color:#9ca3af;"> / {{ $stats['distribusi'] }}</span></div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px;"><span style="color:#e5a820;font-weight:700;">{{ $stats['distribusi_berlangsung'] }}</span> berlangsung · <span style="color:#00034a;font-weight:700;">{{ $stats['distribusi_rencana'] }}</span> rencana</div>
    </div>
    <div class="mini-ring" style="--accent:#b07d14;--p:{{ $kpiRing['distribusi'] }};"><span>{{ $kpiRing['distribusi'] }}%</span></div>
  </div>
  <div class="admin-kpi" style="--accent:#017723;">
    <div class="kpi-icon" style="background:#e8f5ec;color:#017723;"><x-icon name="wallet" size="20"/></div>
    <div class="kpi-copy">
      <div class="kpi-label">Dana Masuk</div>
      <div class="kpi-value">Rp {{ number_format($stats['total_dana_masuk'],0,',','.') }}</div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px;">Total donasi masuk</div>
    </div>
    <div class="mini-ring" style="--accent:#017723;--p:100;"><span>100%</span></div>
  </div>
  <div class="admin-kpi" style="--accent:#b42318;">
    <div class="kpi-icon" style="background:#fef2f2;color:#b42318;"><x-icon name="cash" size="20"/></div>
    <div class="kpi-copy">
      <div class="kpi-label">Total Biaya</div>
      <div class="kpi-value">Rp {{ number_format($stats['total_biaya'],0,',','.') }}</div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px;">Operasional + <span style="color:#dc2626;font-weight:700;">Rp {{ number_format($stats['total_nilai_bantuan'],0,',','.') }}</span> nilai bantuan</div>
    </div>
    <div class="mini-ring" style="--accent:#b42318;--p:{{ $stats['total_dana_masuk'] > 0 ? min(100,round($stats['total_biaya']/$stats['total_dana_masuk']*100)) : 0 }};"><span>{{ $stats['total_dana_masuk'] > 0 ? min(100,round($stats['total_biaya']/$stats['total_dana_masuk']*100)) : 0 }}%</span></div>
  </div>
  <div class="admin-kpi" style="--accent:#00034a;">
    <div class="kpi-icon" style="background:#e8e8f0;color:#00034a;"><x-icon name="save" size="20"/></div>
    <div class="kpi-copy">
      <div class="kpi-label">Sisa Dana</div>
      <div class="kpi-value" style="color:{{ $sisaDana < 0 ? '#b42318' : '#00034a' }};">Rp {{ number_format($sisaDana,0,',','.') }}</div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $stats['total_dana_masuk'] > 0 ? round($sisaDana/$stats['total_dana_masuk']*100) : 0 }}% dana belum terpakai</div>
    </div>
    <div class="mini-ring" style="--accent:#00034a;--p:{{ $stats['total_dana_masuk'] > 0 ? max(0,min(100,round($sisaDana/$stats['total_dana_masuk']*100))) : 0 }};"><span>{{ $stats['total_dana_masuk'] > 0 ? max(0,min(100,round($sisaDana/$stats['total_dana_masuk']*100))) : 0 }}%</span></div>
  </div>
</div>
{{-- Ringkasan Keuangan --}}
<div class="card" style="margin-bottom:24px;animation:cardIn .55s .35s both;">
  <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:8px;"><x-icon name="wallet"/><h3 style="font-size:15px;font-weight:600;margin:0;">Ringkasan Keuangan</h3></div>
  <div class="finance-grid">
    <div class="finance-tile" style="background:linear-gradient(135deg,#e8f5ec,#d1f0e6);border:1px solid #dcf5ee;"><div style="font-size:13px;color:#6b7280;">Dana Masuk</div><div style="font-size:20px;font-weight:700;color:#017723;margin-top:4px;">Rp {{ number_format($stats['total_dana_masuk'],0,',','.') }}</div></div>
    <div class="finance-tile" style="background:linear-gradient(135deg,#fef2f2,#ffe5e5);border:1px solid #ffd6d6;"><div style="font-size:13px;color:#6b7280;">Nilai Bantuan</div><div style="font-size:20px;font-weight:700;color:#dc2626;margin-top:4px;">Rp {{ number_format($stats['total_nilai_bantuan'],0,',','.') }}</div></div>
    <div class="finance-tile" style="background:linear-gradient(135deg,#fef7e6,#feebc8);border:1px solid #fde6b1;"><div style="font-size:13px;color:#6b7280;">Biaya Operasional</div><div style="font-size:20px;font-weight:700;color:#b07d14;margin-top:4px;">Rp {{ number_format($stats['total_biaya'],0,',','.') }}</div></div>
    <div class="finance-tile" style="background:linear-gradient(135deg,#e8e8f0,#dadbee);border:1px solid #c7c9e6;"><div style="font-size:13px;color:rgba(0,3,74,.55);">Sisa Dana</div><div style="font-size:20px;font-weight:700;color:#00034a;margin-top:4px;">Rp {{ number_format($stats['total_dana_masuk'] - $stats['total_nilai_bantuan'] - $stats['total_biaya'],0,',','.') }}</div></div>
  </div>
</div>
@else
<div class="mobile-stack" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#00034a;"><x-icon name="users" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">Penerima</span>
        </div>
        <div class="stat-value">{{ number_format($stats['penerima']) }}</div>
        <div style="display:flex;gap:12px;margin-top:6px;font-size:12px;">
            <span style="color:#017723;">✅ {{ $stats['penerima_terverifikasi'] }} siap</span>
            <span style="color:#b07d14;">⏳ {{ $stats['penerima_pending'] }} pending</span>
        </div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#e8f5ec;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#017723;"><x-icon name="group" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">Kelompok</span>
        </div>
        <div class="stat-value" style="color:#017723;">{{ number_format($stats['kelompok']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">📍 Tersebar di Aceh</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef7e6;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#9a6b0d;"><x-icon name="truck" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">Distribusi</span>
        </div>
        <div class="stat-value" style="color:#b07d14;">{{ number_format($stats['distribusi']) }}</div>
        <div style="display:flex;gap:12px;margin-top:6px;font-size:12px;">
            <span style="color:#017723;">✅ {{ $stats['distribusi_selesai'] }} selesai</span>
            <span style="color:#b07d14;">⏳ {{ $stats['distribusi_berlangsung'] }} berlangsung</span>
        </div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:40px;height:40px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#b42318;"><x-icon name="wallet" size="20"/></div>
            <span style="font-size:13px;color:#6b7280;">{{ $role === 'admin' ? 'Nilai Bantuan' : 'Sudah Menerima' }}</span>
        </div>
        @if($role === 'admin')
        <div class="stat-value">Rp {{ number_format($stats['total_nilai_bantuan'],0,',','.') }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">💵 Dana masuk: Rp {{ number_format($stats['total_dana_masuk'],0,',','.') }}</div>
        @else
        <div class="stat-value">{{ number_format($stats['penerima_diterima']) }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Penerima sudah menerima bantuan</div>
        @endif
    </div>
</div>
@endif

@if($role === 'admin')
@php
  $realisasiPaket = $stats['total_paket_target'] > 0 ? min(100, round($stats['total_paket_terkirim'] / $stats['total_paket_target'] * 100)) : 0;
  $realisasiPenerima = $stats['penerima'] > 0 ? min(100, round($stats['penerima_diterima'] / $stats['penerima'] * 100)) : 0;
  $batch = $stats['biaya_batch'] ?? collect();
@endphp
<div class="card" style="margin-bottom:24px;overflow:hidden;">
  <div style="background:#00034a;color:white;padding:20px 22px;display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;">
    <div>
      <div style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#d6b665;font-weight:700;">DAMPAK PROGRAM</div>
      <div style="font-size:21px;font-weight:750;margin-top:5px;">Jangkauan bantuan YPKM</div>
      <div style="font-size:13px;color:rgba(255,255,255,.68);margin-top:4px;">Ringkasan cakupan wilayah dan realisasi penyaluran</div>
    </div>
    <div style="display:flex;gap:20px;flex-wrap:wrap;">
      @php
        $kabMax = max(23, $stats['kabupaten_terjangkau']);
        $kecMax = max(289, $stats['kecamatan_terjangkau']);
        $desaMax = max(6000, $stats['desa_terjangkau']);
      @endphp
      <div style="text-align:center;">
        <div class="impact-ring" style="--p:{{ min(100,round($stats['kabupaten_terjangkau']/$kabMax*100)) }};"><span>{{ $stats['kabupaten_terjangkau'] }}<small>Kabupaten</small></span></div>
        <div style="font-size:10px;color:rgba(255,255,255,.62);margin-top:6px;">dari {{ $kabMax }} kabupaten</div>
      </div>
      <div style="text-align:center;">
        <div class="impact-ring" style="--p:{{ min(100,round($stats['kecamatan_terjangkau']/$kecMax*100)) }};"><span>{{ $stats['kecamatan_terjangkau'] }}<small>Kecamatan</small></span></div>
        <div style="font-size:10px;color:rgba(255,255,255,.62);margin-top:6px;">dari {{ $kecMax }} kecamatan</div>
      </div>
      <div style="text-align:center;">
        <div class="impact-ring" style="--p:{{ min(100,round($stats['desa_terjangkau']/$desaMax*100)) }};"><span>{{ $stats['desa_terjangkau'] }}<small>Desa</small></span></div>
        <div style="font-size:10px;color:rgba(255,255,255,.62);margin-top:6px;">dari {{ $desaMax }} desa</div>
      </div>
    </div>
  </div>
  <div class="mobile-stack" style="padding:18px 20px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
    <div>
      <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;"><span>Realisasi paket</span><strong>{{ $realisasiPaket }}%</strong></div>
      <div class="progress-bar"><div class="progress-fill animated-fill" style="width:{{ $realisasiPaket }}%;background:#017723;animation-delay:.15s;"></div></div>
      <div style="font-size:11px;color:#667085;margin-top:6px;">{{ number_format($stats['total_paket_terkirim']) }} dari {{ number_format($stats['total_paket_target']) }} paket</div>
    </div>
    <div>
      <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;"><span>Penerima terlayani</span><strong>{{ $realisasiPenerima }}%</strong></div>
      <div class="progress-bar"><div class="progress-fill animated-fill" style="width:{{ $realisasiPenerima }}%;background:#b07d14;animation-delay:.3s;"></div></div>
      <div style="font-size:11px;color:#667085;margin-top:6px;">{{ number_format($stats['penerima_diterima']) }} dari {{ number_format($stats['penerima']) }} penerima</div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
      <a href="{{ route('barang.index', ['tab'=>'pembelian']) }}" style="text-decoration:none;background:{{ $stats['barang_stok_kritis'] > 0 ? '#fef2f2' : '#e8f5ec' }};border-radius:8px;padding:10px 12px;color:#111827;">
        <div style="font-size:19px;font-weight:750;color:{{ $stats['barang_stok_kritis'] > 0 ? '#b42318' : '#017723' }};">{{ $stats['barang_stok_kritis'] }}</div><div style="font-size:11px;">Stok kritis</div>
      </a>
      <a href="{{ route('barang.index', ['tab'=>'kegiatan']) }}" style="text-decoration:none;background:#fef7e6;border-radius:8px;padding:10px 12px;color:#111827;">
        <div style="font-size:19px;font-weight:750;color:#9a6b0d;">{{ $stats['kegiatan_lunas'] }}</div><div style="font-size:11px;">Kegiatan lunas</div>
      </a>
    </div>
  </div>
</div>
@php
  $donutTotal = max(1, $stats['total_dana_masuk'] + $stats['total_nilai_bantuan'] + $stats['total_biaya'] + max(0,$sisaDana));
  $pDana = round($stats['total_dana_masuk']/$donutTotal*100,1);
  $pBantuan = round($stats['total_nilai_bantuan']/$donutTotal*100,1);
  $pBiaya = round($stats['total_biaya']/$donutTotal*100,1);
  $pSisa = max(0,round($sisaDana/$donutTotal*100,1));
  $stop1=$pDana; $stop2=$stop1+$pBantuan; $stop3=$stop2+$pBiaya;
@endphp
<div class="viz-grid">
  <div class="premium-card">
    <div class="viz-head"><h3>Proporsi Penggunaan Dana</h3><p>Komposisi arus dana, bantuan, operasional, dan saldo.</p></div>
    <div class="donut-layout">
      <div class="donut" style="--donut:conic-gradient(#017723 0 {{ $stop1 }}%,#dc2626 {{ $stop1 }}% {{ $stop2 }}%,#d6b665 {{ $stop2 }}% {{ $stop3 }}%,#00034a {{ $stop3 }}% 100%);">
        <div class="donut-center"><strong>{{ $pSisa }}%</strong><span>Sisa dana</span></div>
      </div>
      <div class="legend">
        @foreach([['Dana Masuk','#017723',$stats['total_dana_masuk'],$pDana],['Nilai Bantuan','#dc2626',$stats['total_nilai_bantuan'],$pBantuan],['Biaya Operasional','#d6b665',$stats['total_biaya'],$pBiaya],['Sisa Dana','#00034a',max(0,$sisaDana),$pSisa]] as $item)
        <div class="legend-row"><span class="legend-dot" style="background:{{ $item[1] }};"></span><div><div class="legend-label">{{ $item[0] }} · {{ $item[3] }}%</div><div class="legend-value">Rp {{ number_format($item[2],0,',','.') }}</div></div></div>
        @endforeach
      </div>
    </div>
  </div>
  @if($batch->isNotEmpty())
  <div class="premium-card">
  <div class="viz-head">
    <h3 style="display:flex;align-items:center;gap:8px;">📦 Biaya Operasional per Batch</h3>
    <p>Distribusi biaya terkini per kegiatan/batch.</p>
  </div>
  @php $batchMax = $batch->max('total'); @endphp
  <div class="bar-list">
    @foreach($batch as $i => $b)
    <div class="bar-row">
      <div class="bar-name" title="{{ $b->batch }}">{{ $b->batch }}</div>
      <div class="bar-track">
        <div class="bar-fill" style="width:{{ $batchMax > 0 ? round($b->total/$batchMax*100) : 0 }}%;animation-delay:.{{ $i * 15 }}s;"></div>
      </div>
      <div class="bar-value">Rp {{ number_format($b->total,0,',','.') }}<br><span style="font-size:9px;color:#9ca3af;">{{ $stats['total_biaya'] > 0 ? round($b->total/$stats['total_biaya']*100,1) : 0 }}%</span></div>
    </div>
    @endforeach
  </div>
  </div>
  @else
  <div class="premium-card" style="display:grid;place-items:center;padding:40px;color:#9ca3af;">Belum ada biaya per batch</div>
  @endif
</div>
@endif

{{-- Two columns --}}
<div class="mobile-stack" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Distribusi Terbaru --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px;"><x-icon name="truck"/> Distribusi terbaru</h3>
            <a href="{{ route('distribusi.index') }}" style="font-size:13px;color:#00034a;text-decoration:none;">Lihat Semua →</a>
        </div>
        <div class="card-body table-wrap">
            <table class="table-data">
                <thead><tr><th>Kegiatan</th><th>Daerah</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($distribusiTerbaru as $d)
                    <tr>
                        <td style="font-weight:500;">{{ $d->nama_kegiatan }}</td>
                        <td style="color:#6b7280;">{{ $d->kelompok->daerah ?? '-' }}</td>
                        <td style="color:#6b7280;">{{ is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)) }}</td>
                        <td>
                            @if($d->status == 'selesai') <span class="badge badge-green">✅ Selesai</span>
                            @elseif($d->status == 'berlangsung') <span class="badge badge-gold">⏳ Berlangsung</span>
                            @else <span class="badge badge-navy">📋 Rencana</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada distribusi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Progress --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px;"><x-icon name="report"/> Progres distribusi</h3>
        </div>
        <div style="padding:16px 20px;">
            @forelse($distribusiTerbaru->take(4) as $d)
            @php $persen = $d->status == 'selesai' ? 100 : ($d->status == 'berlangsung' ? 45 : 0); @endphp
            <div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
                    <span>{{ $d->nama_kegiatan }}</span>
                    <span style="color:#6b7280;">{{ $d->jumlah_paket }} paket</span>
                </div>
                <div class="progress-bar"><div class="progress-fill animated-fill" style="width:{{ $persen }}%;background:{{ $d->status == 'selesai' ? '#017723' : ($d->status == 'berlangsung' ? '#e5a820' : '#00034a') }};animation-delay:.{{ $loop->index * 15 }}s;"></div></div>
            </div>
            @empty
            <p style="text-align:center;padding:24px;color:#9ca3af;">Belum ada data</p>
            @endforelse
        </div>
    </div>
@endif
@endsection
