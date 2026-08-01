<form method="POST" action="{{ route('barang.kegiatan.update', $anggaran) }}">
    @csrf @method('PUT')
    <div style="margin-bottom:10px;">
        <label class="form-label">Nama Kegiatan</label>
        <input type="text" name="nama_anggaran" class="form-input" required value="{{ $anggaran->nama_anggaran }}">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-input" required>
                @foreach(['barang_bantuan','transportasi','konsumsi','sewa','atk','cadangan'] as $kat)
                <option value="{{ $kat }}" {{ $anggaran->kategori == $kat ? 'selected' : '' }}>{{ ucfirst($kat) }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="form-label">Target</label><input type="number" name="target_paket" class="form-input" value="{{ $anggaran->target_paket }}"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
        <div><label class="form-label">Satuan</label><input type="text" name="satuan" class="form-input" value="{{ $anggaran->satuan }}"></div>
        <div><label class="form-label">Catatan/Status</label><input type="text" name="catatan" class="form-input" value="{{ $anggaran->catatan }}"></div>
    </div>

    @if($anggaran->barangItems->isNotEmpty())
    <div style="margin-top:16px;padding:14px;border:1px solid #e5e7eb;border-radius:10px;background:#f8f9fc;">
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
                @php
                    $sub = $item->harga_satuan * $item->pivot->jumlah;
                    $grandTotal += $sub;
                @endphp
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
    @endif

    <input type="hidden" name="anggaran" value="{{ $anggaran->anggaran }}">
    <input type="hidden" name="realisasi" value="{{ $anggaran->realisasi }}">

    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e5e7eb;">
        <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Update</button>
    </div>
</form>
