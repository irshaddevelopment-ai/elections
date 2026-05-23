@extends('layouts.app')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>

    <style>
        /* Default style for the <i> element */
        i {
            cursor: default;
            /* Set default cursor style */
        }

        /* Style for the <i> element when hovered */
        i:hover {
            cursor: pointer;
            /* Change cursor to hand on hover */
        }
        body {
            font-size: 18px;
            /* Set your desired default font size */
        }
    </style>
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="text-align:center;">إضافة اسم</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        
                        <form id="user_info_form" action="/saveuserinfo" method="post">
                        @csrf
                        <input type="hidden" id="input_profile_code" name="input_profile_code" class="form-control"
                            autocomplete="off" value="">
                        <!-- First Text Field -->
                        <div class="md-form">
                            <label for="field1">الاسم*</label>
                            <input type="text" id="input_fullname" name="input_fullname" class="form-control"
                            autofocus autocomplete="off" required
                oninvalid="this.setCustomValidity('أدخل الاسم')"
                oninput="this.setCustomValidity('')">
                        </div>


                        <!-- Date Field -->
                        <div class="md-form">
                            <label for="dateField">العمر</label>
                            <input type="date" id="input_age" name="input_age" class="form-control">

                        </div>
                        <div class="md-form">
                            <label for="dateField">الهاتف*</label>
                            <input type="text" id="input_tel" name="input_tel" 
                            class="form-control"
                            autofocus autocomplete="off" required
                oninvalid="this.setCustomValidity('أدخل رقم الهاتف')"
                oninput="this.setCustomValidity('')">

                        </div>
                        <div class="md-form">
                            <label for="dateField">العنوان</label>
                            <textarea class="form-control" id="input_address" 
                            name="input_address"></textarea>

                        </div>
                        <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="input_users_status"
                                    name="input_users_status" >
                                <label class="form-check-label" for="flexSwitchCheckChecked">مسؤول</label>
                            </div>

                        <!-- Submit Button -->
                        <div style="padding-top: 50px;align-items: center;">
                            <button type="submit" class="btn btn-primary  btn-lg ms-2">حفظ المعلومات</button>
                            <button type="button" class="btn btn-danger btn-lg"
                                onclick="clearForm('user_info_form')">مسح</button>
                        </div>

                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <script>
       @if ($Profiles)
          $("#input_profile_code").val('{{$Profiles->profile_code}}');
          $("#input_fullname").val('{{$Profiles->full_name}}');
          $("#input_age").val('{{$Profiles->age}}');
          $("#input_tel").val('{{$Profiles->mobile}}');
          $("#input_address").val('{{$Profiles->address}}');
       @endif
        var currentDate = new Date().toISOString().split('T')[0];

// Set the default value for the input date
$('#input_age').val(currentDate);
        </script>
    <!-- modals-->

@endsection
