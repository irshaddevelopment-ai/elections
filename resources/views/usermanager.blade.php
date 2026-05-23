@extends('layouts.app')

@section('content')

<style>
  /* ── Page background — matches login ── */
  body {
    background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%) !important;
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

  .orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(100px);
    pointer-events: none;
    opacity: 0.16;
    animation: orbFloat 12s ease-in-out infinite;
    z-index: 0;
  }
  .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px;animation-delay:0s; }
  .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
  @keyframes orbFloat {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.14) translate(18px,-18px); }
  }

  .um-page {
    position: relative;
    z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem;
  }

  /* ── Card ── */
  .um-card {
    background: #fff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }

  /* Gold top accent line */
  .um-card::before {
    content: '';
    display: block;
    height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header — dark navy like login ── */
  .um-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1.25rem 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
  }

  /* Corner ornament */
  .um-card-header::after {
    content: '✦';
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(212,168,32,0.3);
    font-size: 1rem;
  }

  .um-card-header .um-header-icon {
    width: 40px; height: 40px;
    border-radius: 0.6rem;
    background: rgba(212,168,32,0.15);
    border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }

  .um-card-header .um-header-icon i { color: #f0c94d; font-size: 1rem; }

  .um-card-header h5 {
    margin: 0;
    font-weight: 700;
    font-size: 1.05rem;
    color: #fff;
  }

  .um-card-body { padding: 1.75rem; border-top: 3px solid #d4a820; }

  /* ── Section label ── */
  .um-section-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: #1e3a70;
    margin-bottom: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .um-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, #d4a820, transparent);
  }

  .um-section-label i { color: #c8920a !important; }

  /* ── Labels ── */
  .um-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e3a70;
    margin-bottom: 0.4rem;
    display: block;
  }

  /* ── Inputs ── */
  .um-input {
    width: 100%;
    height: 44px;
    padding: 0 0.85rem;
    border-radius: 0.65rem;
    border: 1.5px solid #dde3ef;
    background: #f8faff;
    font-size: 0.92rem;
    color: #0f1f40;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  }

  .um-input:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff;
    outline: none;
  }

  .um-textarea {
    width: 100%;
    padding: 0.6rem 0.85rem;
    border-radius: 0.65rem;
    border: 1.5px solid #dde3ef;
    background: #f8faff;
    font-size: 0.92rem;
    color: #0f1f40;
    resize: vertical;
    min-height: 90px;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  }

  .um-textarea:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff;
    outline: none;
  }

  /* ── File input ── */
  .um-file-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    height: 44px;
    padding: 0 1.1rem;
    border-radius: 0.65rem;
    border: 1.5px dashed rgba(212,168,32,0.45);
    background: #fffdf5;
    font-size: 0.88rem;
    font-weight: 600;
    color: #1e3a70;
    cursor: pointer;
    transition: background 0.18s, border-color 0.18s;
    width: 100%;
    justify-content: center;
  }

  .um-file-label:hover {
    background: #fef9e7;
    border-color: #d4a820;
  }

  #fileName {
    font-size: 0.78rem;
    color: #94a3b8;
    margin-top: 0.35rem;
    display: block;
  }

  /* ── Toggle ── */
  .um-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #f8f4e8;
    border: 1.5px solid rgba(212,168,32,0.3);
    border-radius: 0.65rem;
    padding: 0.65rem 1rem;
  }

  .um-toggle-wrap .custom-control-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e3a70;
    cursor: pointer;
  }

  .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #c8920a;
    border-color: #c8920a;
  }

  /* ── Photo panel ── */
  .um-photo-panel {
    background: #f8faff;
    border: 1.5px solid #dde3ef;
    border-radius: 1rem;
    padding: 1.5rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
  }

  .um-photo-panel .photo-placeholder {
    width: 120px; height: 120px;
    border-radius: 50%;
    background: #f8f4e8;
    border: 2px dashed rgba(212,168,32,0.4);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1rem;
    overflow: hidden;
  }

  .um-photo-panel .photo-placeholder i {
    font-size: 2.5rem;
    color: #d4a820;
    opacity: 0.5;
  }

  #image-preview img {
    width: 120px; height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(212,168,32,0.4);
    box-shadow: 0 4px 14px rgba(30,58,112,0.1);
  }

  .um-photo-panel p {
    font-size: 0.78rem;
    color: #94a3b8;
    margin: 0.6rem 0 0;
  }

  /* ── Buttons ── */
  .btn-um-primary {
    position: relative;
    overflow: hidden;
    height: 44px; padding: 0 1.5rem;
    border-radius: 0.65rem;
    font-size: 0.92rem; font-weight: 800;
    font-family: 'Cairo', sans-serif;
    border: none;
    color: #1a2e0f;
    background: linear-gradient(135deg, #c8920a 0%, #f0c94d 45%, #d4a820 75%, #c8920a 100%);
    background-size: 200% 200%;
    animation: goldShift 5s ease infinite;
    box-shadow: 0 4px 16px rgba(212,168,32,0.38);
    transition: transform 0.15s, box-shadow 0.2s;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.45rem;
  }
  @keyframes goldShift {
    0%,100% { background-position:0% 50%; }
    50%      { background-position:100% 50%; }
  }
  .btn-um-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 22px rgba(212,168,32,0.5);
    color: #1a2e0f;
  }
  .btn-um-primary:active { transform: translateY(0); }
  .btn-um-primary .waves-ripple { background: rgba(255,255,255,0.45); }

  .btn-um-secondary {
    height: 44px; padding: 0 1.25rem;
    border-radius: 0.65rem;
    font-size: 0.92rem; font-weight: 700;
    font-family: 'Cairo', sans-serif;
    background: #fff;
    border: 1.5px solid #dde3ef;
    color: #1e3a70;
    transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.45rem;
  }

  .btn-um-secondary:hover {
    background: #f8f4e8;
    border-color: rgba(212,168,32,0.45);
    color: #1a2e0f;
  }
