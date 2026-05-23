@extends('layouts.app')

@section('content')

<section dir="rtl">
    <!-- Registration Form with Picture Upload -->
    <!-- Registration Form with Picture Upload -->
    <!-- Registration Form with Picture Upload -->
    <div class="container mt-5">
        <div class="row" style="font-size: 18px;">
            <div class="col-md-6" style="text-align: right;">
                <h2>إضافة عملية مع لوائح مرشحيها</h2>
                <div class="dropdown-divider"></div>
                <form id="usermanagerform" action="/saveuserinfo" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="input_profile_code" name="input_profile_code" class="form-control" autocomplete="off" value="">
                    <div class="form-group">
                        <label for="fullname">اسم العملية*</label>
                        <input type="text" class="form-control" id="input_fullname" name="input_fullname" required oninvalid="this.setCustomValidity('أدخل الاسم')" oninput="this.setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        <label for="age">التاريخ</label>
                        <input type="date" id="input_age" name="input_age" class="form-control">
                    </div>
                    <div class="container_rounds mt-4">
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label for="fullname">عدد الجولات</label>
                        <input type="number" class="form-control" id="input_rounds_number" name="input_rounds_number" value="1" >
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fullname">نسبة الأصوات المطلوبة للنجاح</label>
                        <div class="input-group">
            <!-- Right Side ComboBox -->
            <div class="input-group-append">
                <select class="custom-select">
                    <option selected>></option>
                    <option value="1">&ge;</option>
                </select>
            </div>
            <!-- Input Text -->
            <input type="text" class="form-control" id="input_rounds_percent" 
            name="input_rounds_percent" value="50%">
            
        </div>
                    </div>
                    </div>
                    </div>
                    <!-- Switch Toggle Button -->
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="input_users_status" name="input_users_status" checked>
                        <label class="custom-control-label" for="input_users_status">مفعلة</label>
                    </div>

            </div>
            <div class="col-md-6 text-center">
                <h2>إضافة شعار للإنتخابات</h2>
                <div class="dropdown-divider"></div>
                <label for="profile_picture" class="btn btn-secondary btn-sm" style="font-size: 18px;">
                    <i class="fas fa-upload"></i> تحميل صورة
                    <input type="file" id="profile_picture" name="profile_picture" style="display:none;" accept="image/*" onchange="previewImage(this)" />
                </label>
                <p class="mt-2">الصيغ المدعومة: JPG، PNG، GIF</p>

                <!-- Picture Preview -->
                <div id="image-preview" class="mt-3"></div>
            </div>
            <div class="container mt-4">
            <div class="row">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <input type="text" id="sharedSearch" class="form-control" placeholder="بحث في اللوائح">
                            <div class="input-group-prepend" data-toggle="modal" data-target="#exampleModal">
                                <span class="input-group-text btn-primary rounded-pill mr-2"><i class="fa fa-plus"></i></span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <!-- First Panel -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header text-center fw-bold" style="font-size:18px;">لوائح الإختيار</div>
                            <div class="card-body p-0">
                                <table id="dataTable1" class="table table-striped table-bordered table-sm mb-0">
                                    <thead>
                                        <tr class="text-center" style="font-weight:bold;">
                                            <th></th>
                                            <th>اسم اللائحة</th>
                                            <th>معلومات</th>
                                            <th style="display:none;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if($ListMasters)
                                        @foreach ($ListMasters as $listmaster)
                                        <tr class="text-center">
                                            <td><input type="checkbox" class="filled-in chk-col-red chk-md" onclick="moveChooseUserToChoosen(this)"></td>
                                            <td>{{ $listmaster->list_name }}</td>
                                            <td>{{ $listmaster->list_info }}</td>
                                            <td style="display:none;">{{ $listmaster->list_code }}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Second Panel -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header text-center fw-bold" style="font-size:18px;">اللوائح المختارة</div>
                            <div class="card-body p-0">
                                <table id="dataTable2" class="table table-striped table-bordered table-sm mb-0">
                                    <thead>
                                        <tr class="text-center" style="font-weight:bold;">
                                            <th>اسم اللائحة</th>
                                            <th>معلومات</th>
                                            <th style="display:none;"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">حفظ المعلومات</button>
            <!-- Clear Button -->
            <button type="button" class="btn btn-secondary ml-2" onclick="clearForm()">مسح</button>
            </form>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
        <div class="modal-dialog" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">إضافة لائحة</h5>
                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Input field inside the modal -->
                    <div class="form-group text-right">
                        <label for="inputText" class="text-right">اسم اللائحة</label>
                        <input type="text" id="inputText" class="form-control" autofocus required>
                    </div>
                    <div class="form-group text-right">
                        <label for="address">معلومات عن اللائحة</label>
                        <textarea class="form-control" id="input_address" name="input_address"></textarea>
                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Get the current date
        var currentDate = new Date();

        // Format the date as "YYYY-MM-DD" (required by the date input)
        var formattedDate = currentDate.toISOString().split('T')[0];

        // Set the default date for the input
        $('#input_age').val(formattedDate);

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
                img.style.maxWidth = '200px'; // Set the maximum width to 100px
                preview.appendChild(img);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearForm() {

        var form = document.getElementById('usermanagerform');
        form.reset();
        document.getElementById('image-preview').innerHTML = '';
        var currentDate = new Date();

        // Format the date as "YYYY-MM-DD" (required by the date input)
        var formattedDate = currentDate.toISOString().split('T')[0];

        // Set the default date for the input
        $('#input_age').val(formattedDate);
        $('#input_fullname').focus();
    }

    function moveChooseUserToChoosen(clickedElement) {
                var name = clickedElement.closest('tr').cells[1].textContent;
                var mobile = clickedElement.closest('tr').cells[2].textContent;
                var profilecode = clickedElement.closest('tr').cells[3].textContent;
            var rownumber = clickedElement.closest('tr').rowIndex;;
            var isChecked = $(clickedElement).prop('checked');
            if (isChecked == true) {
                $('#dataTable2').append(
                    '<tr id=' + rownumber + '><th>'+name+'</th><th>'+mobile+'</th><th style="display:none;">'+profilecode+'</th></tr>'
                );
            } else {
                $('#' + rownumber + '').remove();
            }

        }
</script>

@endsection
