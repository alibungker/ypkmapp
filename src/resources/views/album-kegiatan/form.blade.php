@extends('layouts.app')

@php($editing = isset($albumKegiatan) && $albumKegiatan->exists)
@section('title', $editing ? 'Edit Album Kegiatan' : 'Tambah Album Kegiatan')
@section('subtitle', 'Kelola dokumentasi foto dan lampiran kegiatan')

@section('styles')
<style>
.album-form-grid{display:grid;grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:22px;align-items:start}
.form-section{padding:22px}.form-section h2{font-size:16px;font-weight:700;color:var(--navy);margin:0 0 18px;padding-bottom:12px;border-bottom:2px solid #f0f1f5}
.form-group{margin-bottom:16px}.form-help{font-size:12px;color:var(--muted);margin-top:5px;line-height:1.45}
.photo-drop{border:2px dashed #cbd5e1;border-radius:12px;padding:28px 18px;text-align:center;background:#f8f9fc;cursor:pointer;transition:.15s}
.photo-drop:hover,.photo-drop.dragover{border-color:var(--gold);background:#fff8e8}.photo-drop input{position:absolute;width:1px;height:1px;opacity:0}
.preview-grid,.existing-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:14px}
.preview-item,.existing-item{aspect-ratio:1;position:relative;border-radius:9px;overflow:hidden;background:#e8e8f0;border:1px solid var(--line)}
.preview-item img,.existing-item img{width:100%;height:100%;object-fit:cover}
.preview-item span{position:absolute;bottom:0;left:0;right:0;background:rgba(0,3,74,.76);color:white;font-size:10px;padding:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.existing-actions{position:absolute;left:5px;right:5px;bottom:5px;display:flex;gap:4px}.existing-actions button{flex:1;border:0;border-radius:5px;padding:5px;font-size:10px;font-weight:700;cursor:pointer}
.current-cover{outline:3px solid var(--gold);outline-offset:2px}.cover-label{position:absolute;top:5px;left:5px;background:var(--gold);color:var(--navy);font-size:10px;font-weight:700;padding:3px 6px;border-radius:4px}
.form-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:20px;flex-wrap:wrap}
.error-list{background:#fff1f0;border:1px solid #f5b7b1;color:#9b1c13;padding:14px 18px;border-radius:9px;margin-bottom:18px;font-size:13px}.error-list ul{margin:5px 0 0;padding-left:18px}
@media(max-width:900px){.album-form-grid{grid-template-columns:1fr}.preview-grid,.existing-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:520px){.preview-grid,.existing-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.form-section{padding:16px}.form-actions .btn{flex:1}}
</style>
@endsection

@section('content')
@if($errors->any())
    <div class="error-list" role="alert"><strong>Periksa data berikut:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ $editing ? route('album-kegiatan.update', $albumKegiatan) : route('album-kegiatan.store') }}" enctype="multipart/form-data" id="albumForm">
    @csrf
    @if($editing) @method('PUT') @endif
    <div class="album-form-grid">
        <div class="card form-section">
            <h2>Informasi Album</h2>
            <div class="form-group">
                <label for="title" class="form-label">Judul Album <span style="color:#b42318">*</span></label>
                <input id="title" type="text" name="title" class="form-input" maxlength="255" required value="{{ old('title', $albumKegiatan->title ?? '') }}" placeholder="Contoh: Distribusi Bantuan Ramadan 2026">
            </div>
            <div class="form-group">
                <label for="event_date" class="form-label">Tanggal Kegiatan <span style="color:#b42318">*</span></label>
                <input id="event_date" type="date" name="event_date" class="form-input" required value="{{ old('event_date', isset($albumKegiatan) && $albumKegiatan->event_date ? $albumKegiatan->event_date->format('Y-m-d') : date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" class="form-input" rows="5" maxlength="5000" placeholder="Ceritakan kegiatan, lokasi, peserta, dan hasilnya...">{{ old('description', $albumKegiatan->description ?? '') }}</textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="mobile-stack">
                <div class="form-group">
                    <label for="anggaran_id" class="form-label">Tautan Anggaran/Kegiatan</label>
                    <select id="anggaran_id" name="anggaran_id" class="form-input">
                        <option value="">— Tidak ditautkan —</option>
                        @foreach($anggarans as $anggaran)<option value="{{ $anggaran->id }}" {{ old('anggaran_id', $albumKegiatan->anggaran_id ?? '') == $anggaran->id ? 'selected' : '' }}>{{ $anggaran->nama_anggaran }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="distribusi_id" class="form-label">Tautan Distribusi</label>
                    <select id="distribusi_id" name="distribusi_id" class="form-input">
                        <option value="">— Tidak ditautkan —</option>
                        @foreach($distribusis as $distribusi)<option value="{{ $distribusi->id }}" {{ old('distribusi_id', $albumKegiatan->distribusi_id ?? '') == $distribusi->id ? 'selected' : '' }}>{{ $distribusi->nama_kegiatan }} — {{ $distribusi->tanggal }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>

        <aside class="card form-section">
            <h2>Media Album</h2>
            <div class="form-group">
                <label class="form-label">{{ $editing ? 'Tambah Foto' : 'Foto Kegiatan' }}</label>
                <label class="photo-drop" id="photoDrop" for="photos">
                    <input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple>
                    <div style="font-size:32px;margin-bottom:8px;">📷</div>
                    <strong style="color:var(--navy);font-size:13px;">Pilih atau jatuhkan foto</strong>
                    <div class="form-help">JPG, PNG, WEBP · maks. 5 MB/foto · maks. 20 foto</div>
                </label>
                <div id="photoPreview" class="preview-grid" hidden></div>
            </div>
            <div class="form-group">
                <label for="attachment_file" class="form-label">Lampiran Dokumen</label>
                <input id="attachment_file" type="file" name="attachment_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.txt,.csv,.rtf,.zip,.rar,.7z">
                <div class="form-help">PDF, Word, Excel, PowerPoint, TXT, ZIP, RAR · maks. 20 MB</div>
                @if($editing && $albumKegiatan->attachment_path)
                    <div style="margin-top:10px;padding:10px;background:#f0f4ff;border-radius:8px;font-size:12px;">
                        📎 <a href="{{ asset('storage/' . $albumKegiatan->attachment_path) }}" target="_blank" style="color:var(--navy);font-weight:600;">{{ $albumKegiatan->attachment_name }}</a>
                        <label style="display:block;margin-top:8px;"><input type="checkbox" name="hapus_lampiran" value="1"> Hapus lampiran saat disimpan</label>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    @if($editing && $albumKegiatan->photos->isNotEmpty())
        <div class="card form-section" style="margin-top:22px;">
            <h2>Foto Tersimpan <span style="font-weight:400;color:var(--muted);font-size:12px;">({{ $albumKegiatan->photos->count() }} foto)</span></h2>
            <p class="form-help">Pilih “Jadikan Sampul” untuk mengubah foto utama album. Foto dihapus langsung dan tidak dapat dipulihkan.</p>
            <div class="existing-grid">
                @foreach($albumKegiatan->photos as $photo)
                    <div class="existing-item {{ $albumKegiatan->cover_photo_id == $photo->id ? 'current-cover' : '' }}">
                        <img src="{{ $photo->url() }}" alt="{{ $photo->original_name }}" loading="lazy">
                        @if($albumKegiatan->cover_photo_id == $photo->id)<span class="cover-label">Sampul</span>@endif
                        <div class="existing-actions">
                            @if($albumKegiatan->cover_photo_id != $photo->id)
                                <button type="submit" form="cover-{{ $photo->id }}" style="background:var(--gold);color:var(--navy);">Sampul</button>
                            @endif
                            <button type="submit" form="delete-photo-{{ $photo->id }}" onclick="return confirm('Hapus foto ini?')" style="background:#b42318;color:white;">Hapus</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="form-actions">
        <a href="{{ $editing ? route('album-kegiatan.show', $albumKegiatan) : route('album-kegiatan.index') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">{{ $editing ? 'Simpan Perubahan' : 'Simpan Album' }}</button>
    </div>
</form>

@if($editing)
    @foreach($albumKegiatan->photos as $photo)
        <form id="cover-{{ $photo->id }}" method="POST" action="{{ route('album-kegiatan.sampul.set', [$albumKegiatan, $photo]) }}" hidden>@csrf</form>
        <form id="delete-photo-{{ $photo->id }}" method="POST" action="{{ route('album-kegiatan.foto.destroy', [$albumKegiatan, $photo]) }}" hidden>@csrf @method('DELETE')</form>
    @endforeach
@endif
@endsection

@section('scripts')
<script>
(() => {
    const input = document.getElementById('photos');
    const preview = document.getElementById('photoPreview');
    const drop = document.getElementById('photoDrop');
    if (!input || !preview || !drop) return;
    function render(files) {
        preview.innerHTML = '';
        const images = Array.from(files).filter(file => file.type.startsWith('image/')).slice(0, 20);
        preview.hidden = images.length === 0;
        images.forEach(file => {
            const item = document.createElement('div'); item.className = 'preview-item';
            const img = document.createElement('img'); img.alt = file.name; img.src = URL.createObjectURL(file); img.onload = () => URL.revokeObjectURL(img.src);
            const label = document.createElement('span'); label.textContent = file.name;
            item.append(img, label); preview.append(item);
        });
    }
    input.addEventListener('change', () => render(input.files));
    ['dragenter','dragover'].forEach(type => drop.addEventListener(type, event => { event.preventDefault(); drop.classList.add('dragover'); }));
    ['dragleave','drop'].forEach(type => drop.addEventListener(type, event => { event.preventDefault(); drop.classList.remove('dragover'); }));
    drop.addEventListener('drop', event => { input.files = event.dataTransfer.files; render(input.files); });
})();
</script>
@endsection
