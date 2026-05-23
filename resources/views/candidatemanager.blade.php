@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

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
    opacity: 0.16; animation: orbFloat 12s ease-in-out infinite; z-index: 0;
  }
  .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px;animation-delay:0s; }
  .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
  @keyframes orbFloat {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.14) translate(18px,-18px); }
  }

  /* ── Page ── */
  .cm-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem 3rem;
  }

  /* ── Page title ── */
  .cm-page-title {
    font-size: 1.3rem; font-weight: 800; color: #1e3a70;
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: 1.5rem;
  }
  .cm-page-title i { color: #c8920a; }

  /* ── Card ── */
  .cm-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
    margin-bottom: 1.25rem;
  }
  .cm-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .cm-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1rem 1.75rem;
    display: flex; align-items: center; gap: .6rem;
    position: relative;
  }
  .cm-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .cm-header-icon {
    width: 36px; height: 36px; border-radius: .55rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .cm-header-icon i { color: #f0c94d; font-size: .9rem; }
  .cm-card-header span { font-weight: 700; font-size: .97rem; color: #fff; }

  .cm-card-body { padding: 1.25rem 1.5rem 1.5rem; border-top: 3px solid #d4a820; }

  /* ── Labels ── */
  .cm-label {
    display: block; font-size: .82rem; font-weight: 700;
    color: #1e3a70; margin-bottom: .38rem;
  }

  /* ── Inputs ── */
  .cm-input {
    border: 1.5px solid #dde3ef; border-radius: .65rem;
    font-size: .88rem; color: #0f1f40;
    background: #f8faff; height: 42px;
    font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .cm-input:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }

  /* ── Select2 overrides ── */
  .select2-container .select2-selection--single {
    height: 42px !important;
    border: 1.5px solid #dde3ef !important;
    border-radius: .65rem !important;
    background: #f8faff !important;
    display: flex; align-items: center;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important; color: #0f1f40;
    font-size: .88rem; padding-right: 12px;
    font-family: 'Cairo', sans-serif;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
  }
  .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #d4a820 !important;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18) !important;
  }
  .select2-dropdown {
    border: 1.5px solid #dde3ef !important;
    border-radius: .65rem !important;
    box-shadow: 0 8px 24px rgba(30,58,112,0.12) !important;
  }
  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #1e3a70 !important;
  }

  /* ── Search wrapper ── */
  .cm-search-wrap { position: relative; }
  .cm-search-wrap .cm-search-icon {
    position: absolute; right: .85rem; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: .85rem; pointer-events: none;
  }
  .cm-search-wrap input {
    padding-right: 2.4rem; border-radius: 2rem;
    border: 1.5px solid #dde3ef; font-size: .88rem;
    height: 40px; background: #f8faff; color: #0f1f40;
    width: 100%; font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .cm-search-wrap input:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }
  .cm-search-wrap input::placeholder { color: #94a3b8; }

  /* ── Dual-table cards ── */
  .cm-dt-card {
    border-radius: .85rem; overflow: hidden;
    border: 1px solid rgba(212,168,32,0.2);
    box-shadow: 0 2px 8px rgba(30,58,112,0.06);
  }
  .cm-dt-title {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    color: #f0c94d; font-size: .85rem; font-weight: 700;
    padding: .6rem 1rem; text-align: center; letter-spacing: .3px;
  }

  /* ── DataTables ── */
  #dataTable1 thead th,
  #dataTable2 thead th,
  #dataTable_modal1 thead th,
  #dataTable_modal2 thead th {
    font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, #f8f4e8, #fef9e7);
    color: #1e3a70; padding: .6rem .75rem;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    text-align: center; white-space: nowrap;
  }
  #dataTable1 tbody tr,
  #dataTable2 tbody tr,
  #dataTable_modal1 tbody tr,
  #dataTable_modal2 tbody tr {
    border-bottom: 1px solid #f0ecd8; transition: background .15s;
  }
  #dataTable1 tbody tr:hover,
  #dataTable_modal1 tbody tr:hover { background: #fef9e7 !important; }
  #dataTable1 tbody td,
  #dataTable2 tbody td,
  #dataTable_modal1 tbody td,
  #dataTable_modal2 tbody td {
    font-size: .83rem; color: #1e3a70;
    padding: .55rem .75rem; vertical-align: middle; text-align: center;
  }
  #dataTable1 tbody tr { cursor: pointer; }

  /* Checkbox style */
  #dataTable1 input[type="checkbox"],
  #dataTable_modal1 input[type="checkbox"] {
    width: 16px; height: 16px; accent-color: #1e3a70; cursor: pointer;
  }

  /* Pagination */
  .pagination .page-item.active .page-link,
  div.dataTables_wrapper div.dataTables_paginate ul.pagination .page-item.active .page-link:focus,
  .pagination .page-item.active .page-link:hover {
    background-color: #1e3a70; border-color: #1e3a70;
  }
  .page-link { color: #1e3a70; font-size: .82rem; }
  .dataTables_filter { display: none; }

  /* ── Buttons ── */
  .btn-cm-primary {
    position: relative; overflow: hidden;
    height: 42px; padding: 0 1.4rem; border-radius: .65rem;
    font-size: .9rem; font-weight: 800;
    font-family: 'Cairo', sans-serif; border: none; color: #1a2e0f;
    background: linear-gradient(135deg, #c8920a 0%, #f0c94d 45%, #d4a820 75%, #c8920a 100%);
    background-size: 200% 200%; animation: goldShift 5s ease infinite;
    box-shadow: 0 4px 16px rgba(212,168,32,0.38);
    transition: transform .15s, box-shadow .2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: .45rem;
  }
  @keyframes goldShift {
    0%,100% { background-position:0% 50%; }
    50%      { background-position:100% 50%; }
  }
  .btn-cm-primary:hover {
    transform: translateY(-2px); box-shadow: 0 7px 22px rgba(212,168,32,0.5);
    color: #1a2e0f;
  }

  .btn-cm-secondary {
    height: 42px; padding: 0 1.25rem; border-radius: .65rem;
    font-size: .9rem; font-weight: 700;
    font-family: 'Cairo', sans-serif;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    transition: background .2s, border-color .2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: .45rem;
  }
  .btn-cm-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.45); color: #1a2e0f; }

  .btn-cm-add-list {
    height: 38px; padding: 0 1rem; border-radius: 2rem;
    font-size: .83rem; font-weight: 700;
    font-family: 'Cairo', sans-serif;
    background: linear-gradient(135deg, #1a3268, #1e4098);
    color: #f0c94d; border: 1px solid rgba(212,168,32,0.3);
    display: inline-flex; align-items: center; gap: .35rem;
    cursor: pointer; transition: opacity .15s, transform .12s;
    box-shadow: 0 2px 8px rgba(30,58,112,0.2);
  }
  .btn-cm-add-list:hover { opacity: .88; transform: translateY(-1px); color: #f0c94d; }

  /* ── Modal ── */
  .cm-modal .modal-content {
    border-radius: 1rem; border: none;
    box-shadow: 0 8px 40px rgba(10,22,40,0.22); overflow: hidden;
  }
  .cm-modal .modal-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    border-bottom: none; padding: 1rem 1.5rem;
    position: relative;
  }
  .cm-modal .modal-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .cm-modal .modal-title { color: #fff; font-weight: 700; font-size: 1rem; }
  .cm-modal .modal-header .close { color: rgba(255,255,255,.75) !important; text-shadow: none; opacity: 1; }
  .cm-modal .modal-header .close:hover { color: #fff !important; }
  .cm-modal .modal-body {
    padding: 1.5rem; border-top: 3px solid #d4a820;
  }
  .cm-modal .modal-footer { border-color: #e8edf6; padding: .85rem 1.25rem; gap: .5rem; }

  .cm-modal-label {
    font-size: .82rem; font-weight: 700;
    color: #1e3a70; display: block; margin-bottom: .35rem;
  }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="cm-page" dir="rtl">
<div class="container-fluid" style="max-width:1200px;">

  <h1 class="cm-page-title"><i class="fas fa-user-edit"></i> إضافة / تعديل مرشحين</h1>

  <form id="candidatesmanagerform" action="/savecandidateinfo" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="input_election_type"    name="input_election_type"    value="">
    <input type="hidden" id="input_candidates_codes" name="input_candidates_codes" value="">
    <input type="hidden" id="input_all_profiles"     name="input_all_profiles"     value="">

    {{-- Election selects card --}}
    <div class="cm-card">
      <div class="cm-card-header">
        <div class="cm-header-icon"><i class="fas fa-vote-yea"></i></div>
        <span>بيانات العملية الانتخابية</span>
      </div>
      <div class="cm-card-body">
        <div class="row">
          <div class="col-md-5 mb-3">
            <label class="cm-label" for="select_election">اسم العملية الانتخابية <span style="color:#dc3545;">*</span></label>
            <select name="select_election" id="select_election" class="js-example-basic-single form-control cm-input" style="width:100%;">
              @if($Elections)
                @foreach ($Elections as $election)
                  <option value="{{ $election->election_code }}">{{ $election->election_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label class="cm-label" for="selectelectionround">رقم الجولة</label>
            <select name="selectelectionround" id="selectelectionround" class="js-example-basic-single form-control cm-input" style="width:100%;"></select>
          </div>
        </div>
      </div>
    </div>

    {{-- Candidates tables card --}}
    <div class="cm-card">
      <div class="cm-card-header">
        <div class="cm-header-icon"><i class="fas fa-users"></i></div>
        <span>اختيار المرشحين</span>
      </div>
      <div class="cm-card-body">

        {{-- Search + add-list button --}}
        <div class="d-flex align-items-center mb-3" style="gap:.6rem;">
          <div class="cm-search-wrap flex-grow-1">
            <i class="fas fa-search cm-search-icon"></i>
            <input type="text" id="searchInput" placeholder="بحث..." autocomplete="off">
          </div>
          <div id="btn_add_list" hidden>
            <button type="button" class="btn-cm-add-list" onclick="showlistmodal();">
              <i class="fas fa-plus"></i> إضافة لائحة
            </button>
          </div>
        </div>

        <div class="row">
          {{-- Table 1: available --}}
          <div class="col-md-6 mb-3">
            <div class="cm-dt-card">
              <div class="cm-dt-title" id="tab1">اختيار الأسماء</div>
              <div class="table-responsive">
                <table id="dataTable1" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th></th>
                      <th>الإسم</th>
                      <th>رقم الهاتف</th>
                      <th>profile code</th>
                      <th>candidate_status</th>
                      <th>selected_candidates</th>
                      <th></th>
                      <th>win number</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- Table 2: selected --}}
          <div class="col-md-6 mb-3">
            <div class="cm-dt-card">
              <div class="cm-dt-title" id="tab2">الأسماء المختارة</div>
              <div class="table-responsive">
                <table id="dataTable2" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th>اسم المرشح</th>
                      <th>رقم الهاتف</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- Action buttons --}}
        <div class="d-flex" style="gap:.6rem; margin-top:.5rem;">
          <button type="button" class="btn-cm-primary" onclick="submitform();">
            <i class="fas fa-save"></i> حفظ المعلومات
          </button>
          <button type="button" class="btn-cm-secondary" onclick="clearForm()">
            <i class="fas fa-eraser"></i> مسح
          </button>
        </div>

      </div>
    </div>

  </form>

</div>
</div>

{{-- List modal --}}
<div class="modal fade cm-modal" id="listmodal" tabindex="-1" role="dialog" aria-labelledby="listmodalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="listmodalLabel">
          <i class="fas fa-list-alt fa-sm" style="color:#f0c94d;margin-left:.4rem;"></i> إضافة / تعديل لائحة
        </h5>
        <button type="button" class="close ml-0" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="inputlistcode"      name="inputlistcode">
        <input type="hidden" id="inputprofilescodes" name="inputprofilescodes">
        <input type="hidden" id="inputelectioncode"  name="inputelectioncode">
        <input type="hidden" id="inputelectionround" name="inputelectionround">

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="cm-modal-label">اسم اللائحة <span style="color:#dc3545;">*</span></label>
            <input type="text" id="inputlistname" name="inputlistname"
              class="form-control cm-input" autocomplete="off" required>
          </div>
          <div class="col-md-4">
            <label class="cm-modal-label">العدد المطلوب للفوز <span style="color:#dc3545;">*</span></label>
            <input type="number" id="inputwinnumber" name="inputwinnumber"
              class="form-control cm-input" value="1" required>
          </div>
        </div>

        {{-- Modal search --}}
        <div class="cm-search-wrap mb-3">
          <i class="fas fa-search cm-search-icon"></i>
          <input type="text" id="searchInputmodal" placeholder="بحث في الأسماء..." autocomplete="off">
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="cm-dt-card">
              <div class="cm-dt-title">اختيار الأسماء</div>
              <div class="table-responsive">
                <table id="dataTable_modal1" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="selectAllModal" title="تحديد الكل"></th>
                      <th>الإسم</th>
                      <th>رقم الهاتف</th>
                      <th>profile code</th>
                      <th>candidate_status</th>
                      <th>اللائحة</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <div class="cm-dt-card">
              <div class="cm-dt-title">الأسماء المختارة</div>
              <div class="table-responsive">
                <table id="dataTable_modal2" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th>اسم المرشح</th>
                      <th>رقم الهاتف</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex" style="gap:.6rem;">
          <button type="button" class="btn-cm-primary" onclick="savecandidateslist();">
            <i class="fas fa-save"></i> حفظ اللائحة
          </button>
          <button type="button" class="btn-cm-secondary" onclick="clearmodalform()">
            <i class="fas fa-eraser"></i> مسح
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    var datatable1_dataset = [];
    var datatable2_dataset = [];
    var datatable_dataset_modal1 = [];
    var datatable_dataset_modal2 = [];
    var table_modal1 = null;
    var table_modal2 = null;

    let progressBar = document.querySelector('.progress-bar');
    let loadingContainer = document.querySelector('.loading-container');

    $('#listmodal').on('shown.bs.modal', function(e) {
        datatable_dataset_modal1.length = 0;
        datatable_dataset_modal2.length = 0;
        createdatatablemodals();
        var array_prf_codes = [];
        var prf_codes = $('#inputprofilescodes').val();
        if (prf_codes != '') {
            array_prf_codes = JSON.parse(prf_codes);
        }
        var electioncode = $('#select_election').select2().val();
        fetch('/getProfiles/' + electioncode)
            .then(response => response.json())
            .then(data => {
                var profiles = data.filter(function(item) {
                    if (!item.group_name) return true; // not assigned to any list
                    return array_prf_codes.some(function(i) { return i.profile_code === item.profile_code; }); // already in this list
                });
                profiles.sort(function(a, b) {
                    var aIn = array_prf_codes.find(i => i.profile_code === a.profile_code);
                    var bIn = array_prf_codes.find(i => i.profile_code === b.profile_code);
                    if (aIn && !bIn) return -1;
                    if (!aIn && bIn) return 1;
                    return 0;
                });
                profiles.forEach(function(item) {
                    datatable_dataset_modal1.push(['', item.full_name, item.mobile, item.profile_code, '1', item.group_name]);
                });
                $('#dataTable_modal1').DataTable().clear().rows.add(datatable_dataset_modal1).draw();
                $('#dataTable_modal1').DataTable().rows().every(function() {
                    var rowData = this.data();
                    var isExists = array_prf_codes.some(i => i.profile_code === rowData[3]);
                    if (isExists) {
                        datatable_dataset_modal2.push([rowData[1], rowData[2], rowData[3]]);
                        $(this.node()).find('input[type="checkbox"]').prop('checked', true);
                    }
                });
                $('#inputelectioncode').val($('#select_election').val());
                $('#inputelectionround').val($('#selectelectionround').val());
                $('#dataTable_modal2').DataTable().clear().rows.add(datatable_dataset_modal2).draw();
                $('#inputlistname').focus();
            })
            .catch(error => console.error('Error fetching data:', error));
    });

    $(document).ready(function() {
        var table1 = $('#dataTable1').DataTable({
            data: datatable1_dataset,
            searching: true,
            lengthChange: false,
            info: false,
            dom: 'rtp',
            language: {
                paginate: { next: "الصفحة القادمة", previous: "الصفحة السابقة" },
                emptyTable: "لا توجد معلومات"
            },
            rowReorder: true,
            columnDefs: [
                { className: 'dt-center', targets: '_all' },
                { targets: [3, 4, 5, 7], visible: false },
                { targets: 0, render: function(data, type) { return type === 'display' ? '<input type="checkbox">' : data; } },
                { targets: 6, render: function(data, type) { return type === 'display' ? '<i class="fa fa-eye" style="cursor:pointer;margin-left:8px;"></i><i class="fa fa-trash text-danger" style="cursor:pointer;" title="حذف اللائحة"></i>' : data; } }
            ]
        });

        var table2 = $('#dataTable2').DataTable({
            data: datatable2_dataset,
            searching: false,
            lengthChange: false,
            info: false,
            dom: 'rtp',
            language: {
                paginate: { next: "الصفحة القادمة", previous: "الصفحة السابقة" },
                emptyTable: "لا توجد معلومات"
            },
            rowReorder: true,
            columnDefs: [
                { className: 'dt-center', targets: '_all' },
                { targets: [2], visible: false }
            ]
        });

        $('#select_election').select2({
            placeholder: 'اختيار عملية إنتخابية',
            allowClear: true,
            language: { noResults: function() { return 'لا يوجد نتائج'; } }
        });

        var electioncode = $('#select_election').select2().val();
        fetchdata(electioncode, 0);

        $('#select_election').on('change', function() {
            fetchdata($(this).select2().val(), 0);
        });

        $('#searchInput').on('keyup', function() {
            table1.search($(this).val()).draw();
        });

        $('#searchInputmodal').on('keyup', function() {
            if (table_modal1) table_modal1.search($(this).val()).draw();
        });
    });

    function clearForm() {
        var form = document.getElementById('usermanagerform');
        if (form) form.reset();
    }

    function submitform() {
        var candidates_data = [];
        $('#dataTable2').DataTable().rows().every(function() {
            candidates_data.push(this.data()[2]);
        });
        $('#input_candidates_codes').val(JSON.stringify(candidates_data));
        $('#candidatesmanagerform').submit();
    }

    $('#selectelectionround').change(function() {
        fetchdata($('#select_election').select2().val(), $(this).select2().val());
    });

    function fetchdata(electioncode, roundnumber) {
        showOverlay();
        datatable2_dataset.length = 0;
        $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
        let progress = 0, success_var = 0;
        progressBar.style.width = 0;
        loadingContainer.style.display = 'block';
        let interval = setInterval(() => {
            progress += Math.random() * 50;
            if (success_var == 1) {
                clearInterval(interval);
                loadingContainer.style.display = 'none';
                hideOverlay();
            } else {
                progressBar.style.width = progress + '%';
                progressBar.setAttribute('aria-valuenow', progress);
            }
        }, 500);

        fetch('/electioninfo/' + electioncode + '/' + roundnumber)
            .then(response => response.json())
            .then(data => {
                var election_type = data.election_type;
                var candidates_array = data.candidates;
                var election_rounds_array = data.election_rounds;

                if ($('#selectelectionround').children().length == 0) {
                    $.each(election_rounds_array, function(index, option) {
                        $('#selectelectionround').append('<option value="' + option.round_number + '">الجولة ' + numToWordsAR_M(option.round_number) + '</option>');
                    });
                }

                $('#input_election_type').val(election_type);
                datatable1_dataset.length = 0;
                $('#inputprofilescodes').val('');

                if (election_type == 1) {
                    $('#tab1').text("اختيار الأسماء");
                    $('#tab2').text("الأسماء المختارة");
                    $("#dataTable1 thead tr:eq(0) th:eq(2)").text("رقم الهاتف");
                    $("#dataTable2 thead tr:eq(0) th:eq(1)").text("رقم الهاتف");
                    $('#btn_add_list').attr('hidden', true);
                    $('#searchInput').attr('placeholder', 'بحث في الأسماء...');
                    $('#dataTable1').DataTable().column(6).visible(false);
                    if (candidates_array.length > 0) {
                        candidates_array.forEach(function(item) {
                            datatable1_dataset.push(['', item.full_name, item.mobile, item.profile_code, item.candidate_status, '', '']);
                        });
                    }
                } else {
                    $('#tab1').text("اللوائح");
                    $('#tab2').text("اللوائح المختارة");
                    $("#dataTable1 thead tr:eq(0) th:eq(2)").text("عدد الأعضاء");
                    $("#dataTable2 thead tr:eq(0) th:eq(1)").text("عدد الأعضاء");
                    $('#btn_add_list').attr('hidden', false);
                    $('#searchInput').attr('placeholder', 'بحث في اللوائح...');
                    var candidatestochoose = data.candidatestochoose;
                    if (candidates_array.length > 0) {
                        candidates_array.forEach(function(item) {
                            datatable1_dataset.push(['', item.group_name, item.count_candidates, item.group_code, '1', JSON.stringify(candidatestochoose[item.group_code]), '', item.win_number]);
                        });
                    }
                }

                $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
                $('#dataTable1').DataTable().rows().every(function() {
                    var rowData = this.data();
                    if (rowData[4] != '') {
                        datatable2_dataset.push([rowData[1], rowData[2], rowData[3]]);
                        $(this.node()).find('input[type="checkbox"]').prop('checked', true);
                    } else {
                        $(this.node()).find('input[type="checkbox"]').prop('checked', false);
                    }
                });
                $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
                $('#dataTable1').find('input[type="checkbox"]').each(function() {
                    $(this).prop('checked', true);
                });
                success_var = 1;
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    $('#dataTable1').on('change', 'tbody td:first-child input[type="checkbox"]', function() {
        var row = $('#dataTable1').DataTable().row($(this).closest('tr'));
        var rowData = row.data();
        var new_row = [rowData[1], rowData[2], rowData[3]];
        if ($(this).is(':checked')) {
            datatable2_dataset.push(new_row);
        } else {
            for (var i = datatable2_dataset.length - 1; i >= 0; i--) {
                if (datatable2_dataset[i][2] == new_row[2]) datatable2_dataset.splice(i, 1);
            }
        }
        $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
    });

    $('#dataTable_modal1').on('change', 'tbody td:first-child input[type="checkbox"]', function() {
        var row = $('#dataTable_modal1').DataTable().row($(this).closest('tr'));
        var rowData = row.data();
        var new_row = [rowData[1], rowData[2], rowData[3]];
        if ($(this).is(':checked')) {
            datatable_dataset_modal2.push(new_row);
        } else {
            for (var i = datatable_dataset_modal2.length - 1; i >= 0; i--) {
                if (datatable_dataset_modal2[i][2] == new_row[2]) datatable_dataset_modal2.splice(i, 1);
            }
        }
        $('#dataTable_modal2').DataTable().clear().rows.add(datatable_dataset_modal2).draw();
    });

    $(document).on('change', '#selectAllModal', function() {
        var checked = $(this).is(':checked');
        $('#dataTable_modal1').DataTable().rows({ search: 'applied' }).every(function() {
            var rowData = this.data();
            var new_row = [rowData[1], rowData[2], rowData[3]];
            $(this.node()).find('input[type="checkbox"]').prop('checked', checked);
            if (checked) {
                var exists = datatable_dataset_modal2.some(function(r) { return r[2] == new_row[2]; });
                if (!exists) datatable_dataset_modal2.push(new_row);
            } else {
                for (var i = datatable_dataset_modal2.length - 1; i >= 0; i--) {
                    if (datatable_dataset_modal2[i][2] == new_row[2]) datatable_dataset_modal2.splice(i, 1);
                }
            }
        });
        $('#dataTable_modal2').DataTable().clear().rows.add(datatable_dataset_modal2).draw();
    });

    $('#dataTable1').on('click', 'tbody td:last-child .fa-eye', function() {
        var rowData = $('#dataTable1').DataTable().row($(this).closest('tr').index()).data();
        $('#inputlistcode').val(rowData[3]);
        $('#inputprofilescodes').val(rowData[5]);
        $('#inputlistname').val(rowData[1]);
        $('#inputwinnumber').val(rowData[7]);
        $('#listmodal').modal('show');
    });

    $('#dataTable1').on('click', 'tbody td:last-child .fa-trash', function() {
        var rowData = $('#dataTable1').DataTable().row($(this).closest('tr').index()).data();
        var group_code = rowData[3];
        var group_name = rowData[1];
        if (!confirm('هل تريد حذف اللائحة "' + group_name + '"؟')) return;
        fetch('/deletecandidatelist/' + group_code, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        })
        .then(r => r.json())
        .then(function() {
            fetchdata($('#select_election').select2().val(), $('#selectelectionround').val() || 0);
        })
        .catch(error => alert('خطأ في الحذف: ' + error));
    });

    function clearmodal() {
        $('#inputlistcode').val('');
        $('#inputprofilescodes').val('');
        $('#inputlistname').val('');
    }

    function clearmodalform() { clearmodal(); }

    function showlistmodal() {
        clearmodal();
        $('#listmodal').modal('show');
    }

    function createdatatablemodals() {
        if (table_modal1 == null) {
            table_modal1 = $('#dataTable_modal1').DataTable({
                pageLength: 6,
                data: datatable_dataset_modal1,
                searching: true,
                lengthChange: false,
                info: false,
                dom: 'rtp',
                language: {
                    paginate: { next: "الصفحة القادمة", previous: "الصفحة السابقة" },
                    emptyTable: "لا توجد معلومات"
                },
                rowReorder: true,
                columnDefs: [
                    { className: 'dt-center', targets: '_all' },
                    { targets: [3, 4], visible: false },
                    { targets: 0, render: function(data, type) { return type === 'display' ? '<input type="checkbox">' : data; } }
                ]
            });
        }
        if (table_modal2 == null) {
            table_modal2 = $('#dataTable_modal2').DataTable({
                pageLength: 6,
                data: datatable_dataset_modal2,
                searching: false,
                lengthChange: false,
                info: false,
                dom: 'rtp',
                language: {
                    paginate: { next: "الصفحة القادمة", previous: "الصفحة السابقة" },
                    emptyTable: "لا توجد معلومات"
                },
                rowReorder: true,
                columnDefs: [
                    { className: 'dt-center', targets: '_all' },
                    { targets: [2], visible: false }
                ]
            });
        }
    }

    function savecandidateslist() {
        var hashMapJson = {
            listcode:     $('#inputlistcode').val(),
            listname:     $('#inputlistname').val(),
            winnumber:    $('#inputwinnumber').val(),
            electioncode: $('#inputelectioncode').val(),
            round_number: $('#inputelectionround').val(),
            listmembers:  datatable_dataset_modal2,
        };
        fetch('/savecandidatelist', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            body: JSON.stringify(hashMapJson)
        })
        .then(r => { if (!r.ok) alert('Network response was not ok'); return r.json(); })
        .then(() => {
            $('#listmodal').modal('hide');
            fetchdata($('#select_election').select2().val(), 0);
        })
        .catch(error => alert('There was a problem with your fetch operation: ' + error));
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
            if (h) {
                if (h > 2) H = T[h].slice(0, (h == 8 ? -2 : -1)) + m;
                else if (h == 1) H = m;
                else H = m.slice(0, -1) + (s && !N ? "تا" : "تان");
            }
            if (N > 19) wN = T[u] + (u ? W : "") + (t == 2 ? "عشر" : T[t].slice(0, (t == 8 ? -2 : -1))) + "ون";
            else if (N > 10) wN = (u == 1 ? "أحد" : (u == 2 ? "اثنا" : T[u])) + " عشر";
            else wN = T[N];
            let w = H + (h && N ? W : "") + wN;
            if (!s) return w;
            if (N > 2) return w + " " + (N > 10 ? s + "ًا" : (P < 3 ? [, "آلاف", "ملايين"][P] : S[P] + "ات"));
            if (!N) return w + " " + s;
            w = (h ? H + W : "") + s;
            return (N == 1) ? w : w + "ان";
        }
    }
</script>

@endsection
