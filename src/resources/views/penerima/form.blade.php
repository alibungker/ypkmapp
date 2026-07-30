@extends('layouts.app')
@section('title', 'Tambah Penerima')
@section('content')
<div class="card" style="max-width:700px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
        <h3 style="font-size:15px;font-weight:600;">📝 Tambah Penerima Baru</h3>
    </div>
    <div style="padding:20px;">
        <form method="POST" action="{{ route('penerima.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label class="form-label">NIK <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="nik" class="form-input" required value="{{ old('nik') }}" placeholder="16 digit NIK">
                    @error('nik')<small style="color:#dc2626;">{{ $message }}</small>@enderror
                </div>
                <div>
                    <label class="form-label">No. KK</label>
                    <input type="text" name="no_kk" class="form-input" value="{{ old('no_kk') }}">
                </div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Nama Lengkap <span style="color:#dc2626;">*</span></label>
                <input type="text" name="nama" class="form-input" required value="{{ old('nama') }}" placeholder="Nama sesuai KTP">
                @error('nama')<small style="color:#dc2626;">{{ $message }}</small>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-input" value="{{ old('tempat_lahir') }}">
                </div>
                <div>
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-input" value="{{ old('tanggal_lahir') }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-input">
                        <option value="">Pilih</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">No. HP <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="phone" class="form-input" required value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Alamat Lengkap <span style="color:#dc2626;">*</span></label>
                <input type="text" name="alamat" class="form-input" required value="{{ old('alamat') }}" placeholder="Jl. ... No. ...">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Kabupaten <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="kabupaten" class="form-input" required value="{{ old('kabupaten') }}" placeholder="Aceh Tamiang">
                </div>
                <div>
                    <label class="form-label">Kecamatan <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="kecamatan" class="form-input" required value="{{ old('kecamatan') }}">
                </div>
                <div>
                    <label class="form-label">Desa <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="desa" class="form-input" required value="{{ old('desa') }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div>
                    <label class="form-label">Jumlah Keluarga</label>
                    <input type="number" name="jumlah_keluarga" class="form-input" value="{{ old('jumlah_keluarga', 1) }}">
                </div>
                <div>
                    <label class="form-label">Sumber Data <span style="color:#dc2626;">*</span></label>
                    <select name="sumber_data" class="form-input" required>
                        <option value="relawan">Relawan</option>
                        <option value="mandiri">Mandiri</option>
                        <option value="ketua_kelompok">Ketua Kelompok</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:16px;">
                <label class="form-label">Kelompok <span style="color:#dc2626;">*</span></label>
                <select name="kelompok_id" class="form-input" required>
                    <option value="">Pilih Kelompok</option>
                    @foreach($kelompoks as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->daerah }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                <a href="{{ route('penerima.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
