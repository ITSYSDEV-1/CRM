@extends('layouts.master')
@section('title')
    Template Management  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel title">
                            <div class="x_title">
                                <h3>Email Template Management</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                                @can('6.1.7_view_list_template')
                                @can('6.1.1_create_new_template') 
                                <a href="{{ url('email/create') }}" title="Create New Template" class=" btn btn-sm btn-success"> <i class="fa fa-plus"></i> Create New Template</a>
                                @endcan
                                @endcan
                            </div>
                            @can('6.1.7_view_list_template')
                            <div class="x_content">
                                <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%">
                                    <thead>
                                    <th width="10px">No</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th >Manage</th>
                                    </thead>
                                    <tbody>
                                    @foreach($template as $key=>$tem)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td> {{ $tem->name }} </td>
                                            <td>{{ $tem->type }}</td>
                                            <td>{!! Form::open(['method' => 'DELETE','route' => ['email.destroy', $tem->id],'id'=>'form'.$tem->id]) !!}
                                                {!! Form::close() !!}
                                                @can('6.1.2_delete_template')
                                                <a href="#" title="Delete Template" onclick="
                                                        return swal({title:'Delete Confirmation',text:'This Template will permanently deleted',type:'warning',                                                        showCancelButton: true,
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
                                                                url:'{{ url("template/destroy") }}',
                                                                type:'POST',
                                                                data:{
                                                                _token:'{{ csrf_token() }}',
                                                                id:'{{ $tem->id }}',

                                                        },success:function(data) {
                                                        if(data.status==='error'){
                                                        var tmpl='{{$tem->campaign }}'
                                                        tmpl=JSON.parse(tmpl);
                                                        swal('Delete Failed', 'This template is being used in campaign or scheduled email, please delete the campaign/schedule first','error')
                                                        } else {
                                                        swal({title: 'Success', text: 'Template deleted', type: 'success'},
                                                        function(){
                                                            location.reload();
                                                             }
                                                         );
                                                         }
                                                            }
                                                            })
                                                        } else {
                                                        swal('Cancelled', 'Delete Template Cancelled','error');
                                                        }
                                                        });"><i class="fa  fa-trash" style="font-size: 1.5em"></i> </a>
                                                @endcan
                                                @can('6.1.3_edit_template')
                                                <a href="{{ url('email/'.$tem->id.'/edit') }}" title="Edit"><i class="fa  fa-edit" style="font-size: 1.5em"></i> </a>
                                                @endcan
                                                @can('6.1.4_view_template')
                                                <a href="#myModal{{$tem->id}}" data-toggle="modal" data-target="#myModal{{$tem->id}}"><i class="fa  fa-eye" style="font-size: 1.5em"></i> </a>
                                                @endcan
                                                @can('6.1.5_duplicate_template')
                                                <a href="#cloneTemplate{{$tem->id}}" data-toggle="modal" data-target="#cloneTemplate{{$tem->id}}" id="copyTempalte" title="Copy Template" ><i class="fa fa-copy" style="font-size: 1.5em"></i> </a>
                                                @endcan
                                                @can('6.1.6_test_send_email')
                                                <a href="#testEmail{{ $tem->id }}" data-toggle="modal" data-target="#testEmail{{ $tem->id }}" id="testSendEmail" title="Send Email Test"> <i class="fa fa-send"></i>   </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @can('6.1.4_view_template')
    @foreach($template as $item)
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
    @endforeach
    @endcan

    @can('6.1.5_duplicate_template')
    @foreach($template as $item)
        <div class="modal fade" id="cloneTemplate{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="cloneTemplateLable" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cloneTemplateLable{{ $item->id }}">Clone Template</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="templateClone{{ $item->id }}" >
                            <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                                {{ Form::label('tname','New Template Name') }}
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <div class="form-line">
                                        {{ Form::text('tname',null,['class'=>'form-control','id'=>'tname','data-live-search'=>'true','required','placeholder'=>'New Template Name']) }}
                                    </div>
                                    <span class="text-danger">
                                            <strong id="tname-error">
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
                        <a href="#" id="{{ $item->id }}" class="btn btn-sm btn-success" onclick="return submitClone(this.id)">Save</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @endcan

    @can('6.1.6_test_send_email')
    @foreach($template as $item)
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
    @endcan

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
        function submitClone(id) {

            var form=$('#templateClone'+id)
            var name=form.find('#tname');
            $.ajax({
                url:'saveclone',
                type:'post',
                data:{
                    tid:id,
                    name:name.val(),
                    _token:'{{ csrf_token() }}'
                },success:function (data) {
                    if(data==='success'){
                        swal({
                            title: "Sucess",
                            text: "Template Cloned",
                            type: "success",
                        },function() {
                            window.location.reload();
                        });
                    }else {
                        if(data.errors.name){
                            swal('', data.errors.name[0], 'warning')
                        }
                    }
                }
            })
        }
        $('.dataTable').dataTable();
    </script>
@endsection
