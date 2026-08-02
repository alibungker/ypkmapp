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
            <div class="form-field"><label class="form-label">Anggaran Otomatis (Rp)</label><input type="number" min="0" step="0.01" name="anggaran" id="kegAnggaran" class="form-input auto-total" readonly value="{{ old('form_type') === 'kegiatan' ? old('anggaran', 0) : 0 }}"><small class="form-hint">Σ (harga satuan × jumlah barang)</small></div>
            <div class="form-field"><label class="form-label">Realisasi Otomatis (Rp)</label><input type="number" min="0" step="0.01" name="realisasi" id="kegRealisasi" class="form-input auto-total" readonly value="{{ old('form_type') === 'kegiatan' ? old('realisasi', 0) : 0 }}"><small class="form-hint">Σ (harga satuan × jumlah barang)</small></div>
            <div class="form-field form-field--full"><label class="form-label">Catatan/Status</label><input type="text" name="catatan" class="form-input" value="{{ old('form_type') === 'kegiatan' ? old('catatan') : '' }}" placeholder="Contoh: Direncanakan"></div>
            <div class="form-field form-field--full">
                <label class="form-label">Barang yang Disalurkan <span>*</span></label>
                <div id="kegiatanBarangRows" class="allocation-list"></div>
                <button type="button" class="btn btn-outline btn-sm" id="addKegiatanBarang">+ Tambah Jenis Barang</button>
                <small class="form-hint">Pilih barang dari stok pembelian. Anggaran & realisasi terhitung otomatis dari harga satuan × jumlah.</small>
            </div>
            <div class="create-modal__actions"><button type="button" class="btn btn-secondary" data-close-modal>Batal</button><button type="submit" class="btn btn-primary">Simpan Kegiatan</button></div>
        </form>
    </div>
</div>

