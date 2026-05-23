@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
  :root {
    --win-old:    #1a7a40;
    --win-old-bg: #d1f0de;
    --win-bg:     #c8f5ce;
    --win-text:   #155724;
    --next-bg:    #cce8f5;
    --next-text:  #0c4a6e;
    --lose-bg:    #fad5cf;
    --lose-text:  #7b1d13;
  }

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
  .ar-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 0 3rem;
  }

  /* ── Page header card ── */
  .ar-header-card {
    background: #fff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
    margin-bottom: 1.5rem;
  }
  .ar-header-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }
  .ar-header-card-body {
    padding: 1.1rem 1.75rem;
    border-right: 5px solid #d4a820;
  }
  .ar-header-card .ar-page-label {
    font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.2rem;
  }
  .ar-header-card .ar-election-title {
    font-size: 1.25rem; font-weight: 800; color: #1e3a70; margin: 0;
  }

  /* ── Filter card ── */
  .ar-filter-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
    margin-bottom: 1.5rem;
  }
  .ar-filter-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }
  .ar-filter-body { padding: 1.25rem 1.75rem; }
  .ar-filter-card label {
    font-weight: 700; font-size: 0.8rem;
    color: #1e3a70; margin-bottom: 0.4rem; display: block;
  }
  .ar-filter-card .form-select {
    border: 1.5px solid #dde3ef; border-radius: 0.65rem;
    font-size: 0.92rem; padding: 0.5rem 0.9rem;
    color: #0f1f40; background-color: #f8faff;
    font-family: 'Cairo', sans-serif;
    transition: border-color .2s, box-shadow .2s;
  }
  .ar-filter-card .form-select:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    outline: none;
  }

  /* ── Legend ── */
  .ar-legend-bar {
    display: flex; flex-wrap: wrap; gap: .6rem; margin-bottom: 1.25rem;
  }
  .legend-pill {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .28rem .75rem; border-radius: 2rem;
    font-size: .78rem; font-weight: 600;
  }
  .legend-pill .dot { width: 9px; height: 9px; border-radius: 50%; }

  /* ── Table card ── */
  .ar-table-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }
  .ar-table-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }
  .ar-table-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1rem 1.5rem;
    display: flex; align-items: center; gap: .6rem;
    position: relative;
  }
  .ar-table-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .ar-table-header-icon {
    width: 36px; height: 36px; border-radius: .55rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .ar-table-header-icon i { color: #f0c94d; font-size: 0.9rem; }
  .ar-table-header span { font-weight: 700; font-size: 1rem; color: #fff; }

  /* ── DataTable overrides ── */
  #dataTable1 { border-collapse: separate; border-spacing: 0; width: 100% !important; }

  #dataTable1 thead th {
    background: linear-gradient(135deg, #f8f4e8, #fef9e7);
    color: #1e3a70; font-weight: 700; font-size: .88rem;
    padding: .8rem 1rem;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    white-space: nowrap; text-align: center;
  }
  #dataTable1 tbody td {
    font-size: .88rem; padding: .72rem 1rem;
    vertical-align: middle; text-align: center;
    border-bottom: 1px solid #f0ecd8 !important;
    color: #1e3a70;
  }
  #dataTable1 tbody tr:last-child td { border-bottom: none !important; }
  #dataTable1 tbody tr:hover { background: #fef9e7 !important; }

  /* ── Row status colours ── */
  #dataTable1 tbody tr.oldroundwin td    { background: var(--win-old-bg); color: var(--win-old);   font-weight: 600; }
  #dataTable1 tbody tr.wincolor td       { background: var(--win-bg);     color: var(--win-text);  font-weight: 600; }
  #dataTable1 tbody tr.nextroundcolor td { background: var(--next-bg);    color: var(--next-text); font-weight: 500; }
  #dataTable1 tbody tr.losecolor td      { background: var(--lose-bg);    color: var(--lose-text); }

  /* ── Delete icon ── */
  .btn-delete-row {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: .45rem;
    background: #fff0f0; border: 1.5px solid #ffc9c9;
    color: #dc3545; cursor: pointer;
    transition: background .18s, border-color .18s;
  }
  .btn-delete-row:hover { background: #dc3545; color: #fff; border-color: #dc3545; }

  /* ── Pagination ── */
  .dataTables_wrapper .pagination .page-item.active .page-link,
  .dataTables_wrapper .pagination .page-item.active .page-link:focus,
  .dataTables_wrapper .pagination .page-item.active .page-link:hover {
    background-color: #1e3a70; border-color: #1e3a70;
  }
  .dataTables_wrapper .pagination .page-link { color: #1e3a70; border-radius: .45rem; }
  .dataTables_wrapper { padding: 1rem 1.25rem 1.25rem; border-top: 3px solid #d4a820; }
  .dataTables_filter { display: none; }
  div.dataTables_wrapper div.dataTables_info { color: #6c757d; font-size: .83rem; }

  /* ── Modal ── */
  .ar-modal .modal-content {
    border-radius: 1rem; border: none;
    box-shadow: 0 8px 40px rgba(10,22,40,0.2); overflow: hidden;
  }
  .ar-modal .modal-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    border-bottom: none; padding: 1rem 1.25rem;
  }
  .ar-modal .modal-title { font-weight: 700; font-size: 1rem; color: #fff; }
  .ar-modal .modal-header .btn-close { filter: invert(1); opacity: .7; }
  .ar-modal .modal-body {
    font-size: .92rem; color: #1e3a70; padding: 1.25rem 1.5rem;
    border-top: 3px solid #d4a820;
    text-align: right;
  }
  .ar-modal .modal-footer { border-top: 1px solid #e8edf6; padding: .85rem 1.25rem; }
  .ar-modal .modal-footer .btn { border-radius: .6rem; font-weight: 600; padding: .45rem 1.25rem; font-family: 'Cairo', sans-serif; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="ar-page" dir="rtl">
  <div class="container-lg">

    {{-- ── Page Header ── --}}
    <div class="ar-header-card">
      <div class="ar-header-card-body">
        <p class="ar-page-label"><i class="fas fa-chart-bar fa-sm me-1"></i> نتائج التصويت</p>
        <h1 class="ar-election-title">
          @if($electionobj)
            {{ $electionobj->election_name }}
          @else
            &mdash;
          @endif
        </h1>
      </div>
    </div>

    <?php
      $curr_round = 1;
      if (isset($curr_election_rounds->round_number)) {
        $curr_round = $curr_election_rounds->round_number;
      }
    ?>

    {{-- ── Filters ── --}}
    <div class="ar-filter-card">
      <div class="ar-filter-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label for="selectelectionround"><i class="fas fa-sync-alt fa-xs me-1"></i> الجولة</label>
            <select id="selectelectionround" class="form-select">
              @if($election_rounds)
                @foreach ($election_rounds as $election_round)
                  <option value="{{ $election_round->round_number }}"></option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-6">
            <label for="selectcandidategroup"><i class="fas fa-layer-group fa-xs me-1"></i> اللائحة</label>
            <select id="selectcandidategroup" class="form-select">
              <option value="">جميع اللوائح</option>
              @if($candidategroup)
                @foreach ($candidategroup as $candidate_group_obj)
                  <option value="{{ $candidate_group_obj->group_name }}">{{ $candidate_group_obj->group_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Legend ── --}}
    <div class="ar-legend-bar">
      <span class="legend-pill" style="background:var(--win-old-bg);color:var(--win-old);">
        <span class="dot" style="background:var(--win-old);"></span> فائز (جولة سابقة)
      </span>
      <span class="legend-pill" style="background:var(--win-bg);color:var(--win-text);">
        <span class="dot" style="background:#28a745;"></span> فائز
      </span>
      <span class="legend-pill" style="background:var(--next-bg);color:var(--next-text);">
        <span class="dot" style="background:#0ea5e9;"></span> للجولة القادمة
      </span>
      <span class="legend-pill" style="background:var(--lose-bg);color:var(--lose-text);">
        <span class="dot" style="background:#e53935;"></span> خارج
      </span>
    </div>

    {{-- ── Results Table ── --}}
    <div class="ar-table-card">
      <div class="ar-table-header">
        <div class="ar-table-header-icon"><i class="fas fa-table"></i></div>
        <span>جدول النتائج</span>
      </div>
      <div class="dataTables_wrapper">
        <table id="dataTable1" class="table table-hover" style="width:100%">
          <thead>
            <tr>
              <th>المرشح</th>
              <th>اللائحة</th>
              <th>الأصوات</th>
              <th>pass</th>
              <th>round_num</th>
              <th>prfcode</th>
              <th>win_max</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>
</div>

{{-- ── Remove Candidate Modal ── --}}
<div class="modal fade ar-modal" id="resetcandidate" tabindex="-1" aria-labelledby="removeCandidateLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="removeCandidateLabel">
          <i class="fas fa-user-minus fa-sm me-2"></i> إزالة مرشح
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex align-items-center" style="gap:.75rem;">
          <div style="width:42px;height:42px;border-radius:.5rem;background:#fff0f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-exclamation-triangle" style="color:#dc3545;font-size:1.1rem;"></i>
          </div>
          <p class="mb-0">هل تريد إزالة هذا المرشح من نتائج الجولة الحالية؟</p>
        </div>
        <input type="hidden" id="modal_profile_code" name="modal_profile_code">
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">إلغاء</button>
        <button type="button" id="btn_remove_candidate_fromresults" data-bs-dismiss="modal" class="btn btn-danger">
          <i class="fas fa-trash fa-sm me-1"></i> إزالة
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
  var cur_round_var = {{ $curr_round }};
  var datatable1_dataset = [];

  $(document).ready(function () {

    /* ── Remove candidate ── */
    $('#btn_remove_candidate_fromresults').on('click', function () {
      const postData = {
        profile_code: $('#modal_profile_code').val(),
        election_code: '{{ $electionobj->election_code ?? '' }}',
        round_number: $('#selectelectionround').val()
      };
      fetch('/resetcandidate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(postData)
      })
      .then(response => {
        if (response.ok) fetchdata($('#selectelectionround').val(), $('#selectcandidategroup').val());
      })
      .catch(error => console.error('Error:', error));
    });

    /* ── Group filter ── */
    $('#selectcandidategroup').on('change', function () {
      $('#dataTable1').DataTable().column(1).search($(this).val()).draw();
    });

    /* ── DataTable init ── */
    var table1 = $('#dataTable1').DataTable({
      data: datatable1_dataset,
      searching: true,
      lengthChange: false,
      info: false,
      dom: 'rtp',
      language: {
        paginate: { next: 'التالي', previous: 'السابق' },
        emptyTable: 'لا توجد نتائج'
      },
      rowReorder: true,
      columnDefs: [
        { className: 'dt-center', targets: '_all' },
        { targets: [3, 4, 5, 6], visible: false },
        {
          targets: 7,
          render: function (data, type) {
            if (type === 'display') {
              return '<button class="btn-delete-row" title="إزالة"><i class="fas fa-trash-alt fa-xs"></i></button>';
            }
            return data;
          }
        },
        {
          type: 'num-fmt',
          targets: [2],
          render: function (data, type) {
            if (type === 'sort') {
              var parts = data.split('/');
              return parseFloat(parts[0]) / parseFloat(parts[1]);
            }
            return data;
          }
        }
      ],
      order: [[1, 'desc'], [4, 'asc'], [2, 'desc']],
      rowCallback: function (row, data) {
        $(row).removeClass('oldroundwin wincolor nextroundcolor losecolor');
        var round = $('#selectelectionround').val();
        if (data[3] == 1 && data[4] < round)  $(row).addClass('oldroundwin');
        if (data[3] == 1 && data[4] == round)  $(row).addClass('wincolor');
        if (data[3] == 2)                       $(row).addClass('nextroundcolor');
        if (data[3] == -1)                      $(row).addClass('losecolor');
      }
    });

    /* ── Row delete click ── */
    $('#dataTable1').on('click', 'tbody td:last-child', function () {
      var rowData = $('#dataTable1').DataTable().row($(this).closest('tr')).data();
      $('#modal_profile_code').val(rowData[5]);
      $('#resetcandidate').modal('show');
    });

    /* ── Round select labels ── */
    $('#selectelectionround option').each(function () {
      $(this).text('الجولة ' + numToWordsAR_M($(this).val()));
    });
    $('#selectelectionround').val(cur_round_var);

    /* ── Round change ── */
    $('#selectelectionround').on('change', function () {
      fetchdata($(this).val(), $('#selectcandidategroup').val());
    });

    fetchdata($('#selectelectionround').val(), $('#selectcandidategroup').val());
  });

  function fetchdata(roundnumber, candidategrp) {
    datatable1_dataset.length = 0;
    @if($electionobj)
    var electioncode = '{{ $electionobj->election_code }}';
    $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
    fetch('/getelectionresults/' + electioncode + '/' + roundnumber)
      .then(r => r.json())
      .then(data => {
        data.forEach(function (c) {
          datatable1_dataset.push([
            c.full_name, c.group_name,
            c.elect_perc + '/' + c.votersTotal,
            c.reswin, c.can_round_num, c.profile_code, c.win_max, ''
          ]);
        });
        $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
        $('#dataTable1').DataTable().column(1).search(candidategrp).draw();
      })
      .catch(error => console.error('Error fetching data:', error));
    @endif
  }

  function numToWordsAR_M(num = 0) {
    if (num == 0) return 'صفر';
    let n, N, o = '', l = false,
        W = ' و', m = 'مائة',
        L = (num = '0'.repeat((num += '').length * 2 % 3) + num).length,
        S = [, 'ألف', 'مليون', 'مليار', 'ترليون', 'كوادرليون'],
        T = ['', 'الأولى', 'الثانية', 'الثالثة', 'الرابعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة'];
    for (let D = L; D > 0; D -= 3) {
      n = +num.substring(L - D, L - D + 3);
      l = !+num.substring(L - D + 3);
      n && (o += $(D / 3 - 1), l || (o += '' + W));
    }
    return o;

    function $(P) {
      let s = S[P], h = ~~(n / 100), u = (N = n % 100) % 10,
          t = ~~(N / 10), H = '', wN = '';
      if (h) {
        if (h > 2) H = T[h].slice(0, (h == 8 ? -2 : -1)) + m;
        else if (h == 1) H = m;
        else H = m.slice(0, -1) + (s && !N ? 'تا' : 'تان');
      }
      if (N > 19) wN = T[u] + (u ? W : '') + (t == 2 ? 'عشر' : T[t].slice(0, (t == 8 ? -2 : -1))) + 'ون';
      else if (N > 10) wN = (u == 1 ? 'أحد' : (u == 2 ? 'اثنا' : T[u])) + ' عشر';
      else wN = T[N];
      let w = H + (h && N ? W : '') + wN;
      if (!s) return w;
      if (N > 2) return w + ' ' + (N > 10 ? s + 'ًا' : (P < 3 ? [, 'آلاف', 'ملايين'][P] : S[P] + 'ات'));
      if (!N) return w + ' ' + s;
      w = (h ? H + W : '') + s;
      return (N == 1) ? w : w + 'ان';
    }
  }
</script>
@endsection
