<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — PEDULI YPKM</title>
    <link rel="icon" href="{{ asset('img/logo-ypkm.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{
            font-family:'Inter',sans-serif;
            min-height:100vh;display:flex;align-items:center;justify-content:center;
            background:linear-gradient(135deg,#00034a 0%,#1a1e6e 50%,#00034a 100%);
            background-size:200% 200%;
            animation:gradientMove 12s ease infinite;
            padding:24px;position:relative;overflow:hidden;
        }
        @keyframes gradientMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

        /* Floating shapes */
        .shape{position:absolute;border-radius:50%;filter:blur(60px);opacity:0.35;animation:float 10s ease-in-out infinite;}
        .shape1{width:340px;height:340px;background:#e5a820;top:-90px;left:-90px;}
        .shape2{width:280px;height:280px;background:#017723;bottom:-70px;right:-70px;animation-delay:3s;}
        .shape3{width:200px;height:200px;background:#4a4eb5;top:55%;left:12%;animation-delay:6s;}
        @keyframes float{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-28px) scale(1.06)}}

        .login-card{
            width:100%;max-width:420px;background:rgba(255,255,255,0.98);
            border-radius:20px;padding:40px 36px;position:relative;z-index:2;
            box-shadow:0 24px 80px rgba(0,3,74,0.45);
            animation:cardIn .7s cubic-bezier(.2,.9,.3,1.2) both;
        }
        @keyframes cardIn{from{opacity:0;transform:translateY(36px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}

        .logo-wrap{display:flex;justify-content:center;margin-bottom:18px;}
        .logo-ring{
            width:92px;height:92px;border-radius:50%;padding:5px;
            background:conic-gradient(#e5a820,#017723,#00034a,#e5a820);
            animation:spin 5s linear infinite;
            display:flex;align-items:center;justify-content:center;
        }
        @keyframes spin{to{transform:rotate(360deg)}}
        .logo-ring img{
            width:100%;height:100%;border-radius:50%;object-fit:cover;background:white;
            animation:spinBack 5s linear infinite;
            border:3px solid white;
        }
        @keyframes spinBack{to{transform:rotate(-360deg)}}

        h1{text-align:center;font-size:22px;font-weight:800;color:#00034a;letter-spacing:-0.5px;animation:fadeUp .6s .2s both;}
        .sub{text-align:center;font-size:13px;color:#6b7280;margin-top:4px;margin-bottom:26px;animation:fadeUp .6s .3s both;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

        .field{margin-bottom:16px;animation:fadeUp .6s .4s both;}
        .field label{display:block;font-size:13px;font-weight:600;color:#1a1a2e;margin-bottom:6px;}
        .input-wrap{position:relative;}
        .input-wrap .icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:15px;opacity:.55;}
        .field input{
            width:100%;padding:12px 14px 12px 40px;border:1.5px solid #e5e7eb;border-radius:10px;
            font-size:14px;font-family:inherit;transition:all .2s;background:#fafafa;
        }
        .field input:focus{outline:none;border-color:#00034a;background:white;box-shadow:0 0 0 4px rgba(0,3,74,0.08);}

        .row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;animation:fadeUp .6s .5s both;}
        .remember{display:flex;align-items:center;gap:7px;font-size:13px;color:#4b5563;cursor:pointer;}
        .remember input{accent-color:#00034a;width:15px;height:15px;}
        .row a{font-size:13px;color:#017723;text-decoration:none;font-weight:500;}
        .row a:hover{text-decoration:underline;}

        .btn-login{
            width:100%;padding:13px;border:none;border-radius:10px;cursor:pointer;
            background:linear-gradient(90deg,#00034a,#1a1e6e 55%,#00034a);background-size:200% auto;
            color:white;font-size:15px;font-weight:700;font-family:inherit;letter-spacing:.2px;
            transition:all .3s;animation:fadeUp .6s .6s both;
        }
        .btn-login:hover{background-position:right center;transform:translateY(-1px);box-shadow:0 10px 24px rgba(0,3,74,0.35);}
        .btn-login:active{transform:translateY(0);}

        .daftar-link{text-align:center;margin-top:20px;font-size:13px;color:#6b7280;animation:fadeUp .6s .7s both;}
        .daftar-link a{color:#017723;font-weight:600;text-decoration:none;}
        .daftar-link a:hover{text-decoration:underline;}

        .alert-err{background:#fce8e6;border:1px solid #f5c6c0;color:#dc2626;padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;animation:shake .4s;}
        .alert-ok{background:#e8f5ec;border:1px solid #c6e6d0;color:#017723;padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;}
        @keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}

        .footer-note{position:absolute;bottom:18px;left:0;right:0;text-align:center;font-size:12px;color:rgba(255,255,255,0.5);z-index:2;}
        .login-card,h1,.sub,.field,.row,.btn-login,.daftar-link,.logo-ring,.logo-ring img{animation:none;}
        .shape{animation:none;opacity:.24;}
        :focus-visible{outline:3px solid rgba(229,168,32,.85);outline-offset:3px;}
        @media(max-width:480px){body{padding:14px;align-items:center;justify-content:flex-start;flex-direction:column;overflow-y:auto}.login-card{margin-top:18px;padding:28px 22px;border-radius:16px}.logo-ring{width:78px;height:78px}.footer-note{position:relative;margin:18px 12px 4px}.field input,.btn-login{min-height:46px}}
        @media(prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important;scroll-behavior:auto!important;}}
    </style>
</head>
<body>
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>

    <div class="login-card">
        <div class="logo-wrap">
            <div class="logo-ring">
                <img src="{{ asset('img/logo-ypkm.jpg') }}" alt="Logo YPKM">
            </div>
        </div>
        <h1>PEDULI YPKM</h1>
        <p class="sub">Pendataan &amp; Distribusi Untuk Layanan Insani<br>Yayasan Pelangi Kesejahteraan Masyarakat</p>

        @if(session('status'))<div class="alert-ok">✅ {{ session('status') }}</div>@endif
        @if($errors->any())<div class="alert-err">❌ Email atau password salah. Silakan coba lagi.</div>@endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <span class="icon">📧</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@ypkm.info">
                </div>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="icon">🔒</span>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </div>
            </div>
            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Lupa password?</a>
                @endif
            </div>
            <button type="submit" class="btn-login">🔐 Masuk ke Sistem</button>
        </form>

        <p class="daftar-link">Masyarakat penerima bantuan? <a href="{{ url('daftar') }}">Daftar di sini</a></p>
    </div>

    <p class="footer-note">© {{ date('Y') }} Yayasan Pelangi Kesejahteraan Masyarakat · peduli.ypkm.info</p>
</body>
</html>