{{-- Modal Pembelian Barang --}}
<div class="create-modal" id="createPembelianModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="createPembelianTitle">
    <div class="create-modal__backdrop" data-close-modal></div>
    <div class="create-modal__panel" role="document">
        <div class="create-modal__header"><div><span class="create-modal__eyebrow">TRANSAKSI BARU</span><h2 id="createPembelianTitle">Pembelian Barang</h2></div><button type="button" class="create-modal__close" data-close-modal aria-label="Tutup modal">×</button></div>
        <form method="POST" action="{{ route('barang.pembelian.store') }}" class="create-modal__form" enctype="multipart/form-data">
            @csrf <input type="hidden" name="form_type" value="pembelian">
            <div class="form-field form-field--full"><label class="form-label">Nama Barang <span>*</span></label><input type="text" name="nama_barang" class="form-input" required value="{{ old('form_type') === 'pembelian' ? old('nama_barang') : '' }}" placeholder="Contoh: Beras 10 Kg"></div>
            <div class="form-field"><label class="form-label">Kategori <span>*</span></label>
                <select name="kategori_barang_id" class="form-input" id="kategoriBarangCreate" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoriBarangs as $kat)
                        <option value="{{ $kat->id }}" data-jenis="{{ $kat->jenis_default }}" @selected(old('form_type') === 'pembelian' && old('kategori_barang_id') == $kat->id)>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field"><label class="form-label">Peruntukan <span>*</span></label>
                <select name="jenis_peruntukan" class="form-input" id="jenisPeruntukanCreate" required>
                    <option value="bantuan" @selected(old('form_type') === 'pembelian' && old('jenis_peruntukan') === 'bantuan')>Bantuan (disalurkan)</option>
                    <option value="operasional" @selected(old('form_type') === 'pembelian' && old('jenis_peruntukan') === 'operasional')>Operasional (habis pakai)</option>
                    <option value="aset" @selected(old('form_type') === 'pembelian' && old('jenis_peruntukan') === 'aset')>Aset (inventaris)</option>
                </select>
            </div>
            <div class="form-field"><label class="form-label">Satuan</label><input type="text" name="satuan" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('satuan') : '' }}" placeholder="kg, liter, kotak, pcs"></div>
            <div class="form-field"><label class="form-label">Batch</label><input type="text" name="batch" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('batch') : '' }}" placeholder="Batch 4"></div>
            <div class="form-field"><label class="form-label">Tanggal Pembelian</label><input type="date" name="tanggal_pembelian" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('tanggal_pembelian') : '' }}"></div>
            <div class="form-field"><label class="form-label">Supplier / Vendor</label><input type="text" name="supplier" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('supplier') : '' }}" placeholder="Contoh: CV Berkah Jaya"></div>
            <div class="form-field"><label class="form-label">Nomor Invoice</label><input type="text" name="nomor_invoice" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('nomor_invoice') : '' }}" placeholder="INV-2026-001"></div>
            <div class="form-field"><label class="form-label">Metode Pembayaran</label>
                <select name="metode_pembayaran" class="form-input">
                    <option value="">-- Pilih --</option>
                    <option value="tunai" @selected(old('form_type') === 'pembelian' && old('metode_pembayaran') === 'tunai')>Tunai</option>
                    <option value="transfer" @selected(old('form_type') === 'pembelian' && old('metode_pembayaran') === 'transfer')>Transfer</option>
                    <option value="lainnya" @selected(old('form_type') === 'pembelian' && old('metode_pembayaran') === 'lainnya')>Lainnya</option>
                </select>
            </div>
            <div class="form-field"><label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="diterima" @selected(old('form_type') === 'pembelian' && old('status') === 'diterima')>Diterima</option>
                    <option value="rencana" @selected(old('form_type') === 'pembelian' && old('status') === 'rencana')>Rencana</option>
                    <option value="dipesan" @selected(old('form_type') === 'pembelian' && old('status') === 'dipesan')>Dipesan</option>
                    <option value="batal" @selected(old('form_type') === 'pembelian' && old('status') === 'batal')>Batal</option>
                </select>
            </div>
            <div class="form-field"><label class="form-label">Harga Satuan (Rp) <span>*</span></label><input type="number" min="0" step="0.01" name="harga_satuan" class="form-input" required value="{{ old('form_type') === 'pembelian' ? old('harga_satuan') : '' }}"></div>
            <div class="form-field"><label class="form-label">Qty Rencana <span>*</span></label><input type="number" min="0" name="qty_rencana" class="form-input" required value="{{ old('form_type') === 'pembelian' ? old('qty_rencana') : '' }}"></div>
            <div class="form-field"><label class="form-label">Qty Terbeli</label><input type="number" min="0" name="qty_terbeli" class="form-input" value="{{ old('form_type') === 'pembelian' ? old('qty_terbeli', 0) : 0 }}"></div>
            <div class="form-field"><label class="form-label">Anggaran Otomatis (Rp)</label><input type="number" min="0" step="0.01" name="anggaran" class="form-input auto-total" readonly value="{{ old('form_type') === 'pembelian' ? old('anggaran', 0) : 0 }}"><small class="form-hint">Harga satuan × jumlah rencana</small></div>
            <div class="form-field"><label class="form-label">Realisasi Otomatis (Rp)</label><input type="number" min="0" step="0.01" name="realisasi" class="form-input auto-total" readonly value="{{ old('form_type') === 'pembelian' ? old('realisasi', 0) : 0 }}"><small class="form-hint">Harga satuan × jumlah terbeli</small></div>
            <div class="form-field form-field--full"><label class="form-label">Bukti Pembelian</label><input type="file" name="bukti_pembelian" class="form-input" accept=".jpg,.jpeg,.png,.pdf"><small class="form-hint">Foto nota / invoice (maks 5 MB)</small></div>
            <div class="form-field form-field--full"><label class="form-label">Catatan</label><textarea name="catatan" class="form-input" rows="2" placeholder="Keterangan tambahan">{{ old('form_type') === 'pembelian' ? old('catatan') : '' }}</textarea></div>
            <div class="create-modal__actions"><button type="button" class="btn btn-secondary" data-close-modal>Batal</button><button type="submit" class="btn btn-primary">Simpan Pembelian</button></div>
        </form>
    </div>
</div>
