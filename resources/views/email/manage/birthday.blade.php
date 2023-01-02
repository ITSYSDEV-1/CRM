@extends('layouts.master')
@section('title')
    Birthday Configuration  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel title">
                            <div class="x_title">
                                <h3>Birthday Configuration</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="form-inline">
                                    @if($birthday)
                                        {{ Form::model($birthday,['route'=>['birthday.update',$birthday->id],'files'=>'true','id'=>'templateForm']) }}
                                        <div class="form-group">

                                            <div class="col-md-12">
                                                <div class="pull-right">
                                                    <div class="switch">
                                                        <label>
                                                            Deactivate
                                                            <input type="checkbox" class="js-switch" name="activate" id="myonoffswitch" >
                                                            <span class="lever"></span>
                                                            Activate
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            Send before / after birthday</label>
                                                        {{ Form::select('sendafter',[''=>'Day/s before']+[-2=>-2,-1=>-1,0=>0,1=>1,2=>2],$birthday->sendafter,['class'=>'form-control selectpicker']) }}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div id="templateSelection">
                                                        <label class="control-label">Select Email Template</label>
                                                        {{ Form::select('template',[''=>'Select Template']+\App\Models\MailEditor::where('type','Birthday')->pluck('name','id')->all(),$birthday->template_id,['id'=>'templateChose','class'=>'form-control selectpicker','onchange'=>'selectTemplate(this.value)']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <br>
                                        <div class="well" id="templatepreview">
                                            {!! $birthday->template->content !!}
                                        </div>
                                        <div class="form-group">
                                            <div class="row clearfix">
                                                <div class="col-lg-12 ">
                                                    <a class="btn btn-success btn-sm" href="{{ url('email/template') }}" title="Back"><i class="fa fa-arrow-circle-o-left"></i> Back</a>
                                                    <button class="btn btn-success btn-sm" type="submit"> <i class="fa fa-save"></i> Save</button>
                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    @else
                                        <a href="{{  url('campaign/template') }}"> Create Template </a>
                                    @endif
                                </div>
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
        function setSwitchery(switchElement, checkedBool) {
            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
                switchElement.setPosition(true);
                switchElement.handleOnchange(true);
            }
        }
        var mySwitch = new Switchery($('#myonoffswitch')[0], {
            size:"small",
            color: '#0D74E9'
        });
        //Checks the switch
        var status='{{ $birthday->active }}';
        if(status==='y'){
            setSwitchery(mySwitch, true);
        } else {
            setSwitchery(mySwitch, false);
        }

        $('#myonoffswitch').on('click',function () {
            if (mySwitch.isChecked()){
                var state='on';
            }else {
                state='off';
            }
            $.ajax({
                url:'birthday/activate',
                type:'post',
                data:{
                    state:state,
                    _token:'{{ csrf_token() }}',
                },
                success:function (data) {
                    if(data['active']==true){
                        setSwitchery(mySwitch, true);
                    }else {
                        setSwitchery(mySwitch, false);
                    }
                }

            })
        })
        //Unchecks the switch
    </script>
    <script>
        $('#summernote').summernote({
            //height:350,
            popover: {
                image: [
                    ['custom', ['imageAttributes']],
                    ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ],

            },
            imageAttributes:{
                icon:'<i class="note-icon-pencil"/>',
                removeEmpty:false, // true = remove attributes | false = leave empty if present
                disableUpload: false // true = don't display Upload Options | Display Upload Options
            },
        });

    </script>
    <script>

        function selectTemplate(id) {
            $.ajax({
                url:'template',
                type:'post',
                data:{
                    id:id,
                    _token:'{{ csrf_token() }}'
                },
                success:function (data) {
                    $('#summernote').summernote('code', '');
                    $('#templatepreview').empty();
                    $('#templatepreview').append(data['content']);
                }
            })
        }
    </script>
@endsection
