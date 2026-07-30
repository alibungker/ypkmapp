@extends('layouts.app')
@section('title', 'Data Kelompok')
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ session('error') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

<div style="display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start;">
    {{-- Form Tambah Kelompok --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">➕ Tambah Kelompok</h3>
        </div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('kelompok.store') }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <label class="form-label">Nama Kelompok <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="nama" class="form-input" required value="{{ old('nama') }}" placeholder="Juar - Sekerak">
                </div>
                <div style="margin-bottom:12px;">
                    <label class="form-label">Kode <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="kode" class="form-input" required value="{{ old('kode') }}" placeholder="JR-SKR-02">
                </div>
                <div style="margin-bottom:12px;">
                    <label class="form-label">Kabupaten/Daerah <span style="color:#dc2626;">*</span></label>
                    <select name="daerah" class="form-input" required>
                        <option value="">— Pilih Kabupaten/Kota —</option>
                        @foreach($kabupatens ?? [] as $kode => $nama)
                        <option value="{{ preg_replace('/^(Kabupaten|Kota)\s/', '', $nama) }}" {{ old('daerah') == preg_replace('/^(Kabupaten|Kota)\s/', '', $nama) ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" name="kecamatan" class="form-input" value="{{ old('kecamatan') }}" placeholder="Sekerak">
                </div>
                <div style="margin-bottom:12px;">
                    <label class="form-label">Desa</label>
                    <input type="text" name="desa" class="form-input" value="{{ old('desa') }}" placeholder="Juar">
                </div>
                <div style="margin-bottom:12px;">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="description" class="form-input" value="{{ old('description') }}" placeholder="Gampong ..., Batch ...">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">💾 Simpan Kelompok</button>
            </form>
        </div>
    </div>

    {{-- Tabel Kelompok --}}
    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:15px;font-weight:600;">📋 Data Kelompok</h3>
        </div>
        <div style="padding:16px 20px;overflow-x:auto;">
            <table class="table-data">
                <thead><tr><th>Kode</th><th>Nama</th><th>Daerah</th><th>Kecamatan</th><th>Anggota</th><th>Ketua</th><th></th></tr></thead>
                <tbody>
                    @forelse($kelompoks ?? [] as $k)
                    <tr>
                        <td style="font-family:monospace;color:#6b7280;">{{ $k->kode }}</td>
                        <td style="font-weight:500;">{{ $k->nama }}</td>
                        <td>{{ $k->daerah }}</td>
                        <td style="color:#6b7280;">{{ $k->kecamatan ?? '-' }}</td>
                        <td>👥 {{ number_format($k->penerima_count ?? 0) }}</td>
                        <td>{{ $k->ketua->nama ?? '-' }}</td>
                        <td style="white-space:nowrap;">
                            <button onclick="editKelompok({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ addslashes($k->kode) }}', '{{ addslashes($k->daerah) }}', '{{ addslashes($k->kecamatan ?? '') }}', '{{ addslashes($k->desa ?? '') }}', {{ $k->ketua_id ?? 'null' }}, '{{ addslashes($k->description ?? '') }}')" style="color:#00034a;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">✏️ Edit</button>
                            <form method="POST" action="{{ route('kelompok.destroy', $k) }}" style="display:inline;" onsubmit="return confirm('Hapus kelompok ini?')">
                                @csrf @method('DELETE')
                                <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada kelompok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEdit()">
    <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">✏️ Edit Kelompok</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:12px;">
                <label class="form-label">Nama Kelompok</label>
                <input id="e_nama" name="nama" class="form-input" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Kode</label>
                    <input id="e_kode" name="kode" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Daerah</label>
                    <select id="e_daerah" name="daerah" class="form-input" required>
                        <option value="">— Pilih —</option>
                        @foreach($kabupatens ?? [] as $kode => $nama)
                        <option value="{{ preg_replace('/^(Kabupaten|Kota)\s/', '', $nama) }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                <div>
                    <label class="form-label">Kecamatan</label>
                    <input id="e_kecamatan" name="kecamatan" class="form-input">
                </div>
                <div>
                    <label class="form-label">Desa</label>
                    <input id="e_desa" name="desa" class="form-input">
                </div>
            </div>
            <div style="margin-top:12px;">
                <label class="form-label">Ketua Kelompok</label>
                <select id="e_ketua" name="ketua_id" class="form-input">
                    <option value="">— Pilih dari anggota —</option>
                </select>
            </div>
            <div style="margin-top:12px;">
                <label class="form-label">Keterangan</label>
                <input id="e_desc" name="description" class="form-input">
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                <button type="button" onclick="closeEdit()" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editKelompok(id, nama, kode, daerah, kecamatan, desa, ketuaId, desc) {
    document.getElementById('editForm').action = '/kelompok/' + id;
    document.getElementById('e_nama').value = nama;
    document.getElementById('e_kode').value = kode;
    document.getElementById('e_daerah').value = daerah;
    document.getElementById('e_kecamatan').value = kecamatan;
    document.getElementById('e_desa').value = desa;
    document.getElementById('e_desc').value = desc;
    document.getElementById('editModal').style.display = 'flex';

    // Load anggota untuk pilihan ketua
    const sel = document.getElementById('e_ketua');
    sel.innerHTML = '<option value="">— Memuat anggota... —</option>';
    fetch('/kelompok/' + id + '/anggota')
        .then(r => r.json())
        .then(list => {
            sel.innerHTML = '<option value="">— Pilih dari anggota —</option>';
            list.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.nama + ' (' + p.nik + ')';
                if (ketuaId && p.id == ketuaId) opt.selected = true;
                sel.appendChild(opt);
            });
        })
        .catch(() => { sel.innerHTML = '<option value="">— Gagal memuat —</option>'; });
}
function closeEdit() { document.getElementById('editModal').style.display = 'none'; }
</script>
@endsection
