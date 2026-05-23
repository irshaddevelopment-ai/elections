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
  <link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css" rel="stylesheet">
  <!-- MDBootstrap DataTables JS CDN -->
 

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
  
  <!-- jQuery CDN -->

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
 

       
</head>

<body>
  <style>
    /* ── Overlay ── */
    #overlay {
      position: fixed; top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.45);
      z-index: 9999; display: none;
    }

    /* ── Navbar shell ── */
    .app-navbar {
      background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
      border-bottom: 3px solid #d4a820;
      box-shadow: 0 4px 20px rgba(10,22,40,0.25);
      padding: 0 1.25rem;
      min-height: 62px;
      position: sticky;
      top: 0;
      z-index: 1030;
    }

    /* ── Brand ── */
    .app-navbar .navbar-brand {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0;
      font-weight: 800;
      font-size: 0.97rem;
      color: #fff !important;
      letter-spacing: 0.2px;
    }

    .app-navbar .brand-logo {
      width: 36px; height: 36px;
      border-radius: 50%;
      border: 2px solid rgba(212,168,32,0.55);
      object-fit: cover;
      box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }

    /* ── Nav links ── */
    .app-navbar .navbar-nav .nav-link {
      color: rgba(255,255,255,0.82) !important;
      font-size: 0.9rem;
      font-weight: 600;
      padding: 0.45rem 0.85rem !important;
      border-radius: 0.5rem;
      transition: background 0.18s, color 0.18s;
      display: flex;
      align-items: center;
      gap: 0.3rem;
    }

    .app-navbar .navbar-nav .nav-link:hover,
    .app-navbar .navbar-nav .nav-item.show > .nav-link {
      background: rgba(255,255,255,0.1);
      color: #f0c94d !important;
    }

    .app-navbar .navbar-nav .nav-link .dropdown-caret {
      font-size: 0.65rem;
      opacity: 0.6;
      margin-top: 1px;
    }

    /* ── Dropdown menu ── */
    .app-navbar .dropdown-menu {
      border: 1px solid #e8edf6;
      border-top: 3px solid #d4a820;
      border-radius: 0.75rem;
      box-shadow: 0 8px 28px rgba(10,22,40,0.15);
      padding: 0.4rem;
      min-width: 200px;
      margin-top: 0.35rem !important;
      background: #fff;
    }

    .app-navbar .dropdown-item {
      border-radius: 0.5rem;
      font-size: 0.88rem;
      font-weight: 600;
      color: #1e3a70;
      padding: 0.5rem 0.85rem;
      transition: background 0.15s, color 0.15s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .app-navbar .dropdown-item:hover {
      background: #fef9e7;
      color: #c8920a;
    }

    .app-navbar .dropdown-divider {
      margin: 0.3rem 0.5rem;
      border-color: #e8edf6;
    }

    /* ── Settings button ── */
    .app-navbar .settings-btn {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: rgba(212,168,32,0.12);
      border: 1.5px solid rgba(212,168,32,0.35);
      display: flex; align-items: center; justify-content: center;
      color: #f0c94d;
      font-size: 0.92rem;
      transition: background 0.18s, border-color 0.18s, color 0.18s;
      text-decoration: none;
    }

    .app-navbar .settings-btn:hover,
    .app-navbar .nav-item.show .settings-btn {
      background: #d4a820;
      border-color: #d4a820;
      color: #0c1e35;
    }

    .app-navbar .dropdown-item.text-danger { color: #dc3545 !important; }
    .app-navbar .dropdown-item.text-danger:hover { background: #fff5f5; color: #b91c1c !important; }
    .app-navbar .dropdown-item.text-primary { color: #1e3a70 !important; }

    /* ── Mobile toggler ── */
    .app-navbar .navbar-toggler {
      border: 1.5px solid rgba(212,168,32,0.4);
      border-radius: 0.5rem;
      padding: 0.35rem 0.6rem;
      color: #f0c94d;
    }

    .app-navbar .navbar-toggler:focus { box-shadow: none; }
    .app-navbar .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23f0c94d' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* ── Progress bar ── */
    .loading-container .progress {
      height: 3px;
      border-radius: 0;
      background: rgba(212,168,32,0.2);
    }
    .loading-container .progress-bar {
      background: linear-gradient(90deg, #c8920a, #f0c94d);
    }
  </style>
  <!-- Overlay -->
<div id="overlay"></div>
  <div class="loading-container" style="display:none;">
  <div class="progress">
    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
</div>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg app-navbar" dir="rtl">

    <a class="navbar-brand" href="{{ route('dashboard') }}">
      <img src="{{ URL('images/logo.jpg') }}" alt="Logo" class="brand-logo">
      @if($full_name)
        <span>{{ $full_name }}</span>
      @endif
    </a>

    <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- Main nav -->
      <ul class="navbar-nav mr-3">

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-users fa-sm"></i> الأسماء
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('usermanager') }}"><i class="fas fa-user-plus fa-sm"></i> إضافة اسم</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('userslist') }}"><i class="fas fa-list fa-sm"></i> لائحة الأسماء</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('subjectmanager') }}"><i class="fas fa-tag fa-sm"></i> إضافة موضوع</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('subjectslist') }}"><i class="fas fa-tags fa-sm"></i> لائحة المواضيع</a>
          </div>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-vote-yea fa-sm"></i> العمليات الانتخابية
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('electionmanager') }}"><i class="fas fa-plus-circle fa-sm"></i> إضافة عملية انتخابية</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('electionslist') }}"><i class="fas fa-list-alt fa-sm"></i> لائحة العمليات الانتخابية</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('adminresults') }}"><i class="fas fa-chart-bar fa-sm"></i> نتائج التصويت</a>
          </div>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-tie fa-sm"></i> المرشحون
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('candidatemanager') }}"><i class="fas fa-user-edit fa-sm"></i> إضافة/تعديل مرشحين</a>
          </div>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-id-card fa-sm"></i> الناخبون
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('votermanager') }}"><i class="fas fa-user-plus fa-sm"></i> إضافة ناخبين لعملية انتخابية</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('groupmanager') }}"><i class="fas fa-users fa-sm"></i> إضافة مجموعة ناخبين</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('groupslist') }}"><i class="fas fa-layer-group fa-sm"></i> لائحة المجموعات</a>
          </div>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-chalkboard-teacher fa-sm"></i> المرشدون
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('leadermanager') }}"><i class="fas fa-user-plus fa-sm"></i> إضافة مرشد</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('leaderslist') }}"><i class="fas fa-list fa-sm"></i> لائحة المرشدين</a>
          </div>
        </li>

      </ul>

      <!-- Settings (right side) -->
      <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">
          <a class="nav-link p-1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="settings-btn"><i class="fas fa-cog"></i></span>
          </a>
          <div class="dropdown-menu dropdown-menu-left">
            <a class="dropdown-item text-danger" onclick="resetdata()" style="cursor:pointer;">
              <i class="fas fa-database fa-sm"></i> إعادة تعيين البيانات
            </a>
            <a class="dropdown-item text-danger" onclick="resetlogin()" style="cursor:pointer;">
              <i class="fas fa-key fa-sm"></i> إعادة تعيين بيانات تسجيل الدخول
            </a>
            <a class="dropdown-item text-primary" href="{{ route('settings') }}">
              <i class="fas fa-sliders-h fa-sm"></i> إعدادات
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('logout', ['profile_code' => Session::get('profile_code')]) }}">
              <i class="fas fa-sign-out-alt fa-sm"></i> خروج
            </a>
          </div>
        </li>
      </ul>

    </div>
  </nav>

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

