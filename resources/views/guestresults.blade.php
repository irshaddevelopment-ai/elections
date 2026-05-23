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
      <!-- MDBootstrap DataTables CSS CDN -->
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <!-- jQuery CDN -->
      <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
   </head>
   <body>
      <style>
          /* Customize the back arrow color */
    .back-arrow {
      color: #6c757d; /* Gray color */
    }
    /* Additional styles */
    .arrow-container {
      
      padding-top: 15px; /* Add some padding for spacing */
    }
          #dataTable1 tbody tr.oldroundwin {
      background-color: #3CB371;
 }
         #dataTable1 tbody tr.wincolor {
      background-color: #93ed9a;
 }
 #dataTable1 tbody tr.nextroundcolor {
      background-color: #a1dded;
 }
 #dataTable1 tbody tr.losecolor {
      background-color: #ed9385;
 }
         .navbar-nav {
         font-size: 18px;
         /* Adjust the font size as needed */
         }
         .navbar-brand img {
         border-radius: 50%;
         }
         /* Custom CSS for DataTable header font weight */
         #dataTable1 thead th {
         font-weight: bold;
         font-size: 20px;
         }
         #dataTable1 tbody td {
         font-size: 14px;
         }
         .tooltip-inner {
         max-width: 350px;
         /* the minimum width */
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
         .dataTables_filter {
         display: none;
         }
      </style>
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
         <a class="navbar-brand" href="{{ route('guesthome') }}">
        
         @if($users)
         @if($users->picture)
      <img src="{{ URL('../profile_picture/' . $users->picture) }}" alt="Profile Picture" width="30" height="30" class="d-inline-block align-top">
      @else
      <img src="{{ URL('images/logo.webp') }}" alt="Profile Picture" width="30" height="30" class="d-inline-block align-top">
      @endif
         {{$users->full_name}}
         @endif
         </a>
        
         <!-- Toggle button for mobile -->
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
         </button>
         <!-- Navbar links -->
         <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Logout Icon -->
            <ul class="navbar-nav ml-auto">
               <li class="nav-item">
                  <a class="nav-link" href="{{ route('logout', ['profile_code' => $users->profile_code])}}">
                  <i class="fas fa-sign-out-alt"></i> خروج
                  </a>
               </li>
            </ul>
         </div>
      </nav>
       <!-- Back arrow -->
  <div class="container arrow-container">
  <a href="{{ route('guesthome') }}">
    <i class="fas fa-arrow-left fa-2x back-arrow"></i>
  </a>
</div>
      <div class="loading-container" style="display:none;">
         <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
         </div>
      </div>
      <main class="py-4">
         @yield('content')
      </main>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha3/0.8.0/sha3.min.js"></script>
      <!-- Bootstrap JS and Popper.js CDN -->
      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <!-- Font Awesome JS CDN -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
      <!-- MDBootstrap JS CDN -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/js/mdb.min.js"></script>
   </body>
   <section dir="rtl">
      <!-- Table with MDBootstrap DataTables -->
      <div class="container mt-5 ">
      <div class="container mt-4" style="text-align: center;">
         <span class="custom-label">
            <h4 class="text-xl">@if($electionobj)
               العملية الإنتخابية :
               {{$electionobj->election_name}}
               @endif
            </h4>
            <?php
             $curr_round=1;
             if($curr_election_rounds->round_number){
              $curr_round=$curr_election_rounds->round_number;
             }
           ?>
            </h5>
         </span>
      </div>
      <div class="dropdown-divider"></div>
      <div class="container mt-5">
      <div class="row">
         <div class="form-group col-md-6" style="text-align: right;">
            <!-- Searchable Select Input -->

                      
                        <select name="Select Dropdown" id="selectelectionround" class="js-example-basic-single form-control">
                            @if($election_rounds)
                            @foreach ($election_rounds as $election_round)
                            <option value="{{ $election_round->round_number }}"></option>
                            @endforeach
                            @endif

                        </select>

         </div>
         <div class="form-group col-md-6" style="text-align: right;">
            <!-- Searchable Select Input -->

                      
                        <select name="Select Dropdown" id="selectcandidategroup" class="js-example-basic-single form-control">
                        <option value="">جميع اللوائح</option>    
                        @if($candidategroup)
                            @foreach ($candidategroup as $candidate_group_obj)
                            <option value="{{ $candidate_group_obj->group_name }}">
                            {{ $candidate_group_obj->group_name }}
                            </option>
                            @endforeach
                            @endif

                        </select>

         </div>
        </div>
         <div class="datatable" data-mdb-datatable-init data-mdb-striped="true" data-mdb-bordered="true" data-mdb-full-pagination="true" data-mdb-selectable="true" data-mdb-sm="true">
            <table id="dataTable1" class="table  table-bordered table-sm" style="width:100%">
               <thead class="text-center">
                  <tr>
                     <th class="text-center">المرشح</th>
                     <th class="text-center">اللائحة</th>
                     <th class="text-center">العدد</th>
                     <th class="text-center">pass</th>
                     <th class="text-center">round_num</th>
                     <th class="text-center">prfcode</th>
                     <th class="text-center">win_max</th>
                  </tr>
               </thead>
               <tbody class="text-center">
               </tbody>
            </table>
         </div>
      </div>
   </section>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
   <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
   <script>
       var cur_round_var={{$curr_round}};
      var datatable1_dataset = [];
      
       $(document).ready(function() {
            
         $('#selectcandidategroup').on('change', function() {
        var selectedGroup = $(this).val();
        $('#dataTable1').DataTable().column(1).search(selectedGroup).draw(); // Assuming the group is in the second column. Adjust the column index accordingly.
    });
      
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
                     },{
                        "targets": [3,4,5,6],
                    "visible": false

                },{
                "type": "num-fmt",
                "targets": [2], // Assuming you want to sort the first column containing fractions
                "render": function (data, type, row) {
                    if (type === "sort") {
                        // Convert fractions to a format that can be sorted numerically
                        var parts = data.split('/');
                        return parseFloat(parts[0]) / parseFloat(parts[1]);
                    } else {
                        return data; // Return the original data for display
                    }
                }
            }
                 ],
                 order: [[1, 'desc'],[4,'asc'],[2,'desc']],
                
                 "rowCallback": function( row, data, index ) {
                  
                  $(row).removeClass('oldroundwin');
                  $(row).removeClass('wincolor');
                  $(row).removeClass('nextroundcolor');
                  $(row).removeClass('losecolor');
                  
                  var round_num_choosen=$('#selectelectionround').val();
           
           if((data[3]==1)&&(data[4]<round_num_choosen)){
              $(row).addClass('oldroundwin');
           }
           if((data[3]==1)&&(data[4]==round_num_choosen)){
              $(row).addClass('wincolor');
           }
                  if(data[3]==2){
                     $(row).addClass('nextroundcolor');
                  }
                  if(data[3]==-1){
                     $(row).addClass('losecolor');
                  }
      }
             });
      
             fetchdata($('#selectelectionround').val());
       });
      
       function fetchdata(roundnumber){
         
          
           
         datatable1_dataset.length = 0; 
         '@if($electionobj)'
         electioncode='{{$electionobj->election_code}}';
         $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
       
       fetch('/getelectionresults/' + electioncode+'/'+roundnumber )
         .then(response => response.json()) // Parse the JSON response
         .then(data => {
           var candidates_winning = data;
        
           
           candidates_winning.forEach(function(candidate, index) {
           
            var new_row = [candidate['full_name'], candidate['group_name'],
                candidate['elect_perc']+'/'+candidate['votersTotal'],candidate['reswin'],
                candidate['can_round_num'],candidate['profile_code'],candidate['win_max']];
             
               datatable1_dataset.push(new_row);
             });
             
             $('#dataTable1').DataTable().clear().rows.add(datatable1_dataset).draw();
         })
         .catch(error => {
           console.error('Error fetching data:', error);
         });
         '@endif'
   }

       $('#selectelectionround option').each(function() {
        // Get the value of the option
        var value = $(this).val();
        // Append the value as text within the option
        $(this).text("الجولة "+numToWordsAR_M(value)+"");
    });

    $('#selectelectionround').val(cur_round_var);

    $('#selectelectionround').on('change', function(){
        // Get the selected value
        var selectedValue = $(this).val();
        
        fetchdata(selectedValue);
    });

    function numToWordsAR_M(num = 0) {
        if (num == 0) return "صفر";
        let n, N, o = "",
            l = false,
            W = " و",
            m = "مائة",
            L = (num = "0".repeat((num += "").length * 2 % 3) + num).length,
            S = [, "ألف", "مليون", "مليار", "ترليون", "كوادرليون"],
            T = ["", "الأولى", "الثانية", "الثالثة", "الرابعة", "خمسة", "ستة", "سبعة", "ثمانية", "تسعة", "عشرة"];
        for (let D = L; D > 0; D -= 3) {
            n = +num.substring(L - D, L - D + 3);
            l = !+num.substring(L - D + 3);
            n && (o += $(D / 3 - 1), l || (o += "" + W));
        }
        return o;

        function $(P) {
            let s = S[P],
                h = ~~(n / 100),
                u = (N = n % 100) % 10,
                t = ~~(N / 10),
                H = "",
                wN = "";
            if (h) {
                if (h > 2) H = T[h].slice(0, (h == 8 ? -2 : -1)) + m;
                else if (h == 1) H = m;
                else H = m.slice(0, -1) + (s && !N ? "تا" : "تان");
            }
            if (N > 19) wN = T[u] + (u ? W : "") + (t == 2 ? "عشر" : T[t].slice(0, (t == 8 ? -2 : -1))) + "ون";
            else if (N > 10) wN = (u == 1 ? "أحد" : (u == 2 ? "اثنا" : T[u])) + " عشر";
            else if (N > 2 || !N) wN = T[N];
            else wN = T[N];
            let w = H + (h && N ? W : "") + wN;
            if (!s) return w;
            if (N > 2) return w + " " + (N > 10 ? s + "ًا" : (P < 3 ? [, "آلاف", "ملايين"][P] : S[P] + "ات"));
            if (!N) return w + " " + s;
            w = (h ? H + W : "") + s;
            return (N == 1) ? w : w + "ان";
        }
    }
   </script>
</html>
