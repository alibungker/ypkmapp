@extends('layouts.app')

@section('title', $albumKegiatan->title)
@section('subtitle', 'Galeri Kegiatan YPKM')

@section('styles')
<style>
.album-hero{background:linear-gradient(135deg,var(--navy),#171b63);color:white;border-radius:14px;padding:28px;margin-bottom:24px;position:relative;overflow:hidden}
.album-hero:after{content:'';position:absolute;right:-50px;top:-80px;width:220px;height:220px;border-radius:50%;background:rgba(229,168,32,.14)}
.album-hero h1{font-size:clamp(24px,4vw,36px);font-weight:800;margin:0 0 12px;line-height:1.2;position:relative;z-index:1}
.album-hero-meta{display:flex;gap:16px;flex-wrap:wrap;color:rgba(255,255,255,.8);font-size:13px;position:relative;z-index:1}
.album-description{background:white;border:1px solid var(--line);border-left:4px solid var(--gold);border-radius:10px;padding:18px 20px;margin-bottom:24px;line-height:1.7;color:#333}
.attachment-card{background:#f0f4ff;border:1px solid #cdd7f5;border-radius:10px;padding:14px 16px;margin-bottom:24px;display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.attachment-card__info{flex:1;min-width:180px}.attachment-card__name{display:block;color:var(--navy);font-size:13px;font-weight:700;word-break:break-word}.attachment-card__meta{font-size:12px;color:var(--muted);margin-top:3px}
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
.gallery-item{position:relative;aspect-ratio:4/3;border-radius:12px;overflow:hidden;background:#e8e8f0;border:1px solid var(--line);cursor:pointer}
.gallery-item img{width:100%;height:100%;object-fit:cover;transition:transform .25s}
.gallery-item:hover img{transform:scale(1.04)}
.gallery-item:focus-visible{outline:3px solid var(--gold);outline-offset:3px}
.gallery-count{font-size:13px;color:var(--muted);margin-bottom:14px}
.lightbox{position:fixed;inset:0;background:rgba(0,3,30,.95);z-index:100;display:none;align-items:center;justify-content:center;padding:56px 72px}
.lightbox.open{display:flex}
.lightbox img{max-width:100%;max-height:calc(100vh - 112px);object-fit:contain;border-radius:8px}
.lightbox-button{position:absolute;width:48px;height:48px;border:0;border-radius:50%;background:rgba(255,255,255,.13);color:white;font-size:26px;cursor:pointer;display:flex;align-items:center;justify-content:center}
.lightbox-button:hover{background:rgba(255,255,255,.25)}
.lightbox-close{right:18px;top:18px}.lightbox-prev{left:16px;top:50%;transform:translateY(-50%)}.lightbox-next{right:16px;top:50%;transform:translateY(-50%)}
.lightbox-counter{position:absolute;left:50%;bottom:18px;transform:translateX(-50%);color:white;font-size:13px;background:rgba(0,0,0,.4);padding:6px 12px;border-radius:20px}
.related-links{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;position:relative;z-index:1}
.admin-actions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
@media(max-width:767px){.album-hero{padding:22px 18px}.gallery-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.lightbox{padding:60px 8px}.lightbox-prev{left:8px}.lightbox-next{right:8px}.lightbox-button{width:42px;height:42px}.attachment-card__name{font-size:12px}}
</style>
@endsection

@section('content')
<div class="admin-actions">
    <a href="{{ route('album-kegiatan.index') }}" class="btn btn-outline">← Kembali ke Galeri</a>
    @if(auth()->user()?->isAdmin())
        <a href="{{ route('album-kegiatan.edit', $albumKegiatan) }}" class="btn btn-primary"><x-icon name="edit" /> Edit Album</a>
        <form method="POST" action="{{ route('album-kegiatan.destroy', $albumKegiatan) }}" onsubmit="return confirm('Hapus album beserta seluruh foto dan audio?')">
            @csrf
            @method('DELETE')
            <button class="btn" style="background:#b42318;color:white;" type="submit">Hapus Album</button>
        </form>
    @endif
</div>

<section class="album-hero">
    <h1>{{ $albumKegiatan->title }}</h1>
    <div class="album-hero-meta">
        <span><x-icon name="calendar" style="margin-right:5px;" /> {{ is_object($albumKegiatan->event_date) ? $albumKegiatan->event_date->format('d F Y') : date('d F Y', strtotime($albumKegiatan->event_date)) }}</span>
        <span><x-icon name="gallery" style="margin-right:5px;" /> {{ $albumKegiatan->photos->count() }} foto</span>
        @if($albumKegiatan->creator)<span>Oleh {{ $albumKegiatan->creator->name }}</span>@endif
    </div>
    @if($albumKegiatan->anggaran || $albumKegiatan->distribusi)
        <div class="related-links">
            @if($albumKegiatan->anggaran)
                <span class="badge badge-gold">Kegiatan: {{ $albumKegiatan->anggaran->nama_anggaran }}</span>
            @endif
            @if($albumKegiatan->distribusi)
                <a href="{{ route('distribusi.show', $albumKegiatan->distribusi) }}" class="badge badge-gold" style="text-decoration:none;">Distribusi: {{ $albumKegiatan->distribusi->nama_kegiatan }}</a>
            @endif
        </div>
    @endif
</section>

@if($albumKegiatan->description)
    <div class="album-description">{!! nl2br(e($albumKegiatan->description)) !!}</div>
@endif

@if($albumKegiatan->attachment_path)
    <div class="attachment-card">
        <div style="font-size:26px;">📎</div>
        <div class="attachment-card__info">
            <a href="{{ asset('storage/' . $albumKegiatan->attachment_path) }}" target="_blank" rel="noopener" class="attachment-card__name">
                <x-icon name="download" style="width:14px;height:14px;margin-right:5px;" />{{ $albumKegiatan->attachment_name ?: 'Lampiran dokumen' }}
            </a>
            <span class="attachment-card__meta">
                {{ strtoupper(pathinfo($albumKegiatan->attachment_name ?? '', PATHINFO_EXTENSION)) }}
                @if($albumKegiatan->attachment_size) · {{ number_format($albumKegiatan->attachment_size / 1024, 1, ',', '.') }} KB @endif
            </span>
        </div>
        <a href="{{ asset('storage/' . $albumKegiatan->attachment_path) }}" class="btn btn-sm btn-primary" download>Unduh</a>
    </div>
@endif

<div class="gallery-count">{{ $albumKegiatan->photos->count() }} foto dokumentasi</div>
@if($albumKegiatan->photos->isEmpty())
    <div class="card" style="padding:50px;text-align:center;color:var(--muted);">Belum ada foto dalam album ini.</div>
@else
    <div class="gallery-grid" id="galleryGrid">
        @foreach($albumKegiatan->photos as $index => $photo)
            <button class="gallery-item" type="button" data-index="{{ $index }}" aria-label="Buka foto {{ $index + 1 }}: {{ $photo->original_name }}">
                <img src="{{ $photo->url() }}" alt="{{ $photo->original_name }}" loading="lazy">
            </button>
        @endforeach
    </div>
@endif

<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Tampilan foto" aria-hidden="true">
    <button type="button" class="lightbox-button lightbox-close" id="lightboxClose" aria-label="Tutup">×</button>
    <button type="button" class="lightbox-button lightbox-prev" id="lightboxPrev" aria-label="Foto sebelumnya">‹</button>
    <img id="lightboxImage" src="" alt="">
    <button type="button" class="lightbox-button lightbox-next" id="lightboxNext" aria-label="Foto berikutnya">›</button>
    <div class="lightbox-counter" id="lightboxCounter"></div>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const photos = @json($albumKegiatan->photos->map(fn($photo) => ['url' => $photo->url(), 'alt' => $photo->original_name])->values());
    const lightbox = document.getElementById('lightbox');
    if (!lightbox || !photos.length) return;
    const image = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    const closeButton = document.getElementById('lightboxClose');
    let current = 0;
    let previousFocus = null;

    function show(index) {
        current = (index + photos.length) % photos.length;
        image.src = photos[current].url;
        image.alt = photos[current].alt;
        counter.textContent = `${current + 1} / ${photos.length}`;
    }
    function open(index, trigger) {
        previousFocus = trigger;
        show(index);
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeButton.focus();
    }
    function close() {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        previousFocus?.focus();
    }
    document.querySelectorAll('.gallery-item').forEach(button => button.addEventListener('click', () => open(Number(button.dataset.index), button)));
    closeButton.addEventListener('click', close);
    document.getElementById('lightboxPrev').addEventListener('click', () => show(current - 1));
    document.getElementById('lightboxNext').addEventListener('click', () => show(current + 1));
    lightbox.addEventListener('click', event => { if (event.target === lightbox) close(); });
    document.addEventListener('keydown', event => {
        if (!lightbox.classList.contains('open')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') show(current - 1);
        if (event.key === 'ArrowRight') show(current + 1);
    });
})();
</script>
@endsection
