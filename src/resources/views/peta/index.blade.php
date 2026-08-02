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
.custom-marker{background:transparent!important;border:0!important;filter:drop-shadow(0 3px 4px rgba(0,3,74,.32));transition:transform .2s ease,filter .2s ease}
.custom-marker:hover{transform:translateY(-3px) scale(1.12);filter:drop-shadow(0 6px 7px rgba(0,3,74,.38));z-index:1000!important}
.custom-marker svg{display:block}
@media(prefers-reduced-motion:reduce){.custom-marker{transition:none}.custom-marker:hover{transform:none}}
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

const markerIcons = {
    selesai: L.divIcon({
        className: 'custom-marker',
        html: `<svg width="28" height="36" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="${colors.selesai}"/><circle cx="12" cy="9.5" r="2.5" fill="white"/></svg>`,
        iconSize: [28, 36],
        iconAnchor: [14, 36],
        popupAnchor: [0, -30]
    }),
    berlangsung: L.divIcon({
        className: 'custom-marker',
        html: `<svg width="28" height="36" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="${colors.berlangsung}"/><circle cx="12" cy="9.5" r="2.5" fill="white"/></svg>`,
        iconSize: [28, 36],
        iconAnchor: [14, 36],
        popupAnchor: [0, -30]
    }),
    direncanakan: L.divIcon({
        className: 'custom-marker',
        html: `<svg width="28" height="36" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="${colors.direncanakan}"/><circle cx="12" cy="9.5" r="2.5" fill="white"/></svg>`,
        iconSize: [28, 36],
        iconAnchor: [14, 36],
        popupAnchor: [0, -30]
    }),
    dibatalkan: L.divIcon({
        className: 'custom-marker',
        html: `<svg width="28" height="36" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="${colors.dibatalkan}"/><path d="M12 7.5v5M12 14.5v.01" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>`,
        iconSize: [28, 36],
        iconAnchor: [14, 36],
        popupAnchor: [0, -30]
    }),
    default: L.divIcon({
        className: 'custom-marker',
        html: `<svg width="28" height="36" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#6b7280"/><circle cx="12" cy="9.5" r="2.5" fill="white"/></svg>`,
        iconSize: [28, 36],
        iconAnchor: [14, 36],
        popupAnchor: [0, -30]
    })
};

const layers = [];

polygons.forEach(p => {
    if (!Array.isArray(p.path)) return;
    const layer = L.polygon(p.path, {color:'#00034a',weight:1.5,fillColor:'#017723',fillOpacity:.07}).addTo(map);
    layer.bindTooltip(p.nama, {sticky:true});
    layers.push(layer);
});

data.forEach(d => {
    if (!Number.isFinite(d.lat) || !Number.isFinite(d.lng) || (!d.lat && !d.lng)) return;
    const marker = L.marker([d.lat,d.lng], {icon: markerIcons[d.status] || markerIcons.default}).addTo(map);
    const statusBadge = {
        selesai: '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600">✅ Selesai</span>',
        berlangsung: '<span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600">⏳ Berlangsung</span>',
        direncanakan: '<span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600">📋 Direncanakan</span>',
        dibatalkan: '<span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600">❌ Dibatalkan</span>'
    }[d.status] || '<span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600">—</span>';
    const icon = (svg, color='#00034a') => `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;flex-shrink:0">${svg}</svg>`;
    const popHtml = `<div style="min-width:280px;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif">
        <div style="font-size:16px;font-weight:700;color:#00034a;margin-bottom:4px;display:flex;align-items:center;gap:8px">${icon('<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>')}${d.name}</div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:10px">${statusBadge}</div>
        <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;line-height:1.5;color:#1f2937">
            <div style="display:flex;align-items:flex-start;gap:8px">${icon('<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>','#017723')}<span><b>Wilayah:</b> ${d.daerah} · ${d.kecamatan} · ${d.desa}</span></div>
            <div style="display:flex;align-items:flex-start;gap:8px">${icon('<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>','#00034a')}<span><b>Lokasi:</b> ${d.lokasi}</span></div>
            <div style="display:flex;align-items:center;gap:8px">${icon('<path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>','#017723')}<span><b>Paket:</b> ${Number(d.paket).toLocaleString('id-ID')}</span></div>
            <div style="display:flex;align-items:center;gap:8px">${icon('<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>','#e5a820')}<span><b>Nilai:</b> ${d.nilai}</span></div>
            <div style="display:flex;align-items:center;gap:8px">${icon('<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>','#00034a')}<span><b>Target:</b> ${Number(d.penerima).toLocaleString('id-ID')} penerima</span></div>
            <div style="display:flex;align-items:center;gap:8px">${icon('<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>','#6b7280')}<span><b>Kelompok:</b> ${d.kelompok}</span></div>
            <div style="display:flex;align-items:center;gap:8px">${icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>','#6b7280')}<span><b>Ketua:</b> ${d.ketua}</span></div>
            <div style="display:flex;align-items:center;gap:8px">${icon('<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>','#00034a')}<span><b>Tanggal:</b> ${d.tgl}</span></div>
        </div>
        <a href="${d.url}" style="display:inline-block;margin-top:12px;padding:8px 14px;background:#017723;color:white;text-decoration:none;border-radius:8px;font-weight:600;font-size:13px;transition:background .15s" onmouseover="this.style.background='#005a1a'" onmouseout="this.style.background='#017723'">Lihat Detail →</a>
    </div>`;
    marker.bindPopup(popHtml);
    layers.push(marker);
});

if (layers.length) {
    const group = L.featureGroup(layers);
    const bounds = group.getBounds();
    if (bounds.isValid()) map.fitBounds(bounds.pad(.12), {maxZoom:13});
}
</script>
@endsection
