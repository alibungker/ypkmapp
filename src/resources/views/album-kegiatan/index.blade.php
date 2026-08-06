@extends('layouts.app')

@section('title', 'Galeri Kegiatan')
@section('subtitle', 'Album dokumentasi kegiatan YPKM')

@section('styles')
<style>
.album-card{display:flex;flex-direction:column;height:100%;transition:transform .15s,box-shadow .15s}
.album-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,3,74,.12)}
.album-cover{aspect-ratio:16/9;background:#e8e8f0;overflow:hidden;position:relative}
.album-cover img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.album-card:hover .album-cover img{transform:scale(1.03)}
.album-cover .badge{position:absolute;top:10px;left:10px;z-index:2}
.album-body{padding:16px;flex:1;display:flex;flex-direction:column}
.album-title{font-size:15px;font-weight:700;color:var(--navy);margin:0 0 6px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.album-meta{display:flex;gap:12px;font-size:12px;color:var(--muted);margin-bottom:10px;flex-wrap:wrap}
.album-desc{font-size:13px;color:var(--muted);line-height:1.5;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.album-footer{margin-top:auto;padding-top:12px;border-top:1px solid var(--line);display:flex;gap:8px;flex-wrap:wrap}
.album-empty{text-align:center;padding:60px 20px;color:var(--muted)}
.album-empty .icon{font-size:48px;margin-bottom:16px;opacity:.5}
.filter-bar{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:20px}
.filter-bar .form-input{min-width:180px;max-width:280px}
@media (max-width: 767px){
    .filter-bar .form-input{width:100%;max-width:none}
}
</style>
@endsection

@section('content')
<div class="filter-bar">
    <form method="GET" class="filter-grid" style="flex:1;">
        <input type="text" name="q" class="form-input" placeholder="Cari judul, deskripsi..." value="{{ request('q') }}">
        <select name="tahun" class="form-input" style="min-width:140px;">
            <option value="">Semua Tahun</option>
            @foreach($tahunList as $t)
                <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('album-kegiatan.create') }}" class="btn btn-primary" style="margin-left:auto;"><x-icon name="plus" /> Tambah Album</a>
        @endif
    </form>
</div>

@if($albums->isEmpty())
    <div class="album-empty">
        <div class="icon">📷</div>
        <h3 style="margin:0 0 8px;color:var(--navy);font-weight:600;">Belum ada album kegiatan</h3>
        <p style="margin:0 0 16px;">Mulai dokumentasikan kegiatan YPKM dengan membuat album pertama.</p>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('album-kegiatan.create') }}" class="btn btn-primary"><x-icon name="plus" /> Buat Album</a>
        @endif
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($albums as $album)
            <article class="card album-card">
                <div class="album-cover">
                    @if($album->coverUrl())
                        <a href="{{ route('album-kegiatan.show', $album) }}">
                            <img src="{{ $album->coverUrl() }}" alt="{{ $album->title }}" loading="lazy">
                        </a>
                    @else
                        <a href="{{ route('album-kegiatan.show', $album) }}">
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:32px;">📷</div>
                        </a>
                    @endif
                    <span class="badge badge-navy">{{ $album->photos_count ?? $album->photos->count() }} foto</span>
                    @if($album->attachment_path)
                        <span class="badge badge-gold" style="top:10px;left:auto;right:10px;">📎</span>
                    @endif
                </div>
                <div class="album-body">
                    <h3 class="album-title">
                        <a href="{{ route('album-kegiatan.show', $album) }}" style="color:inherit;text-decoration:none;">{{ $album->title }}</a>
                    </h3>
                    <div class="album-meta">
                        <span><x-icon name="calendar" style="width:14px;height:14px;margin-right:4px;" /> {{ is_object($album->event_date) ? $album->event_date->format('d M Y') : date('d M Y', strtotime($album->event_date)) }}</span>
                        @if($album->anggaran)
                            <span><x-icon name="box" style="width:14px;height:14px;margin-right:4px;" /> {{ $album->anggaran->nama_anggaran }}</span>
                        @endif
                        @if($album->distribusi)
                            <span><x-icon name="truck" style="width:14px;height:14px;margin-right:4px;" /> {{ $album->distribusi->nama_kegiatan }}</span>
                        @endif
                    </div>
                    @if($album->description)
                        <p class="album-desc">{{ Str::limit($album->description, 120) }}</p>
                    @endif
                    <div class="album-footer">
                        <a href="{{ route('album-kegiatan.show', $album) }}" class="btn btn-sm btn-outline flex-1" style="text-align:center;min-height:36px;">
                            <x-icon name="eye" style="width:14px;height:14px;" /> Lihat Album
                        </a>
                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('album-kegiatan.edit', $album) }}" class="btn btn-sm btn-outline" style="min-height:36px;">
                                <x-icon name="edit" style="width:14px;height:14px;" /> Edit
                            </a>
                            <form method="POST" action="{{ route('album-kegiatan.destroy', $album) }}" style="display:inline;" onsubmit="return confirm('Hapus album {{ addslashes($album->title) }} beserta semua foto dan audio? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="min-height:36px;background:#fee2e2;color:#b42318;border:1px solid #fecaca;" title="Hapus album" aria-label="Hapus album {{ $album->title }}">
                                    <x-icon name="trash" style="width:14px;height:14px;" /> Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $albums->links() }}
    </div>
@endif
@endsection