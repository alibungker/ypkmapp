@extends('layouts.app')
@section('title', 'Peta Distribusi')
@section('subtitle', 'Peta interaktif distribusi bantuan PEDULI YPKM di Aceh')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#map{height:480px;border-radius:12px;border:1px solid #e5e7eb}
.legend-item{display:flex;align-items:center;gap:8px;font-size:13px}
.legend-dot{width:14px;height:14px;border-radius:50%;border:2px solid white;box-shadow:0 1px 3px rgba(0,0,0,0.3)}
</style>
@endsection

@section('content')
{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px;">
    <div class="stat-card"><div class="stat-value">{{ count($distribusi) }}</div><div class="stat-label">Distribusi</div></div>
    <div class="stat-card"><div class="stat-value" style="color:#017723;">{{ number_format($distribusi->sum('paket')) }}</div><div class="stat-label">Total Paket</div></div>
    <div class="stat-card"><div class="stat-value">{{ number_format($distribusi->sum('penerima')) }}</div><div class="stat-label">Penerima Manfaat</div></div>
    <div class="stat-card"><div class="stat-value">{{ count(collect($distribusi)->pluck('daerah')->unique()) }}</div><div class="stat-label">Daerah</div></div>
</div>

{{-- Map + Legend --}}
<div class="card" style="margin-bottom:20px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">🗺️ Peta Sebaran Distribusi</h3>
    </div>
    <div style="padding:16px 20px;">
        <div id="map"></div>
        <div style="display:flex;gap:24px;margin-top:12px;">
            <div class="legend-item"><span class="legend-dot" style="background:#017723;"></span> Selesai (5)</div>
            <div class="legend-item"><span class="legend-dot" style="background:#e5a820;"></span> Berlangsung (2)</div>
            <div class="legend-item"><span class="legend-dot" style="background:#00034a;"></span> Rencana (3)</div>
            <div class="legend-item"><span class="legend-dot" style="background:#ef4444;"></span> Kantor YPKM</div>
        </div>
    </div>
</div>

{{-- Table + Charts --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Table --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">📍 Daftar Distribusi</h3>
        </div>
        <div style="padding:16px 20px;overflow-x:auto;">
            <table class="table-data">
                <thead><tr><th>Daerah</th><th>Kec.</th><th>Paket</th><th>Nilai</th><th>Penerima</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td><strong>Aceh Tamiang</strong></td><td>Sekerak</td><td>500</td><td>Rp 75 Jt</td><td>342 KK</td><td><span class="badge badge-green">✅</span></td></tr>
                    <tr><td><strong>Aceh Tamiang</strong></td><td>Karang Baru</td><td>250</td><td>Rp 37,5 Jt</td><td>180 KK</td><td><span class="badge badge-green">✅</span></td></tr>
                    <tr><td><strong>Pidie</strong></td><td>Mutiara</td><td>300</td><td>Rp 45 Jt</td><td>281 KK</td><td><span class="badge badge-gold">⏳</span></td></tr>
                    <tr><td><strong>Aceh Utara</strong></td><td>Lhoksukon</td><td>200</td><td>Rp 30 Jt</td><td>198 KK</td><td><span class="badge badge-gold">⏳</span></td></tr>
                    <tr><td><strong>Bireuen</strong></td><td>Jeunieb</td><td>150</td><td>Rp 22,5 Jt</td><td>156 KK</td><td><span class="badge badge-navy">📋</span></td></tr>
                    <tr><td><strong>Subulussalam</strong></td><td>Penanggalan</td><td>120</td><td>Rp 18 Jt</td><td>120 KK</td><td><span class="badge badge-navy">📋</span></td></tr>
                    <tr><td><strong>Aceh Besar</strong></td><td>Indrapuri</td><td>200</td><td>Rp 30 Jt</td><td>200 KK</td><td><span class="badge badge-navy">📋</span></td></tr>
                    <tr style="background:#f8f9fa;font-weight:600;"><td colspan="2">Total</td><td>1.720</td><td>Rp 258 Jt</td><td>1.477 KK</td><td></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">📊 Distribusi per Daerah</h3>
        </div>
        <div style="padding:16px 20px;">
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>Aceh Tamiang</span><span>750 paket</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:100%;background:#017723;"></div></div>
            </div>
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>Pidie</span><span>300 paket</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:40%;background:#e5a820;"></div></div>
            </div>
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>Aceh Utara</span><span>200 paket</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:27%;background:#e5a820;"></div></div>
            </div>
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>Bireuen</span><span>150 paket</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:20%;background:#00034a;"></div></div>
            </div>
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>Subulussalam</span><span>120 paket</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:16%;background:#00034a;"></div></div>
            </div>
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;"><span>Aceh Besar</span><span>200 paket</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:27%;background:#00034a;"></div></div>
            </div>
            <div style="margin-top:16px;background:#f8f9fa;border-radius:8px;padding:16px;">
                <div style="font-size:13px;font-weight:600;margin-bottom:8px;">📍 Jangkauan Wilayah</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span style="padding:4px 10px;background:white;border-radius:6px;font-size:12px;border:1px solid #e5e7eb;">🏛️ 6 Kabupaten/Kota</span>
                    <span style="padding:4px 10px;background:white;border-radius:6px;font-size:12px;border:1px solid #e5e7eb;">📌 13 Kecamatan</span>
                    <span style="padding:4px 10px;background:white;border-radius:6px;font-size:12px;border:1px solid #e5e7eb;">👥 48 Gampong</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([4.9, 96.5], 8);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:18}).addTo(map);

// Kantor YPKM
L.marker([4.92, 96.50], {icon:L.divIcon({html:'🏢',className:'',iconSize:[24,24]})})
 .addTo(map).bindPopup('<b>📍 Kantor YPKM</b><br>Banda Aceh');

// Data distribusi
const data = [
    @foreach($distribusi as $d)
    {name:'{{ $d['name'] }} ({{ $d['daerah'] }})',lat:{{ $d['lat'] }},lng:{{ $d['lng'] }},paket:{{ $d['paket'] }},nilai:'{{ $d['nilai'] }}',penerima:{{ $d['penerima'] }},status:'{{ $d['status'] }}',tgl:'{{ $d['tgl'] }}'},
    @endforeach
];
const colors = {done:'#017723',progress:'#e5a820',plan:'#00034a'};
data.forEach(d => {
    L.circleMarker([d.lat,d.lng],{radius:Math.max(6,d.paket/30),fillColor:colors[d.status],color:'white',weight:2,fillOpacity:.7})
    .addTo(map).bindPopup(`
        <div style="min-width:220px;">
            <div style="font-size:15px;font-weight:700;color:#00034a;margin-bottom:8px;">📍 ${d.name}</div>
            <table style="width:100%;font-size:13px;border-collapse:collapse;">
                <tr><td style="padding:3px 0;color:#6b7280;">📦 Distribusi</td><td style="padding:3px 0;font-weight:600;text-align:right;">${d.paket} paket</td></tr>
                <tr><td style="padding:3px 0;color:#6b7280;">💰 Nilai Bantuan</td><td style="padding:3px 0;font-weight:600;text-align:right;">${d.nilai}</td></tr>
                <tr><td style="padding:3px 0;color:#6b7280;">👥 Penerima</td><td style="padding:3px 0;font-weight:600;text-align:right;">${d.penerima} KK</td></tr>
                <tr><td style="padding:3px 0;color:#6b7280;">📋 Kelompok</td><td style="padding:3px 0;font-weight:600;text-align:right;">${d.kelompok} kelompok</td></tr>
                <tr><td style="padding:3px 0;color:#6b7280;">📅 Tanggal</td><td style="padding:3px 0;font-weight:600;text-align:right;">${d.tgl}</td></tr>
                <tr><td style="padding:3px 0;color:#6b7280;">📊 Status</td>
                    <td style="padding:3px 0;text-align:right;">${d.status=='done'?'<span style="color:#017723;font-weight:600;">✅ Selesai</span>':d.status=='progress'?'<span style="color:#e5a820;font-weight:600;">⏳ Berlangsung</span>':'<span style="color:#00034a;font-weight:600;">📋 Rencana</span>'}</td></tr>
            </table>
        </div>
    `);
});

// Fit bounds
const group = L.featureGroup(data.map(d=>L.circleMarker([d.lat,d.lng])));
map.fitBounds(group.getBounds().pad(.15));
</script>
@endsection
