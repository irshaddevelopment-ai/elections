@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

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
  .ll-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem;
  }

  /* ── Card ── */
  .ll-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }
  .ll-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .ll-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1rem 1.75rem;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem; position: relative;
  }
  .ll-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .ll-header-left { display: flex; align-items: center; gap: .75rem; }
  .ll-header-icon {
    width: 40px; height: 40px; border-radius: .6rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .ll-header-icon i { color: #f0c94d; font-size: 1rem; }
  .ll-card-header h5 { margin: 0; font-weight: 700; font-size: 1.05rem; color: #fff; }
  .ll-header-actions { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

  /* ── Toolbar (below header) ── */
  .ll-toolbar {
    padding: .9rem 1.5rem;
    background: #f8f4e8;
    border-bottom: 1px solid rgba(212,168,32,0.2);
    display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
  }

  /* ── Election select ── */
  .ll-select {
    height: 38px; border: 1.5px solid #dde3ef; border-radius: .65rem;
    font-size: .88rem; color: #0f1f40; background: #fff;
    padding: 0 .85rem; font-family: 'Cairo', sans-serif;
    min-width: 220px; flex: 1 1 200px;
    transition: border-color .18s, box-shadow .18s;
  }
  .ll-select:focus {
    border-color: #d4a820; box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    outline: none;
  }

  /* ── Search ── */
  .ll-search {
    height: 38px; padding: 0 1rem 0 2.5rem; border-radius: 2rem;
    border: 1.5px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.1)
      url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23f0c94d' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    font-size: .88rem; color: #fff; min-width: 180px;
    transition: border-color .2s, box-shadow .2s;
    font-family: 'Cairo', sans-serif;
  }
  .ll-search::placeholder { color: rgba(255,255,255,0.5); }
  .ll-search:focus {
    border-color: rgba(212,168,32,0.6);
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background-color: rgba(255,255,255,0.15);
    outline: none;
  }

  /* ── Icon action buttons ── */
  .btn-ll-icon {
    width: 38px; height: 38px; border-radius: .55rem;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .95rem; cursor: pointer;
    transition: background .18s, border-color .18s, transform .12s;
    border: 1.5px solid;
    flex-shrink: 0;
  }
  .btn-ll-icon:hover { transform: translateY(-1px); }
  .btn-ll-print {
    background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: #fff;
  }
  .btn-ll-print:hover { background: rgba(255,255,255,0.22); border-color: rgba(255,255,255,0.5); }
  .btn-ll-excel {
    background: rgba(212,168,32,0.15); border-color: rgba(212,168,32,0.4); color: #f0c94d;
  }
  .btn-ll-excel:hover { background: rgba(212,168,32,0.3); border-color: #f0c94d; }

  /* ── Table wrap ── */
  .ll-table-wrap { padding: 1.25rem 1.5rem 1.5rem; border-top: 3px solid #d4a820; }

  #dtleaders {
    border-collapse: separate !important; border-spacing: 0; width: 100% !important;
  }
  #dtleaders thead tr { background: linear-gradient(135deg, #f8f4e8, #fef9e7); }
  #dtleaders thead th {
    font-weight: 700; font-size: .85rem; color: #1e3a70;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    padding: .75rem 1rem; white-space: nowrap; text-align: center;
  }
  #dtleaders tbody tr { border-bottom: 1px solid #f0ecd8; transition: background .15s; }
  #dtleaders tbody tr:last-child { border-bottom: none; }
  #dtleaders tbody tr:hover { background: #fef9e7 !important; }
  #dtleaders tbody td {
    font-size: .88rem; color: #1e3a70;
    padding: .65rem 1rem; vertical-align: middle; text-align: center;
  }

  /* Eye button in table */
  .btn-ll-view {
    width: 30px; height: 30px; border-radius: .45rem;
    background: #eef4ff; border: 1.5px solid #c2d9ff;
    color: #1e3a70; font-size: .78rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .15s, border-color .15s;
  }
  .btn-ll-view:hover { background: #1e3a70; border-color: #1e3a70; color: #fff; }

  /* Pagination */
  .pagination .page-item.active .page-link,
  div.dataTables_wrapper div.dataTables_paginate ul.pagination .page-item.active .page-link:focus,
  .pagination .page-item.active .page-link:hover {
    background-color: #1e3a70; border-color: #1e3a70;
  }
  .page-link { color: #1e3a70; font-size: .82rem; }
  .dataTables_filter { display: none; }

  /* ── Modal ── */
  .ll-modal .modal-content {
    border-radius: 1rem; border: none;
    box-shadow: 0 8px 40px rgba(10,22,40,0.22); overflow: hidden;
  }
  .ll-modal .modal-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    border-bottom: none; padding: 1rem 1.5rem;
    display: flex; align-items: center; flex-wrap: wrap; gap: .5rem;
  }
  .ll-modal .modal-title { font-weight: 700; font-size: 1rem; color: #fff; margin: 0; }
  .ll-modal #leadergrouptitle { font-size: .88rem; color: rgba(240,201,77,0.9); margin: 0; }
  .ll-modal .modal-header .close { color: rgba(255,255,255,.75) !important; text-shadow: none; opacity: 1; }
  .ll-modal .modal-header .close:hover { color: #fff !important; }
  .ll-modal .modal-body { padding: 1.25rem; border-top: 3px solid #d4a820; }
  .ll-modal .modal-footer { border-top: 1px solid #e8edf6; padding: .85rem 1.25rem; gap: .5rem; }

  /* Modal search */
  .ll-modal-search { position: relative; margin-bottom: .85rem; }
  .ll-modal-search-icon {
    position: absolute; right: .85rem; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: .85rem; pointer-events: none;
  }
  .ll-modal-search input {
    padding-right: 2.4rem; border-radius: 2rem;
    border: 1.5px solid #dde3ef; font-size: .88rem;
    height: 38px; background: #f8faff; color: #0f1f40;
    width: 100%; font-family: 'Cairo', sans-serif;
    transition: border-color .18s, box-shadow .18s;
  }
  .ll-modal-search input:focus {
    border-color: #d4a820; box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }
  .ll-modal-search input::placeholder { color: #94a3b8; }

  /* Modal inner table */
  #dataTable_modal1 { width: 100%; border-collapse: collapse; }
  #dataTable_modal1 thead th {
    font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, #f8f4e8, #fef9e7);
    color: #1e3a70; padding: .55rem .75rem;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    text-align: center;
  }
  #dataTable_modal1 tbody tr { border-bottom: 1px solid #f0ecd8; }
  #dataTable_modal1 tbody td,
  #dataTable_modal1 tbody th {
    font-size: .83rem; color: #1e3a70;
    padding: .5rem .75rem; text-align: center; vertical-align: middle;
    border: none !important;
  }
  #dataTable_modal1 input[type="checkbox"] {
    width: 15px; height: 15px; accent-color: #1e3a70; cursor: pointer;
  }

  /* Modal buttons */
  .btn-modal-secondary {
    height: 38px; padding: 0 1.1rem; border-radius: .55rem;
    font-size: .88rem; font-weight: 600;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    cursor: pointer; transition: background .18s, border-color .18s;
    font-family: 'Cairo', sans-serif;
  }
  .btn-modal-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.4); }

  .btn-modal-primary {
    height: 38px; padding: 0 1.1rem; border-radius: .55rem;
    font-size: .88rem; font-weight: 700;
    background: linear-gradient(135deg, #1a3268, #1e4098);
    border: none; color: #f0c94d;
    cursor: pointer; transition: opacity .18s;
    font-family: 'Cairo', sans-serif;
    display: inline-flex; align-items: center; gap: .4rem;
  }
  .btn-modal-primary:hover { opacity: .88; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="ll-page" dir="rtl">
<div class="container-fluid" style="max-width:1100px;">

  @if($election)
    <?php $election_name = $election->election_name; ?>
  @else
    <?php $election_name = ''; ?>
  @endif

  <div class="ll-card">

    <!-- Header -->
    <div class="ll-card-header">
      <div class="ll-header-left">
        <div class="ll-header-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <h5>لائحة المرشدين</h5>
      </div>
      <div class="ll-header-actions">
        <input type="text" class="ll-search" id="searchInput" placeholder="بحث..." autocomplete="off">
        <button type="button" class="btn-ll-icon btn-ll-print" onclick="printleaders();" title="طباعة">
          <i class="fas fa-print"></i>
        </button>
        <button type="button" class="btn-ll-icon btn-ll-excel" id="exportBtn" title="تصدير Excel">
          <i class="fas fa-file-excel"></i>
        </button>
      </div>
    </div>

    <!-- Election filter toolbar -->
    <div class="ll-toolbar">
      <label style="font-size:.8rem;font-weight:700;color:#1e3a70;margin:0;white-space:nowrap;">
        <i class="fas fa-vote-yea fa-xs" style="color:#c8920a;margin-left:.3rem;"></i> العملية الانتخابية:
      </label>
      <select id="selectTest" class="ll-select">
        @if($Elections)
          @foreach ($Elections as $election)
            <option value="{{ $election->election_code }}">{{ $election->election_name }}</option>
          @endforeach
        @endif
      </select>
    </div>

    <!-- Table -->
    <div class="ll-table-wrap">
      <div class="table-responsive">
        <table id="dtleaders" class="table table-bordered table-sm" style="width:100%">
          <thead class="text-center">
            <tr>
              <th>المرشد</th>
              <th>رقم الهاتف</th>
              <th>عدد الأعضاء</th>
              <th>الرمز</th>
              <th style="display:none;"></th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>

  <div id="printContainer"></div>

</div>
</div>

<!-- Voters modal -->
<div class="modal fade ll-modal" id="leader_members_modal" tabindex="-1" role="dialog" aria-labelledby="leaderModalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="leaderModalLabel"><i class="fas fa-users fa-sm" style="color:#f0c94d;margin-left:.4rem;"></i> لائحة الناخبين</h5>
        <h5 id="leadergrouptitle"></h5>
        <input type="hidden" id="input_leadername" name="input_leadername" value="">
        <input type="hidden" id="input_groupname"  name="input_groupname"  value="">
        <button type="button" class="close ml-0" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div class="ll-modal-search">
          <i class="fas fa-search ll-modal-search-icon"></i>
          <input type="text" id="inputsearch" placeholder="بحث في الأسماء..." autocomplete="off">
        </div>

        <div class="table-responsive">
          <table id="dataTable_modal1" class="table table-sm mb-0">
            <thead>
              <tr class="text-center">
                <th><input type="checkbox" id="leadervotercheckbox"></th>
                <th>الإسم</th>
                <th>رقم الهاتف</th>
                <th>المجموعة</th>
                <th>العنوان</th>
                <th>رمز التعريف</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <div class="modal-footer" style="border-top:1px solid #e8edf6;padding:.85rem 0 0;margin-top:.85rem;">
          <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="button" class="btn-modal-primary" onclick="printuserscodes();">
            <i class="fas fa-print fa-sm"></i> طباعة
          </button>
        </div>

      </div>

    </div>
  </div>
</div>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    var datatable1_dataset = [];
    let progressBar = document.querySelector('.progress-bar');
    let loadingContainer = document.querySelector('.loading-container');

    $("#leadervotercheckbox").change(function() {
        if ($(this).prop("checked")) {
            $("#dataTable_modal1 tr input[type='checkbox']").prop("checked", true);
        } else {
            $("#dataTable_modal1 tr input[type='checkbox']").prop("checked", false);
        }
    });

    function showvoters(leadername, leaderinfo) {
        $('#dataTable_modal1 tbody').empty();
        var voters = leaderinfo;
        var leader = leadername;
        if (voters == '') {
            alert("لا يوجد مرشحين");
        } else {
            var infoArray = JSON.parse(voters);
            $('#leadergrouptitle').text("مجموعة المرشد : " + leader);
            $('#input_leadername').val(leader);
            infoArray.forEach(function(element) {
                var newRow = '<tr class="text-center">' +
                    '<th><input type="checkbox" class="filled-in chk-col-red chk-md" id="customCheckbox2"></th>' +
                    '<th>' + element.full_name + '</th>' +
                    '<th>' + element.mobile + '</th>' +
                    '<th></th>' +
                    '<th>' + (element.address !== null ? element.address : '') + '</th>' +
                    '<th>' + element.user_code + '</th>' +
                    '</tr>';
                $('#dataTable_modal1 tbody').append(newRow);
            });
            $("#dataTable_modal1 tr input[type='checkbox']").prop("checked", true);
            $('#leader_members_modal').modal('show');
        }
    }

    $(document).ready(function() {

        $('#searchInput').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            $('tbody tr').each(function() {
                var rowData = $(this).text().toLowerCase();
                $(this).toggle(rowData.indexOf(searchText) !== -1);
            });
        });

        var table = $('#dtleaders').DataTable({
            data: datatable1_dataset,
            searching: false,
            lengthChange: false,
            language: {
                "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_",
                "paginate": {
                    "next": "الصفحة القادمة",
                    "previous": "الصفحة السابقة"
                },
                "emptyTable": "لا توجد معلومات",
            },
            rowReorder: true,
            columnDefs: [
                { orderable: true,  className: 'reorder', targets: [0, 1, 2] },
                { orderable: false, targets: '_all' },
                { targets: [4], visible: false },
                {
                    targets: 5,
                    render: function(data, type, row, meta) {
                        if (type === 'display') {
                            return '<button class="btn-ll-view" title="عرض"><i class="fa fa-eye"></i></button>';
                        }
                        return data;
                    }
                }
            ]
        });

        function exportToExcel(data, filename) {
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(data);
            var firstRowStyle = {
                font: { bold: true },
                fill: { fgColor: { rgb: "FFFF00" } }
            };
            for (var i = 0; i < data[0].length; i++) {
                var cellRef = XLSX.utils.encode_cell({ r: 0, c: i });
                ws[cellRef].s = firstRowStyle;
            }
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
            XLSX.writeFile(wb, filename + ".xlsx");
        }

        $("#exportBtn").click(function() {
            var tabData = [];
            var rowDataOBJ = ['المرشد', 'هاتف المرشد', 'رمز المرشد', 'الناخب', 'رمز الناخب'];
            tabData.push(rowDataOBJ);
            $('#dtleaders').DataTable().rows().every(function() {
                var rowData = this.data();
                var usersdata = JSON.parse(rowData[4]);
                usersdata.forEach(function(obj) {
                    var rowDataOBJ = [rowData[0], rowData[1], rowData[3], obj.full_name, obj.user_code];
                    tabData.push(rowDataOBJ);
                });
            });
            exportToExcel(tabData, "users");
        });

    });

    fetchvotersbyelections();

    function confirmDelete() {
        if (confirm('Are you sure you want to delete this row?')) {
            alert('Delete button clicked');
        }
    }

    $('#inputsearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#dataTable_modal1 tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    function printleaders() {
        let selectedData = [];
        $('#dtleaders').DataTable().rows().every(function() {
            var rowData = this.data();
            selectedData.push({ name: rowData[0], mobile: rowData[1], usercode: rowData[3] });
        });

        let printContent = `
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <div class="container" style="text-align: right;">
                <div class="row"><div class="col">
                <h5>لائحة المرشدين للعملية الانتخابية : {{$election_name}}</h5>
                <table class="table table-bordered" style="text-align: right;">
                    <thead class="thead-dark">
                        <tr><th>الرمز</th><th>الهاتف</th><th>الاسم</th></tr>
                    </thead>
                    <tbody>`;
        selectedData.forEach(data => {
            printContent += `<tr><td>${data.usercode}</td><td>${data.mobile}</td><td>${data.name}</td></tr>`;
        });
        printContent += `</tbody></table></div></div></div>`;

        let windowWidth = 800, windowHeight = 800;
        let windowLeft = (window.screen.width  - windowWidth)  / 2;
        let windowTop  = (window.screen.height - windowHeight) / 2;
        var printWindow = window.open('', '', `width=${windowWidth},height=${windowHeight},left=${windowLeft},top=${windowTop}`);
        printWindow.document.write('<html><head><title>DIV Contents</title></head><body>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
    }

    function printuserscodes() {
        let selectedData = [];
        $('#dataTable_modal1 tbody tr').each(function() {
            var cells = $(this).find('th, td');
            selectedData.push({
                name:     $(cells[1]).text(),
                mobile:   $(cells[2]).text(),
                usercode: $(cells[5]).text()
            });
        });

        if (selectedData.length > 0) {
            let printContent = `
                <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
                <div class="container" style="text-align: right;">
                    <div class="row"><div class="col">
                    <h5>المرشد : ` + $('#input_leadername').val() + `</h5>
                    <table class="table table-bordered" style="text-align: right;">
                        <thead class="thead-dark">
                            <tr><th>الرمز</th><th>الهاتف</th><th>الاسم</th></tr>
                        </thead>
                        <tbody>`;
            selectedData.forEach(data => {
                printContent += `<tr><td>${data.usercode}</td><td>${data.mobile}</td><td>${data.name}</td></tr>`;
            });
            printContent += `</tbody></table></div></div></div>`;

            let windowWidth = 800, windowHeight = 800;
            let windowLeft = (window.screen.width  - windowWidth)  / 2;
            let windowTop  = (window.screen.height - windowHeight) / 2;
            var printWindow = window.open('', '', `width=${windowWidth},height=${windowHeight},left=${windowLeft},top=${windowTop}`);
            printWindow.document.write(printContent);
            printWindow.document.close();
        } else {
            alert("لم يتم اختيار الاسماء");
        }
    }

    $('#selectTest').change(function() {
        fetchvotersbyelections();
    });

    function fetchvotersbyelections() {
        datatable1_dataset.length = 0;
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

        var election_code = $('#selectTest').val();
        fetch('/getvotersbyelectioncode/' + election_code)
            .then(response => response.json())
            .then(data => {
                var leaders_array = data;
                datatable1_dataset.length = 0;
                leaders_array.forEach(function(item) {
                    var new_row = [
                        item['full_name'], item['mobile'],
                        item['leader_counts_members'], item['user_code'],
                        item['full_names_json'], ''
                    ];
                    datatable1_dataset.push(new_row);
                });
                $('#dtleaders').DataTable().clear().rows.add(datatable1_dataset).draw();
                success_var = 1;
            })
            .catch(error => { console.error('Error fetching data:', error); });
    }

    $('#dtleaders').on('click', 'tbody td:last-child', function() {
        var rowIndex = $('#dtleaders').DataTable().row($(this).closest('tr')).index();
        var rowData  = $('#dtleaders').DataTable().row(rowIndex).data();
        showvoters(rowData[0], rowData[4]);
    });
</script>

@endsection
