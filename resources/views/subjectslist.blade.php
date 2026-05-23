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

  .sl-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem;
  }

  /* ── Card ── */
  .sl-card {
    background: #fff; border-radius: 1.25rem; overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }
  .sl-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .sl-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1.1rem 1.75rem;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 0.75rem; position: relative;
  }
  .sl-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .sl-header-left { display: flex; align-items: center; gap: 0.75rem; }
  .sl-header-icon {
    width: 40px; height: 40px; border-radius: 0.6rem;
    background: rgba(212,168,32,0.15); border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .sl-header-icon i { color: #f0c94d; font-size: 1rem; }
  .sl-card-header h5 { margin: 0; font-weight: 700; font-size: 1.05rem; color: #fff; }

  /* ── Search ── */
  .sl-search {
    height: 40px; padding: 0 1rem 0 2.5rem; border-radius: 2rem;
    border: 1.5px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.1)
      url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23f0c94d' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    font-size: 0.88rem; color: #fff; min-width: 200px;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: 'Cairo', sans-serif;
  }
  .sl-search::placeholder { color: rgba(255,255,255,0.5); }
  .sl-search:focus {
    border-color: rgba(212,168,32,0.6);
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background-color: rgba(255,255,255,0.15);
    outline: none;
  }

  /* ── Table wrap ── */
  .sl-table-wrap { padding: 1.25rem 1.5rem 1.5rem; border-top: 3px solid #d4a820; }

  #dtprofiles {
    border-collapse: separate !important; border-spacing: 0; width: 100% !important;
  }
  #dtprofiles thead tr { background: linear-gradient(135deg, #f8f4e8, #fef9e7); }
  #dtprofiles thead th {
    font-weight: 700; font-size: 0.85rem; color: #1e3a70;
    border: none !important;
    border-bottom: 2px solid rgba(212,168,32,0.3) !important;
    padding: 0.75rem; white-space: nowrap; letter-spacing: 0.3px;
  }
  #dtprofiles tbody tr { transition: background 0.15s; }
  #dtprofiles tbody tr:hover { background: #fef9e7 !important; }
  #dtprofiles tbody td {
    font-size: 0.88rem; color: #1e3a70;
    border-color: #f0ecd8 !important;
    padding: 0.65rem 0.75rem; vertical-align: middle;
  }

  /* Subject thumbnail */
  .subject-thumb {
    width: 36px; height: 36px; border-radius: 0.45rem; object-fit: cover;
    border: 2px solid rgba(212,168,32,0.35);
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

  /* ── Modals ── */
  .sl-modal .modal-content {
    border-radius: 1rem; border: none;
    box-shadow: 0 8px 40px rgba(10,22,40,0.2); overflow: hidden;
  }
  .sl-modal .modal-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    border-bottom: none; padding: 1rem 1.25rem;
  }
  .sl-modal .modal-title { font-weight: 700; font-size: 1rem; color: #fff; }
  .sl-modal .modal-header .close {
    color: rgba(255,255,255,0.7) !important; text-shadow: none; opacity: 1;
  }
  .sl-modal .modal-body {
    font-size: 0.92rem; color: #1e3a70; padding: 1.25rem;
    border-top: 3px solid #d4a820;
  }
  .sl-modal .modal-footer { border-top: 1px solid #e8edf6; padding: 0.85rem 1.25rem; gap: 0.5rem; }

  .btn-modal-secondary {
    height: 38px; padding: 0 1.1rem; border-radius: 0.55rem;
    font-size: 0.88rem; font-weight: 600;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    cursor: pointer; transition: background 0.18s, border-color 0.18s;
    font-family: 'Cairo', sans-serif;
  }
  .btn-modal-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.4); }

  .btn-modal-danger {
    height: 38px; padding: 0 1.1rem; border-radius: 0.55rem;
    font-size: 0.88rem; font-weight: 600;
    background: #dc3545; border: none; color: #fff;
    cursor: pointer; transition: background 0.18s;
    font-family: 'Cairo', sans-serif;
  }
  .btn-modal-danger:hover { background: #bb2d3b; }

  @keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
  .rotate { animation: spin 1s linear infinite; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="sl-page" dir="rtl">
  <div class="container-fluid" style="max-width:1100px;">

    <div class="sl-card">

      <!-- Header -->
      <div class="sl-card-header">
        <div class="sl-header-left">
          <div class="sl-header-icon"><i class="fas fa-tags"></i></div>
          <h5>لائحة المواضيع</h5>
        </div>
        <input type="text" class="sl-search" id="searchInput" placeholder="بحث..." autocomplete="off" autofocus>
      </div>

      <!-- Table -->
      <div class="sl-table-wrap">
        <table id="dtprofiles" class="table table-bordered table-sm" style="width:100%">
          <thead class="text-center">
            <tr>
              <th>عنوان الموضوع</th>
              <th>معلومات</th>
              <th>الصورة</th>
              <th style="display:none;">subject_code</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody class="text-center">
            @if($Subjects)
              @foreach ($Subjects as $subject)
              <tr>
                <td>{{ $subject->title }}</td>
                <td>{{ $subject->description }}</td>
                @if($subject->picture)
                  <td class="p-2">
                    <img class="subject-thumb"
                      src="{{ URL('profile_picture/'.$subject->picture) }}"
                      alt="{{ $subject->title }}">
                  </td>
                @else
                  <td></td>
                @endif
                <td style="display:none;">{{ $subject->subject_code }}</td>
                <td class="p-1">
                  <button type="button" class="btn-tbl-edit edit-btn"
                    onclick="callEditForm('{{ $subject->subject_code }}')">
                    <i class="fas fa-edit"></i>
                  </button>
                </td>
                <td class="p-1">
                  <button type="button" class="btn-tbl-delete"
                    data-toggle="modal" data-target="#confirmDeleteModal"
                    data-subjectcode="{{ $subject->subject_code }}">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              @endforeach
            @endif
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade sl-modal" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-trash ms-2" style="color:#f87171;"></i> مسح الموضوع</h5>
        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p style="margin:0;">هل تريد مسح هذا الموضوع؟</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" data-dismiss="modal">إلغاء</button>
        <button type="button" class="btn-modal-danger" id="confirmDeleteBtn">
          <i class="fas fa-trash"></i> مسح
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  var pendingDeleteCode = null;

  $('#confirmDeleteModal').on('shown.bs.modal', function(e) {
    pendingDeleteCode = $(e.relatedTarget).data('subjectcode');
  });

  $('#confirmDeleteBtn').on('click', function() {
    if (pendingDeleteCode) {
      // placeholder — wire to your delete route when ready
      alert('مسح الموضوع: ' + pendingDeleteCode);
      $('#confirmDeleteModal').modal('hide');
    }
  });

  function callEditForm(code) {
    // placeholder — navigate to edit route
    window.location.href = '/subjectmanager/' + code;
  }

  $(document).ready(function() {
    $('#searchInput').on('keyup', function() {
      var q = $(this).val().toLowerCase();
      $('tbody tr').each(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1);
      });
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
  });
</script>

@endsection
