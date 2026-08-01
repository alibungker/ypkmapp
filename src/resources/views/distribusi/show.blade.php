@extends('layouts.app')
@section('title', 'Detail Distribusi')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@verbatim
<style>
#showmap{height:260px;border-radius:10px;border:1px solid #e5e7eb;}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.table-data th,.table-data td{padding:8px 10px;border-bottom:1px solid #f1f1f1;font-size:13px;}
.table-data th{background:#f8fafc;font-weight:600;color:#00034a;text-align:left;}
.table-data td:nth-child(3),.table-data td:nth-child(5),.table-data td:nth-child(6){text-align:right;}
.distribusi-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:20px;max-width:1100px;margin:0 auto;padding:20px;}
@media screen and (max-width:768px){.distribusi-grid{grid-template-columns:1fr}.table-data th,.table-data td{font-size:12px;padding:6px 8px;}}
</style>
@endverbatim
@endsection
@section('content')
<div class="distribusi-grid">
  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <h3 style="font-size:15px;font-weight:600;">🚚 {{ $distribusi->nama_kegiatan }}</h3>
      <a href="{{ route('distribusi.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
      <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
          <tr><td style="padding:6px 0;color:#6b7280;width:45%;">Kode</td><td style="font-weight:600;font-family:monospace;">{{ $distribusi->kode_distribusi }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">📅 Tanggal</td><td style="font-weight:600;">{{ is_object($distribusi->tanggal) ? $distribusi->tanggal->format('d M Y') : date('d M Y', strtotime($distribusi->tanggal)) }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">📍 Lokasi</td><td style="font-weight:600;">{{ $distribusi->lokasi }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">🗺️ Koordinat</td><td style="font-family:monospace;font-size:13px;">{{ $distribusi->titik_koordinat ?? '-' }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">📋 Kelompok</td><td style="font-weight:600;">{{ $distribusi->kelompok->nama ?? '-' }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">👤 Ketua Kelompok</td><td style="font-weight:600;">{{ optional(optional($distribusi->kelompok)->ketuaUser)->name ?? '-' }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">👥 Jumlah Penerima</td><td style="font-weight:600;">{{ number_format($distribusi->kelompok->penerima_count ?? 0) }} orang</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">📦 Target Paket</td><td style="font-weight:600;">{{ number_format($distribusi->jumlah_paket) }} paket</td></tr>
          @php
            $selisihPaket = (int) $distribusi->jumlah_paket - (int) ($distribusi->kelompok->penerima_count ?? 0);
          @endphp
          @if($selisihPaket !== 0)
          <tr><td style="padding:6px 0;color:#6b7280;">⚖️ Selisih Paket</td><td style="font-weight:700;color:{{ $selisihPaket > 0 ? '#e5a820' : '#dc2626' }};">{{ $selisihPaket > 0 ? '+' : '' }}{{ number_format($selisihPaket) }} paket</td></tr>
          @endif
          <tr><td style="padding:6px 0;color:#6b7280;">💰 Estimasi Nilai</td><td style="font-weight:600;color:#017723;">Rp {{ number_format($distribusi->estimasi_nilai_total,0,',','.') }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">💵 Sumber Dana</td><td>{{ $distribusi->sumber_dana ?? '-' }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">📎 Lampiran</td><td>{{ $distribusi->lampiran->count() ? $distribusi->lampiran->count() . ' file' : ($distribusi->bukti_file ? '1 file lama' : '-') }}</td></tr>
          <tr><td style="padding:6px 0;color:#6b7280;">📊 Status</td><td>
            @if($distribusi->status == 'selesai') <span class="badge badge-green">✅ Selesai</span>
            @elseif($distribusi->status == 'berlangsung') <span class="badge badge-gold">⏳ Berlangsung</span>
            @else <span class="badge badge-navy">📋 Rencana</span>
            @endif
          </td></tr>
        </table>
      </div>

      @if($distribusi->lampiran->isNotEmpty())
        <div style="margin-top:16px;">
          <div style="font-size:13px;font-weight:700;color:#00034a;margin-bottom:9px;">🖼️ Dokumentasi Lapangan</div>
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:9px;">
            @foreach($distribusi->lampiran as $file)
              <a href="{{ Storage::url($file->path) }}" target="_blank" rel="noopener" style="border:1px solid #e5e7eb;border-radius:9px;padding:8px;text-decoration:none;min-width:0;background:#fff;">
                @if($file->jenis === 'foto')
                  <img src="{{ Storage::url($file->path) }}" alt="{{ $file->nama_asli }}" loading="lazy" style="display:block;width:100%;height:90px;object-fit:cover;border-radius:6px;background:#f3f4f6;">
                @else
                  <div style="height:90px;display:flex;align-items:center;justify-content:center;border-radius:6px;background:#eef2ff;color:#00034a;font-weight:800;">PDF</div>
                @endif
                <span style="display:block;margin-top:6px;font-size:11px;color:#00034a;overflow-wrap:anywhere;">{{ $file->nama_asli }}</span>
              </a>
            @endforeach
          </div>
        </div>
      @elseif($distribusi->bukti_file)
        <div style="margin-top:12px;"><a href="{{ Storage::url($distribusi->bukti_file) }}" target="_blank" rel="noopener">Lihat bukti lama</a></div>
      @endif

      @if($distribusi->pembelianBarang->isNotEmpty())
        <div style="margin-top:16px;border-top:1px solid #e5e7eb;padding-top:16px;">
          <div style="font-size:13px;font-weight:700;color:#00034a;margin-bottom:9px;">📦 Barang Didistribusikan ({{ number_format($distribusi->jumlah_paket) }} paket)</div>
          <div class="table-wrap">
            <table class="table-data">
              <thead><tr><th>Nama Barang</th><th>Batch</th style="text-align:right;">Per Paket</th><th style="text-align:right;">Total</th><th style="text-align:right;">Harga Satuan</th><th style="text-align:right;">Subtotal</th></tr></thead>
              <tbody>
                @php $totalNilai = 0; $paket = max(1,(int)$distribusi->jumlah_paket); @endphp
                @foreach($distribusi->pembelianBarang as $pb)
                  @php
                    $sub = $pb->pivot->jumlah * $pb->harga_satuan;
                    $totalNilai += $sub;
                    $perPaket = $pb->pivot->jumlah / $paket;
                    $perPaketTxt = $perPaket == (int)$perPaket ? number_format($perPaket) : rtrim(rtrim(number_format($perPaket,2,',','.'),'0'),',');
                  @endphp
                  <tr>
                    <td style="font-weight:500;">{{ $pb->nama_barang }}</td>
                    <td>{{ $pb->batch ?? '-' }}</td>
                    <td style="font-weight:600;text-align:right;">{{ $perPaketTxt }} /paket</td>
                    <td style="font-weight:600;color:#b07d14;text-align:right;">{{ number_format($pb->pivot->jumlah) }}</td>
                    <td style="text-align:right;">Rp {{ number_format($pb->harga_satuan,0,',','.') }}</td>
                    <td style="font-weight:500;text-align:right;">Rp {{ number_format($sub,0,',','.') }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot><tr><td colspan="5" style="font-weight:600;text-align:right;">Total Nilai</td><td style="font-weight:700;color:#017723;text-align:right;">Rp {{ number_format($totalNilai,0,',','.') }}</td></tr></tfoot>
            </table>
          </div>
        </div>
      @endif

      @if(auth()->user()->isAdmin())
        <div style="margin-top:16px;border-top:1px solid #e5e7eb;padding-top:16px;display:flex;gap:9px;flex-wrap:wrap;">
          <a href="{{ route('distribusi.edit', $distribusi) }}" class="btn btn-primary btn-sm">✏️ Edit</a>
          @if($distribusi->status != 'selesai')
            <form method="POST" action="{{ route('distribusi.selesai', $distribusi) }}">@csrf
              <button class="btn btn-sm" style="background:#017723;color:white;">✅ Tandai Selesai</button>
            </form>
          @endif
        </div>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 style="font-size:15px;font-weight:600;">🗺️ Lokasi Distribusi</h3>
    </div>
    <div class="card-body">
      <div id="showmap"></div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const coord = "{{ $distribusi->titik_koordinat ?? '' }}";
const showmap = L.map('showmap').setView([4.7, 96.8], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:18}).addTo(showmap);
if (coord.includes(',')) {
    const parts = coord.split(',').map(Number);
    L.marker(parts, {icon: L.divIcon({html:'<div style="font-size:15px;text-align:center;line-height:1;">🎁</div>',className:'',iconSize:[24,24],iconAnchor:[12,12]})})
        .addTo(showmap)
        .bindPopup("<b>{{ $distribusi->nama_kegiatan }}</b><br>📦 {{ number_format($distribusi->jumlah_paket) }} paket<br>👥 {{ number_format($distribusi->kelompok->penerima_count ?? 0) }} penerima");
    showmap.setView(parts, 12);
}
</script>
@endsection