<script>
function resetlogin(){
  let progressBar = document.querySelector('.progress-bar');
  let loadingContainer = document.querySelector('.loading-container');
        var result = confirm("هذه العملية تؤدي إلى خسارة بيانات تسجيل الدخول. هل تريد إعادة تعيين البيانات؟");
        if (result) {
          let progress = 0;
    let success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block'; // Show the loading container
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        clearInterval(interval);
        loadingContainer.style.display = 'none'; // Hide the loading container when loading is complete
      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);
          fetch('/resetlogin')
      .then(response => response.json()) // Parse the JSON response
      .then(data => {
        success_var=1;
       
        location.reload();
      })
      .catch(error => {
       
        console.error('Error fetching data:', error);
      });
        }
}
function resetdata(){
  let progressBar = document.querySelector('.progress-bar');
  let loadingContainer = document.querySelector('.loading-container');
        var result = confirm("هذه العملية تؤدي إلى خسارة البيانات بالكامل ونهائيا. هل تريد إعادة تعيين البيانات؟");
        if (result) {
          let progress = 0;
    let success_var = 0;
    progressBar.style.width = 0;
    loadingContainer.style.display = 'block'; // Show the loading container
    let interval = setInterval(() => {
      progress += Math.random() * 50;
      if (success_var == 1) {
        clearInterval(interval);
        loadingContainer.style.display = 'none'; // Hide the loading container when loading is complete
      } else {
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
      }
    }, 500);
          fetch('/resetdata')
      .then(response => response.json()) // Parse the JSON response
      .then(data => {
        success_var=1;
       
        location.reload();
      })
      .catch(error => {
       
        console.error('Error fetching data:', error);
      });
        }
    };
  // Show overlay
  function showOverlay() {
        $('#overlay').show();
    }

    // Hide overlay
    function hideOverlay() {
        $('#overlay').hide();
    }
  </script>
</html>