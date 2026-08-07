@extends('layouts.app')

@php($editing = isset($albumKegiatan) && $albumKegiatan->exists)
@section('title', $editing ? 'Edit Album Kegiatan' : 'Tambah Album Kegiatan')
@section('subtitle', 'Kelola dokumentasi foto dan lampiran kegiatan')

@section('styles')
<style>
:root{--navy:#00034a;--gold:#d6b665;--gold-deep:#b07d14;--green:#017723;--red:#b42318;--ink:#111827;--muted:#6b7280;--line:#e5e7eb;--bg:#f4f6fb}

/* === Media Album — dropzone & preview === */
.af-page{max-width:960px;margin:0 auto}
.album-form-grid{display:grid;grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:22px;align-items:start}
@media(max-width:900px){.album-form-grid{grid-template-columns:1fr}}

.form-section{padding:22px}
.form-section h2{font-size:16px;font-weight:700;color:var(--navy);margin:0 0 18px;padding-bottom:12px;border-bottom:2px solid #f0f1f5}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.04em;margin-bottom:7px}
.form-input{width:100%;border:1.5px solid var(--line);border-radius:10px;padding:11px 14px;font-size:14px;color:var(--ink);background:#fff;transition:.15s;font-family:inherit}
.form-input:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(214,182,101,.15)}
.form-help{font-size:12px;color:var(--muted);margin-top:5px;line-height:1.45}

/* === Photo Dropzone === */
.photo-drop{
  border:2px dashed #cbd5e1;
  border-radius:14px;
  padding:32px 18px;
  text-align:center;
  background:#f8f9fc;
  cursor:pointer;
  transition:.2s;
  display:block;
  width:100%;
  box-sizing:border-box;
}
.photo-drop:hover{
  border-color:var(--gold);
  background:#fff8e8;
  transform:translateY(-2px);
}
.photo-drop.dragover{
  border-color:var(--gold);
  background:#fff7e6;
  box-shadow:0 0 0 4px rgba(214,182,101,.25);
}
.photo-drop input{position:absolute;width:1px;height:1px;opacity:0}
.photo-icon{font-size:36px;margin-bottom:10px}
.photo-drop strong{display:block;font-size:14px;font-weight:700;color:var(--navy);margin-bottom:6px}

/* === Preview Grid === */
.preview-grid,.existing-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:14px}
@media(max-width:520px){.preview-grid,.existing-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
.preview-item,.existing-item{aspect-ratio:1;position:relative;border-radius:10px;overflow:hidden;background:#e8e8f0;border:1px solid var(--line);padding:4px;box-sizing:border-box}
.preview-item img,.existing-item img{width:100%;height:100%;object-fit:cover;border-radius:6px}
.preview-item span,.existing-item span{position:absolute;bottom:0;left:0;right:0;background:rgba(0,3,74,.78);color:#fff;font-size:9px;padding:4px 5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* === Document Upload (custom dropzone) === */
.doc-drop{
  border:2px dashed #cbd5e1;
  border-radius:10px;
  padding:22px;
  text-align:center;
  background:#f8f9fc;
  cursor:pointer;
  transition:.15s;
  display:block;
  width:100%;
  box-sizing:border-box;
}
.doc-drop:hover{border-color:var(--gold);background:#fff8e8}
.doc-drop input{position:absolute;width:1px;height:1px;opacity:0}
.doc-icon{font-size:28px;margin-bottom:8px}
.doc-drop strong{font-size:13px;color:var(--navy);font-weight:600}
.doc-help{font-size:11.5px;color:var(--muted);margin-top:4px}
.doc-selected{display:flex;align-items:center;gap:8px;margin-top:10px;padding:8px 12px;background:#f0f4ff;border-radius:8px;font-size:12px;color:var(--navy);word-break:break-all}
.doc-selected .remove-cue{cursor:pointer;color:var(--red);font-weight:700;font-size:14px}

/* === Existing Photos === */
.existing-item{aspect-ratio:1;position:relative;border-radius:10px;overflow:hidden;background:#e8e8f0;border:1px solid var(--line);padding:4px;box-sizing:border-box}
.existing-item img{width:100%;height:100%;object-fit:cover;border-radius:6px}
.existing-actions{position:absolute;left:5px;right:5px;bottom:5px;display:flex;gap:4px}
.existing-actions button{flex:1;border:0;border-radius:5px;padding:5px;font-size:10px;font-weight:700;cursor:pointer;transition:.15s}
.existing-actions .coverbtn{background:var(--gold);color:var(--navy)}
.existing-actions .delbtn{background:#b42318;color:#fff}
.current-cover{outline:3px solid var(--gold);outline-offset:2px;border-radius:10px}
.cover-label{position:absolute;top:5px;left:5px;background:var(--gold);color:var(--navy);font-size:10px;font-weight:700;padding:3px 6px;border-radius:4px}

/* === Actions === */
.form-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:20px;flex-wrap:wrap}
@media(max-width:520px){.form-actions .btn{flex:1}}
.btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;padding:11px 18px;text-decoration:none;transition:.15s}
.btn-primary{background:var(--navy);color:#fff}.btn-primary:hover{background:#0a1859}
.btn-outline{background:transparent;color:var(--navy);border:1.5px solid var(--line)}.btn-outline:hover{background:#f4f6fb}
.btn-success{background:var(--green);color:#fff}.btn-success:hover{background:#015e1a}
.error-list{background:#fff1f0;border:1px solid #f5b7b1;color:#9b1c13;padding:14px 18px;border-radius:9px;margin-bottom:18px;font-size:13px}
.error-list ul{margin:5px 0 0;padding-left:18px}

@media(max-width:767px){.form-section{padding:16px}}
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
        <div class="form-section">
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

        <aside class="form-section">
            <h2>Media Album</h2>

            <div class="form-group">
                <label class="form-label">{{ $editing ? 'Tambah Foto' : 'Foto Kegiatan' }}</label>
                <label class="photo-drop" id="photoDrop" for="photos">
                    <input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple>
                    <div class="photo-icon">📷</div>
                    <strong>Pilih atau jatuhkan foto</strong>
                    <div class="form-help">JPG, PNG, WEBP · maks. 5 MB/foto · maks. 50 foto</div>
                </label>
                <div id="photoPreview" class="preview-grid" hidden></div>
            </div>

            <div class="form-group">
                <label for="attachment_file" class="form-label">Lampiran Dokumen</label>
                <label class="doc-drop" id="docDrop" for="attachment_file">
                    <input id="attachment_file" type="file" name="attachment_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.txt,.csv,.rtf,.zip,.rar,.7z">
                    <div class="doc-icon">📎</div>
                    <strong>Pilih lampiran dokumen</strong>
                    <div class="doc-help">PDF, Word, Excel, PowerPoint, TXT, ZIP, RAR · maks. 20 MB · 1 file</div>
                </label>
                <div id="docSelected" class="doc-selected" hidden>
                    <span id="docName"></span>
                    <span class="remove-cue" id="docRemove" title="Hapus berkas">×</span>
                </div>
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
        <div class="form-section" style="margin-top:22px;">
            <h2 style="margin-bottom:6px;">Foto Tersimpan <span style="font-weight:400;color:var(--muted);font-size:12px;">({{ $albumKegiatan->photos->count() }} foto)</span></h2>
            <p class="form-help">Pilih "Jadikan Sampul" untuk mengubah foto utama album. Foto yang dihapus tidak dapat dipulihkan.</p>
            <div class="existing-grid">
                @foreach($albumKegiatan->photos as $photo)
                    <div class="existing-item {{ $albumKegiatan->cover_photo_id == $photo->id ? 'current-cover' : '' }}">
                        <img src="{{ $photo->url() }}" alt="{{ $photo->original_name }}" loading="lazy">
                        @if($albumKegiatan->cover_photo_id == $photo->id)<span class="cover-label">Sampul</span>@endif
                        <div class="existing-actions">
                            @if($albumKegiatan->cover_photo_id != $photo->id)
                                <button type="submit" form="cover-{{ $photo->id }}" class="coverbtn">Sampul</button>
                            @endif
                            <button type="submit" form="delete-photo-{{ $photo->id }}" class="delbtn" onclick="return confirm('Hapus foto ini?')">Hapus</button>
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
    // === Photo Upload ===
    const input = document.getElementById('photos');
    const preview = document.getElementById('photoPreview');
    const drop = document.getElementById('photoDrop');
    const MAX_PHOTOS = 50;
    if (!input || !preview || !drop) return;

    function render(files) {
        preview.innerHTML = '';
        const images = Array.from(files).filter(file => file.type.startsWith('image/')).slice(0, MAX_PHOTOS);
        preview.hidden = images.length === 0;
        if (images.length > MAX_PHOTOS) {
            alert(`Hanya ${MAX_PHOTOS} foto pertama yang ditampilkan.`);
        }
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

    // === Document Upload ===
    const docInput = document.getElementById('attachment_file');
    const docDrop = document.getElementById('docDrop');
    const docSelected = document.getElementById('docSelected');
    const docName = document.getElementById('docName');
    const docRemove = document.getElementById('docRemove');

    if (docInput && docDrop && docSelected) {
        docInput.addEventListener('change', () => {
            if (docInput.files.length) {
                const file = docInput.files[0];
                docName.textContent = file.name + ' (' + (file.size/1024/1024).toFixed(1) + ' MB)';
                docSelected.hidden = false;
            } else {
                docSelected.hidden = true;
                docName.textContent = '';
            }
        });
        docRemove?.addEventListener('click', (e) => {
            e.preventDefault();
            docInput.value = '';
            docSelected.hidden = true;
            docName.textContent = '';
        });
    }
})();
</script>
@endsection
