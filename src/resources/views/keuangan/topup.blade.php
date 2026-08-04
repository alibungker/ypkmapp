@extends('layouts.app')
@section('title', 'Top-up Anggaran')
@section('content')
<style>
.topup-table{width:100%;border-collapse:collapse;margin-bottom:16px}.topup-table th,.topup-table td{border-bottom:1px solid #e5e7eb;padding:10px 12px;text-align:left;font-size:13px}.topup-table th{background:#f9fafb;color:#374151;font-weight:600}.badge{display:inline-flex;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700}.bg-amber{background:#fef3c7;color:#92400e}.bg-green{background:#dcfce7;color:#065f46}.bg-red{background:#fee2e2;color:#991b1b}.bg-gray{background:#e5e7eb;color:#374151}.btn{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;border:none;cursor:pointer}.btn-primary{background:#00034a;color:#fff}.btn-success{background:#017723;color:#fff}.btn-danger{background:#dc2626;color:#fff}.btn-outline{border:1px solid #d1d5db;background:#fff;color:#374151}
</style>
<div class="card">
<div style="padding:20px;border-bottom:1px solid #e5e7eb"><h3 style="font-size:18px;font-weight:700;color:#00034a">Top-up Anggaran</h3></div>
<div class="card-body">
<div style="background:#f0f6f2;border:1px solid #cde5d4;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#1f3a2a;line-height:1.6;display:flex;gap:12px;align-items:flex-start;"><span style="font-size:18px">💡</span><div><strong>Alur Top-up Anggaran:</strong> anggaran kegiatan dibuat di menu <strong>Barang &amp; Kegiatan → tab Kegiatan</strong>. Setelah disetujui, nominal top-up ditambahkan ke anggaran kegiatan terpilih dan dicatat sebagai dana masuk. Ajukan top-up hanya untuk kegiatan yang sudah memiliki anggaran.</div></div>
<div class="profile-section" style="background:#fff;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.06)">
<h3 style="font-size:14px;font-weight:700;color:#00034a;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #e5e7eb">📝 Ajukan Top-up Baru</h3>
<form method="POST" action="{{ route('keuangan.topup.store') }}">@csrf
<div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px" class="mobile-stack">
<div><label class="form-label">User Penerima Top-up <span style="color:#dc2626">*</span></label><select name="user_id" class="form-input" required><option value="">— Pilih User —</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('user_id')==$u->id?'selected':'' }}>{{ $u->name }} — {{ $u->email }} ({{ ucfirst(str_replace('_',' ',$u->role)) }})</option>@endforeach</select><small class="form-hint" style="display:block;margin-top:4px;color:#6b7280;">Saldo top-up akan muncul di halaman laporan pribadi user terpilih.</small></div>
<div><label class="form-label">Anggaran Target <span style="color:#dc2626">*</span></label><select name="anggaran_id" class="form-input" required><option value="">— Pilih Anggaran Kegiatan —</option>@foreach($anggarans as $a)<option value="{{ $a->id }}">{{ $a->nama_anggaran ?: $a->kategori }}{{ $a->target_paket ? ' — '.$a->target_paket.' '.($a->satuan?:'paket') : '' }} (Rp {{ number_format($a->anggaran,0,',','.') }})</option>@endforeach</select><small class="form-hint" style="display:block;margin-top:4px;color:#6b7280;">Sumber: menu <strong>Barang &amp; Kegiatan → tab Kegiatan</strong>.</small></div>
<div><label class="form-label">Nominal <span style="color:#dc2626">*</span></label><input type="number" step="0.01" min="1" name="nominal" class="form-input" required placeholder="0"></div>
<div><label class="form-label">Tanggal <span style="color:#dc2626">*</span></label><input type="date" name="tanggal" class="form-input" required value="{{ now()->format('Y-m-d') }}"></div>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:16px" class="mobile-stack">
<div><label class="form-label">Sumber Dana</label><input type="text" name="sumber_dana" class="form-input" placeholder="Contoh: BSI, Dana Mitigasi"></div>
<div><label class="form-label">No. Referensi</label><input type="text" name="nomor_referensi" class="form-input" placeholder="Nomor surat/transfer"></div>
</div>
<div style="margin-top:16px"><label class="form-label">Keterangan</label><textarea name="keterangan" rows="3" class="form-input" placeholder="Alasan top-up"></textarea></div>
<div style="display:flex;justify-content:flex-end;margin-top:20px"><button type="submit" class="btn btn-primary">📤 Ajukan Top-up</button></div>
</form>
</div>

<div class="profile-section" style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.06)">
<h3 style="font-size:14px;font-weight:700;color:#00034a;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #e5e7eb">📋 Riwayat Top-up</h3>
<div class="table-wrap" style="overflow-x:auto">
<table class="topup-table">
<thead><tr><th>Tanggal</th><th>User Penerima</th><th>Anggaran</th><th>Nominal</th><th>Sumber Dana</th><th>Status</th><th>Diajukan Oleh</th><th>Disetujui Oleh</th><th>Aksi</th></tr></thead>
<tbody>
@forelse($topups as $t)
<tr>
<td>{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y H:i') }}</td>
<td><strong>{{ $t->user_id ? (\App\Models\User::find($t->user_id)->name ?? '-') : '-' }}</strong><br><small style="color:#6b7280">{{ $t->user_id ? (\App\Models\User::find($t->user_id)->email ?? '') : '' }}</small></td>
<td>{{ $t->anggaran_id ? (\App\Models\Anggaran::find($t->anggaran_id)->nama_anggaran ?? \App\Models\Anggaran::find($t->anggaran_id)->kategori ?? '-') : 'Umum' }}</td>
<td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
<td>{{ $t->sumber_dana ?: '-' }}</td>
<td><span class="badge {{ $t->status==='disetujui'?'bg-green':($t->status==='diajukan'?'bg-amber':($t->status==='ditolak'?'bg-red':'bg-gray')) }}">{{ $t->status }}</span></td>
<td>{{ \App\Models\User::find($t->diajukan_oleh)->name ?? '-' }}</td>
<td>{{ $t->disetujui_oleh ? \App\Models\User::find($t->disetujui_oleh)->name : '-' }}</td>
<td>
@if($t->status==='diajukan' && auth()->user()->canApproveTopUp())
<form method="POST" action="{{ route('keuangan.topup.approve', $t->id) }}" style="display:inline">@csrf<button class="btn btn-success btn-sm">✅ Setujui</button></form>
<form method="POST" action="{{ route('keuangan.topup.reject', $t->id) }}" style="display:inline;margin-left:4px">@csrf<input type="text" name="alasan_penolakan" placeholder="Alasan" required style="width:120px;padding:4px 8px;border:1px solid #d1d5db;border-radius:4px;font-size:11px"><button class="btn btn-danger btn-sm">❌ Tolak</button></form>
@endif
</td>
</tr>
@empty
<tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:30px">Belum ada pengajuan top-up</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>
</div>
@endsection