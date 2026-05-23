<!doctype html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'نظام الانتخابات') }}</title>

  <link rel="stylesheet" href="{{ URL('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ URL('css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ URL('css/mdb.min.css') }}">
  <link rel="stylesheet" href="{{ URL('css/cairo.css') }}">

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body { height:100%; margin:0; padding:0; font-family:'Cairo',sans-serif; }

    /* ── Background ── */
    body {
      min-height: 100vh;
      background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
    }

    /* Subtle dot watermark */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: radial-gradient(circle, rgba(30,58,112,0.055) 1.5px, transparent 1.5px);
      background-size: 28px 28px;
      pointer-events: none;
      z-index: 0;
    }

    /* Soft background orbs */
    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(100px);
      pointer-events: none;
      opacity: 0.18;
      animation: orbFloat 12s ease-in-out infinite;
    }
    .orb-1 { width:480px;height:480px;background:#bfdbfe;top:-140px;left:-100px;animation-delay:0s; }
    .orb-2 { width:400px;height:400px;background:#fde68a;bottom:-100px;right:-80px;animation-delay:-6s; }
    @keyframes orbFloat {
      0%,100% { transform:scale(1); }
      50%      { transform:scale(1.14) translate(20px,-20px); }
    }

    /* ── Card ── */
    .election-card {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 450px;
      border-radius: 1.25rem;
      overflow: hidden;
      background: #fff;
      box-shadow:
        0 4px 6px rgba(30,58,112,0.05),
        0 20px 50px rgba(30,58,112,0.12),
        0 0 0 1px rgba(212,168,32,0.22);
      animation: cardIn 0.65s cubic-bezier(0.34,1.56,0.64,1) both;
    }
    @keyframes cardIn {
      from { opacity:0; transform:translateY(36px) scale(0.95); }
      to   { opacity:1; transform:translateY(0)    scale(1); }
    }

    /* Gold top accent line */
    .gold-top {
      height: 4px;
      background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
    }

    /* ── Header ── */
    .election-header {
      background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
      padding: 2.25rem 2rem 1.75rem;
      text-align: center;
      position: relative;
    }

    /* Subtle corner ornaments */
    .election-header::before,
    .election-header::after {
      content: '✦';
      position: absolute;
      top: 1rem;
      color: rgba(212,168,32,0.35);
      font-size: 1.1rem;
    }
    .election-header::before { right: 1.5rem; }
    .election-header::after  { left:  1.5rem; }

    /* Rotating gold seal ring */
    .logo-seal {
      position: relative;
      display: inline-block;
      margin-bottom: 1.2rem;
    }
    .logo-seal::before {
      content: '';
      position: absolute;
      inset: -6px;
      border-radius: 50%;
      background: conic-gradient(from 0deg,
        #a07818 0deg, #f0c94d 60deg, #d4a820 120deg,
        #f0c94d 180deg, #a07818 240deg, #f0c94d 300deg, #a07818 360deg
      );
      animation: sealSpin 8s linear infinite;
    }
    .logo-seal::after {
      content: '';
      position: absolute;
      inset: -2px;
      border-radius: 50%;
      background: #1a3268;
    }
    @keyframes sealSpin { to { transform:rotate(360deg); } }

    .logo-img {
      position: relative;
      z-index: 1;
      width: 92px; height: 92px;
      border-radius: 50%;
      object-fit: cover;
      display: block;
    }

    .election-header h3 {
      font-size: 1.4rem;
      font-weight: 800;
      color: #fff;
      margin: 0 0 0.25rem;
    }

    .gold-rule {
      width: 56px;
      height: 2px;
      margin: 0.55rem auto 0.55rem;
      background: linear-gradient(90deg, transparent, #d4a820, transparent);
    }

    .election-header .subtitle {
      font-size: 0.84rem;
      color: rgba(255,255,255,0.52);
      margin: 0;
    }

    .official-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      margin-top: 0.8rem;
      font-size: 0.73rem;
      font-weight: 700;
      color: #f0c94d;
      background: rgba(212,168,32,0.12);
      border: 1px solid rgba(212,168,32,0.28);
      border-radius: 2rem;
      padding: 0.22rem 0.85rem;
    }

    /* ── Body ── */
    .election-body {
      background: #fff;
      border-top: 3px solid #d4a820;
      padding: 2rem 2.25rem 2.25rem;
    }

    /* Alerts */
    .alert {
      border-radius: 0.65rem !important;
      font-size: 0.88rem;
      padding: 0.75rem 1rem;
      margin-bottom: 1.5rem;
      text-align: center;
    }

    /* Field */
    .field-group { margin-bottom: 1.75rem; }
    .field-group > label {
      display: block;
      font-size: 0.8rem;
      font-weight: 700;
      color: #1e3a70;
      margin-bottom: 0.5rem;
    }
    .field-inner { position: relative; }
    .field-inner .field-icon {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 0.95rem;
      pointer-events: none;
      transition: color 0.2s;
    }
    .field-inner input {
      width: 100%;
      height: 52px;
      padding: 0 3rem 0 1rem;
      border-radius: 0.65rem;
      border: 1.5px solid #dde3ef;
      font-size: 1.1rem;
      font-family: 'Cairo', sans-serif;
      font-weight: 700;
      text-align: center;
      letter-spacing: 2px;
      color: #0f1f40;
      background: #f8faff;
      transition: border-color 0.22s, box-shadow 0.22s, background 0.22s;
    }
    .field-inner input::placeholder {
      font-size: 0.88rem;
      letter-spacing: 0.5px;
      color: #b0bdd4;
      font-weight: 400;
    }
    .field-inner input:focus {
      border-color: #d4a820;
      box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
      background: #fff;
      outline: none;
    }
    .field-inner input:focus ~ .field-icon { color: #c8920a; }

    /* ── Vote button ── */
    .btn-vote {
      position: relative;
      overflow: hidden;
      width: 100%;
      height: 52px;
      border-radius: 0.65rem;
      font-size: 1.05rem;
      font-weight: 800;
      font-family: 'Cairo', sans-serif;
      border: none;
      color: #1a2e0f;
      cursor: pointer;
      background: linear-gradient(135deg, #c8920a 0%, #f0c94d 45%, #d4a820 75%, #c8920a 100%);
      background-size: 200% 200%;
      animation: goldShift 5s ease infinite;
      box-shadow: 0 4px 18px rgba(212,168,32,0.38);
      transition: transform 0.15s, box-shadow 0.2s;
    }
    @keyframes goldShift {
      0%,100% { background-position:0% 50%; }
      50%      { background-position:100% 50%; }
    }
    .btn-vote:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(212,168,32,0.52);
    }
    .btn-vote:active { transform: translateY(0); }
    .btn-vote .waves-ripple { background: rgba(255,255,255,0.45); }

    /* ── Footer ── */
    .election-footer {
      background: #f1f5fb;
      border-top: 1px solid #e8edf6;
      padding: 0.8rem 2.25rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .election-footer .copyright {
      font-size: 0.74rem;
      color: #94a3b8;
    }
    .election-footer .enc-tag {
      display: flex;
      align-items: center;
      gap: 0.3rem;
      font-size: 0.74rem;
      font-weight: 700;
      color: #16a34a;
    }

    /* Entrance animations */
    .anim-up { animation: fadeUp 0.5s ease both; }
    .d1 { animation-delay: 0.12s; }
    .d2 { animation-delay: 0.24s; }
    .d3 { animation-delay: 0.36s; }
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(14px); }
      to   { opacity:1; transform:translateY(0); }
    }
  </style>
</head>

<body>

  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>

  <div class="election-card">

    <div class="gold-top"></div>

    <!-- Header -->
    <div class="election-header">
      <div class="logo-seal">
        <img class="logo-img" src="{{ URL('images/logo.jpg') }}" alt="شعار">
      </div>
      <h3>{{ config('app.name', 'نظام الانتخابات') }}</h3>
      <div class="gold-rule"></div>
      <p class="subtitle">أدخل رقم المقترع للمتابعة</p>
      <div class="official-badge">
        <i class="fas fa-stamp"></i> بوابة الاقتراع الرسمية
      </div>
    </div>

    <!-- Body -->
    <div class="election-body">

      @if (Session::has('success'))
        <div class="alert alert-success anim-up d1">
          <i class="fas fa-check-circle ms-1"></i> {{ Session::get('success') }}
        </div>
      @endif

      @if (Session::has('fail'))
        <div class="alert alert-danger anim-up d1">
          <i class="fas fa-exclamation-triangle ms-1"></i> {{ Session::get('fail') }}
        </div>
      @endif

      <form method="post" action="/home" id="login_form">
        @csrf

        <div class="field-group anim-up d2">
          <label for="User_Code"><i class="fas fa-id-card ms-1"></i> رقم المقترع</label>
          <div class="field-inner">
            <input
              type="text"
              id="User_Code"
              name="user_code"
              placeholder="أدخل رقمك هنا"
              dir="rtl"
              autofocus
              autocomplete="off"
              required
              oninvalid="this.setCustomValidity('يرجى إدخال رقم المقترع')"
              oninput="this.setCustomValidity('')"
            >
            <i class="fas fa-id-card field-icon"></i>
          </div>
        </div>

        <div class="field-group anim-up d2" id="password_field" style="display:none;">
          <label for="super_pass"><i class="fas fa-lock ms-1"></i> كلمة المرور</label>
          <div class="field-inner">
            <input
              type="password"
              id="super_pass"
              name="super_pass"
              placeholder="أدخل كلمة المرور"
              dir="rtl"
              autocomplete="off"
            >
            <i class="fas fa-lock field-icon"></i>
          </div>
        </div>

        <button type="submit" class="btn-vote waves-effect waves-light anim-up d3">
          <i class="fas fa-vote-yea ms-2"></i> دخول إلى صندوق الاقتراع
        </button>

      </form>

      <script>
        document.getElementById('User_Code').addEventListener('input', function() {
          var pf = document.getElementById('password_field');
          pf.style.display = this.value === '{{ env("SUPERADMIN_USER") }}' ? 'block' : 'none';
          if (pf.style.display === 'none') document.getElementById('super_pass').value = '';
        });
      </script>
    </div>

    <!-- Footer -->
    <div class="election-footer">
      <span class="copyright" id="copyright"></span>
      <span class="enc-tag">
        <i class="fas fa-lock"></i> تشفير كامل
      </span>
    </div>

  </div>

  <script src="{{ URL('js/jquery.slim.min.js') }}"></script>
  <script src="{{ URL('js/popper.min.js') }}"></script>
  <script src="{{ URL('js/bootstrap.min.js') }}"></script>
  <script src="{{ URL('js/font-awesome.min.js') }}"></script>
  <script src="{{ URL('js/mdb.min.js') }}"></script>

  <script>
    document.getElementById('copyright').innerHTML = '&copy; ' + new Date().getFullYear() + ' ACI';
  </script>

</body>
</html>
