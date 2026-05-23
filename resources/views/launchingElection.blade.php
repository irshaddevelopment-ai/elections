@extends('layouts.app')

@section('content')
<script
  src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script>

<style>
    body {
            font-size: 18px;
            /* Set your desired default font size */
        }
        /* Default style for the <i> element */
        i {
            cursor: default; /* Set default cursor style */
        }

        /* Style for the <i> element when hovered */
        i:hover {
            cursor: pointer; /* Change cursor to hand on hover */
        }
    </style>
<div class="container" dir="rtl">
<div id="progress" style="display: none;">
    <progress id="loading-progress" value="0" max="100"></progress>
    <span id="progress-text">0%</span>
</div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="text-align:center;">لائحة العمليات الانتخابية</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row mt-1">
        <div class="col-md-12 mx-auto">
            <div class="input-group">
                <input class="form-control border rounded-pill" type="search" placeholder="بحث" id="search_input">
               
            </div>
        </div>
    </div>
    <form id="edit_form" action="/edituser" method="post">
    <table id="users_table" class="table table-striped nowrap" style="width:100%">
        <thead>
            <tr>
                <th>اسم العملية الانتخابية</th>
                <th>التاريخ</th>
                <th>نوع العملية</th>
                <th></th>
                <th></th>
                
            </tr>
        </thead>
        <tbody>
        @if ($elections)
        @foreach($elections as $elections_var)
            <tr>
                <td>{{$elections_var->election_name}}</td>
                <td>{{$elections_var->election_date}}</td>
                <td></td>
                <td></td>
                <td>
                <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="input_election_status"
                                    name="input_election_status">
                              
                            </div>
                </td>
                
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="deleteusermodal" 
tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">هل تريد حذف هذا الاسم</h5>
       
      </div>
      <form id="modal_delete_form" action="/deleteuser" method="post">
        @csrf
      <input type="text" id="modal_profile_code" name="modal_profile_code"
                                    class="form-control" autocomplete="off" hidden>
      <div class="modal-footer" >
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">الغاء</button>
        <button type="submit" class="btn btn-success">نعم</button>
      </div>
    </form>
    </div>
  </div>
</div>
<!-- modals-->

@endsection
