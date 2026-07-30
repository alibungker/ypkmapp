@extends('layouts.app')
@section('title', 'Peta Distribusi')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>#map{height:500px;border-radius:12px}</style>
@endsection
@section('content')
<div class="bg-white rounded-xl border p-6">
    <h3 class="font-semibold mb-2">🗺️ Peta Distribusi PEDULI YPKM</h3>
    <p class="text-sm text-gray-500 mb-4">Peta interaktif distribusi bantuan di Aceh</p>
    <div id="map"></div>
</div>
@endsection
@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([4.9, 96.5], 8);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:18}).addTo(map);
const data = [
    {name:'Aceh Tamiang (Sekerak)',lat:4.25,lng:97.95,paket:500,nilai:'Rp 75 Juta',penerima:342,status:'done'},
    {name:'Aceh Tamiang (Karang Baru)',lat:4.30,lng:97.85,paket:250,nilai:'Rp 37,5 Juta',penerima:180,status:'done'},
    {name:'Pidie (Mutiara)',lat:5.25,lng:95.95,paket:300,nilai:'Rp 45 Juta',penerima:281,status:'progress'},
    {name:'Aceh Utara (Lhoksukon)',lat:5.05,lng:97.30,paket:200,nilai:'Rp 30 Juta',penerima:198,status:'progress'},
    {name:'Bireuen (Jeunieb)',lat:5.20,lng:96.70,paket:150,nilai:'Rp 22,5 Juta',penerima:156,status:'plan'},
    {name:'Subulussalam',lat:2.65,lng:98.00,paket:120,nilai:'Rp 18 Juta',penerima:120,status:'plan'},
    {name:'Aceh Besar (Indrapuri)',lat:5.40,lng:95.55,paket:200,nilai:'Rp 30 Juta',penerima:200,status:'plan'}
];
const colors = {done:'#017723',progress:'#e5a820',plan:'#00034a'};
data.forEach(d => {
    L.circleMarker([d.lat,d.lng],{radius:d.paket/30,fillColor:colors[d.status],color:'white',weight:2,fillOpacity:.7})
    .addTo(map).bindPopup(`<b>${d.name}</b><br>📦 ${d.paket} paket<br>💰 ${d.nilai}<br>👥 ${d.penerima} KK`);
});
const group = L.featureGroup(data.map(d=>L.circleMarker([d.lat,d.lng])));
map.fitBounds(group.getBounds().pad(.15));
</script>
@endsection
