@extends('layouts.app')
@section('title', 'Manajemen User')
@section('subtitle', 'Kelola akun ketua kelompok & relawan dengan kunci wilayah kerja')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ session('error') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <h3 style="font-size:15px;font-weight:600;">👥 Daftar User</h3>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">➕ Tambah User</a>
    </div>
    <div style="padding:16px 20px;overflow-x:auto;">
        <table class="table-data">
            <thead><tr><th>NIK</th><th>Nama</th><th>Email</th><th>Role</th><th>Kelompok</th><th>Wilayah Kerja</th><th></th></tr></thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td style="font-family:monospace;color:#6b7280;font-size:13px;"><x-masked-nik :value="$u->nik" /></td>
                        <td style="font-weight:500;">
                            <a href="{{ route('users.show', $u) }}" style="color:#00034a;text-decoration:none;">{{ $u->name }}</a>
                        </td>
                        <td style="color:#6b7280;">{{ $u->email }}</td>
                        <td>
                            @if($u->role == 'admin') <span class="badge badge-navy">👑 Admin</span>
                            @elseif($u->role == 'relawan') <span class="badge badge-green">🤝 Relawan</span>
                            @else <span class="badge badge-gold">👤 {{ optional($u->kelompok)->nama ?? 'Ketua' }}</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">{{ optional($u->kelompok)->nama ?? ($u->role == 'ketua_kelompok' ? 'Belum ditentukan' : '-') }}</td>
                        <td style="font-size:13px;color:#6b7280;">🔒 {{ $u->wilayahLabel() }}</td>
                        <td style="white-space:nowrap;">
                            <button type="button"
                                onclick="editUser(this.dataset)"
                                data-id="{{ $u->id }}"
                                data-name="{{ $u->name }}"
                                data-email="{{ $u->email }}"
                                data-role="{{ $u->role }}"
                                data-phone="{{ $u->phone }}"
                                data-foto="{{ $u->foto ? asset('storage/'.$u->foto) : '' }}"
                                data-kelompok-id="{{ $u->kelompok_id }}"
                                data-wilayah-kabupaten="{{ $u->wilayah_kabupaten }}"
                                data-wilayah-kecamatan="{{ $u->wilayah_kecamatan }}"
                                data-wilayah-desa="{{ $u->wilayah_desa }}"
                                style="color:#00034a;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">✏️ Edit</button>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline;" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️</button>
                            </form>
                            @endif
                        </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada user</td></tr>
                        @endforelse
                        </tbody>
                        </table>
                        </div>
                        </div>
                        </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEdit()">
    <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">✏️ Edit User</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div style="display:flex;gap:16px;margin-bottom:12px;align-items:flex-start;">
                <div style="flex:0 0 72px;">
                    <img id="e_foto_preview" src="" alt="Foto" style="width:72px;height:84px;object-fit:cover;border-radius:10px;border:2px solid #e5e7eb;background:#f8f9fa;display:block;">
                </div>
                <div style="flex:1;min-width:0;">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" id="e_foto" class="form-input" accept="image/*" onchange="previewFoto(event)">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Kosongkan untuk mempertahankan foto lama.</div>
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label class="form-label">Nama</label>
                <input id="e_name" name="name" class="form-input" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label class="form-label">Email</label>
                    <input id="e_email" name="email" type="email" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Password Baru</label>
                    <input name="password" class="form-input" placeholder="Kosongkan jika tetap">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label class="form-label">Role</label>
                    <select id="e_role" name="role" class="form-input" required>
                        <option value="ketua_kelompok">👤 Ketua Kelompok</option>
                        <option value="relawan">🤝 Relawan</option>
                        <option value="admin">👑 Admin</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">No. HP</label>
                    <input id="e_phone" name="phone" class="form-input">
                </div>
            </div>
            <div style="margin-bottom:12px;" id="e_kelompok_box">
                <label class="form-label">Kelompok <span style="color:#dc2626;">*</span></label>
                <select name="kelompok_id" id="e_kelompok" class="form-input">
                    <option value="">— Pilih Kelompok —</option>
                    @foreach($kelompoks as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }} — {{ $k->daerah }} ({{ $k->penerima_count }} anggota)</option>
                    @endforeach
                </select>
            </div>
            <div style="background:#f8f9fa;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:14px;">
                <div style="font-size:13px;font-weight:600;margin-bottom:10px;">🔒 Kunci Wilayah Kerja</div>
                <div style="margin-bottom:10px;">
                    <label class="form-label">Kabupaten/Kota</label>
                    <select name="wilayah_kabupaten" id="e_kab" class="form-input">
                        <option value="">— Tidak dikunci —</option>
                    </select>
                </div>
                <div style="margin-bottom:10px;">
                    <label class="form-label">Kecamatan</label>
                    <select name="wilayah_kecamatan" id="e_kec" class="form-input">
                        <option value="">— Sampai kabupaten saja —</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Desa</label>
                    <select name="wilayah_desa" id="e_desa" class="form-input">
                        <option value="">— Sampai kecamatan saja —</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid #e5e7eb;">
                <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ===== Modal edit =====
function editUser(u) {
    document.getElementById('editForm').action = '/users/' + u.id;
    document.getElementById('e_name').value = u.name;
    document.getElementById('e_email').value = u.email;
    document.getElementById('e_role').value = u.role;
    document.getElementById('e_phone').value = u.phone || '';
    document.getElementById('e_kelompok').value = u.kelompokId || '';
    document.getElementById('e_foto').value = '';
    document.getElementById('e_foto_preview').src = u.foto || '';
    // Toggle kelompok field
    const eBox = document.getElementById('e_kelompok_box');
    eBox.style.display = u.role === 'ketua_kelompok' ? 'block' : 'none';
    document.getElementById('editModal').style.display = 'flex';
    setupCascade(
        document.getElementById('e_kab'),
        document.getElementById('e_kec'),
        document.getElementById('e_desa'),
        u.wilayahKabupaten, u.wilayahKecamatan, u.wilayahDesa
    );
}
function previewFoto(ev) {
    const f = ev.target.files[0];
    if (f) {
        const r = new FileReader();
        r.onload = e => { document.getElementById('e_foto_preview').src = e.target.result; };
        r.readAsDataURL(f);
    }
}
function closeEdit() { document.getElementById('editModal').style.display = 'none'; }

// ===== Cascading wilayah untuk modal edit =====
function setupCascade(kabSel, kecSel, desaSel, preKab, preKec, preDesa) {
    fetch('/api/wilayah/kabupaten').then(r => r.json()).then(list => {
        kabSel.innerHTML = '<option value="">— Tidak dikunci —</option>';
        list.forEach(w => {
            const opt = document.createElement('option');
            opt.value = w.nama.replace(/^(Kabupaten|Kota)\s/, '');
            opt.dataset.kode = w.kode;
            opt.textContent = w.nama;
            if (preKab && opt.value === preKab) opt.selected = true;
            kabSel.appendChild(opt);
        });
        if (kabSel.value) loadKec(kabSel, kecSel, desaSel, preKec, preDesa);
    });
    kabSel.onchange = () => { loadKec(kabSel, kecSel, desaSel, null, null); };
    kecSel.onchange = () => { loadDesa(kecSel, desaSel, null); };
}
function loadKec(kabSel, kecSel, desaSel, preKec, preDesa) {
    const kode = kabSel.options[kabSel.selectedIndex]?.dataset.kode;
    desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>';
    if (!kode) { kecSel.innerHTML = '<option value="">— Sampai kabupaten saja —</option>'; return; }
    fetch('/api/wilayah/kecamatan/' + kode).then(r => r.json()).then(list => {
        kecSel.innerHTML = '<option value="">— Sampai kabupaten saja —</option>';
        list.forEach(w => { const opt = document.createElement('option'); opt.value = w.nama; opt.dataset.kode = w.kode; opt.textContent = w.nama; if (preKec && w.nama === preKec) opt.selected = true; kecSel.appendChild(opt); });
        if (kecSel.value) loadDesa(kecSel, desaSel, preDesa);
    });
}
function loadDesa(kecSel, desaSel, preDesa) {
    const kode = kecSel.options[kecSel.selectedIndex]?.dataset.kode;
    if (!kode) { desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>'; return; }
    fetch('/api/wilayah/desa/' + kode).then(r => r.json()).then(list => { desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>'; list.forEach(w => { const opt = document.createElement('option'); opt.value = w.nama; opt.textContent = w.nama; if (preDesa && w.nama === preDesa) opt.selected = true; desaSel.appendChild(opt); }); });
}
</script>
@endsection
