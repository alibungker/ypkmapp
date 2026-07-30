@extends('layouts.app')
@section('title', 'Laporan')
@section('subtitle', 'Rekap dinamis distribusi, penerima, dan keuangan dari database')

@section('styles')
<style>
.report-filter{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;align-items:end}
@media print{.sidebar,.topbar,.no-print{display:none!important}.main-content{margin:0!important;padding:0!important}.card{box-shadow:none!important;break-inside:avoid}body{background:#fff!important}}
</style>
@endsection

@section('content')
<div class="card no-print" style="margin-bottom:18px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;"><h3 style="font-size:15px;font-weight:600;">🔎 Filter Laporan</h3></div>
    <form method="GET" style="padding:16px 20px;">
        <div class="report-filter">
            <div><label class="form-label">Mulai</label><input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-input"></div>
            <div><label class="form-label">Selesai</label><input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-input"></div>
            <div><label class="form-label">Status</label><select name="status" class="form-input"><option value="">Semua status</option>@foreach(['direncanakan'=>'Direncanakan','berlangsung'=>'Berlangsung','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'] as $v=>$l)<option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>@endforeach</select></div>
            <div><label class="form-label">Kabupaten/Kota</label><select name="kabupaten" id="r_kab" data-selected="{{ request('kabupaten') }}" class="form-input"><option value="">Semua wilayah</option></select></div>
            <div><label class="form-label">Kecamatan</label><select name="kecamatan" id="r_kec" data-selected="{{ request('kecamatan') }}" class="form-input"><option value="">Semua kecamatan</option></select></div>
            <div><label class="form-label">Desa</label><select name="desa" id="r_desa" data-selected="{{ request('desa') }}" class="form-input"><option value="">Semua desa</option></select></div>
            <div><label class="form-label">Kelompok</label><select name="kelompok_id" class="form-input"><option value="">Semua kelompok</option>@foreach($kelompoks as $k)<option value="{{ $k->id }}" {{ (string)request('kelompok_id')===(string)$k->id?'selected':'' }}>{{ $k->nama }}</option>@endforeach</select></div>
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px;flex-wrap:wrap;">
            <a href="{{ route('laporan.index') }}" class="btn btn-outline">↩️ Reset</a>
            <button class="btn btn-primary">🔍 Terapkan</button>
            <a href="{{ route('laporan.export-csv', request()->query()) }}" class="btn btn-outline">📊 Export CSV</a>
            <button type="button" onclick="window.print()" class="btn btn-outline">🖨️ Cetak/PDF</button>
        </div>
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:18px;">
    <div class="stat-card"><div class="stat-value" style="font-size:24px;color:#017723;">Rp {{ number_format($totals['dana_masuk'],0,',','.') }}</div><div class="stat-label">Dana Masuk Periode</div></div>
    <div class="stat-card"><div class="stat-value" style="font-size:24px;">Rp {{ number_format($totals['nilai_bantuan'],0,',','.') }}</div><div class="stat-label">Nilai Bantuan</div></div>
    <div class="stat-card"><div class="stat-value" style="font-size:24px;color:#e5a820;">Rp {{ number_format($totals['biaya_operasional'],0,',','.') }}</div><div class="stat-label">Operasional Distribusi</div></div>
    <div class="stat-card"><div class="stat-value" style="font-size:24px;color:{{ $totals['sisa_dana'] < 0 ? '#dc2626' : '#017723' }};">Rp {{ number_format($totals['sisa_dana'],0,',','.') }}</div><div class="stat-label">Saldo Terhitung</div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:18px;">
    <div class="stat-card"><div class="stat-value">{{ number_format($distribusi->count()) }}</div><div class="stat-label">Kegiatan Distribusi</div></div>
    <div class="stat-card"><div class="stat-value">{{ number_format($totals['paket']) }}</div><div class="stat-label">Paket</div></div>
    <div class="stat-card"><div class="stat-value">{{ number_format($totals['penerima']) }}</div><div class="stat-label">Target Penerima</div></div>
    <div class="stat-card"><div class="stat-value">{{ number_format($totals['terverifikasi']) }}</div><div class="stat-label">Terverifikasi</div></div>
    <div class="stat-card"><div class="stat-value">{{ number_format($totals['menerima']) }}</div><div class="stat-label">Tanda Terima</div></div>
</div>

<div class="card" style="margin-bottom:18px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;"><h3 style="font-size:15px;font-weight:600;">📍 Ringkasan per Kabupaten/Kota</h3></div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data"><thead><tr><th>Wilayah</th><th>Kegiatan</th><th>Paket</th><th>Nilai</th><th>Target</th><th>Terverifikasi</th><th>Tanda Terima</th></tr></thead><tbody>
        @forelse($perDaerah as $row)
        <tr><td style="font-weight:600;">{{ $row['daerah'] }}</td><td>{{ number_format($row['kegiatan']) }}</td><td>{{ number_format($row['paket']) }}</td><td>Rp {{ number_format($row['nilai'],0,',','.') }}</td><td>{{ number_format($row['penerima']) }}</td><td>{{ number_format($row['terverifikasi']) }}</td><td>{{ number_format($row['menerima']) }}</td></tr>
        @empty<tr><td colspan="7" style="text-align:center;padding:28px;color:#9ca3af;">Tidak ada data sesuai filter.</td></tr>@endforelse
        </tbody></table>
    </div>
</div>

<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;"><h3 style="font-size:15px;font-weight:600;">📦 Rincian Distribusi</h3></div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data"><thead><tr><th>Tanggal/Kode</th><th>Kegiatan/Wilayah</th><th>Kelompok</th><th>Status</th><th>Paket</th><th>Nilai</th><th>Target</th><th>Terverifikasi</th><th>Tanda Terima</th></tr></thead><tbody>
        @forelse($distribusi as $d)
        <tr>
            <td>{{ optional($d->tanggal)->format('d/m/Y') ?? $d->tanggal }}<br><small style="color:#6b7280;">{{ $d->kode_distribusi }}</small></td>
            <td><a href="{{ route('distribusi.show',$d) }}" style="font-weight:600;color:#00034a;text-decoration:none;">{{ $d->nama_kegiatan }}</a><br><small style="color:#6b7280;">{{ optional($d->kelompok)->daerah }} · {{ optional($d->kelompok)->kecamatan }} · {{ optional($d->kelompok)->desa }}</small></td>
            <td>{{ optional($d->kelompok)->nama ?? '-' }}</td>
            <td>{{ ucfirst($d->status) }}</td>
            <td>{{ number_format($d->jumlah_paket) }}</td>
            <td>Rp {{ number_format($d->estimasi_nilai_total,0,',','.') }}</td>
            <td>{{ number_format(optional($d->kelompok)->penerima_count ?? 0) }}</td>
            <td>{{ number_format(optional($d->kelompok)->penerima_terverifikasi_count ?? 0) }}</td>
            <td>{{ number_format($d->tanda_terima_count ?: (optional($d->kelompok)->penerima_menerima_count ?? 0)) }}</td>
        </tr>
        @empty<tr><td colspan="9" style="text-align:center;padding:28px;color:#9ca3af;">Tidak ada distribusi sesuai filter.</td></tr>@endforelse
        </tbody></table>
    </div>
</div>
@endsection

@section('scripts')
<script>
const rKab=document.getElementById('r_kab'),rKec=document.getElementById('r_kec'),rDesa=document.getElementById('r_desa');
function fill(select,rows,selected,placeholder,kab=false){select.innerHTML=`<option value="">${placeholder}</option>`;rows.forEach(x=>{const o=document.createElement('option');o.value=kab?x.nama.replace(/^(Kabupaten|Kota)\s/,''):x.nama;o.dataset.kode=x.kode;o.textContent=x.nama;if(selected&&o.value.toLowerCase()===selected.toLowerCase())o.selected=true;select.appendChild(o);});}
function code(select){return select.options[select.selectedIndex]?.dataset.kode||'';}
async function loadDesa(pre=false){const k=code(rKec);if(!k)return fill(rDesa,[],'','Semua desa');const rows=await fetch(`/api/wilayah/desa/${encodeURIComponent(k)}`).then(r=>r.json());fill(rDesa,rows,pre?rDesa.dataset.selected:'','Semua desa');}
async function loadKec(pre=false){const k=code(rKab);if(!k){fill(rKec,[],'','Semua kecamatan');fill(rDesa,[],'','Semua desa');return;}const rows=await fetch(`/api/wilayah/kecamatan/${encodeURIComponent(k)}`).then(r=>r.json());fill(rKec,rows,pre?rKec.dataset.selected:'','Semua kecamatan');if(rKec.value)await loadDesa(pre);}
fetch('/api/wilayah/kabupaten').then(r=>r.json()).then(async rows=>{fill(rKab,rows,rKab.dataset.selected,'Semua wilayah',true);if(rKab.value)await loadKec(true);});
rKab.addEventListener('change',()=>loadKec(false));rKec.addEventListener('change',()=>loadDesa(false));
</script>
@endsection
