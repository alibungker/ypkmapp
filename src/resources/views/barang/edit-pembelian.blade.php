<form method="POST" action="{{ route('barang.pembelian.update', $p) }}" data-purchase-calculator enctype="multipart/form-data">
    @csrf @method('PUT')
    <div style="margin-bottom:10px;">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang" class="form-input" required value="{{ $p->nama_barang }}">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div><label class="form-label">Kategori</label>
            <select name="kategori_barang_id" class="form-input" id="kategoriBarangEdit">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriBarangs as $kat)
                    <option value="{{ $kat->id }}" data-jenis="{{ $kat->jenis_default }}" @selected($p->kategori_barang_id == $kat->id)>{{ $kat->nama }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="form-label">Peruntukan</label>
            <select name="jenis_peruntukan" class="form-input" id="jenisPeruntukanEdit">
                <option value="bantuan" @selected($p->jenis_peruntukan === 'bantuan')>Bantuan (disalurkan)</option>
                <option value="operasional" @selected($p->jenis_peruntukan === 'operasional')>Operasional (habis pakai)</option>
                <option value="aset" @selected($p->jenis_peruntukan === 'aset')>Aset (inventaris)</option>
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Satuan</label><input type="text" name="satuan" class="form-input" value="{{ $p->satuan }}" placeholder="kg, liter, kotak, pcs"></div>
        <div><label class="form-label">Batch</label><input type="text" name="batch" class="form-input" value="{{ $p->batch }}"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Tanggal Pembelian</label><input type="date" name="tanggal_pembelian" class="form-input" value="{{ is_object($p->tanggal_pembelian) ? $p->tanggal_pembelian->format('Y-m-d') : ($p->tanggal_pembelian ?? '') }}"></div>
        <div><label class="form-label">Supplier / Vendor</label><input type="text" name="supplier" class="form-input" value="{{ $p->supplier }}"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Nomor Invoice</label><input type="text" name="nomor_invoice" class="form-input" value="{{ $p->nomor_invoice }}"></div>
        <div><label class="form-label">Metode Pembayaran</label>
            <select name="metode_pembayaran" class="form-input">
                <option value="">-- Pilih --</option>
                <option value="tunai" @selected($p->metode_pembayaran === 'tunai')>Tunai</option>
                <option value="transfer" @selected($p->metode_pembayaran === 'transfer')>Transfer</option>
                <option value="lainnya" @selected($p->metode_pembayaran === 'lainnya')>Lainnya</option>
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Harga Satuan</label><input type="number" name="harga_satuan" class="form-input" required step="0.01" value="{{ $p->harga_satuan }}"></div>
        <div><label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="diterima" @selected($p->status === 'diterima')>Diterima</option>
                <option value="rencana" @selected($p->status === 'rencana')>Rencana</option>
                <option value="dipesan" @selected($p->status === 'dipesan')>Dipesan</option>
                <option value="batal" @selected($p->status === 'batal')>Batal</option>
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Qty Rencana</label><input type="number" name="qty_rencana" class="form-input" required value="{{ $p->qty_rencana }}"></div>
        <div><label class="form-label">Qty Terbeli</label><input type="number" name="qty_terbeli" class="form-input" required value="{{ $p->qty_terbeli }}"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Anggaran Otomatis</label><input type="number" name="anggaran" class="form-input auto-total" readonly step="0.01" value="{{ $p->anggaran }}"></div>
        <div><label class="form-label">Realisasi Otomatis</label><input type="number" name="realisasi" class="form-input auto-total" readonly step="0.01" value="{{ $p->realisasi }}"></div>
    </div>
    <div style="margin-top:10px;">
        <label class="form-label">Bukti Pembelian</label>
        @if($p->bukti_pembelian)
            <div style="margin-bottom:6px;"><a href="{{ asset('storage/'.$p->bukti_pembelian) }}" target="_blank" style="color:#00034a;font-size:13px;">📎 Lihat bukti saat ini</a></div>
        @endif
        <input type="file" name="bukti_pembelian" class="form-input" accept=".jpg,.jpeg,.png,.pdf">
    </div>
    <div style="margin-top:10px;">
        <label class="form-label">Catatan</label>
        <textarea name="catatan" class="form-input" rows="2">{{ $p->catatan }}</textarea>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e5e7eb;">
        <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Update</button>
    </div>
</form>
