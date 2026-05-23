<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <style>
    * { font-family: 'Cairo', sans-serif; }

    /* ── Background ── */
    body {
      background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%) !important;
      min-height: 100vh;
    }
    body::after {
      content: ''; position: fixed; inset: 0;
      background-image: radial-gradient(circle, rgba(30,58,112,0.05) 1.5px, transparent 1.5px);
      background-size: 28px 28px; pointer-events: none; z-index: 0;
    }
    .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; opacity: 0.16; animation: orbFloat 12s ease-in-out infinite; z-index: 0; }
    .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px; }
    .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
    @keyframes orbFloat { 0%,100%{transform:scale(1);}50%{transform:scale(1.14) translate(18px,-18px);} }

    /* ── Navbar ── */
    .gn-bar {
      background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
      padding: .75rem 1.5rem;
      display: flex; align-items: center; justify-content: space-between;
      box-shadow: 0 4px 20px rgba(10,22,60,0.25);
      position: relative; z-index: 10;
    }
    .gn-bar::after {
      content: ''; position: absolute; bottom: 0; left: 0; right: 0;
      height: 3px; background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
    }
    .gn-brand { display: flex; align-items: center; gap: .65rem; text-decoration: none; }
    .gn-brand img { width: 36px; height: 36px; border-radius: 50%; border: 2px solid rgba(212,168,32,0.5); object-fit: cover; }
    .gn-brand span { font-weight: 800; font-size: 1rem; color: #fff; }
    .gn-leader-link {
      font-size: .88rem; font-weight: 600; color: #f0c94d; text-decoration: none;
      display: flex; align-items: center; gap: .35rem;
      border: 1px solid rgba(240,201,77,0.3); border-radius: 2rem;
      padding: .3rem .85rem; transition: background .18s;
    }
    .gn-leader-link:hover { background: rgba(240,201,77,0.12); color: #f0c94d; }
    .gn-logout {
      font-size: .88rem; font-weight: 600; color: rgba(255,255,255,0.8); text-decoration: none;
      display: flex; align-items: center; gap: .35rem;
      border: 1px solid rgba(255,255,255,0.2); border-radius: 2rem;
      padding: .3rem .85rem; transition: background .18s, color .18s;
    }
    .gn-logout:hover { background: rgba(255,255,255,0.1); color: #fff; }

    /* ── Progress ── */
    .loading-container { position: fixed; top: 0; left: 0; right: 0; z-index: 999; }
    .loading-container .progress { height: 4px; border-radius: 0; background: rgba(212,168,32,0.2); }
    .loading-container .progress-bar { background: linear-gradient(90deg, #c8920a, #f0c94d); }

    /* ── Page ── */
    .g-page { position: relative; z-index: 1; padding: 2rem 1rem 3rem; }

    /* ── Card ── */
    .g-card {
      background: #fff; border-radius: 1.25rem; overflow: hidden;
      box-shadow: 0 4px 6px rgba(30,58,112,0.05), 0 20px 50px rgba(30,58,112,0.11), 0 0 0 1px rgba(212,168,32,0.2);
      margin-bottom: 1.25rem;
    }
    .g-card::before { content: ''; display: block; height: 4px; background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a); }
    .g-card-header {
      background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
      padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
    }
    .g-card-header-left { display: flex; align-items: center; gap: .6rem; }
    .g-header-icon {
      width: 36px; height: 36px; border-radius: .55rem;
      background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .g-header-icon i { color: #f0c94d; font-size: .9rem; }
    .g-card-header span { font-weight: 700; font-size: .97rem; color: #fff; }
    .g-card-body { padding: 1.25rem 1.5rem 1.5rem; border-top: 3px solid #d4a820; }

    /* ── Action buttons ── */
    .btn-g-primary {
      height: 40px; padding: 0 1.4rem; border-radius: .6rem;
      font-size: .9rem; font-weight: 700;
      background: linear-gradient(135deg, #c8920a, #f0c94d, #c8920a);
      background-size: 200% 200%; animation: goldShift 5s ease infinite;
      border: none; color: #1a2e0f; cursor: pointer;
      box-shadow: 0 2px 10px rgba(212,168,32,0.3);
      transition: transform .15s, box-shadow .18s;
      display: inline-flex; align-items: center; gap: .4rem;
    }
    .btn-g-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(212,168,32,0.45); }
    .btn-g-primary:disabled { opacity: .55; cursor: not-allowed; transform: none; }
    @keyframes goldShift { 0%,100%{background-position:0% 50%;}50%{background-position:100% 50%;} }

    /* ── Search ── */
    .g-search-wrap { position: relative; }
    .g-search-icon { position: absolute; top: 50%; transform: translateY(-50%); right: .85rem; color: #c8920a; font-size: .85rem; pointer-events: none; }
    .g-search-wrap input {
      width: 100%; padding: .5rem .85rem .5rem 1rem;
      padding-right: 2.2rem;
      border: 1.5px solid #dde3ef; border-radius: .65rem;
      font-size: .88rem; color: #0f1f40; background: #f8faff;
      font-family: 'Cairo', sans-serif; height: 42px;
      transition: border-color .18s, box-shadow .18s;
    }
    .g-search-wrap input:focus { border-color: #d4a820; box-shadow: 0 0 0 3px rgba(212,168,32,0.18); outline: none; }

    /* ── Tables ── */
    #dtcandidateslist thead tr,
    #dtcandidates thead tr { background: linear-gradient(135deg, #f8f4e8, #fef9e7); }
    #dtcandidateslist thead th,
    #dtcandidates thead th {
      font-weight: 700; font-size: .85rem; color: #1e3a70;
      border-bottom: 2px solid rgba(212,168,32,0.35) !important;
      text-align: center; padding: .7rem;
    }
    #dtcandidateslist tbody tr,
    #dtcandidates tbody tr { transition: background .12s; cursor: pointer; }
    #dtcandidateslist tbody tr:not(.highlight):hover,
    #dtcandidates tbody tr:not(.highlight):hover { background: #fef9e7; }
    td { border: 1px solid #f0ecd8 !important; text-align: center; vertical-align: middle; font-size: .88rem; color: #1e3a70; }

    /* ── Selection highlight ── */
    .highlight td,
    .highlight th { background-color: lightblue !important; }

    /* ── Overlay ── */
    #overlay { position: fixed; inset: 0; background: rgba(10,22,60,0.45); z-index: 9999; display: none; backdrop-filter: blur(2px); }

    /* ── Alerts ── */
    #pageMessages { position: fixed; bottom: 15px; right: 15px; width: 380px; z-index: 10000; }
    .alert { position: relative; font-size: .9rem; text-align: right; border-radius: .65rem; }
    .alert .close { position: absolute; top: 5px; left: 5px; font-size: 1em; }
    .alert .fa { margin-right: .3em; }

    .no_drop { cursor: not-allowed; }

    /* ── Modals ── */
    .gm-modal .modal-content { border-radius: 1rem; border: none; box-shadow: 0 8px 40px rgba(10,22,40,0.2); overflow: hidden; }
    .gm-modal .modal-header { background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%); border-bottom: none; padding: 1rem 1.25rem; }
    .gm-modal .modal-title { font-weight: 700; font-size: 1rem; color: #fff; }
    .gm-modal .modal-header .btn-close,
    .gm-modal .modal-header .close { color: rgba(255,255,255,0.7) !important; text-shadow: none; opacity: 1; }
    .gm-modal .modal-body { font-size: .92rem; color: #1e3a70; padding: 1.25rem; border-top: 3px solid #d4a820; }
    .gm-modal .modal-footer { border-top: 1px solid #e8edf6; padding: .85rem 1.25rem; gap: .5rem; }

    .btn-modal-secondary { height: 38px; padding: 0 1.1rem; border-radius: .55rem; font-size: .88rem; font-weight: 600; background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70; cursor: pointer; transition: background .18s; }
    .btn-modal-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.4); }
    .btn-modal-primary { height: 38px; padding: 0 1.1rem; border-radius: .55rem; font-size: .88rem; font-weight: 700; background: linear-gradient(135deg, #c8920a, #f0c94d, #c8920a); background-size: 200%; border: none; color: #1a2e0f; cursor: pointer; box-shadow: 0 2px 10px rgba(212,168,32,0.3); display: inline-flex; align-items: center; gap: .4rem; }
    .btn-modal-primary:hover { box-shadow: 0 4px 14px rgba(212,168,32,0.45); }

    /* ── ID Card extras ── */
    .btn-circle { width: 34px; height: 34px; border-radius: 50%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; }
    .card-img-top { height: 140px; width: 140px; object-fit: cover; border-radius: .75rem; }
    .custom-table { background-color: #f8f4e8; }
    .lblidcardtitle { font-size: 1.3rem; font-weight: 800; color: #1e3a70; }
    #idcardtable1 tr td, #idcardworkat tr td, #residencetable tr td, #idcardtable2 tr td { font-size: .88rem; font-weight: 600; }
    @media (max-width: 767px) {
      .modal-dialog { width: 100% !important; max-width: 100% !important; }
      #id_cardmodal input[type="text"], #id_cardmodal label:not(.lblidcardtitle), #id_cardmodal select, #id_cardmodal input[type="date"], #id_cardmodal input[type="number"] { font-size: .72rem; }
      #id_cardmodal td, #id_cardmodal th { padding: 1px; }
    }
    /* Adjust row height in candidates table */
    #dtcandidates tbody tr { line-height: 0.7rem; }
  </style>
</head>
<body>
<?php
  $sett_id_var = false;
  if ($setting == null) { $sett_id_var = true; }
  if (isset($setting->settings_value) && $setting->settings_value == '1') { $sett_id_var = true; }
?>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<!-- Loading bar -->
<div class="loading-container" style="display:none;">
  <div class="progress">
    <div class="progress-bar" role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
</div>

<!-- Overlay -->
<div id="overlay"></div>

<!-- Alert messages -->
<div id="pageMessages"></div>

<!-- Navbar -->
<nav class="gn-bar">
  <div class="d-flex align-items-center gap-3">
    <a class="gn-brand" href="#">
      @if($users)
        @if($users->picture)
          <img src="{{ URL('../profile_picture/' . $users->picture) }}" alt="صورة">
        @else
          <img src="{{ URL('images/logo.webp') }}" alt="شعار">
        @endif
        <span>{{ $users->full_name }}</span>
      @endif
    </a>
    @if($isleader)
      <a class="gn-leader-link" href="{{ route('leaderdash') }}">
        <i class="fas fa-users"></i> أفراد المجموعة
      </a>
    @endif
  </div>
  <a class="gn-logout" href="{{ route('logout', ['profile_code' => $users->profile_code]) }}">
    <i class="fas fa-sign-out-alt"></i> خروج
  </a>
</nav>

<!-- Main -->
<div class="g-page" dir="rtl">
  <div class="container-fluid" style="max-width:1100px;">

    {{-- Election name + action buttons --}}
    <div class="g-card">
      <div class="g-card-header">
        <div class="g-card-header-left">
          <div class="g-header-icon"><i class="fas fa-vote-yea"></i></div>
          <span>
            @if($electionobj) {{ $electionobj->election_name }} @else العملية الانتخابية @endif
          </span>
        </div>
        <span id="round_count" style="color:#f0c94d;font-size:.9rem;font-weight:600;"></span>
      </div>
      <div class="g-card-body text-center py-3">
        <div class="d-flex justify-content-center gap-3 flex-wrap">
          <button class="btn-g-primary @if(!$any_results_exist) no_drop @endif"
                  id="btn_results"
                  onclick="handleButtonClick();"
                  @if(!$any_results_exist) disabled @endif>
            <i class="fas fa-poll"></i>
            @if(!$any_results_exist) لم تصدر النتائج بعد @else النتائج @endif
          </button>
          @if(!$isvotedbefore)
          <button class="btn-g-primary" id="btn_vote" onclick="showsubmitmodal();">
            <i class="fas fa-check-square"></i> تصويت
          </button>
          @endif
        </div>
      </div>
    </div>

    {{-- Lists table --}}
    <div class="g-card">
      <div class="g-card-header">
        <div class="g-card-header-left">
          <div class="g-header-icon"><i class="fas fa-list-ul"></i></div>
          <span>اللوائح</span>
        </div>
      </div>
      <div class="g-card-body p-0">
        <div class="table-responsive">
          <table id="dtcandidateslist" class="table table-sm mb-0 text-center">
            <thead>
              <tr>
                <th>اللائحة</th>
                <th>المرشحين</th>
                <th>للفوز</th>
                <th>المختارين</th>
                <th style="display:none;">candidate_list_code</th>
              </tr>
            </thead>
            <tbody>
              @if($candidate_groups)
                @foreach ($candidate_groups as $candidate_groupobj)
                  <?php
                    $resvar = 0;
                    if (isset($results_exists[$candidate_groupobj->group_code])) {
                      $resvar = $results_exists[$candidate_groupobj->group_code];
                    }
                    if ($candidate_groupobj->win_number - $resvar > 0) {
                  ?>
                  <tr>
                    <th>{{ $candidate_groupobj->group_name }}</th>
                    <th>{{ $candidate_groupobj->candidates_number }}</th>
                    <th>{{ $candidate_groupobj->win_number - $resvar }}</th>
                    <th>0</th>
                    <th style="display:none;">{{ $candidate_groupobj->group_code }}</th>
                  </tr>
                  <?php } ?>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Candidates table --}}
    <div class="g-card">
      <div class="g-card-header">
        <div class="g-card-header-left">
          <div class="g-header-icon"><i class="fas fa-users"></i></div>
          <span>المرشحون</span>
        </div>
      </div>
      <div class="g-card-body">

        {{-- Hidden stats --}}
        <div style="display:none;">
          @if($candidates)
            <span id="candidatenumber">عدد المرشحين : {{ sizeof($candidates) }}</span>
            <span id="listwinnumber">العدد المطلوب للفوز: {{ sizeof($candidates) }}</span>
          @endif
        </div>

        {{-- Hidden list combo --}}
        <div style="display:none;">
          <select id="election_list_combo">
            @if($candidate_groups)
              @foreach ($candidate_groups as $candidate_groupobj)
                <?php
                  $resvar = 0;
                  if (isset($results_exists[$candidate_groupobj->group_code])) {
                    $resvar = $results_exists[$candidate_groupobj->group_code];
                  }
                  if ($candidate_groupobj->win_number - $resvar > 0) {
                ?>
                <option value="{{ $candidate_groupobj->group_code }}">{{ $candidate_groupobj->group_name }}</option>
                <?php } ?>
              @endforeach
            @endif
          </select>
        </div>

        <div class="mb-3">
          <div class="g-search-wrap">
            <i class="fas fa-search g-search-icon"></i>
            <input type="text" id="searchInput" placeholder="بحث في الأسماء..." autocomplete="off">
          </div>
        </div>

        <div class="table-responsive">
          <table id="dtcandidates" class="table table-sm text-center">
            <thead>
              <tr>
                <th>الإسم</th>
                <th class="d-none d-sm-table-cell">الجنس</th>
                <th class="d-none d-sm-table-cell">العنوان</th>
                <th style="display:none;">الصورة</th>
                <th style="display:none;">الملفات</th>
                <th @if(!$sett_id_var) style="display:none;" @endif>بطاقة تعريفية</th>
                <th style="display:none;">profile_code</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Vote confirm modal --}}
<div class="modal fade gm-modal" id="vote_submit_modal" tabindex="-1" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i> الموافقة على التصويت</h5>
        <h5 class="modal-title" id="leadergrouptitle"></h5>
        <input type="hidden" id="input_leadername" value="">
        <input type="hidden" id="input_groupname" value="">
        <button type="button" class="close ms-auto" data-bs-dismiss="modal" aria-label="Close"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table id="datatable_vote_modal" class="table table-sm table-bordered text-center">
          <thead>
            <tr style="font-weight:bold;">
              <th>اللائحة</th>
              <th>اسم المرشح</th>
              <th style="display:none;"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">عودة</button>
        @if($isvoter)
        <button type="button" class="btn-modal-primary" onclick="submitVotes();" id="btn_sendvote">
          <i class="fas fa-vote-yea"></i> تصويت
        </button>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Alert dialog --}}
<div class="modal fade gm-modal" id="promptdialog" tabindex="-1" aria-hidden="true" dir="rtl">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="alert alert-warning mb-0" id="alert_message"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">إغلاق</button>
      </div>
    </div>
  </div>
</div>

{{-- ID Card modal --}}
<div class="modal fade gm-modal" id="id_cardmodal" tabindex="-1" aria-hidden="true" dir="rtl">
  <form id="idcardmanagerform" action="/saveprofileextrainfo" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="idcard_input_profile_code" name="idcard_input_profile_code" value="">
    <input type="hidden" id="idcard_alldata" name="idcard_alldata" value="">
    <div class="modal-dialog modal-xl" role="document" style="max-width:750px;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-address-card me-2"></i> بطاقة تعريفية للمرشح</h5>
          <div class="d-flex gap-2 ms-auto me-3">
            <button type="button" class="btn btn-outline-light btn-circle btn-sm" id="btn_idcard_clear" onclick="resetidcardcomp();" title="إعادة تعيين"><i class="fa fa-arrows-rotate"></i></button>
            <button type="button" class="btn btn-outline-warning btn-circle btn-sm" id="btn_idcard_edit" onclick="showeditidcardcomp();" title="تعديل"><i class="fa fa-edit"></i></button>
            <button type="submit" class="btn btn-success btn-circle btn-sm" id="btn_idcard_success" title="حفظ"><i class="fa fa-check"></i></button>
            <button type="button" class="btn btn-danger btn-circle btn-sm" id="btn_idcard_close" data-bs-dismiss="modal" title="إغلاق"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="modal-body">
          <div class="row align-items-center mb-3">
            <div class="col-md-4 text-center">
              <div id="image-preview" class="mt-2">
                <img src="" class="rounded-circle card-img-top mt-2 img-fluid" id="idcard_picture">
              </div>
              <label class="btn btn-outline-secondary btn-sm mt-2" id="lblimageidcard">
                <i class="fas fa-upload"></i> تحميل صورة
                <input type="file" id="profile_picture" name="profile_picture" style="display:none;" accept="image/*" onchange="previewImage(this)">
              </label>
              <p class="mt-1 small text-muted" id="lblidcardbtnimage">الصيغ المدعومة: JPG، PNG، GIF</p>
            </div>
            <div class="col-md-8 text-center">
              <label class="lblidcardtitle">بطاقة تعريفية للمرشح</label>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-12">
              <table class="table custom-table" style="table-layout:fixed;" id="idcardtable1">
                <tbody>
                  <tr>
                    <td style="border-bottom:none;text-align:right;width:50%">
                      <label id="idcard_lbl_fullname"></label>
                      <input type="text" class="form-control" id="idcard_input_fullname" name="idcard_input_fullname" placeholder="الاسم الثلاثي" autocomplete="off" style="display:none;">
                    </td>
                  </tr>
                  <tr>
                    <td style="width:50%">
                      <label>المواليد: </label>
                      <label id="idcard_lbl_age" style="font-weight:lighter;">age</label>
                      <input type="date" id="idcard_input_age" name="idcard_input_age" style="display:none;" class="form-control">
                    </td>
                    <td style="width:30%">
                      <label>الجنسية: </label>
                      <label id="idcard_lbl_nationality" style="font-weight:lighter;"></label>
                      <input type="text" class="form-control" id="idcard_input_nationality" name="idcard_input_nationality" style="display:none;">
                    </td>
                    <td style="width:20%">
                      <label>الفئة: </label>
                      <label id="idcard_lbl_category" style="font-weight:lighter;"></label>
                      <select id="idcard_input_category" name="idcard_input_category" class="form-select form-select-sm"></select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <table class="table table-bordered" style="table-layout:fixed;" id="idcardtable2">
                <tbody>
                  <tr>
                    <td>
                      <label>الوضع الاجتماعي: </label>
                      <label id="idcard_lbl_social_situation" style="font-weight:lighter;"></label>
                      <select id="idcard_input_social_situation" name="idcard_input_social_situation" class="form-select form-select-sm"></select>
                    </td>
                    <td>
                      <label>عدد الأولاد <span id="lblchilnbhint">(_/_/)</span>: </label>
                      <label id="idcard_lbl_children" style="font-weight:lighter;"></label>
                      <input type="text" class="form-control" id="idcard_input_children" name="idcard_input_children" placeholder="عدد الأولاد" autocomplete="off" style="display:none;">
                    </td>
                    <td>
                      <label>سنة الولادة: </label>
                      <label id="idcard_lbl_children_age" style="font-weight:lighter;"></label>
                      <input type="text" class="form-control" id="idcard_input_children_age" name="idcard_input_children_age" placeholder="أعمارهم" autocomplete="off" style="display:none;">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <table class="table table-bordered" id="residencetable">
                <tbody>
                  <tr>
                    <td rowspan="2" style="width:120px;"><label>السكن الحالي</label></td>
                    <td colspan="2">
                      <label id="idcard_lbl_residence" style="font-weight:lighter;"></label>
                      <input type="text" class="form-control" id="idcard_input_residence" name="idcard_input_residence" placeholder="السكن الحالي" autocomplete="off" style="display:none;">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <table class="table table-bordered" style="table-layout:fixed;" id="idcardtable2">
                <tbody>
                  <tr>
                    <td>
                      <label>التعليم: </label>
                      <label id="idcard_lbl_education" style="font-weight:lighter;"></label>
                      <input type="text" class="form-control" id="idcard_input_education" name="idcard_input_education" placeholder="التعليم" autocomplete="off" style="display:none;">
                    </td>
                    <td>
                      <label>العمل الحالي: </label>
                      <label id="idcard_lbl_current_work" style="font-weight:lighter;"></label>
                      <input type="text" class="form-control" id="idcard_input_current_work" name="idcard_input_current_work" placeholder="العمل الحالي" autocomplete="off" style="display:none;">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <table class="table table-bordered" style="table-layout:fixed;" id="idcardtable2">
                <tbody>
                  <tr>
                    <td><label>تاريخ الانتساب للجمعية: </label></td>
                    <td>
                      <label id="idcard_lbl_joiningdate" style="font-weight:lighter;">joiningdate</label>
                      <input type="date" id="idcard_input_joiningdate" name="idcard_input_joiningdate" style="display:none;" class="form-control">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <table class="table table-bordered" style="table-layout:fixed;" id="idcardworkat">
                <thead>
                  <tr class="custom-table">
                    <td colspan="4"><label>تاريخ العمل في الجمعية</label></td>
                  </tr>
                  <tr class="text-center custom-table" style="font-weight:bold;">
                    <th><label>من</label></th>
                    <th><label>إلى</label></th>
                    <th><label>طبيعة العمل في الجمعية</label></th>
                    <td class="p-2">
                      <button type="button" class="btn btn-primary btn-sm" id="add_row_work"><i class="fas fa-plus"></i></button>
                    </td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="col-md-12">
              <table class="table table-bordered" style="table-layout:fixed;" id="idcardtable2">
                <tbody>
                  <tr>
                    <td><label>معرّفون:</label></td>
                    <td>
                      <label id="idcard_lbl_identifiers" style="font-weight:lighter;">identifiers</label>
                      <input type="text" class="form-control" id="idcard_input_identifiers" name="idcard_input_identifiers" placeholder="معرّفون" autocomplete="off" style="display:none;">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <table class="table table-bordered" style="table-layout:fixed;" id="idcardtable2">
                <tbody>
                  <tr>
                    <td rowspan="2"><label>كلمة من المرشح عن الجمعية:</label></td>
                    <td>
                      <label id="idcard_lbl_word_about_association" style="font-weight:lighter;">word_about_association</label>
                      <input type="text" class="form-control" id="idcard_input_word_about_association" name="idcard_input_word_about_association" placeholder="كلمة من المرشح عن الجمعية" autocomplete="off" style="display:none;">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha3/0.8.0/sha3.min.js"></script>
<script>
  var g_ssocial_situation = ['', 'عازب', 'متزوج', 'أرمل', 'مطلق'];
  var g_education = ['ثانوي', 'جامعي'];
  var json_obj_array = [];
  var count_round = "{{$count_round}}";
  if (count_round > 0) {
    $('#round_count').text("(الجولة " + numToWordsAR_M(count_round) + ")");
  }

  var sum_win_number = 0;
  var group_win_number_arr = [];
  var group_win_counter = 0;
  var g_rowIndex = 0;

  let progressBar = document.querySelector('.progress-bar');
  let loadingContainer = document.querySelector('.loading-container');

  $('#dtcandidateslist tbody tr:first').addClass('highlight');

  var app_aeskey = "{{env('APP_AESKEY')}}";
  var app_aesiv  = "{{env('APP_AESIV')}}";
  var encrypteduser_code = CryptoJS.AES.encrypt(CryptoJS.enc.Utf8.parse("{{$users->user_code}}"), app_aeskey, {
    iv: app_aesiv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.Pkcs7
  });

  var hashMapJson = {
    "usercode": "{{$users->user_code}}",
    "electioncode": "{{$electioncode}}",
    "round_number": "{{$count_round}}",
    "info": {}
  };

  $('#election_list_combo option').each(function() { var group_code = $(this).val(); });

  fetchdata($('#election_list_combo').val());
  var json_obj_array = [];

  $(document).ready(function() {
    resetidcardcomp();
    $('#id_cardmodal').on('hidden.bs.modal', function(e) { resetidcardcomp(); });
    $('#election_list_combo').change(function() { fetchdata($('#election_list_combo').val()); });

    $('#dtcandidateslist tbody').on('click', 'tr', function(e) {
      if (!$(e.target).hasClass('exclude-cell')) {
        $('#dtcandidateslist tbody tr').removeClass('highlight');
        g_rowIndex = $(this).closest('tr').index();
        var $row = $('#dtcandidateslist tbody').find('tr').eq(g_rowIndex);
        var selectedValue = $row.find('th').eq(4).text();
        if (hashMapJson["info"][selectedValue] != null) {
          json_obj_array = hashMapJson["info"][selectedValue];
        } else {
          json_obj_array = [];
        }
        $(this).toggleClass('highlight');
        var cellValue = $(this).find('th').eq(4).text();
        fetchdata(cellValue);
      }
    });

    $('#dtcandidates tbody').on('click', 'tr', function(e) {
      var isvotedbefore = '{{$isvotedbefore}}';
      var $row = $('#dtcandidateslist tbody').find('tr').eq(g_rowIndex);
      var tdindexclicked = $(e.target).closest('td').index();
      if (isvotedbefore != true) {
        if (tdindexclicked != 3 && tdindexclicked != 4 && tdindexclicked != 6) {
          if ($(e.target).hasClass('exclude-cell') == false) {
            var selectedValue = $row.find('th').eq(4).text();
            var group_win_number_var = $row.find('th').eq(2).text();
            var cellValue = $(this).find('td').eq(5).text();
            if ($(this).hasClass('highlight')) {
              var index = json_obj_array.indexOf(cellValue);
              if (index !== -1) {
                json_obj_array.splice(index, 1);
                hashMapJson["info"][selectedValue] = json_obj_array;
              }
              $(this).removeClass('highlight');
              reorderTableRows();
              group_win_counter--;
            } else {
              if (group_win_counter < group_win_number_var) {
                group_win_counter++;
                json_obj_array.push(cellValue);
                hashMapJson["info"][selectedValue] = json_obj_array;
                var row = $(this).hide();
                $(row).addClass('highlight');
                $(row).prependTo('#dtcandidates tbody');
                $(row).fadeIn("slow");
              } else {
                createAlert('', 'عدد المرشحين في هذه اللائحة تخطى المطلوب', '', 'danger', true, true, 'pageMessages');
              }
            }
            $row.find('th').eq(3).text(group_win_counter);
          }
        }
      }
    });
  });

  $(".sendButton .close").click(function() { $(".alert").hide('medium'); });

  function imageExists(url, callback) {
    var img = new Image();
    img.onload = function() { callback(true); };
    img.onerror = function() { callback(false); };
    img.src = url;
  }

  function formatDate(date) {
    var year = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var day = ('0' + date.getDate()).slice(-2);
    return year + '-' + month + '-' + day;
  }

  function fetchidcard(prfcode) {
    var electioncode = "{{$electioncode}}";
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) { clearInterval(interval); loadingContainer.style.display = 'none'; }
      else { progressBar.style.width = progress + '%'; progressBar.setAttribute('aria-valuenow', progress); }
    }, 500);
    fetch('/getidcard/' + prfcode + '/' + electioncode)
      .then(response => response.json())
      .then(data => {
        var data_idcard = data.idcard;
        var data_elections_lists = data.elections_lists;
        $.each(data_elections_lists, function(index, obj) {
          $('#idcard_input_category').append($('<option>', { value: obj.group_name, text: obj.group_name }));
        });
        var imageUrl = "../profile_picture/" + data_idcard.picture;
        imageExists(imageUrl, function(exists) {
          $('#idcard_picture').attr('src', exists ? imageUrl : "../images/noimage.png");
        });
        $('#idcard_input_profile_code').val(data_idcard.profile_code);
        $('#idcard_lbl_fullname').text(data_idcard.full_name);
        $('#idcard_input_fullname').val(data_idcard.full_name);
        var dateObj = new Date(data_idcard.age);
        let datevar = dateObj.toLocaleDateString('ar-EG-u-nu-latn', { weekday:'long', year:'numeric', month:'short', day:'numeric' });
        $('#idcard_lbl_age').text(datevar);
        $('#idcard_input_age').val(formatDate(dateObj));
        var desired_nationality = (data_idcard.nationality !== null) ? data_idcard.nationality : '';
        $('#idcard_lbl_nationality').text(desired_nationality);
        $("#idcard_input_nationality").val(desired_nationality);
        $('#idcard_lbl_category').text(data_idcard.category);
        $('#idcard_input_category').val(data_idcard.category);
        var desired_social = (data_idcard.social_situation !== null) ? data_idcard.social_situation : '';
        var selectedOptionText = $("#idcard_input_social_situation").find("option[value='" + desired_social + "']").text();
        $('#idcard_lbl_social_situation').text(selectedOptionText);
        $("#idcard_input_social_situation").find("option[value='" + desired_social + "']").prop("selected", true);
        $('#idcard_lbl_children').text(data_idcard.children);
        $('#idcard_input_children').val(data_idcard.children);
        $('#idcard_lbl_children_age').text(data_idcard.children_age);
        $('#idcard_input_children_age').val(data_idcard.children_age);
        $('#idcard_lbl_residence').text(data_idcard.residence);
        $('#idcard_input_residence').val(data_idcard.residence);
        $('#idcard_lbl_education').text(data_idcard.education);
        $('#idcard_input_education').val(data_idcard.education);
        $('#idcard_lbl_current_work').text(data_idcard.current_work);
        $('#idcard_input_current_work').val(data_idcard.current_work);
        $('#idcard_lbl_joiningdate').text('');
        $('#idcard_input_joiningdate').val('');
        if (data_idcard.joiningdate != null) {
          var joiningdateObj = new Date(data_idcard.joiningdate);
          let joiningdatevar = joiningdateObj.toLocaleDateString('ar-EG-u-nu-latn', { weekday:'long', year:'numeric', month:'short', day:'numeric' });
          $('#idcard_lbl_joiningdate').text(joiningdatevar);
          $('#idcard_input_joiningdate').val(formatDate(joiningdateObj));
        }
        $('#idcardworkat tbody').empty();
        var parsedData = JSON.parse(data_idcard.work_at_association);
        if (parsedData != null) {
          parsedData.sort(function(a, b) {
            if (a.to_year < b.to_year) return 1;
            if (a.to_year > b.to_year) return -1;
            if (a.from_year < b.from_year) return 1;
            if (a.from_year > b.from_year) return -1;
            return 0;
          });
        }
        $.each(parsedData, function(index, item) {
          var newRow = '<tr class="text-center" style="font-weight:bold;">' +
            '<td><label class="lblworkat" style="font-weight:lighter;">' + item.from_year + '</label><input type="number" class="form-control inputworkat" name="input_from_year[]" placeholder="من تاريخ" style="display:none;" value="' + item.from_year + '"></td>' +
            '<td><label class="lblworkat" style="font-weight:lighter;">' + item.to_year + '</label><input type="number" class="form-control inputworkat" name="input_to_year[]" placeholder="إلى تاريخ" style="display:none;" value="' + item.to_year + '"></td>' +
            '<td><label class="lblworkat" style="font-weight:lighter;">' + item.work_description + '</label><input type="text" class="form-control inputworkat" name="work_description[]" style="display:none;" value="' + item.work_description + '"></td>' +
            '<td class="p-3"><button type="button" class="btn btn-danger btn-sm removeRowBtn" style="display:none;"><i class="fas fa-trash"></i></button></td>' +
            '</tr>';
          $('#idcardworkat tbody').append(newRow);
        });
        var resultString = "";
        if (data_idcard.identifiers != null) {
          var arr = data_idcard.identifiers.split("/");
          for (var i = 0; i < arr.length; i++) { resultString += arr[i]; if (i < arr.length - 1) resultString += "/"; }
        }
        $('#idcard_lbl_identifiers').text(resultString);
        $('#idcard_input_identifiers').val(resultString);
        $('#idcard_lbl_word_about_association').text(data_idcard.word_about_association);
        $('#idcard_input_word_about_association').val(data_idcard.word_about_association);
        var session_profile_code = '{{session("profile_code","")}}';
        if (session_profile_code == prfcode) {
          $('#btn_idcard_edit').show(); $('#btn_idcard_success').show();
          $('#btn_idcard_clear').show(); $('#lblidcardbtnimage').show();
        } else {
          $('#btn_idcard_edit').hide(); $('#btn_idcard_success').hide();
          $('#btn_idcard_clear').hide(); $('#lblidcardbtnimage').hide();
        }
        $('#idcardworkat tr').each(function() { $(this).find('td:last-child').hide(); });
        $('#idcardworkat tr td:first').attr('colspan', '3');
        new bootstrap.Modal(document.getElementById('id_cardmodal')).show();
        success_var = 1;
      })
      .catch(error => { console.error('Error fetching data:', error); });
  }

  function showeditidcardcomp() {
    $('#idcard_lbl_fullname').show(); $('#idcard_input_fullname').hide();
    $('#idcard_lbl_age').hide(); $('#idcard_input_age').show();
    $('#idcard_lbl_nationality').hide(); $('#idcard_input_nationality').show();
    $('#idcard_lbl_category').hide(); $('#idcard_input_category').show();
    $('#idcard_lbl_social_situation').hide(); $('#idcard_input_social_situation').show();
    $('#idcard_lbl_children').hide(); $('#idcard_input_children').show();
    $('#idcard_lbl_children_age').hide(); $('#idcard_input_children_age').show();
    $('#idcard_lbl_residence').hide(); $('#idcard_input_residence').show();
    $('#idcard_lbl_education').hide(); $('#idcard_input_education').show();
    $('#idcard_lbl_current_work').hide(); $('#idcard_input_current_work').show();
    $('#idcard_lbl_joiningdate').hide(); $('#idcard_input_joiningdate').show();
    $('#idcard_lbl_identifiers').hide(); $('#idcard_input_identifiers').show();
    $('#idcard_lbl_word_about_association').hide(); $('#idcard_input_word_about_association').show();
    $('.lblworkat').hide(); $('.inputworkat').show();
    $('#add_row_work').show(); $('.removeRowBtn').show();
    $('#lblimageidcard').show(); $('#lblidcardbtnimage').show(); $('#lblchilnbhint').show();
    $('#idcardworkat tr').each(function() { $(this).find('td:last-child').show(); });
    $('#idcardworkat tr td:first').attr('colspan', '4');
  }

  function resetidcardcomp() {
    $('#idcard_lbl_fullname').show(); $('#idcard_input_fullname').hide();
    $('#idcard_lbl_age').show(); $('#idcard_input_age').hide();
    $('#idcard_lbl_nationality').show(); $('#idcard_input_nationality').hide();
    $('#idcard_lbl_category').show(); $('#idcard_input_category').hide();
    $('#idcard_lbl_social_situation').show(); $('#idcard_input_social_situation').hide();
    $('#idcard_lbl_children').show(); $('#idcard_input_children').hide();
    $('#idcard_lbl_children_age').show(); $('#idcard_input_children_age').hide();
    $('#idcard_lbl_residence').show(); $('#idcard_input_residence').hide();
    $('#idcard_lbl_education').show(); $('#idcard_input_education').hide();
    $('#idcard_lbl_current_work').show(); $('#idcard_input_current_work').hide();
    $('#idcard_lbl_joiningdate').show(); $('#idcard_input_joiningdate').hide();
    $('#idcard_lbl_identifiers').show(); $('#idcard_input_identifiers').hide();
    $('#idcard_lbl_word_about_association').show(); $('#idcard_input_word_about_association').hide();
    $('.lblworkat').show(); $('.inputworkat').hide();
    $('#idcardworkat tbody tr#bsh_delete').remove();
    $('#add_row_work').hide(); $('.removeRowBtn').hide();
    $('#lblimageidcard').hide(); $('#lblidcardbtnimage').hide(); $('#lblchilnbhint').hide();
    $('#idcardworkat tr').each(function() { $(this).find('td:last-child').hide(); });
    $('#idcardworkat tr td:first').attr('colspan', '3');
    document.getElementById('idcardmanagerform').reset();
  }

  $('#searchInput').on('keyup', function() {
    var searchText = $(this).val().toLowerCase();
    $('#dtcandidates tbody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
    });
  });

  function fetchdata(groupcode) {
    $('#dtcandidates tr:gt(0)').remove();
    group_win_counter = 0;
    var round_number = "{{$count_round}}";
    var electioncode = "{{$electioncode}}";
    var json_obj_var = hashMapJson["info"][groupcode];
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) { clearInterval(interval); loadingContainer.style.display = 'none'; }
      else { progressBar.style.width = progress + '%'; progressBar.setAttribute('aria-valuenow', progress); }
    }, 500);
    fetch('/candidatemanager/' + electioncode + '/' + groupcode + '/' + round_number)
      .then(response => response.json())
      .then(data => {
        var candidates_arr = data.candidates;
        group_win_number_arr = data.group_win_number;
        sum_win_number = data.sum_win_number;
        $('#candidatenumber').text("عدد المرشحين : " + candidates_arr.length);
        $('#listwinnumber').text("العدد المطلوب للفوز: " + group_win_number_arr[groupcode]);
        $('#dtcandidates tbody').empty();
        candidates_arr.forEach(function(candidate, index) {
          var sex_arr = ['ذكر', 'أنثى'];
          var newRow = '<tr class="text-center">' +
            '<td>' + candidate.full_name + '</td>' +
            '<td class="d-none d-sm-table-cell">' + sex_arr[candidate.sex - 1] + '</td>' +
            '<td class="d-none d-sm-table-cell">' + candidate.address + '</td>' +
            '<td class="p-3 exclude-cell" style="display:none;"><a target="_blank" href="../profile_picture/' + candidate.picture + '" class="exclude-cell" onclick="return openProfilePicture(`profile_picture/' + candidate.picture + '`)"><i class="fas fa-file-image exclude-cell"></i></a></td>' +
            '<td class="p-3 exclude-cell" style="display:none;"><a target="_blank" href="../profile_attachment/' + candidate.attachment + '" class="exclude-cell" onclick="return openProfilePicture(`profile_attachment/' + candidate.attachment + '`)"><i class="fas fa-file-pdf exclude-cell" style="color:red;"></i></a></td>' +
            '<td style="display:none;">' + candidate.profile_code + '</td>' +
            '<td class="p-2" @if(!$sett_id_var) style="display:none;" @endif><button type="button" class="btn btn-primary btn-sm exclude-cell" onclick="showidcardmodal(`' + candidate.profile_code + '`)" ><i class="fa fa-address-card exclude-cell"></i></button></td>' +
            '</tr>';
          $('#dtcandidates').append(newRow);
        });
        $('#dtcandidates tbody tr').each(function(index, element) {
          var cellValue = $(this).find('td').eq(5).text();
          if (json_obj_var != undefined || json_obj_var != null) {
            var idx = json_obj_var.indexOf(cellValue);
            if (idx !== -1) {
              group_win_counter++;
              var row = $(this).hide();
              $(row).addClass('highlight');
              $(row).prependTo('#dtcandidates tbody');
              $(row).fadeIn("slow");
            }
          }
        });
        success_var = 1;
      })
      .catch(error => { console.error('Error fetching data:', error); });
  }

  function showidcardmodal(prfcode) {
    $('#idcard_picture').attr('src', "../images/noimage.png");
    fetchidcard(prfcode);
  }

  function reorderTableRows() {
    var $tbody = $('#dtcandidates tbody');
    var $rows = $tbody.find('tr').get();
    $rows.sort(function(a, b) {
      var keyA = $(a).hasClass('highlight');
      var keyB = $(b).hasClass('highlight');
      if (keyA === keyB) return 0;
      return keyA ? -1 : 1;
    });
    $tbody.empty();
    $.each($rows, function(index, row) { $tbody.append(row); });
  }

  function checkIfCandidatesChoosen() {
    var $rows = $('#dtcandidates tbody tr').get(), count = 0;
    for (var i = 0; i < $rows.length; i++) {
      if ($rows[i].classList.contains("highlight")) count++;
    }
    return count;
  }

  function showsubmitmodal() {
    $('#datatable_vote_modal tbody').empty();
    showOverlay();
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) { hideOverlay(); clearInterval(interval); loadingContainer.style.display = 'none'; }
      else { progressBar.style.width = progress + '%'; progressBar.setAttribute('aria-valuenow', progress); }
    }, 500);
    var hashMapJson_str = JSON.stringify(hashMapJson);
    fetch('/getvoterschoosen/' + hashMapJson_str)
      .then(response => response.json())
      .then(data => {
        $.each(data, function(index, dataObj) {
          var newRow = '<tr class="text-center">' +
            '<td>' + dataObj.listname + '</td>' +
            '<td>' + dataObj.full_name + '</td>' +
            '<td style="display:none;"></td></tr>';
          $('#datatable_vote_modal').append(newRow);
        });
        success_var = 1;
      })
      .catch(error => { console.error('Error:', error); });
    new bootstrap.Modal(document.getElementById('vote_submit_modal')).show();
  }

  function submitVotes() {
    showOverlay();
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) { hideOverlay(); clearInterval(interval); loadingContainer.style.display = 'none'; }
      else { progressBar.style.width = progress + '%'; progressBar.setAttribute('aria-valuenow', progress); }
    }, 500);
    bootstrap.Modal.getInstance(document.getElementById('vote_submit_modal'))?.hide();
    var usercode = '{{$users->user_code}}';
    var electioncode = "{{$electioncode}}";
    var round_count = "{{$count_round}}";
    fetch('/getvoterstatus/' + usercode + '/' + electioncode + '/' + round_count)
      .then(response => response.json())
      .then(data => {
        success_var = 1;
        if (data <= 0) {
          var election_status = "{{$count_round_status}}";
          
          if (election_status == 0) {
            createAlert('', 'هذه العملية ليست مطلقة', '', 'danger', true, true, 'pageMessages');
          } else {
            fetch('/savevote', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
              body: JSON.stringify(hashMapJson)
            })
            .then(function(response) {
              if (!response.ok) alert('Network response was not ok');
              return response.json();
            })
            .then(function(data) {
              $('#dtcandidates tbody').off('click', 'tr');
              success_var = 1;
              $('#btn_vote').prop('disabled', true);
              createAlert('', 'لقد تم تسجيل صوتك بنجاح. الرجاء الإنتظار ريثما تنتهي عملية الإقتراع', '', 'success', true, true, 'pageMessages');
            })
            .catch(function(error) { hideOverlay(); alert(error); });
          }
        }
      })
      .catch(error => { hideOverlay(); alert(error); });
  }

  function openProfilePicture(url) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', url, true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          var newWindow = window.open(url, '_blank');
          setTimeout(function() { newWindow.close(); }, 5000);
        } else {
          createAlert('', 'الملف ليس موجود', '', 'danger', true, true, 'pageMessages');
        }
      }
    };
    xhr.send();
    return false;
  }

  function numToWordsAR_M(num = 0) {
    if (num == 0) return "صفر";
    let n, N, o = "", l = false, W = " و", m = "مائة",
      L = (num = "0".repeat((num += "").length * 2 % 3) + num).length,
      S = [, "ألف", "مليون", "مليار", "ترليون", "كوادرليون"],
      T = ["", "الأولى", "الثانية", "الثالثة", "الرابعة", "خمسة", "ستة", "سبعة", "ثمانية", "تسعة", "عشرة"];
    for (let D = L; D > 0; D -= 3) {
      n = +num.substring(L - D, L - D + 3);
      l = !+num.substring(L - D + 3);
      n && (o += $(D / 3 - 1), l || (o += "" + W));
    }
    return o;
    function $(P) {
      let s = S[P], h = ~~(n / 100), u = (N = n % 100) % 10, t = ~~(N / 10), H = "", wN = "";
      if (h) { if (h > 2) H = T[h].slice(0, (h == 8 ? -2 : -1)) + m; else if (h == 1) H = m; else H = m.slice(0, -1) + (s && !N ? "تا" : "تان"); }
      if (N > 19) wN = T[u] + (u ? W : "") + (t == 2 ? "عشر" : T[t].slice(0, (t == 8 ? -2 : -1))) + "ون";
      else if (N > 10) wN = (u == 1 ? "أحد" : (u == 2 ? "اثنا" : T[u])) + " عشر";
      else if (N > 2 || !N) wN = T[N]; else wN = T[N];
      let w = H + (h && N ? W : "") + wN;
      if (!s) return w;
      if (N > 2) return w + " " + (N > 10 ? s + "ًا" : (P < 3 ? [, "آلاف", "ملايين"][P] : S[P] + "ات"));
      if (!N) return w + " " + s;
      w = (h ? H + W : "") + s;
      return (N == 1) ? w : w + "ان";
    }
  }

  function handleButtonClick() {
    if (document.getElementById('btn_results').disabled) {
      createAlert('', 'لم تصدر النتائج بعد', '', 'danger', true, true, 'pageMessages');
    } else {
      showguestresults();
    }
  }

  function showguestresults() {
    var url = '{{ route("guestresults", ":eleccode") }}';
    url = url.replace(':eleccode', '{{$electioncode}}');
    window.location.href = url;
  }

  function showOverlay() { $('#overlay').show(); }
  function hideOverlay() { $('#overlay').hide(); }

  function createAlert(title, summary, details, severity, dismissible, autoDismiss, appendToId) {
    var iconMap = { info:"fa fa-info-circle", success:"fa fa-thumbs-up", warning:"fa fa-exclamation-triangle", danger:"fa fa-exclamation-circle" };
    var iconAdded = false;
    var alertClasses = ["alert", "animated", "flipInX", "alert-" + severity.toLowerCase()];
    if (dismissible) alertClasses.push("alert-dismissible");
    var msgIcon = $("<i />", { "class": iconMap[severity] });
    var msg = $("<div />", { "class": alertClasses.join(" ") });
    if (title) { var msgTitle = $("<h4 />", { html: title }).appendTo(msg); if (!iconAdded) { msgTitle.prepend(msgIcon); iconAdded = true; } }
    if (summary) { var msgSummary = $("<strong />", { html: summary }).appendTo(msg); if (!iconAdded) { msgSummary.prepend(msgIcon); iconAdded = true; } }
    if (details) { var msgDetails = $("<p />", { html: details }).appendTo(msg); if (!iconAdded) { msgDetails.prepend(msgIcon); iconAdded = true; } }
    if (dismissible) { $("<span />", { "class":"close", "data-dismiss":"alert", html:"<i class='fa fa-times-circle'></i>" }).appendTo(msg); }
    $('#' + appendToId).prepend(msg);
    if (autoDismiss) { setTimeout(function() { msg.addClass("flipOutX"); setTimeout(function() { msg.remove(); }, 1000); }, 5000); }
  }

  $(document).ready(function() {
    $('#idcard_input_social_situation').empty();
    resetidcardcomp();
    for (var i = 0; i < g_ssocial_situation.length; i++) {
      $('#idcard_input_social_situation').append($('<option>', { value: i, text: g_ssocial_situation[i] }));
    }
  });

  function previewImage(input) {
    var preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        var img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'img-fluid';
        img.style.maxWidth = window.innerWidth <= 767 ? '50px' : '100px';
        preview.appendChild(img);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  $('#add_row_work').click(function() {
    var rowCount = $('#idcardworkat tbody tr').length + 1;
    if (rowCount <= 6) {
      var newRow = '<tr class="text-center" style="font-weight:bold;" id="bsh_delete">' +
        '<td><input type="number" class="form-control inputworkat" name="input_from_year[]" placeholder="من تاريخ"></td>' +
        '<td><input type="number" class="form-control inputworkat" name="input_to_year[]" placeholder="إلى تاريخ"></td>' +
        '<td><input type="text" class="form-control inputworkat" name="work_description[]" placeholder="طبيعة العمل في الجمعية"></td>' +
        '<td class="p-3"><button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
      $('#idcardworkat tbody').append(newRow);
    }
  });

  $('#idcardworkat').on('click', '.removeRowBtn', function() { $(this).closest('tr').remove(); });

  function getBase64(file) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = () => resolve(reader.result);
      reader.onerror = error => reject(error);
    });
  }
</script>
</body>
</html>
