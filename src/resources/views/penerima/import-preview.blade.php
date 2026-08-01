@extends('layouts.app')
@section('title', 'Preview Import Penerima')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 style="font-size:15px;font-weight:600;">📋 Preview Import Penerima</h3>
    </div>
    <div class="card-body">
        @if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert" style="background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;">❌ {{ $errors->first() }}</div>@endif

        <p style="font-size:13px;color:#667085;margin:0 0 16px;">
            ✅ <strong>{{ count($validRows) }}</strong> baris valid siap diimport.
            @if(count($errorRows))
            ⚠️ <strong>{{ count($errorRows) }}</strong> baris bermasalah (tidak akan diimport).
            @endif
        </p>

        @if(count($validRows))
        <form method="POST" action="{{ route('penerima.import.store') }}">
            @csrf
            <div class="table-wrap">
                <table class="table-data">
                    <thead><tr>
                        <th>Baris</th><th>NIK</th><th>Nama</th><th>Kabupaten</th><th>Kecamatan</th><th>Desa</th><th>Kelompok</th><th>HP</th>
                    </tr></thead>
                    <tbody>
                        @foreach($validRows as $r)
                        <tr>
                            <td>{{ $r['_line'] }}</td>
                            <td style="font-family:monospace;font-size:13px;">{{ $r['nik'] }}</td>
                            <td>{{ $r['nama'] }}</td>
                            <td>{{ $r['kabupaten'] }}</td>
                            <td>{{ $r['kecamatan'] }}</td>
                            <td>{{ $r['desa'] }}</td>
                            <td>{{ $r['kelompok_id'] }}</td>
                            <td>{{ $r['phone'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px;display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm">✅ Import {{ count($validRows) }} Data</button>
                <form method="POST" action="{{ route('penerima.import.cancel') }}" style="display:inline;">
                    @csrf
                    <button class="btn btn-outline btn-sm">↩️ Batal</button>
                </form>
            </div>
        </form>
        @endif

        @if(count($errorRows))
        <div style="margin-top:24px;">
            <h4 style="font-size:14px;color:#dc2626;margin:0 0 8px;">⚠️ Baris Bermasalah (tidak diimport)</h4>
            <div class="table-wrap">
                <table class="table-data">
                    <thead><tr><th>Baris</th><th>NIK</th><th>Nama</th><th>Masalah</th></tr></thead>
                    <tbody>
                        @foreach($errorRows as $r)
                        <tr>
                            <td>{{ $r['_line'] }}</td>
                            <td style="font-family:monospace;font-size:13px;">{{ $r['nik'] }}</td>
                            <td>{{ $r['nama'] }}</td>
                            <td style="color:#dc2626;font-size:12px;">{{ implode('; ', $r['_errors']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(!count($validRows) && !count($errorRows))
        <p style="text-align:center;color:#9ca3af;padding:32px;">File kosong atau format tidak sesuai template.</p>
        @endif

        <div style="margin-top:16px;">
            <a href="{{ route('penerima.index') }}" class="btn btn-outline btn-sm">← Kembali ke Daftar Penerima</a>
        </div>
    </div>
</div>
@endsection
