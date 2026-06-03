<style>
  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  .refresh-animation { animation: spin 1s linear infinite; }
  .status-dot {
    display: inline-block;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    vertical-align: middle;
  }
  .status-dot-yes { background: #28a745; }
  .status-dot-no  { background: #dc3545; }

  .btn-refresh-row {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 50%;
    background: #eef4ff; border: 1.5px solid #dce8ff;
    color: #2c4a7c; cursor: pointer; font-size: .78rem;
    transition: background .18s, color .18s;
  }
  .btn-refresh-row:hover { background: #0d6efd; color: #fff; border-color: #0d6efd; }
  .btn-refresh-card {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 50%;
    background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.3);
    color: #fff; cursor: pointer; font-size: .8rem;
    transition: background .18s, border-color .18s;
  }
  .btn-refresh-card:hover { background: rgba(255,255,255,.28); border-color: rgba(255,255,255,.5); }

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
  <div class="dash-card-header d-flex align-items-center justify-content-between">
    <span><i class="fas fa-id-badge fa-sm"></i> حالة الناخبين</span>
    <button type="button" class="btn-refresh-card" id="refreshCandidatesStatusBtn" title="تحديث حالة الناخبين">
      <i class="fas fa-sync-alt fa-xs"></i>
    </button>
  </div>
  <div class="table-responsive">
    <table id="dataTable_leader_1" class="table table-hover mb-0" style="width:100%">
      <thead>
        <tr>
          <th>الناخب</th>
          <th>الرمز</th>
          <th>المرشد</th>
          <th>رمز المرشد</th>
          <th>تسجيل الدخول</th>
          <th>التصويت</th>
          <th>الحالة</th>
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
        { orderable: true, className: 'reorder', targets: [0,1,2,3,4,5,6] },
        { targets: [8], visible: false },
        { targets: [7], visible: true },
        { orderable: false, targets: '_all' },
        {
          targets: [4,5,6],
          render: function(data, type) {
            if (type !== 'display') return data;
            return data == 'نعم'
              ? '<span class="status-dot status-dot-yes"></span>'
              : '<span class="status-dot status-dot-no"></span>';
          }
        },
        {
          targets: 7,
          render: function(data, type) {
            if (type === 'display') return '<button class="btn-refresh-row" title="إعادة تعيين الرمز"><i class="fas fa-sync-alt fa-xs"></i></button>';
            return data;
          }
        }
      ]
    });

    fetchdataleader(formattedDateTime);

    $('#refreshCandidatesStatusBtn').on('click', function() {
      var btn = $(this);
      btn.addClass('refresh-animation');
      fetchdataleader(formattedDateTime).finally(function() {
        btn.removeClass('refresh-animation');
      });
    });

    $('#dataTable_leader_1').on('click', 'tbody td:last-child', function() {
      var btn = $(this).find('.btn-refresh-row');
      btn.addClass('refresh-animation');
      setTimeout(function() { btn.removeClass('refresh-animation'); }, 1000);

      var rowIndexleader = $('#dataTable_leader_1').DataTable().row($(this).closest('tr')).index();
      var rowDataleader  = $('#dataTable_leader_1').DataTable().row(rowIndexleader).data();
      resetusercodeLeader(rowDataleader[8], rowIndexleader);
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

    return fetch('/getcandidatesstatus/' + electioncode)
      .then(r => r.json())
      .then(data => {
        data.forEach(function(voter) {
          datatableleader_1_dataset.push([
            voter.voter_name, voter.usercode, voter.leader_name, voter.leader_usercode,
            voter.loggedin, voter.votestatus, voter.isconnected == 1 ? 'نعم' : 'كلا', '', voter.profile_code
          ]);
        });
        $('#dataTable_leader_1').DataTable().clear().rows.add(datatableleader_1_dataset).draw();
      })
      .catch(error => console.error('Error fetching data:', error));
  }
</script>
