@extends('layouts.app')
@section('title', 'Laporan')
@section('subtitle', 'Rekap dinamis distribusi, penerima, dan keuangan dari database')

@section('styles')
<style>
/* ===== shadcn/ui-inspired filter components ===== */

/* --- Card --- */
.filter-card {
  background: #fff;
  border: 1px solid hsl(214.3 31.8% 91.4%);
  border-radius: 12px;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,.05);
  overflow: hidden;
}
.filter-card-header {
  padding: 16px 20px;
  border-bottom: 1px solid hsl(214.3 31.8% 91.4%);
}
.filter-card-header h3 {
  font-size: 15px;
  font-weight: 600;
  color: hsl(222.2 47.4% 11.2%);
  margin: 0;
}
.filter-card-header p {
  font-size: 13px;
  color: hsl(215.4 16.3% 46.9%);
  margin: 2px 0 0;
}
.filter-card-body {
  padding: 20px;
}

/* --- Form Grid --- */
.report-filter {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 14px;
  align-items: end;
}

/* --- Label --- */
.filter-label {
  display: block;
  font-size: 13px;
  font-weight: 500;
  color: hsl(222.2 47.4% 11.2%);
  margin-bottom: 6px;
  letter-spacing: .01em;
}

/* --- Input / Select (shadcn/ui Input style) --- */
.filter-input {
  display: flex;
  width: 100%;
  height: 36px;
  border-radius: 8px;
  border: 1px solid hsl(214.3 31.8% 91.4%);
  background: #fff;
  padding: 0 12px;
  font-size: 13px;
  color: hsl(222.2 47.4% 11.2%);
  transition: border-color .15s, box-shadow .15s;
  outline: none;
  font-family: inherit;
  box-sizing: border-box;
}
.filter-input:hover {
  border-color: hsl(215.4 16.3% 46.9%);
}
.filter-input:focus {
  border-color: hsl(222.2 47.4% 11.2%);
  box-shadow: 0 0 0 2px hsl(222.2 47.4% 11.2% / .15);
}
.filter-input::placeholder {
  color: hsl(215.4 16.3% 76.9%);
}

/* Select custom arrow */
.filter-select {
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  padding-right: 32px;
}

/* Date input icon fix */
.filter-input[type="date"] {
  padding-right: 12px;
}

/* --- Separator --- */
.filter-separator {
  height: 1px;
  background: hsl(214.3 31.8% 91.4%);
  margin: 16px 0;
}

/* --- Action buttons row --- */
.report-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 16px;
  flex-wrap: wrap;
  align-items: center;
}

/* --- Button: shadcn/ui variants --- */
/* Primary (filled navy) */
.btn-shadcn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  height: 36px;
  padding: 0 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  font-family: inherit;
  cursor: pointer;
  transition: background .15s, border-color .15s, color .15s, box-shadow .15s;
  text-decoration: none;
  white-space: nowrap;
  border: 1px solid transparent;
}
.btn-shadcn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px hsl(222.2 47.4% 11.2% / .3);
}
.btn-shadcn:disabled {
  opacity: .5;
  pointer-events: none;
}
.btn-shadcn-primary {
  background: hsl(222.2 47.4% 11.2%);
  color: #fff;
  border-color: hsl(222.2 47.4% 11.2%);
}
.btn-shadcn-primary:hover {
  background: hsl(222.2 47.4% 15%);
}
/* Outline (border, transparent bg) */
.btn-shadcn-outline {
  background: transparent;
  color: hsl(222.2 47.4% 11.2%);
  border-color: hsl(214.3 31.8% 91.4%);
}
.btn-shadcn-outline:hover {
  background: hsl(210 40% 96.1%);
}
/* Ghost (no border) */
.btn-shadcn-ghost {
  background: transparent;
  color: hsl(222.2 47.4% 11.2%);
  border-color: transparent;
}
.btn-shadcn-ghost:hover {
  background: hsl(210 40% 96.1%);
}

/* --- Active filter badges --- */
.filter-badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-top: 12px;
}
.filter-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  height: 24px;
  padding: 0 10px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 500;
  background: hsl(210 40% 96.1%);
  color: hsl(222.2 47.4% 11.2%);
  border: 1px solid hsl(214.3 31.8% 91.4%);
  white-space: nowrap;
}
.filter-badge-close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: hsl(214.3 31.8% 85%);
  color: hsl(222.2 47.4% 39.2%);
  font-size: 10px;
  line-height: 1;
  cursor: pointer;
  transition: background .15s;
  text-decoration: none;
  border: none;
  padding: 0;
}
.filter-badge-close:hover {
  background: hsl(0 84.2% 60.2%);
  color: #fff;
}

