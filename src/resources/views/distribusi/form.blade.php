@extends('layouts.app')
@section('title', isset($distribusi) ? 'Edit Distribusi' : 'Buat Distribusi')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>#pickmap{height:300px;border-radius:10px;border:1px solid #e5e7eb;margin-top:8px;}</style>
@endsection
@section('content')
<div class="card" style="max-width:760px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">{{ isset($distribusi) ? '✏️ Edit Distribusi' : '🚚 Buat Distribusi Baru' }}</h3>
    </div>
    <div style="padding:20px;">
        <form method="POST" enctype="multipart/form-data" action="{{ isset($distribusi) ? route('distribusi.update', $distribusi) : route('distribusi.store') }}">
            @csrf
            @if(isset($distribusi)) @method('PUT') @endif

            <div style="margin-bottom:16px;">
                <label class="form-label">Nama Kegiatan <span style="color:#dc2626;">*</span></label>
                <input type="text" name="nama_kegiatan" class="form-input" required value="{{ old('nama_kegiatan', $distribusi->nama_kegiatan ?? '') }}" placeholder="Distribusi Sembako Batch 5 - ...">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label class="form-label">Tanggal <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="tanggal" class="form-input" required value="{{ old('tanggal', isset($distribusi) ? (is_object($distribusi->tanggal) ? $distribusi->tanggal->format('Y-m-d') : date('Y-m-d', strtotime($distribusi->tanggal))) : '') }}">
                </div>
                <div>
                    <label class="form-label">Kelompok Penerima <span style="color:#dc2626;">*</span></label>
                    <select name="kelompok_id" class="form-input" required id="selKelompok">
                        <option value="">Pilih Kelompok</option>
                        @foreach($kelompoks as $k)
                        <option value="{{ $k->id }}" data-anggota="{{ $k->penerima_count }}" data-ketua="{{ optional($k->ketuaUser)->name ?? '-' }}" {{ old('kelompok_id', $distribusi->kelompok_id ?? '') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} — {{ $k->daerah }} ({{ $k->penerima_count }} penerima)
                        </option>
                        @endforeach
                    </select>
                    <small id="infoKelompok" style="color:#017723;font-size:12px;"></small>
                </div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Lokasi <span style="color:#dc2626;">*</span></label>
                <input type="text" name="lokasi" class="form-input" required value="{{ old('lokasi', $distribusi->lokasi ?? '') }}" placeholder="Gampong ..., Kec. ..., Kabupaten ...">
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Titik Koordinat <span style="color:#dc2626;">*</span> <span style="font-weight:400;color:#6b7280;">(klik peta untuk pilih titik)</span></label>
                <input type="text" name="titik_koordinat" id="koordInput" class="form-input" required value="{{ old('titik_koordinat', $distribusi->titik_koordinat ?? '') }}" placeholder="4.2991424,97.8653578">
                <div id="pickmap"></div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Jenis Bantuan <span style="color:#dc2626;">*</span></label>
                    <select name="jenis_bantuan" class="form-input" required>
                        @foreach(['Sembako','Uang Tunai','Alat Pertanian','Paket Pendidikan','Lainnya'] as $j)
                        <option value="{{ $j }}" {{ old('jenis_bantuan', $distribusi->jenis_bantuan ?? '') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jumlah Paket <span style="color:#dc2626;">*</span></label>
                    <input type="number" name="jumlah_paket" id="jmlPaket" class="form-input" required min="1" max="10000000" value="{{ old('jumlah_paket', $distribusi->jumlah_paket ?? '') }}" placeholder="250">
                </div>
                <div>
                    <label class="form-label">Estimasi Nilai (Rp)</label>
                    <input type="number" name="estimasi_nilai_total" class="form-input" step="0.01" min="0" value="{{ old('estimasi_nilai_total', $distribusi->estimasi_nilai_total ?? 0) }}" placeholder="37500000">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Sumber Dana</label>
                    <input type="text" name="sumber_dana" class="form-input" value="{{ old('sumber_dana', $distribusi->sumber_dana ?? '') }}" placeholder="YPKM - Hong Kong SWAB">
                </div>
                <div>
                    <label class="form-label">Status <span style="color:#dc2626;">*</span></label>
                    <select name="status" class="form-input" required>
                        <option value="direncanakan" {{ old('status', $distribusi->status ?? '') == 'direncanakan' ? 'selected' : '' }}>📋 Direncanakan</option>
                        <option value="berlangsung" {{ old('status', $distribusi->status ?? '') == 'berlangsung' ? 'selected' : '' }}>⏳ Berlangsung</option>
                        <option value="selesai" {{ old('status', $distribusi->status ?? '') == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                        <option value="dibatalkan" {{ old('status', $distribusi->status ?? '') == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-input" rows="3" maxlength="5000" placeholder="Catatan tambahan distribusi...">{{ old('catatan', $distribusi->catatan ?? '') }}</textarea>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Bukti Distribusi</label>
                <input type="file" name="bukti_file" class="form-input" accept=".jpg,.jpeg,.png,.pdf">
                <small style="color:#6b7280;">JPG, PNG, atau PDF; maksimum 5 MB.</small>
                @if(isset($distribusi) && $distribusi->bukti_file)
                    <div style="margin-top:6px;"><a href="{{ Storage::url($distribusi->bukti_file) }}" target="_blank" rel="noopener">Lihat bukti tersimpan</a></div>
                @endif
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                <a href="{{ route('distribusi.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">💾 {{ isset($distribusi) ? 'Update' : 'Simpan' }} Distribusi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Peta picker koordinat
const pickmap = L.map('pickmap').setView([4.7, 96.8], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:18}).addTo(pickmap);
let marker = null;
const koordInput = document.getElementById('koordInput');

function setMarker(lat, lng) {
    if (marker) pickmap.removeLayer(marker);
    marker = L.marker([lat, lng], {icon: L.divIcon({html:'<div style="font-size:15px;text-align:center;line-height:1;">🎁</div>',className:'',iconSize:[24,24],iconAnchor:[12,12]})}).addTo(pickmap);
}

// Existing value
if (koordInput.value && koordInput.value.includes(',')) {
    const parts = koordInput.value.split(',').map(Number);
    if (!isNaN(parts[0]) && !isNaN(parts[1])) { setMarker(parts[0], parts[1]); pickmap.setView([parts[0], parts[1]], 12); }
}

pickmap.on('click', function(e) {
    const lat = e.latlng.lat.toFixed(7), lng = e.latlng.lng.toFixed(7);
    koordInput.value = lat + ',' + lng;
    setMarker(lat, lng);
});

koordInput.addEventListener('change', function() {
    if (this.value.includes(',')) {
        const parts = this.value.split(',').map(Number);
        if (!isNaN(parts[0]) && !isNaN(parts[1])) { setMarker(parts[0], parts[1]); pickmap.setView([parts[0], parts[1]], 12); }
    }
});

// Info kelompok: auto-isi jumlah paket sesuai anggota
const selKelompok = document.getElementById('selKelompok');
const infoKelompok = document.getElementById('infoKelompok');
const jmlPaket = document.getElementById('jmlPaket');
selKelompok.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt.value) {
        infoKelompok.textContent = '👥 ' + opt.dataset.anggota + ' penerima · Ketua: ' + opt.dataset.ketua;
        if (!jmlPaket.value) jmlPaket.value = opt.dataset.anggota;
    } else { infoKelompok.textContent = ''; }
});
if (selKelompok.value) selKelompok.dispatchEvent(new Event('change'));
</script>
@endsection
