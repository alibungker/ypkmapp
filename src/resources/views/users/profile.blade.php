@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<style>
.profile-card{max-width:640px;margin:0 auto}
.profile-avatar{width:80px;height:80px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:28px;color:#00034a;margin-bottom:12px;border:2px solid #d1d5db}
.profile-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:600px){.profile-grid{grid-template-columns:1fr}}
.profile-section{background:#fff;border-radius:12px;padding:24px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.profile-section h3{font-size:14px;font-weight:700;color:#00034a;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #e5e7eb}
.role-badge{display:inline-flex;padding:4px 10px;border-radius:999px;background:#00034a;color:#fff;font-size:11px;font-weight:700}
</style>
<div class="profile-card">
@if($errors->any())<div style="background:#fef2f2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px">{{ $errors->first() }}</div>@endif
@if(session('success'))<div style="background:#f0fdf4;color:#017723;padding:12px;border-radius:8px;margin-bottom:16px">{{ session('success') }}</div>@endif

<div class="profile-section">
<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">
 <div class="profile-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
 <div>
  <div style="font-size:20px;font-weight:700;color:#00034a">{{ $user->name }}</div>
  <div style="margin-top:4px"><span class="role-badge">{{ $user->role }}</span></div>
  @if($user->jabatan)<div style="margin-top:6px;color:#6b7280;font-size:13px">{{ $user->jabatan }}</div>@endif
  @if($user->kode_keanggotaan)<div style="margin-top:6px;color:#00034a;font-size:12px;font-weight:600">Kode: {{ $user->kode_keanggotaan }}</div>@endif
  <div style="margin-top:6px;color:#6b7280;font-size:12px">{{ $user->wilayahLabel() }}</div>
 </div>
</div>
</div>

<form method="POST" action="{{ route('users.profile.update') }}" enctype="multipart/form-data">
@csrf @method('PUT')
@php
$fields = [
    ['name' => 'name', 'label' => 'Nama Lengkap', 'type' => 'text', 'value' => $user->name, 'required' => true],
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'value' => $user->email, 'required' => true],
    ['name' => 'phone', 'label' => 'Phone', 'type' => 'text', 'value' => $user->phone],
    ['name' => 'nip', 'label' => 'NIP', 'type' => 'text', 'value' => $user->nip],
    ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'type' => 'text', 'value' => $user->tempat_lahir],
    ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'type' => 'date', 'value' => $user->tanggal_lahir ? (is_object($user->tanggal_lahir) ? $user->tanggal_lahir->format('Y-m-d') : date('Y-m-d', strtotime($user->tanggal_lahir))) : ''],
    ['name' => 'password', 'label' => 'Password Baru (kosongkan jika tidak ganti)', 'type' => 'password'],
    ['name' => 'alamat_lengkap', 'label' => 'Alamat Lengkap', 'type' => 'text', 'value' => $user->alamat_lengkap],
];
@endphp

<div class="profile-section"><h3>📋 Edit Profil</h3>
<div class="profile-grid">
@foreach($fields as $f)
<div><label class="form-label">{{ $f['label'] }}</label>
<input type="{{ $f['type'] ?? 'text' }}" name="{{ $f['name'] }}" class="form-input" value="{{ $f['value'] ?? '' }}" {{ ($f['required'] ?? false) ? 'required' : '' }} placeholder="{{ $f['label'] }}" @if($f['name']==='password') autocomplete="new-password" @endif></div>
@endforeach
<div><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-input"><option value="">Pilih</option><option value="L" @selected($user->jenis_kelamin==='L')>Laki-laki</option><option value="P" @selected($user->jenis_kelamin==='P')>Perempuan</option></select></div>
<div><label class="form-label">Foto Profil</label><input type="file" name="foto" accept="image/*" class="form-input"></div>
</div>
<div style="display:flex;justify-content:flex-end;margin-top:20px"><button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button></div>
</div>
</form>

<div class="profile-section"><h3>ℹ️ Info Akun</h3>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;color:#6b7280">
 <div><span style="font-weight:600">ID:</span> {{ $user->id }}</div>
 <div><span style="font-weight:600">Role:</span> {{ $user->role }}</div>
 <div><span style="font-weight:600">Status:</span> {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</div>
 <div><span style="font-weight:600">Terdaftar:</span> {{ $user->created_at->format('d M Y') }}</div>
</div>
</div>
</div>
@endsection