</style>

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="um-page" dir="rtl">
  <div class="container" style="max-width: 900px;">
    <div class="um-card">

      <!-- Header -->
      <div class="um-card-header">
        <div class="um-header-icon">
          <i class="fas fa-user-plus"></i>
        </div>
        <h5>إضافة اسم</h5>
      </div>

      <!-- Body -->
      <div class="um-card-body">
        <form id="usermanagerform" action="/saveuserinfo" method="post" enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="input_profile_code" name="input_profile_code" value="">

          <div class="row">

            <!-- Left: Form fields -->
            <div class="col-md-7">

              <div class="um-section-label"><i class="fas fa-info-circle" style="color:#0d6efd;"></i> المعلومات الشخصية</div>

              <div class="row">
                <div class="col-12 mb-3">
                  <label class="um-label" for="input_fullname">الاسم <span style="color:#dc3545;">*</span></label>
                  <input type="text" class="um-input" id="input_fullname" name="input_fullname"
                    required oninvalid="this.setCustomValidity('أدخل الاسم')" oninput="this.setCustomValidity('')"
                    autocomplete="off" placeholder="الاسم الكامل">
                </div>

                <div class="col-md-6 mb-3">
                  <label class="um-label" for="sex">الجنس</label>
                  <select class="um-input" id="sex" name="sex">
                    <option value="1">ذكر</option>
                    <option value="2">أنثى</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label class="um-label" for="input_age">تاريخ الولادة</label>
                  <input type="date" class="um-input" id="input_age" name="input_age">
                </div>

                <div class="col-12 mb-3">
                  <label class="um-label" for="input_tel">الهاتف <span style="color:#dc3545;">*</span></label>
                  <input type="number" class="um-input" id="input_tel" name="input_tel"
                    required oninvalid="this.setCustomValidity('أدخل رقم الهاتف')" oninput="this.setCustomValidity('')"
                    autocomplete="off" placeholder="رقم الهاتف">
                </div>

                <div class="col-12 mb-3">
                  <label class="um-label" for="input_address">العنوان</label>
                  <textarea class="um-textarea" id="input_address" name="input_address" autocomplete="off" placeholder="العنوان الكامل"></textarea>
                </div>
              </div>

              <div class="um-section-label mt-1"><i class="fas fa-paperclip" style="color:#0d6efd;"></i> المرفقات والصلاحيات</div>

              <div class="mb-3">
                <label class="um-label">تحميل ملف</label>
                <label class="um-file-label" for="input_profile_attach">
                  <i class="fas fa-upload"></i> اختر ملفاً (JPG، PNG، PDF)
                  <input type="file" id="input_profile_attach" name="input_profile_attach"
                    accept=".jpg,.png,.pdf" style="display:none;" onchange="displayFileName()">
                </label>
                <small id="fileName" class="form-text"></small>
              </div>

              <div class="um-toggle-wrap mb-4">
                <div class="custom-control custom-switch mb-0">
                  <input type="checkbox" class="custom-control-input" id="input_users_status" name="input_users_status">
                  <label class="custom-control-label" for="input_users_status">صلاحيات مطلقة في البرنامج</label>
                </div>
              </div>

              <!-- Actions -->
              <div class="d-flex" style="gap:0.75rem;">
                <button type="submit" class="btn-um-primary">
                  <i class="fas fa-save"></i> حفظ المعلومات
                </button>
                <button type="button" class="btn-um-secondary" onclick="clearForm()">
                  <i class="fas fa-eraser"></i> مسح
                </button>
              </div>

            </div>

            <!-- Right: Photo upload -->
            <div class="col-md-5 mt-4 mt-md-0">
              <div class="um-section-label"><i class="fas fa-camera" style="color:#0d6efd;"></i> الصورة الشخصية</div>
              <div class="um-photo-panel">
                <div class="photo-placeholder" id="photo-placeholder">
                  <i class="fas fa-user"></i>
                </div>
                <div id="image-preview"></div>
                <label class="um-file-label mt-3" style="width:auto; padding: 0 1.25rem;" for="profile_picture">
                  <i class="fas fa-camera"></i> تحميل صورة
                  <input type="file" id="profile_picture" name="profile_picture"
                    style="display:none;" accept="image/*" onchange="previewImage(this)">
                </label>
                <p>الصيغ المدعومة: JPG، PNG، GIF</p>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    var currentDate = new Date();
    $('#input_age').val(currentDate.toISOString().split('T')[0]);
  });

  function displayFileName() {
    var input = document.getElementById('input_profile_attach');
    var label = document.getElementById('fileName');
    if (input.files && input.files[0]) {
      label.textContent = 'اسم الملف: ' + input.files[0].name;
    }
  }

  function previewImage(input) {
    var placeholder = document.getElementById('photo-placeholder');
    var preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        placeholder.style.display = 'none';
        preview.innerHTML = '';
        var img = document.createElement('img');
        img.src = e.target.result;
        preview.appendChild(img);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function clearForm() {
    var form = document.getElementById('usermanagerform');
    form.reset();
    document.getElementById('image-preview').innerHTML = '';
    document.getElementById('photo-placeholder').style.display = 'flex';
    document.getElementById('fileName').textContent = '';
    var currentDate = new Date();
    $('#input_age').val(currentDate.toISOString().split('T')[0]);
    $('#input_fullname').focus();
  }
</script>

@if ($current_profile)
<script>
  var isadmin = '{{$isadmin}}';
  $("#input_profile_code").val('{{$current_profile->profile_code}}');
  $("#input_fullname").val('{{$current_profile->full_name}}');
  $("#input_age").val('{{$current_profile->age}}');
  $("#input_tel").val('{{$current_profile->mobile}}');
  $("#input_address").val('{{$current_profile->address}}');
  $("#fileName").text('اسم الملف : {{$current_profile->attachment}}');
  if (isadmin == 1) {
    $("#input_users_status").prop("checked", true);
  } else {
    $("#input_users_status").prop("checked", false);
  }
  $('#sex').val('{{$current_profile->sex}}');
  var imagePathvar = '{{$current_profile->picture}}';
  document.getElementById('photo-placeholder').style.display = 'none';
  var preview = document.getElementById('image-preview');
  preview.innerHTML = '';
  var img = document.createElement('img');
  img.src = "../profile_picture/" + imagePathvar;
  preview.appendChild(img);
</script>
@endif

@endsection
