@extends('layouts.app')
@section('title', isset($penerima) ? 'Edit Penerima' : 'Tambah Penerima')
@section('content')
<div class="card" style="max-width:700px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">{{ isset($penerima) ? '✏️ Edit Penerima' : '📝 Tambah Penerima Baru' }}</h3>
    </div>
    <div style="padding:20px;">
        <form method="POST" action="{{ isset($penerima) ? route('penerima.update', $penerima) : route('penerima.store') }}">
            @csrf
            @if(isset($penerima)) @method('PUT') @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label class="form-label">NIK <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="nik" class="form-input" required value="{{ old('nik', $penerima->nik ?? '') }}" placeholder="16 digit NIK">
                    @error('nik')<small style="color:#dc2626;">{{ $message }}</small>@enderror
                </div>
                <div>
                    <label class="form-label">No. KK</label>
                    <input type="text" name="no_kk" class="form-input" value="{{ old('no_kk', $penerima->no_kk ?? '') }}">
                </div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Nama Lengkap <span style="color:#dc2626;">*</span></label>
                <input type="text" name="nama" class="form-input" required value="{{ old('nama', $penerima->nama ?? '') }}" placeholder="Nama sesuai KTP">
                @error('nama')<small style="color:#dc2626;">{{ $message }}</small>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-input" value="{{ old('tempat_lahir', $penerima->tempat_lahir ?? '') }}">
                </div>
                <div>
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-input" value="{{ old('tanggal_lahir', isset($penerima) && $penerima->tanggal_lahir ? (is_object($penerima->tanggal_lahir) ? $penerima->tanggal_lahir->format('Y-m-d') : date('Y-m-d', strtotime($penerima->tanggal_lahir))) : '') }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-input">
                        <option value="">Pilih</option>
                        <option value="L" {{ old('jenis_kelamin', $penerima->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $penerima->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-input" value="{{ old('pekerjaan', $penerima->pekerjaan ?? '') }}" placeholder="Petani/Pekebun">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">No. HP <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="phone" class="form-input" required value="{{ old('phone', $penerima->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="form-label">Jumlah Keluarga</label>
                    <input type="number" name="jumlah_keluarga" class="form-input" value="{{ old('jumlah_keluarga', $penerima->jumlah_keluarga ?? 1) }}">
                </div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Alamat Lengkap <span style="color:#dc2626;">*</span></label>
                <input type="text" name="alamat" class="form-input" required value="{{ old('alamat', $penerima->alamat ?? '') }}" placeholder="Jl. ... No. ...">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Kabupaten <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="kabupaten" class="form-input" required value="{{ old('kabupaten', $penerima->kabupaten ?? '') }}" placeholder="Aceh Tamiang">
                </div>
                <div>
                    <label class="form-label">Kecamatan <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="kecamatan" class="form-input" required value="{{ old('kecamatan', $penerima->kecamatan ?? '') }}">
                </div>
                <div>
                    <label class="form-label">Desa <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="desa" class="form-input" required value="{{ old('desa', $penerima->desa ?? '') }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Sumber Data <span style="color:#dc2626;">*</span></label>
                    <select name="sumber_data" class="form-input" required>
                        @foreach(['relawan','mandiri','ketua_kelompok'] as $src)
                        <option value="{{ $src }}" {{ old('sumber_data', $penerima->sumber_data ?? '') == $src ? 'selected' : '' }}>{{ ucfirst($src) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Kelompok <span style="color:#dc2626;">*</span></label>
                    <select name="kelompok_id" class="form-input" required>
                        <option value="">Pilih Kelompok</option>
                        @foreach($kelompoks as $k)
                        <option value="{{ $k->id }}" {{ old('kelompok_id', $penerima->kelompok_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama }} ({{ $k->daerah }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                <a href="{{ route('penerima.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">{{ isset($penerima) ? '💾 Update Data' : '💾 Simpan Data' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
