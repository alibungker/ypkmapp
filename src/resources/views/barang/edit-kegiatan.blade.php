<form method="POST" action="{{ route('barang.kegiatan.update', $a) }}">
    @csrf @method('PUT')
    <div style="margin-bottom:10px;">
        <label class="form-label">Nama Kegiatan</label>
        <input type="text" name="nama_anggaran" class="form-input" required value="{{ $a->nama_anggaran }}">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-input" required>
                @foreach(['barang_bantuan','transportasi','konsumsi','sewa','atk','cadangan'] as $kat)
                <option value="{{ $kat }}" {{ $a->kategori == $kat ? 'selected' : '' }}>{{ ucfirst($kat) }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="form-label">Target</label><input type="number" name="target_paket" class="form-input" value="{{ $a->target_paket }}"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Satuan</label><input type="text" name="satuan" class="form-input" value="{{ $a->satuan }}"></div>
        <div><label class="form-label">Anggaran</label><input type="number" name="anggaran" class="form-input" required step="0.01" value="{{ $a->anggaran }}"></div>
        <div><label class="form-label">Realisasi</label><input type="number" name="realisasi" class="form-input" required step="0.01" value="{{ $a->realisasi }}"></div>
    </div>
    <div style="margin-top:10px;"><label class="form-label">Catatan</label><input type="text" name="catatan" class="form-input" value="{{ $a->catatan }}"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e5e7eb;">
        <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Update</button>
    </div>
</form>
