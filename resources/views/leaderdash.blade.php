<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>{{ config('app.name', 'Laravel') }}</title>
      <!-- Bootstrap CSS CDN -->
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
      <!-- Font Awesome CSS CDN -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <!-- MDBootstrap CSS CDN -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/css/mdb.min.css">
      <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
      <!-- MDBootstrap DataTables CSS CDN -->
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <!-- jQuery CDN -->
      <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
   </head>
   <body>
      <style>
         body {
         background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%) !important;
         }
         .leader-navbar {
         background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
         padding: 0.75rem 1.5rem;
         display: flex;
         align-items: center;
         justify-content: space-between;
         box-shadow: 0 4px 20px rgba(10,22,60,0.25);
         position: relative;
         z-index: 10;
         direction: rtl;
         }
         .leader-navbar::after {
         content: '';
         position: absolute;
         bottom: 0;
         left: 0;
         right: 0;
         height: 3px;
         background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
         }
         .leader-navbar-left {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         flex-wrap: wrap;
         }
         .leader-nav-brand {
         display: flex;
         align-items: center;
         gap: 0.65rem;
         text-decoration: none;
         }
         .leader-nav-brand:hover {
         text-decoration: none;
         }
         .leader-nav-brand img {
         width: 36px;
         height: 36px;
         border-radius: 50%;
         border: 2px solid rgba(212,168,32,0.5);
         object-fit: cover;
         box-shadow: 0 2px 8px rgba(0,0,0,0.25);
         }
         .leader-nav-brand span {
         font-weight: 800;
         font-size: 1rem;
         color: #fff;
         }
         .leader-nav-link {
         font-size: 0.88rem;
         font-weight: 600;
         color: #f0c94d;
         text-decoration: none;
         display: flex;
         align-items: center;
         gap: 0.35rem;
         border: 1px solid rgba(240,201,77,0.3);
         border-radius: 2rem;
         padding: 0.3rem 0.85rem;
         transition: background 0.18s, color 0.18s;
         }
         .leader-nav-link:hover {
         background: rgba(240,201,77,0.12);
         color: #f0c94d;
         text-decoration: none;
         }
         .leader-nav-logout {
         font-size: 0.88rem;
         font-weight: 600;
         color: rgba(255,255,255,0.82);
         text-decoration: none;
         display: flex;
         align-items: center;
         gap: 0.35rem;
         border: 1px solid rgba(255,255,255,0.2);
         border-radius: 2rem;
         padding: 0.3rem 0.85rem;
         transition: background 0.18s, color 0.18s;
         }
         .leader-nav-logout:hover {
         background: rgba(255,255,255,0.1);
         color: #fff;
         text-decoration: none;
         }
         body::after {
         content: '';
         position: fixed;
         inset: 0;
         background-image: radial-gradient(circle, rgba(30,58,112,0.05) 1.5px, transparent 1.5px);
         background-size: 28px 28px;
         pointer-events: none;
         z-index: 0;
         }
         .leader-page {
         position: relative;
         z-index: 1;
         min-height: calc(100vh - 56px);
         padding: 2.5rem 1rem;
         }
         .leader-card {
         background: #fff;
         border-radius: 1.25rem;
         overflow: hidden;
         box-shadow: 0 4px 6px rgba(30,58,112,0.05), 0 20px 50px rgba(30,58,112,0.11), 0 0 0 1px rgba(212,168,32,0.2);
         }
         .leader-card::before {
         content: '';
         display: block;
         height: 4px;
         background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
         }
         .leader-card-header {
         background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
         border-bottom: none;
         padding: 1.1rem 1.75rem;
         display: flex;
         align-items: center;
         justify-content: space-between;
         flex-wrap: wrap;
         gap: 0.75rem;
         }
         .leader-card-title {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         color: #fff;
         }
         .leader-header-icon {
         width: 40px;
         height: 40px;
         border-radius: 0.6rem;
         background: rgba(212,168,32,0.15);
         border: 1px solid rgba(212,168,32,0.3);
         display: flex;
         align-items: center;
         justify-content: center;
         color: #f0c94d;
         }
         .leader-card-title h5,
         .leader-card-title p {
         margin: 0;
         }
         .leader-card-title h5 {
         font-weight: 700;
         font-size: 1.05rem;
         }
         .leader-card-title p {
         color: rgba(255,255,255,0.74);
         font-size: 0.85rem;
         margin-top: 0.15rem;
         }
         .leader-toolbar {
         display: flex;
         align-items: center;
         gap: 0.65rem;
         flex-wrap: wrap;
         }
         .leader-date {
         height: 40px;
         border-radius: 2rem;
         border: 1.5px solid #dde3ef;
         padding: 0 1rem;
         font-size: 0.88rem;
         color: #0f1f40;
         min-width: 170px;
         }
         .leader-date:focus {
         border-color: #d4a820;
         box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
         outline: none;
         }
         .btn-leader-refresh {
         width: 40px;
         height: 40px;
         border-radius: 0.65rem;
         border: none;
         background: linear-gradient(135deg, #c8920a, #f0c94d, #c8920a);
         color: #1a2e0f;
         box-shadow: 0 3px 12px rgba(212,168,32,0.35);
         display: inline-flex;
         align-items: center;
         justify-content: center;
         cursor: pointer;
         }
         .leader-table-wrap {
         padding: 1.25rem 1.5rem 1.5rem;
         border-top: 3px solid #d4a820;
         }
         #dataTable1 {
         border-collapse: separate !important;
         border-spacing: 0;
         width: 100% !important;
         }
         #dataTable1 thead tr {
         background: linear-gradient(135deg, #f8f4e8, #fef9e7);
         }
         #dataTable1 thead th {
         font-weight: 700;
         font-size: 0.82rem;
         color: #1e3a70;
         border: none !important;
         border-bottom: 2px solid rgba(212,168,32,0.3) !important;
         padding: 0.75rem;
         white-space: nowrap;
         }
         #dataTable1 tbody tr {
         transition: background 0.15s;
         }
         #dataTable1 tbody tr:hover {
         background: #fef9e7 !important;
         }
         #dataTable1 tbody td {
         font-size: 0.88rem;
         color: #1e3a70;
         border-color: #f0ecd8 !important;
         padding: 0.6rem 0.75rem;
         vertical-align: middle;
         }
         .status-icon-yes {
         color: #198754;
         font-size: 0.95rem;
         }
         .status-icon-no {
         color: #dc3545;
         font-size: 0.95rem;
         }
         .btn-refresh-row {
         width: 30px;
         height: 30px;
         border-radius: 0.45rem;
         background: #f0fff4;
         border: 1.5px solid #b2f0c8;
         color: #198754;
         font-size: 0.78rem;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         cursor: pointer;
         transition: background 0.15s, border-color 0.15s, color 0.15s;
         }
         .btn-refresh-row:hover {
         background: #198754;
         border-color: #198754;
         color: #fff;
         }
         #pageMessages {
         position: fixed;
         bottom: 15px;
         right: 15px;
         width: min(420px, calc(100% - 30px));
         z-index: 1055;
         }
         .dataTables_filter {
         display: none;
         }
         .dataTables_wrapper .dataTables_paginate .paginate_button.current,
         .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
         background: #1e3a70 !important;
         color: #fff !important;
         border-color: #1e3a70 !important;
         border-radius: 0.45rem;
         }
         @keyframes spin {
         0% { transform: rotate(0deg); }
         100% { transform: rotate(360deg); }
         }
         .refresh-animation {
         animation: spin 1s linear infinite;
         }
         @media (max-width: 575.98px) {
         .leader-navbar {
         align-items: stretch;
         flex-direction: column;
         gap: 0.75rem;
         }
         .leader-navbar-left,
         .leader-nav-logout {
         justify-content: center;
         }
         }
      </style>
      <!-- Navbar -->
      <?php
     $sett_id_var=false;
     if($setting==null){
      $sett_id_var=true;
     }
      
     if(isset($setting->settings_value)){
      
       if($setting->settings_value=='1'){
        $sett_id_var=true;
       }
      }
     ?>
      <nav class="leader-navbar">
         <div class="leader-navbar-left">
            <a class="leader-nav-brand" href="{{ route('guesthome') }}">
               @if($users)
                  @if($users->picture)
                     <img src="{{ URL('../profile_picture/' . $users->picture) }}" alt="صورة">
                  @else
                     <img src="{{ URL('images/logo.webp') }}" alt="شعار">
                  @endif
                  <span>{{ $users->full_name }}</span>
               @endif
            </a>
            @if($isleader)
               <a class="leader-nav-link" href="{{ route('leaderdash') }}">
                  <i class="fas fa-users"></i> أفراد المجموعة
               </a>
            @endif
         </div>
         <a class="leader-nav-logout" href="{{ route('logout', ['profile_code' => $users->profile_code])}}">
            <i class="fas fa-sign-out-alt"></i> خروج
         </a>
      </nav>
      <div class="loading-container" style="display:none;">
         <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
         </div>
      </div>
      <div id="pageMessages">

  </div>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha3/0.8.0/sha3.min.js"></script>
      <!-- Bootstrap JS and Popper.js CDN -->
      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <!-- Font Awesome JS CDN -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
      <!-- MDBootstrap JS CDN -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/js/mdb.min.js"></script>
   <section dir="rtl">
      <div class="leader-page">
         <div class="container-fluid" style="max-width: 1200px;">
            <div class="leader-card">
               <div class="leader-card-header">
                  <div class="leader-card-title">
                     <div class="leader-header-icon"><i class="fas fa-users"></i></div>
                     <div>
                        <h5>أفراد المجموعة</h5>
                        <p>@if($electionobj) العملية الإنتخابية : {{$electionobj->election_name}} @endif</p>
                     </div>
                  </div>
                  <div class="leader-toolbar">
                     <input type="date" class="leader-date" id="input_filter_date" name="input_filter_date" onchange="fetchdata(this.value)"/>
                     <button class="btn-leader-refresh" type="button" id="btnrefreshleaderdash" title="تحديث">
                        <i class="fas fa-refresh"></i>
                     </button>
                  </div>
               </div>
               <div class="leader-table-wrap table-responsive">
                  <table id="dataTable1" class="table table-bordered table-sm" style="width:100%">
                     <thead class="text-center">
                        <tr>
                           <th class="text-center">الناخب</th>
                           <th class="text-center">رقم الهاتف</th>
                           <th class="text-center">الرمز</th>
                           <th class="text-center">تسجيل الدخول</th>
                           <th class="text-center">التصويت</th>
                           <th class="text-center"></th>
                           <th class="text-center">الحالة</th>
                           <th class="text-center"></th>
                           <th class="text-center">prf_code</th>
                        </tr>
                     </thead>
                     <tbody class="text-center"></tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </section>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
   <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
   <script>
          var sett_id_var = @if(empty($sett_id_var)) false @else {!! json_encode($sett_id_var) !!} @endif;
      
      var datatable1_dataset = [];
      
       $(document).ready(function() {
             // Get the current date
             var currentDate = new Date();
             
      
             // Format the date and time as "YYYY-MM-DDTHH:MM" (required by the datetime-local input)
             var formattedDateTime = currentDate.toISOString().slice(0, 10);
         
             // Set the default date and time for the input
             $('#input_filter_date').val(formattedDateTime);
      
      
             var table1 = $('#dataTable1').DataTable({
                 data: datatable1_dataset,
                 searching: true, // Disable search box
                 lengthChange: false, // Disable length change feature
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
          }, {
            orderable: true,
            className: 'reorder',
            targets: [0, 1, 2, 3, 4]
          }, {
                    "targets": [5,8],
                    "visible": false

                }, {
                    "targets": [7],
                    "visible": sett_id_var

                },
          {
            orderable: false,
            targets: '_all'
          }, {
            targets: 6,
            render: function(data, type, row, meta) {
              
              if (row[5]=='1') {
                return '<i class="fas fa-check-circle status-icon-yes"></i>';
              }else{
               return '<i class="fas fa-times-circle status-icon-no"></i>';
              }
              return data;
            }
          }, {
            targets: 7,
            render: function(data, type, row, meta) {
              
              if (type === 'display') {
                return '<button class="btn-refresh-row" title="إعادة تفعيل الرمز"><i class="fa fa-refresh" aria-hidden="true"></i></button>';
              }
              return data;
            }
          }
                 ]
             });
      
             fetchdata($('#input_filter_date').val());
             //setInterval(fetchdata($('#input_filter_date').val()), 10000); // 5000 milliseconds = 5 seconds
      
             $('#btnrefreshleaderdash').click(function() {
               fetchdata($('#input_filter_date').val());
             });
            });


            $('#dataTable1').on('click', '.btn-refresh-row', function() {
        // Get the row index
        startAnimation(this);
        var rowIndex = $('#dataTable1').DataTable().row($(this).closest('tr')).index();
       
        // Get the data for the clicked row
        var rowData = $('#dataTable1').DataTable().row(rowIndex).data();
        
        // Log or do something with the row data
        // Example: Open a modal with row details
        var prf_code = rowData[8];

        resetusercode(prf_code,rowIndex,this);
      });
      
       function fetchdata(senderobj){
         
           
        
             datatable1_dataset.length = 0;
             datevar=senderobj;
             
             electioncode='{{$electionobj->election_code}}';
             $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
          
           fetch('/getvotersforleaderinfo/' + datevar+'/'+electioncode )
             .then(response => response.json()) // Parse the JSON response
             .then(data => {
               var voters_arr = data;
               
               voters_arr.forEach(function(voter, index) {
               
                   var new_row = [voter['voter_name'], voter['mobile'], voter['usercode'],
                   voter['loggedin'], voter['votestatus'], voter['isconnected'], '', '', voter['profile_code']];
                 
                   datatable1_dataset.push(new_row);
                 });
                 
                 $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
             })
             .catch(error => {
               console.error('Error fetching data:', error);
             });
       }

       function resetusercode(prfcode,rowIndex,button) {
      axios.put('/resetusercode/' + prfcode)
        .then(response => {
          createAlert('', 'تم تفعيل الرمز', '', 'success', true, true, 'pageMessages');
          stopAnimation(button);
          // Find the row where column 8 (index 7) has the value 'prfcode'
         
        })
        .catch(error => {
          alert(error); // Output any errors
          // Handle error if needed
        });
    }
    function createAlert(title, summary, details, severity, dismissible, autoDismiss, appendToId) {

var iconMap = {
  info: "fa fa-info-circle",
  success: "fa fa-thumbs-up",
  warning: "fa fa-exclamation-triangle",
  danger: "fa ffa fa-exclamation-circle"
};

var iconAdded = false;

var alertClasses = ["alert", "animated", "flipInX"];

alertClasses.push("alert-" + severity.toLowerCase());


if (dismissible) {
  alertClasses.push("alert-dismissible");
}

var msgIcon = $("<i />", {
  "class": iconMap[severity] // you need to quote "class" since it's a reserved keyword
});

var msg = $("<div />", {
  "class": alertClasses.join(" ") // you need to quote "class" since it's a reserved keyword
});

if (title) {
  var msgTitle = $("<h4 />", {
    html: title
  }).appendTo(msg);

  if (!iconAdded) {
    msgTitle.prepend(msgIcon);
    iconAdded = true;
  }
}

if (summary) {
  var msgSummary = $("<strong />", {
    html: summary
  }).appendTo(msg);

  if (!iconAdded) {
    msgSummary.prepend(msgIcon);
    iconAdded = true;
  }
}

if (details) {
  var msgDetails = $("<p />", {
    html: details
  }).appendTo(msg);

  if (!iconAdded) {
    msgDetails.prepend(msgIcon);
    iconAdded = true;
  }
}


if (dismissible) {
  var msgClose = $("<span />", {
    "class": "close", // you need to quote "class" since it's a reserved keyword
    "data-dismiss": "alert",
    html: "<i class='fa fa-times-circle'></i>"
  }).appendTo(msg);
}

$('#' + appendToId).prepend(msg);

if (autoDismiss) {
  setTimeout(function() {
    msg.addClass("flipOutX");
    setTimeout(function() {
      msg.remove();
    }, 1000);
  }, 5000);
}
}
function startAnimation(icon) {
    // Add animation class to the clicked icon
    $(icon).addClass('refresh-animation');

    // Remove animation class after a delay
    setTimeout(function() {
      $(icon).removeClass('refresh-animation');
    }, 1000); // Adjust the delay as needed
  }
  function stopAnimation(icon) {
  // Remove animation class
  $(icon).removeClass('refresh-animation');
}
   </script>
   </body>
</html>
