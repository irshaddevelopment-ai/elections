@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<style>
  body {
    background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%) !important;
  }
  body::after {
    content: '';
    position: fixed; inset: 0;
    background-image: radial-gradient(circle, rgba(30,58,112,0.05) 1.5px, transparent 1.5px);
    background-size: 28px 28px;
    pointer-events: none; z-index: 0;
  }
  .orb {
    position: fixed; border-radius: 50%;
    filter: blur(100px); pointer-events: none;
    opacity: 0.15; animation: orbFloat 12s ease-in-out infinite; z-index: 0;
  }
  .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px;animation-delay:0s; }
  .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
  @keyframes orbFloat {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.14) translate(18px,-18px); }
  }

  /* ── Page ── */
  .db-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.25rem 1rem 3rem;
  }

  /* ── Welcome banner ── */
  .db-banner {
    border-radius: 1.25rem; overflow: hidden; margin-bottom: 2rem;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.06),
      0 20px 50px rgba(30,58,112,0.12),
      0 0 0 1px rgba(212,168,32,0.22);
  }
  .db-banner::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }
  .db-banner-inner {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1.75rem 2rem;
    display: flex; align-items: center; gap: 1.5rem;
    position: relative; overflow: hidden;
  }
  .db-banner-inner::after {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(212,168,32,0.06) 1.5px, transparent 1.5px);
    background-size: 22px 22px;
    pointer-events: none;
  }

  /* Spinning seal */
  .db-seal {
    position: relative; display: inline-block; flex-shrink: 0; z-index: 1;
  }
  .db-seal::before {
    content: '';
    position: absolute; inset: -5px; border-radius: 50%;
    background: conic-gradient(from 0deg,
      #a07818 0deg, #f0c94d 60deg, #d4a820 120deg,
      #f0c94d 180deg, #a07818 240deg, #f0c94d 300deg, #a07818 360deg);
    animation: sealSpin 8s linear infinite;
  }
  .db-seal::after {
    content: '';
    position: absolute; inset: -1px; border-radius: 50%;
    background: #1a3268;
  }
  @keyframes sealSpin { to { transform: rotate(360deg); } }
  .db-seal img {
    position: relative; z-index: 1;
    width: 72px; height: 72px; border-radius: 50%; object-fit: cover; display: block;
  }

  .db-banner-text { z-index: 1; }
  .db-banner-text h2 {
    margin: 0 0 .2rem; font-size: 1.35rem; font-weight: 800; color: #fff;
  }
  .db-banner-text p { margin: 0; font-size: .88rem; color: rgba(255,255,255,.55); }
  .db-banner-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    margin-top: .65rem; font-size: .72rem; font-weight: 700; color: #f0c94d;
    background: rgba(212,168,32,0.12); border: 1px solid rgba(212,168,32,0.3);
    border-radius: 2rem; padding: .2rem .8rem;
  }
  .db-decornum {
    position: absolute; left: 2rem; top: 50%; transform: translateY(-50%);
    font-size: 5rem; font-weight: 900; color: rgba(212,168,32,0.07);
    letter-spacing: -4px; line-height: 1; pointer-events: none; z-index: 0;
    font-family: 'Cairo', sans-serif;
  }

  /* ── Section label ── */
  .db-section-label {
    font-size: .72rem; font-weight: 700; letter-spacing: .9px;
    text-transform: uppercase; color: #1e3a70;
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: 1rem; margin-top: .25rem;
  }
  .db-section-label::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(90deg, rgba(212,168,32,0.4), transparent);
  }
  .db-section-label i { color: #c8920a; }

  /* ── Tile grid ── */
  .db-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 1.75rem;
  }

  /* ── Tile ── */
  .db-tile {
    background: #fff; border-radius: 1rem; overflow: hidden;
    box-shadow:
      0 2px 6px rgba(30,58,112,0.05),
      0 0 0 1px rgba(212,168,32,0.15);
    text-decoration: none; color: inherit;
    display: flex; flex-direction: column;
    transition: box-shadow .18s, transform .15s;
  }
  .db-tile:hover {
    box-shadow:
      0 6px 24px rgba(30,58,112,0.12),
      0 0 0 1px rgba(212,168,32,0.35);
    transform: translateY(-3px);
    text-decoration: none; color: inherit;
  }
  .db-tile-top {
    height: 3px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }
  .db-tile-body {
    padding: 1.1rem 1.15rem .95rem;
    display: flex; align-items: flex-start; gap: .85rem; flex: 1;
  }
  .db-tile-icon {
    width: 42px; height: 42px; border-radius: .65rem; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    background: linear-gradient(135deg, #1a3268, #1e4098);
    color: #f0c94d;
    box-shadow: 0 3px 10px rgba(30,58,112,0.2);
  }
  .db-tile-icon.amber { background: linear-gradient(135deg, #92400e, #d97706); }
  .db-tile-icon.green { background: linear-gradient(135deg, #14532d, #16a34a); }
  .db-tile-icon.red   { background: linear-gradient(135deg, #7f1d1d, #b91c1c); }
  .db-tile-icon.teal  { background: linear-gradient(135deg, #134e4a, #0d9488); }

  .db-tile-info { flex: 1; min-width: 0; }
  .db-tile-info strong {
    display: block; font-size: .88rem; font-weight: 800;
    color: #0c1e35; margin-bottom: .18rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .db-tile-info span {
    font-size: .75rem; color: #94a3b8; line-height: 1.35;
  }
  .db-tile-footer {
    padding: .45rem 1.15rem .6rem;
    display: flex; align-items: center; justify-content: flex-end;
    border-top: 1px solid #f0ecd8;
  }
  .db-tile-footer span {
    font-size: .72rem; font-weight: 700; color: #c8920a;
    display: flex; align-items: center; gap: .3rem;
  }
  .db-tile-footer span i { font-size: .65rem; }

  /* ── Alert ── */
  .db-alert {
    border-radius: .75rem; font-size: .88rem;
    padding: .75rem 1rem; margin-bottom: 1.5rem;
    background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
    display: flex; align-items: center; gap: .5rem;
  }

  /* Stagger animation */
  .anim-up { animation: dbFadeUp .45s ease both; }
  .d1{animation-delay:.05s} .d2{animation-delay:.1s}  .d3{animation-delay:.15s}
  .d4{animation-delay:.2s}  .d5{animation-delay:.25s} .d6{animation-delay:.3s}
  .d7{animation-delay:.35s} .d8{animation-delay:.4s}  .d9{animation-delay:.45s}
  .d10{animation-delay:.5s} .d11{animation-delay:.55s} .d12{animation-delay:.6s}
  @keyframes dbFadeUp {
    from { opacity:0; transform:translateY(12px); }
    to   { opacity:1; transform:translateY(0); }
  }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="db-page" dir="rtl">
<div class="container-fluid" style="max-width:1100px;">

  @if (session('status'))
    <div class="db-alert anim-up d1">
      <i class="fas fa-check-circle"></i> {{ session('status') }}
    </div>
  @endif

  {{-- Welcome banner --}}
  <div class="db-banner anim-up d1">
    <div class="db-banner-inner">
      <div class="db-seal">
        <img src="{{ URL('images/logo.jpg') }}" alt="شعار">
      </div>
      <div class="db-banner-text">
        <h2>{{ config('app.name', 'نظام الانتخابات') }}</h2>
        <p>لوحة التحكم الرئيسية</p>
        <div class="db-banner-badge">
          <i class="fas fa-stamp"></i> بوابة الاقتراع الرسمية
        </div>
      </div>
      <div class="db-decornum">✦</div>
    </div>
  </div>

  {{-- Profiles section --}}
  <div class="db-section-label anim-up d2"><i class="fas fa-users"></i> الأسماء والمواضيع</div>
  <div class="db-grid">
    <a class="db-tile anim-up d3" href="{{ route('usermanager') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon"><i class="fas fa-user-plus"></i></div>
        <div class="db-tile-info">
          <strong>إضافة اسم</strong>
          <span>تسجيل اسم جديد في النظام</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d4" href="{{ route('userslist') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon"><i class="fas fa-list"></i></div>
        <div class="db-tile-info">
          <strong>لائحة الأسماء</strong>
          <span>عرض وإدارة جميع الأسماء</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d5" href="{{ route('subjectmanager') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon amber"><i class="fas fa-tag"></i></div>
        <div class="db-tile-info">
          <strong>إضافة موضوع</strong>
          <span>إنشاء موضوع انتخابي جديد</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d6" href="{{ route('subjectslist') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon amber"><i class="fas fa-tags"></i></div>
        <div class="db-tile-info">
          <strong>لائحة المواضيع</strong>
          <span>عرض وإدارة المواضيع</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>
  </div>

  {{-- Elections section --}}
  <div class="db-section-label anim-up d3"><i class="fas fa-vote-yea"></i> العمليات الانتخابية</div>
  <div class="db-grid">
    <a class="db-tile anim-up d4" href="{{ route('electionmanager') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon"><i class="fas fa-plus-circle"></i></div>
        <div class="db-tile-info">
          <strong>إضافة عملية انتخابية</strong>
          <span>إنشاء عملية انتخابية جديدة</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d5" href="{{ route('electionslist') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon"><i class="fas fa-list-alt"></i></div>
        <div class="db-tile-info">
          <strong>لائحة العمليات</strong>
          <span>عرض وإدارة جميع الانتخابات</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d6" href="{{ route('adminresults') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon green"><i class="fas fa-chart-bar"></i></div>
        <div class="db-tile-info">
          <strong>نتائج التصويت</strong>
          <span>عرض نتائج الجولات</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>
  </div>

  {{-- People section --}}
  <div class="db-section-label anim-up d5"><i class="fas fa-id-card"></i> المرشحون والناخبون والمرشدون</div>
  <div class="db-grid">
    <a class="db-tile anim-up d6" href="{{ route('candidatemanager') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon"><i class="fas fa-user-edit"></i></div>
        <div class="db-tile-info">
          <strong>المرشحون</strong>
          <span>إضافة وتعديل المرشحين</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d7" href="{{ route('votermanager') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon teal"><i class="fas fa-user-plus"></i></div>
        <div class="db-tile-info">
          <strong>إضافة ناخبين</strong>
          <span>ربط الناخبين بعملية انتخابية</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d8" href="{{ route('groupmanager') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon teal"><i class="fas fa-users"></i></div>
        <div class="db-tile-info">
          <strong>مجموعات الناخبين</strong>
          <span>إضافة وإدارة المجموعات</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d9" href="{{ route('leadermanager') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon amber"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="db-tile-info">
          <strong>إضافة مرشد</strong>
          <span>تعيين ناخبين لمرشد</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d10" href="{{ route('leaderslist') }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon amber"><i class="fas fa-list"></i></div>
        <div class="db-tile-info">
          <strong>لائحة المرشدين</strong>
          <span>عرض المرشدين ومجموعاتهم</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>فتح <i class="fas fa-chevron-left"></i></span></div>
    </a>

    <a class="db-tile anim-up d11" href="{{ route('logout', ['profile_code' => session('profile_code')]) }}">
      <div class="db-tile-top"></div>
      <div class="db-tile-body">
        <div class="db-tile-icon red"><i class="fas fa-sign-out-alt"></i></div>
        <div class="db-tile-info">
          <strong>تسجيل الخروج</strong>
          <span>إنهاء الجلسة الحالية</span>
        </div>
      </div>
      <div class="db-tile-footer"><span>خروج <i class="fas fa-chevron-left"></i></span></div>
    </a>
  </div>

</div>
</div>

@endsection
