@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.js"></script>

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
  .lm-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem 3rem;
  }

  /* ── Page title ── */
  .lm-page-title {
    font-size: 1.3rem; font-weight: 800; color: #1e3a70;
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: 1.5rem;
  }
  .lm-page-title i { color: #c8920a; }

  /* ── Card ── */
  .lm-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
    margin-bottom: 1.25rem;
  }
  .lm-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .lm-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1rem 1.75rem;
    display: flex; align-items: center; gap: .6rem;
    position: relative;
  }
  .lm-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .lm-header-icon {
    width: 36px; height: 36px; border-radius: .55rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .lm-header-icon i { color: #f0c94d; font-size: .9rem; }
  .lm-card-header span { font-weight: 700; font-size: .97rem; color: #fff; }

  .lm-card-body { padding: 1.25rem 1.5rem 1.5rem; border-top: 3px solid #d4a820; }

  /* ── Labels ── */
  .lm-label {
    display: block; font-size: .82rem; font-weight: 700;
    color: #1e3a70; margin-bottom: .38rem;
  }

  /* ── Inputs ── */
  .lm-input {
    border: 1.5px solid #dde3ef; border-radius: .65rem;
    font-size: .88rem; color: #0f1f40;
    background: #f8faff; height: 42px;
    font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .lm-input:focus {
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

  /* ── Search ── */
  .lm-search-wrap { position: relative; }
  .lm-search-icon {
    position: absolute; right: .85rem; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: .85rem; pointer-events: none;
  }
  .lm-search-wrap input {
    padding-right: 2.4rem; border-radius: 2rem;
    border: 1.5px solid #dde3ef; font-size: .88rem;
    height: 40px; background: #f8faff; color: #0f1f40;
    width: 100%; font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .lm-search-wrap input:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }
  .lm-search-wrap input::placeholder { color: #94a3b8; }

  /* ── Dual-table cards ── */
  .lm-dt-card {
    border-radius: .85rem; overflow: hidden;
    border: 1px solid rgba(212,168,32,0.2);
    box-shadow: 0 2px 8px rgba(30,58,112,0.06);
  }
  .lm-dt-title {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    color: #f0c94d; font-size: .85rem; font-weight: 700;
    padding: .6rem 1rem; text-align: center; letter-spacing: .3px;
  }

  /* ── DataTables ── */
  #dataTable1 thead th,
  #dataTable2 thead th {
    font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, #f8f4e8, #fef9e7);
    color: #1e3a70; padding: .6rem .75rem;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    text-align: center; white-space: nowrap;
  }
  #dataTable1 tbody tr,
  #dataTable2 tbody tr {
    border-bottom: 1px solid #f0ecd8; transition: background .15s;
  }
  #dataTable1 tbody tr:hover { background: #fef9e7 !important; cursor: pointer; }
  #dataTable1 tbody td,
  #dataTable2 tbody td {
    font-size: .83rem; color: #1e3a70;
    padding: .55rem .75rem; vertical-align: middle; text-align: center;
  }

  /* Checkbox */
  #dataTable1 input[type="checkbox"] {
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
  .btn-lm-primary {
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
  .btn-lm-primary:hover {
    transform: translateY(-2px); box-shadow: 0 7px 22px rgba(212,168,32,0.5);
    color: #1a2e0f;
  }

  .btn-lm-secondary {
    height: 42px; padding: 0 1.25rem; border-radius: .65rem;
    font-size: .9rem; font-weight: 700;
    font-family: 'Cairo', sans-serif;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    transition: background .2s, border-color .2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: .45rem;
  }
  .btn-lm-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.45); color: #1a2e0f; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="lm-page" dir="rtl">
<div class="container-fluid" style="max-width:1200px;">

  <h1 class="lm-page-title"><i class="fas fa-chalkboard-teacher"></i> إضافة مرشد</h1>

  <form id="leadermanagerform" action="/saveleaderinfo" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="input_profile_code" name="input_profile_code" value="">
    <input type="hidden" id="input_voters_codes" name="input_voters_codes" value="">

    {{-- Leader info card --}}
    <div class="lm-card">
      <div class="lm-card-header">
        <div class="lm-header-icon"><i class="fas fa-vote-yea"></i></div>
        <span>بيانات المرشد</span>
      </div>
      <div class="lm-card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="lm-label" for="selectTest">اسم العملية الانتخابية <span style="color:#dc3545;">*</span></label>
            <select name="Select Dropdown" id="selectTest" class="js-example-basic-single form-control lm-input" style="width:100%;">
              @if($Elections)
                @foreach ($Elections as $election)
                  <option value="{{ $election->election_code }}">{{ $election->election_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="lm-label" for="selectleader">اسم المرشد <span style="color:#dc3545;">*</span></label>
            <select name="Select Dropdown" id="selectleader" class="js-example-basic-single form-control lm-input" style="width:100%;">
              @if($Profiles)
                @foreach ($Profiles as $profile)
                  <option value="{{ $profile->profile_code }}">{{ $profile->full_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- Voter assignment card --}}
    <div class="lm-card">
      <div class="lm-card-header">
        <div class="lm-header-icon"><i class="fas fa-users"></i></div>
        <span>تعيين الناخبين</span>
      </div>
      <div class="lm-card-body">

        {{-- Search + group filter --}}
        <div class="row mb-3">
          <div class="col-md-7 mb-2">
            <div class="lm-search-wrap">
              <i class="fas fa-search lm-search-icon"></i>
              <input type="text" id="searchInput" placeholder="بحث في الأسماء..." autocomplete="off">
            </div>
          </div>
          <div class="col-md-5 mb-2 d-flex align-items-end">
            <select name="Select Dropdown" id="select_voter_group" class="js-example-basic-single form-control lm-input" style="width:100%;">
              <option value=""></option>
              @if($Groups)
                @foreach ($Groups as $group)
                  <option value="{{ $group->voter_group_name }}">{{ $group->voter_group_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>

        <div class="row">
          {{-- Table 1: available --}}
          <div class="col-md-6 mb-3">
            <div class="lm-dt-card">
              <div class="lm-dt-title" id="tab1">اختيار الأسماء</div>
              <div class="table-responsive">
                <table id="dataTable1" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="checkAll" title="تحديد الكل" style="width:16px;height:16px;accent-color:#1e3a70;cursor:pointer;"></th>
                      <th>اسم الناخب</th>
                      <th>رقم الهاتف</th>
                      <th>المجموعة</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- Table 2: selected --}}
          <div class="col-md-6 mb-3">
            <div class="lm-dt-card">
              <div class="lm-dt-title" id="tab2">الأسماء المختارة</div>
              <div class="table-responsive">
                <table id="dataTable2" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th>اسم الناخب</th>
                      <th>رقم الهاتف</th>
                      <th>المجموعة</th>
                      <th></th>
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
          <button type="button" class="btn-lm-primary" onclick="submitform();">
            <i class="fas fa-save"></i> حفظ المعلومات
          </button>
          <button type="button" class="btn-lm-secondary" onclick="clearForm()">
            <i class="fas fa-eraser"></i> مسح
          </button>
        </div>

      </div>
    </div>

  </form>

</div>
</div>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    var datatable1_dataset = [];
    var datatable2_dataset = [];

    let progressBar = document.querySelector('.progress-bar');
    let loadingContainer = document.querySelector('.loading-container');

    fetchvotersdata();

    $('#select_voter_group').on('change', function() {
        var selectedValue = $(this).val();
        $('#dataTable1').DataTable().column(3).search(selectedValue).draw();
    });

    $(document).ready(function() {

        $('#selectleader').change(function() {
            fetchvotersdata();
        });

        var table1 = $('#dataTable1').DataTable({
            data: datatable1_dataset,
            searching: true,
            lengthChange: false,
            "info": false,
            "dom": 'rtp',
            language: {
                "paginate": {
                    "next": "الصفحة القادمة",
                    "previous": "الصفحة السابقة"
                },
                "emptyTable": "لا توجد معلومات",
            },
            rowReorder: true,
            columnDefs: [{
                    className: 'dt-center',
                    targets: '_all'
                },
                {
                    "targets": [4, 5],
                    "visible": false
                },
                {
                    targets: 0,
                    render: function(data, type, row, meta) {
                        if (type === 'display') {
                            return '<input type="checkbox"'
                                + ' data-name="'       + (row[1] || '').replace(/"/g, '&quot;') + '"'
                                + ' data-mobile="'     + (row[2] || '') + '"'
                                + ' data-group_name="' + (row[3] || '').replace(/"/g, '&quot;') + '"'
                                + ' data-group_code="' + (row[4] || '') + '"'
                                + ' data-profilecode="'+ (row[5] || '') + '"'
                                + '>';
                        }
                        return data;
                    }
                }
            ]
        });

        var table2 = $('#dataTable2').DataTable({
            data: datatable2_dataset,
            searching: false,
            lengthChange: false,
            "info": false,
            "dom": 'rtp',
            language: {
                "paginate": {
                    "next": "الصفحة القادمة",
                    "previous": "الصفحة السابقة"
                },
                "emptyTable": "لا توجد معلومات",
            },
            rowReorder: true,
            columnDefs: [{
                    className: 'dt-center',
                    targets: '_all'
                },
                {
                    "targets": [3, 4],
                    "visible": false
                }
            ]
        });

    });

    function fetchvotersdata() {
        datatable2_dataset.length = 0;
        let progress = 0;
        let success_var = 0;
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

        var election_code = $('#selectTest').val();
        var leader_code   = $('#selectleader').val();

        fetch('/getvotersbyleader/' + election_code + '/' + leader_code)
            .then(response => response.json())
            .then(data => {
                var profiles_array    = data.allfreeprofiles;
                var voters_rel_leader = data.voters_rel_leader;
                datatable1_dataset.length = 0;
                profiles_array.forEach(function(item) {
                    var new_row = ['', item.full_name, item.mobile, item.voter_group_name, item.voter_group_code, item.profile_code];
                    datatable1_dataset.push(new_row);
                });
                $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
                $('#dataTable1').DataTable().rows().every(function() {
                    var rowData = this.data();
                    if (rowData && rowData.length > 0) {
                        var name        = rowData[1];
                        var mobile      = rowData[2];
                        var group_name  = rowData[3];
                        var group_code  = rowData[4];
                        var profilecode = rowData[5];
                        if ($.inArray(profilecode, voters_rel_leader) !== -1) {
                            var new_row2 = [name, mobile, group_name, group_code, profilecode];
                            datatable2_dataset.push(new_row2);
                            $(this.node()).find('input[type="checkbox"]').prop('checked', true);
                        } else {
                            $(this.node()).find('input[type="checkbox"]').prop('checked', false);
                        }
                    }
                });
                $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
                $('#checkAll').prop('checked', false).prop('indeterminate', false);
                syncCheckAll();
                success_var = 1;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    $('#selectTest').select2({
        placeholder: 'اختيار عملية إنتخابية',
        allowClear: true,
        language: {
            noResults: function() { return 'لا يوجد نتائج'; }
        }
    });
    $('#selectleader').select2({
        placeholder: 'اختيار اسم',
        allowClear: true,
        language: {
            noResults: function() { return 'لا يوجد نتائج'; }
        }
    });
    $('#select_voter_group').select2({
        placeholder: 'اختيار مجموعة',
        allowClear: true,
        language: {
            noResults: function() { return 'لا يوجد نتائج'; }
        }
    });

    function clearForm() {
        $('#dataTable1 tbody').empty();
        var form = document.getElementById('usermanagerform');
        form.reset();
        document.getElementById('image-preview').innerHTML = '';
        var currentDate = new Date();
        var formattedDate = currentDate.toISOString().split('T')[0];
        $('#input_age').val(formattedDate);
        $('#input_fullname').focus();
    }

    function syncCheckAll() {
        var dt = $('#dataTable1').DataTable();
        var total   = dt.rows({search: 'applied'}).count();
        var checked = 0;
        dt.rows({search: 'applied'}).every(function() {
            if ($(this.node()).find('input[type="checkbox"]').is(':checked')) checked++;
        });
        var $ca = $('#checkAll');
        $ca.prop('checked', total > 0 && checked === total);
        $ca.prop('indeterminate', checked > 0 && checked < total);
    }

    $('#checkAll').on('change', function() {
        var isChecked = $(this).is(':checked');
        var dt = $('#dataTable1').DataTable();
        dt.rows({search: 'applied'}).every(function() {
            var $cb = $(this.node()).find('input[type="checkbox"]');
            var profilecode = $cb.data('profilecode');
            if (isChecked) {
                if (!$cb.is(':checked')) {
                    $cb.prop('checked', true);
                    var exists = datatable2_dataset.some(function(r) { return r[4] == profilecode; });
                    if (!exists) {
                        datatable2_dataset.push([$cb.data('name'), $cb.data('mobile'), $cb.data('group_name'), $cb.data('group_code'), profilecode]);
                    }
                }
            } else {
                $cb.prop('checked', false);
                for (var i = datatable2_dataset.length - 1; i >= 0; i--) {
                    if (datatable2_dataset[i][4] == profilecode) datatable2_dataset.splice(i, 1);
                }
            }
        });
        $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
    });

    $('#dataTable1').on('change', 'tbody td:first-child input[type="checkbox"]', function() {
        var $cb        = $(this);
        var name       = $cb.data('name');
        var mobile     = $cb.data('mobile');
        var group_name = $cb.data('group_name');
        var group_code = $cb.data('group_code');
        var profilecode= $cb.data('profilecode');
        var isChecked  = $cb.is(':checked');
        var new_row    = [name, mobile, group_name, group_code, profilecode];
        if (isChecked) {
            datatable2_dataset.push(new_row);
        } else {
            for (var i = datatable2_dataset.length - 1; i >= 0; i--) {
                if (datatable2_dataset[i][4] == profilecode) {
                    datatable2_dataset.splice(i, 1);
                }
            }
        }
        $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
        syncCheckAll();
    });

    $('#searchInput').on('keyup', function() {
        $('#dataTable1').DataTable().search($(this).val()).draw();
    });

    function submitform() {
        var voters_data = [];
        $('#dataTable2').DataTable().rows().every(function(index, element) {
            var rowData = this.data();
            var voter_code       = rowData[4];
            var voter_group_code = rowData[3];
            voters_data.push({ voter_code: voter_code, voter_group_code: voter_group_code });
        });
        var hashMapJson = {
            "electioncode": $('#selectTest').select2().val(),
            "leader_code":  $('#selectleader').select2().val(),
            "voters":       JSON.stringify(voters_data)
        };
        $('#input_voters_codes').val(JSON.stringify(hashMapJson));
        $('#leadermanagerform').submit();
    }
</script>

@endsection
