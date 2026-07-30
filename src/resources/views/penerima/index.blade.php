@extends('layouts.app')
@section('title', 'Data Penerima')
@section('content')
<div class="card">
    <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h3 style="font-size:15px;font-weight:600;">👥 Data Penerima Manfaat</h3>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('penerima.create') }}" class="btn btn-primary btn-sm">+ Tambah</a>
        </div>
    </div>
    <div style="padding:16px 20px;">
        <form style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
            <input type="text" name="search" placeholder="Cari NIK/nama..." value="{{ request('search') }}" class="form-input" style="width:200px;padding:8px 12px;font-size:13px;">
            <select name="status" class="form-input" style="width:140px;padding:8px 12px;font-size:13px;">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="terverifikasi" {{ request('status')=='terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="kabupaten" class="form-input" style="width:200px;padding:8px 12px;font-size:13px;">
                <option value="">Semua Kabupaten</option>
                @foreach($kabupatens ?? [] as $kode => $nama)
                <option value="{{ preg_replace('/^(Kabupaten|Kota)\s/', '', $nama) }}" {{ request('kabupaten') == preg_replace('/^(Kabupaten|Kota)\s/', '', $nama) ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline btn-sm">🔍 Cari</button>
        </form>
        <div style="overflow-x:auto;">
            <table class="table-data">
                <thead><tr><th>NIK</th><th>Nama</th><th>Kelompok</th><th>Sumber</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($penerima as $p)
                    <tr>
                        <td style="color:#6b7280;">{{ $p->nik }}</td>
                        <td style="font-weight:500;">{{ $p->nama }}</td>
                        <td>{{ $p->kelompok->nama ?? '-' }}</td>
                        <td><span style="padding:2px 8px;background:#f0f0f0;border-radius:6px;font-size:12px;">{{ $p->sumber_data }}</span></td>
                        <td>
                            @if($p->status == 'terverifikasi') <span class="badge badge-green">✅ Terverifikasi</span>
                            @elseif($p->status == 'pending') <span class="badge badge-gold">⏳ Pending</span>
                            @else <span class="badge" style="background:#fce8e6;color:#dc2626;">❌ Ditolak</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('penerima.show', $p) }}" style="color:#00034a;text-decoration:none;font-size:13px;">Detail</a>
                            <a href="{{ route('penerima.edit', $p) }}" style="color:#6b7280;margin-left:12px;font-size:13px;">Edit</a>
                            @if($p->status == 'pending')
                            <form method="POST" action="{{ route('penerima.verify', $p) }}" style="display:inline;margin-left:8px;">
                                @csrf
                                <input type="hidden" name="status" value="terverifikasi">
                                <button style="color:#017723;border:none;background:none;cursor:pointer;font-size:13px;">✅ Verifikasi</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">Belum ada data penerima</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">{{ $penerima->links() }}</div>
    </div>
</div>
@endsection
