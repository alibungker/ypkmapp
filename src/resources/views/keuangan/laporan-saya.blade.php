@extends('layouts.app')
@section('title', 'Laporan Keuangan Saya')
@section('styles')
@endsection
@section('content')
<style>
.ls-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:20px}
.ls-card{background:#fff;border-radius:12px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #eef0f3}
.ls-card h4{font-size:12px;color:#667085;font-weight:600;margin-bottom:8px;letter-spacing:.02em}
.ls-card .val{font-size:22px;font-weight:800;color:#00034a}
.ls-card .val.gold{color:#b07d14}
.ls-card .val.green{color:#017723}
.ls-table{width:100%;border-collapse:collapse;font-size:13px}
.ls-table th{background:#f9fafb;color:#374151;font-weight:600;text-align:left;padding:10px 12px;border-bottom:1px solid #e5e7eb}
.ls-table td{padding:10px 12px;border-bottom:1px solid #eef0f3;vertical-align:middle}
.badge-bukti{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700}
.bg-green{background:#dcfce7;color:#065f46}.bg-red{background:#fee2e2;color:#991b1b}
.bukti-link{display:inline-flex;align-items:center;gap:5px;color:#00034a;font-weight:600;text-decoration:none}
.bukti-link:hover{text-decoration:underline}
.card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #eef0f3}
.card-header{padding:16px 20px;border-bottom:1px solid #eef0f3}
.dt-container{padding:14px 16px}.dt-container .dt-search input,.dt-container .dt-length select{border:1px solid #d0d5dd;border-radius:8px;padding:7px 10px;background:#fff}.dt-container .dt-search input:focus{outline:none;border-color:#00034a;box-shadow:0 0 0 3px rgba(0,3,74,.08)}.dt-container .dt-paging .dt-paging-button.current{background:#00034a!important;color:#fff!important;border-color:#00034a!important;border-radius:7px}.column-filter{width:100%;min-width:90px;border:1px solid #d0d5dd;border-radius:6px;padding:6px 8px;font-size:12px;background:#fff}.filter-row th{padding:7px 6px!important;background:#fff!important}.dt-info{color:#667085;font-size:12px}
@media(max-width:640px){.ls-grid{grid-template-columns:1fr}}
</style>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:16px">
    <div>
        <h2 style="font-size:18px;font-weight:800;color:#00034a;">📄 Laporan Keuangan Saya</h2>
        <p style="font-size:13px;color:#667085;margin-top:3px;">Riwayat pengeluaran yang saya input beserta bukti.</p>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;color:#065f46;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">{{ $errors->first() }}</div>
@endif

{{-- Card Summary --}}
<div class="ls-grid">
    <div class="ls-card" style="background:linear-gradient(135deg,#00034a,#01266b);border:none;">
        <h4 style="color:rgba(255,255,255,.7)">Saldo Top-up Saya</h4>
        <div class="val" style="color:#ffd966">{{ $totalTopup ? 'Rp '.number_format($sisaSaldo,0,',','.') : 'Rp 0' }}</div>
        <div style="color:rgba(255,255,255,.55);font-size:11px;margin-top:4px">Dari Rp {{ number_format($totalTopup,0,',','.') }} disetujui</div>
    </div>
    <div class="ls-card"><h4>Total Pengeluaran Saya</h4><div class="val">Rp {{ number_format($total,0,',','.') }}</div></div>
    <div class="ls-card"><h4>Jumlah Transaksi</h4><div class="val gold">{{ $jumlahPengeluaran }}</div></div>
</div>

{{-- Tombol pemicu modal --}}
<div style="margin-bottom:20px;display:flex;gap:10px;align-items:center">
    <button onclick="openCreateModal()" class="btn btn-primary" style="font-size:14px;padding:10px 20px">
        ➕ Lapor Pengeluaran Baru
    </button>
</div>

{{-- Modal Form Pengeluaran --}}
<div id="createModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1050;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('createModal')">
    <div style="background:white;border-radius:12px;width:90%;max-width:700px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <div style="padding:18px 22px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center">
            <h3 style="font-size:16px;font-weight:700;margin:0">📝 Lapor Pengeluaran Baru</h3>
            <button onclick="closeModal('createModal')" style="background:none;border:none;font-size:22px;cursor:pointer;color:#667085;padding:4px 8px" aria-label="Tutup">&times;</button>
        </div>
        <div style="padding:20px 22px;">
            <form method="POST" action="{{ route('laporan-saya.biaya') }}" enctype="multipart/form-data" onsubmit="return validateCreateForm()">
                @csrf
                <div id="createFormContent"></div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;padding-top:14px;border-top:1px solid #e5e7eb">
                    <button type="button" onclick="closeModal('createModal')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary">💾 Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Riwayat Top-up --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header"><h3 style="font-size:15px;font-weight:700;color:#00034a;">💳 Riwayat Top-up Saya</h3></div>
    <div style="overflow-x:auto">
    <table id="topupTable" class="ls-table display">
        <thead>
        <tr><th>Tanggal</th><th>Kegiatan</th><th style="text-align:right">Nominal</th><th>Status</th></tr>
        <tr class="filter-row"><th><input class="column-filter" placeholder="Cari tanggal"></th><th><input class="column-filter" placeholder="Cari kegiatan"></th><th><input class="column-filter" placeholder="Cari nominal"></th><th><input class="column-filter" placeholder="Cari status"></th></tr>
        </thead>
        <tbody>
        @forelse($topups as $t)
        <tr>
            <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y H:i') }}</td>
            <td>{{ $t->anggaran_id ? (\App\Models\Anggaran::find($t->anggaran_id)->nama_anggaran ?? \App\Models\Anggaran::find($t->anggaran_id)->kategori ?? '-') : 'Umum' }}</td>
            <td style="text-align:right;font-weight:700;color:#00034a">Rp {{ number_format($t->nominal,0,',','.') }}</td>
            <td>
                @if($t->status==='disetujui') <span class="badge-bukti bg-green">✅ Disetujui</span>
                @elseif($t->status==='diajukan') <span class="badge-bukti" style="background:#fef3c7;color:#92400e">⏳ Diajukan</span>
                @elseif($t->status==='ditolak') <span class="badge-bukti bg-red">❌ Ditolak</span>
                @else <span class="badge-bukti" style="background:#e5e7eb;color:#374151">{{ $t->status }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:28px">Belum ada top-up untuk Anda</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Riwayat Pengeluaran --}}
<div class="card">
    <div class="card-header"><h3 style="font-size:15px;font-weight:700;color:#00034a;">📋 Riwayat Pengeluaran Saya</h3></div>
    <div style="overflow-x:auto">
    <table id="pengeluaranTable" class="ls-table display">
        <thead>
        <tr><th>Tanggal</th><th>Deskripsi</th><th>Kategori</th><th>Kegiatan</th><th style="text-align:right">Jumlah</th><th>Bukti</th><th>Aksi</th></tr>
        <tr class="filter-row"><th><input class="column-filter" placeholder="Cari tanggal"></th><th><input class="column-filter" placeholder="Cari deskripsi"></th><th><input class="column-filter" placeholder="Cari kategori"></th><th><input class="column-filter" placeholder="Cari kegiatan"></th><th><input class="column-filter" placeholder="Cari jumlah"></th><th><input class="column-filter" placeholder="Cari bukti"></th><th></th></tr>
        </thead>
        <tbody>
        @forelse($biaya as $b)
        <tr>
            <td>{{ is_object($b->tanggal) ? $b->tanggal->format('d M Y') : date('d M Y', strtotime($b->tanggal)) }}</td>
            <td style="max-width:260px">{{ $b->deskripsi }}</td>
            <td><span class="transaction-type">{{ ucfirst(str_replace('_',' ',$b->kategori)) }}</span></td>
            <td>{{ $b->anggaran?->nama_anggaran ?: ($b->anggaran?->kategori ?? '-') }}</td>
            <td style="text-align:right;font-weight:700;color:#00034a">Rp {{ number_format($b->jumlah,0,',','.') }}</td>
            <td>
                @if($b->bukti_foto)
                <a class="bukti-link" href="{{ asset('storage/'.$b->bukti_foto) }}" target="_blank">📎 <span class="badge-bukti bg-green">Ada</span></a>
                @elseif($b->buktis->isNotEmpty())
                <span class="badge-bukti bg-green">{{ $b->buktis->count() }} 📎</span>
                @else
                <span class="badge-bukti bg-red">Tanpa Bukti</span>
                @endif
            </td>
            <td style="white-space:nowrap">
                <button class="btn btn-outline btn-sm" style="padding:2px 8px;font-size:11px" onclick="openDetail({{ $b->id }})">👁️ Detail</button>
                <button class="btn btn-outline btn-sm" style="padding:2px 8px;font-size:11px;color:#00034a" onclick="openEdit({{ $b->id }})">✏️</button>
                <form method="POST" action="{{ route('laporan-saya.destroy', $b->id) }}" style="display:inline" onsubmit="return confirm('Hapus pengeluaran ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline btn-sm" style="padding:2px 8px;font-size:11px;color:#dc2626">🗑️</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:32px">Belum ada pengeluaran yang dilaporkan</td></tr>
        @endforelse
        </tbody>
    </table>

    </div>
</div>

{{-- Modals --}}
<div id="detailModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1050;align-items:center;justify-content:center">
<div style="background:#fff;border-radius:12px;padding:20px;max-width:520px;width:90%;max-height:80vh;overflow-y:auto;box-shadow:0 8px 30px rgba(0,0,0,.2)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
        <h3 style="font-size:16px;font-weight:700;color:#00034a">📋 Detail Pengeluaran</h3>
        <button onclick="closeModal('detailModal')" style="border:none;background:none;font-size:20px;cursor:pointer;color:#6b7280">✕</button>
    </div>
    <div id="detailContent"></div>
</div>
</div>

<div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1050;align-items:center;justify-content:center">
<div style="background:#fff;border-radius:12px;padding:20px;max-width:560px;width:90%;max-height:85vh;overflow-y:auto;box-shadow:0 8px 30px rgba(0,0,0,.2)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
        <h3 style="font-size:16px;font-weight:700;color:#00034a">✏️ Edit Pengeluaran</h3>
        <button onclick="closeModal('editModal')" style="border:none;background:none;font-size:20px;cursor:pointer;color:#6b7280">✕</button>
    </div>
    <div id="editContent"></div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function initDataTable(selector, noOrderTargets) {
        var table = new DataTable(selector, {
            pageLength: 20,
            lengthMenu: [[10,20,50,-1],[10,20,50,'Semua']],
            orderCellsTop: true,
            order: [[0, 'desc']],
            columnDefs: [{ targets: noOrderTargets, orderable: false, searchable: false }],
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
        document.querySelectorAll(selector + ' thead .filter-row th').forEach(function(th, index) {
            var input = th.querySelector('input');
            if (!input) return;
            input.addEventListener('click', function(e) { e.stopPropagation(); });
            input.addEventListener('input', function() { table.column(index).search(this.value).draw(); });
        });
        return table;
    }
    initDataTable('#topupTable', []);
    initDataTable('#pengeluaranTable', [6]);
});
function openCreateModal() {
    var html = '';
    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">';
    html += '<div><label class="form-label">Kategori <span style="color:#dc2626">*</span></label><select name="kategori" class="form-input" required><option value="">— Pilih —</option>';
    @foreach(['barang_bantuan'=>'Barang Bantuan','transportasi'=>'Transportasi','konsumsi'=>'Konsumsi','hotel'=>'Hotel','sewa'=>'Sewa','atk'=>'ATK','cadangan'=>'Cadangan','lainnya'=>'Lainnya'] as $v=>$l)
    html += '<option value="{{ $v }}">{{ $l }}</option>';
    @endforeach
    html += '</select></div>';
    html += '<div><label class="form-label">Jumlah (Rp) *</label><input type="number" step="0.01" min="1" name="jumlah" class="form-input" required></div>';
    html += '</div>';
    html += '<div style="margin-bottom:14px"><label class="form-label">Tanggal *</label><input type="date" name="tanggal" class="form-input" required value="{{ date('Y-m-d') }}"></div>';
    html += '<div style="margin-bottom:14px"><label class="form-label">Deskripsi Pengeluaran *</label><textarea name="deskripsi" rows="2" class="form-input" required placeholder="Contoh: Pembelian bahan pokok batch 4"></textarea></div>';
    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px"><div><label class="form-label">Kegiatan Terkait</label><select name="anggaran_id" class="form-input"><option value="">— Tidak terkait kegiatan —</option>';
    @foreach($kegiatanList as $k)
    html += '<option value="{{ $k->id }}">{{ $k->nama_anggaran ?: $k->kategori }} (Rp {{ number_format($k->anggaran,0,',','.') }})</option>';
    @endforeach
    html += '</select></div>';
    html += '<div><label class="form-label">Bukti Utama (Opsional)</label><input type="file" name="bukti_foto" class="form-input" accept="image/*,.pdf"></div>';
    html += '</div>';
    html += '<div style="margin-bottom:14px"><label class="form-label">Unggah Bukti Tambahan (Opsional)</label><input type="file" name="bukti_files[]" class="form-input" multiple accept="image/*,.pdf,.doc,.docx"><div style="font-size:12px;color:#667085;margin-top:4px">Max 5 MB per file. Format: JPG, PNG, PDF, DOC, DOCX.</div></div>';
    document.getElementById('createFormContent').innerHTML=html;
    document.getElementById('createModal').style.display='flex';
}
function validateCreateForm() {
    var kat = document.querySelector('#createFormContent select[name="kategori"]');
    var jum = document.querySelector('#createFormContent input[name="jumlah"]');
    var tgl = document.querySelector('#createFormContent input[name="tanggal"]');
    var desc = document.querySelector('#createFormContent textarea[name="deskripsi"]');
    if (!kat || !kat.value) { alert('Kategori wajib dipilih.'); return false; }
    if (!jum || !jum.value || parseFloat(jum.value) <= 0) { alert('Jumlah harus lebih dari 0.'); return false; }
    if (!tgl || !tgl.value) { alert('Tanggal wajib diisi.'); return false; }
    if (!desc || !desc.value.trim()) { alert('Deskripsi wajib diisi.'); return false; }
    return true;
}
function openDetail(id) {
    fetch('/keuangan/laporan-saya/'+id+'/detail').then(function(r){return r.json()}).then(function(d){
        var html='<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">';
        html+='<div><strong>Tanggal</strong><br>'+(d.tanggal||'-')+'</div>';
        html+='<div><strong>Kategori</strong><br>'+(d.kategori||'-')+'</div>';
        html+='</div>';
        html+='<div style="margin-bottom:10px"><strong>Deskripsi</strong><p>'+(d.deskripsi||'-')+'</p></div>';
        html+='<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px"><div><strong>Kegiatan</strong><br>'+(d.anggaran_nama||'— Tidak terkait —')+'</div><div><strong>Jumlah</strong><br style="color:#00034a;font-size:18px">Rp '+number_format(d.jumlah)+'</div></div>';
        if(d.bukti_foto){html+='<div style="margin-bottom:10px"><strong>Bukti Utama</strong><br><a href="'+d.bukti_foto+'" target="_blank">📎 Buka file</a></div>';}
        if(d.buktis&&d.buktis.length){html+='<div><strong>Bukti Tambahan ('+d.buktis.length+')</strong><ul style="padding-left:18px;font-size:13px">';
        d.buktis.forEach(function(b){html+='<li><a href="'+b.url+'" target="_blank">'+b.name+'</a> <span style="color:#667085">('+b.tipe+')</span></li>';});
        html+='</ul></div>';}
        document.getElementById('detailContent').innerHTML=html;
        document.getElementById('detailModal').style.display='flex';
    });
}
function openEdit(id) {
    fetch('/keuangan/laporan-saya/'+id+'/detail').then(function(r){return r.json()}).then(function(d){
        var html='<form method="POST" action="/keuangan/laporan-saya/'+d.id+'" enctype="multipart/form-data">';
        html+='<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PUT">';
        html+='<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px"><div><label class="form-label">Kategori</label><select name="kategori" class="form-input">';
        ['barang_bantuan','transportasi','konsumsi','hotel','sewa','atk','cadangan','lainnya'].forEach(function(k){
            html+='<option value="'+k+'"'+(d.kategori===k?' selected':'')+'>'+k+'</option>';
        });
        html+='</select></div><div><label class="form-label">Jumlah (Rp)</label><input type="number" step="0.01" min="1" name="jumlah" class="form-input" value="'+d.jumlah+'" required></div></div>';
        html+='<div style="margin-bottom:14px"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-input" value="'+d.tanggal+'" required></div>';
        html+='<div style="margin-bottom:14px"><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="2" class="form-input" required>'+d.deskripsi+'</textarea></div>';
        html+='<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px"><div><label class="form-label">Kegiatan Terkait</label><select name="anggaran_id" class="form-input"><option value="">— Tidak terkait —</option>';
        @foreach($kegiatanList as $k)
        html+='<option value="{{ $k->id }}"'+ (d.anggaran_id==='{{ $k->id }}'?' selected':'') +'>{{ $k->nama_anggaran ?: $k->kategori }}</option>';
        @endforeach
        html+='</select></div><div><label class="form-label">Bukti Utama (ganti jika perlu)</label><input type="file" name="bukti_foto" class="form-input" accept="image/*,.pdf"></div></div>';
        html+='<div style="margin-bottom:14px"><label class="form-label">Unggah Bukti Tambahan</label><input type="file" name="bukti_files[]" class="form-input" multiple accept="image/*,.pdf,.doc,.docx"><div style="font-size:12px;color:#667085;margin-top:4px">Max 5 MB per file. JPG, PNG, PDF, DOC, DOCX.</div></div>';
        if(d.buktis&&d.buktis.length){html+='<div style="margin-bottom:10px"><strong>Bukti saat ini:</strong><ul style="padding-left:18px;font-size:12px">';
        d.buktis.forEach(function(b){html+='<li>'+b.name+' <a href="/keuangan/laporan-saya/bukti/'+b.id+'/hapus" style="color:#dc2626" onclick="return confirm(\'Hapus bukti ini?\')">🗑</a></li>';});
        html+='</ul></div>';}
        html+='<div style="display:flex;gap:10px;justify-content:flex-end;margin-top:14px;padding-top:14px;border-top:1px solid #e5e7eb"><button type="button" onclick="closeModal(\'editModal\')" class="btn btn-outline">Batal</button><button type="submit" class="btn btn-primary">💾 Update</button></div></form>';
        document.getElementById('editContent').innerHTML=html;
        document.getElementById('editModal').style.display='flex';
    });
}
function closeModal(id) { document.getElementById(id).style.display='none'; }
function number_format(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
document.addEventListener('click',function(e){if(e.target.id==='detailModal'||e.target.id==='editModal'||e.target.id==='createModal')e.target.style.display='none'});
</script>
@endsection