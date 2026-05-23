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
    opacity: 0.16; animation: orbFloat 12s ease-in-out infinite; z-index: 0;
  }
  .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px;animation-delay:0s; }
  .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
  @keyframes orbFloat {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.14) translate(18px,-18px); }
  }

  /* ── Page ── */
  .el-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem;
  }

  /* ── Page header ── */
  .el-page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem; flex-wrap: wrap; gap: .75rem;
  }
  .el-page-title {
    font-size: 1.3rem; font-weight: 800; color: #1e3a70;
    display: flex; align-items: center; gap: .5rem; margin: 0;
  }
  .el-page-title i { color: #c8920a; font-size: 1.1rem; }

  /* ── Summary cards ── */
  .el-summary-row {
    display: flex; gap: 1rem; margin-bottom: 1.75rem; flex-wrap: wrap;
  }
  .el-summary-card {
    flex: 1 1 130px; background: #fff; border-radius: 1rem;
    padding: 1rem 1.25rem;
    display: flex; align-items: center; gap: .85rem;
    box-shadow:
      0 2px 8px rgba(30,58,112,0.06),
      0 0 0 1px rgba(212,168,32,0.15);
  }
  .el-summary-card .sc-icon {
    width: 44px; height: 44px; border-radius: .65rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
  }
  .sc-navy  { background: rgba(30,58,112,0.1);  color: #1e3a70; }
  .sc-green { background: #dcfce7; color: #15803d; }
  .sc-amber { background: #fef9c3; color: #a16207; }
  .el-summary-card .sc-body { line-height: 1.25; }
  .el-summary-card .sc-value { font-size: 1.35rem; font-weight: 700; color: #1e3a70; }
  .el-summary-card .sc-label { font-size: .78rem; color: #6b7280; }

  /* ── Table card ── */
  .el-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }
  .el-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .el-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1.1rem 1.75rem;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem; position: relative;
  }
  .el-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .el-header-left { display: flex; align-items: center; gap: .75rem; }
  .el-header-icon {
    width: 40px; height: 40px; border-radius: .6rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .el-header-icon i { color: #f0c94d; font-size: 1rem; }
  .el-card-header h5 { margin: 0; font-weight: 700; font-size: 1.05rem; color: #fff; }

  /* ── Search ── */
  .el-search {
    height: 40px; padding: 0 1rem 0 2.5rem; border-radius: 2rem;
    border: 1.5px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.1)
      url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23f0c94d' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    font-size: 0.88rem; color: #fff; min-width: 200px;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: 'Cairo', sans-serif;
  }
  .el-search::placeholder { color: rgba(255,255,255,0.5); }
  .el-search:focus {
    border-color: rgba(212,168,32,0.6);
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background-color: rgba(255,255,255,0.15);
    outline: none;
  }

  /* ── Table wrap ── */
  .el-table-wrap { border-top: 3px solid #d4a820; }

  #dtprofiles {
    border-collapse: separate !important; border-spacing: 0; width: 100% !important;
  }
  #dtprofiles thead tr { background: linear-gradient(135deg, #f8f4e8, #fef9e7); }
  #dtprofiles thead th {
    font-weight: 700; font-size: 0.85rem; color: #1e3a70;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    padding: 0.75rem 1rem; white-space: nowrap; letter-spacing: 0.3px;
  }
  #dtprofiles tbody tr { border-bottom: 1px solid #f0ecd8; transition: background .15s; }
  #dtprofiles tbody tr:last-child { border-bottom: none; }
  #dtprofiles tbody tr:hover { background: #fef9e7 !important; }
  #dtprofiles tbody td {
    font-size: 0.88rem; color: #1e3a70;
    padding: 0.7rem 1rem; vertical-align: middle;
  }

  /* ── Badges ── */
  .badge-type {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .28rem .7rem; border-radius: 2rem;
    font-size: .78rem; font-weight: 600;
  }
  .badge-fardi { background: rgba(30,58,112,0.1); color: #1e3a70; }
  .badge-lawah { background: #f3e8ff; color: #7c3aed; }

  .badge-count {
    display: inline-block; min-width: 26px;
    padding: .18rem .55rem; border-radius: 2rem;
    font-size: .8rem; font-weight: 600; text-align: center;
  }
  .bc-rounds     { background: #fef9c3; color: #a16207; }
  .bc-candidates { background: #dcfce7; color: #15803d; }
  .bc-voters     { background: rgba(30,58,112,0.1); color: #1e3a70; }

  /* ── Action buttons ── */
  .btn-tbl-edit {
    width: 30px; height: 30px; border-radius: .45rem;
    background: #eef4ff; border: 1.5px solid #c2d9ff;
    color: #1e3a70; font-size: .78rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .15s, border-color .15s;
  }
  .btn-tbl-edit:hover { background: #1e3a70; border-color: #1e3a70; color: #fff; }

  .btn-tbl-delete {
    width: 30px; height: 30px; border-radius: .45rem;
    background: #fff0f0; border: 1.5px solid #ffc9c9;
    color: #dc3545; font-size: .78rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .15s, border-color .15s;
  }
  .btn-tbl-delete:hover { background: #dc3545; border-color: #dc3545; color: #fff; }

  /* ── Empty state ── */
  .el-empty {
    text-align: center; padding: 3rem 1rem; color: #9ca3af;
  }
  .el-empty i { font-size: 3rem; margin-bottom: .75rem; color: rgba(212,168,32,0.3); display: block; }

  /* ── Modal ── */
  .el-modal .modal-content {
    border-radius: 1rem; border: none;
    box-shadow: 0 8px 40px rgba(10,22,40,0.2); overflow: hidden;
  }
  .el-modal .modal-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    border-bottom: none; padding: 1rem 1.25rem;
  }
  .el-modal .modal-title { font-weight: 700; font-size: 1rem; color: #fff; }
  .el-modal .modal-header .close { color: rgba(255,255,255,0.7) !important; text-shadow: none; opacity: 1; }
  .el-modal .modal-body {
    font-size: .92rem; color: #1e3a70; padding: 1.25rem 1.5rem;
    border-top: 3px solid #d4a820;
    display: flex; align-items: center; gap: .75rem;
  }
  .el-modal .modal-body i { font-size: 1.5rem; color: #c8920a; flex-shrink: 0; }
  .el-modal .modal-footer { border-top: 1px solid #e8edf6; padding: .85rem 1.25rem; gap: .5rem; }

  .btn-modal-secondary {
    height: 38px; padding: 0 1.1rem; border-radius: .55rem;
    font-size: .88rem; font-weight: 600;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    cursor: pointer; transition: background .18s, border-color .18s;
    font-family: 'Cairo', sans-serif;
  }
  .btn-modal-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.4); }

  .btn-modal-danger {
    height: 38px; padding: 0 1.1rem; border-radius: .55rem;
    font-size: .88rem; font-weight: 600;
    background: #dc3545; border: none; color: #fff;
    cursor: pointer; transition: background .18s;
    font-family: 'Cairo', sans-serif;
  }
  .btn-modal-danger:hover { background: #bb2d3b; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="el-page" dir="rtl">
<div class="container-fluid" style="max-width:1100px;">

  {{-- Page header --}}
  <div class="el-page-header">
    <h1 class="el-page-title">
      <i class="fas fa-vote-yea"></i>
      العمليات الانتخابية
    </h1>
  </div>

  {{-- Summary cards --}}
  <div class="el-summary-row">
    <div class="el-summary-card">
      <div class="sc-icon sc-navy"><i class="fas fa-layer-group"></i></div>
      <div class="sc-body">
        <div class="sc-value">{{ $Elections->count() }}</div>
        <div class="sc-label">عدد العمليات</div>
      </div>
    </div>
    <div class="el-summary-card">
      <div class="sc-icon sc-green"><i class="fas fa-users"></i></div>
      <div class="sc-body">
        <div class="sc-value">{{ array_sum($candidatesCounts) }}</div>
        <div class="sc-label">إجمالي المرشحين</div>
      </div>
    </div>
    <div class="el-summary-card">
      <div class="sc-icon sc-amber"><i class="fas fa-id-card"></i></div>
      <div class="sc-body">
        <div class="sc-value">{{ array_sum($votersCounts) }}</div>
        <div class="sc-label">إجمالي الناخبين</div>
      </div>
    </div>
  </div>

  {{-- Table card --}}
  <div class="el-card">

    <!-- Header -->
    <div class="el-card-header">
      <div class="el-header-left">
        <div class="el-header-icon"><i class="fas fa-list-alt"></i></div>
        <h5>لائحة العمليات</h5>
      </div>
      <input type="text" class="el-search" id="searchInput" placeholder="بحث..." autocomplete="off" autofocus>
    </div>

    <!-- Table -->
    <div class="el-table-wrap">
      <div class="table-responsive">
        <table id="dtprofiles" class="table table-bordered table-sm" style="width:100%">
          <thead class="text-center">
            <tr>
              <th>اسم العملية</th>
              <th>تاريخ الإطلاق</th>
              <th>النوع</th>
              <th>الجولات</th>
              <th>المرشحون</th>
              <th>الناخبون</th>
              <th style="display:none;">election_code</th>
              <th style="width:80px;"></th>
            </tr>
          </thead>
          <tbody class="text-center">
            @forelse ($Elections as $election)
            <tr>
              <td><strong>{{ $election->election_name }}</strong></td>
              <td>{{ $election->election_date }}</td>
              <td>
                @if($election->election_type == 1)
                  <span class="badge-type badge-fardi"><i class="fas fa-user fa-xs"></i> فردية</span>
                @elseif($election->election_type == 2)
                  <span class="badge-type badge-lawah"><i class="fas fa-list fa-xs"></i> لوائح</span>
                @endif
              </td>
              <td><span class="badge-count bc-rounds">{{ $electionCodesCount[$election->election_code] ?? 0 }}</span></td>
              <td><span class="badge-count bc-candidates">{{ $candidatesCounts[$election->election_code] ?? 0 }}</span></td>
              <td><span class="badge-count bc-voters">{{ $votersCounts[$election->election_code] ?? 0 }}</span></td>
              <td style="display:none;">{{ $election->election_code }}</td>
              <td class="p-1">
                <div style="display:flex;gap:.4rem;justify-content:center;">
                  <button type="button" class="btn-tbl-edit"
                    onclick="callEditForm('{{ $election->election_code }}')"
                    title="تعديل">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button type="button" class="btn-tbl-delete"
                    data-toggle="modal" data-target="#confirmDeleteModal"
                    data-eleccode="{{ $election->election_code }}"
                    title="مسح">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8">
                <div class="el-empty">
                  <i class="fas fa-inbox"></i>
                  لا توجد عمليات انتخابية
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>
</div>

{{-- Delete confirmation modal --}}
<div class="modal fade el-modal" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">
          <i class="fas fa-trash-alt ms-2" style="color:#f87171;"></i> مسح العملية الانتخابية
        </h5>
        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <i class="fas fa-exclamation-triangle"></i>
        هل أنت متأكد من رغبتك في مسح هذه العملية الانتخابية؟ لا يمكن التراجع عن هذا الإجراء.
      </div>
      <form id="modal_delete_form" action="/deleteelection" method="post">
        @csrf
        <input type="hidden" id="modal_election_code" name="modal_election_code">
        <div class="modal-footer">
          <button type="button" class="btn-modal-secondary" data-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn-modal-danger">
            <i class="fas fa-trash-alt"></i> مسح
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $('#confirmDeleteModal').on('shown.bs.modal', function (e) {
    $('#modal_election_code').val($(e.relatedTarget).data('eleccode'));
  });

  $(document).ready(function () {
    $('#searchInput').on('keyup', function () {
      var q = $(this).val().toLowerCase();
      $('#dtprofiles tbody tr').filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
      });
    });
  });

  function callEditForm(electioncode) {
    var url = '{{ route("editelectionmanager", ":electioncode") }}';
    window.location.href = url.replace(':electioncode', electioncode);
  }
</script>
@endsection
