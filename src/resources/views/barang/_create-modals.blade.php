{{-- Modal Tambah Kegiatan --}}
<div class="create-modal" id="createKegiatanModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="createKegiatanTitle">
    <div class="create-modal__backdrop" data-close-modal></div>
    <div class="create-modal__panel" role="document">
        <div class="create-modal__header">
            <div><span class="create-modal__eyebrow">KEGIATAN BARU</span><h2 id="createKegiatanTitle">Tambah Kegiatan</h2></div>
            <button type="button" class="create-modal__close" data-close-modal aria-label="Tutup modal">×</button>
        </div>
        <form method="POST" action="{{ route('barang.kegiatan.store') }}" class="create-modal__form">
            @csrf <input type="hidden" name="form_type" value="kegiatan">
            <div class="form-field form-field--full"><label class="form-label">Nama Kegiatan <span>*</span></label><input type="text" name="nama_anggaran" class="form-input" required value="{{ old('form_type') === 'kegiatan' ? old('nama_anggaran') : '' }}" placeholder="Contoh: Distribusi Sembako Batch 4"></div>
            <div class="form-field"><label class="form-label">Kategori <span>*</span></label><select name="kategori" class="form-input" required>@foreach(['barang_bantuan'=>'Barang Bantuan','transportasi'=>'Transportasi','konsumsi'=>'Konsumsi','sewa'=>'Sewa','atk'=>'ATK','cadangan'=>'Cadangan'] as $value=>$label)<option value="{{ $value }}" @selected(old('form_type') === 'kegiatan' && old('kategori') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="form-field"><label class="form-label">Target Paket</label><input type="number" min="0" name="target_paket" class="form-input" value="{{ old('form_type') === 'kegiatan' ? old('target_paket') : '' }}" placeholder="5000"></div>
            <div class="form-field"><label class="form-label">Satuan</label><input type="text" name="satuan" class="form-input" value="{{ old('form_type') === 'kegiatan' ? old('satuan') : '' }}" placeholder="paket"></div>
            <div class="form-field"><label class="form-label">Anggaran (Rp) <span>*</span></label><input type="number" min="0" step="0.01" name="anggaran" class="form-input" required value="{{ old('form_type') === 'kegiatan' ? old('anggaran') : '' }}"></div>
            <div class="form-field"><label class="form-label">Realisasi (Rp) <span>*</span></label><input type="number" min="0" step="0.01" name="realisasi" class="form-input" required value="{{ old('form_type') === 'kegiatan' ? old('realisasi', 0) : 0 }}"></div>
            <div class="form-field form-field--full"><label class="form-label">Catatan/Status</label><input type="text" name="catatan" class="form-input" value="{{ old('form_type') === 'kegiatan' ? old('catatan') : '' }}" placeholder="Contoh: Direncanakan"></div>
            <div class="create-modal__actions"><button type="button" class="btn btn-secondary" data-close-modal>Batal</button><button type="submit" class="btn btn-primary">Simpan Kegiatan</button></div>
        </form>
    </div>
</div>

{{-- Modal Pembelian Barang --}}
<div class="create-modal" id="createPembelianModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="createPembelianTitle">
    <div class="create-modal__backdrop" data-close-modal></div>
    <div class="create-modal__panel" role="document">
        <div class="create-modal__header"><div><span class="create-modal__eyebrow">TRANSAKSI BARU</span><h2 id="createPembelianTitle">Pembelian Barang</h2></div><button type="button" class="create-modal__close" data-close-modal aria-label="Tutup modal">×</button></div>
        <form method="POST" action="{{ route('barang.pembelian.store') }}" class="create-modal__form">
            @csrf <input type="hidden" name="form_type" value="pembelian">
            <div class="form-field form-field--full"><label class="form-label">Nama Barang <span>*</span></label><input type="text" name="nama_barang" class="form-input" required value="{{ old('form_type') === 'pembelian' ? old('nama_barang') : '' }}" placeholder="Contoh: Beras 10 Kg"></div>
            <div class="form-field"><label class="form-label">Batch</label><input type="text" name="batch" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('batch') : '' }}" placeholder="Batch 4"></div>
            <div class="form-field"><label class="form-label">Harga Satuan (Rp) <span>*</span></label><input type="number" min="0" step="0.01" name="harga_satuan" class="form-input" required value="{{ old('form_type') === 'pembelian' ? old('harga_satuan') : '' }}"></div>
            <div class="form-field"><label class="form-label">Qty Rencana <span>*</span></label><input type="number" min="0" name="qty_rencana" class="form-input" required value="{{ old('form_type') === 'pembelian' ? old('qty_rencana') : '' }}"></div>
            <div class="form-field"><label class="form-label">Qty Terbeli</label><input type="number" min="0" name="qty_terbeli" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('qty_terbeli', 0) : 0 }}"></div>
            <div class="form-field"><label class="form-label">Anggaran (Rp) <span>*</span></label><input type="number" min="0" step="0.01" name="anggaran" class="form-input" required value="{{ old('form_type') === 'pembelian' ? old('anggaran') : '' }}"></div>
            <div class="form-field"><label class="form-label">Realisasi (Rp)</label><input type="number" min="0" step="0.01" name="realisasi" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('realisasi', 0) : 0 }}"></div>
            <div class="create-modal__actions"><button type="button" class="btn btn-secondary" data-close-modal>Batal</button><button type="submit" class="btn btn-primary">Simpan Pembelian</button></div>
        </form>
    </div>
</div>
