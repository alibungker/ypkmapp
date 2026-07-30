@extends('layouts.app')
@section('title', 'Tambah User')
@section('content')
<div class="card" style="max-width:560px;">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <h3 style="font-size:15px;font-weight:600;">➕ Tambah User Baru</h3>
        <a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
    </div>
    <div style="padding:20px;">
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

            <div style="margin-bottom:12px;">
                <label class="form-label">NIK <span style="font-weight:400;color:#9ca3af;">(KTP)</span></label>
                <input type="text" name="nik" class="form-input" value="{{ old('nik') }}" maxlength="20" placeholder="16 digit NIK (opsional)">
            </div>
            <div style="margin-bottom:12px;">
                <label class="form-label">Nama Lengkap <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" class="form-input" required value="{{ old('name') }}" placeholder="Nama sesuai KTP">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label class="form-label">Email <span style="color:#dc2626;">*</span></label>
                    <input type="email" name="email" class="form-input" required value="{{ old('email') }}" placeholder="user@ypkm.info">
                </div>
                <div>
                    <label class="form-label">Password <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="password" class="form-input" required placeholder="min 6 karakter">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label class="form-label">Role <span style="color:#dc2626;">*</span></label>
                    <select name="role" class="form-input" required>
                        <option value="ketua_kelompok">👤 Ketua Kelompok</option>
                        <option value="relawan">🤝 Relawan</option>
                        <option value="admin">👑 Admin</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">No. HP</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-input">
                        <option value="">— Pilih —</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-input" value="{{ old('tempat_lahir') }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-input" value="{{ old('tanggal_lahir') }}">
                </div>
                <div>
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" class="form-input" accept="image/*">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label class="form-label">Alamat Lengkap</label>
                <input type="text" name="alamat_lengkap" class="form-input" value="{{ old('alamat_lengkap') }}" placeholder="Dusun/Jalan, Desa, Kecamatan, Kabupaten">
            </div>

            {{-- Kunci Wilayah --}}
            <div style="background:#f8f9fa;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:14px;">
                <div style="font-size:13px;font-weight:600;margin-bottom:4px;">🔒 Kunci Wilayah Kerja</div>
                <div style="font-size:12px;color:#6b7280;margin-bottom:10px;">Pilih sampai level mana user dikunci. Kosongkan = semua wilayah.</div>
                <div style="margin-bottom:10px;">
                    <label class="form-label">Kabupaten/Kota</label>
                    <select name="wilayah_kabupaten" id="w_kab" class="form-input"><option value="">— Tidak dikunci —</option></select>
                </div>
                <div style="margin-bottom:10px;">
                    <label class="form-label">Kecamatan</label>
                    <select name="wilayah_kecamatan" id="w_kec" class="form-input"><option value="">— Sampai kabupaten saja —</option></select>
                </div>
                <div>
                    <label class="form-label">Desa</label>
                    <select name="wilayah_desa" id="w_desa" class="form-input"><option value="">— Sampai kecamatan saja —</option></select>
                </div>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid #e5e7eb;">
                <a href="{{ route('users.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">💾 Simpan User</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function setupCascade(kabSel, kecSel, desaSel, preKab, preKec, preDesa) {
    fetch('/api/wilayah/kabupaten').then(r => r.json()).then(list => {
        kabSel.innerHTML = '<option value="">— Tidak dikunci —</option>';
        list.forEach(w => { const opt = document.createElement('option'); opt.value = w.nama.replace(/^(Kabupaten|Kota)\s/, ''); opt.dataset.kode = w.kode; opt.textContent = w.nama; if (preKab && opt.value === preKab) opt.selected = true; kabSel.appendChild(opt); });
        if (kabSel.value) loadKec(kabSel, kecSel, desaSel, preKec, preDesa);
    });
    kabSel.onchange = () => { loadKec(kabSel, kecSel, desaSel, null, null); };
    kecSel.onchange = () => { loadDesa(kecSel, desaSel, null); };
}
function loadKec(kabSel, kecSel, desaSel, preKec, preDesa) {
    const kode = kabSel.options[kabSel.selectedIndex]?.dataset.kode;
    desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>';
    if (!kode) { kecSel.innerHTML = '<option value="">— Sampai kabupaten saja —</option>'; return; }
    fetch('/api/wilayah/kecamatan/' + kode).then(r => r.json()).then(list => { kecSel.innerHTML = '<option value="">— Sampai kabupaten saja —</option>'; list.forEach(w => { const opt = document.createElement('option'); opt.value = w.nama; opt.dataset.kode = w.kode; opt.textContent = w.nama; if (preKec && w.nama === preKec) opt.selected = true; kecSel.appendChild(opt); }); if (kecSel.value) loadDesa(kecSel, desaSel, preDesa); });
}
function loadDesa(kecSel, desaSel, preDesa) {
    const kode = kecSel.options[kecSel.selectedIndex]?.dataset.kode;
    if (!kode) { desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>'; return; }
    fetch('/api/wilayah/desa/' + kode).then(r => r.json()).then(list => { desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>'; list.forEach(w => { const opt = document.createElement('option'); opt.value = w.nama; opt.textContent = w.nama; if (preDesa && w.nama === preDesa) opt.selected = true; desaSel.appendChild(opt); }); });
}
setupCascade(document.getElementById('w_kab'), document.getElementById('w_kec'), document.getElementById('w_desa'), null, null, null);
</script>
@endsection
