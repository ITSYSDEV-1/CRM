@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel title">
                            <div class="x_title">
                                <h3>Booking Confirmation</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="form-inline">
                                    {{ Form::model($confirm,['route'=>['confirm.update',$confirm->id],'files'=>'true','id'=>'templateForm']) }}
                                    <div class="form-group">

                                        <div class="col-md-12">
                                            <div class="pull-right">
                                                <div class="switch">
                                                    <label>
                                                        Deactivate
                                                        <input type="checkbox" class="js-switch" name="activate" id="onOff" >
                                                        <span class="lever"></span>
                                                        Activate
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Number of days before birthday that the campaign is sent :</label>
                                                    {{ Form::select('sendafter',[''=>'Day/s before']+range(0,31,1),$confirm->sendafter,['class'=>'form-control selectpicker']) }}
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div id="templateSelection">
                                                    <label class="control-label">Select Email Template</label>
                                                    {{ Form::select('template',[''=>'Select Template']+\App\MailEditor::pluck('name','id')->all(),$confirm->template_id,['id'=>'templateChose','class'=>'form-control selectpicker','onchange'=>'selectTemplate(this.value)']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="editorInput col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    {{ Form::textarea('contents',$confirm->template->content,['class'=>'form-control','id'=>'summernote']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-lg-12 ">
                                                <div class="col-lg-6">
                                                    <a class="btn btn-success btn-sm" href="{{ url('email/template') }}" title="Back"><i class="fa fa-arrow-circle-o-left"></i> Back</a>
                                                    <button class="btn btn-success btn-sm" type="submit"> <i class="fa fa-save"></i> Save</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
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
        var mySwitch = new Switchery($('#onOff')[0], {
            size:"small",
            color: '#0D74E9'
        });
        //Checks the switch
        var status='{{ $confirm->active }}';
        if(status==='Y'){
            setSwitchery(mySwitch, true);
        } else {
            setSwitchery(mySwitch, false);
        }

        $('#onOff').on('click',function () {
            if (mySwitch.isChecked()){
                var state='on';
            }else {
                state='off';
            }
            $.ajax({
                url:'confirm/activate',
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
                    $('#summernote').summernote('editor.pasteHTML',data['content']);
                }
            })
        }
    </script>
@endsection