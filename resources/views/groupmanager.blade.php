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
  .gm-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem 3rem;
  }

  /* ── Page title ── */
  .gm-page-title {
    font-size: 1.3rem; font-weight: 800; color: #1e3a70;
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: 1.5rem;
  }
  .gm-page-title i { color: #c8920a; }

  /* ── Card ── */
  .gm-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
    margin-bottom: 1.25rem;
  }
  .gm-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .gm-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1rem 1.75rem;
    display: flex; align-items: center; gap: .6rem;
    position: relative;
  }
  .gm-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .gm-header-icon {
    width: 36px; height: 36px; border-radius: .55rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .gm-header-icon i { color: #f0c94d; font-size: .9rem; }
  .gm-card-header span { font-weight: 700; font-size: .97rem; color: #fff; }

  .gm-card-body { padding: 1.25rem 1.5rem 1.5rem; border-top: 3px solid #d4a820; }

  /* ── Labels ── */
  .gm-label {
    display: block; font-size: .82rem; font-weight: 700;
    color: #1e3a70; margin-bottom: .38rem;
  }

  /* ── Inputs ── */
  .gm-input {
    border: 1.5px solid #dde3ef; border-radius: .65rem;
    font-size: .88rem; color: #0f1f40;
    background: #f8faff; height: 42px;
    font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .gm-input:focus {
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
  .gm-search-wrap { position: relative; }
  .gm-search-icon {
    position: absolute; right: .85rem; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: .85rem; pointer-events: none;
  }
  .gm-search-wrap input {
    padding-right: 2.4rem; border-radius: 2rem;
    border: 1.5px solid #dde3ef; font-size: .88rem;
    height: 40px; background: #f8faff; color: #0f1f40;
    width: 100%; font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .gm-search-wrap input:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }
  .gm-search-wrap input::placeholder { color: #94a3b8; }

  /* ── Gender filter toggles ── */
  .gm-filter-bar {
    display: flex; align-items: center; gap: 1.25rem;
    padding: .6rem .85rem; background: #f8f4e8;
    border: 1px solid rgba(212,168,32,0.25); border-radius: .65rem;
    margin-bottom: .75rem;
  }
  .gm-filter-bar .gm-filter-label {
    font-size: .8rem; font-weight: 700; color: #1e3a70;
    margin-left: 0.5rem;
  }
  /* custom-switch gold tint */
  .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #c8920a;
    border-color: #c8920a;
  }
  .custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 3px rgba(212,168,32,0.25);
  }

  /* ── Dual-table cards ── */
  .gm-dt-card {
    border-radius: .85rem; overflow: hidden;
    border: 1px solid rgba(212,168,32,0.2);
    box-shadow: 0 2px 8px rgba(30,58,112,0.06);
  }
  .gm-dt-title {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    color: #f0c94d; font-size: .85rem; font-weight: 700;
    padding: .6rem 1rem; text-align: center; letter-spacing: .3px;
  }

  /* ── DataTables ── */
  #dataTable1 thead tr:first-child th,
  #dataTable2 thead tr:first-child th { display: none; }

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

  /* Checkbox style */
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
  .btn-gm-primary {
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
  .btn-gm-primary:hover {
    transform: translateY(-2px); box-shadow: 0 7px 22px rgba(212,168,32,0.5);
    color: #1a2e0f;
  }

  .btn-gm-secondary {
    height: 42px; padding: 0 1.25rem; border-radius: .65rem;
    font-size: .9rem; font-weight: 700;
    font-family: 'Cairo', sans-serif;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    transition: background .2s, border-color .2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: .45rem;
  }
  .btn-gm-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.45); color: #1a2e0f; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="gm-page" dir="rtl">
<div class="container-fluid" style="max-width:1200px;">

  <h1 class="gm-page-title"><i class="fas fa-users"></i> إضافة مجموعة ناخبين</h1>

  <form id="votergroupmanagerform" action="/savevotergroup" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="input_voters_group" name="input_voters_group" class="form-control" autocomplete="off" value="">

    {{-- Election + group name card --}}
    <div class="gm-card">
      <div class="gm-card-header">
        <div class="gm-header-icon"><i class="fas fa-vote-yea"></i></div>
        <span>بيانات المجموعة</span>
      </div>
      <div class="gm-card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="gm-label" for="select_election">اسم العملية الانتخابية <span style="color:#dc3545;">*</span></label>
            <select name="Select Dropdown" id="select_election" class="js-example-basic-single form-control gm-input" style="width:100%;">
              @if($Elections)
                @foreach ($Elections as $election)
                  <option value="{{ $election->election_code }}">{{ $election->election_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="gm-label" for="input_voter_group_name">اسم المجموعة <span style="color:#dc3545;">*</span></label>
            <input type="text" class="form-control gm-input" id="input_voter_group_name" name="input_voter_group_name"
              required oninvalid="this.setCustomValidity('أدخل اسم المجموعة')" oninput="this.setCustomValidity('')"
              autocomplete="off" placeholder="اسم المجموعة">
          </div>
        </div>
      </div>
    </div>

    {{-- Voter selection card --}}
    <div class="gm-card">
      <div class="gm-card-header">
        <div class="gm-header-icon"><i class="fas fa-users"></i></div>
        <span>اختيار الناخبين</span>
      </div>
      <div class="gm-card-body">

        {{-- Search --}}
        <div class="gm-search-wrap mb-3">
          <i class="fas fa-search gm-search-icon"></i>
          <input type="text" id="sharedSearch" placeholder="بحث في الأسماء..." autocomplete="off">
        </div>

        {{-- Gender filter --}}
        <div class="gm-filter-bar mb-3">
          <span class="gm-filter-label"><i class="fas fa-filter fa-xs" style="color:#c8920a;margin-left:.3rem;"></i> تصفية حسب الجنس:</span>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="input_voters_male" name="input_voters_male" checked onchange="filterTable();">
            <label class="custom-control-label" for="input_voters_male">ذكر</label>
          </div>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="input_voters_female" name="input_voters_female" checked onchange="filterTable();">
            <label class="custom-control-label" for="input_voters_female">أنثى</label>
          </div>
        </div>

        <div class="row">
          {{-- Table 1: available --}}
          <div class="col-md-6 mb-3">
            <div class="gm-dt-card">
              <div class="gm-dt-title" id="tab1">اختيار الأسماء</div>
              <div class="table-responsive">
                <table id="dataTable1" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th colspan="6" class="text-center" style="display:none;" id="tab1_inner">اختيار الأسماء</th>
                    </tr>
                    <tr>
                      <th></th>
                      <th>الإسم</th>
                      <th>الجنس</th>
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
            <div class="gm-dt-card">
              <div class="gm-dt-title" id="tab2">الأسماء المختارة</div>
              <div class="table-responsive">
                <table id="dataTable2" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th colspan="4" class="text-center" style="display:none;" id="tab2_inner">الأسماء المختارة</th>
                    </tr>
                    <tr>
                      <th>اسم الناخبين</th>
                      <th>الجنس</th>
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
          <button type="button" class="btn-gm-primary" onclick="savevotersgroup()">
            <i class="fas fa-save"></i> حفظ المعلومات
          </button>
          <button type="button" class="btn-gm-secondary" onclick="clearForm()">
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

    $('#select_election').select2({
        placeholder: 'اختيار عملية إنتخابية',
        allowClear: true,
        language: {
            noResults: function() {
                return 'لا يوجد نتائج';
            }
        }
    });
    $('#selectLeader').select2({
        placeholder: 'اختيار معرف',
        allowClear: true,
        language: {
            noResults: function() {
                return 'لا يوجد نتائج';
            }
        }
    });

    var table1 = $('#dataTable1').DataTable({
            data: datatable1_dataset,
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
                    "targets": [5],
                    "visible": false
                },
                {
                    targets: 0,
                    render: function(data, type, row, meta) {
                        if (type === 'display') {
                            return '<input type="checkbox">';
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
                    "targets": [3],
                    "visible": false
                }
            ]
        });

        var electioncode = $('#select_election').select2().val();
        fetchdata(electioncode);

    function clearForm() {
        var form = document.getElementById('usermanagerform');
        form.reset();
        document.getElementById('image-preview').innerHTML = '';
        var currentDate = new Date();
        var formattedDate = currentDate.toISOString().split('T')[0];
        $('#input_age').val(formattedDate);
        $('#input_fullname').focus();
    }

    $('#sharedSearch').on('keyup', function() {
        $('#dataTable1').DataTable().search($(this).val()).draw();
    });

    function savevotersgroup(){
        var voters_group_data = [];
        $('#dataTable2').DataTable().rows().every(function(index, element) {
            var rowData = this.data();
            voters_group_data.push(rowData[3]);
        });
        var hashMapJson = {
            "electioncode": $('#select_election').val(),
            "voter_group_name": $('#input_voter_group_name').val(),
            "votersgroup": JSON.stringify(voters_group_data),
        };
        var jsonData = JSON.stringify(hashMapJson);
        $('#input_voters_group').val(jsonData);
        $('#votergroupmanagerform').submit();
    }

    function filterTable() {
        var isChecked_male   = $('#input_voters_male').prop('checked');
        var isChecked_female = $('#input_voters_female').prop('checked');
        var filter;
        if ((isChecked_male == true)  && (isChecked_female == true))  filter = '';
        if ((isChecked_male == false) && (isChecked_female == false)) filter = 0;
        if ((isChecked_male == true)  && (isChecked_female == false)) filter = 1;
        if ((isChecked_male == false) && (isChecked_female == true))  filter = 2;
        var table = document.getElementById("dataTable1");
        var tr = table.getElementsByTagName("tr");
        for (var i = 0; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[6];
            if (td) {
                var txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function fetchdata(electioncode) {
        datatable2_dataset.length = 0;
        $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
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

        fetch('/voterprofilesforgroups/' + electioncode)
            .then(response => response.json())
            .then(data => {
                var profiles_array = data;
                datatable1_dataset.length = 0;
                profiles_array.forEach(function(item) {
                    var sex = item.sex == 1 ? 'ذكر' : 'أنثى';
                    var new_row = ['', item.full_name, sex, item.mobile, item.voter_group_name, item.profile_code, ''];
                    datatable1_dataset.push(new_row);
                });
                $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
                $('#dataTable1').DataTable().rows().every(function() {
                    var rowData = this.data();
                    if (rowData[4] != '') {
                        var name = rowData[1];
                        var sex = rowData[2] == 1 ? 'ذكر' : 'أنثى';
                        var mobile = rowData[3];
                        var profilecode = rowData[3];
                        var new_row2 = [name, sex, mobile, profilecode];
                    }
                });
                $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
                success_var = 1;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    $('#dataTable1').on('change', 'tbody td:first-child input[type="checkbox"]', function() {
        var row = $('#dataTable1').DataTable().row($(this).closest('tr'));
        var rowData = row.data();
        var name = rowData[1];
        var sex = rowData[2] == 1 ? 'ذكر' : 'أنثى';
        var mobile = rowData[3];
        var profilecode = rowData[5];
        var isChecked = $(this).is(':checked');
        var new_row = [name, sex, mobile, profilecode];
        if (isChecked) {
            datatable2_dataset.push(new_row);
        } else {
            for (var i = datatable2_dataset.length - 1; i >= 0; i--) {
                if (datatable2_dataset[i][2] == new_row[2]) {
                    datatable2_dataset.splice(i, 1);
                }
            }
        }
        $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
    });

    $('#select_election').on('change', function() {
        var electioncode = $(this).select2().val();
        fetchdata(electioncode);
    });
</script>

@endsection
