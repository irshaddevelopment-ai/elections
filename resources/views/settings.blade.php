@extends('layouts.app')

@section('content')
<style>
    /* Increase font size */
    .custom-control-label {
      font-size: 20px;
    }

    /* Increase switch button size */
    .custom-control-input {
      width: 40px;
      height: 24px;
    }
  </style>
<section dir="rtl">
  <!-- Registration Form with Picture Upload -->
  <!-- Registration Form with Picture Upload -->
  <!-- Registration Form with Picture Upload -->
  <div class="container mt-5" style="text-align: right;">
  <h1 class="mb-4">إعدادات</h1>
  <div class="dropdown-divider"></div>
  <!-- Switch buttons -->
  <div class="custom-control custom-switch mb-3">
    <input type="checkbox" class="custom-control-input" id="sett_idcard" name="input_sett_idcard" checked>
    <label class="custom-control-label" for="sett_idcard">تفعيل البطاقة الشخصية</label>
  </div>
  
  <div class="custom-control custom-switch mb-3">
    <input type="checkbox" class="custom-control-input" id="sett_resetusercode" name="input_sett_resetusercode">
    <label class="custom-control-label" for="sett_resetusercode">تفعيل إعادة تنشيط رمز الناخب من صفحة المرشد</label>
  </div>

  <div class="dropdown-divider my-4"></div>

  <div class="mb-3">
    <h5 class="mb-1"><i class="fas fa-sign-out-alt"></i> الجلسات النشطة</h5>
    <p class="text-muted" style="font-size:14px;">تسجيل خروج جميع المستخدمين المتصلين حالياً وإعادة تعيين حالة الاتصال.</p>
    <button type="button" class="btn btn-warning" onclick="doLogoutAll()">
      <i class="fas fa-users-slash me-1"></i> تسجيل خروج الجميع
    </button>
    <span id="logoutAllStatus" style="display:none;margin-right:.75rem;font-size:.9rem;"></span>
  </div>

  <div class="dropdown-divider my-4"></div>

  <div class="mb-3">
    <h5 class="text-danger mb-1"><i class="fas fa-exclamation-triangle"></i> منطقة الخطر</h5>
    <p class="text-muted" style="font-size:14px;">سيؤدي هذا الإجراء إلى حذف جميع البيانات نهائياً ولا يمكن التراجع عنه.</p>
    <button type="button" class="btn btn-danger" onclick="document.getElementById('confirmResetModal').style.display='flex'">
      <i class="fas fa-trash-alt me-1"></i> إعادة تعيين جميع البيانات
    </button>
  </div>
</div>
</section>

<!-- Confirm Reset Modal -->
<div id="confirmResetModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:2rem;max-width:420px;width:90%;box-shadow:0 8px 40px rgba(0,0,0,0.25);text-align:right;">
    <h5 class="text-danger mb-3"><i class="fas fa-exclamation-circle"></i> تأكيد إعادة التعيين</h5>
    <p style="font-size:15px;color:#444;">هل أنت متأكد أنك تريد حذف <strong>جميع البيانات</strong>؟ هذا الإجراء لا يمكن التراجع عنه.</p>
    <div class="d-flex gap-2 mt-4" style="flex-direction:row-reverse;">
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('confirmResetModal').style.display='none'">إلغاء</button>
      <button type="button" class="btn btn-danger" id="btnConfirmReset" onclick="doResetData()">
        <i class="fas fa-trash-alt me-1"></i> نعم، احذف جميع البيانات
      </button>
    </div>
    <div id="resetStatus" class="mt-3" style="display:none;font-size:14px;"></div>
  </div>
</div>

<!-- Loading overlay -->
<div id="resetLoadingOverlay" style="display:none;position:fixed;inset:0;background:rgba(10,22,60,0.72);z-index:10000;align-items:center;justify-content:center;flex-direction:column;gap:1.2rem;">
  <div style="width:64px;height:64px;border:6px solid rgba(255,255,255,0.2);border-top-color:#f0c94d;border-radius:50%;animation:spinReset 0.85s linear infinite;"></div>
  <p style="color:#fff;font-size:1.1rem;font-weight:700;margin:0;">جارٍ حذف البيانات...</p>
  <style>@keyframes spinReset{to{transform:rotate(360deg);}}</style>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  $('#sett_idcard').change(function() {
   var sett_name_var = $(this).attr('id');
   var sett_value_var = $(this).prop('checked');
   savesettings(sett_name_var,sett_value_var);
  });
  $('#sett_resetusercode').change(function() {
   var sett_name_var = $(this).attr('id');
   var sett_value_var = $(this).prop('checked');
   savesettings(sett_name_var,sett_value_var);
  });
  function savesettings(sett_name,sett_value){
    var postData = {};
    postData['sett_name'] = sett_name;
    postData['sett_value'] = sett_value;
    fetch('/savesettings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      body: JSON.stringify(postData)
    })
    .then(response => { if (response.ok) {} })
    .catch(error => { console.error('Error:', error); });
  }

  function doLogoutAll() {
    var status = document.getElementById('logoutAllStatus');
    status.style.display = 'inline';
    status.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin"></i> جارٍ التنفيذ...</span>';
    fetch('/resetlogin')
      .then(function(r) { return r.json(); })
      .then(function() {
        status.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> تم تسجيل خروج الجميع بنجاح.</span>';
        setTimeout(function() { status.style.display = 'none'; }, 3000);
      })
      .catch(function() {
        status.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> حدث خطأ، يرجى المحاولة مجدداً.</span>';
      });
  }

  function doResetData() {
    document.getElementById('confirmResetModal').style.display = 'none';
    var overlay = document.getElementById('resetLoadingOverlay');
    overlay.style.display = 'flex';
    fetch('/resetdata')
      .then(function(r) { return r.text(); })
      .then(function() {
        overlay.style.display = 'none';
        var status = document.getElementById('resetStatus');
        document.getElementById('confirmResetModal').style.display = 'flex';
        status.style.display = 'block';
        status.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> تمت إعادة التعيين بنجاح.</span>';
        setTimeout(function() {
          document.getElementById('confirmResetModal').style.display = 'none';
          status.style.display = 'none';
        }, 2000);
      })
      .catch(function() {
        overlay.style.display = 'none';
        var status = document.getElementById('resetStatus');
        document.getElementById('confirmResetModal').style.display = 'flex';
        status.style.display = 'block';
        status.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> حدث خطأ، يرجى المحاولة مجدداً.</span>';
      });
  }
</script>

@if ($settings)
@foreach ($settings as $settingOBJ)
<script>
  var sett_idcard='{{$settingOBJ->settings_name}}';
  var settvalue='{{$settingOBJ->settings_value}}';
 
  
  $("#" + sett_idcard).prop("checked", settvalue == 1);
  
</script>
@endforeach
@endif

@endsection
