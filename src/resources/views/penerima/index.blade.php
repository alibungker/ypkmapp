@extends('layouts.app')
@section('title', 'Data Penerima')
@section('styles')
<style>
#penerimaTable thead tr:first-child th{background:#00034a;color:#fff;font-weight:600;text-transform:uppercase;letter-spacing:.5px;border-bottom:3px solid #e5a820}
#penerimaTable tbody tr:nth-child(even){background:#f4f6fb}
#penerimaTable tbody tr:hover{background:#fff7e6}
#penerimaTable .dt-column-order:before,#penerimaTable .dt-column-order:after{color:#e5a820}
.dt-container{padding:14px 16px}.dt-container .dt-search input,.dt-container .dt-length select{border:1.5px solid #cbd5e1;border-radius:8px;padding:7px 11px;background:#fff;font-size:13px}.dt-container .dt-search input:focus{outline:none;border-color:#00034a;box-shadow:0 0 0 3px rgba(0,3,74,.1)}.dt-container .dt-paging .dt-paging-button{border-radius:7px}.dt-container .dt-paging .dt-paging-button.current{background:#00034a!important;color:#fff!important;border-color:#00034a!important}.dt-container .dt-info{color:#667085;font-size:12px}
.badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap}
</style>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h3 style="font-size:15px;font-weight:600;">Data penerima manfaat</h3>
        <div class="button-row">
            <a href="{{ route('penerima.import.template') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">⬇️ Template CSV</a>
            <a href="{{ route('penerima.create') }}" class="btn btn-primary btn-sm">+ Tambah</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-grid" style="margin-bottom:16px;">
            <input type="text" name="search" placeholder="Cari NIK/nama..." value="{{ request('search') }}" class="form-input" style="width:170px;padding:8px 12px;font-size:13px;">
            <select name="status" class="form-input" style="width:170px;padding:8px 12px;font-size:13px;">
                <option value="">Semua status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                <option value="terverifikasi" {{ request('status')=='terverifikasi' ? 'selected' : '' }}>📦 Terverifikasi</option>
                <option value="menunggu_bantuan" {{ request('status')=='menunggu_bantuan' ? 'selected' : '' }}>📦 Menunggu Bantuan</option>
                <option value="menerima_bantuan" {{ request('status')=='menerima_bantuan' ? 'selected' : '' }}>✅ Menerima Bantuan</option>
                <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
            </select>
            <select name="kabupaten" id="f_kab" data-selected="{{ request('kabupaten') }}" class="form-input" style="width:170px;padding:8px 12px;font-size:13px;">
                <option value="">Semua Kabupaten</option>
            </select>
            <details class="filter-advanced" {{ request()->hasAny(['kecamatan','desa','kelompok_id','sumber_data','status_terima']) ? 'open' : '' }}>
                <summary>Filter lanjutan</summary>
                <div class="filter-advanced__grid">
            <select name="kecamatan" id="f_kec" data-selected="{{ request('kecamatan') }}" class="form-input" style="width:155px;padding:8px 12px;font-size:13px;">
                <option value="">Semua Kecamatan</option>
            </select>
            <select name="desa" id="f_desa" data-selected="{{ request('desa') }}" class="form-input" style="width:150px;padding:8px 12px;font-size:13px;">
                <option value="">Semua Desa</option>
            </select>
            <select name="kelompok_id" class="form-input" style="width:170px;padding:8px 12px;font-size:13px;">
                <option value="">Semua Kelompok</option>
                @foreach($kelompoks as $k)
                <option value="{{ $k->id }}" {{ (string) request('kelompok_id') === (string) $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
            <select name="sumber_data" class="form-input" style="width:145px;padding:8px 12px;font-size:13px;">
                <option value="">Semua sumber</option>
                @foreach(['relawan' => 'Relawan', 'mandiri' => 'Mandiri', 'ketua_kelompok' => 'Ketua Kelompok'] as $value => $label)
                <option value="{{ $value }}" {{ request('sumber_data') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status_terima" class="form-input" style="width:155px;padding:8px 12px;font-size:13px;">
                <option value="">Semua penerimaan</option>
                <option value="1" {{ request('status_terima') === '1' ? 'selected' : '' }}>Sudah menerima</option>
                <option value="0" {{ request('status_terima') === '0' ? 'selected' : '' }}>Belum menerima</option>
            </select>
                </div>
            </details>
            <button class="btn btn-outline btn-sm">Terapkan</button>
            <a href="{{ route('penerima.index') }}" class="btn btn-outline btn-sm">Bersihkan filter</a>
        </form>
        <details style="margin-bottom:16px;border:1px dashed #cbd5e1;border-radius:8px;padding:8px 12px;">
            <summary style="font-size:13px;color:#00034a;cursor:pointer;">📥 Import data dari CSV</summary>
            <div style="margin-top:10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <form method="POST" action="{{ route('penerima.import.preview') }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    @csrf
                    <input type="file" name="csv_file" accept=".csv,.txt" required class="form-input" style="font-size:13px;">
                    <button type="submit" class="btn btn-primary btn-sm">🔍 Preview & Cek</button>
                </form>
                <a href="{{ route('penerima.import.template') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">⬇️ Download template dulu</a>
            </div>
            <p style="font-size:12px;color:#667085;margin:8px 0 0;">Format: CSV (kolom sesuai template). Data valid langsung ditampilkan untuk dikonfirmasi sebelum disimpan. NIK duplikat & data tidak lengkap otomatis ditolak.</p>
        </details>
        <p style="font-size:13px;color:#667085;margin:0 0 12px;">Menampilkan {{ number_format(count($penerima)) }} penerima</p>
        <div class="table-wrap desktop-table">
            <table id="penerimaTable" class="table-data display">
                <thead>
                    <tr><th>Nama Lengkap</th><th>NIK</th><th>Pekerjaan</th><th>Kecamatan</th><th>Desa</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($penerima as $p)
                    <tr>
                        <td style="font-weight:500;">{{ $p->nama }}</td>
                        <td style="color:#6b7280;font-family:monospace;"><x-masked-nik :value="$p->nik" /></td>
                        <td>{{ $p->pekerjaan ?? '-' }}</td>
                        <td style="color:#6b7280;">{{ $p->kecamatan }}</td>
                        <td style="color:#6b7280;">{{ $p->desa }}</td>
                        <td>
                            @if($p->status == 'ditolak') <span class="badge" style="background:#fce8e6;color:#dc2626;">❌ Ditolak</span>
                            @elseif($p->status == 'pending') <span class="badge" style="background:#fef3c2;color:#92400e;">⏳ Menunggu Verifikasi</span>
                            @elseif($p->terima_bantuan) <span class="badge badge-green">✅ Menerima Bantuan</span>
                            @else <span class="badge" style="background:#dbeafe;color:#1e40af;">📦 Menunggu Bantuan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('penerima.show', $p) }}" style="color:#00034a;text-decoration:none;font-size:13px;">Detail</a>
                            <a href="{{ route('penerima.edit', $p) }}" style="color:#6b7280;margin-left:12px;font-size:13px;">Edit</a>
                            @if($p->status == 'pending' && (auth()->user()->isAdmin() || auth()->user()->isRelawan()))
                            <form method="POST" action="{{ route('penerima.verify', $p) }}" style="display:inline;margin-left:8px;">
                                @csrf
                                <input type="hidden" name="status" value="terverifikasi">
                                <button style="color:#017723;border:none;background:none;cursor:pointer;font-size:13px;">✅ Verifikasi</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada data penerima</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mobile-card-list" aria-label="Daftar penerima">
            @forelse($penerima as $p)
            <article class="mobile-data-card">
                <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;">
                    <div>
                        <div class="mobile-data-card__title">{{ $p->nama }}</div>
                        <div class="mobile-data-card__meta">
                            NIK <x-masked-nik :value="$p->nik" /><br>
                            {{ $p->kecamatan ?: '-' }} · {{ $p->desa ?: '-' }}
                        </div>
                    </div>
                    @if($p->status == 'ditolak') <span class="badge" style="background:#fce8e6;color:#dc2626;">❌ Ditolak</span>
                    @elseif($p->status == 'pending') <span class="badge" style="background:#fef3c2;color:#92400e;">⏳ Menunggu Verifikasi</span>
                    @elseif($p->terima_bantuan) <span class="badge badge-green">✅ Menerima Bantuan</span>
                    @else <span class="badge" style="background:#dbeafe;color:#1e40af;">📦 Menunggu Bantuan</span>
                    @endif
                </div>
                <div class="mobile-data-card__actions">
                    <a href="{{ route('penerima.show', $p) }}" class="btn btn-outline btn-sm">Detail</a>
                    <a href="{{ route('penerima.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
                    @if($p->status == 'pending' && (auth()->user()->isAdmin() || auth()->user()->isRelawan()))
                    <form method="POST" action="{{ route('penerima.verify', $p) }}">@csrf<input type="hidden" name="status" value="terverifikasi"><button class="btn btn-primary btn-sm">Verifikasi</button></form>
                    @endif
                </div>
            </article>
            @empty
            <div class="mobile-data-card" style="text-align:center;color:#667085;">Tidak ada penerima sesuai filter.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('penerimaTable')) {
        new DataTable('#penerimaTable', {
            pageLength: 20,
            lengthMenu: [[10,20,50,-1],[10,20,50,'Semua']],
            order: [[0,'asc']],
            columnDefs: [{targets: 6, orderable: false, searchable: false}],
            language: {
                search: 'Cari semua:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(disaring dari _MAX_ data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
            }
        });
    }
});
</script>
<script>
const fKab = document.getElementById('f_kab');
const fKec = document.getElementById('f_kec');
const fDesa = document.getElementById('f_desa');

