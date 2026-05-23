<style>
  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  .refresh-animation { animation: spin 1s linear infinite; }
  .status-icon-yes { color: #28a745; font-size: .95rem; }
  .status-icon-no  { color: #dc3545; font-size: .95rem; }

  .btn-refresh-row {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 50%;
    background: #eef4ff; border: 1.5px solid #dce8ff;
    color: #2c4a7c; cursor: pointer; font-size: .78rem;
    transition: background .18s, color .18s;
  }
  .btn-refresh-row:hover { background: #0d6efd; color: #fff; border-color: #0d6efd; }

  #dataTable_leader_1 thead th {
    background: #f0f5ff; color: #1a3a6b; font-weight: 700;
    font-size: .88rem; padding: .8rem 1rem;
    border-bottom: 2px solid #dce8ff; text-align: center;
  }
  #dataTable_leader_1 tbody td {
    font-size: .85rem; padding: .7rem 1rem;
    vertical-align: middle; text-align: center;
    border-bottom: 1px solid #f0f3f9; color: #2c3e50;
  }
  #dataTable_leader_1 tbody tr:last-child td { border-bottom: none; }
  #dataTable_leader_1 tbody tr:hover { background: #f5f8ff; }
</style>

<div class="dash-card" dir="rtl">
  <div class="dash-card-header">
    <i class="fas fa-id-badge fa-sm"></i> حالة الناخبين
  </div>
  <div class="table-responsive">
    <table id="dataTable_leader_1" class="table table-hover mb-0" style="width:100%">
      <thead>
        <tr>
          <th>المرشح</th>
          <th>الرمز</th>
          <th>المرشد</th>
          <th>تسجيل الدخول</th>
          <th>التصويت</th>
          <th></th>
          <th>prf_code</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
  var datatableleader_1_dataset = [];

  $(document).ready(function() {
    var formattedDateTime = new Date().toISOString().slice(0, 10);

    var tableleader_1 = $('#dataTable_leader_1').DataTable({
      data: datatableleader_1_dataset,
      searching: true,
      lengthChange: false,
      info: false,
      dom: 'rtp',
      language: {
        paginate: { next: 'التالي', previous: 'السابق' },
        emptyTable: 'لا توجد معلومات'
      },
      rowReorder: true,
      columnDefs: [
        { className: 'dt-center', targets: '_all' },
        { orderable: true, className: 'reorder', targets: [0,1,2,3,4] },
        { targets: [6], visible: false },
        { targets: [5], visible: true },
        { orderable: false, targets: '_all' },
        {
          targets: [3,4],
          render: function(data, type) {
            if (type !== 'display') return data;
            return data == 'نعم'
              ? '<i class="fas fa-check-circle status-icon-yes"></i>'
              : '<i class="fas fa-times-circle status-icon-no"></i>';
          }
        },
        {
          targets: 5,
          render: function(data, type) {
            if (type === 'display') return '<button class="btn-refresh-row" title="إعادة تعيين الرمز"><i class="fas fa-sync-alt fa-xs"></i></button>';
            return data;
          }
        }
      ]
    });

    fetchdataleader(formattedDateTime);

    $('#dataTable_leader_1').on('click', 'tbody td:last-child', function() {
      var btn = $(this).find('.btn-refresh-row');
      btn.addClass('refresh-animation');
      setTimeout(function() { btn.removeClass('refresh-animation'); }, 1000);

      var rowIndexleader = $('#dataTable_leader_1').DataTable().row($(this).closest('tr')).index();
      var rowDataleader  = $('#dataTable_leader_1').DataTable().row(rowIndexleader).data();
      resetusercodeLeader(rowDataleader[6], rowIndexleader);
    });
  });

  function resetusercodeLeader(prfcode, rowIndex) {
    axios.put('/resetusercode/' + prfcode)
      .then(response => {
        createAlert('', 'تم تفعيل الرمز', '', 'success', true, true, 'pageMessages');
      })
      .catch(error => alert(error));
  }

  function fetchdataleader(senderobj) {
    datatableleader_1_dataset.length = 0;
    var electioncode = '{{ $electioncode }}';
    $('#dataTable_leader_1').DataTable().clear().rows.add(datatableleader_1_dataset).draw();

    fetch('/getcandidatesstatus/' + electioncode)
      .then(r => r.json())
      .then(data => {
        data.forEach(function(candidate) {
          datatableleader_1_dataset.push([
            candidate.candidate_name, candidate.usercode, candidate.leader_name,
            candidate.loggedin, candidate.votestatus, '', candidate.profile_code
          ]);
        });
        $('#dataTable_leader_1').DataTable().clear().rows.add(datatableleader_1_dataset).draw();
      })
      .catch(error => console.error('Error fetching data:', error));
  }
</script>
