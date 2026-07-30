<form method="POST" action="{{ route('barang.pembelian.update', $p) }}">
    @csrf @method('PUT')
    <div style="margin-bottom:10px;">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang" class="form-input" required value="{{ $p->nama_barang }}">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div><label class="form-label">Batch</label><input type="text" name="batch" class="form-input" value="{{ $p->batch }}"></div>
        <div><label class="form-label">Harga Satuan</label><input type="number" name="harga_satuan" class="form-input" required step="0.01" value="{{ $p->harga_satuan }}"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Qty Rencana</label><input type="number" name="qty_rencana" class="form-input" required value="{{ $p->qty_rencana }}"></div>
        <div><label class="form-label">Qty Terbeli</label><input type="number" name="qty_terbeli" class="form-input" required value="{{ $p->qty_terbeli }}"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Anggaran</label><input type="number" name="anggaran" class="form-input" required step="0.01" value="{{ $p->anggaran }}"></div>
        <div><label class="form-label">Realisasi</label><input type="number" name="realisasi" class="form-input" required step="0.01" value="{{ $p->realisasi }}"></div>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e5e7eb;">
        <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Update</button>
    </div>
</form>
