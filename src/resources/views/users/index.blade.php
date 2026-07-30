@extends('layouts.app')
@section('title', 'Manajemen User')
@section('subtitle', 'Kelola akun ketua kelompok & relawan dengan kunci wilayah kerja')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ session('error') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

<div style="display:grid;grid-template-columns:360px 1fr;gap:20px;align-items:start;">
    {{-- Form Tambah User --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">➕ Tambah User</h3>
        </div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <label class="form-label">Nama Lengkap <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" class="form-input" required value="{{ old('name') }}">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <label class="form-label">Email <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" class="form-input" required value="{{ old('email') }}">
                    </div>
                    <div>
                        <label class="form-label">Password <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="password" class="form-input" required placeholder="min 6 karakter">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <label class="form-label">Role <span style="color:#dc2626;">*</span></label>
                        <select name="role" id="f_role" class="form-input" required>
                            <option value="ketua_kelompok">👤 Ketua Kelompok</option>
                            <option value="relawan">🤝 Relawan</option>
                            <option value="admin">👑 Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">No. HP</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone') }}">
                    </div>
                </div>

                <div id="wilayahBox" style="background:#f8f9fa;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:14px;">
                    <div style="font-size:13px;font-weight:600;margin-bottom:4px;">🔒 Kunci Wilayah Kerja</div>
                    <div style="font-size:12px;color:#6b7280;margin-bottom:10px;">Pilih sampai level mana user dikunci. Kosongkan = semua wilayah.</div>
                    <div style="margin-bottom:10px;">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select name="wilayah_kabupaten" id="u_kab" class="form-input">
                            <option value="">— Tidak dikunci —</option>
                        </select>
                    </div>
                    <div style="margin-bottom:10px;">
                        <label class="form-label">Kecamatan <span style="font-weight:400;color:#9ca3af;">(opsional)</span></label>
                        <select name="wilayah_kecamatan" id="u_kec" class="form-input">
                            <option value="">— Sampai kabupaten saja —</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Desa <span style="font-weight:400;color:#9ca3af;">(opsional)</span></label>
                        <select name="wilayah_desa" id="u_desa" class="form-input">
                            <option value="">— Sampai kecamatan saja —</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">💾 Simpan User</button>
            </form>
        </div>
    </div>

    {{-- Tabel User --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">👥 Daftar User</h3>
        </div>
        <div style="padding:16px 20px;overflow-x:auto;">
            <table class="table-data">
                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Wilayah Kerja</th><th></th></tr></thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td style="font-weight:500;">{{ $u->name }}</td>
                        <td style="color:#6b7280;">{{ $u->email }}</td>
                        <td>
                            @if($u->role == 'admin') <span class="badge badge-navy">👑 Admin</span>
                            @elseif($u->role == 'relawan') <span class="badge badge-green">🤝 Relawan</span>
                            @else <span class="badge badge-gold">👤 Ketua Kelompok</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:#6b7280;">🔒 {{ $u->wilayahLabel() }}</td>
                        <td style="white-space:nowrap;">
                            <button onclick='editUser(@json($u))' style="color:#00034a;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">✏️ Edit</button>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline;" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEdit()">
    <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">✏️ Edit User</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
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
// ===== Cascading wilayah utk form tambah & edit =====
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
        list.forEach(w => {
            const opt = document.createElement('option');
            opt.value = w.nama; opt.dataset.kode = w.kode; opt.textContent = w.nama;
            if (preKec && w.nama === preKec) opt.selected = true;
            kecSel.appendChild(opt);
        });
        if (kecSel.value) loadDesa(kecSel, desaSel, preDesa);
    });
}

function loadDesa(kecSel, desaSel, preDesa) {
    const kode = kecSel.options[kecSel.selectedIndex]?.dataset.kode;
    if (!kode) { desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>'; return; }
    fetch('/api/wilayah/desa/' + kode).then(r => r.json()).then(list => {
        desaSel.innerHTML = '<option value="">— Sampai kecamatan saja —</option>';
        list.forEach(w => {
            const opt = document.createElement('option');
            opt.value = w.nama; opt.textContent = w.nama;
            if (preDesa && w.nama === preDesa) opt.selected = true;
            desaSel.appendChild(opt);
        });
    });
}

// Init form tambah
setupCascade(
    document.getElementById('u_kab'),
    document.getElementById('u_kec'),
    document.getElementById('u_desa'),
    null, null, null
);

// ===== Modal edit =====
function editUser(u) {
    document.getElementById('editForm').action = '/users/' + u.id;
    document.getElementById('e_name').value = u.name;
    document.getElementById('e_email').value = u.email;
    document.getElementById('e_role').value = u.role;
    document.getElementById('e_phone').value = u.phone || '';
    document.getElementById('editModal').style.display = 'flex';
    setupCascade(
        document.getElementById('e_kab'),
        document.getElementById('e_kec'),
        document.getElementById('e_desa'),
        u.wilayah_kabupaten, u.wilayah_kecamatan, u.wilayah_desa
    );
}
function closeEdit() { document.getElementById('editModal').style.display = 'none'; }
</script>
@endsection
