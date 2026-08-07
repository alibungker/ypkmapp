<?php

namespace App\Http\Controllers;

use App\Models\AlbumKegiatan;
use App\Models\AlbumPhoto;
use App\Models\Anggaran;
use App\Models\Distribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlbumKegiatanController extends Controller
{
    private const IMAGE_MIMES = 'jpg,jpeg,png,webp';
    private const IMAGE_MAX_KB = 5120;
    private const ATTACHMENT_MIMES = 'pdf,doc,docx,xls,xlsx,ppt,pptx,odt,ods,odp,txt,csv,rtf,zip,rar,7z';
    private const ATTACHMENT_MAX_KB = 20480;
    private const MAX_PHOTOS = 50;

    public function index(Request $request)
    {
        $query = AlbumKegiatan::with(['cover', 'photos', 'anggaran', 'distribusi', 'creator']);

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }
        if ($request->filled('tahun')) {
            $query->whereYear('event_date', (int) $request->input('tahun'));
        }

        $albums = $query->orderByDesc('event_date')->orderByDesc('id')->paginate(12)->withQueryString();

        $tahunList = AlbumKegiatan::selectRaw('YEAR(event_date) as tahun')
            ->groupBy('tahun')->orderByDesc('tahun')->pluck('tahun');

        return view('album-kegiatan.index', compact('albums', 'tahunList'));
    }

    public function show(AlbumKegiatan $albumKegiatan)
    {
        $albumKegiatan->load(['photos', 'cover', 'anggaran', 'distribusi', 'creator']);
        return view('album-kegiatan.show', compact('albumKegiatan'));
    }

    public function create()
    {
        $anggarans = Anggaran::orderByDesc('id')->get();
        $distribusis = Distribusi::orderByDesc('id')->get();
        return view('album-kegiatan.form', compact('anggarans', 'distribusis'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $photos = $request->file('photos', []);
        $attachment = $data['attachment_file'] ?? null;
        unset($data['attachment_file']);
        $data['created_by'] = auth()->id();

        $stored = [];
        $album = null;
        try {
            DB::transaction(function () use ($data, $photos, $attachment, &$stored, &$album) {
                $album = AlbumKegiatan::create($data);
                if ($attachment) {
                    $album->attachment_path = $attachment->store('album-kegiatan/lampiran', 'public');
                    $album->attachment_name = $attachment->getClientOriginalName();
                    $album->attachment_mime = $attachment->getMimeType();
                    $album->attachment_size = $attachment->getSize();
                    $album->save();
                    $stored[] = $album->attachment_path;
                }
                $this->storePhotos($album, $photos, $stored);
                if ($album->photos()->exists() && !$album->cover_photo_id) {
                    $album->cover_photo_id = $album->photos()->value('id');
                    $album->save();
                }
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($stored);
            throw $e;
        }

        return redirect()->route('album-kegiatan.show', $album)
            ->with('success', 'Album kegiatan berhasil disimpan.');
    }

    public function edit(AlbumKegiatan $albumKegiatan)
    {
        $albumKegiatan->load(['photos', 'cover']);
        $anggarans = Anggaran::orderByDesc('id')->get();
        $distribusis = Distribusi::orderByDesc('id')->get();
        return view('album-kegiatan.form', compact('albumKegiatan', 'anggarans', 'distribusis'));
    }

    public function update(Request $request, AlbumKegiatan $albumKegiatan)
    {
        $data = $this->validated($request);
        $photos = $request->file('photos', []);
        $attachment = $data['attachment_file'] ?? null;
        unset($data['attachment_file']);

        $newStored = [];
        try {
            DB::transaction(function () use ($request, $albumKegiatan, &$data, $photos, $attachment, &$newStored) {
                if ($request->boolean('hapus_lampiran') && $albumKegiatan->attachment_path) {
                    Storage::disk('public')->delete($albumKegiatan->attachment_path);
                    $data['attachment_path'] = null;
                    $data['attachment_name'] = null;
                    $data['attachment_mime'] = null;
                    $data['attachment_size'] = null;
                }
                if ($attachment) {
                    if ($albumKegiatan->attachment_path) {
                        Storage::disk('public')->delete($albumKegiatan->attachment_path);
                    }
                    $data['attachment_path'] = $attachment->store('album-kegiatan/lampiran', 'public');
                    $data['attachment_name'] = $attachment->getClientOriginalName();
                    $data['attachment_mime'] = $attachment->getMimeType();
                    $data['attachment_size'] = $attachment->getSize();
                    $newStored[] = $data['attachment_path'];
                }
                $albumKegiatan->update($data);
                $this->storePhotos($albumKegiatan, $photos, $newStored);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newStored);
            throw $e;
        }

        return redirect()->route('album-kegiatan.show', $albumKegiatan)
            ->with('success', 'Album kegiatan diperbarui.');
    }

    public function destroy(AlbumKegiatan $albumKegiatan)
    {
        $paths = $albumKegiatan->photos()->pluck('path')->all();
        if ($albumKegiatan->attachment_path) {
            $paths[] = $albumKegiatan->attachment_path;
        }
        $albumKegiatan->delete();
        Storage::disk('public')->delete(array_values(array_unique(array_filter($paths))));
        return redirect()->route('album-kegiatan.index')->with('success', 'Album kegiatan dihapus.');
    }

    public function destroyPhoto(AlbumKegiatan $albumKegiatan, AlbumPhoto $photo)
    {
        abort_unless((int) $photo->album_kegiatan_id === (int) $albumKegiatan->id, 404);
        $path = $photo->path;
        $photo->delete();
        Storage::disk('public')->delete($path);

        if ((int) $albumKegiatan->cover_photo_id === (int) $photo->id) {
            $albumKegiatan->cover_photo_id = $albumKegiatan->photos()->value('id');
            $albumKegiatan->save();
        }
        return back()->with('success', 'Foto dihapus.');
    }

    public function setCover(AlbumKegiatan $albumKegiatan, AlbumPhoto $photo)
    {
        abort_unless((int) $photo->album_kegiatan_id === (int) $albumKegiatan->id, 404);
        $albumKegiatan->cover_photo_id = $photo->id;
        $albumKegiatan->save();
        return back()->with('success', 'Sampul album diperbarui.');
    }

    private function storePhotos(AlbumKegiatan $album, array $photos, array &$stored): void
    {
        $next = (int) $album->photos()->max('sort_order') + 1;
        foreach ($photos as $photo) {
            $path = $photo->store('album-kegiatan/foto', 'public');
            $stored[] = $path;
            $album->photos()->create([
                'path' => $path,
                'original_name' => $photo->getClientOriginalName(),
                'mime_type' => $photo->getMimeType(),
                'size' => $photo->getSize(),
                'sort_order' => $next++,
            ]);
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'event_date' => 'required|date',
            'anggaran_id' => 'nullable|integer|exists:anggarans,id',
            'distribusi_id' => 'nullable|integer|exists:distribusis,id',
            'photos' => 'nullable|array|max:' . self::MAX_PHOTOS,
            'photos.*' => 'file|mimes:' . self::IMAGE_MIMES . '|max:' . self::IMAGE_MAX_KB,
            'attachment_file' => 'nullable|file|mimes:' . self::ATTACHMENT_MIMES . '|max:' . self::ATTACHMENT_MAX_KB,
            'hapus_lampiran' => 'nullable|boolean',
        ], [
            'photos.max' => 'Maksimal ' . self::MAX_PHOTOS . ' foto dalam sekali unggah.',
            'photos.*.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
            'attachment_file.mimes' => 'Lampiran harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV, ZIP, atau RAR.',
            'attachment_file.max' => 'Ukuran lampiran maksimal 20 MB.',
        ]);
    }
}
