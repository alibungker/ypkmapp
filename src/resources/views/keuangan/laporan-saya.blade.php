@extends('layouts.app')
@section('title', 'Laporan Keuangan Saya')
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
.ls-form{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #eef0f3;margin-bottom:20px}
@media(max-width:640px){.ls-grid{grid-template-columns:1fr}.ls-form .row{grid-template-columns:1fr!important}}
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

<div class="ls-grid">
    <div class="ls-card" style="background:linear-gradient(135deg,#00034a,#01266b);border:none;"><h4 style="color:rgba(255,255,255,.7)">Saldo Top-up Saya</h4><div class="val" style="color:#ffd966">{{ $totalTopup ? 'Rp '.number_format($sisaSaldo,0,',','.') : 'Rp 0' }}</div><div style="color:rgba(255,255,255,.55);font-size:11px;margin-top:4px">Dari Rp {{ number_format($totalTopup,0,',','.') }} disetujui</div></div>
    <div class="ls-card"><h4>Total Pengeluaran Saya</h4><div class="val">Rp {{ number_format($total,0,',','.') }}</div></div>
    <div class="ls-card"><h4>Jumlah Transaksi</h4><div class="val gold">{{ $jumlahPengeluaran }}</div></div>
</div>

<div class="ls-form">
    <h3 style="font-size:15px;font-weight:700;color:#00034a;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #eef0f3;">➕ Lapor Pengeluaran Baru</h3>
    <form method="POST" action="{{ route('keuangan.laporan-saya.biaya') }}" enctype="multipart/form-data">
        @csrf
        <div class="row" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px">
            <div><label class="form-label">Kategori <span style="color:#dc2626">*</span></label>
                <select name="kategori" class="form-input" required>
                    <option value="">— Pilih —</option>
                    @foreach(['barang_bantuan'=>'Barang Bantuan','transportasi'=>'Transportasi','konsumsi'=>'Konsumsi','hotel'=>'Hotel','sewa'=>'Sewa','atk'=>'ATK','cadangan'=>'Cadangan','lainnya'=>'Lainnya'] as $v=>$l)
                    <option value="{{ $v }}" @selected(old('kategori')===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="form-label">Jumlah (Rp) <span style="color:#dc2626">*</span></label><input type="number" min="1" step="0.01" name="jumlah" class="form-input" required value="{{ old('jumlah') }}" placeholder="0"></div>
            <div><label class="form-label">Tanggal <span style="color:#dc2626">*</span></label><input type="date" name="tanggal" class="form-input" required value="{{ old('tanggal', now()->format('Y-m-d')) }}"></div>
        </div>
        <div style="margin-top:14px"><label class="form-label">Deskripsi Pengeluaran <span style="color:#dc2626">*</span></label><textarea name="deskripsi" rows="2" class="form-input" required placeholder="Contoh: Pembelian bahan pokok batch 4">{{ old('deskripsi') }}</textarea></div>
        <div class="row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
            <div><label class="form-label">Kegiatan Terkait</label>
                <select name="anggaran_id" class="form-input">
                    <option value="">— Tidak terkait kegiatan —</option>
                    @foreach($kegiatanList as $k)
                    <option value="{{ $k->id }}" @selected(old('anggaran_id')==$k->id)>{{ $k->nama_anggaran ?: $k->kategori }} (Rp {{ number_format($k->anggaran,0,',','.') }})</option>
                    @endforeach
                </select>
                <small class="form-hint" style="display:block;margin-top:4px;color:#6b7280">Daftar dari Barang &amp; Kegiatan → tab Kegiatan</small></div>
            <div><label class="form-label">Bukti Pengeluaran</label><input type="file" name="bukti_foto" class="form-input" accept=".jpg,.jpeg,.png,.pdf"><small class="form-hint" style="display:block;margin-top:4px;color:#6b7280">Foto nota/kwitansi (JPG/PNG/PDF, maks 5MB)</small></div>
        </div>
        <div style="margin-top:12px"><label class="form-label">Bukti Tambahan (Opsional)</label><input type="file" name="bukti_files[]" class="form-input" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple><small class="form-hint" style="display:block;margin-top:4px;color:#6b7280">Gambar atau dokumen (JPG/PNG/PDF/DOC, maks 5MB per file)</small></div>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:18px"><button type="submit" class="btn btn-primary">📤 Simpan Pengeluaran</button></div>
    </form>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-header"><h3 style="font-size:15px;font-weight:700;color:#00034a;">💳 Riwayat Top-up Saya</h3></div>
    <div style="overflow-x:auto">
    <table class="ls-table">
        <thead><tr><th>Tanggal</th><th>Kegiatan</th><th style="text-align:right">Nominal</th><th>Status</th></tr></thead>
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

<div class="card">
    <div class="card-header"><h3 style="font-size:15px;font-weight:700;color:#00034a;">📋 Riwayat Pengeluaran Saya</h3></div>
    <div style="overflow-x:auto">
    <table class="ls-table">
        <thead><tr><th>Tanggal</th><th>Deskripsi</th><th>Kategori</th><th>Kegiatan</th><th style="text-align:right">Jumlah</th><th>Bukti</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($biaya as $b)
        <tr>
            <td>{{ is_object($b->tanggal) ? $b->tanggal->format('d M Y') : date('d M Y', strtotime($b->tanggal)) }}</td>
            <td style="max-width:260px">{{ $b->deskripsi }}</td>
            <td><span class="transaction-type">{{ ucfirst(str_replace('_',' ',$b->kategori)) }}</span></td>
            <td>{{ $b->anggaran->nama_anggaran ?: ($b->anggaran->kategori ?? '-') }}</td>
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
                <form method="POST" action="{{ route('keuangan.laporan-saya.destroy', $b->id) }}" style="display:inline" onsubmit="return confirm('Hapus pengeluaran ini?')">
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
function openDetail(id) {
    fetch('/keuangan/laporan-saya/'+id+'/detail')
    .then(r=>r.json()).then(d=>{
        let html='<p><strong>Tanggal:</strong> '+d.tanggal+'</p>';
        html+='<p><strong>Deskripsi:</strong> '+d.deskripsi+'</p>';
        html+='<p><strong>Kategori:</strong> '+d.kategori+'</p>';
        html+='<p><strong>Kegiatan:</strong> '+(d.anggaran_nama||'-')+'</p>';
        html+='<p><strong>Jumlah:</strong> Rp '+number_format(d.jumlah)+'</p>';
        if(d.bukti_foto) html+='<p><strong>Bukti Utama:</strong> <a href="'+d.bukti_foto+'" target="_blank">📎 Lihat</a></p>';
        if(d.buktis.length){
            html+='<p><strong>Bukti Tambahan ('+d.buktis.length+'):</strong></p><ul style="font-size:12px">';
            d.buktis.forEach(b=>{html+='<li><a href="'+b.url+'" target="_blank">'+b.name+' ('+b.tipe+')</a></li>'});
            html+='</ul>';
        }
        document.getElementById('detailContent').innerHTML=html;
        document.getElementById('detailModal').style.display='flex';
    });
}
function openEdit(id) {
    fetch('/keuangan/laporan-saya/'+id+'/detail')
    .then(r=>r.json()).then(d=>{
        let html='<form method="POST" action="/keuangan/laporan-saya/'+d.id+'" enctype="multipart/form-data">@csrf @method("PUT")';
        html+='<div style="margin-bottom:10px"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-input" value="'+d.tanggal+'" required></div>';
        html+='<div style="margin-bottom:10px"><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="2" class="form-input" required>'+d.deskripsi+'</textarea></div>';
        html+='<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px"><div><label class="form-label">Kategori</label><select name="kategori" class="form-input">';
        ['barang_bantuan','transportasi','konsumsi','hotel','sewa','atk','cadangan','lainnya'].forEach(k=>{
            html+='<option value="'+k+'" '+(d.kategori===k?'selected':'')+'>'+k+'</option>';
        });
        html+='</select></div><div><label class="form-label">Jumlah (Rp)</label><input type="number" step="0.01" min="1" name="jumlah" class="form-input" value="'+d.jumlah+'" required></div></div>';
        html+='<div style="margin-bottom:10px"><label class="form-label">Kegiatan Terkait</label><select name="anggaran_id" class="form-input"><option value="">— Tidak terkait —</option></select></div>';
        html+='<div style="margin-bottom:10px"><label class="form-label">Bukti Tambahan</label><input type="file" name="bukti_files[]" class="form-input" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple></div>';
        html+='<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px"><button type="button" onclick="closeModal(\'editModal\')" class="btn btn-outline">Batal</button><button type="submit" class="btn btn-primary">💾 Update</button></div></form>';
        document.getElementById('editContent').innerHTML=html;
        document.getElementById('editModal').style.display='flex';
    });
}
function closeModal(id) { document.getElementById(id).style.display='none'; }
function number_format(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
document.addEventListener('click',function(e){if(e.target.id==='detailModal'||e.target.id==='editModal')e.target.style.display='none'});
</script>
@endsection
