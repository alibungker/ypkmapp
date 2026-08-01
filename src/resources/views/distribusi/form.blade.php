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

            <div style="margin-top:18px;padding:16px;border:1px solid #d6e4ff;border-radius:12px;background:#f8fbff;">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:10px;">
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#00034a;">📦 Barang Keluar dari Pembelian</div>
                        <div style="font-size:11px;color:#6b7280;margin-top:3px;">Pilih barang pembelian yang dipakai. Stok tersisa otomatis berkurang.</div>
                    </div>
                    <button type="button" id="addDistribusiBarang" class="btn btn-outline btn-sm">+ Tambah Barang</button>
                </div>
                <div id="distribusiBarangRows" style="display:grid;gap:9px;"></div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-input" rows="3" maxlength="5000" placeholder="Catatan tambahan distribusi...">{{ old('catatan', $distribusi->catatan ?? '') }}</textarea>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Foto & Dokumen Hasil Lapangan</label>
                <input type="file" name="lampiran[]" class="form-input" accept=".jpg,.jpeg,.png,.pdf" multiple>
                <small style="color:#6b7280;display:block;margin-top:6px;line-height:1.5;">
                    Pilih beberapa file sekaligus. Format JPG, JPEG, PNG, atau PDF; maksimum 5 MB per file dan 10 file setiap unggahan.
                </small>

                @if(isset($distribusi) && $distribusi->lampiran->isNotEmpty())
                    <div style="margin-top:14px;padding:14px;border:1px solid #e5e7eb;border-radius:10px;background:#f8fafc;">
                        <div style="font-size:13px;font-weight:700;color:#00034a;margin-bottom:10px;">Lampiran tersimpan ({{ $distribusi->lampiran->count() }})</div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;">
                            @foreach($distribusi->lampiran as $file)
                                <div style="border:1px solid #e5e7eb;border-radius:9px;background:white;padding:10px;min-width:0;">
                                    @if($file->jenis === 'foto')
                                        <a href="{{ Storage::url($file->path) }}" target="_blank" rel="noopener" style="display:block;">
                                            <img src="{{ Storage::url($file->path) }}" alt="{{ $file->nama_asli }}" style="width:100%;height:110px;object-fit:cover;border-radius:7px;background:#f3f4f6;">
                                        </a>
                                    @else
                                        <div style="height:62px;display:flex;align-items:center;justify-content:center;border-radius:7px;background:#eef2ff;color:#00034a;font-weight:700;">PDF</div>
                                    @endif
                                    <a href="{{ Storage::url($file->path) }}" target="_blank" rel="noopener" style="display:block;margin-top:8px;font-size:12px;color:#00034a;overflow-wrap:anywhere;">{{ $file->nama_asli }}</a>
                                    <div style="font-size:11px;color:#6b7280;margin-top:3px;">{{ $file->ukuran ? number_format($file->ukuran / 1024, 0, ',', '.') . ' KB' : 'File lama' }}</div>
                                    <label style="display:flex;align-items:center;gap:7px;margin-top:8px;font-size:12px;color:#b91c1c;cursor:pointer;">
                                        <input type="checkbox" name="hapus_lampiran[]" value="{{ $file->id }}">
                                        Hapus saat disimpan
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif(isset($distribusi) && $distribusi->bukti_file)
                    <div style="margin-top:8px;"><a href="{{ Storage::url($distribusi->bukti_file) }}" target="_blank" rel="noopener">Lihat bukti lama</a></div>
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

// Alokasi barang pembelian ke distribusi
@php
$purchaseItemsJs = $pembelian->map(function ($p) {
    return [
        'id' => $p->id,
        'label' => $p->nama_barang . ($p->batch ? ' — '.$p->batch : ''),
        'stok' => $p->stok_tersedia,
        'harga' => $p->harga_satuan,
    ];
})->values();
$existingPurchasesJs = old('pembelian_barang');
if ($existingPurchasesJs === null) {
    $existingPurchasesJs = isset($distribusi)
        ? $distribusi->pembelianBarang->map(function ($p) {
            return ['pembelian_barang_id' => $p->id, 'jumlah' => $p->pivot->jumlah];
        })->values()->all()
        : [];
}
@endphp
const purchaseItems = @json($purchaseItemsJs);
const purchaseRows = document.getElementById('distribusiBarangRows');
let purchaseIndex = 0;
function addPurchaseRow(selected = '', jumlah = '') {
    const row = document.createElement('div');
    row.style.cssText = 'display:grid;grid-template-columns:minmax(0,1fr) 150px 44px;gap:8px;align-items:end;padding:10px;background:white;border:1px solid #e5e7eb;border-radius:10px;';
    const options = purchaseItems.map(item => `<option value="${item.id}" data-stok="${item.stok}" ${String(item.id)===String(selected)?'selected':''}>${item.label} — stok ${item.stok}</option>`).join('');
    row.innerHTML = `<div><label class="form-label">Barang Pembelian</label><select class="form-input purchase-select" name="pembelian_barang[${purchaseIndex}][pembelian_barang_id]" required><option value="">-- Pilih barang --</option>${options}</select></div><div><label class="form-label">Jumlah Keluar</label><input class="form-input purchase-qty" type="number" min="1" name="pembelian_barang[${purchaseIndex}][jumlah]" value="${jumlah}" required></div><button type="button" class="purchase-remove" style="width:44px;height:44px;border:1px solid #fecaca;border-radius:8px;background:white;color:#dc2626;cursor:pointer;">×</button>`;
    purchaseRows.appendChild(row); purchaseIndex++;
    const sel = row.querySelector('.purchase-select'), qty = row.querySelector('.purchase-qty');
    const sync = () => { const stok = sel.selectedOptions[0]?.dataset.stok || ''; qty.max = stok; qty.title = stok ? `Stok tersedia ${stok}` : ''; };
    sel.addEventListener('change', sync); sync();
    row.querySelector('.purchase-remove').addEventListener('click', () => row.remove());
}
document.getElementById('addDistribusiBarang').addEventListener('click', () => addPurchaseRow());
const existingPurchases = @json($existingPurchasesJs);
existingPurchases.forEach(item => addPurchaseRow(item.pembelian_barang_id, item.jumlah));
</script>
@endsection
