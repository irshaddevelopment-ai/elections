@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ URL('css/cairo.css') }}">
<style>
  /* ── Page background — matches login ── */
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

  /* ── Page ── */
  .em-page {
    position: relative; z-index: 1;
    min-height: calc(100vh - 72px);
    padding: 2.5rem 1rem;
    font-family: 'Cairo', sans-serif;
  }

  /* ── Card ── */
  .em-card {
    background: #fff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow:
      0 4px 6px rgba(30,58,112,0.05),
      0 20px 50px rgba(30,58,112,0.11),
      0 0 0 1px rgba(212,168,32,0.2);
  }
  .em-card::before {
    content: ''; display: block; height: 4px;
    background: linear-gradient(90deg, #c8920a, #f0c94d, #c8920a);
  }

  /* ── Card header ── */
  .em-card-header {
    background: linear-gradient(160deg, #1a3268 0%, #1e4098 55%, #16305e 100%);
    padding: 1.25rem 1.75rem;
    display: flex; align-items: center; gap: 0.75rem;
    position: relative;
  }
  .em-card-header::after {
    content: '✦';
    position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%);
    color: rgba(212,168,32,0.3); font-size: 1rem;
  }
  .em-card-header .em-header-icon {
    width: 40px; height: 40px; border-radius: 0.6rem;
    background: rgba(212,168,32,0.15);
    border: 1px solid rgba(212,168,32,0.3);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .em-card-header .em-header-icon i { color: #f0c94d; font-size: 1rem; }
  .em-card-header h5 { margin: 0; font-weight: 700; font-size: 1.05rem; color: #fff; }

  /* ── Card body ── */
  .em-card-body { padding: 1.75rem; border-top: 3px solid #d4a820; }

  /* ── Section label ── */
  .em-section-label {
    font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.8px; text-transform: uppercase;
    color: #1e3a70; margin-bottom: 0.9rem;
    display: flex; align-items: center; gap: 0.5rem;
  }
  .em-section-label::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(90deg, #d4a820, transparent);
  }
  .em-section-label i { color: #c8920a !important; }

  /* ── Labels ── */
  .em-label {
    font-size: 0.8rem; font-weight: 700;
    color: #1e3a70; margin-bottom: 0.4rem; display: block;
  }

  /* ── Inputs ── */
  .em-input {
    width: 100%; height: 44px; padding: 0 0.85rem;
    border-radius: 0.65rem; border: 1.5px solid #dde3ef;
    background: #f8faff; font-size: 0.92rem; color: #0f1f40;
    font-family: 'Cairo', sans-serif;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  }
  .em-input:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 3px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }

  /* ── Toggle switches ── */
  .em-toggle-group { display: flex; gap: 1rem; flex-wrap: wrap; }
  .em-toggle-item {
    display: flex; align-items: center; gap: 0.5rem;
    background: #f8f4e8; border: 1.5px solid rgba(212,168,32,0.3);
    border-radius: 0.65rem; padding: 0.5rem 1rem;
    cursor: pointer; transition: border-color 0.2s, background 0.2s;
  }
  .em-toggle-item:hover { border-color: #d4a820; background: #fef9e7; }
  .em-toggle-item .custom-control-label {
    font-size: 0.92rem; font-weight: 600; color: #1e3a70; cursor: pointer;
  }
  .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #c8920a; border-color: #c8920a;
  }

  /* ── Rounds table ── */
  .em-rounds-wrap {
    border-radius: 0.85rem;
    border: 1.5px solid rgba(212,168,32,0.25);
    overflow: hidden; background: #fff;
  }
  .em-rounds-table {
    width: 100%; border-collapse: collapse; font-size: 0.88rem; margin: 0;
  }
  .em-rounds-table thead tr { background: linear-gradient(135deg, #f8f4e8, #fef9e7); }
  .em-rounds-table thead th,
  .em-rounds-table thead td {
    padding: 0.75rem 0.6rem; font-weight: 700;
    color: #1e3a70; font-size: 0.8rem; text-align: center;
    border-bottom: 2px solid rgba(212,168,32,0.3); white-space: nowrap;
  }
  .em-rounds-table tbody tr {
    border-bottom: 1px solid #f0ecd8; transition: background 0.15s;
  }
  .em-rounds-table tbody tr:last-child { border-bottom: none; }
  .em-rounds-table tbody tr:hover { background: #fef9e7; }
  .em-rounds-table tbody td { padding: 0.55rem 0.5rem; vertical-align: middle; text-align: center; }

  .em-rounds-table .form-control,
  .em-rounds-table .custom-select {
    border-radius: 0.5rem; border: 1.5px solid #dde3ef;
    background: #f8faff; font-size: 0.88rem; height: 38px;
    color: #0f1f40; font-family: 'Cairo', sans-serif;
  }
  .em-rounds-table .form-control:focus,
  .em-rounds-table .custom-select:focus {
    border-color: #d4a820;
    box-shadow: 0 0 0 2px rgba(212,168,32,0.18);
    background: #fff; outline: none;
  }

  /* ── Buttons ── */
  .btn-em-primary {
    position: relative; overflow: hidden;
    height: 44px; padding: 0 1.5rem; border-radius: 0.65rem;
    font-size: 0.92rem; font-weight: 800; font-family: 'Cairo', sans-serif;
    border: none; color: #1a2e0f;
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
  .btn-em-primary:hover {
    transform: translateY(-2px); box-shadow: 0 7px 22px rgba(212,168,32,0.5); color: #1a2e0f;
  }

  .btn-em-secondary {
    height: 44px; padding: 0 1.25rem; border-radius: 0.65rem;
    font-size: 0.92rem; font-weight: 700; font-family: 'Cairo', sans-serif;
    background: #fff; border: 1.5px solid #dde3ef; color: #1e3a70;
    transition: background 0.2s, border-color 0.2s; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.45rem;
  }
  .btn-em-secondary:hover { background: #f8f4e8; border-color: rgba(212,168,32,0.45); color: #1a2e0f; }

  .btn-em-add {
    height: 32px; padding: 0 0.75rem; border-radius: 0.5rem;
    font-size: 0.82rem; font-weight: 700; font-family: 'Cairo', sans-serif;
    background: linear-gradient(135deg, #1a3268, #1e4098);
    border: none; color: #f0c94d;
    cursor: pointer; transition: opacity 0.2s, transform 0.15s;
    display: inline-flex; align-items: center; gap: 0.3rem;
  }
  .btn-em-add:hover { opacity: 0.88; transform: translateY(-1px); }

  .btn-em-danger {
    height: 32px; padding: 0 0.65rem; border-radius: 0.5rem;
    font-size: 0.82rem; background: #fff0f0;
    border: 1.5px solid #ffc9c9; color: #dc3545;
    cursor: pointer; transition: background 0.2s, border-color 0.2s;
    display: inline-flex; align-items: center;
  }
  .btn-em-danger:hover { background: #ffe0e0; border-color: #f5a0a0; }
</style>

<div class="em-page" dir="rtl">
  <div class="container" style="max-width: 820px;">

    <div class="em-card">

      <!-- Header -->
      <div class="em-card-header">
        <div class="em-header-icon">
          <i class="fas fa-vote-yea"></i>
        </div>
        <h5>إضافة عملية انتخابية</h5>
      </div>

      <!-- Body -->
      <div class="em-card-body">
        <form id="electionmanagerform" action="/saveelectioninfo" method="post" enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="input_election_code" name="input_election_code" value="">

          <!-- Basic Info -->
          <div class="em-section-label"><i class="fas fa-info-circle" style="color:#0d6efd;"></i> المعلومات الأساسية</div>

          <div class="row">
            <div class="col-md-7 mb-3">
              <label class="em-label" for="election_name">اسم العملية <span style="color:#dc3545;">*</span></label>
              <input type="text" class="em-input" id="election_name" name="election_name"
                required oninvalid="this.setCustomValidity('أدخل الاسم')" oninput="this.setCustomValidity('')"
                autocomplete="off" placeholder="أدخل اسم العملية الانتخابية">
            </div>
            <div class="col-md-5 mb-3">
              <label class="em-label" for="input_election_date">التاريخ والوقت</label>
              <input type="datetime-local" id="input_election_date" name="input_election_date" class="em-input">
            </div>
          </div>

          <!-- Election Type -->
          <div class="em-section-label mt-2"><i class="fas fa-sliders-h" style="color:#0d6efd;"></i> نوع العملية</div>

          <div class="em-toggle-group mb-4">
            <label class="em-toggle-item" for="input_election_type1">
              <div class="custom-control custom-switch mb-0">
                <input type="checkbox" class="custom-control-input" id="input_election_type1" name="input_election_type1" checked>
                <label class="custom-control-label" for="input_election_type1">فردية</label>
              </div>
            </label>
            <label class="em-toggle-item" for="input_election_type2">
              <div class="custom-control custom-switch mb-0">
                <input type="checkbox" class="custom-control-input" id="input_election_type2" name="input_election_type2">
                <label class="custom-control-label" for="input_election_type2">لوائح</label>
              </div>
            </label>
          </div>

          <!-- Rounds -->
          <div class="em-section-label mt-1"><i class="fas fa-layer-group" style="color:#0d6efd;"></i> جولات الانتخاب</div>

          <div class="em-rounds-wrap mb-4">
            <table id="election_rounds_table" class="em-rounds-table">
              <thead>
                <tr>
                  <td style="display:none;"></td>
                  <th>عدد الجولات</th>
                  <th>نسبة الأصوات المطلوبة للنجاح</th>
                  <th>الحد الأدنى للانتقال للجولة التالية</th>
                  <td style="padding:0.6rem;">
                    <button type="button" class="btn-em-add" id="add_row_election_rounds">
                      <i class="fas fa-plus"></i> إضافة
                    </button>
                  </td>
                </tr>
              </thead>
              <tbody>
                <tr class="text-center">
                  <td style="display:none;"><input type="hidden" name="input_rounds_number[]" value="1"></td>
                  <td><input type="text" class="form-control" value="الجولة الأولى" readonly></td>
                  <td>
                    <div class="input-group" style="flex-wrap:nowrap;">
                      <div class="input-group-append">
                        <select class="custom-select" name="input_rounds_sign[]" style="border-radius:0.5rem 0 0 0.5rem; min-width:52px;">
                          <option value="0" selected>></option>
                          <option value="1">&ge;</option>
                        </select>
                      </div>
                      <input type="text" class="form-control" name="input_rounds_percent[]" value="50%" style="border-radius:0 0.5rem 0.5rem 0;">
                    </div>
                  </td>
                  <td>
                    <input type="text" class="form-control" name="input_rounds_min_percent[]" value="5%">
                  </td>
                  <td>
                    <button type="button" class="btn-em-danger removeRowBtn">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Actions -->
          <div class="d-flex gap-2" style="gap:0.75rem;">
            <button type="submit" class="btn-em-primary">
              <i class="fas fa-save"></i> حفظ المعلومات
            </button>
            <button type="button" class="btn-em-secondary" onclick="clearForm()">
              <i class="fas fa-eraser"></i> مسح
            </button>
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
    $('#input_election_date').val(currentDate.toISOString().slice(0, 16));

    $('#input_election_type1').change(function() {
      $('#input_election_type2').prop('checked', !$(this).is(':checked'));
    });

    $('#input_election_type2').change(function() {
      $('#input_election_type1').prop('checked', !$(this).is(':checked'));
    });

    $('#election_rounds_table').on('click', '.removeRowBtn', function() {
      $(this).closest('tr').remove();
    });

    $('#add_row_election_rounds').click(function() {
      var rowCount = $('#election_rounds_table tbody tr').length + 1;
      if (rowCount <= 3) {
        var round_text = 'الجولة ' + numToWordsAR_M(rowCount);
        var newRow =
          '<tr class="text-center">' +
          '<td style="display:none;"><input type="hidden" name="input_rounds_number[]" value="' + rowCount + '"></td>' +
          '<td><input type="text" class="form-control" value="' + round_text + '" readonly></td>' +
          '<td>' +
            '<div class="input-group" style="flex-wrap:nowrap;">' +
              '<div class="input-group-append">' +
                '<select class="custom-select" name="input_rounds_sign[]" style="border-radius:0.5rem 0 0 0.5rem; min-width:52px;">' +
                  '<option value="0" selected>></option>' +
                  '<option value="1">&ge;</option>' +
                '</select>' +
              '</div>' +
              '<input type="text" class="form-control" name="input_rounds_percent[]" value="50%" style="border-radius:0 0.5rem 0.5rem 0;">' +
            '</div>' +
          '</td>' +
          '<td><input type="text" class="form-control" name="input_rounds_min_percent[]" value="5%"></td>' +
          '<td><button type="button" class="btn-em-danger removeRowBtn"><i class="fas fa-trash"></i></button></td>' +
          '</tr>';
        $('#election_rounds_table tbody').append(newRow);
      }
    });
  });

  function previewImage(input) {
    var preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        var img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'img-fluid';
        img.style.maxWidth = '200px';
        preview.appendChild(img);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function clearForm() {
    document.getElementById('electionmanagerform').reset();
    var currentDate = new Date();
    $('#input_election_date').val(currentDate.toISOString().slice(0, 16));
    $('#election_name').focus();
  }

  function numToWordsAR_M(num = 0) {
    if (num == 0) return "صفر";
    let n, N, o = "", l = false, W = " و", m = "مائة",
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
      let s = S[P], h = ~~(n / 100), u = (N = n % 100) % 10, t = ~~(N / 10), H = "", wN = "";
      if (h) {
        if (h > 2) H = T[h].slice(0, (h == 8 ? -2 : -1)) + m;
        else if (h == 1) H = m;
        else H = m.slice(0, -1) + (s && !N ? "تا" : "تان");
      }
      if (N > 19) wN = T[u] + (u ? W : "") + (t == 2 ? "عشر" : T[t].slice(0, (t == 8 ? -2 : -1))) + "ون";
      else if (N > 10) wN = (u == 1 ? "أحد" : (u == 2 ? "اثنا" : T[u])) + " عشر";
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

@if ($current_election)
<script>
  $("#input_election_code").val('{{$current_election->election_code}}');
  $("#election_name").val('{{$current_election->election_name}}');
  var electiontype = '{{$current_election->election_type}}';
  if (electiontype == 1) {
    $('#input_election_type1').prop('checked', true);
    $('#input_election_type2').prop('checked', false);
  } else {
    $('#input_election_type1').prop('checked', false);
    $('#input_election_type2').prop('checked', true);
  }
  var imagePathvar = '{{$current_election->logo}}';
  var preview = document.getElementById('image-preview');
  if (preview) {
    preview.innerHTML = '';
    var img = document.createElement('img');
    img.src = "../election_logo/" + imagePathvar;
    img.className = 'img-fluid';
    img.style.maxWidth = '200px';
    preview.appendChild(img);
  }
  $('#election_rounds_table tbody').empty();
</script>
@endif

@if(isset($election_rounds))
  @foreach($election_rounds as $index => $round)
  <script>
    (function() {
      var rowCount = $('#election_rounds_table tbody tr').length + 1;
      if (rowCount <= 3) {
        var win_signValue = '{{$round->win_sign}}';
        var round_text = 'الجولة ' + numToWordsAR_M('{{$round->round_number}}');
        var newRow =
          '<tr class="text-center">' +
          '<td style="display:none;"><input type="hidden" name="input_rounds_number[]" value="' + rowCount + '"></td>' +
          '<td><input type="text" class="form-control" value="' + round_text + '" readonly></td>' +
          '<td>' +
            '<div class="input-group" style="flex-wrap:nowrap;">' +
              '<div class="input-group-append">' +
                '<select class="custom-select" name="input_rounds_sign[]" style="border-radius:0.5rem 0 0 0.5rem; min-width:52px;">' +
                  '<option value="0" ' + (win_signValue == 0 ? 'selected' : '') + '>></option>' +
                  '<option value="1" ' + (win_signValue == 1 ? 'selected' : '') + '>&ge;</option>' +
                '</select>' +
              '</div>' +
              '<input type="text" class="form-control" name="input_rounds_percent[]" value="{{$round->win_percentage}}%" style="border-radius:0 0.5rem 0.5rem 0;">' +
            '</div>' +
          '</td>' +
          '<td><input type="text" class="form-control" name="input_rounds_min_percent[]" value="{{$round->min_win_percentage}}%"></td>' +
          '<td><button type="button" class="btn-em-danger removeRowBtn"><i class="fas fa-trash"></i></button></td>' +
          '</tr>';
        $('#election_rounds_table tbody').append(newRow);
      }
    })();
  </script>
  @endforeach
@endif

@endsection
