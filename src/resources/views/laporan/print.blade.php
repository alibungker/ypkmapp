<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Detail — {{ $distribusi->kode_distribusi }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;color:#111827;background:#f3f4f6;font-size:12px;line-height:1.5}
/* ===== Toolbar (screen only) ===== */
.toolbar{position:sticky;top:0;z-index:50;display:flex;align-items:center;gap:10px;padding:10px 20px;background:#00034a;color:#fff}
.toolbar .tb-title{font-weight:600;font-size:14px;flex:1}
.toolbar a,.toolbar button{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;border:1px solid rgba(255,255,255,.35);background:transparent;color:#fff;font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;transition:background .15s}
.toolbar button.primary{background:#fff;color:#00034a;border-color:#fff}
.toolbar a:hover,.toolbar button:hover{background:rgba(255,255,255,.15)}
.toolbar button.primary:hover{background:#f0f0f5}

/* ===== Sheet (A4) ===== */
.sheet{max-width:794px;margin:24px auto;background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.1);padding:40px 44px;border:1px solid #e5e7eb}

/* ===== Kop surat ===== */
.kop{display:flex;align-items:center;gap:16px;border-bottom:3px solid #00034a;padding-bottom:14px;margin-bottom:20px}
.kop img{width:64px;height:64px;object-fit:contain}
.kop .kop-text{flex:1}
.kop .kop-text .nama-yayasan{font-size:15px;font-weight:800;color:#00034a;text-transform:uppercase;letter-spacing:.02em}
.kop .kop-text .nama-peduli{font-size:12px;font-weight:700;color:#b08a2e}
.kop .kop-text .alamat{font-size:10px;color:#6b7280;margin-top:2px}
.kop .kop-nomor{text-align:right;font-size:10px;color:#6b7280}

/* ===== Judul ===== */
.judul{text-align:center;margin-bottom:18px}
.judul h1{font-size:16px;font-weight:800;color:#00034a;text-transform:uppercase;letter-spacing:.05em}
.judul p{font-size:11px;color:#6b7280;margin-top:2px}

/* ===== Info block ===== */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 24px;margin-bottom:16px}
.info-grid .item{display:flex;gap:8px;font-size:11.5px}
.info-grid .item .k{min-width:110px;color:#6b7280;font-weight:500}
.info-grid .item .v{font-weight:600;color:#111827}
.status-badge{display:inline-block;padding:2px 10px;border-radius:9999px;font-size:10px;font-weight:700;text-transform:capitalize}
.status-direncanakan{background:#fef3c7;color:#92400e}
.status-berlangsung{background:#dbeafe;color:#1e40af}
.status-selesai{background:#d1fae5;color:#065f46}
.status-dibatalkan{background:#fee2e2;color:#991b1b}

/* ===== KPI ===== */
.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px}
.kpi{border:1px solid #e5e7eb;border-radius:10px;padding:12px 14px;background:#fafafa}
.kpi .val{font-size:16px;font-weight:800;color:#00034a}
.kpi .val.green{color:#017723}
.kpi .val.gold{color:#b08a2e}
.kpi .val.red{color:#dc2626}
.kpi .lbl{font-size:10px;color:#6b7280;font-weight:500;margin-top:2px}

/* ===== Section title ===== */
.sec-title{font-size:12px;font-weight:800;color:#00034a;text-transform:uppercase;letter-spacing:.04em;margin:18px 0 8px;padding-bottom:6px;border-bottom:1.5px solid #b08a2e;display:flex;align-items:center;gap:8px}

/* ===== Tables ===== */
table.tbl{width:100%;border-collapse:collapse;font-size:11px;margin-bottom:6px}
table.tbl th{background:#f3f4f6;color:#00034a;font-weight:700;text-align:left;padding:7px 9px;border:1px solid #d1d5db;text-transform:uppercase;font-size:9.5px;letter-spacing:.03em}
table.tbl td{padding:6px 9px;border:1px solid #e5e7eb;vertical-align:top}
table.tbl tr:nth-child(even) td{background:#fafafa}
table.tbl .num{text-align:right;font-variant-numeric:tabular-nums}
table.tbl .ctr{text-align:center}
table.tbl tfoot td{font-weight:700;background:#f3f4f6;border-top:2px solid #d1d5db}

/* ===== Signature ===== */
.ttd{display:flex;justify-content:space-between;gap:40px;margin-top:36px}
.ttd .blok{flex:1;text-align:center;font-size:11px}
.ttd .blok .jabatan{margin-bottom:64px}
.ttd .blok .nama{font-weight:700;text-decoration:underline;margin-top:64px}
.ttd .blok .nip{font-size:10px;color:#6b7280;margin-top:2px}

/* ===== Peta ===== */
.map-wrap{border:1px solid #d1d5db;border-radius:8px;overflow:hidden;margin-top:8px}
.map-wrap iframe{display:block;width:100%;height:320px;border:0}
.map-coord{font-size:10px;color:#6b7280;margin-top:6px}

/* ===== Galeri media ===== */
.media-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:8px}
.media-grid .media-item{border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;background:#fafafa}
.media-grid img{display:block;width:100%;height:150px;object-fit:cover}
.media-grid .cap{font-size:9px;color:#6b7280;text-align:center;padding:4px 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
@media print{
  .media-grid{grid-template-columns:repeat(4,1fr);gap:8px}
  .media-grid .media-item{break-inside:avoid}
  .media-grid img{height:130px}
  .map-wrap{break-inside:avoid}
}

/* ===== Footer page ===== */
.page-footer{margin-top:24px;padding-top:10px;border-top:1px solid #e5e7eb;display:flex;justify-content:space-between;font-size:9.5px;color:#9ca3af}

/* ===== Print rules ===== */
@page{size:A4;margin:14mm 12mm}
@media print{
  body{background:#fff}
  .toolbar{display:none!important}
  .sheet{max-width:none;margin:0;padding:0;border:none;border-radius:0;box-shadow:none}
  .kpi{break-inside:avoid}
  table.tbl{page-break-inside:auto}
  table.tbl tr{page-break-inside:avoid}
  .sec-title{page-break-after:avoid}
  .ttd{page-break-inside:avoid}
}
</style>
</head>
<body>

<div class="toolbar no-print">
    <span class="tb-title">🖨 Laporan Detail — {{ $distribusi->kode_distribusi }}</span>
    <button class="primary" onclick="window.print()">⬇ Cetak / Simpan PDF</button>
    <a href="{{ url()->previous() }}">← Kembali</a>
</div>

<div class="sheet">

    {{-- ===== Kop surat ===== --}}
    <div class="kop">
        <img src="{{ asset('img/logo-ypkm-transparent.png') }}" alt="Logo YPKM">
        <div class="kop-text">
            <div class="nama-yayasan">Yayasan Pelangi Kesejahteraan Masyarakat</div>
            <div class="nama-peduli">PEDULI YPKM — Pendataan &amp; Distribusi Untuk Layanan Insani</div>
            <div class="alamat">Provinsi Aceh, Indonesia</div>
        </div>
        <div class="kop-nomor">
            <div><b>Kode:</b> {{ $distribusi->kode_distribusi }}</div>
            <div><b>Cetak:</b> {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ===== Judul ===== --}}
    <div class="judul">
        <h1>Laporan Detail Kegiatan Distribusi</h1>
        <p>{{ $distribusi->nama_kegiatan }}</p>
    </div>

    {{-- ===== Info distribusi ===== --}}
    <div class="info-grid">
        <div class="item"><span class="k">Tanggal Kegiatan</span><span class="v">{{ optional($distribusi->tanggal)->format('d/m/Y') ?: $distribusi->tanggal }}</span></div>
        <div class="item"><span class="k">Status</span><span class="v"><span class="status-badge status-{{ $distribusi->status }}">{{ $distribusi->status }}</span></span></div>
        <div class="item"><span class="k">Jenis Bantuan</span><span class="v">{{ $distribusi->jenis_bantuan ?: '-' }}</span></div>
        <div class="item"><span class="k">Sumber Dana</span><span class="v">{{ $distribusi->sumber_dana ?: '-' }}</span></div>
        <div class="item"><span class="k">Lokasi</span><span class="v">{{ $distribusi->lokasi ?: optional($distribusi->kelompok)->desa ?: '-' }}</span></div>
        <div class="item"><span class="k">Kelompok</span><span class="v">{{ optional($distribusi->kelompok)->nama ?: '-' }}</span></div>
        <div class="item"><span class="k">Wilayah</span><span class="v">{{ collect([optional($distribusi->kelompok)->daerah, optional($distribusi->kelompok)->kecamatan, optional($distribusi->kelompok)->desa])->filter()->implode(' — ') ?: '-' }}</span></div>
        <div class="item"><span class="k">Ketua Kelompok</span><span class="v">{{ optional($distribusi->kelompok->ketuaUser)->name ?: optional($distribusi->kelompok->ketua)->nama ?: '-' }}</span></div>
    </div>

    {{-- ===== Ringkasan (KPI) ===== --}}
    <div class="kpi-grid">
        <div class="kpi"><div class="val">{{ number_format($distribusi->jumlah_paket, 0, ',', '.') }}</div><div class="lbl">Jumlah Paket</div></div>
        <div class="kpi"><div class="val green">Rp {{ number_format($distribusi->estimasi_nilai_total, 0, ',', '.') }}</div><div class="lbl">Estimasi Nilai Bantuan</div></div>
        <div class="kpi"><div class="val">{{ number_format(optional($distribusi->kelompok)->penerima_count ?? 0, 0, ',', '.') }}</div><div class="lbl">Target Penerima</div></div>
        <div class="kpi"><div class="val">{{ number_format($totalTerverifikasi, 0, ',', '.') }}</div><div class="lbl">Terverifikasi</div></div>
        <div class="kpi"><div class="val green">{{ number_format($totalTerima ?: $distribusi->tanda_terima_count, 0, ',', '.') }}</div><div class="lbl">Tanda Terima</div></div>
        <div class="kpi"><div class="val gold">Rp {{ number_format($distribusi->biayaOperasional->sum('jumlah'), 0, ',', '.') }}</div><div class="lbl">Biaya Operasional</div></div>
    </div>

    {{-- ===== Rincian item bantuan ===== --}}
    @if($distribusi->items->isNotEmpty())
    <div class="sec-title">📦 Rincian Paket Bantuan</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:32px">No</th>
                <th>Barang</th>
                <th class="ctr">Satuan</th>
                <th class="num">Jumlah/Paket</th>
                <th class="num">Paket</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribusi->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($item->barang)->nama ?: '-' }}</td>
                <td class="ctr">{{ optional($item->barang)->satuan ?: '-' }}</td>
                <td class="num">{{ number_format($item->jumlah_per_paket, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($item->jumlah_paket_distribusi, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->subtotal_nilai, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total Nilai Bantuan</td>
                <td class="num">Rp {{ number_format($distribusi->items->sum('subtotal_nilai'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ===== Rincian bantuan dari pembelian (pivot distribusi_pembelian_barang) ===== --}}
    @if($distribusi->pembelianBarang->isNotEmpty())
    <div class="sec-title">📦 Rincian Bantuan (Barang Disalurkan)</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:32px">No</th>
                <th>Barang</th>
                <th class="ctr">Satuan</th>
                <th class="num">Jumlah</th>
                <th class="num">Harga Satuan</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotalBantuan = 0; @endphp
            @foreach($distribusi->pembelianBarang as $i => $pb)
            @php
                $jml = (float) $pb->pivot->jumlah;
                $harga = (float) $pb->harga_satuan;
                $sub = $jml * $harga;
                $subtotalBantuan += $sub;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pb->nama_barang ?: '-' }}</td>
                <td class="ctr">{{ $pb->satuan ?: '-' }}</td>
                <td class="num">{{ number_format($jml, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($sub, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total Nilai Bantuan</td>
                <td class="num">Rp {{ number_format($subtotalBantuan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ===== Peta titik lokasi ===== --}}
    @if($koordinat)
    <div class="sec-title">📍 Peta Titik Lokasi Bantuan</div>
    <div class="map-wrap">
        <iframe
            src="https://maps.google.com/maps?q={{ $koordinat['lat'] }},{{ $koordinat['lng'] }}&z=15&output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Peta titik lokasi {{ $distribusi->kode_distribusi }}"></iframe>
    </div>
    <div class="map-coord">Koordinat: {{ $koordinat['lat'] }}, {{ $koordinat['lng'] }}</div>
    @endif

    {{-- ===== Media (maks 4 foto) ===== --}}
    @if($media->isNotEmpty())
    <div class="sec-title">📷 Dokumentasi Kegiatan</div>
    <div class="media-grid">
        @foreach($media as $photo)
        <div class="media-item">
            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->original_name ?: 'Dokumentasi' }}">
            <div class="cap">{{ $photo->original_name ?: 'Foto' }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ===== Biaya operasional ===== --}}
    @if($distribusi->biayaOperasional->isNotEmpty())
    <div class="sec-title">💰 Biaya Operasional</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:32px">No</th>
                <th>Kategori / Deskripsi</th>
                <th>Pihak Penerima</th>
                <th class="num">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribusi->biayaOperasional as $i => $b)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $b->kategori ?: '-' }}<br><small style="color:#6b7280">{{ $b->deskripsi }}</small></td>
                <td>{{ $b->pihak_penerima ?: '-' }}</td>
                <td class="num">Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total Biaya Operasional</td>
                <td class="num">Rp {{ number_format($distribusi->biayaOperasional->sum('jumlah'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ===== Daftar penerima ===== --}}
    <div class="sec-title">👥 Daftar Penerima Bantuan</div>
    @if($penerimaList->isNotEmpty())
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:32px">No</th>
                <th>Nama Penerima</th>
                <th>NIK</th>
                <th>Desa</th>
                <th class="ctr">Status Verifikasi</th>
                <th class="ctr">Tanda Terima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penerimaList as $i => $pd)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="font-weight:600">{{ optional($pd->penerima)->nama ?: '-' }}</td>
                <td>{{ optional($pd->penerima)->nik ?: '-' }}</td>
                <td>{{ optional($pd->penerima)->desa ?: '-' }}</td>
                <td class="ctr">{{ ucfirst(optional($pd->penerima)->status ?: '-') }}</td>
                <td class="ctr">{{ $pd->status === 'diterima' ? '✔' : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:28px 16px;color:#6b7280;font-size:13px;background:#f9fafb;border-radius:8px;border:1px dashed #d1d5db;margin-top:6px">
        <div style="font-size:24px;margin-bottom:8px">📋</div>
        <div style="font-weight:600;color:#374151;margin-bottom:4px">Belum ada data penerima</div>
        <div style="font-size:12px">Data nama penerima bantuan untuk distribusi ini belum tersedia atau belum diverifikasi.</div>
    </div>
    @endif

    {{-- ===== Catatan ===== --}}
    @if($distribusi->catatan)
    <div class="sec-title">📝 Catatan</div>
    <p style="font-size:11px;color:#374151">{{ $distribusi->catatan }}</p>
    @endif

    {{-- ===== Tanda tangan ===== --}}
    <div class="ttd">
        <div class="blok">
            <div class="jabatan">Dibuat oleh,</div>
            <div class="nama">{{ optional($distribusi->creator)->name ?: 'Administrator' }}</div>
            <div class="nip">{{ now()->format('d/m/Y') }}</div>
        </div>
        <div class="blok">
            <div class="jabatan">Mengetahui,</div>
            <div class="nama">Ketua YPKM</div>
            <div class="nip">&nbsp;</div>
        </div>
    </div>

    <div class="page-footer">
        <span>PEDULI YPKM — {{ $distribusi->kode_distribusi }}</span>
        <span>Dicetak {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</div>

<script>
// Auto trigger print dialog (skip on iOS Safari popup blockers)
window.addEventListener('load', function () {
    setTimeout(function () {
        var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        if (!isIOS) window.print();
    }, 600);
});
</script>
</body>
</html>
