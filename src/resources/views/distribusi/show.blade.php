@extends('layouts.app')
@section('title', 'Detail Distribusi')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>#showmap{height:280px;border-radius:10px;border:1px solid #e5e7eb;}</style>
@endsection
@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:1000px;">
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:600;">🚚 {{ $distribusi->nama_kegiatan }}</h3>
            <a href="{{ route('distribusi.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
        </div>
        <div style="padding:20px;">
            <table style="width:100%;font-size:14px;border-collapse:collapse;">
                <tr><td style="padding:6px 0;color:#6b7280;width:45%;">Kode</td><td style="font-weight:600;font-family:monospace;">{{ $distribusi->kode_distribusi }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">📅 Tanggal</td><td style="font-weight:600;">{{ is_object($distribusi->tanggal) ? $distribusi->tanggal->format('d M Y') : date('d M Y', strtotime($distribusi->tanggal)) }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">📍 Lokasi</td><td style="font-weight:600;">{{ $distribusi->lokasi }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">🗺️ Koordinat</td><td style="font-family:monospace;font-size:13px;">{{ $distribusi->titik_koordinat ?? '-' }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">📋 Kelompok</td><td style="font-weight:600;">{{ $distribusi->kelompok->nama ?? '-' }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">👤 Ketua Kelompok</td><td style="font-weight:600;">{{ optional(optional($distribusi->kelompok)->ketuaUser)->name ?? '-' }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">👥 Jumlah Penerima</td><td style="font-weight:600;">{{ number_format($distribusi->kelompok->penerima_count ?? 0) }} orang</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">📦 Jenis / Paket</td><td style="font-weight:600;">{{ $distribusi->jenis_bantuan }} — {{ number_format($distribusi->jumlah_paket) }} paket</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">💰 Estimasi Nilai</td><td style="font-weight:600;color:#017723;">Rp {{ number_format($distribusi->estimasi_nilai_total,0,',','.') }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">💵 Sumber Dana</td><td>{{ $distribusi->sumber_dana ?? '-' }}</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280;">📊 Status</td><td>
                    @if($distribusi->status == 'selesai') <span class="badge badge-green">✅ Selesai</span>
                    @elseif($distribusi->status == 'berlangsung') <span class="badge badge-gold">⏳ Berlangsung</span>
                    @else <span class="badge badge-navy">📋 Rencana</span>
                    @endif
                </td></tr>
            </table>
            <div style="margin-top:16px;display:flex;gap:8px;">
                <a href="{{ route('distribusi.edit', $distribusi) }}" class="btn btn-primary btn-sm">✏️ Edit</a>
                @if($distribusi->status != 'selesai')
                <form method="POST" action="{{ route('distribusi.selesai', $distribusi) }}">
                    @csrf
                    <button class="btn btn-sm" style="background:#017723;color:white;">✅ Tandai Selesai</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">🗺️ Lokasi Distribusi</h3>
        </div>
        <div style="padding:16px 20px;">
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
