@extends('layouts.app')

@section('content')

<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">

<style>
  body {
    background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%) !important;
  }
  body::after {
    content: ''; position: fixed; inset: 0;
    background-image: radial-gradient(circle, rgba(30,58,112,0.05) 1.5px, transparent 1.5px);
    background-size: 28px 28px; pointer-events: none; z-index: 0;
  }
  .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; opacity: 0.16; animation: orbFloat 12s ease-in-out infinite; z-index: 0; }
  .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px; }
  .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
  @keyframes orbFloat { 0%,100%{transform:scale(1);}50%{transform:scale(1.14) translate(18px,-18px);} }

  .gl-page { position:relative;z-index:1;min-height:calc(100vh - 72px);padding:2.5rem 1rem; }

  .gl-card { background:#fff;border-radius:1.25rem;overflow:hidden;
    box-shadow:0 4px 6px rgba(30,58,112,0.05),0 20px 50px rgba(30,58,112,0.11),0 0 0 1px rgba(212,168,32,0.2); }
  .gl-card::before { content:'';display:block;height:4px;background:linear-gradient(90deg,#c8920a,#f0c94d,#c8920a); }

  .gl-card-header {
    background:linear-gradient(160deg,#1a3268 0%,#1e4098 55%,#16305e 100%);
    padding:1.1rem 1.75rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;
  }
  .gl-header-left { display:flex;align-items:center;gap:.75rem; }
  .gl-header-icon { width:40px;height:40px;border-radius:.6rem;background:rgba(212,168,32,0.15);border:1px solid rgba(212,168,32,0.3);display:flex;align-items:center;justify-content:center; }
  .gl-header-icon i { color:#f0c94d;font-size:1rem; }
  .gl-card-header h5 { margin:0;font-weight:700;font-size:1.05rem;color:#fff; }

  .gl-select {
    height:40px;padding:0 1rem;border-radius:2rem;border:1.5px solid rgba(255,255,255,0.25);
    background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.9);font-size:.85rem;
    font-family:'Cairo',sans-serif;min-width:220px;
    transition:border-color .2s,box-shadow .2s;
  }
  .gl-select:focus { border-color:#f0c94d;outline:none; }
  .gl-select option { background:#1e3a70;color:#fff; }

  .gl-search {
    height:40px;padding:0 1rem 0 2.5rem;border-radius:2rem;border:1.5px solid #dde3ef;
    background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23c8920a' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat .75rem center;
    font-size:.88rem;color:#0f1f40;min-width:200px;transition:border-color .2s,box-shadow .2s;
  }
  .gl-search:focus { border-color:#d4a820;box-shadow:0 0 0 3px rgba(212,168,32,0.18);outline:none; }

  .gl-table-wrap { padding:1.25rem 1.5rem 1.5rem;border-top:3px solid #d4a820; }

  #dtgroups { border-collapse:separate!important;border-spacing:0;width:100%!important; }
  #dtgroups thead tr { background:linear-gradient(135deg,#f8f4e8,#fef9e7); }
  #dtgroups thead th { font-weight:700;font-size:.82rem;color:#1e3a70;border:none!important;border-bottom:2px solid rgba(212,168,32,0.3)!important;padding:.75rem;white-space:nowrap; }
  #dtgroups tbody tr { transition:background .15s; }
  #dtgroups tbody tr:hover { background:#fef9e7!important; }
  #dtgroups tbody td { font-size:.88rem;color:#1e3a70;border-color:#f0ecd8!important;padding:.6rem .75rem;vertical-align:middle; }

  .btn-tbl-edit { width:30px;height:30px;border-radius:.45rem;background:#eef4ff;border:1.5px solid #c2d9ff;color:#1e3a70;font-size:.78rem;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s,border-color .15s; }
  .btn-tbl-edit:hover { background:#1e3a70;border-color:#1e3a70;color:#fff; }
  .btn-tbl-delete { width:30px;height:30px;border-radius:.45rem;background:#fff0f0;border:1.5px solid #ffc9c9;color:#dc3545;font-size:.78rem;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s,border-color .15s; }
  .btn-tbl-delete:hover { background:#dc3545;border-color:#dc3545;color:#fff; }

  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:#1e3a70!important;color:#fff!important;border-color:#1e3a70!important;border-radius:.45rem; }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#fef9e7!important;color:#c8920a!important;border-color:rgba(212,168,32,0.3)!important;border-radius:.45rem; }
  .dataTables_wrapper .dataTables_info { font-size:.82rem;color:#64748b;padding-top:.5rem; }
  .dataTables_filter { display:none; }

  /* Modals */
  .gl-modal .modal-content { border-radius:1rem;border:none;box-shadow:0 8px 40px rgba(10,22,40,0.2);overflow:hidden; }
  .gl-modal .modal-header { background:linear-gradient(160deg,#1a3268 0%,#1e4098 55%,#16305e 100%);border-bottom:none;padding:1rem 1.25rem; }
  .gl-modal .modal-title { font-weight:700;font-size:1rem;color:#fff; }
  .gl-modal .modal-header .btn-close,.gl-modal .modal-header .close { color:rgba(255,255,255,0.7)!important;text-shadow:none;opacity:1; }
  .gl-modal .modal-body { font-size:.92rem;color:#1e3a70;padding:1.25rem;border-top:3px solid #d4a820; }
  .gl-modal .modal-footer { border-top:1px solid #e8edf6;padding:.85rem 1.25rem;gap:.5rem; }

  .btn-modal-secondary { height:38px;padding:0 1.1rem;border-radius:.55rem;font-size:.88rem;font-weight:600;background:#fff;border:1.5px solid #dde3ef;color:#1e3a70;cursor:pointer;transition:background .18s,border-color .18s; }
  .btn-modal-secondary:hover { background:#f8f4e8;border-color:rgba(212,168,32,0.4); }
  .btn-modal-primary { height:38px;padding:0 1.1rem;border-radius:.55rem;font-size:.88rem;font-weight:700;background:linear-gradient(135deg,#c8920a,#f0c94d,#c8920a);background-size:200% 200%;animation:goldShift 5s ease infinite;border:none;color:#1a2e0f;cursor:pointer;box-shadow:0 2px 10px rgba(212,168,32,0.3);transition:transform .15s,box-shadow .18s; }
  .btn-modal-primary:hover { transform:translateY(-1px);box-shadow:0 4px 14px rgba(212,168,32,0.45);color:#1a2e0f; }
  .btn-modal-danger { height:38px;padding:0 1.1rem;border-radius:.55rem;font-size:.88rem;font-weight:600;background:#dc3545;border:none;color:#fff;cursor:pointer;transition:background .18s; }
  .btn-modal-danger:hover { background:#bb2d3b; }
  @keyframes goldShift { 0%,100%{background-position:0% 50%;}50%{background-position:100% 50%;} }

  .cm-input { border:1.5px solid #dde3ef;border-radius:.65rem;font-size:.9rem;color:#0f1f40;background:#f8faff;height:42px;font-family:'Cairo',sans-serif;transition:border-color .18s,box-shadow .18s;width:100%;padding:0 .85rem; }
  .cm-input:focus { border-color:#d4a820;box-shadow:0 0 0 3px rgba(212,168,32,0.18);background:#fff;outline:none; }

  #successMessage { border-radius:.65rem;font-size:.92rem;margin:0 1rem;text-align:right;display:none; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="gl-page" dir="rtl">
  <div class="container-fluid" style="max-width:1200px;">

    <div class="alert" role="alert" id="successMessage"></div>

    <div class="gl-card">

      <div class="gl-card-header">
        <div class="gl-header-left">
          <div class="gl-header-icon"><i class="fas fa-layer-group"></i></div>
          <h5>لائحة المجموعات</h5>
        </div>
        <div class="d-flex align-items-center flex-wrap" style="gap:.75rem;">
          <select id="select_election" class="gl-select">
            @foreach($Elections as $election)
              <option value="{{ $election->election_code }}">{{ $election->election_name }}</option>
            @endforeach
          </select>
          <input type="text" class="gl-search" placeholder="بحث..." id="searchInput" autocomplete="off">
        </div>
      </div>

      <div class="gl-table-wrap">
        <table id="dtgroups" class="table table-bordered table-sm text-center" style="width:100%">
          <thead>
            <tr>
              <th>اسم المجموعة</th>
              <th>عدد الأعضاء</th>
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

{{-- Edit modal --}}
<div class="modal fade gl-modal" id="editGroupModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> تعديل المجموعة</h5>
        <button type="button" class="close ml-0" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_group_code">
        <label style="font-weight:700;font-size:.85rem;color:#1e3a70;display:block;margin-bottom:.4rem;">اسم المجموعة</label>
        <input type="text" id="edit_group_name" class="cm-input" autocomplete="off">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">إلغاء</button>
        <button type="button" class="btn-modal-primary" onclick="saveGroupEdit()"><i class="fas fa-save"></i> حفظ</button>
      </div>
    </div>
  </div>
</div>

{{-- Delete modal --}}
<div class="modal fade gl-modal" id="deleteGroupModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-trash me-2" style="color:#dc3545;"></i> حذف المجموعة</h5>
        <button type="button" class="close ml-0" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p style="margin:0;">هل تريد حذف المجموعة <strong id="delete_group_name_label"></strong>؟ سيتم إلغاء تعيين جميع أعضائها.</p>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="delete_group_code">
        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">إلغاء</button>
        <button type="button" class="btn-modal-danger" onclick="confirmDeleteGroup()"><i class="fas fa-trash"></i> حذف</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
  var table;

  $(document).ready(function() {
    table = $('#dtgroups').DataTable({
      searching: true,
      lengthChange: false,
      language: {
        sInfo: "عرض _START_ إلى _END_ من أصل _TOTAL_",
        paginate: { next: "الصفحة القادمة", previous: "الصفحة السابقة" },
        emptyTable: "لا توجد مجموعات"
      },
      columnDefs: [
        { orderable: true, targets: [0, 1] },
        { orderable: false, targets: [2, 3] },
        { width: '40px', targets: [2, 3] }
      ]
    });

    $('#searchInput').on('keyup', function() { table.search($(this).val()).draw(); });
    $('#select_election').on('change', function() { loadGroups($(this).val()); });

    var firstElection = $('#select_election').val();
    if (firstElection) loadGroups(firstElection);
  });

  function loadGroups(electioncode) {
    fetch('/getvotergroups/' + electioncode)
      .then(r => r.json())
      .then(function(data) {
        table.clear();
        data.forEach(function(g) {
          table.row.add([
            g.voter_group_name,
            g.member_count,
            '<button class="btn-tbl-edit" onclick="openEditModal(\'' + g.voter_group_code + '\', \'' + g.voter_group_name.replace(/'/g, "\\'") + '\')" title="تعديل"><i class="fas fa-edit"></i></button>',
            '<button class="btn-tbl-delete" onclick="openDeleteModal(\'' + g.voter_group_code + '\', \'' + g.voter_group_name.replace(/'/g, "\\'") + '\')" title="حذف"><i class="fas fa-trash"></i></button>'
          ]);
        });
        table.draw();
      })
      .catch(function(e) { console.error(e); });
  }

  function openEditModal(code, name) {
    $('#edit_group_code').val(code);
    $('#edit_group_name').val(name);
    $('#editGroupModal').modal('show');
    setTimeout(function() { $('#edit_group_name').focus(); }, 400);
  }

  function saveGroupEdit() {
    var code = $('#edit_group_code').val();
    var name = $('#edit_group_name').val().trim();
    if (!name) return;
    fetch('/updatevotergroup/' + code, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      body: JSON.stringify({ voter_group_name: name })
    })
    .then(r => r.json())
    .then(function() {
      $('#editGroupModal').modal('hide');
      loadGroups($('#select_election').val());
      showalert('تم التعديل بنجاح', 1, 2500);
    })
    .catch(function(e) { showalert('خطأ في التعديل', 2, 3000); });
  }

  function openDeleteModal(code, name) {
    $('#delete_group_code').val(code);
    $('#delete_group_name_label').text('"' + name + '"');
    $('#deleteGroupModal').modal('show');
  }

  function confirmDeleteGroup() {
    var code = $('#delete_group_code').val();
    fetch('/deletevotergroup/' + code, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    })
    .then(r => r.json())
    .then(function() {
      $('#deleteGroupModal').modal('hide');
      loadGroups($('#select_election').val());
      showalert('تم الحذف بنجاح', 1, 2500);
    })
    .catch(function(e) { showalert('خطأ في الحذف', 2, 3000); });
  }

  function showalert(msg, type, timeout) {
    var el = $('#successMessage');
    el.removeClass('alert-danger alert-success');
    el.addClass(type == 1 ? 'alert-success' : 'alert-danger');
    el.text(msg).show();
    setTimeout(function() { el.hide(); }, timeout);
  }
</script>

@endsection