/* --- KPI Stat cards --- */
.report-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
  margin-bottom: 18px;
}
.report-kpi {
  background: #fff;
  border: 1px solid hsl(214.3 31.8% 91.4%);
  border-radius: 10px;
  padding: 16px;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,.03);
}
.report-kpi-value {
  font-size: 22px;
  font-weight: 700;
  color: hsl(222.2 47.4% 11.2%);
  line-height: 1.2;
  margin-bottom: 2px;
}
.report-kpi-label {
  font-size: 12px;
  color: hsl(215.4 16.3% 46.9%);
  font-weight: 500;
}

/* --- Tables --- */
.laporan-table-wrap {
  max-width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 10px;
}
.laporan-table-wrap table.table-data { min-width: 900px; }
.laporan-table-wrap th {
  white-space: nowrap;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: .03em;
}
.laporan-table-wrap td { font-size: 13px; }

/* Pagination: hide text, show icon */
.laporan-table-wrap .dt-paging .dt-paging-button { font-size: 0 !important; }
.laporan-table-wrap .dt-paging .dt-paging-button::before {
  font-size: .8rem !important;
  font-family: 'Inter', sans-serif !important;
  font-weight: 600;
}

.dt-nowrap { white-space: nowrap; }
.text-end { text-align: right; }
.text-center { text-align: center; }

