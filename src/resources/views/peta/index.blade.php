@extends('layouts.app')
@section('title', 'Peta Distribusi')
@section('subtitle', 'Data distribusi aktual berdasarkan hak akses dan wilayah kerja')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#map{height:520px;border-radius:12px;border:1px solid #e5e7eb}
.legend{display:flex;gap:18px;margin-top:12px;flex-wrap:wrap}
.legend-item{display:flex;align-items:center;gap:7px;font-size:13px;color:#4b5563}
.legend-dot{width:12px;height:12px;border-radius:50%;border:2px solid white;box-shadow:0 1px 3px rgba(0,0,0,.3)}
.peta-grid{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(320px,.75fr);gap:20px}
@media(max-width:900px){.peta-grid{grid-template-columns:1fr}#map{height:55dvh;min-height:360px}.legend{gap:10px 14px}}
@media(max-width:480px){#map{min-height:340px}.leaflet-control-zoom a{width:36px!important;height:36px!important;line-height:36px!important}}
</style>
@endsection

@section('content')
<div class="mobile-two" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:20px;">
    <div class="stat-card"><div class="stat-value">{{ number_format($distribusi->count()) }}</div><div class="stat-label">Distribusi</div></div>
    <div class="stat-card"><div class="stat-value" style="color:#017723;">{{ number_format($distribusi->sum('paket')) }}</div><div class="stat-label">Total Paket</div></div>
    <div class="stat-card"><div class="stat-value">{{ number_format($distribusi->sum('penerima')) }}</div><div class="stat-label">Target Penerima</div></div>
    <div class="stat-card"><div class="stat-value" style="font-size:24px;">Rp {{ number_format($distribusi->sum('nilai_raw'),0,',','.') }}</div><div class="stat-label">Nilai Bantuan</div></div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div><h3 style="font-size:15px;font-weight:600;">🗺️ Peta Sebaran Distribusi</h3><small style="color:#6b7280;">Polygon dan marker bersumber dari database</small></div>
        <div style="font-size:12px;color:#6b7280;">{{ $wilayahStats['kabupaten'] }} kab/kota · {{ $wilayahStats['kecamatan'] }} kecamatan · {{ $wilayahStats['desa'] }} desa</div>
    </div>
    <div style="padding:16px 20px;">
        <div id="map"></div>
        <div class="legend">
            <div class="legend-item"><span class="legend-dot" style="background:#017723;"></span>Selesai ({{ $statusCounts['selesai'] ?? 0 }})</div>
            <div class="legend-item"><span class="legend-dot" style="background:#e5a820;"></span>Berlangsung ({{ $statusCounts['berlangsung'] ?? 0 }})</div>
            <div class="legend-item"><span class="legend-dot" style="background:#00034a;"></span>Direncanakan ({{ $statusCounts['direncanakan'] ?? 0 }})</div>
            <div class="legend-item"><span class="legend-dot" style="background:#dc2626;"></span>Dibatalkan ({{ $statusCounts['dibatalkan'] ?? 0 }})</div>
        </div>
    </div>
</div>

<div class="peta-grid">
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;"><h3 style="font-size:15px;font-weight:600;">📍 Daftar Distribusi Aktual</h3></div>
        <div style="padding:16px 20px;overflow-x:auto;">
            <table class="table-data">
                <thead><tr><th>Kegiatan/Wilayah</th><th>Tanggal</th><th>Paket</th><th>Nilai</th><th>Penerima</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($distribusi as $d)
                    <tr>
                        <td><a href="{{ $d['url'] }}" style="font-weight:600;color:#00034a;text-decoration:none;">{{ $d['name'] }}</a><br><small style="color:#6b7280;">{{ $d['daerah'] }} · {{ $d['kecamatan'] }} · {{ $d['desa'] }}</small></td>
                        <td>{{ $d['tgl'] }}</td>
                        <td>{{ number_format($d['paket']) }}</td>
                        <td>{{ $d['nilai'] }}</td>
                        <td>{{ number_format($d['penerima']) }}</td>
                        <td>
                            @if($d['status']==='selesai')<span class="badge badge-green">✅ Selesai</span>
                            @elseif($d['status']==='berlangsung')<span class="badge badge-gold">⏳ Berlangsung</span>
                            @elseif($d['status']==='dibatalkan')<span class="badge" style="background:#fee2e2;color:#b91c1c;">❌ Dibatalkan</span>
                            @else<span class="badge badge-navy">📋 Rencana</span>@endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada distribusi dengan koordinat.</td></tr>
                    @endforelse
                </tbody>
                @if($distribusi->isNotEmpty())
                <tfoot><tr style="font-weight:700;background:#f8f9fa;"><td colspan="2">Total</td><td>{{ number_format($distribusi->sum('paket')) }}</td><td>Rp {{ number_format($distribusi->sum('nilai_raw'),0,',','.') }}</td><td>{{ number_format($distribusi->sum('penerima')) }}</td><td></td></tr></tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;"><h3 style="font-size:15px;font-weight:600;">📊 Paket per Daerah</h3></div>
        <div style="padding:20px;">
            @php($maxPaket = max(1, (int) ($perDaerah->max('paket') ?? 1)))
            @forelse($perDaerah as $row)
            <div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;gap:12px;font-size:13px;margin-bottom:5px;"><span style="font-weight:600;">{{ $row['daerah'] }}</span><span>{{ number_format($row['paket']) }} paket</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:{{ round(($row['paket']/$maxPaket)*100,1) }}%;background:#017723;"></div></div>
                <small style="color:#6b7280;">{{ $row['distribusi'] }} kegiatan · {{ number_format($row['penerima']) }} target · Rp {{ number_format($row['nilai'],0,',','.') }}</small>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:#9ca3af;">Belum ada data daerah.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([4.2, 96.9], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:18, attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
const data = @json($distribusi->values());
const polygons = @json($polygons);
const colors = {selesai:'#017723',berlangsung:'#e5a820',direncanakan:'#00034a',dibatalkan:'#dc2626'};
const layers = [];

polygons.forEach(p => {
    if (!Array.isArray(p.path)) return;
    const layer = L.polygon(p.path, {color:'#00034a',weight:1.5,fillColor:'#017723',fillOpacity:.07}).addTo(map);
    layer.bindTooltip(p.nama, {sticky:true});
    layers.push(layer);
});

data.forEach(d => {
    if (!Number.isFinite(d.lat) || !Number.isFinite(d.lng) || (!d.lat && !d.lng)) return;
    const marker = L.circleMarker([d.lat,d.lng], {radius:9,color:'#fff',weight:2,fillColor:colors[d.status] || '#00034a',fillOpacity:1}).addTo(map);
    marker.bindPopup(`<div style="min-width:230px"><div style="font-size:15px;font-weight:700;color:#00034a;margin-bottom:8px">${d.name}</div><div style="font-size:13px;line-height:1.65"><b>Wilayah:</b> ${d.daerah} · ${d.kecamatan} · ${d.desa}<br><b>Lokasi:</b> ${d.lokasi}<br><b>Paket:</b> ${Number(d.paket).toLocaleString('id-ID')}<br><b>Nilai:</b> ${d.nilai}<br><b>Target:</b> ${Number(d.penerima).toLocaleString('id-ID')} penerima<br><b>Kelompok:</b> ${d.kelompok}<br><b>Ketua:</b> ${d.ketua}<br><b>Tanggal:</b> ${d.tgl}</div><a href="${d.url}" style="display:inline-block;margin-top:9px;color:#017723;font-weight:700">Lihat detail →</a></div>`);
    layers.push(marker);
});

if (layers.length) {
    const group = L.featureGroup(layers);
    const bounds = group.getBounds();
    if (bounds.isValid()) map.fitBounds(bounds.pad(.12), {maxZoom:13});
}
</script>
@endsection
