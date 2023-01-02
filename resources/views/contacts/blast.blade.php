@extends('layouts.master')
@section('title')
    Email Blast | {{ \App\Configuration::first()->hotel_name.' '.\App\Configuration::first()->app_title }}
@endsection
@section('content')

    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h1>Email Blast</h1>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="body">
                                <form id="saveBlast" action="saveemailblast" method="post" enctype="multipart/form-data">
                                    {!! Form::token() !!}
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                        {{ Form::label('file','Import Email Address') }}
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                                        <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                                            {{ Form::file('file') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                        {{ Form::label('template','Template') }}
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                                        <div class="form-group {{ $errors->has('template') ? 'has-error' : '' }}">
                                            {{ Form::select('template',[''=>'Select Template']+\App\MailEditor::where('type','=','Promo')->pluck('name','id')->all(),null,['class'=>'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('campaign_name','Campaign Name') }}
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                                            <div class="form-group {{ $errors->has('campaign_name') ? 'has-error' : '' }}">
                                                {{ Form::text('campaign_name',null,['class'=>'form-control','placeholder'=>'Campaign name']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('schedule','Schedule') }}
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                                            <div class="form-group {{ $errors->has('schedule') ? 'has-error' : '' }}">
                                                {{ Form::text('schedule',null,['class'=>'datepicker form-control','id'=>'schedule','placeholder'=>'Schedule' ,'required']) }}
                                            </div>
                                        </div>
                                    </div>
                                <button class="btn btn-sm btn-primary" id="saveEmailBlast" >Upload</button>
                                </form>
                            </div>
                        </div>
                        <hr>
                        <div class="card">
                            <div class="header">
                                <h1>Email Blast List</h1>
                            </div>
                            <div class="body">
                                <table class="table table-bordered table-striped table-hover  responsive " id="datatable-responsive" width="100%">
                                    <thead>
                                    <th>No</th>
                                    <th>Campaign Name</th>
                                    <th>Template</th>
                                    <th>Schedule</th>
                                    <th>Status</th>
                                    <th>Recipient</th>
                                    <th>Action</th>
                                    </thead>
                                    <tbody>
                                    @foreach(\App\Blast::all() as $key=>$blast)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $blast->campaign_name }}</td>
                                            <td><a href="#myModal{{$blast->id}}" data-toggle="modal" data-target="#myModal{{$blast->id}}">{{ \App\MailEditor::find($blast->template_id)->name }}</a></td>
                                            <td>{{ \Carbon\Carbon::parse($blast->schedule)->format('d F Y H:i') }}</td>
                                            <td>{{ ucfirst($blast->status) }}</td>
                                            <td><a href="#recepientModal{{$blast->id}}" data-toggle="modal" data-target="#recepientModal{{$blast->id}}"><i class="fa fa-users"></i> </a></td>
                                            <td><a href="#" title="Delete Email Blast List" onclick="
                                                        return swal({title:'Delete Confirmation',text:'This list will permanently deleted',type:'warning',                                                        showCancelButton: true,
                                                        confirmButtonColor: '#DD6B55',
                                                        confirmButtonText:'Delete',
                                                        cancelButtonText: 'No',
                                                        closeOnConfirm: false,
                                                        closeOnCancel: false
                                                        },
                                                        function(isConfirm){
                                                        if (isConfirm) {
                                                        $.ajax({
                                                        url:'{{ url("blast/destroy") }}',
                                                        type:'POST',
                                                        data:{
                                                        _token:'{{ csrf_token() }}',
                                                        id:'{{ $blast->id }}',

                                                        },success:function(data) {
                                                        if(data.status==='error'){
                                                        swal('Delete Failed', ' ','error')
                                                        } else {
                                                        swal({title: 'Success', text: 'Email blast list deleted', type: 'success'},
                                                        function(){
                                                        location.reload();
                                                        }
                                                        );
                                                        }
                                                        }
                                                        })
                                                        } else {
                                                        swal('Cancelled', 'Delete email blast list cancelled','error');
                                                        }
                                                        });"><i class="fa  fa-trash" style="font-size: 1.5em"></i> </a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach(\App\Blast::all() as $key=>$blast)
                    <div  class="modal " id="myModal{{ $blast->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" >
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">{{ $blast->campaign_name }}</h4>
                                </div>
                                <div class="modal-body">
                                    {!! \App\MailEditor::find($blast->template_id)->content !!}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class="modal " id="recepientModal{{ $blast->id }}" tabindex="-1" role="dialog" aria-labelledby="recepientModalLabel">
                        <div class="modal-dialog  modal-lg " role="document">
                            <div class="modal-content" >
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="recepientModalLabel"> Campaign {{ $blast->campaign_name }} </h4>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$blast->id}}">
                                        <thead>
                                        <tr>
                                            <th width="20%"> No </th>
                                            <th width="60%"> Name </th>
                                            <th width="20%"> Email </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($blast->email as $key=>$email)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $email->fname.' '.$email->lname }}</td>
                                                <td>{{ $email->email }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
            </div>
        </section>
    </div>
@endsection
@section('script')
<script>
    $('#schedule').datetimepicker({
        format: 'DD MMMM YYYY hh:mm',
        minDate: new Date(),
        showClear:true,

    })

    function delElement() {
        $('form input, form select').removeClass('error');
        $('span.text-danger').remove();
    }
    $(document).ready(function(){
        //based on: http://stackoverflow.com/a/9622978
        $('#saveBlast').on('submit', function(e){
            e.preventDefault();
            delElement()
            var form = e.target;
            var data = new FormData(form);
            $.ajax({
                url: form.action,
                method: form.method,
                processData: false,
                contentType: false,
                data: data,
                success: function(data){
                    if (data.errors){
                        $.each(data.errors,function(i,v){
                            var msg = '<span class="text-danger" id="'+i+'">'+v+'</span>';
                            $('input[name="' + i + '"], select[name="' + i + '"]').addClass('error').after(msg);
                        })
                    } else {
                        swal({title:'Success',text:'Email blast has been added',type:'success'},function () {
                            location.reload()
                        })
                    }
                }
            })
        })
    })
</script>
@endsection
