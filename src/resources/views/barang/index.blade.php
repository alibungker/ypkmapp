@extends('layouts.app')
@section('title', 'Manajemen Barang & Kegiatan')
@section('subtitle', 'Tambah, edit, hapus item anggaran kegiatan & pembelian barang')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

{{-- Tabs --}}
<div style="display:flex;gap:4px;margin-bottom:20px;">
    <button onclick="showTab('kegiatan')" id="tab_kegiatan" style="padding:10px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;background:#00034a;color:white;">📦 Kegiatan</button>
    <button onclick="showTab('pembelian')" id="tab_pembelian" style="padding:10px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;background:#e5e7eb;color:#374151;">📋 Pembelian Barang</button>
</div>

{{-- Tab KEGIATAN --}}
<div id="panel_kegiatan">
    <div style="display:grid;grid-template-columns:400px 1fr;gap:20px;align-items:start;">
        {{-- Form Tambah Kegiatan --}}
        <div class="card">
            <div style="padding:14px 18px;border-bottom:1px solid #e5e7eb;"><h3 style="font-size:14px;font-weight:600;">➕ Tambah Kegiatan</h3></div>
            <div style="padding:18px;">
                <form method="POST" action="{{ route('barang.kegiatan.store') }}">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <label class="form-label">Nama Kegiatan <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="nama_anggaran" class="form-input" required placeholder="Batch 4 — 5.000 Paket">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <label class="form-label">Kategori <span style="color:#dc2626;">*</span></label>
                            <select name="kategori" class="form-input" required>
                                <option value="barang_bantuan">📦 Barang Bantuan</option>
                                <option value="transportasi">🚛 Transportasi</option>
                                <option value="konsumsi">🍱 Konsumsi</option>
                                <option value="sewa">🏠 Sewa</option>
                                <option value="atk">📎 ATK</option>
                                <option value="cadangan">💰 Cadangan</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Target Paket</label>
                            <input type="number" name="target_paket" class="form-input" placeholder="5000">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-top:10px;">
                        <div>
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-input" placeholder="paket">
                        </div>
                        <div>
                            <label class="form-label">Anggaran (Rp) <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="anggaran" class="form-input" required step="0.01" placeholder="1000000000">
                        </div>
                        <div>
                            <label class="form-label">Realisasi (Rp) <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="realisasi" class="form-input" required step="0.01" placeholder="1000000000">
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <label class="form-label">Catatan/Status</label>
                        <input type="text" name="catatan" class="form-input" value="✅ Lunas" placeholder="✅ Lunas">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:12px;">💾 Simpan Kegiatan</button>
                </form>
            </div>
        </div>
        {{-- Tabel Kegiatan --}}
        <div class="card">
            <div style="padding:14px 18px;border-bottom:1px solid #e5e7eb;">
                <h3 style="font-size:14px;font-weight:600;">📋 Daftar Kegiatan</h3>
            </div>
            <div style="padding:14px 18px;overflow-x:auto;">
                <table class="table-data">
                    <thead><tr><th>No</th><th>Nama Kegiatan</th><th>Target</th><th>Anggaran</th><th>Realisasi</th><th>%</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($anggarans as $i => $a)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td style="font-weight:500;">{{ $a->nama_anggaran ?? $a->kategori }}</td>
                            <td>{{ $a->target_paket ? number_format($a->target_paket).' '.$a->satuan : '-' }}</td>
                            <td>Rp {{ number_format($a->anggaran,0,',','.') }}</td>
                            <td>Rp {{ number_format($a->realisasi,0,',','.') }}</td>
                            <td>{{ $a->anggaran > 0 ? round(($a->realisasi/$a->anggaran)*100,1) : 0 }}%</td>
                            <td><span class="badge badge-green">✅ {{ $a->catatan ?? 'Lunas' }}</span></td>
                            <td style="white-space:nowrap;">
                                <button onclick="editKegiatan({{ $a->id }})" style="color:#00034a;border:none;background:none;cursor:pointer;font-size:12px;">✏️</button>
                                <form method="POST" action="{{ route('barang.kegiatan.destroy', $a) }}" style="display:inline;" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:12px;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="padding:24px;text-align:center;color:#9ca3af;">Belum ada kegiatan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Tab PEMBELIAN BARANG --}}