@media(max-width:767px){
  .report-filter { grid-template-columns: 1fr 1fr; gap: 12px; }
  .report-actions { justify-content: stretch; }
  .report-actions .btn-shadcn { flex: 1; }
  .report-kpi-grid { grid-template-columns: 1fr 1fr; }
  .laporan-table-wrap table.table-data { min-width: 760px; }
  .laporan-table-wrap td, .laporan-table-wrap th { font-size: 12px; }
}
@media print {
  .sidebar, .topbar, .no-print { display: none !important; }
  .main-content { margin: 0 !important; padding: 0 !important; }
  .card { box-shadow: none !important; break-inside: avoid; }
  body { background: #fff !important; }
}
</style>
@endsection

@section('content')
{{-- ===== FILTER CARD (shadcn/ui style) ===== --}}
<div class="filter-card no-print" style="margin-bottom:18px;">
    <div class="filter-card-header">
        <h3>🔎 Filter Laporan</h3>
        <p>Gunakan filter di bawah untuk mempersempit data yang ditampilkan.</p>
    </div>

    {{-- Active filter badges --}}
    @php
    $activeFilters = [];
    if(request('tanggal_mulai')) $activeFilters[] = ['label' => 'Mulai: '.request('tanggal_mulai'), 'param' => 'tanggal_mulai'];
    if(request('tanggal_selesai')) $activeFilters[] = ['label' => 'Selesai: '.request('tanggal_selesai'), 'param' => 'tanggal_selesai'];
    if(request('status')) $activeFilters[] = ['label' => 'Status: '.ucfirst(request('status')), 'param' => 'status'];
    if(request('kabupaten')) $activeFilters[] = ['label' => request('kabupaten'), 'param' => 'kabupaten'];
    if(request('kecamatan')) $activeFilters[] = ['label' => request('kecamatan'), 'param' => 'kecamatan'];
    if(request('desa')) $activeFilters[] = ['label' => request('desa'), 'param' => 'desa'];
    if(request('kelompok_id')) $activeFilters[] = ['label' => 'Kelompok: '.optional($kelompoks->firstWhere('id', request('kelompok_id')))->nama, 'param' => 'kelompok_id'];
    @endphp

    @if(count($activeFilters) > 0)
    <div style="padding:0 20px;">
        <div class="filter-separator" style="margin-top:0;"></div>
        <div class="filter-badges">
            @foreach($activeFilters as $af)
            <span class="filter-badge">
                {{ $af['label'] }}
                <a href="{{ route('laporan.index', request()->except($af['param'])) }}" class="filter-badge-close" title="Hapus filter">&times;</a>
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <form method="GET" class="filter-card-body">
        <div class="report-filter">
            <div>
                <label class="filter-label">Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="filter-input">
            </div>
            <div>
                <label class="filter-label">Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="filter-input">
            </div>
            <div>
                <label class="filter-label">Status</label>
                <select name="status" class="filter-input filter-select">
                    <option value="">Semua status</option>
                    @foreach(['direncanakan'=>'Direncanakan','berlangsung'=>'Berlangsung','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'] as $v=>$l)
                        <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="filter-label">Kabupaten/Kota</label>
                <select name="kabupaten" id="r_kab" data-selected="{{ request('kabupaten') }}" class="filter-input filter-select">
                    <option value="">Semua wilayah</option>
                </select>
            </div>
            <div>
                <label class="filter-label">Kecamatan</label>
                <select name="kecamatan" id="r_kec" data-selected="{{ request('kecamatan') }}" class="filter-input filter-select">
                    <option value="">Semua kecamatan</option>
                </select>
            </div>
            <div>
                <label class="filter-label">Desa</label>
                <select name="desa" id="r_desa" data-selected="{{ request('desa') }}" class="filter-input filter-select">
                    <option value="">Semua desa</option>
                </select>
            </div>
            <div>
                <label class="filter-label">Kelompok</label>
                <select name="kelompok_id" class="filter-input filter-select">
                    <option value="">Semua kelompok</option>
                    @foreach($kelompoks as $k)
                        <option value="{{ $k->id }}" {{ (string)request('kelompok_id')===(string)$k->id?'selected':'' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-separator"></div>

        <div class="report-actions">
            <a href="{{ route('laporan.index') }}" class="btn-shadcn btn-shadcn-ghost">✕ Bersihkan</a>
            <button class="btn-shadcn btn-shadcn-primary">✓ Terapkan</button>
            <a href="{{ route('laporan.export-csv', request()->query()) }}" class="btn-shadcn btn-shadcn-outline">⬇ Unduh CSV</a>
            <button type="button" onclick="window.print()" class="btn-shadcn btn-shadcn-outline">🖨 Cetak/PDF</button>
        </div>
    </form>
</div>

{{-- ===== KPI CARDS (shadcn/ui style) ===== --}}
<div class="report-kpi-grid">
    <div class="report-kpi">
        <div class="report-kpi-value" style="color:#017723;">Rp {{ number_format($totals['dana_masuk'],0,',','.') }}</div>
        <div class="report-kpi-label">Dana Masuk Periode</div>
    </div>
    <div class="report-kpi">
        <div class="report-kpi-value">Rp {{ number_format($totals['nilai_bantuan'],0,',','.') }}</div>
        <div class="report-kpi-label">Nilai Bantuan</div>
    </div>
    <div class="report-kpi">
        <div class="report-kpi-value" style="color:#e5a820;">Rp {{ number_format($totals['biaya_operasional'],0,',','.') }}</div>
        <div class="report-kpi-label">Operasional Distribusi</div>
    </div>
    <div class="report-kpi">
        <div class="report-kpi-value" style="color:{{ $totals['sisa_dana'] < 0 ? '#dc2626' : '#017723' }};">Rp {{ number_format($totals['sisa_dana'],0,',','.') }}</div>
        <div class="report-kpi-label">Saldo Terhitung</div>
    </div>
</div>

<div class="report-kpi-grid" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr));">
    <div class="report-kpi">
        <div class="report-kpi-value">{{ number_format($distribusi->count()) }}</div>
        <div class="report-kpi-label">Kegiatan Distribusi</div>
    </div>
    <div class="report-kpi">
        <div class="report-kpi-value">{{ number_format($totals['paket']) }}</div>
        <div class="report-kpi-label">Paket</div>
    </div>
    <div class="report-kpi">
        <div class="report-kpi-value">{{ number_format($totals['penerima']) }}</div>
        <div class="report-kpi-label">Target Penerima</div>
    </div>
    <div class="report-kpi">
        <div class="report-kpi-value">{{ number_format($totals['terverifikasi']) }}</div>
        <div class="report-kpi-label">Terverifikasi</div>
    </div>
    <div class="report-kpi">
        <div class="report-kpi-value">{{ number_format($totals['menerima']) }}</div>
        <div class="report-kpi-label">Tanda Terima</div>
    </div>
</div>

{{-- ===== RINGKASAN TABLE ===== --}}
<div class="filter-card" style="margin-bottom:18px;">
    <div class="filter-card-header">
        <h3>📍 Ringkasan per Kabupaten/Kota</h3>
    </div>
    <div class="laporan-table-wrap" style="padding:16px 20px;">
        <table class="table-data" id="tblRingkasan">
            <thead>
                <tr>
                    <th>Wilayah</th><th>Kegiatan</th><th>Paket</th><th class="text-end">Nilai</th>
                    <th class="text-end">Target</th><th class="text-end">Terverifikasi</th><th class="text-end">Tanda Terima</th>
                </tr>
            </thead>
            <tbody>
            @forelse($perDaerah as $row)
                <tr>
                    <td style="font-weight:600;">{{ $row['daerah'] }}</td>
                    <td>{{ number_format($row['kegiatan']) }}</td>
                    <td>{{ number_format($row['paket']) }}</td>
                    <td class="text-end">Rp {{ number_format($row['nilai'],0,',','.') }}</td>
                    <td class="text-end">{{ number_format($row['penerima']) }}</td>
                    <td class="text-end">{{ number_format($row['terverifikasi']) }}</td>
                    <td class="text-end">{{ number_format($row['menerima']) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:28px;color:#9ca3af;">Tidak ada data sesuai filter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== RINCIAN TABLE ===== --}}
<div class="filter-card">
    <div class="filter-card-header">
        <h3>📦 Rincian Distribusi</h3>
    </div>
    <div class="laporan-table-wrap" style="padding:16px 20px;">
        <table class="table-data" id="tblRincian">
            <thead>
                <tr>
                    <th>Tanggal/Kode</th><th>Kegiatan/Wilayah</th><th>Kelompok</th>
                    <th class="text-center">Status</th><th class="text-end">Paket</th>
                    <th class="text-end">Nilai</th><th class="text-end">Target</th>
                    <th class="text-end">Terverifikasi</th><th class="text-end">Tanda Terima</th>
                </tr>
            </thead>
            <tbody>
            @forelse($distribusi as $d)
                <tr>
                    <td>{{ optional($d->tanggal)->format('d/m/Y') ?? $d->tanggal }}<br><small style="color:#6b7280;">{{ $d->kode_distribusi }}</small></td>
                    <td><a href="{{ route('distribusi.show',$d) }}" style="font-weight:600;color:#00034a;text-decoration:none;">{{ $d->nama_kegiatan }}</a><br><small style="color:#6b7280;">{{ optional($d->kelompok)->daerah }} · {{ optional($d->kelompok)->kecamatan }} · {{ optional($d->kelompok)->desa }}</small></td>
                    <td>{{ optional($d->kelompok)->nama ?? '-' }}</td>
                    <td class="text-center">{{ ucfirst($d->status) }}</td>
                    <td class="text-end">{{ number_format($d->jumlah_paket) }}</td>
                    <td class="text-end">Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}</td>
                    <td class="text-end">{{ number_format(optional($d->kelompok)->penerima_count ?? 0) }}</td>
                    <td class="text-end">{{ number_format(optional($d->kelompok)->penerima_terverifikasi_count ?? 0) }}</td>
                    <td class="text-end">{{ number_format($d->tanda_terima_count ?: (optional($d->kelompok)->penerima_menerima_count ?? 0)) }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;padding:28px;color:#9ca3af;">Tidak ada distribusi sesuai filter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
const rKab=document.getElementById('r_kab'),rKec=document.getElementById('r_kec'),rDesa=document.getElementById('r_desa');

function fill(select, rows, selected, placeholder, kab=false) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    rows.forEach(x => {
        const o = document.createElement('option');
        o.value = kab ? x.nama.replace(/^(Kabupaten|Kota)\s/, '\x27').replace('\x27', '') : x.nama;
        o.dataset.kode = x.kode;
        o.textContent = x.nama;
        if (selected && o.value.toLowerCase() === selected.toLowerCase()) o.selected = true;
        select.appendChild(o);
    });
}

function code(select) {
    return select.options[select.selectedIndex]?.dataset.kode || '';
}

async function loadDesa(pre) {
    const k = code(rKec);
    if (!k) return fill(rDesa, [], '', 'Semua desa');
    const rows = await fetch(`/api/wilayah/desa/${encodeURIComponent(k)}`).then(r => r.json());
    fill(rDesa, rows, pre ? rDesa.dataset.selected : '', 'Semua desa');
}

async function loadKec(pre) {
    const k = code(rKab);
    if (!k) {
        fill(rKec, [], '', 'Semua kecamatan');
        fill(rDesa, [], '', 'Semua desa');
        return;
    }
    const rows = await fetch(`/api/wilayah/kecamatan/${encodeURIComponent(k)}`).then(r => r.json());
    fill(rKec, rows, pre ? rKec.dataset.selected : '', 'Semua kecamatan');
    if (rKec.value) await loadDesa(pre);
}

fetch('/api/wilayah/kabupaten').then(r => r.json()).then(async rows => {
    fill(rKab, rows, rKab.dataset.selected, 'Semua wilayah', true);
    if (rKab.value) await loadKec(true);
});

rKab.addEventListener('change', () => loadKec(false));
rKec.addEventListener('change', () => loadDesa(false));

// === Init DataTables ===
if ($('#tblRingkasan').length) {
    $('#tblRingkasan').DataTable({
        ordering: true,
        searching: false,
        info: true,
        paging: true,
        columnDefs: [{ targets: '_all', className: 'dt-nowrap' }]
    });
}

if ($('#tblRincian').length) {
    $('#tblRincian').DataTable({
        ordering: true,
        searching: true,
        info: true,
        paging: true,
        language: {
            search: '🔍 Cari:',
            searchPlaceholder: 'Ketik untuk mencari...'
        },
        columnDefs: [
            { targets: [4, 5, 6, 7, 8], className: 'dt-nowrap text-end' },
            { targets: [3], className: 'dt-nowrap text-center' }
        ]
    });
}
</script>
@endsection
