<form method="POST" action="{{ route('barang.kegiatan.update', $anggaran) }}">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <div style="margin-bottom:10px;">
        <label class="form-label">Nama Kegiatan</label>
        <input type="text" name="nama_anggaran" class="form-input" required value="{{ $anggaran->nama_anggaran }}">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-input" required id="editKegiatanKategori">
                @foreach(['barang_bantuan'=>'Barang Bantuan','transportasi'=>'Transportasi','konsumsi'=>'Konsumsi','sewa'=>'Sewa','atk'=>'ATK','cadangan'=>'Cadangan'] as $val=>$lbl)
                <option value="{{ $val }}" {{ $anggaran->kategori == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div id="editTargetField"><label class="form-label">Target</label><input type="number" name="target_paket" class="form-input" value="{{ $anggaran->target_paket }}"></div>
    </div>
    <div id="editSatuanField" style="margin-top:10px;"><label class="form-label">Satuan</label><input type="text" name="satuan" class="form-input" value="{{ $anggaran->satuan }}"></div>

    @if($anggaran->barangItems->isNotEmpty())
    <div id="editBarangSection" style="margin-top:16px;padding:14px;border:1px solid #e5e7eb;border-radius:10px;background:#f8f9fc;">
        <div style="font-size:13px;font-weight:700;color:#00034a;margin-bottom:10px;">📦 Barang untuk Kegiatan ({{ $anggaran->barangItems->count() }} item)</div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="text-align:left;color:#6b7280;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:6px 8px;">Barang</th>
                    <th style="padding:6px 8px;text-align:right;">Harga</th>
                    <th style="padding:6px 8px;text-align:right;">Jumlah</th>
                    <th style="padding:6px 8px;text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($anggaran->barangItems as $item)
                @php $sub = $item->harga_satuan * $item->pivot->jumlah; $grandTotal += $sub; @endphp
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:7px 8px;font-weight:500;">{{ $item->nama_barang }} <span style="color:#9ca3af;font-size:11px;">({{ $item->batch ?? '-' }})</span></td>
                    <td style="padding:7px 8px;text-align:right;">Rp {{ number_format($item->harga_satuan,0,',','.') }}</td>
                    <td style="padding:7px 8px;text-align:right;">{{ number_format($item->pivot->jumlah) }}</td>
                    <td style="padding:7px 8px;text-align:right;font-weight:600;color:#00034a;">Rp {{ number_format($sub,0,',','.') }}</td>
                </tr>
                @endforeach
                <tr style="border-top:2px solid #00034a;">
                    <td colspan="3" style="padding:8px;font-weight:700;text-align:right;">Total Anggaran &amp; Realisasi</td>
                    <td style="padding:8px;text-align:right;font-weight:800;font-size:15px;color:#00034a;">Rp {{ number_format($grandTotal,0,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top:8px;font-size:11px;color:#6b7280;">Nilai dihitung otomatis dari harga pembelian × jumlah. Ubah di tab Pembelian Barang atau tambah kegiatan baru untuk menyesuaikan.</div>
    </div>
    @else
    <div id="editBarangSection" style="display:none;"></div>
    @endif

    <div id="editRincianField" style="display:none;margin-top:12px;">
        <label class="form-label">Rincian Biaya</label>
        <textarea name="rincian_biaya" rows="4" class="form-input" placeholder="Contoh: 1. Sewa mobil Rp 500.000&#10;2. Driver Rp 200.000&#10;Total: Rp 700.000">{{ $anggaran->catatan }}</textarea>
        <small class="form-hint" style="color:#667085;">Input rincian manual untuk transportasi, konsumsi, sewa, ATK, atau cadangan.</small>
    </div>
    <div id="editEstimasiField" style="display:none;margin-top:12px;">
        <label class="form-label">Estimasi Biaya (Rp) <span style="color:#dc2626">*</span></label>
        <input type="number" min="0" step="0.01" name="estimasi_biaya" class="form-input" value="{{ $anggaran->anggaran }}">
        <small class="form-hint" style="color:#667085;">Total estimasi biaya kegiatan ini.</small>
    </div>

    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e5e7eb;">
        <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Update</button>
    </div>
</form>

<script>
(function(){
    const kategori = document.getElementById('editKegiatanKategori');
    if(!kategori) return;
    const isBarang = () => kategori.value === 'barang_bantuan';
    function applyK(){
        const show = isBarang();
        document.getElementById('editTargetField').style.display = show ? '' : 'none';
        document.getElementById('editSatuanField').style.display = show ? '' : 'none';
        const barang = document.getElementById('editBarangSection');
        if(barang && barang.querySelector('table')) barang.style.display = show ? '' : 'none';
        document.getElementById('editRincianField').style.display = show ? 'none' : '';
        document.getElementById('editEstimasiField').style.display = show ? 'none' : '';
    }
    kategori.addEventListener('change', applyK);
    applyK();
})();
</script>
