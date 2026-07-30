<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penerima Bantuan — PEDULI YPKM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:#f5f6fa;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
        .wrap{width:100%;max-width:560px;}
        .head{background:#00034a;border-radius:16px 16px 0 0;padding:28px;text-align:center;}
        .head img{width:56px;height:56px;border-radius:12px;background:white;object-fit:cover;}
        .head h1{color:white;font-size:20px;margin-top:12px;}
        .head p{color:rgba(255,255,255,0.6);font-size:13px;margin-top:4px;}
        .card{background:white;border-radius:0 0 16px 16px;padding:28px;border:1px solid #e5e7eb;border-top:none;}
        .form-label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:#1a1a2e;}
        .form-input{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:white;}
        .form-input:focus{outline:none;border-color:#00034a;box-shadow:0 0 0 3px rgba(0,3,74,0.08);}
        .grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        .mb{margin-bottom:14px;}
        .btn{width:100%;padding:13px;background:#017723;color:white;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;}
        .btn:hover{background:#01611c;}
        .alert-ok{background:#e8f5ec;border:1px solid #c6e6d0;color:#017723;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:16px;}
        .alert-err{background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:16px;}
        .note{font-size:12px;color:#6b7280;text-align:center;margin-top:16px;}
    </style>
</head>
<body>
<div class="wrap">
    <div class="head">
        <img src="{{ asset('img/logo-ypkm.jpg') }}" alt="Logo YPKM">
        <h1>Pendaftaran Penerima Bantuan</h1>
        <p>Yayasan Pelangi Kesejahteraan Masyarakat</p>
    </div>
    <div class="card">
        @if(session('success'))<div class="alert-ok">✅ {{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-err">❌ {{ $errors->first() }}</div>@endif

        <form method="POST" action="{{ route('penerima.daftar') }}">
            @csrf
            <div class="mb">
                <label class="form-label">NIK (sesuai KTP) *</label>
                <input type="text" name="nik" class="form-input" required maxlength="16" value="{{ old('nik') }}" placeholder="16 digit NIK">
            </div>
            <div class="mb">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-input" required value="{{ old('nama') }}" placeholder="Nama sesuai KTP">
            </div>
            <div class="grid2 mb">
                <div>
                    <label class="form-label">No. HP *</label>
                    <input type="text" name="phone" class="form-input" required value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="form-label">Jumlah Anggota Keluarga</label>
                    <input type="number" name="jumlah_keluarga" class="form-input" value="{{ old('jumlah_keluarga', 1) }}" min="1">
                </div>
            </div>
            <div class="mb">
                <label class="form-label">Alamat Lengkap *</label>
                <input type="text" name="alamat" class="form-input" required value="{{ old('alamat') }}" placeholder="Dusun/Jalan, Desa, Kecamatan">
            </div>
            <button type="submit" class="btn">📝 Daftar Sekarang</button>
        </form>
        <p class="note">Data Anda akan diverifikasi oleh petugas YPKM.<br>Status kepesertaan akan diinformasikan melalui nomor HP.</p>
    </div>
</div>
</body>
</html>
