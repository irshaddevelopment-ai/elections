@extends('layouts.app')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
   

    <style>
        body {
            font-size: 18px;
            /* Set your desired default font size */
        }

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

        .tableFixHead {
            overflow-y: auto;
            /* make the table scrollable if height is more than 200 px  */
            height: 400px;
            /* gives an initial height of 200px to the table */
        }

        .tableFixHead thead th {
            position: sticky;
            /* make the table heads sticky */
            top: 0px;
            /* table head will be placed from the top of the table and sticks to it */
        }

        table {
            border-collapse: collapse;
            /* make the table borders collapse to each other */
            width: 100%;
        }

        th,
        td {
            padding: 8px 16px;
            border: 1px solid #ccc;
        }

        th {
            background: #eee;
        }

        .tableFixHead thead th {
            background: #DA5A5A;
            color: white;
        }
    </style>
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card d-grid gap-3">
                    <div class="card-header p-2" style="text-align:center;">إضافة مجموعة</div>

                    <div class="card-body ">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif


                        <form id="user_info_form" action="/savegroups" method="post">
                            @csrf
                            <!-- {{ csrf_field() }} -->
                            <input type="hidden" id="input_election_code" name="input_election_code" class="form-control"
                                autocomplete="off" value="">
                            <!-- First Text Field -->
                            <div class="md-form">
                            <label for="dateField">اسم المجموعة</label>
                            <input type="text" id="input_group" name="input_group" class="form-control">
                            <label for="dateField">اسم العملية الانتخابية</label>
                            <input type="hidden" id="input_election_code" name="input_election_code" class="form-control">
                            <input type="text" id="input_election" name="input_election" class="form-control" oninput="fetchData(this)"  autocomplete="off">
                            <div class="list-group" id="users_chooser">
  
                             </div>
                        </div>
       
                            <div class="container p-2">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <div class="md-form  tableFixHead">

                                            <table id="choose_table" class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10px;"></th>
                                                        <th>لائحة الاختيار</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="choose_users">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="md-form tableFixHead">
                                            <table id="choosen_table" class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr style="width: 10px;">
                                                        <th>اللائحة المختارة</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="choosen_users">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
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

    <!-- modals-->
    <script>
       

       function moveChooseUserToChoosen(clickedElement) {
            var checkbox_id = $(clickedElement).attr('id');
            var rownumber = checkbox_id.split('_')[2];
            var row_selected = $('#choose_table tbody tr:eq(' + rownumber + ')');
            var isChecked = $(clickedElement).prop('checked');
            var profilecode = row_selected.find('th:eq(1)').text();
            var nameValue = row_selected.find('th:eq(2)').text();
            if (isChecked == true) {
                $('#choosen_users').append(
                    '<tr id=' + rownumber + '><th><input type="hidden" name="choosen_profile_id[]" value="' + profilecode + '"/>' + nameValue + '</th></tr>'
                );
            } else {
                $('#' + rownumber + '').remove();
            }

        }
        // Set the CSRF token as a default header for Axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');

            fetch('/getUsers')
    .then(response => response.json())
    .then(data => {
        
        var result_data = data;
                $('#choose_users').empty();
                for (let i = 0; i < result_data.length; i++) {
                    $('#choose_users').append(
                        '<tr><th><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="choose_checkbox_' +
                        i + '" onclick="moveChooseUserToChoosen(this)"></div></th><th style="display: none;">' +
                        result_data[i].profile_code + '</th><th>' +
                        result_data[i].full_name + '</th></tr>'
                    );
                }
             
}).catch(err=>console.log(err))

        

function fetchData(sender_var) {
        var inputValue = $(sender_var).val();
        // Your data to be sent
const dataToSend = {
    election_name: inputValue,
};

// Convert data to URL parameters
const params = new URLSearchParams(dataToSend).toString();
           axios.get('/getelections?'+params)
            .then(response => {
                var result_data = response.data;
                $('#users_chooser').empty();
                for (let i = 0; i < result_data.length; i++) {
                    $('#users_chooser').append(
                        '<a href="#" class="list-group-item list-group-item-action list-group-item-light"  id="'+result_data[i].election_code+'" onclick="appendinput(this)">'+
                        result_data[i].election_name+'</a>'
                    );
                }

            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }
       function appendinput(sender_var){
        var inputValue = $(sender_var).text();
        $('#input_election').val(inputValue);
        $('#input_election_code').val($(sender_var).attr('id'));
        $('#users_chooser').empty();
       }
    </script>
@endsection
