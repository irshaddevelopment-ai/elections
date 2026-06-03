@extends('layouts.app')

@section('content')

<style>
  #pageMessages {
      position: fixed;
      bottom: 15px;
      right: 15px;
      width: 100%;
    }
  /* Custom CSS for DataTable header font weight */
  #dtelections thead th {
    font-weight: bold;
    font-size: 20px;
  }

  #dtelections tbody td {
    font-size: 16px;
  }

  td {
    border: 1px solid #dddddd;
    text-align: center;
    vertical-align: middle;
  }

  .pagination .page-item.active .page-link {
    background-color: #0d6efd;
  }

  div.dataTables_wrapper div.dataTables_paginate ul.pagination .page-item.active .page-link:focus {
    background-color: #0d6efd;
  }

  .pagination .page-item.active .page-link:hover {
    background-color: #0d6efd;
  }
</style>
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<div id="pageMessages">

</div>
<section dir="rtl">
  <div class="container mt-5">
    <h2 style="text-align: right;">إطلاق عملية إنتخابية</h2>
    <!-- Search Bar with Icon -->
    <div class="input-group md-form mt-3">
      <div class="input-group rounded">
        <input type="search" id="searchInput" class="form-control rounded" placeholder="بحث" aria-label="بحث" aria-describedby="search-addon" autocomplete="off">
        <button class="btn btn-primary" type="button" id="search-addon">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
    <table id="dtelections" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th style="display:none" class="text-center">election_code</th>
          <th class="text-center">اسم العملية</th>
          <th class="text-center">تاريخ الإطلاق</th>
          <th class="text-center">الحالة</th>
          <th class="text-center">الإطلاق</th>
          <th class="text-center" style="display:none;"></th>
        </tr>
      </thead>
      <tbody>
        @if ($Elections)
        @foreach ($Elections as $election)
        <?php
        $ElectionRoundsHashMap_Obj = $ElectionRoundsHashMap[$election['election_code']];
        $rowspanvar = sizeof($ElectionRoundsHashMap_Obj);
        $isChecked = $election['election_status'];
        $isstatusdisabled = false;
        if (isset($ElectionRoundsHashMap_Obj)) {
          foreach ($ElectionRoundsHashMap_Obj as $ElectionRoundsHashMap_Obj_var) {
            $roundstatus = $ElectionRoundsHashMap_Obj_var->round_status;
            if ($roundstatus != 0) {
              $isstatusdisabled = true;
              break;
            }
          }
        }
        ?>
        <tr>
          <td style="display:none">{{$election['election_code']}}</th>
          <td>{{$election['election_name']}}</td>
          <td>{{$election['election_date']}}</td>
          <td id="activate_td_{{$loop->index}}">
            <!-- Switch Toggle Button -->
            <div class="custom-control custom-switch mb-0">
              <input type="checkbox" class="custom-control-input" id="input_election_status_{{$loop->index}}" name="input_election_status" onchange="showconfirmupdatestatus(this);" @if($isChecked) checked @endif @if($isstatusdisabled) disabled @endif>
              <label class="custom-control-label" for="input_election_status_{{$loop->index}}">مفعلة</label>
            </div>
          </td>

          <td id="round_td_{{$loop->index}}">
            <?php
            $counter_var = 0;
            $oldroundstatus = 0;
            if (isset($ElectionRoundsHashMap_Obj)) {
              foreach ($ElectionRoundsHashMap_Obj as $ElectionRoundsHashMap_Obj_var) {
                $islaunched = false;
                $isfinished = false;
                $isdisabled = true;
                $round_number = $ElectionRoundsHashMap_Obj_var->round_number;
                $roundstatus = $ElectionRoundsHashMap_Obj_var->round_status;


                if (($counter_var == 0) and ($isChecked) and ($roundstatus != 2)) {
                  $isdisabled = false;
                }
                if (($roundstatus == 1) or ($oldroundstatus == 2) and ($roundstatus != 2)) {
                  $isdisabled = false;
                }

                if ($roundstatus == 1) {
                  $islaunched = true;
                }
                if ($roundstatus == 2) {
                  $isfinished = true;
                }
                $oldroundstatus = $roundstatus;
            ?>

                <!-- Switch Toggle Button -->
                <div class="custom-control custom-switch mb-0">
                  <div>
                    <input type="hidden" id="input_election_round_{{$loop->index}}_{{$counter_var}}" value="{{$ElectionRoundsHashMap_Obj_var->round_number}}" />
                    <div class="row" id="row_div_{{$loop->index}}_{{$counter_var}}">
                      <div class="col-md-6">
                        <div class="custom-control custom-switch" id="startelection">
                          <input type="checkbox" class="custom-control-input" id="input_election_round_{{$loop->index}}_{{$counter_var}}" name="input_election_round_{{$loop->index}}_{{$counter_var}}" onchange="updateLaunchstatus(this);" @if($islaunched) checked @endif @>
                          <label class="custom-control-label" for="input_election_round_{{$loop->index}}_{{$counter_var}}">الجولة {{$ElectionRoundsHashMap_Obj_var->round_number}}</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="custom-control custom-switch" id="endelection">
                          <input type="checkbox" class="custom-control-input" id="input_election_launch_{{$loop->index}}_{{$counter_var}}_2" name="input_election_launch_{{$loop->index}}_{{$counter_var}}_2" onchange="finishElectionRound(this);" @if($isfinished) checked @endif>
                          <label class="custom-control-label" for="input_election_launch_{{$loop->index}}_{{$counter_var}}_2">إنهاء</label>
                        </div>
                      </div>
                      <input type="hidden" id="round_status_{{$loop->index}}_{{$counter_var}}" value="{{$isdisabled}}" />
                    </div>
                  </div>

                </div>
            <?php $counter_var++;
              }
            }  ?>
          </td>
          <td style="display:none;">{{$election['election_status']}}</td>
        </tr>
        @endforeach
        @endif

      </tbody>
    </table>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="confirmupdatestatus" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close ml-0" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-right">
          الترميز قد فعل سابقا, هل تريد ترميز الكل من جديد؟
        </div>
        <form id="modal_delete_form" action="/deleteuser" method="post">
          @csrf
          <input type="hidden" id="modal_profile_code" name="modal_profile_code" class="form-control" autocomplete="off">
          <div class="modal-footer ">
            <button type="button" id="no_recoding" class="btn btn-secondary" data-dismiss="modal">ترميز الأسماء الجديدة</button>
            <button type="button" id="recoding" class="btn btn-primary">إعادة الترميز</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
  var sender_g_var = null;

  // Simulate loading progress
  let progressBar = document.querySelector('.progress-bar');
  let loadingContainer = document.querySelector('.loading-container');
  $(document).ready(function() {
    var table = $('#dtelections').DataTable({
      searching: false, // Disable search box
      lengthChange: false, // Disable length change feature
      language: {
        "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_",
        "paginate": {
          "next": "الصفحة القادمة",
          "previous": "الصفحة السابقة"
        },
        "emptyTable": "لا توجد معلومات"
      }
    });


    $('#searchInput').on('keyup', function() {
      var searchText = $(this).val().toLowerCase();

      $('#dtelections tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
      });
    });
    //======================


  });

  function showconfirmupdatestatus(senderobj) {

    sender_g_var = senderobj;
    var election_status = $('#' + senderobj.id).prop('checked') ? '1' : '0';

    if (election_status == 1) {
      var electionstatusvar = senderobj.closest('tr').cells[5].textContent;

      if (electionstatusvar == 1) {
        $("#confirmupdatestatus").modal('show');
      } else {

        updatestatus(senderobj, 0);
      }
    } else {
      updatestatus(senderobj, 0);
    }
  }
  $('#no_recoding').on('click', function() {
    updatestatus(sender_g_var, 0);
  });
  $('#recoding').on('click', function() {
    updatestatus(sender_g_var, 1);
  });
  //==================================
  function enabledisablelauncher() {
    
    $("#dtelections tr").slice(1).each(function(index) {
      var tdobj = $('#round_td_' + index);

      var divs = tdobj.find('div').filter('[id*="row_div_"]'); // Find the two divs within the td

      divs.each(function() {
        var checkboxes_status = $(this).find('input[type="checkbox"]').filter('[id*="input_election_status"]');
        var checkboxes_round = $(this).find('input[type="checkbox"]').filter('[id*="input_election_round"]');
        var checkboxes_launch = $(this).find('input[type="checkbox"]').filter('[id*="input_election_launch"]');
        
        var hiddenobj = $(this).find('input[type="hidden"]').filter('[id*="round_status_"]'); // Find checkboxes within each div
        var isdisabled = hiddenobj.val(); // Output the ID of each checkbox
        
       
        checkboxes_launch.each(function() {
          if (isdisabled == 1) {
            $(this).prop('disabled', true); // Disable the current checkbox
          
          } else {
            $(this).prop('disabled', false); // enable the current checkbox
          }
        });
        checkboxes_status.each(function(index) {
          if (isdisabled == 1) {
            $(this).prop('disabled', true); // Disable the current checkbox
          } else {
            $(this).prop('disabled', false); // enable the current checkbox
          }
         
          var checkbox = checkboxes_status.eq(index); // Get checkbox at current index
          var checkboxes_launchOBJ=checkboxes_launch.eq(index);
          
          var checkboxes_roundOBJ=checkboxes_round.eq(index);
          
          var isChecked = checkbox.prop('checked');
         
          if(isChecked){
            checkboxes_launchOBJ.prop('disabled', false);
            checkboxes_roundOBJ.prop('disabled', false);
          }else{
            checkboxes_launchOBJ.prop('disabled', true);
            checkboxes_roundOBJ.prop('disabled', true);
          }
        });
       
      });
    });
  }

  enabledisablelauncher();
  //============update election status
  function updatestatus(senderobj, codingvar) {

    var election_code = senderobj.closest('tr').cells[0].textContent;
    var rowIndex = $('#dtelections tr').index(senderobj.closest('tr')) - 1;
    var election_status = $('#' + senderobj.id).prop('checked') ? '1' : '0';
    var tdobj = $('#round_td_' + rowIndex);


    showOverlay();
    let progress = 0;
    let success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block'; // Show the loading container
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        senderobj.closest('tr').cells[5].textContent = "1";
        clearInterval(interval);
        loadingContainer.style.display = 'none'; // Hide the loading container when loading is complete

        hideOverlay();
        // Reload the page
        location.reload();

      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);

    axios.put('/updatestatus/' + election_code + '/' + election_status + '/' + codingvar)
      .then(response => {

       // $("#successMessage").show();
        success_var = 1;
        
        if(election_status==1){
           createAlert('', 'تم الرميز بنجاح', '', 'success', true, true, 'pageMessages');
        }
        $("#confirmupdatestatus").modal('hide');
        enabledisablelauncher();
      })
      .catch(error => {
        console.error('Error:', error); // Output any errors
        // Handle error if needed
      });

    if (election_status == 1) {
      senderobj.closest('tr').cells[5].textContent = "1";
      tdobj.find('input[type="checkbox"]').prop('disabled', false);
    } else {
      senderobj.closest('tr').cells[5].textContent = "0";

      tdobj.find('input[type="checkbox"]').prop('disabled', true);
    }
  }
  //==========================================

  //============update election status
  function updateLaunchstatus(senderobj) {
    showOverlay();
    let progress = 0;
    let success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block'; // Show the loading container
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        senderobj.closest('tr').cells[5].textContent = "1";
        clearInterval(interval);
        loadingContainer.style.display = 'none'; // Hide the loading container when loading is complete

        hideOverlay();
        // Reload the page
        location.reload();

      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);
    var election_code = senderobj.closest('tr').cells[0].textContent.trim();
    var rowIndex = $('table tr').index(senderobj.closest('tr')) - 1;
    var isactive = $('#input_election_status_' + rowIndex).prop('checked');
   
    if (isactive) {
      var election_status = $('#' + senderobj.id).prop('checked') ? '1' : '0';
      var election_round = $('#' + senderobj.name).val();
     
      axios.put('/updateLaunchstatus/' + election_code + '/' + election_round + '/' + election_status)
        .then(response => {
          success_var = 1;
          // console.log(response.data); // Output the response data
          // Handle success if needed
        })
        .catch(error => {
          console.error('Error:', error); // Output any errors
          // Handle error if needed
        });
    } else {
      alert("العملية الانتخابية غير مفعلة")
    }

  }

  function finishElectionRound(senderobj) {
    showOverlay();
    let progress = 0;
    let success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block'; // Show the loading container
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        senderobj.closest('tr').cells[5].textContent = "1";
        clearInterval(interval);
        loadingContainer.style.display = 'none'; // Hide the loading container when loading is complete

        hideOverlay();


      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);
    var isChecked = senderobj.checked;
    if (isChecked) {
      var election_code = senderobj.closest('tr').cells[0].textContent.trim();
      var rowIndex = $('table tr').index(senderobj.closest('tr')) - 1;
      var isactive = $('#input_election_status_' + rowIndex).prop('checked');
      if (isactive) {


        axios.put('/genearteresults/' + election_code)
          .then(response => {

            // Handle success if needed
            //$('#input_election_launch_' + rowIndex).prop('disabled', true);
            success_var = 1;
            // Reload the page
            location.reload();
          })
          .catch(error => {
            alert(error);
            // Handle error if needed
          });
      } else {
        alert("العملية الانتخابية غير مفعلة")
      }
    } else {
      success_var = 1;
    }
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
</script>
@endsection