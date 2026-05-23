@extends('layouts.app')

@section('content')

<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<style>
  /* ── Page background — matches login ── */
  body {
    background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%) !important;
  }
  body::after {
    content: '';
    position: fixed;
    inset: 0;
    background-image: radial-gradient(circle, rgba(30,58,112,0.05) 1.5px, transparent 1.5px);
    background-size: 28px 28px;
    pointer-events: none;
    z-index: 0;
  }
  .orb {
    position: fixed; border-radius: 50%;
    filter: blur(100px); pointer-events: none;
    opacity: 0.16; animation: orbFloat 12s ease-in-out infinite; z-index: 0;
  }
  .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px;animation-delay:0s; }
  .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
  @keyframes orbFloat {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.14) translate(18px,-18px); }
  }

  /* ── Page ── */
  .ul-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem;
  }

  /* ── Card ── */
  .ul-card {
    background: #fff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }
  .ul-card::before {
    content: '';
    display: block;
    height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .ul-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    border-bottom: none;
    padding: 1.1rem 1.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    position: relative;
  }
  .ul-card-header::after {
    content: '✦';
    position: absolute;
    left: 1.5rem; top: 50%;
    transform: translateY(-50%);
    color: rgba(212,168,32,0.3);
    font-size: 1rem;
  }

  .ul-card-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .ul-header-icon {
    width: 40px; height: 40px;
    border-radius: 0.6rem;
    background: rgba(212,168,32,0.15);
    border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .ul-header-icon i { color: #f0c94d; font-size: 1rem; }

  .ul-card-header h5 {
    margin: 0;
    font-weight: 700;
    font-size: 1.05rem;
    color: #fff;
  }

  /* ── Search input ── */
  .ul-search {
    height: 40px;
    padding: 0 1rem 0 2.5rem;
    border-radius: 2rem;
    border: 1.5px solid #dde3ef;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23c8920a' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    font-size: 0.88rem;
    color: #0f1f40;
    min-width: 220px;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .ul-search:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    outline: none;
  }

  /* ── Toolbar buttons ── */
  .btn-ul-info {
    height: 38px; padding: 0 1rem;
    border-radius: 2rem;
    font-size: 0.82rem; font-weight: 600;
    background: rgba(255,255,255,0.12);
    border: 1.5px solid rgba(255,255,255,0.25);
    color: rgba(255,255,255,0.88);
    cursor: pointer; transition: background 0.18s, border-color 0.18s;
    display: inline-flex; align-items: center; gap: 0.4rem;
    white-space: nowrap;
  }
  .btn-ul-info:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.4);
    color: #fff;
  }

  .btn-ul-primary {
    height: 38px; padding: 0 1rem;
    border-radius: 2rem;
    font-size: 0.82rem; font-weight: 700;
    background: linear-gradient(135deg, #c8920a, #f0c94d, #c8920a);
    background-size: 200% 200%;
    animation: goldShift 5s ease infinite;
    border: none; color: #1a2e0f;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.4rem;
    white-space: nowrap;
    box-shadow: 0 3px 12px rgba(212,168,32,0.35);
    transition: transform 0.15s, box-shadow 0.18s;
  }
  .btn-ul-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 16px rgba(212,168,32,0.5);
    color: #1a2e0f;
  }
  @keyframes goldShift {
    0%,100% { background-position:0% 50%; }
    50%      { background-position:100% 50%; }
  }

  .ul-hint {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.45);
    font-weight: 600;
    text-align: center;
    display: block;
    margin-top: 0.2rem;
  }

  /* ── Progress ── */
  .ul-progress-wrap {
    display: none;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.3rem;
  }
  .ul-progress-wrap progress {
    height: 6px; border-radius: 3px; width: 120px;
    accent-color: #d4a820;
  }
  .ul-progress-wrap span { font-size: 0.75rem; color: rgba(255,255,255,0.7); font-weight: 600; }

  /* ── Table wrap ── */
  .ul-table-wrap {
    padding: 1.25rem 1.5rem 1.5rem;
    border-top: 3px solid #d4a820;
  }

  #dtprofiles {
    border-collapse: separate !important;
    border-spacing: 0;
    width: 100% !important;
  }
  #dtprofiles thead tr {
    background: linear-gradient(135deg, #f8f4e8, #fef9e7);
  }
  #dtprofiles thead th {
    font-weight: 700;
    font-size: 0.82rem;
    color: #1e3a70;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    padding: 0.75rem;
    white-space: nowrap;
    letter-spacing: 0.3px;
  }
  #dtprofiles tbody tr { transition: background 0.15s; }
  #dtprofiles tbody tr:hover { background: #fef9e7 !important; }
  #dtprofiles tbody td {
    font-size: 0.88rem;
    color: #1e3a70;
    border-color: #f0ecd8 !important;
    padding: 0.6rem 0.75rem;
    vertical-align: middle;
  }

  /* Action buttons */
  .btn-tbl-edit {
    width: 30px; height: 30px; border-radius: 0.45rem;
    background: #eef4ff; border: 1.5px solid #c2d9ff;
    color: #1e3a70; font-size: 0.78rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background 0.15s, border-color 0.15s;
  }
  .btn-tbl-edit:hover { background: #1e3a70; border-color: #1e3a70; color: #fff; }

  .btn-tbl-delete {
    width: 30px; height: 30px; border-radius: 0.45rem;
    background: #fff0f0; border: 1.5px solid #ffc9c9;
    color: #dc3545; font-size: 0.78rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background 0.15s, border-color 0.15s;
  }
  .btn-tbl-delete:hover { background: #dc3545; border-color: #dc3545; color: #fff; }

  .btn-tbl-reset {
    width: 30px; height: 30px; border-radius: 0.45rem;
    background: #f0fff4; border: 1.5px solid #b2f0c8;
    color: #198754; font-size: 0.78rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background 0.15s, border-color 0.15s;
  }
  .btn-tbl-reset:hover { background: #198754; border-color: #198754; color: #fff; }

  /* DataTables pagination */
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #1e3a70 !important; color: #fff !important;
    border-color: #1e3a70 !important; border-radius: 0.45rem;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #fef9e7 !important; color: #c8920a !important;
    border-color: rgba(212,168,32,0.3) !important; border-radius: 0.45rem;
  }
  .dataTables_wrapper .dataTables_info {
    font-size: 0.82rem; color: #64748b; padding-top: 0.5rem;
  }
  .dataTables_filter { display: none; }

  /* Image link */
  .fa-file-image { color: #c8920a !important; }

  /* Alert */
  #successMessage {
    border-radius: 0.65rem; font-size: 0.92rem;
    margin: 0 1rem; text-align: right; display: none;
  }

  /* ── Modals ── */
  .ul-modal .modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 8px 40px rgba(10,22,40,0.2);
    overflow: hidden;
  }
  .ul-modal .modal-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    border-bottom: none;
    padding: 1rem 1.25rem;
  }
  .ul-modal .modal-title {
    font-weight: 700; font-size: 1rem; color: #fff;
  }
  .ul-modal .modal-header .close,
  .ul-modal .modal-header .btn-close {
    color: rgba(255,255,255,0.7) !important;
    text-shadow: none; opacity: 1;
  }
  .ul-modal .modal-body {
    font-size: 0.92rem; color: #1e3a70; padding: 1.25rem;
    border-top: 3px solid #d4a820;
  }
  .ul-modal .modal-footer {
    border-top: 1px solid #e8edf6; padding: 0.85rem 1.25rem; gap: 0.5rem;
  }

  .btn-modal-secondary {
    height: 38px; padding: 0 1.1rem; border-radius: 0.55rem;
    font-size: 0.88rem; font-weight: 600;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    cursor: pointer; transition: background 0.18s, border-color 0.18s;
  }
  .btn-modal-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.4); }

  .btn-modal-primary {
    height: 38px; padding: 0 1.1rem; border-radius: 0.55rem;
    font-size: 0.88rem; font-weight: 700;
    background: linear-gradient(135deg, #c8920a, #f0c94d, #c8920a);
    background-size: 200% 200%; animation: goldShift 5s ease infinite;
    border: none; color: #1a2e0f;
    cursor: pointer; box-shadow: 0 2px 10px rgba(212,168,32,0.3);
    transition: transform 0.15s, box-shadow 0.18s;
  }
  .btn-modal-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(212,168,32,0.45);
    color: #1a2e0f;
  }

  .btn-modal-danger {
    height: 38px; padding: 0 1.1rem; border-radius: 0.55rem;
    font-size: 0.88rem; font-weight: 600;
    background: #dc3545; border: none; color: #fff;
    cursor: pointer; transition: background 0.18s;
  }
  .btn-modal-danger:hover { background: #bb2d3b; }

  /* Events table */
  #event_table thead th {
    font-size: 0.82rem; font-weight: 700;
    color: #1e3a70; background: #f8f4e8;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
  }
  #event_table tbody td { font-size: 0.85rem; color: #1e3a70; }

  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  .rotate { animation: spin 1s linear infinite; }

  @media (max-width: 767.98px) and (orientation: landscape) {
    .hide-on-mobile-landscape { display: none !important; }
  }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="ul-page" dir="rtl">
  <div class="container-fluid" style="max-width: 1200px;">

    <!-- Alert -->
    <div class="alert" role="alert" id="successMessage"></div>

    <div class="ul-card">

      <!-- Card header: title + toolbar -->
      <div class="ul-card-header">
        <div class="ul-card-header-left">
          <div class="ul-header-icon"><i class="fas fa-users"></i></div>
          <h5>لائحة الأسماء</h5>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap:1.25rem;">

          <!-- Search -->
          <input type="text" class="ul-search" placeholder="بحث..." id="searchInput" autocomplete="off" autofocus>

          <!-- Upload folder -->
          <div class="text-center">
            <div class="ul-progress-wrap" id="folder_progress">
              <progress id="folder_loading-progress" value="0" max="100"></progress>
              <span id="folder_progress-text">0%</span>
            </div>
            <button class="btn-ul-info" id="uploadfolder" onclick="selectFolder()">
              <i class="fas fa-file-image"></i> إضافة ملفات الصور والمستندات
            </button>
            <input type="file" id="folderInput" name="folderInput" webkitdirectory directory multiple style="display:none;" accept=".jpg,.png,.pdf">
            <span class="ul-hint">(profile_picture, profile_attachment)</span>
          </div>

          <!-- ID Card Import -->
          <div class="text-center">
            <div class="ul-progress-wrap" id="cardinfo_progress">
              <progress id="cardinfo_loading-progress" value="0" max="100"></progress>
              <span id="cardinfo_progress-text">0%</span>
            </div>
            <button class="btn-ul-info" id="importCardInfoBtn">
              <i class="fas fa-id-card"></i> اضافة بطاقة شخصية
            </button>
            <input type="file" id="cardInfoInput" name="cardInfoInput" style="display:none;" accept=".xlsx,.xls">
            <span class="ul-hint">&nbsp;</span>
          </div>

          <!-- Import Excel -->
          <div class="text-center">
            <div class="ul-progress-wrap" id="progress">
              <progress id="loading-progress" value="0" max="100"></progress>
              <span id="progress-text">0%</span>
            </div>
            <button class="btn-ul-primary" id="ImportExcelBtn">
              <i class="fas fa-file-excel"></i> Import Excel
            </button>
            <input type="file" id="fileInput" name="fileInput" style="display:none;" accept=".xlsx,.xls" multiple>
            <span class="ul-hint">(ترتيب حسب المجموعة والمرشد)</span>
          </div>

        </div>
      </div>

      <!-- Table -->
      <div class="ul-table-wrap">
        <table id="dtprofiles" class="table table-bordered table-sm" style="width:100%">
          <thead class="text-center">
            <tr>
              <th>الاسم</th>
              <th>رقم الهاتف</th>
              <th class="d-none d-sm-table-cell">الجنس</th>
              <th>الرمز</th>
              <th class="d-none d-sm-table-cell hide-on-mobile-landscape">العنوان</th>
              <th>الصورة</th>
              <th style="display:none;">profile_code</th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody class="text-center">
            @if($Profiles)
              @foreach ($Profiles as $profile)
              <tr>
                <td>{{ $profile->full_name }}</td>
                <td>{{ $profile->mobile }}</td>
                <td class="d-none d-sm-table-cell">{{ $profile->sex == 1 ? 'ذكر' : 'أنثى' }}</td>
                <td>{{ $profile->user_code }}</td>
                <td class="d-none d-sm-table-cell">{{ $profile->address }}</td>
                @if($profile->picture)
                  <td class="p-2 exclude-cell">
                    <a href="{{ URL('../profile_picture/'.$profile->picture) }}" target="_blank" class="exclude-cell"
                      onclick="return openProfilePicture('{{ URL('profile_picture/'.$profile->picture) }}')">
                      <i class="fa-solid fa-file-image exclude-cell" style="color:#0d6efd;"></i>
                    </a>
                  </td>
                @else
                  <td></td>
                @endif
                <td style="display:none;"><input type="hidden" name="profilecode" value="{{ $profile->profile_code }}"></td>
                <td class="p-1">
                  <button type="button" class="btn-tbl-edit edit-btn" onclick="callEditForm('{{$profile->profile_code}}')">
                    <i class="fas fa-edit"></i>
                  </button>
                </td>
                <td class="p-1">
                  <button type="button" class="btn-tbl-delete" data-toggle="modal" data-target="#confirmDeleteModal" data-prfcode="{{$profile->profile_code}}">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
                <td class="p-1">
                  <button type="button" class="btn-tbl-reset" onclick="showevents('{{$profile->profile_code}}')" title="إعادة تفعيل الرمز">
                    <i class="fas fa-refresh" id="refreshIcon"></i>
                  </button>
                </td>
              </tr>
              @endforeach
            @endif
          </tbody>
        </table>
        <ul id="folderList"></ul>
        <ul id="fileList"></ul>
      </div>

    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade ul-modal" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-trash me-2" style="color:#dc3545;"></i> مسح الاسم</h5>
        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p style="margin:0;">هل تريد مسح هذا الاسم؟</p>
      </div>
      <form id="modal_delete_form" action="/deleteuser" method="post">
        @csrf
        <input type="hidden" id="modal_profile_code" name="modal_profile_code">
        <div class="modal-footer">
          <button type="button" class="btn-modal-secondary" data-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn-modal-danger"><i class="fas fa-trash"></i> مسح</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Events Modal -->