<div id="panel_pembelian" style="display:none;">
    <div style="display:grid;grid-template-columns:400px 1fr;gap:20px;align-items:start;">
        {{-- Form Tambah Pembelian --}}
        <div class="card">
            <div style="padding:14px 18px;border-bottom:1px solid #e5e7eb;"><h3 style="font-size:14px;font-weight:600;">➕ Tambah Item Barang</h3></div>
            <div style="padding:18px;">
                <form method="POST" action="{{ route('barang.pembelian.store') }}">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <label class="form-label">Nama Barang <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="nama_barang" class="form-input" required placeholder="Beras Rojolele 10 Kg">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <label class="form-label">Batch</label>
                            <input type="text" name="batch" class="form-input" placeholder="Batch 4">
                        </div>
                        <div>
                            <label class="form-label">Harga Satuan (Rp) <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="harga_satuan" class="form-input" required step="0.01" placeholder="154000">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
                        <div>
                            <label class="form-label">Qty Rencana <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="qty_rencana" class="form-input" required placeholder="9500">
                        </div>
                        <div>
                            <label class="form-label">Qty Terbeli</label>
                            <input type="number" name="qty_terbeli" class="form-input" placeholder="4500">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
                        <div>
                            <label class="form-label">Anggaran (Rp) <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="anggaran" class="form-input" required step="0.01" placeholder="1463000000">
                        </div>
                        <div>
                            <label class="form-label">Realisasi (Rp)</label>
                            <input type="number" name="realisasi" class="form-input" step="0.01" placeholder="693000000">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:12px;">💾 Simpan Item Barang</button>
                </form>
            </div>
        </div>
        {{-- Tabel Pembelian --}}
        <div class="card">
            <div style="padding:14px 18px;border-bottom:1px solid #e5e7eb;">
                <h3 style="font-size:14px;font-weight:600;">📋 Daftar Pembelian Barang</h3>
            </div>
            <div style="padding:14px 18px;overflow-x:auto;">
                <table class="table-data">
                    <thead><tr><th>No</th><th>Nama Barang</th><th>Batch</th><th>Qty Renc</th><th>Qty Beli</th><th>Belum</th><th>Harga</th><th>Anggaran</th><th>Realisasi</th><th>Sisa</th><th>%</th><th></th></tr></thead>
                    <tbody>
                        @forelse($pembelian as $i => $p)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td style="font-weight:500;font-size:13px;">{{ $p->nama_barang }}</td>
                            <td style="font-size:12px;">{{ $p->batch ?? '-' }}</td>
                            <td>{{ number_format($p->qty_rencana) }}</td>
                            <td>{{ number_format($p->qty_terbeli) }}</td>
                            <td>{{ $p->qty_belum > 0 ? number_format($p->qty_belum) : '< 0.00 >' }}</td>
                            <td>{{ number_format($p->harga_satuan) }}</td>
                            <td>{{ number_format($p->anggaran) }}</td>
                            <td>{{ number_format($p->realisasi) }}</td>
                            <td>{{ number_format($p->sisa) }}</td>
                            <td><span style="color:{{ $p->persen_real >= 100 ? '#017723' : '#e5a820' }};">{{ $p->persen_real }}%</span></td>
                            <td style="white-space:nowrap;">
                                <button onclick="editPembelian({{ $p->id }})" style="color:#00034a;border:none;background:none;cursor:pointer;font-size:12px;">✏️</button>
                                <form method="POST" action="{{ route('barang.pembelian.destroy', $p) }}" style="display:inline;" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:12px;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="12" style="padding:24px;text-align:center;color:#9ca3af;">Belum ada item barang</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal Kegiatan --}}
<div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEdit()">
    <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:500px;max-height:90vh;overflow-y:auto;" onclick="event.stopPropagation()">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">✏️ Edit</h3>
        <div id="editFormContainer"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showTab(tab) {
    document.getElementById('panel_kegiatan').style.display = tab === 'kegiatan' ? 'block' : 'none';
    document.getElementById('panel_pembelian').style.display = tab === 'pembelian' ? 'block' : 'none';
    document.getElementById('tab_kegiatan').style.background = tab === 'kegiatan' ? '#00034a' : '#e5e7eb';
    document.getElementById('tab_pembelian').style.background = tab === 'pembelian' ? '#00034a' : '#e5e7eb';
    document.getElementById('tab_kegiatan').style.color = tab === 'kegiatan' ? 'white' : '#374151';
    document.getElementById('tab_pembelian').style.color = tab === 'pembelian' ? 'white' : '#374151';
}

// Set default tab
showTab('kegiatan');

function closeEdit() { document.getElementById('editModal').style.display = 'none'; }
window.editKegiatan = function(id) {
    fetch('/barang/kegiatan/' + id + '/edit')
        .then(r => r.text())
        .then(html => { document.getElementById('editFormContainer').innerHTML = html; document.getElementById('editModal').style.display = 'flex'; })
        .catch(() => alert('Gagal memuat form edit'));
};
window.editPembelian = function(id) {
    fetch('/barang/pembelian/' + id + '/edit')
        .then(r => r.text())
        .then(html => { document.getElementById('editFormContainer').innerHTML = html; document.getElementById('editModal').style.display = 'flex'; })
        .catch(() => alert('Gagal memuat form edit'));
};
</script>
@endsection
