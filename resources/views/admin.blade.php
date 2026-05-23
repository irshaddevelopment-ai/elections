@extends('layouts.app')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@section('content')
<style>
  .dashboard-page { background: #f4f7fb; min-height: 100vh; padding: 2rem 0 3rem; }

  /* ── Page header ── */
  .page-header-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 16px rgba(13,110,253,.08);
    padding: 1.25rem 2rem;
    margin-bottom: 1.75rem;
    border-right: 5px solid #0d6efd;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .page-header-card .page-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a3a6b;
    margin: 0;
  }
  .page-header-card .page-label {
    font-size: 0.82rem;
    color: #6c757d;
    margin-bottom: 0.2rem;
  }

  /* ── Cards ── */
  .dash-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 16px rgba(13,110,253,.08);
    overflow: hidden;
    margin-bottom: 1.5rem;
  }
  .dash-card-header {
    background: linear-gradient(135deg, #1a3a6b 0%, #0d6efd 100%);
    padding: .85rem 1.5rem;
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    display: flex;
    align-items: center;
    gap: .5rem;
  }
  .dash-card-body { padding: 1.25rem 1.5rem; }

  /* ── Search bar ── */
  .search-wrap { position: relative; max-width: 340px; }
  .search-wrap input {
    border: 1.5px solid #dce8ff;
    border-radius: 2rem;
    padding: .48rem 2.6rem .48rem 1rem;
    font-size: .88rem;
    color: #1a3a6b;
    background: #f8faff;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
  }
  .search-wrap input:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.12); outline: none; }
  .search-wrap .search-icon {
    position: absolute;
    left: .9rem; top: 50%; transform: translateY(-50%);
    color: #6c757d; font-size: .8rem; pointer-events: none;
  }

  /* ── Elections table ── */
  #dtelections { border-collapse: separate; border-spacing: 0; width: 100% !important; }
  #dtelections thead th {
    background: #f0f5ff;
    color: #1a3a6b;
    font-weight: 700;
    font-size: .88rem;
    padding: .8rem 1rem;
    border-bottom: 2px solid #dce8ff;
    text-align: center;
    white-space: nowrap;
  }
  #dtelections tbody td {
    font-size: .88rem;
    padding: .75rem 1rem;
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid #f0f3f9;
    color: #2c3e50;
  }
  #dtelections tbody tr:last-child td { border-bottom: none; }
  #dtelections tbody tr:hover { background: #f5f8ff; }

  /* ── Round controls ── */
  .round-row {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: .3rem 0;
    border-bottom: 1px dashed #eef2ff;
  }
  .round-row:last-child { border-bottom: none; }
  .round-badge {
    font-size: .75rem;
    font-weight: 700;
    color: #1a3a6b;
    background: #eef4ff;
    border-radius: 1rem;
    padding: .2rem .65rem;
    min-width: 60px;
    text-align: center;
  }

  /* ── Custom switches ── */
  .custom-control-label { font-size: .82rem; color: #2c4a7c; font-weight: 500; cursor: pointer; }
  .custom-switch .custom-control-input:checked ~ .custom-control-label::before { background-color: #0d6efd; border-color: #0d6efd; }

  /* ── Chart cards ── */
  .chart-stat {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem .75rem;
    margin-bottom: 1rem;
    font-size: .83rem;
    color: #2c4a7c;
  }
  .chart-stat .stat-item { display: flex; align-items: center; gap: .3rem; }
  .chart-stat .stat-num { font-weight: 700; font-size: 1rem; color: #1a3a6b; }
  .chart-canvas-wrap { display: flex; justify-content: center; }
  canvas { max-height: 260px; }

  /* ── Pagination ── */
  .dataTables_wrapper .pagination .page-item.active .page-link,
  .dataTables_wrapper .pagination .page-item.active .page-link:focus,
  .dataTables_wrapper .pagination .page-item.active .page-link:hover { background-color: #0d6efd; border-color: #0d6efd; }
  .dataTables_wrapper .pagination .page-link { color: #0d6efd; border-radius: .45rem; }
  .dataTables_filter { display: none; }

  /* ── DataTable1 (modal) ── */
  #dataTable1 thead th { font-weight: 700; font-size: .85rem; background: #f0f5ff; color: #1a3a6b; }
  #dataTable1 tbody td { font-size: .82rem; }

  /* ── Modals ── */
  .modal-content { border: none; border-radius: 1rem; box-shadow: 0 8px 40px rgba(0,0,0,.18); }
  .modal-header { background: linear-gradient(135deg, #1a3a6b, #0d6efd); border-radius: 1rem 1rem 0 0; padding: 1rem 1.5rem; }
  .modal-header .modal-title { color: #fff; font-weight: 700; font-size: .95rem; }
  .modal-header .btn-close { filter: invert(1); opacity: .85; }
  .modal-footer { border-top: 1px solid #f0f3f9; }
  .modal-footer .btn { border-radius: .6rem; font-weight: 600; }

  /* ── Filter bar (inside datamodal header) ── */
  .modal-filter-bar {
    background: #f8faff;
    border-bottom: 1px solid #eef2ff;
    padding: .85rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
  }
  .modal-filter-bar input[type="date"] {
    border: 1.5px solid #dce8ff;
    border-radius: .6rem;
    padding: .4rem .75rem;
    font-size: .85rem;
    color: #1a3a6b;
    background: #fff;
  }
  .modal-stats {
    background: #f0f5ff;
    padding: .6rem 1.25rem;
    font-size: .85rem;
    color: #1a3a6b;
    font-weight: 600;
    border-bottom: 1px solid #eef2ff;
  }
  .modal-stats span { font-size: 1rem; font-weight: 700; }

  /* ── Alert messages ── */
  #pageMessages { position: relative; width: 100%; }
  #successMessage { font-size: .92rem; border-radius: .75rem; text-align: right; }

  /* ── Rotate animation ── */
  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  .rotate { animation: spin 1s linear infinite; }

  /* ── Search in modal ── */
  .modal-search input {
    border: 1.5px solid #dce8ff;
    border-radius: .6rem;
    font-size: .85rem;
    padding: .45rem .85rem;
    width: 100%;
  }
  .modal-search input:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.1); outline: none; }
</style>

<?php
  $electioncode = $current_election ? $current_election->election_code : '';
?>

<div class="dashboard-page" dir="rtl">
  <div class="container-lg">

    <div id="pageMessages"></div>

    {{-- ── Page Header ── --}}
    <div class="page-header-card">
      <div>
        <p class="page-label"><i class="fas fa-tachometer-alt fa-xs me-1"></i> لوحة التحكم</p>
        <h1 class="page-title">إطلاق عملية انتخابية</h1>
      </div>
      <div class="search-wrap">
        <input type="search" id="searchInput" placeholder="بحث في العمليات..." autocomplete="off">
        <i class="fas fa-search search-icon"></i>
      </div>
    </div>

    {{-- ── Elections Table ── --}}
    <div class="dash-card">
      <div class="dash-card-header">
        <i class="fas fa-vote-yea fa-sm"></i> العمليات الانتخابية
      </div>
      <div class="dash-card-body" style="padding:0;">
        <div class="table-responsive">
          <table id="dtelections" class="table table-hover mb-0" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th style="display:none">election_code</th>
                <th>اسم العملية</th>
                <th>تاريخ الإطلاق</th>
                <th>الحالة</th>
                <th>الجولات</th>
                <th style="display:none"></th>
              </tr>
            </thead>
            <tbody>
              @if($Elections)
              @foreach ($Elections as $election)
              <?php
                $ElectionRoundsHashMap_Obj = $ElectionRoundsHashMap[$election['election_code']];
                $rowspanvar = sizeof($ElectionRoundsHashMap_Obj);
                $isChecked = $election['election_status'];
                $isstatusdisabled = false;
                if (isset($ElectionRoundsHashMap_Obj)) {
                  foreach ($ElectionRoundsHashMap_Obj as $v) {
                    if ($v->round_status != 0) { $isstatusdisabled = true; break; }
                  }
                }
              ?>
              <tr>
                <td style="display:none">{{ $election['election_code'] }}</td>
                <td style="font-weight:600;color:#1a3a6b;">{{ $election['election_name'] }}</td>
                <td style="color:#6c757d;font-size:.83rem;">{{ $election['election_date'] }}</td>
                <td id="activate_td_{{ $loop->index }}">
                  <div class="custom-control custom-switch mb-0">
                    <input type="checkbox" class="custom-control-input input_election_status_{{ $loop->index }}"
                      id="input_election_status_{{ $loop->index }}" name="input_election_status"
                      onchange="showconfirmupdatestatus(this);"
                      @if($isChecked) checked @endif
                      @if($isstatusdisabled) disabled @endif>
                    <label class="custom-control-label" for="input_election_status_{{ $loop->index }}">مفعلة</label>
                  </div>
                </td>
                <td id="round_td_{{ $loop->index }}">
                  <?php $counter_var = 0; $oldroundstatus = 0; ?>
                  @if(isset($ElectionRoundsHashMap_Obj))
                  @foreach($ElectionRoundsHashMap_Obj as $round)
                  <?php
                    $islaunched  = false; $isfinished = false; $isdisabled = true;
                    $round_number = $round->round_number;
                    $roundstatus  = $round->round_status;
                    if (($counter_var == 0) && $isChecked && ($roundstatus != 2)) $isdisabled = false;
                    if (($roundstatus == 1) || ($oldroundstatus == 2) && ($roundstatus != 2)) $isdisabled = false;
                    if ($roundstatus == 1) $islaunched = true;
                    if ($roundstatus == 2) $isfinished = true;
                    $oldroundstatus = $roundstatus;
                  ?>
                  <div class="round-row" id="row_div_{{ $loop->parent->index }}_{{ $counter_var }}">
                    <span class="round-badge">الجولة {{ $round->round_number }}</span>
                    <input type="hidden" id="input_election_round_{{ $loop->parent->index }}_{{ $counter_var }}" value="{{ $round->round_number }}" />
                    <input type="hidden" id="round_status_{{ $loop->parent->index }}_{{ $counter_var }}" value="{{ $isdisabled }}" />
                    <div class="custom-control custom-switch" id="startelection">
                      <input type="checkbox" class="custom-control-input"
                        id="input_election_status_{{ $loop->parent->index }}_{{ $counter_var }}"
                        name="input_election_round_{{ $loop->parent->index }}_{{ $counter_var }}"
                        onchange="updateLaunchstatus(this);"
                        @if($islaunched) checked @endif>
                      <label class="custom-control-label" for="input_election_status_{{ $loop->parent->index }}_{{ $counter_var }}">إطلاق</label>
                    </div>
                    <div class="custom-control custom-switch" id="endelection">
                      <input type="checkbox" class="custom-control-input"
                        id="input_election_launch_{{ $loop->parent->index }}_{{ $counter_var }}_2"
                        name="input_election_launch_{{ $loop->parent->index }}_{{ $counter_var }}_2"
                        onchange="finishElectionRound(this);"
                        @if($isfinished) checked @endif>
                      <label class="custom-control-label" for="input_election_launch_{{ $loop->parent->index }}_{{ $counter_var }}_2">إنهاء</label>
                    </div>
                  </div>
                  <?php $counter_var++; ?>
                  @endforeach
                  @endif
                </td>
                <td style="display:none;">{{ $election_users_exists }}</td>
              </tr>
              @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="row g-4 mb-4">

      {{-- Vote chart --}}
      <div class="col-md-6">
        <div class="dash-card h-100">
          <div class="dash-card-header"><i class="fas fa-check-circle fa-sm"></i> نسبة التصويت</div>
          <div class="dash-card-body">
            @if($current_election)
            <div class="chart-stat">
              <span class="stat-item">
                <i class="fas fa-vote-yea" style="color:#0d6efd;font-size:.75rem;"></i>
                {{ $current_election->election_name }}
              </span>
              <span class="stat-item">
                العدد الكلي: <span class="stat-num" id="votespan">—</span>
              </span>
            </div>
            @endif
            <div class="chart-canvas-wrap">
              <canvas id="votechart" width="300" height="260"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- Login chart --}}
      <div class="col-md-6">
        <div class="dash-card h-100">
          <div class="dash-card-header"><i class="fas fa-sign-in-alt fa-sm"></i> نسبة تسجيل الدخول</div>
          <div class="dash-card-body">
            @if($current_election)
            <div class="chart-stat">
              <span class="stat-item">
                <i class="fas fa-vote-yea" style="color:#0d6efd;font-size:.75rem;"></i>
                {{ $current_election->election_name }}
              </span>
              <span class="stat-item">
                المجموع: <span class="stat-num" id="loginspan">—</span>
              </span>
              <span class="stat-item">
                المرشحون: <span class="stat-num" id="candidatesspan">—</span>
              </span>
              <span class="stat-item">
                الناخبون: <span class="stat-num" id="votersspan">—</span>
              </span>
            </div>
            @endif
            <div class="chart-canvas-wrap">
              <canvas id="loginchart" width="300" height="260"></canvas>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- ── Voters state (included partial) ── --}}
    @include('admin_voters_state')

  </div>
</div>

{{-- ── Voters detail modal ── --}}
<div class="modal fade" id="datamodal" tabindex="-1" aria-labelledby="datamodalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width:85%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="datamodalLabel"><i class="fas fa-users fa-sm me-2"></i> بيانات الناخبين</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="alert alert-success mx-3 mt-3 mb-0" role="alert" id="successMessage" style="display:none;font-size:.9rem;"></div>

      <div class="modal-filter-bar">
        <input type="date" id="loggedinfilterdate" />
        <button class="btn btn-primary btn-sm" type="button" id="btn_filterloggedin">
          <i class="fas fa-filter fa-xs me-1"></i> تصفية
        </button>
        <button class="btn btn-outline-secondary btn-sm" type="button" id="btn_clearfilterloggedin">
          <i class="fas fa-times fa-xs me-1"></i> إلغاء التصفية
        </button>
      </div>

      <div class="modal-stats" id="allnumber">
        العدد الكلي: <span id="modal_allloggedusers">—</span>
        &nbsp;|&nbsp;
        المسجلون الجدد: <span id="modal_newloggedusers">—</span>
      </div>

      <div class="modal-body pt-2">
        <div class="modal-search mb-3">
          <input type="text" id="searchInputmodal" placeholder="بحث في الأسماء..." autocomplete="off">
        </div>
        <div class="table-responsive">
          <table id="dataTable1" class="table table-hover table-bordered table-sm" style="width:100%">
            <thead class="text-center">
              <tr>
                <th>الناخب</th>
                <th>المرشد</th>
                <th>الهاتف</th>
                <th>الرمز</th>
                <th>تسجيل الدخول</th>
                <th>تسجيل الخروج</th>
                <th>التصويت</th>
                <th>تاريخ الدخول</th>
                <th>prf_code</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="text-center"></tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-success" id="exportBtn">
          <i class="fas fa-file-excel fa-xs me-1"></i> Excel
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ── Re-encode confirm modal ── --}}
<div class="modal fade" id="confirmupdatestatus" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">
          <i class="fas fa-exclamation-triangle fa-sm me-2"></i> تأكيد الترميز
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="text-align:right;font-size:.95rem;color:#2c3e50;">
        الترميز قد فُعِّل سابقاً. هل تريد ترميز الكل من جديد؟
      </div>
      <form id="modal_delete_form" action="/deleteuser" method="post">
        @csrf
        <input type="hidden" id="modal_profile_code" name="modal_profile_code">
        <div class="modal-footer">
          <button type="button" id="no_recoding" class="btn btn-outline-primary">ترميز الأسماء الجديدة فقط</button>
          <button type="button" id="recoding" class="btn btn-danger">
            <i class="fas fa-sync fa-xs me-1"></i> إعادة الترميز الكامل
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/mdbootstrap@4.19.1/js/mdb.min.js" integrity="sha384-rGIQNa4XjtoK9dlLj8p01b2dHl2Rz5LvUyRVVFFpX5zEx/kouLYb5C6HPLFXEfn3" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

<script>
  var index = -1;
  var datevar = new Date();
  var click_var = -1;
  var datatable1_dataset = [];
  var table1 = null;
  let progressBar = document.querySelector('.progress-bar');
  let loadingContainer = document.querySelector('.loading-container');

  $(document).ready(function() {

    var table = $('#dtelections').DataTable({
      searching: false,
      lengthChange: false,
      language: {
        sInfo: 'عرض _START_ إلى _END_ من أصل _TOTAL_',
        paginate: { next: 'التالي', previous: 'السابق' },
        emptyTable: 'لا توجد معلومات'
      }
    });

    $('#exportBtn').click(function() {
      var tabData = [['الناخب','المرشد','هاتف','رمز','تسجيل الدخول','تسجيل الخروج','تاريخ الدخول']];
      $('#dataTable1').DataTable().rows().every(function() {
        var r = this.data();
        tabData.push([r[0],r[1],r[2],r[3],r[4],r[5],r[7]]);
      });
      exportToExcel(tabData, 'loggedin');
    });

    $('#searchInput').on('keyup', function() {
      var s = $(this).val().toLowerCase();
      $('#dtelections tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
      });
    });

    $('#searchInputmodal').on('keyup', function() {
      $('#dataTable1').DataTable().search($(this).val()).draw();
    });

    var table1 = $('#dataTable1').DataTable({
      data: datatable1_dataset,
      searching: true,
      lengthChange: false,
      aaSorting: [[1,'asc'],[5,'asc'],[7,'desc']],
      info: false,
      dom: 'rtp',
      language: {
        paginate: { next: 'التالي', previous: 'السابق' },
        emptyTable: 'لا توجد معلومات'
      },
      rowReorder: true,
      columnDefs: [
        { className: 'dt-center', targets: '_all' },
        { orderable: true, className: 'reorder', targets: [0,1,2,3,4,5,6,7] },
        { orderable: false, targets: '_all' },
        {
          targets: 9,
          render: function(data, type) {
            if (type === 'display') return '<i class="fa fa-refresh" aria-hidden="true" id="refreshIcon"></i>';
            return data;
          }
        }
      ]
    });

    $('#dataTable1').on('click', 'tbody td:last-child', function() {
      var rowIndex = $('#dataTable1').DataTable().row($(this).closest('tr')).index();
      var rowData  = $('#dataTable1').DataTable().row(rowIndex).data();
      resetusercode(rowData[8], rowIndex);
    });

    $('#btn_filterloggedin').on('click', function() {
      fetchdata(index, click_var, new Date($('#loggedinfilterdate').val()));
    });
    $('#btn_clearfilterloggedin').on('click', function() {
      fetchdata(index, click_var, '');
    });
  });

  function exportToExcel(data, filename) {
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.aoa_to_sheet(data);
    var headerStyle = { font: { bold: true }, fill: { fgColor: { rgb: 'FFFF00' } } };
    for (var i = 0; i < data[0].length; i++) {
      var ref = XLSX.utils.encode_cell({ r: 0, c: i });
      ws[ref].s = headerStyle;
    }
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, filename + '.xlsx');
  }

  function resetusercode(prfcode, rowIndex) {
    $('#refreshIcon').addClass('rotate');
    axios.put('/resetusercode/' + prfcode)
      .then(response => {
        $('#refreshIcon').removeClass('rotate');
        showalert('تم تفعيل الرمز', 1, 2000);
        if (rowIndex !== -1) $('#dataTable1').DataTable().cell(rowIndex, 5).data('نعم').draw();
      })
      .catch(error => alert(error));
  }

  function showalert(msg, type, timeout) {
    var el = $('#successMessage');
    el.removeClass('alert-danger alert-success').addClass(type == 1 ? 'alert-success' : 'alert-danger');
    el.text(msg).show();
    setTimeout(function() { el.hide(); }, timeout);
  }

  function showconfirmupdatestatus(senderobj) {
    var checkanotherelecactive = 0;
    if (checkanotherelecactive == 0) {
      sender_g_var = senderobj;
      var election_status = $('#' + senderobj.id).prop('checked') ? '1' : '0';
      if (election_status == 1) {
        var electionstatusvar = senderobj.closest('tr').cells[5].textContent;
        if (electionstatusvar == 1) { $('#confirmupdatestatus').modal('show'); }
        else { updatestatus(sender_g_var, 0); }
      } else {
        updatestatus(sender_g_var, 0);
      }
    }
  }

  $('#no_recoding').on('click', function() { updatestatus(sender_g_var, 0); });
  $('#recoding').on('click', function() { updatestatus(sender_g_var, 1); });

  function enabledisablelauncher() {
    $('#dtelections tr').slice(1).each(function(index) {
      var tdobj = $('#round_td_' + index);
      var divs  = tdobj.find('div').filter('[id*="row_div_"]');
      divs.each(function() {
        var checkboxes_status = $(this).find('input[type="checkbox"]').filter('[id*="input_election_status"]');
        var checkboxes_launch = $(this).find('input[type="checkbox"]').filter('[id*="input_election_launch"]');
        var isdisabled = $(this).find('input[type="hidden"]').filter('[id*="round_status_"]').val();
        checkboxes_launch.each(function() { $(this).prop('disabled', isdisabled == 1); });
        checkboxes_status.each(function(i) {
          $(this).prop('disabled', isdisabled == 1);
          var isChecked = checkboxes_status.eq(i).prop('checked');
          checkboxes_launch.eq(i).prop('disabled', !isChecked);
        });
      });
    });
  }

  enabledisablelauncher();

  function updatestatus(senderobj, codingvar) {
    var election_code   = senderobj.closest('tr').cells[0].textContent;
    var rowIndex        = $('#dtelections tr').index(senderobj.closest('tr')) - 1;
    var election_status = $('#' + senderobj.id).prop('checked') ? '1' : '0';
    var tdobj           = $('#round_td_' + rowIndex);

    showOverlay();
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        senderobj.closest('tr').cells[5].textContent = '1';
        clearInterval(interval);
        loadingContainer.style.display = 'none';
        hideOverlay();
        location.reload();
      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);

    axios.put('/updatestatus/' + election_code + '/' + election_status + '/' + codingvar)
      .then(response => {
        createAlert('', 'تم إضافة الرمز بنجاح', '', 'success', true, true, 'pageMessages');
        success_var = 1;
        $('#confirmupdatestatus').modal('hide');
        enabledisablelauncher();
      })
      .catch(error => console.error('Error:', error));

    if (election_status == 1) {
      senderobj.closest('tr').cells[5].textContent = '1';
      tdobj.find('input[type="checkbox"]').prop('disabled', false);
    } else {
      senderobj.closest('tr').cells[5].textContent = '0';
      tdobj.find('input[type="checkbox"]').prop('disabled', true);
    }
  }

  function updateLaunchstatus(senderobj) {
    showOverlay();
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        senderobj.closest('tr').cells[5].textContent = '1';
        clearInterval(interval);
        loadingContainer.style.display = 'none';
        hideOverlay();
        location.reload();
      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);

    var election_code = senderobj.closest('tr').cells[0].textContent.trim();
    var rowIndex      = $('table tr').index(senderobj.closest('tr')) - 1;
    var isactive      = $('#input_election_status_' + rowIndex).prop('checked');

    if (isactive) {
      var election_status = $('#' + senderobj.id).prop('checked') ? '1' : '0';
      var election_round  = $('#' + senderobj.name).val();
      axios.put('/updateLaunchstatus/' + election_code + '/' + election_round + '/' + election_status)
        .then(response => { success_var = 1; })
        .catch(error => console.error('Error:', error));
    } else {
      alert('العملية الانتخابية غير مفعلة');
    }
  }

  function finishElectionRound(senderobj) {
    showOverlay();
    let progress = 0, success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block';
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        senderobj.closest('tr').cells[5].textContent = '1';
        clearInterval(interval);
        loadingContainer.style.display = 'none';
        hideOverlay();
      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);

    if (senderobj.checked) {
      var election_code = senderobj.closest('tr').cells[0].textContent.trim();
      var rowIndex      = $('table tr').index(senderobj.closest('tr')) - 1;
      var isactive      = $('#input_election_status_' + rowIndex).prop('checked');
      if (isactive) {
        axios.put('/genearteresults/' + election_code)
          .then(response => { success_var = 1; location.reload(); })
          .catch(error => alert(error));
      } else {
        alert('العملية الانتخابية غير مفعلة');
      }
    } else {
      success_var = 1;
    }
  }

  function fetchdata(indexvar, clickvar, datevar) {
    showOverlay();
    datatable1_dataset.length = 0;
    $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
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

    if (clickvar == 1) {
      $('#allnumber').hide();
      $('#dataTable1').DataTable().column(4).visible(false);
      $('#dataTable1').DataTable().column(5).visible(false);
      $('#dataTable1').DataTable().column(6).visible(true);
      $('#dataTable1').DataTable().column(7).visible(false);
      $('#dataTable1').DataTable().column(8).visible(false);
      $('#dataTable1').DataTable().column(9).visible(false);
    } else {
      $('#allnumber').show();
      $('#dataTable1').DataTable().column(4).visible(true);
      $('#dataTable1').DataTable().column(5).visible(true);
      $('#dataTable1').DataTable().column(6).visible(false);
      $('#dataTable1').DataTable().column(7).visible(true);
      $('#dataTable1').DataTable().column(8).visible(false);
      $('#dataTable1').DataTable().column(9).visible(true);
    }
    $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
    var datetosend = datevar !== '' ? datevar.toISOString().split('T')[0] : '0';

    fetch('/getvotersbyelection/{{ $electioncode }}/' + indexvar + '/' + clickvar + '/' + datetosend)
      .then(r => r.json())
      .then(data => {
        Object.values(data[0]).forEach(function(item) {
          datatable1_dataset.push([
            item.voter_name, item.leader_name, item.mobile, item.usercode,
            item.loggedin, item.loggedout, item.votestatus,
            item.loggedin_datetime !== null ? item.loggedin_datetime : '',
            item.prf_code, ''
          ]);
        });
        $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
        var grouped = {};
        $('#dataTable1').DataTable().rows().every(function() {
          var d = this.data();
          if (!grouped[d[0]]) grouped[d[0]] = [];
          grouped[d[0]].push(d);
        });
        var rowCount = Object.keys(grouped).length;
        $('#modal_allloggedusers').text(rowCount);
        $('#modal_newloggedusers').text(datevar !== '' ? data[1] : rowCount);
        if ($('#datamodal').is(':hidden')) $('#datamodal').modal('show');
        success_var = 1;
      })
      .catch(error => console.error('Error fetching data:', error));
  }

  function showdatamodal(indexvar, clickvar) {
    var formattedDate = new Date().toISOString().split('T')[0];
    $('#loggedinfilterdate').val(formattedDate);
    fetchdata(indexvar, clickvar, datevar);
    $('#searchInput').val('');
  }

  function fetchDataAndUpdateloginChart() {
    fetch('/loggedinperc/{{ $electioncode }}')
      .then(r => r.json())
      .then(data => {
        var d = data[0];
        $('#loginspan').text(d.allprofiles[0]);
        $('#candidatesspan').text(d.candidates[0]);
        $('#votersspan').text(d.voters[1]);
        login_chart.data.datasets[0].data = d.login;
        login_chart.data.labels = [
          d.login[0] + ' : لم يسجلوا الدخول',
          d.login[1] + ' : سجلوا الدخول'
        ];
        login_chart.update();
      })
      .catch(error => console.error('Error:', error));
  }

  var login_chart = new Chart(document.getElementById('loginchart').getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: ['لم يسجلوا الدخول', 'سجلوا الدخول'],
      datasets: [{
        label: 'نسبة تسجيل الدخول',
        backgroundColor: ['rgba(255,99,132,.6)', 'rgba(54,162,235,.6)'],
        borderColor:      ['rgba(255,99,132,1)', 'rgba(54,162,235,1)'],
        borderWidth: 2
      }]
    },
    options: {
      plugins: {
        legend: {
          onClick: function(event, legendItem) {
            index = legendItem.index; click_var = 2;
            showdatamodal(index, click_var);
            event.stopPropagation();
          }
        }
      },
      onClick: function(event, elements) {
        if (elements.length) { index = elements[0].index; click_var = 2; showdatamodal(index, click_var); }
      }
    }
  });

  function fetchDataAndUpdatevoteChart() {
    fetch('/votersperc/{{ $electioncode }}')
      .then(r => r.json())
      .then(data => {
        $('#votespan').text(data[0] + data[1]);
        electionvotes_chart.data.datasets[0].data = data;
        electionvotes_chart.data.labels = [data[0] + ' : لم يصوتوا', data[1] + ' : صوتوا'];
        electionvotes_chart.update();
      })
      .catch(error => console.error('Error:', error));
  }

  var electionvotes_chart = new Chart(document.getElementById('votechart').getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: ['لم يصوتوا', 'صوتوا'],
      datasets: [{
        label: 'نسبة التصويت',
        backgroundColor: ['rgba(255,99,132,.6)', 'rgba(54,162,235,.6)'],
        borderColor:      ['rgba(255,99,132,1)', 'rgba(54,162,235,1)'],
        borderWidth: 2
      }]
    },
    options: {
      plugins: {
        legend: {
          onClick: function(event, legendItem) {
            index = legendItem.index; click_var = 1;
            showdatamodal(index, click_var);
            event.stopPropagation();
          }
        }
      },
      onClick: function(event, elements) {
        if (elements.length) { index = elements[0].index; click_var = 1; showdatamodal(index, click_var); }
      }
    }
  });

  $(document).ready(function() {
    fetchDataAndUpdatevoteChart();
    fetchDataAndUpdateloginChart();
    setInterval(fetchDataAndUpdatevoteChart, 10000);
    setInterval(fetchDataAndUpdateloginChart, 10000);
  });

  function createAlert(title, summary, details, severity, dismissible, autoDismiss, appendToId) {
    var iconMap = { info:'fa fa-info-circle', success:'fa fa-thumbs-up', warning:'fa fa-exclamation-triangle', danger:'fa fa-exclamation-circle' };
    var alertClasses = ['alert', 'animated', 'flipInX', 'alert-' + severity.toLowerCase()];
    if (dismissible) alertClasses.push('alert-dismissible');
    var msg = $('<div />', { 'class': alertClasses.join(' ') });
    if (title)   $('<h4 />',     { html: title   }).prepend($('<i />', { 'class': iconMap[severity] })).appendTo(msg);
    if (summary) $('<strong />', { html: summary }).appendTo(msg);
    if (details) $('<p />',      { html: details }).appendTo(msg);
    if (dismissible) $('<span />', { 'class':'close', 'data-dismiss':'alert', html:"<i class='fa fa-times-circle'></i>" }).appendTo(msg);
    $('#' + appendToId).prepend(msg);
    if (autoDismiss) {
      setTimeout(function() { msg.addClass('flipOutX'); setTimeout(function() { msg.remove(); }, 1000); }, 5000);
    }
  }

  function checkifanotherelectionisactive() {
    $('#dtelections tr').slice(1).each(function(index) {
      var divs = $('#round_td_' + index).find('div').filter('[id*="row_div_"]');
      divs.each(function() {
        $(this).find('input[type="checkbox"][class*="input_election_status"]').each(function(i, el) {
          if ($(el).prop('checked')) return 1;
          return 0;
        });
      });
    });
  }
</script>
@endsection
