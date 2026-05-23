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
    .no_drop {
    cursor: no-drop;
}
    .custom-label {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.875rem;
      font-weight: 500;
      color: #fff;
      background-color: #6c757d;
    }

    .navbar-nav {
      font-size: 18px;
      /* Adjust the font size as needed */
    }

    .navbar-brand img {
      border-radius: 50%;
    }

    /* Custom CSS for DataTable header font weight */
    #dtcandidates thead th ,
    #dtcandidateslist thead th {
      font-weight: bold;
      font-size: 16px;
    }

    #dtcandidates tbody td ,
    #dtcandidateslist tbody td {
      font-size: 16px;
    }

    td {
      border: 1px solid #dddddd;
      text-align: center;
      vertical-align: middle;
    }

    .election-toggle {
      position: relative;
      display: inline-block;
      width: 60px;
      /* Adjusted width */
      height: 24px;
      /* Adjusted height */
    }

    .election-toggle input {
      display: none;
    }

    .election-toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 24px;
      /* Adjusted border-radius */
    }

    .election-toggle-slider:before {
      position: absolute;
      content: "";
      height: 18px;
      /* Adjusted height */
      width: 18px;
      /* Adjusted width */
      left: 3px;
      /* Adjusted position */
      bottom: 3px;
      /* Adjusted position */
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.election-toggle-slider {
      background-color: #2196F3;
    }

    input:checked+.election-toggle-slider:before {
      -webkit-transform: translateX(36px);
      /* Adjusted translation */
      -ms-transform: translateX(36px);
      /* Adjusted translation */
      transform: translateX(36px);
      /* Adjusted translation */
    }

    /* Rounded sliders */
    .election-toggle-slider.round {
      border-radius: 24px;
      /* Adjusted border-radius */
    }

    .election-toggle-slider.round:before {
      border-radius: 50%;
    }

    .highlight {
      background-color: lightblue;
    }

    /* Adjust row height */
    #dtcandidates tbody tr {
      line-height: 0.7rem;
      /* Adjust the value as needed */
    }
    #dtcandidates.disabled-overlay {
  pointer-events: all;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: transparent;
}
  </style>
 
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="{{ route('home') }}">
      <img src="{{ URL('images/logo.webp') }}" alt="Profile Picture" width="30" height="30" class="d-inline-block align-top">
      @if($users)
      {{$users->full_name}}
      @endif
    </a>
    <a class="navbar-brand" href="{{ route('leaderdash') }}">
      @if($isleader)
      <span >أفراد المجموعة</span>
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
        <h4 class="text-xl">
             بطاقة تعريفية للمرشح 
        </h4>
        <h5 class="text-xl" id="round_count">
        </h5>
      </span>
    </div>

    <div class="row">
    
      <div class="col-6 d-flex" style="text-align: right;">
      <button class="btn @if(!$results_exists) btn-text-muted @else btn-secondary @endif btn-sm no_drop" style="font-size: 16px;" 
      onclick="showguestresults();" id="btn_results" 
      @if(!$results_exists) disabled @endif>
      @if(!$results_exists) لم تصدر النتئج بعد @else النتائج @endif</button>
      </div>
    
      <div class="col-6">
        <button class="btn btn-primary btn-sm" style="font-size: 16px;" onclick="showsubmitmodal();" id="btn_vote"
        >تصويت</button>
      </div>
    </div>
    <div class="dropdown-divider"></div>
    <div class="container mt-5">
    <table id="dtcandidateslist" class="table">
      <thead class="text-center">
        <tr>
          <th>اللائحة</th>
          <th >المرشحين</th>
          <th >للفوز</th>
          <th>المختارين</th>
          <th style="display:none;">candidate_list_code</th>


        </tr>
      </thead>
      <tbody class="text-center">
      @if($candidate_groups)
            @foreach ($candidate_groups as $candidate_groupobj)
            <tr>
          <th>{{$candidate_groupobj->group_name}}</th>
          <th >{{$candidate_groupobj->candidates_number}}</th>
          <th >{{$candidate_groupobj->win_number}}</th>
          <th>0</th>
          <th style="display:none;">{{$candidate_groupobj->group_code}}</th>


        </tr>
            @endforeach
            @endif
      </tbody>

    </table>
    <h4>
    <div class="container mt-5" style="display:none;">
          @if($candidates)
          <div style="display: flex; justify-content: space-between;">
    <span id="candidatenumber">عدد المرشحين : {{sizeof($candidates)}}</span>
    <span id="listwinnumber">العدد المطلوب للفوز: {{sizeof($candidates)}}</span>
</div>
          @endif
</div>
        </h4>
      <div class="row" style="display:none;">
        <div class="col-md-12">
          <label for="combobox" class="form-label" style="display:block; width:x; height:y; text-align:right;font-size: 18px;font-weight: bold;">اختيار لائحة</label>
          <select id="election_list_combo" class="mdb-select form-control">
            @if($candidate_groups)
            @foreach ($candidate_groups as $candidate_groupobj)
            <option value="{{$candidate_groupobj->group_code}}">{{$candidate_groupobj->group_name}}</option>
            @endforeach
            @endif
          </select>
        </div>
      </div>
    </div>
    <table id="dtcandidates" class="table">
      <thead class="text-center">
        <tr>
          <th>الإسم</th>
          <th class="d-none d-sm-table-cell">الجنس</th>
          <th class="d-none d-sm-table-cell">العنوان</th>
          <th>الصورة</th>
          <th>الملفات</th>
          <th>بطاقة تعريفية</th>
          <th style="display:none;">profile_code</th>


        </tr>
      </thead>
      <tbody class="text-center">
        
      </tbody>

    </table>

  </div>
  <div class="modal fade " id="vote_submit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
            <div class="modal-dialog modal-xl" role="document" style="width: 100%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">الموافقة على التصويت</h5>
                        <h5 class="modal-title" id="leadergrouptitle"></h5>
                        <input type="hidden" id="input_leadername" name="input_leadername" class="form-control" autocomplete="off" value="">
                        <input type="hidden" id="input_groupname" name="input_groupname" class="form-control" autocomplete="off" value="">

                        <button type="button" class="close ml-0" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <div class="modal-body">

                        <!-- First DataTable -->
                        <div class="container mt-4">
                          
                            <div class="row">
                                <!-- First DataTable -->
                                <div class="col-md-12">
                                    <table id="datatable_vote_modal" class="table table-striped table-bordered table-sm">
                                        <thead>

                                            <tr class="text-center" style="font-weight: bold;">
                                                <th>اللائحة</th>
                                                <th>عدد المرشحين</th>
                                                <th>عدد المختارين</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <!-- Add more rows as needed -->
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="modal-footer ">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">عودة</button>
                                <button type="button" class="btn btn-primary" onclick="submitVotes();" id="btn_sendvote">تصويت</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


</section>
<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>



</html>
