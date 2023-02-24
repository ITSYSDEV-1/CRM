@extends('layouts.master')
@section('title')
    Prestay Promo Configuration  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel title">
                            <div class="x_title">
                                <h3>Prestay Promo Configuration</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                                <a href="{{route('promo-configuration.create')}}" title="Create New Template" class=" btn btn-sm btn-success"> <i class="fa fa-plus"></i> Create New</a>
                            </div>
                            <div class="x_content">
                                <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%">
                                    <thead>
                                    <th width="10px">No</th>
                                    <th>Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Event Picture</th>
                                    <th>Event Text</th>
                                    <th>Event Url</th>
                                    <th>Manage</th>
                                    </thead>
                                    <tbody>
                                    @foreach($promotempl as $key=>$tem)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td> {{ $tem->name }} </td>
                                            <td>{{ $tem->duration_start }}</td>
                                            <td>{{ $tem->duration_end }}</td>
                                            <td>{{ $tem->event_picture }}</td>
                                            <td>{{ $tem->event_text }}</td>
                                            <td>{{ $tem->event_url }}</td>
                                            <td>{!! Form::open(['method' => 'DELETE','route' => ['promo-configuration.destroy', $tem->id],'id'=>'form'.$tem->id]) !!}
                                                {!! Form::close() !!}
                                                <a href="#" title="Delete Event" onclick="
                                                    return swal({title:'Delete Confirmation',text:'This Event will permanently deleted',type:'warning',                                                        showCancelButton: true,
                                                    confirmButtonColor: '#DD6B55',
                                                    confirmButtonText:'Delete',
                                                    cancelButtonText: 'No',
                                                    closeOnConfirm: false,
                                                    closeOnCancel: false
                                                    },
                                                    function(isConfirm){
                                                    if (isConfirm) {
                                                    {{--$('#form{{$tem->id}}').submit();--}}
                                                    $.ajax({
                                                    url:'{{ url("prestay/promo-configuration/destroy") }}',
                                                    type:'POST',
                                                    data:{
                                                    _token:'{{ csrf_token() }}',
                                                    id:'{{ $tem->id }}',
                                                    },success:function(data) {
                                                    if(data.status==='error'){
                                                    var tmpl='{{$tem->promoprestaycontact }}'
                                                    tmpl=JSON.parse(tmpl);
                                                    swal('Delete Failed', 'This Event is being used','error')
                                                    } else {
                                                    swal({title: 'Success', text: 'Event deleted', type: 'success'},
                                                    function(){
                                                    location.reload();
                                                    }
                                                    );
                                                    }
                                                    }
                                                    })
                                                    } else {
                                                    swal('Cancelled', 'Delete Event Cancelled','error');
                                                    }
                                                    });"><i class="fa  fa-trash" style="font-size: 1.5em"></i> </a>
                                                <a href="{{ route('promo-configuration.edit',$tem->id) }}" title="Edit"><i class="fa  fa-edit" style="font-size: 1.5em"></i> </a>
                                                <a href="#myModal{{$tem->id}}" data-toggle="modal" data-target="#myModal{{$tem->id}}"><i class="fa  fa-eye" style="font-size: 1.5em"></i></a>
                                            </td>
                                        </tr>
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
    @foreach($promotempl as $item)
        <div  class="modal " id="myModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{ $item->name }}</h4>
                    </div>
                    <div class="modal-body">
                        {!! $item->content !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="testEmail{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="testEmailLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="testEmailLabel{{ $item->id }}">Send Email Test</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="emailTest{{ $item->id }}" >
                            <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                                {{ Form::label('email','Email Address') }}
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <div class="form-line">
                                        {{ Form::email('email',null,['class'=>'form-control','id'=>'email','data-live-search'=>'true','required','placeholder'=>'Email Address']) }}
                                    </div>
                                    <span class="text-danger">
                                            <strong id="email-error">
                                            </strong>
                                            </span>
                                </div>
                            </div>
                            <input type="hidden" name="template_id" id="template_id{{$item->id}}" value="{{$item->id}}">
                            <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                                {{ Form::label('','Preview:') }}
                            </div>
                            {!! $item->content !!}
                            <div class="previewtemplate">

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                        <a href="#" id="{{ $item->id }}" class="btn btn-sm btn-success" onclick="return sendEmailTest(this.id)">Send</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
@section('script')
    <script>
        function sendEmailTest(id) {
            var form =$('#emailTest'+id);
            var email=form.find('#email');
            $.ajax({
                url:'sendtest',
                type:'post',
                data:{
                    id:id,
                    email:email.val(),
                    _token:'{{ csrf_token() }}'
                },success:function (data) {
                    if(data==='success'){
                        swal({
                            title:"Success",
                            text:"Email Sent",
                            type:"success",
                        },function () {
                            window.location.reload();
                        })
                    }else {
                        if(data.errors.email){
                            swal('', data.errors.email[0], 'warning')
                        }
                    }
                },
                // error: function (error){
                //     console.log(error.responseJSON,error.responseText)
                // }
            })
        }
        $('.dataTable').dataTable();
    </script>
@endsection
