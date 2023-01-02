@extends('layouts.master')
@section('title')
    Excluded Email/Domain  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')

    <div class="modal fade" id="addExcluded" tabindex="-1" role="dialog" aria-labelledby="addExcludedLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExcludedLabel">Add Email / Domain to Exclude</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addEmailForm" >
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('email','Email/Domain') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::text('email',null,['class'=>'form-control','id'=>'email','data-live-search'=>'true','required','placeholder'=>'Email/Domain']) }}
                                </div>
                                <span class="text-danger">
                                            <strong id="email-error">
                                            </strong>
                                            </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="submitBtn" class="btn btn-sm btn-success">Save</a>
                </div>
            </div>
        </div>
    </div>


    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>Excluded Email / Domain List</h2>
                            </div>
                            <div class="">
                                <a href="#addExcluded" class="btn btn-xs btn-success" data-toggle="modal" data-target="#addExcluded" id="addEmailDomain" title="Add More Email / Domain to Exclude " ><i class="fa fa-plus" style="font-size: 1.5em"></i> Add Email/Domain</a>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>

                            <div class="body">
                                <table class="table table-bordered table-hover table-striped" id="tbl" width="100%">
                                    <thead class="bg-teal">
                                    <tr>
                                        <th class="align-center">#</th>
                                        <th class="align-center">Email / Domain </th>
                                        <th class="align-center">Reason</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $key=>$contact)
                                        @if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $contact->email))
                                        <tr class="tr align-center">
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $contact->email }} <p class="badge badge-info badge-inverse">Email</p> </td>
                                            <td>{{ $contact->reason }}</td>

                                        </tr>
                                        @else
                                            <tr class="tr align-center">
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $contact->email }} <p class="badge badge-info badge-inverse">Domain</p> </td>
                                                <td>{{ $contact->reason }}</td>

                                            </tr>
                                            @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $('#tbl').DataTable({
            "pageLength": 15,
        })
        $('#submitBtn').on('click',function (e) {

            e.preventDefault();
            $.ajax({
                url:'addemail',
                type:'POST',
                data:{
                    _token:'{{csrf_token()}}',
                    email:$('#email').val()
                },success:function (d) {
                  if(d==='success'){
                      swal({
                          title:'Success',
                          text:'Email/domain has been added to excluded list',
                          type:'success',
                          confirmButtonColor: '#63c6b4',
                          confirmButtonText: 'OK',
                      },function (isconfirm) {
                          if(isconfirm){
                              window.location.reload()
                          }
                      })
                     // $('#addExcluded').modal('hide')
                      //
                  }else if(d.errors){
                    swal({
                        title:'Error',
                        text:d.errors.email[0],
                        type:'error',
                        confirmButtonColor:'#63c6b4',
                        confirmButtonText:'OK'
                    },function (isconfirm) {
                        if (isconfirm){
                            $('#addExcluded').modal('hide')
                        }
                    })
                  }
                }
            })
        })
        $(function () {
            $('[data-toggle="popover"]').popover()
        })

    </script>
@endsection
