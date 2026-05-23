@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<style>
  body {
    background: linear-gradient(145deg, #e8f0fb 0%, #f3f7ff 55%, #e4edf8 100%) !important;
  }
  body::after {
    content: '';
    position: fixed; inset: 0;
    background-image: radial-gradient(circle, rgba(30,58,112,0.05) 1.5px, transparent 1.5px);
    background-size: 28px 28px;
    pointer-events: none; z-index: 0;
  }
  .orb {
    position: fixed; border-radius: 50%;
    filter: blur(100px); pointer-events: none;
    opacity: 0.16; animation: orbFloat 12s ease-in-out infinite; z-index: 0;
  }
  .orb-1 { width:460px;height:460px;background:#bfdbfe;top:-130px;left:-90px;animation-delay:0s; }
  .orb-2 { width:380px;height:380px;background:#fde68a;bottom:-90px;right:-70px;animation-delay:-6s; }
  @keyframes orbFloat {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.14) translate(18px,-18px); }
  }

  .sm-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem;
  }

  /* ── Card ── */
  .sm-card {
    background: #fff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }
  .sm-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .sm-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1.25rem 1.75rem;
    display: flex; align-items: center; gap: 0.75rem;
    position: relative;
  }
  .sm-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .sm-header-icon {
    width: 40px; height: 40px; border-radius: 0.6rem;
    background: rgba(212,168,32,0.15);
    border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .sm-header-icon i { color: #f0c94d; font-size: 1rem; }
  .sm-card-header h5 { margin: 0; font-weight: 700; font-size: 1.05rem; color: #fff; }

  .sm-card-body { padding: 1.75rem; border-top: 3px solid #d4a820; }

  /* ── Section label ── */
  .sm-section-label {
    font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.8px; text-transform: uppercase;
    color: #1e3a70; margin-bottom: 0.9rem;
    display: flex; align-items: center; gap: 0.5rem;
  }
  .sm-section-label::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(90deg, #d4a820, transparent);
  }
  .sm-section-label i { color: #c8920a !important; }

  /* ── Labels ── */
  .sm-label {
    font-size: 0.8rem; font-weight: 700;
    color: #1e3a70; margin-bottom: 0.4rem; display: block;
  }

  /* ── Inputs ── */
  .sm-input {
    width: 100%; height: 44px; padding: 0 0.85rem;
    border-radius: 0.65rem; border: 1.5px solid #dde3ef;
    background: #f8faff; font-size: 0.92rem; color: #0f1f40;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    font-family: 'Cairo', sans-serif;
  }
  .sm-input:focus {
    border-color: #d4a820; box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }
  .sm-textarea {
    width: 100%; padding: 0.6rem 0.85rem;
    border-radius: 0.65rem; border: 1.5px solid #dde3ef;
    background: #f8faff; font-size: 0.92rem; color: #0f1f40;
    resize: vertical; min-height: 110px;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    font-family: 'Cairo', sans-serif;
  }
  .sm-textarea:focus {
    border-color: #d4a820; box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }

  /* ── Photo panel ── */
  .sm-photo-panel {
    background: #f8faff; border: 1.5px solid #dde3ef;
    border-radius: 1rem; padding: 1.5rem 1rem;
    display: flex; flex-direction: column; align-items: center; height: 100%;
  }
  .sm-photo-panel .photo-placeholder {
    width: 130px; height: 130px; border-radius: 0.75rem;
    background: #f8f4e8; border: 2px dashed rgba(212,168,32,0.4);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1rem; overflow: hidden;
  }
  .sm-photo-panel .photo-placeholder i { font-size: 2.5rem; color: #d4a820; opacity: 0.45; }
  #image-preview img {
    width: 130px; height: 130px; border-radius: 0.75rem; object-fit: cover;
    border: 3px solid rgba(212,168,32,0.4);
    box-shadow: 0 4px 14px rgba(30,58,112,0.1);
  }
  .sm-photo-panel p { font-size: 0.78rem; color: #94a3b8; margin: 0.6rem 0 0; }

  /* ── File label ── */
  .sm-file-label {
    display: inline-flex; align-items: center; gap: 0.5rem;
    height: 40px; padding: 0 1.1rem; border-radius: 0.65rem;
    border: 1.5px dashed rgba(212,168,32,0.45); background: #fffdf5;
    font-size: 0.85rem; font-weight: 600; color: #1e3a70;
    cursor: pointer; transition: background 0.18s, border-color 0.18s;
    justify-content: center; margin-top: 0.75rem;
  }
  .sm-file-label:hover { background: #fef9e7; border-color: #d4a820; }

  /* ── Buttons ── */
  .btn-sm-primary {
    position: relative; overflow: hidden;
    height: 44px; padding: 0 1.5rem; border-radius: 0.65rem;
    font-size: 0.92rem; font-weight: 800;
    font-family: 'Cairo', sans-serif; border: none; color: #1a2e0f;
    background: linear-gradient(135deg, #c8920a 0%, #f0c94d 45%, #d4a820 75%, #c8920a 100%);
    background-size: 200% 200%; animation: goldShift 5s ease infinite;
    box-shadow: 0 4px 16px rgba(212,168,32,0.38);
    transition: transform 0.15s, box-shadow 0.2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.45rem;
  }
  @keyframes goldShift {
    0%,100% { background-position:0% 50%; }
    50%      { background-position:100% 50%; }
  }
  .btn-sm-primary:hover {
    transform: translateY(-2px); box-shadow: 0 7px 22px rgba(212,168,32,0.5);
    color: #1a2e0f;
  }
  .btn-sm-secondary {
    height: 44px; padding: 0 1.25rem; border-radius: 0.65rem;
    font-size: 0.92rem; font-weight: 700;
    font-family: 'Cairo', sans-serif;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    transition: background 0.2s, border-color 0.2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.45rem;
  }
  .btn-sm-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.45); color: #1a2e0f; }
</style>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="sm-page" dir="rtl">
  <div class="container" style="max-width: 860px;">
    <div class="sm-card">

      <!-- Header -->
      <div class="sm-card-header">
        <div class="sm-header-icon"><i class="fas fa-tag"></i></div>
        <h5>إضافة موضوع</h5>
      </div>

      <!-- Body -->
      <div class="sm-card-body">
        <form id="usermanagerform" action="/savesubjectinfo" method="post" enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="input_subject_code" name="input_subject_code" value="">

          <div class="row">

            <!-- Left: fields -->
            <div class="col-md-7">

              <div class="sm-section-label"><i class="fas fa-info-circle"></i> معلومات الموضوع</div>

              <div class="mb-3">
                <label class="sm-label" for="input_subjecttitle">
                  عنوان الموضوع <span style="color:#dc3545;">*</span>
                </label>
                <input type="text" class="sm-input" id="input_subjecttitle" name="input_subjecttitle"
                  required oninvalid="this.setCustomValidity('أدخل عنوان الموضوع')"
                  oninput="this.setCustomValidity('')"
                  autocomplete="off" placeholder="عنوان الموضوع">
              </div>

              <div class="mb-4">
                <label class="sm-label" for="input_desc">معلومات عن الموضوع</label>
                <textarea class="sm-textarea" id="input_desc" name="input_desc"
                  placeholder="وصف مختصر عن الموضوع..."></textarea>
              </div>

              <div class="d-flex" style="gap:0.75rem;">
                <button type="submit" class="btn-sm-primary">
                  <i class="fas fa-save"></i> حفظ المعلومات
                </button>
                <button type="button" class="btn-sm-secondary" onclick="clearForm()">
                  <i class="fas fa-eraser"></i> مسح
                </button>
              </div>

            </div>

            <!-- Right: photo -->
            <div class="col-md-5 mt-4 mt-md-0">
              <div class="sm-section-label"><i class="fas fa-image"></i> صورة الموضوع</div>
              <div class="sm-photo-panel">
                <div class="photo-placeholder" id="photo-placeholder">
                  <i class="fas fa-image"></i>
                </div>
                <div id="image-preview"></div>
                <label class="sm-file-label" for="subject_picture">
                  <i class="fas fa-camera"></i> تحميل صورة
                  <input type="file" id="subject_picture" name="subject_picture"
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

<script>
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
    document.getElementById('usermanagerform').reset();
    document.getElementById('image-preview').innerHTML = '';
    document.getElementById('photo-placeholder').style.display = 'flex';
    document.getElementById('input_subjecttitle').focus();
  }
</script>

@endsection
