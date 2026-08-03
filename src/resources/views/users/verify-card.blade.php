@php
    $logoUrl = asset('img/logo-ypkm-transparent.png');
    $fotoUrl = $user->foto ? asset('storage/'.$user->foto) : '';
    $roleLabel = match($user->role) {
        'super_admin' => 'Super Admin', 'pengurus' => 'Pengurus',
        'bendahara' => 'Bendahara', 'staff' => 'Staf',
        'staff_keuangan' => 'Staf Keuangan', 'relawan' => 'Relawan Kemanusiaan',
        'ketua_kelompok' => 'Ketua Kelompok', default => ucwords(str_replace('_',' ',$user->role))
    };
    $alamat = $user->alamat_lengkap ?: $user->wilayahLabel();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Kartu — YPKM</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#00034a 0%,#1a1a5e 55%,#00034a 100%);padding:20px}
.card{width:380px;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 30px 70px -15px rgba(0,0,0,.55);position:relative;border:1px solid rgba(255,255,255,.15)}
.accent-bar{height:8px;background:linear-gradient(90deg,#B71C1C 0%,#D32F2F 30%,#E65100 45%,#2E7D32 60%,#1565C0 80%,#5E35B1 100%)}
.holo{position:absolute;inset:0;pointer-events:none;background:linear-gradient(125deg,transparent 35%,rgba(255,255,255,.18) 47%,rgba(255,255,255,.04) 53%,transparent 65%);z-index:5}
.badge-ok{display:inline-flex;align-items:center;gap:5px;background:#dcfce7;color:#166534;font-size:11px;font-weight:800;padding:5px 12px;border-radius:999px;margin-bottom:10px}
.badge-ok svg{width:13px;height:13px}
.body{padding:26px 24px 22px;position:relative;z-index:10;text-align:center}
.logo{width:54px;height:54px;border-radius:12px;object-fit:cover;margin:0 auto 12px;border:2px solid #e2e8f0;display:block}
h1{font-size:15px;font-weight:800;color:#00034a;letter-spacing:.2px;margin-bottom:3px}
.sub{font-size:11px;color:#64748b;margin-bottom:18px}
.member{display:flex;align-items:center;gap:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:16px;text-align:left}
.member-photo{width:58px;height:66px;border-radius:10px;padding:2px;background:linear-gradient(135deg,#00034a,#e5a820);flex-shrink:0}
.member-photo img{width:100%;height:100%;object-fit:cover;border-radius:8px}
.member-photo .ph{width:100%;height:100%;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;background:#1a1a5e}
.member-info{min-width:0}
.member-info .name{font-size:14px;font-weight:800;color:#00034a;text-transform:uppercase;letter-spacing:.2px}
.member-info .role{font-size:10px;color:#D32F2F;font-weight:700;text-transform:uppercase;margin:1px 0 4px}
.member-info .code{font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:#00034a;letter-spacing:.5px}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px}
.detail{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;text-align:left}
.detail .lbl{font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px}
.detail .val{font-size:12px;font-weight:700;color:#00034a;margin-top:2px}
.detail.full{grid-column:1/-1}
.status-aktif{display:inline-flex;align-items:center;gap:4px;background:#dcfce7;color:#166534;font-size:9px;font-weight:800;padding:3px 9px;border-radius:999px}
.status-nonaktif{display:inline-flex;align-items:center;gap:4px;background:#fee2e2;color:#991b1b;font-size:9px;font-weight:800;padding:3px 9px;border-radius:999px}
.footer{padding:12px;background:linear-gradient(135deg,#00034a,#1a1a5e);text-align:center;position:relative;z-index:10}
.footer p{font-size:9px;color:#94a3b8;font-weight:600}
.footer b{color:#e5a820}
</style>
</head>
<body>
<div class="card">
    <div class="accent-bar"></div>
    <div class="holo"></div>
    <div class="body">
        <span class="badge-ok">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
            KARTU TERVERIFIKASI
        </span>
        <img src="{{ $logoUrl }}" alt="YPKM" class="logo">
        <h1>Yayasan Pelangi Kesejahteraan Masyarakat</h1>
        <p class="sub">Kartu tanda anggota resmi — peduli.ypkm.info</p>

        <div class="member">
            <div class="member-photo">
                @if($fotoUrl)
                    <img src="{{ $fotoUrl }}" alt="{{ $user->name }}">
                @else
                    <div class="ph">{{ strtoupper(substr($user->name,0,1)) }}</div>
                @endif
            </div>
            <div class="member-info">
                <div class="name">{{ $user->name }}</div>
                <div class="role">{{ $roleLabel }}</div>
                <div class="code">{{ $user->kode_keanggotaan }}</div>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail">
                <div class="lbl">Status</div>
                <div class="val"><span class="{{ $user->is_active ? 'status-aktif' : 'status-nonaktif' }}">{{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}</span></div>
            </div>
            <div class="detail">
                <div class="lbl">Kontak</div>
                <div class="val" style="font-size:11px">{{ $user->phone ?: '-' }}</div>
            </div>
            <div class="detail full">
                <div class="lbl">Alamat</div>
                <div class="val" style="font-size:11px;font-weight:600">{{ $alamat }}</div>
            </div>
            <div class="detail full">
                <div class="lbl">Email</div>
                <div class="val" style="font-size:11px;font-weight:600">{{ $user->email }}</div>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>Dokumen ini sah dikeluarkan oleh <b>YPKM</b> · Verifikasi: <b>{{ $user->kode_keanggotaan }}</b></p>
    </div>
</div>
</body>
</html>