function cleanWilayahName(item, kabupaten = false) {
    return kabupaten ? item.nama.replace(/^(Kabupaten|Kota)\s/, '') : item.nama;
}
function fillWilayah(select, rows, selected, placeholder, kabupaten = false) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    rows.forEach(item => {
        const option = document.createElement('option');
        option.value = cleanWilayahName(item, kabupaten);
        option.dataset.kode = item.kode;
        option.textContent = item.nama;
        if (selected && option.value.toLowerCase() === selected.toLowerCase()) option.selected = true;
        select.appendChild(option);
    });
}
function selectedKode(select) {
    return select.options[select.selectedIndex]?.dataset.kode || '';
}
async function loadFilterDesa(preselect = false) {
    const kode = selectedKode(fKec);
    if (!kode) return fillWilayah(fDesa, [], '', 'Semua Desa');
    const rows = await fetch(`/api/wilayah/desa/${encodeURIComponent(kode)}`).then(r => r.json());
    fillWilayah(fDesa, rows, preselect ? fDesa.dataset.selected : '', 'Semua Desa');
}
async function loadFilterKecamatan(preselect = false) {
    const kode = selectedKode(fKab);
    if (!kode) {
        fillWilayah(fKec, [], '', 'Semua Kecamatan');
        fillWilayah(fDesa, [], '', 'Semua Desa');
        return;
    }
    const rows = await fetch(`/api/wilayah/kecamatan/${encodeURIComponent(kode)}`).then(r => r.json());
    fillWilayah(fKec, rows, preselect ? fKec.dataset.selected : '', 'Semua Kecamatan');
    if (fKec.value) await loadFilterDesa(preselect);
}

fetch('/api/wilayah/kabupaten').then(r => r.json()).then(async rows => {
    fillWilayah(fKab, rows, fKab.dataset.selected, 'Semua Kabupaten', true);
    if (fKab.value) await loadFilterKecamatan(true);
});
fKab.addEventListener('change', () => loadFilterKecamatan(false));
fKec.addEventListener('change', () => loadFilterDesa(false));
</script>
@endsection
