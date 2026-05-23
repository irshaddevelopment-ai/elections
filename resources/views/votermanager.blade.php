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
  .vm-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem 3rem;
  }

  /* ── Page title ── */
  .vm-page-title {
    font-size: 1.3rem; font-weight: 800; color: #1e3a70;
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: 1.5rem;
  }
  .vm-page-title i { color: #c8920a; }

  /* ── Card ── */
  .vm-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
    margin-bottom: 1.25rem;
  }
  .vm-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .vm-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1rem 1.75rem;
    display: flex; align-items: center; gap: .6rem;
    position: relative;
  }
  .vm-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .vm-header-icon {
    width: 36px; height: 36px; border-radius: .55rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .vm-header-icon i { color: #f0c94d; font-size: .9rem; }
  .vm-card-header span { font-weight: 700; font-size: .97rem; color: #fff; }

  .vm-card-body { padding: 1.25rem 1.5rem 1.5rem; border-top: 3px solid #d4a820; }

  /* ── Labels ── */
  .vm-label {
    display: block; font-size: .82rem; font-weight: 700;
    color: #1e3a70; margin-bottom: .38rem;
  }

  /* ── Inputs ── */
  .vm-input {
    border: 1.5px solid #dde3ef; border-radius: .65rem;
    font-size: .88rem; color: #0f1f40;
    background: #f8faff; height: 42px;
    font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .vm-input:focus {
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
  .vm-search-wrap { position: relative; }
  .vm-search-wrap .vm-search-icon {
    position: absolute; right: .85rem; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: .85rem; pointer-events: none;
  }
  .vm-search-wrap input {
    padding-right: 2.4rem; border-radius: 2rem;
    border: 1.5px solid #dde3ef; font-size: .88rem;
    height: 40px; background: #f8faff; color: #0f1f40;
    width: 100%; font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .vm-search-wrap input:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }
  .vm-search-wrap input::placeholder { color: #94a3b8; }

  /* ── Dual-table cards ── */
  .vm-dt-card {
    border-radius: .85rem; overflow: hidden;
    border: 1px solid rgba(212,168,32,0.2);
    box-shadow: 0 2px 8px rgba(30,58,112,0.06);
  }
  .vm-dt-title {
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
  .btn-vm-primary {
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
  .btn-vm-primary:hover {
    transform: translateY(-2px); box-shadow: 0 7px 22px rgba(212,168,32,0.5);
    color: #1a2e0f;
  }

  .btn-vm-secondary {
    height: 42px; padding: 0 1.25rem; border-radius: .65rem;
    font-size: .9rem; font-weight: 700;
    font-family: 'Cairo', sans-serif;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    transition: background .2s, border-color .2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: .45rem;
  }
  .btn-vm-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.45); color: #1a2e0f; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="vm-page" dir="rtl">
<div class="container-fluid" style="max-width:1200px;">

  <h1 class="vm-page-title"><i class="fas fa-id-card"></i> إضافة ناخبين</h1>

  <form id="votermanagerform" action="/savevoterinfo" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="input_election"     name="input_election"     value="">
    <input type="hidden" id="input_voters_codes" name="input_voters_codes" value="">

    {{-- Election select card --}}
    <div class="vm-card">
      <div class="vm-card-header">
        <div class="vm-header-icon"><i class="fas fa-vote-yea"></i></div>
        <span>بيانات العملية الانتخابية</span>
      </div>
      <div class="vm-card-body">
        <div class="row">
          <div class="col-md-6">
            <label class="vm-label" for="select_election">اسم العملية الانتخابية <span style="color:#dc3545;">*</span></label>
            <select name="select_election" id="select_election" class="js-example-basic-single form-control vm-input" style="width:100%;">
              @if($Elections)
                @foreach ($Elections as $election)
                  <option value="{{ $election->election_code }}">{{ $election->election_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- Voters tables card --}}
    <div class="vm-card">
      <div class="vm-card-header">
        <div class="vm-header-icon"><i class="fas fa-users"></i></div>
        <span>اختيار الناخبين</span>
      </div>
      <div class="vm-card-body">

        {{-- Search --}}
        <div class="vm-search-wrap mb-3">
          <i class="fas fa-search vm-search-icon"></i>
          <input type="text" id="searchInput" placeholder="بحث في الأسماء..." autocomplete="off">
        </div>

        <div class="row">
          {{-- Table 1: available --}}
          <div class="col-md-6 mb-3">
            <div class="vm-dt-card">
              <div class="vm-dt-title" id="tab1">اختيار الأسماء</div>
              <div class="table-responsive">
                <table id="dataTable1" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="selectAllVoters" title="تحديد الكل" style="width:16px;height:16px;accent-color:#1e3a70;cursor:pointer;"></th>
                      <th>الإسم</th>
                      <th>رقم الهاتف</th>
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
            <div class="vm-dt-card">
              <div class="vm-dt-title" id="tab2">الأسماء المختارة</div>
              <div class="table-responsive">
                <table id="dataTable2" class="table table-sm mb-0" style="width:100%">
                  <thead>
                    <tr>
                      <th>الإسم</th>
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
          <button type="button" class="btn-vm-primary" onclick="savevoterinfo();">
            <i class="fas fa-save"></i> حفظ المعلومات
          </button>
          <button type="button" class="btn-vm-secondary" onclick="clearForm()">
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

    let progressBar      = document.querySelector('.progress-bar');
    let loadingContainer = document.querySelector('.loading-container');

    $(document).ready(function() {
        $('#select_election').select2({
            placeholder: 'اختيار عملية إنتخابية',
            allowClear: true,
            language: { noResults: function() { return 'لا يوجد نتائج'; } }
        });

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
                { targets: [3, 4], visible: false },
                { targets: 0, render: function(data, type) { return type === 'display' ? '<input type="checkbox">' : data; } }
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

        fetchdata($('#select_election').select2().val());

        $('#select_election').on('change', function() {
            fetchdata($(this).select2().val());
        });

        $('#searchInput').on('keyup', function() {
            $('#dataTable1').DataTable().search($(this).val()).draw();
        });
    });

    function fetchdata(electioncode) {
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

        fetch('/voterprofiles/' + electioncode)
            .then(response => response.json())
            .then(data => {
                datatable1_dataset.length = 0;
                data.forEach(function(item) {
                    datatable1_dataset.push(['', item.full_name, item.mobile, item.profile_code, item.voter_status]);
                });
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
                success_var = 1;
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function clearForm() {
        var form = document.getElementById('votermanagerform');
        if (form) form.reset();
    }

    $('#dataTable1').on('change', 'tbody td:first-child input[type="checkbox"]', function() {
        var row     = $('#dataTable1').DataTable().row($(this).closest('tr'));
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

    $(document).on('change', '#selectAllVoters', function() {
        var checked = $(this).is(':checked');
        $('#dataTable1').DataTable().rows({ search: 'applied' }).every(function() {
            var rowData = this.data();
            var new_row = [rowData[1], rowData[2], rowData[3]];
            $(this.node()).find('input[type="checkbox"]').prop('checked', checked);
            if (checked) {
                var exists = datatable2_dataset.some(function(r) { return r[2] == new_row[2]; });
                if (!exists) datatable2_dataset.push(new_row);
            } else {
                for (var i = datatable2_dataset.length - 1; i >= 0; i--) {
                    if (datatable2_dataset[i][2] == new_row[2]) datatable2_dataset.splice(i, 1);
                }
            }
        });
        $('#dataTable2').DataTable().clear().rows.add(datatable2_dataset).draw();
    });

    function savevoterinfo() {
        var voters_data = [];
        $('#dataTable2').DataTable().rows().every(function() {
            voters_data.push(this.data()[2]);
        });
        $('#input_voters_codes').val(JSON.stringify({
            electioncode: $('#select_election').select2().val(),
            voters: JSON.stringify(voters_data)
        }));
        $('#votermanagerform').submit();
    }
</script>

@endsection
