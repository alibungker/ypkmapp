@extends('layouts.app')
@section('title', 'Data Kelompok')
@section('styles')
<style>
.kelompok-filter{padding:14px 20px;background:#f8fafc;border-bottom:1px solid #e5e7eb;display:grid;grid-template-columns:minmax(180px,2fr) repeat(3,minmax(140px,1fr)) auto auto;gap:10px;align-items:end}
@media(max-width:900px){.kelompok-filter{grid-template-columns:1fr 1fr}}
@media(max-width:560px){.kelompok-filter{grid-template-columns:1fr;padding:12px}}
</style>
@endsection
@section('content')
@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ session('error') }}</div>@endif
@if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
        <h3 style="font-size:15px;font-weight:600;">📋 Data Kelompok</h3>
        @if(auth()->user()->isAdmin())
        <button onclick="document.getElementById('tambahModal').style.display='flex'" class="btn btn-primary btn-sm">➕ Tambah Kelompok</button>
        @endif
    </div>
    <form method="GET" action="{{ route('kelompok.index') }}" class="kelompok-filter">
        <div>
            <label class="form-label">Cari</label>
            <input type="search" name="q" value="{{ request('q') }}" class="form-input" placeholder="Nama, kode, atau ketua">
        </div>
        <div>
            <label class="form-label">Kabupaten/Kota</label>
            <select name="daerah" class="form-input" onchange="this.form.submit()">
                <option value="">Semua daerah</option>
                @foreach($daerahOptions ?? [] as $daerah)
                <option value="{{ $daerah }}" {{ request('daerah') === $daerah ? 'selected' : '' }}>{{ $daerah }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan" class="form-input">
                <option value="">Semua kecamatan</option>
                @foreach($kecamatanOptions ?? [] as $kecamatan)
                <option value="{{ $kecamatan }}" {{ request('kecamatan') === $kecamatan ? 'selected' : '' }}>{{ $kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Anggota</label>
            <select name="status_anggota" class="form-input">
                <option value="">Semua</option>
                <option value="ada" {{ request('status_anggota') === 'ada' ? 'selected' : '' }}>Ada anggota</option>
                <option value="kosong" {{ request('status_anggota') === 'kosong' ? 'selected' : '' }}>Belum ada anggota</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">🔍 Filter</button>
        <a href="{{ route('kelompok.index') }}" class="btn btn-outline" style="text-align:center;">Reset</a>
    </form>
    <div style="padding:10px 20px 0;color:#6b7280;font-size:12px;">Menampilkan <strong>{{ count($kelompoks ?? []) }}</strong> kelompok</div>
    <div style="padding:12px 20px 16px;overflow-x:auto;">
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
                        <td>{{ optional($k->ketuaUser)->name ?? '-' }}</td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('kelompok.show', $k) }}" style="color:#017723;text-decoration:none;font-size:13px;padding:4px 8px;">👁️ Detail</a>
                            @if(auth()->user()->isAdmin())
                            <button onclick="editKelompok({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ addslashes($k->kode) }}', '{{ addslashes($k->daerah) }}', '{{ addslashes($k->kecamatan ?? '') }}', '{{ addslashes($k->desa ?? '') }}', '{{ addslashes($k->description ?? '') }}')" style="color:#00034a;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">✏️ Edit</button>
                            <form method="POST" action="{{ route('kelompok.destroy', $k) }}" style="display:inline;" onsubmit="return confirm('Hapus kelompok ini?')">
                                @csrf @method('DELETE')
                                <button style="color:#dc2626;border:none;background:none;cursor:pointer;font-size:13px;padding:4px 8px;">🗑️</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada kelompok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Modal Tambah --}}
<div id="tambahModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">➕ Tambah Kelompok Baru</h3>
        <form method="POST" action="{{ route('kelompok.store') }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label class="form-label">Nama Kelompok <span style="color:#dc2626;">*</span></label>
                <input type="text" name="nama" class="form-input" required placeholder="Acut_Lancok">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Kode <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="kode" class="form-input" required placeholder="AL-LCK-02">
                </div>
                <div>
                    <label class="form-label">Kabupaten <span style="color:#dc2626;">*</span></label>
                    <select name="daerah" id="mk_kab" class="form-input" required>
                        <option value="">— Pilih —</option>
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                <div>
                    <label class="form-label">Kecamatan</label>
                    <select name="kecamatan" id="mk_kec" class="form-input">
                        <option value="">— Pilih kabupaten dulu —</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Desa</label>
                    <select name="desa" id="mk_desa" class="form-input">
                        <option value="">— Pilih kecamatan dulu —</option>
                    </select>
                </div>
            </div>
            <div style="margin-top:12px;">
                <label class="form-label">Keterangan</label>
                <input type="text" name="description" class="form-input" placeholder="Gampong ..., Batch ...">
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;">
                <button type="button" onclick="document.getElementById('tambahModal').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">💾 Simpan Kelompok</button>
            </div>
        </form>
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
// ===== Cascading dropdown wilayah (form tambah modal) =====
const mkKab = document.getElementById('mk_kab');
const mkKec = document.getElementById('mk_kec');
const mkDesa = document.getElementById('mk_desa');

function fillWilayah(sel, list, placeholder) {
    sel.innerHTML = '<option value="">' + placeholder + '</option>';
    list.forEach(w => {
        const opt = document.createElement('option');
        opt.value = w.nama.replace(/^(Kabupaten|Kota)\s/, '');
        opt.dataset.kode = w.kode;
        opt.textContent = w.nama;
        sel.appendChild(opt);
    });
}

fetch('/api/wilayah/kabupaten').then(r => r.json()).then(list => { fillWilayah(mkKab, list, '— Pilih Kabupaten/Kota —'); });

mkKab.addEventListener('change', function() {
    const kode = this.options[this.selectedIndex]?.dataset.kode;
    mkDesa.innerHTML = '<option value="">— Pilih kecamatan dulu —</option>';
    if (!kode) { mkKec.innerHTML = '<option value="">— Pilih kabupaten dulu —</option>'; return; }
    fetch('/api/wilayah/kecamatan/' + kode).then(r => r.json()).then(list => fillWilayah(mkKec, list, '— Pilih Kecamatan —'));
});

mkKec.addEventListener('change', function() {
    const kode = this.options[this.selectedIndex]?.dataset.kode;
    if (!kode) { mkDesa.innerHTML = '<option value="">— Pilih kecamatan dulu —</option>'; return; }
    fetch('/api/wilayah/desa/' + kode).then(r => r.json()).then(list => fillWilayah(mkDesa, list, '— Pilih Desa —'));
});

// ===== Modal edit =====
function editKelompok(id, nama, kode, daerah, kecamatan, desa, desc) {
    document.getElementById('editForm').action = '/kelompok/' + id;
    document.getElementById('e_nama').value = nama;
    document.getElementById('e_kode').value = kode;
    document.getElementById('e_kecamatan').value = kecamatan;
    document.getElementById('e_desa').value = desa;
    document.getElementById('e_desc').value = desc;
    document.getElementById('e_daerah').value = daerah;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEdit() { document.getElementById('editModal').style.display = 'none'; }
</script>
@endsection
