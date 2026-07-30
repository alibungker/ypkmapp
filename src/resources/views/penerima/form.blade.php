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
                    <select name="kabupaten" id="w_kab" class="form-input" required data-selected="{{ old('kabupaten', $penerima->kabupaten ?? '') }}">
                        <option value="">— Pilih —</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Kecamatan <span style="color:#dc2626;">*</span></label>
                    <select name="kecamatan" id="w_kec" class="form-input" required data-selected="{{ old('kecamatan', $penerima->kecamatan ?? '') }}">
                        <option value="">— Pilih kabupaten dulu —</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Desa <span style="color:#dc2626;">*</span></label>
                    <select name="desa" id="w_desa" class="form-input" required data-selected="{{ old('desa', $penerima->desa ?? '') }}">
                        <option value="">— Pilih kecamatan dulu —</option>
                    </select>
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

@section('scripts')
<script>
// Cascading dropdown wilayah terpadu (Kepmendagri)
const wKab = document.getElementById('w_kab');
const wKec = document.getElementById('w_kec');
const wDesa = document.getElementById('w_desa');

function fillSelect(sel, list, selectedName, placeholder) {
    sel.innerHTML = '<option value="">' + placeholder + '</option>';
    list.forEach(w => {
        const opt = document.createElement('option');
        opt.value = w.nama;
        opt.dataset.kode = w.kode;
        opt.textContent = w.nama;
        if (selectedName && w.nama.toLowerCase() === selectedName.toLowerCase()) opt.selected = true;
        sel.appendChild(opt);
    });
}

function selectedKode(sel) {
    const opt = sel.options[sel.selectedIndex];
    return opt ? opt.dataset.kode : null;
}

// Load kabupaten on page load
fetch('/api/wilayah/kabupaten')
    .then(r => r.json())
    .then(list => {
        fillSelect(wKab, list, wKab.dataset.selected, '— Pilih —');
        if (wKab.value) loadKecamatan(true);
    });

function loadKecamatan(usePreselect) {
    const kode = selectedKode(wKab);
    if (!kode) { wKec.innerHTML = '<option value="">— Pilih kabupaten dulu —</option>'; return; }
    fetch('/api/wilayah/kecamatan/' + kode)
        .then(r => r.json())
        .then(list => {
            fillSelect(wKec, list, usePreselect ? wKec.dataset.selected : null, '— Pilih —');
            if (wKec.value) loadDesa(usePreselect);
            else wDesa.innerHTML = '<option value="">— Pilih kecamatan dulu —</option>';
        });
}

function loadDesa(usePreselect) {
    const kode = selectedKode(wKec);
    if (!kode) { wDesa.innerHTML = '<option value="">— Pilih kecamatan dulu —</option>'; return; }
    fetch('/api/wilayah/desa/' + kode)
        .then(r => r.json())
        .then(list => fillSelect(wDesa, list, usePreselect ? wDesa.dataset.selected : null, '— Pilih —'));
}

wKab.addEventListener('change', () => loadKecamatan(false));
wKec.addEventListener('change', () => loadDesa(false));
</script>
@endsection
