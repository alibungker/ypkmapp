@extends('layouts.app')
@section('title', 'Detail Distribusi')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
/* ── layout ── */
.show-page{max-width:1280px;margin:0 auto;padding:24px 28px 40px}
.breadcrumb{font-size:13px;color:#6b7280;margin-bottom:6px;display:flex;align-items:center;gap:6px}
.breadcrumb a{color:#00034a;text-decoration:none;font-weight:500}
.breadcrumb a:hover{text-decoration:underline}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:24px}
.page-title{font-size:22px;font-weight:700;color:#00034a;line-height:1.3;margin:0}
.page-title span{display:block;font-size:14px;font-weight:500;color:#6b7280;margin-top:2px}
.page-actions{display:flex;gap:8px;align-items:center;flex-shrink:0}
.status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600}
.status-selesai{background:#dcfce7;color:#166534}
.status-berlangsung{background:#fef3c7;color:#92400e}
.status-rencana{background:#dbeafe;color:#1e40af}
.status-dibatalkan{background:#fee2e2;color:#b91c1c}

/* ── kpi strip ── */
.kpi-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
.kpi-card{background:white;border:1px solid #e5e7eb;border-radius:10px;padding:16px 18px;display:flex;flex-direction:column;gap:4px}
.kpi-label{font-size:12px;color:#6b7280;font-weight:500;display:flex;align-items:center;gap:5px}
.kpi-value{font-size:22px;font-weight:700;color:#00034a;line-height:1.2}
.kpi-value.green{color:#017723}
.kpi-value.gold{color:#b07d14}
.kpi-sub{font-size:11px;color:#9ca3af}

/* ── two-col ── */
.show-grid{display:grid;grid-template-columns:1fr 420px;gap:20px;align-items:start}
@media(max-width:900px){.show-grid{grid-template-columns:1fr}.kpi-strip{grid-template-columns:repeat(2,1fr)}}

/* ── cards ── */
.d-card{background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden}
.d-card-header{padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:8px}
.d-card-header h3{font-size:15px;font-weight:600;color:#00034a;margin:0}
.d-card-body{padding:20px}

/* ── info grid ── */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:0}
.info-item{padding:10px 16px;border-bottom:1px solid #f9fafb;display:flex;align-items:flex-start;gap:10px}
.info-item:nth-child(odd){background:#fafbfc}
.info-icon{flex-shrink:0;width:20px;height:20px;margin-top:1px}
.info-icon svg{width:18px;height:18px;stroke-width:1.8;fill:none;stroke-linecap:round;stroke-linejoin:round}
.info-label{font-size:11px;color:#9ca3af;font-weight:500;text-transform:uppercase;letter-spacing:.3px}
.info-value{font-size:13px;color:#1f2937;font-weight:600;line-height:1.4}
.info-value.mono{font-family:'SF Mono',SFMono-Regular,Consolas,monospace;font-size:12px}
.info-value.green{color:#017723}
.info-value.amber{color:#b07d14}
.info-value.red{color:#dc2626}
.info-item.full{grid-column:1/-1}

/* ── table ── */
.d-table{width:100%;border-collapse:collapse;font-size:13px}
.d-table th{text-align:left;padding:10px 14px;background:#f8fafc;font-weight:600;color:#00034a;font-size:11px;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid #e5e7eb;white-space:nowrap}
.d-table td{padding:10px 14px;border-bottom:1px solid #f3f4f6;color:#374151}
.d-table tbody tr:hover{background:#f8fafc}
.d-table td.r{text-align:right}
.d-table th.r{text-align:right}
.d-table tfoot td{font-weight:700;color:#017723;border-top:2px solid #e5e7eb;background:#f0fdf4}

/* ── map ── */
#showmap{height:320px;border-radius:8px;border:1px solid #e5e7eb}
@media(min-width:900px){#showmap{height:100%;min-height:500px}}

/* ── docs grid ── */
.docs-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px}
.docs-card{border:1px solid #e5e7eb;border-radius:10px;padding:8px;text-decoration:none;background:white;transition:border-color .15s,box-shadow .15s}
.docs-card:hover{border-color:#017723;box-shadow:0 2px 8px rgba(1,119,35,.12)}
.docs-card img{display:block;width:100%;height:100px;object-fit:cover;border-radius:6px;background:#f3f4f6}
.docs-card .pdf-thumb{height:100px;display:flex;align-items:center;justify-content:center;border-radius:6px;background:#eff6ff;color:#00034a;font-weight:800;font-size:24px}
.docs-card span{display:block;margin-top:6px;font-size:11px;color:#00034a;overflow-wrap:anywhere;line-height:1.3}

/* ── album gallery ── */
.album-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px}
.album-card{border:1px solid #e5e7eb;border-radius:10px;padding:8px;background:white;text-decoration:none;display:block;transition:border-color .15s,box-shadow .15s;position:relative}
.album-card:hover{border-color:#017723;box-shadow:0 2px 8px rgba(1,119,35,.12)}
.album-card img{display:block;width:100%;height:120px;object-fit:cover;border-radius:6px;background:#f3f4f6}
.album-card .album-badge{position:absolute;top:12px;right:12px;background:rgba(0,3,74,.78);color:#fff;font-size:10px;font-weight:600;padding:3px 8px;border-radius:999px;backdrop-filter:blur(2px)}
.album-card .album-cap{display:block;margin-top:6px;font-size:11px;color:#00034a;line-height:1.3;overflow-wrap:anywhere}
.album-card .album-meta{display:block;margin-top:2px;font-size:10px;color:#9ca3af}
@media(max-width:640px){.album-grid{grid-template-columns:repeat(auto-fill,minmax(130px,1fr))}}

/* ── btn ── */
.btn-show{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:background .15s,box-shadow .15s;text-decoration:none}
.btn-show svg{width:16px;height:16px}
.btn-primary-show{background:#00034a;color:white}.btn-primary-show:hover{background:#1a1a5e}
.btn-outline-show{background:white;color:#00034a;border:1px solid #d1d5db}.btn-outline-show:hover{background:#f9fafb}
.btn-success-show{background:#017723;color:white}.btn-success-show:hover{background:#005a1a}

@media(max-width:640px){.kpi-strip{grid-template-columns:1fr}.info-grid{grid-template-columns:1fr}.show-page{padding:16px}.kpi-value{font-size:18px}}
</style>
@endsection

@section('content')
@php
    $d = $distribusi;
    $kelompok = $d->kelompok;
    $selisih = (int)$d->jumlah_paket - (int)($kelompok->penerima_count ?? 0);
    $totalNilai = 0; $paket = max(1,(int)$d->jumlah_paket);
    $tglIndo = \Carbon\Carbon::parse($d->tanggal)->locale('id')->translatedFormat('d F Y');
    $coords = $d->titik_koordinat ? collect(explode(',', $d->titik_koordinat))->map(fn($v) => number_format((float)trim($v), 6, '.', ''))->implode(', ') : null;
    $icon = function($path, $color='#6b7280') { return '<span class="info-icon"><svg viewBox="0 0 24 24" stroke="'.$color.'">'.$path.'</svg></span>'; };
@endphp

<div class="show-page">

  {{-- breadcrumb --}}
  <div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span>›</span>
    <a href="{{ route('distribusi.index') }}">Distribusi</a>
    <span>›</span>
    <span style="color:#374151">{{ $d->kode_distribusi }}</span>
  </div>

  {{-- page header --}}
  <div class="page-header">
    <div>
      <h1 class="page-title">
        {{ $d->nama_kegiatan }}
        <span>{{ $d->lokasi }}</span>
      </h1>
    </div>
    <div class="page-actions">
      <a href="{{ route('distribusi.index') }}" class="btn-show btn-outline-show">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
      </a>
      @if(auth()->user()->isAdmin())
      <a href="{{ route('distribusi.edit', $d) }}" class="btn-show btn-primary-show">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
      </a>
      @endif
    </div>
  </div>

  {{-- kpi strip --}}
  <div class="kpi-strip">
    <div class="kpi-card">
      <div class="kpi-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Tanggal
      </div>
      <div class="kpi-value">{{ $tglIndo }}</div>
      <div class="kpi-sub">{{ $d->kode_distribusi }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        Target Paket
      </div>
      <div class="kpi-value green">{{ number_format($d->jumlah_paket) }} <span style="font-size:13px">paket</span></div>
      <div class="kpi-sub">{{ number_format($kelompok->penerima_count ?? 0) }} penerima terdata</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Estimasi Nilai
      </div>
      <div class="kpi-value green">Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}</div>
      <div class="kpi-sub">{{ $d->sumber_dana ?? '-' }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Status
      </div>
      <div class="kpi-value">
        @if($d->status==='selesai') <span class="status-badge status-selesai"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Selesai</span>
        @elseif($d->status==='berlangsung') <span class="status-badge status-berlangsung"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Berlangsung</span>
        @else <span class="status-badge status-rencana"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Rencana</span>
        @endif
      </div>
      <div class="kpi-sub">Target {{ number_format($kelompok->penerima_count ?? 0) }} penerima</div>
    </div>
  </div>

  {{-- two-column --}}
  <div class="show-grid">

    {{-- LEFT: info + table + docs --}}
    <div style="display:flex;flex-direction:column;gap:20px">

      {{-- info card --}}
      <div class="d-card">
        <div class="d-card-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00034a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <h3>Informasi Distribusi</h3>
        </div>
        <div class="info-grid">
          <div class="info-item">
            {!! $icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>', '#00034a') !!}
            <div><div class="info-label">Kode</div><div class="info-value mono">{{ $d->kode_distribusi }}</div></div>
          </div>
          <div class="info-item">
            {!! $icon('<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>', '#00034a') !!}
            <div><div class="info-label">Tanggal</div><div class="info-value">{{ is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)) }}</div></div>
          </div>
          <div class="info-item full">
            {!! $icon('<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>', '#017723') !!}
            <div><div class="info-label">Lokasi</div><div class="info-value">{{ $d->lokasi }}</div></div>
          </div>
          <div class="info-item">
            {!! $icon('<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', '#00034a') !!}
            <div><div class="info-label">Kelompok</div><div class="info-value">{{ $kelompok->nama ?? '-' }}</div></div>
          </div>
          <div class="info-item">
            {!! $icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>', '#6b7280') !!}
            <div><div class="info-label">Ketua</div><div class="info-value">{{ optional(optional($kelompok)->ketuaUser)->name ?? '-' }}</div></div>
          </div>
          <div class="info-item">
            {!! $icon('<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', '#6b7280') !!}
            <div><div class="info-label">Penerima</div><div class="info-value">{{ number_format($kelompok->penerima_count ?? 0) }} orang</div></div>
          </div>
          <div class="info-item">
            {!! $icon('<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>', '#017723') !!}
            <div><div class="info-label">Paket</div><div class="info-value">{{ number_format($d->jumlah_paket) }} paket
              @if($selisih !== 0) <span style="font-size:11px;font-weight:600;color:{{ $selisih > 0 ? '#b07d14' : '#dc2626' }};margin-left:4px">({{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih) }})</span>@endif
            </div></div>
          </div>
          <div class="info-item">
            {!! $icon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', '#6b7280') !!}
            <div><div class="info-label">Sumber Dana</div><div class="info-value">{{ $d->sumber_dana ?? '-' }}</div></div>
          </div>
        </div>
      </div>

      {{-- barang table --}}
      @if($d->pembelianBarang->isNotEmpty())
      <div class="d-card">
        <div class="d-card-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00034a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
          <h3>Barang Didistribusikan</h3>
          <span style="font-size:12px;color:#6b7280;margin-left:auto">{{ number_format($d->jumlah_paket) }} paket</span>
        </div>
        <div class="table-wrap" style="padding:0">
          <table class="d-table">
            <thead><tr><th>Nama Barang</th><th>Batch</th><th class="r">Per Paket</th><th class="r">Total</th><th class="r">Harga Satuan</th><th class="r">Subtotal</th></tr></thead>
            <tbody>
              @foreach($d->pembelianBarang as $pb)
                @php
                  $sub = $pb->pivot->jumlah * $pb->harga_satuan;
                  $totalNilai += $sub;
                  $perPaket = $pb->pivot->jumlah / $paket;
                  $pp = $perPaket == (int)$perPaket ? number_format($perPaket) : rtrim(rtrim(number_format($perPaket,2,',','.'),'0'),',');
                @endphp
                <tr>
                  <td style="font-weight:600;color:#00034a">{{ $pb->nama_barang }}</td>
                  <td style="color:#6b7280">{{ $pb->batch ?? '-' }}</td>
                  <td class="r" style="font-weight:500">{{ $pp }} <span style="color:#9ca3af;font-size:11px">/paket</span></td>
                  <td class="r" style="font-weight:600;color:#b07d14">{{ number_format($pb->pivot->jumlah) }}</td>
                  <td class="r">Rp {{ number_format($pb->harga_satuan,0,',','.') }}</td>
                  <td class="r" style="font-weight:600;color:#00034a">Rp {{ number_format($sub,0,',','.') }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot><tr><td colspan="5" style="text-align:right">Total Nilai</td><td class="r">Rp {{ number_format($totalNilai,0,',','.') }}</td></tr></tfoot>
          </table>
        </div>
      </div>
      @endif

      {{-- docs --}}
      @if($d->lampiran->isNotEmpty())
      <div class="d-card">
        <div class="d-card-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00034a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <h3>Dokumentasi Lapangan</h3>
          <span style="font-size:12px;color:#6b7280;margin-left:auto">{{ $d->lampiran->count() }} file</span>
        </div>
        <div class="d-card-body">
          <div class="docs-grid">
            @foreach($d->lampiran as $file)
              <a href="{{ Storage::url($file->path) }}" target="_blank" rel="noopener" class="docs-card">
                @if($file->jenis==='foto')
                  <img src="{{ Storage::url($file->path) }}" alt="{{ $file->nama_asli }}" loading="lazy">
                @else
                  <div class="pdf-thumb">PDF</div>
                @endif
                <span>{{ $file->nama_asli }}</span>
              </a>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      {{-- album kegiatan --}}
      @if($albums->isNotEmpty())
      <div class="d-card">
        <div class="d-card-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00034a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <h3>Album Kegiatan</h3>
          <span style="font-size:12px;color:#6b7280;margin-left:auto">{{ $albums->sum(fn ($a) => $a->photos->count()) }} foto · {{ $albums->count() }} album</span>
        </div>
        <div class="d-card-body">
          @foreach($albums as $album)
            @if($album->photos->isNotEmpty())
            <div style="margin-bottom:18px">
              <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:8px">
                <strong style="font-size:13px;color:#00034a">{{ $album->title }}</strong>
                <span style="font-size:11px;color:#9ca3af">{{ $album->photos->count() }} foto{{ $album->event_date ? ' · ' . $album->event_date->format('d M Y') : '' }}</span>
                <a href="{{ route('album-kegiatan.show', $album) }}" style="margin-left:auto;font-size:11px;font-weight:600;color:#017723;text-decoration:none">Buka album →</a>
              </div>
              <div class="album-grid">
                @foreach($album->photos as $photo)
                <a href="{{ asset('storage/' . $photo->path) }}" target="_blank" rel="noopener" class="album-card" title="{{ $photo->original_name }}">
                  <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->original_name ?: 'Foto album' }}" loading="lazy">
                  @if($album->cover_photo_id === $photo->id)
                  <span class="album-badge">★ Sampul</span>
                  @endif
                  <span class="album-cap">{{ $photo->original_name ?: 'Foto' }}</span>
                </a>
                @endforeach
              </div>
            </div>
            @endif
          @endforeach
        </div>
      </div>
      @endif

      {{-- admin actions --}}
      @if(auth()->user()->isAdmin() && $d->status !== 'selesai')
      <div class="d-card">
        <div class="d-card-body" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <span style="font-size:13px;color:#6b7280;font-weight:500">Tandai distribusi ini sebagai selesai:</span>
          <form method="POST" action="{{ route('distribusi.selesai', $d) }}" style="margin:0">
            @csrf
            <button type="submit" class="btn-show btn-success-show" onclick="return confirm('Tandai selesai?')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
              Tandai Selesai
            </button>
          </form>
        </div>
      </div>
      @endif
    </div>

    {{-- RIGHT: map (sticky on desktop) --}}
    <div class="d-card" style="position:sticky;top:24px">
      <div class="d-card-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00034a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        <h3>Lokasi Distribusi</h3>
      </div>
      <div class="d-card-body" style="padding:12px">
        <div id="showmap"></div>
        @if($d->titik_koordinat)
        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <span style="font-family:monospace">{{ $d->titik_koordinat }}</span>
        </div>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const coord = "{{ $d->titik_koordinat ?? '' }}";
const showmap = L.map('showmap').setView([4.7, 96.8], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:18, attribution:'&copy; OpenStreetMap'}).addTo(showmap);
if (coord.includes(',')) {
    const parts = coord.split(',').map(Number);
    const icon = L.divIcon({
        className: 'custom-marker',
        html: `<svg width="32" height="40" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#017723"/><circle cx="12" cy="9.5" r="3" fill="white"/></svg>`,
        iconSize: [32, 40],
        iconAnchor: [16, 40],
        popupAnchor: [0, -32]
    });
    L.marker(parts, {icon})
        .addTo(showmap)
        .bindPopup(`<div style="font-family:system-ui;min-width:200px"><div style="font-weight:700;color:#00034a;margin-bottom:4px">{{ addslashes($d->nama_kegiatan) }}</div><div style="font-size:13px;color:#374151"><b>Paket:</b> {{ number_format($d->jumlah_paket) }}<br><b>Penerima:</b> {{ number_format($kelompok->penerima_count ?? 0) }}<br><b>Nilai:</b> Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}</div></div>`);
    showmap.setView(parts, 12);
}
</script>
@endsection
