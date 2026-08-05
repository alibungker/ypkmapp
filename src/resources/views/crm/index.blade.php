@extends('layouts.app')
@section('title', 'CRM Yayasan')
@section('content')
<style>
.crm-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;align-items:center}.crm-tab{padding:9px 14px;border:1px solid #d1d5db;border-radius:9px;text-decoration:none;color:#374151;font-size:13px;background:#fff}.crm-tab.active{background:#00034a;color:#fff;border-color:#00034a}.crm-badge{display:inline-flex;padding:3px 8px;border-radius:999px;background:#eef7f0;color:#017723;font-size:11px;font-weight:700}.crm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px}@media(max-width:900px){.crm-grid{grid-template-columns:repeat(2,1fr)}}
</style>
<div class="crm-grid mobile-two">
 <div class="stat-card"><div style="font-size:12px;color:#6b7280">Pengurus & Staf</div><div class="stat-value" style="font-size:20px">{{ $stats['pengurus'] }}</div></div>
 <div class="stat-card"><div style="font-size:12px;color:#6b7280">Mitra & Donatur</div><div class="stat-value" style="font-size:20px">{{ $stats['mitra'] }}</div><div style="font-size:11px;color:#017723">Rp {{ number_format($stats['kontribusi'],0,',','.') }}</div></div>
 <div class="stat-card"><div style="font-size:12px;color:#6b7280">Relawan</div><div class="stat-value" style="font-size:20px">{{ $stats['relawan'] }}</div><div style="font-size:11px;color:#017723">{{ number_format($stats['jam_relawan']) }} jam</div></div>
 <div class="stat-card"><div style="font-size:12px;color:#6b7280">Penerima CRM</div><div class="stat-value" style="font-size:20px">{{ $stats['penerima'] }}</div></div>
</div>
<div class="crm-tabs">
 @foreach(['pengurus'=>'Pengurus & Staf','mitra'=>'Mitra & Donatur','relawan'=>'Relawan','penerima'=>'Penerima Bantuan'] as $key=>$label)
 <a href="{{ route('crm.index',['tab'=>$key]) }}" class="crm-tab {{ $tab===$key?'active':'' }}">{{ $label }}</a>
 @endforeach
 @if($tab!=='penerima')<a href="{{ route('crm.create',$tab) }}" class="btn btn-primary" style="margin-left:auto;text-decoration:none">+ Tambah</a>@endif
</div>
<div class="card"><div class="card-body"><div class="table-wrap desktop-table"><table class="table-data">
@if($tab==='pengurus')
<thead><tr><th>Kode Anggota</th><th>Nama Lengkap</th><th>Jabatan</th><th>Bank</th><th>No. Rekening</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@foreach($pengurus as $u)<tr><td><code style="font-size:12px;color:#00034a;font-weight:600;white-space:nowrap">{{ $u->kode_keanggotaan ?? '-' }}</code></td><td><strong>{{ $u->name }}</strong></td><td>{{ $u->jabatan }}</td><td>{{ $u->nama_bank ?: '-' }}</td><td>{{ $u->nomor_rekening ?: '-' }}</td><td>{{ $u->email }}</td><td>{{ $u->phone }}</td><td>{{ $u->role }}</td><td><span class="crm-badge">{{ $u->status_aktif?'Aktif':'Nonaktif' }}</span></td><td><a href="{{ route('crm.edit',[$tab,$u->id]) }}" class="btn btn-sm" style="text-decoration:none">✏️</a> <form method="POST" action="{{ route('crm.destroy',[$tab,$u->id]) }}" style="display:inline" onsubmit="return confirm('Nonaktifkan akun ini?')">@csrf<input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm" style="text-decoration:none;background:none;border:none;color:#dc2626">🗑️</button></form></td></tr>@endforeach
</tbody>
@elseif($tab==='mitra')
<thead><tr><th>Instansi</th><th>Kategori</th><th>PIC</th><th>Kontak</th><th>No. MOU</th><th>Dukungan</th><th>Kontribusi</th><th>Aksi</th></tr></thead><tbody>
@foreach($mitra as $m)<tr><td><strong>{{ $m->nama_instansi }}</strong></td><td>{{ ucwords(str_replace('_',' ',$m->kategori)) }}</td><td>{{ $m->pic_nama }}</td><td>{{ $m->pic_email }}<br>{{ $m->pic_phone }}</td><td>{{ $m->no_mou ?: '-' }}</td><td>{{ ucfirst($m->jenis_dukungan) }}</td><td>Rp {{ number_format($m->total_kontribusi,0,',','.') }}</td><td><a href="{{ route('crm.edit',[$tab,$m->id]) }}" class="btn btn-sm" style="text-decoration:none">✏️</a> <form method="POST" action="{{ route('crm.destroy',[$tab,$m->id]) }}" style="display:inline" onsubmit="return confirm('Hapus data mitra ini?')">@csrf<input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm" style="text-decoration:none;background:none;border:none;color:#dc2626">🗑️</button></form></td></tr>@endforeach
</tbody>
@elseif($tab==='relawan')
<thead><tr><th>Kode Anggota</th><th>Nama</th><th>NIK</th><th>Lahir</th><th>JK</th><th>Kontak</th><th>Keahlian</th><th>Ketersediaan</th><th>Jam</th><th>Domisili</th><th>Aksi</th></tr></thead><tbody>
@foreach($relawan as $r)<tr><td><code style="font-size:12px;color:#00034a;font-weight:600;white-space:nowrap">{{ $r->kode_keanggotaan ?? '-' }}</code></td><td><strong>{{ $r->nama_lengkap }}</strong></td><td>{{ substr($r->nik,0,6) }}******{{ substr($r->nik,-4) }}</td><td>{{ $r->tempat_tanggal_lahir }}</td><td>{{ $r->jenis_kelamin }}</td><td>{{ $r->phone }}<br>{{ $r->email }}</td><td>{{ $r->keahlian_utama }}</td><td>{{ ucwords(str_replace('_',' ',$r->status_ketersediaan)) }}</td><td>{{ number_format($r->jam_kontribusi) }}</td><td>{{ $r->domisili_kota }}</td><td><a href="{{ route('crm.edit',[$tab,$r->id]) }}" class="btn btn-sm" style="text-decoration:none">✏️</a> <form method="POST" action="{{ route('crm.destroy',[$tab,$r->id]) }}" style="display:inline" onsubmit="return confirm('Nonaktifkan relawan ini?')">@csrf<input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm" style="text-decoration:none;background:none;border:none;color:#dc2626">🗑️</button></form></td></tr>@endforeach
</tbody>
@else
<thead><tr><th>Kepala Keluarga</th><th>NIK / KK</th><th>Tanggungan</th><th>Alamat</th><th>Kabupaten</th><th>Koordinat</th><th>Kerentanan</th><th>Penghasilan</th><th>Kelayakan</th></tr></thead><tbody>
@foreach($penerima as $p)<tr><td><strong>{{ $p->nama }}</strong></td><td>{{ substr($p->nik,0,6) }}******{{ substr($p->nik,-4) }}<br>{{ substr($p->no_kk,0,6) }}******{{ substr($p->no_kk,-4) }}</td><td>{{ $p->jumlah_keluarga }}</td><td>{{ $p->alamat }}</td><td>{{ $p->kabupaten }}</td><td>{{ $p->titik_koordinat }}</td><td>{{ ucwords(str_replace('_',' ',$p->kategori_kerentanan)) }}</td><td>Rp {{ number_format($p->penghasilan,0,',','.') }}</td><td><span class="crm-badge">{{ ucwords(str_replace('_',' ',$p->status_kelayakan)) }}</span></td></tr>@endforeach
</tbody>
@endif
</table></div>
<div class="mobile-card-list">
@if($tab==='pengurus') @foreach($pengurus as $u)<article class="mobile-data-card"><div class="mobile-data-card__title">{{ $u->name }}</div><div class="mobile-data-card__meta">{{ $u->jabatan }}<br>Bank: {{ $u->nama_bank ?: '-' }} · No. Rek: {{ $u->nomor_rekening ?: '-' }}<br>{{ $u->email }} · {{ $u->phone }} · {{ $u->status_aktif?'Aktif':'Nonaktif' }}</div></article>@endforeach
@elseif($tab==='mitra') @foreach($mitra as $m)<article class="mobile-data-card"><div class="mobile-data-card__title">{{ $m->nama_instansi }}</div><div class="mobile-data-card__meta">{{ ucwords(str_replace('_',' ',$m->kategori)) }} · {{ ucfirst($m->jenis_dukungan) }}<br>{{ $m->pic_nama }} · Rp {{ number_format($m->total_kontribusi,0,',','.') }}</div></article>@endforeach
@elseif($tab==='relawan') @foreach($relawan as $r)<article class="mobile-data-card"><div class="mobile-data-card__title">{{ $r->nama_lengkap }}</div><div class="mobile-data-card__meta">{{ $r->keahlian_utama }} · {{ $r->domisili_kota }}<br>{{ number_format($r->jam_kontribusi) }} jam · {{ ucwords(str_replace('_',' ',$r->status_ketersediaan)) }}</div></article>@endforeach
@else @foreach($penerima as $p)<article class="mobile-data-card"><div class="mobile-data-card__title">{{ $p->nama }}</div><div class="mobile-data-card__meta">{{ $p->kabupaten }} · {{ ucwords(str_replace('_',' ',$p->kategori_kerentanan)) }}<br>{{ ucwords(str_replace('_',' ',$p->status_kelayakan)) }} · {{ $p->jumlah_keluarga }} tanggungan</div></article>@endforeach @endif
</div></div></div>
@endsection