<div class="modal fade ul-modal" id="eventsmodal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-xl" role="document" style="width:70%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-history me-2" style="color:#0d6efd;"></i> سجل الأحداث</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="eventsmodal_profilecode" id="eventsmodal_profilecode" value="">
        <table id="event_table" class="table table-bordered table-sm" style="width:100%">
          <thead class="text-center">
            <tr>
              <th>الرمز</th>
              <th>الحدث</th>
              <th>تسجيل الدخول</th>
              <th>الخروج</th>
            </tr>
          </thead>
          <tbody class="text-center"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn-modal-primary" data-bs-dismiss="modal" onclick="resetusercode()">
          <i class="fas fa-refresh"></i> إعادة تفعيل رمز المستخدم
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Unmatched Names Modal -->
<div class="modal fade ul-modal" id="unmatchedNamesModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2" style="color:#f0c94d;"></i> أسماء غير متطابقة</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p style="margin-bottom:0.75rem;font-weight:600;">بعض الاسماء لا تتطابق</p>
        <ul id="unmatchedNamesList" style="margin:0;padding-right:1.25rem;line-height:2;font-size:0.9rem;color:#1e3a70;"></ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">إغلاق</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
  var intervalID;
  let progressBar = document.querySelector('.progress-bar');
  let loadingContainer = document.querySelector('.loading-container');

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

  $('#confirmDeleteModal').on('shown.bs.modal', function(e) {
    $('#modal_profile_code').val($(e.relatedTarget).data('prfcode'));
  });

  function showevents(prfcode) {
    $('#eventsmodal_profilecode').val(prfcode);
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        clearInterval(interval);
        loadingContainer.style.display = 'none';
      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);
    fetch('/getevents/' + prfcode)
      .then(r => r.json())
      .then(data => {
        $('#event_table tbody').empty();
        $.each(data['events'], function(i, item) {
          var logout = item.loggedout_datetime || '';
          $('#event_table tbody').append(
            '<tr><td>' + item.user_code + '</td><td>' + item.event_description +
            '</td><td>' + item.loggedin_datetime + '</td><td>' + logout + '</td></tr>'
          );
        });
        $('#eventsmodal').modal('show');
        success_var = 1;
      })
      .catch(error => console.error('Error fetching data:', error));
  }

  function resetusercode() {
    var prfcode = $('#eventsmodal_profilecode').val();
    $('#refreshIcon').addClass('rotate');
    axios.put('/resetusercode/' + prfcode)
      .then(() => { $('#refreshIcon').removeClass('rotate'); showalert('تم تفعيل الرمز', 1, 2000); })
      .catch(error => alert(error));
  }

  function callEditForm(prfcode) {
    var url = '{{ route("editusermanager", ":prfcode") }}'.replace(':prfcode', prfcode);
    window.location.href = url;
  }

  $(document).ready(function() {
    var table = $('#dtprofiles').DataTable({
      searching: true,
      lengthChange: false,
      language: {
        "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_",
        "paginate": { "next": "الصفحة القادمة", "previous": "الصفحة السابقة" },
        "emptyTable": "لا توجد معلومات"
      },
      rowReorder: true,
      columnDefs: [
        { orderable: true, className: 'reorder', targets: [0,1,2,3] },
        { orderable: false, targets: '_all' }
      ]
    });

    $('#searchInput').on('keyup', function() { table.search($(this).val()).draw(); });
    $("#ImportExcelBtn").click(function() { $("#fileInput").click(); });
    $("#uploadfolder").click(function() { $("#folderInput").click(); });
    $("#importCardInfoBtn").click(function() { $("#cardInfoInput").click(); });

    $("#fileInput").change(function() { sendFilePathToAPI($(this)[0].files); });
    $("#folderInput").change(function() { sendFolderPathToAPI(); });
    $("#cardInfoInput").change(function() { sendCardInfoToAPI($(this)[0].files[0]); });

    function sendFolderPathToAPI() {
      var formData = new FormData();
      var files = $('#folderInput')[0].files;
      for (var i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
      }
      document.getElementById('folder_progress').style.display = 'flex';
      axios.post('/uploadusersfolder', formData, {
        headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        onUploadProgress: function(e) {
          var v = (e.loaded / e.total * 100).toFixed(0);
          document.getElementById('folder_loading-progress').value = v;
          document.getElementById('folder_progress-text').innerText = v + '%';
        }
      }).then(function(res) {
        document.getElementById('folder_progress').style.display = 'none';
        document.getElementById('folderInput').value = '';
        var updated   = res.data.updated   || 0;
        var unmatched = res.data.unmatched || [];
        if (updated > 0) {
          showalert('تم تحديث ' + updated + ' صورة بنجاح', 1, 4000);
        }
        if (unmatched.length > 0) {
          var list = $('#unmatchedNamesList').empty();
          unmatched.forEach(function(name) { list.append('<li>' + name + '</li>'); });
          $('#unmatchedNamesModal').modal('show');
        }
        setTimeout(function() { window.location.reload(); }, unmatched.length > 0 ? 0 : 1500);
      }).catch(function() {
        document.getElementById('folder_progress').style.display = 'none';
        showalert('حدث خطأ أثناء رفع الملفات', 2, 4000);
      });
    }

    function sendCardInfoToAPI(file) {
      if (!file) return;
      let formData = new FormData();
      formData.append('cardInfoFile', file);
      document.getElementById('cardinfo_progress').style.display = 'flex';
      var counter = 0;
      axios.post('/importcardinfo', formData, {
        headers: { 'Content-Type': undefined },
        onUploadProgress: function(e) {
          counter = Math.min(counter + 50, 100);
          document.getElementById('cardinfo_loading-progress').value = counter;
          document.getElementById('cardinfo_progress-text').innerText = counter + '%';
        }
      }).then(function(res) {
        document.getElementById('cardinfo_progress').style.display = 'none';
        document.getElementById('cardInfoInput').value = '';
        var imported = res.data.imported || 0;
        var notFound = res.data.not_found || [];
        if (imported > 0) {
          showalert('تم الاستيراد بنجاح: ' + imported + ' سجل', 1, 4000);
        }
        var unmatched = (notFound || []).concat(res.data.errors || []);
        if (unmatched.length > 0) {
          var list = $('#unmatchedNamesList').empty();
          unmatched.forEach(function(name) { list.append('<li>' + name + '</li>'); });
          $('#unmatchedNamesModal').modal('show');
        }
        if (imported === 0 && notFound.length === 0) {
          showalert('لم يتم استيراد أي سجل', 2, 4000);
        }
      }).catch(function(error) {
        document.getElementById('cardinfo_progress').style.display = 'none';
        document.getElementById('cardInfoInput').value = '';
        var errMsg = (error.response && error.response.data && error.response.data.message)
          ? error.response.data.message : 'خطأ في الاستيراد';
        showalert(errMsg, 2, 6000);
      });
    }

    function sendFilePathToAPI(files) {
      let formData = new FormData();
      for (var i = 0; i < files.length; i++) {
        formData.append(files[i].name === 'cards.xlsx' ? 'cardFile' : 'excelFile', files[i]);
      }
      document.getElementById('progress').style.display = 'flex';
      var countervar = 0;
      axios.post('/importexcel', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: function(e) {
          countervar = Math.min(countervar + 50, 100);
          document.getElementById('loading-progress').value = countervar;
          document.getElementById('progress-text').innerText = countervar + '%';
        }
      }).then(() => window.location.reload())
        .catch(error => { alert(JSON.stringify(error.response)); document.getElementById('progress').style.display = 'none'; });
    }
  });

  function getParentFolder(filePath) {
    var parts = filePath.split('/');
    return parts[parts.length - 2];
  }

  function openProfilePicture(url) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', url, true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          var newWindow = window.open(url, '_blank');
          newWindow.onload = function() {
            var interval = newWindow.setInterval(function() {
              if (newWindow) {
                newWindow.addEventListener('focus', function() { clearInterval(interval); });
                newWindow.addEventListener('blur', function() { newWindow.close(); clearInterval(interval); });
              } else { newWindow.close(); }
            }, 1000);
          };
        } else {
          showalert('الملف ليس موجود', 2, 1000);
        }
      }
    };
    xhr.send();
    return false;
  }

  function showalert(messagevar, alertvar, timeout) {
    var el = $("#successMessage");
    el.removeClass("alert-danger alert-success");
    el.addClass(alertvar == 1 ? "alert-success" : "alert-danger");
    el.text(messagevar).show();
    setTimeout(function() { el.hide(); }, timeout);
  }
</script>

@endsection
