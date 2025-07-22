esources\views\email\manageirthday.blade.php
<div class="x_content">
    <div class="form-inline">
        @if($birthday)
            {{ Form::model($birthday,['route'=>['birthday.update',$birthday->id],'files'=>'true','id'=>'templateForm']) }}
            
            <div class="row">
                <div class="form-group">
                    @can('5.3.1_view_email_birthday')
                    @can('5.3.2_active_deactive_birthday')
                    <div class="col-lg-12 col-md-12">
                        <div class="switch">
                            <label>
                                Deactivate
                                <input type="checkbox" class="js-switch" name="activate" id="myonoffswitch" >
                                <span class="lever"></span>
                                Activate
                            </label>
                        </div>
                    </div>
                    @endcan
                    @endcan
                    
                    @can('5.3.1_view_email_birthday')
                    @can('5.3.3_set_time_email_birthday')
                    <div class="form-group">
                    <div class="col-lg-12 col-md-12">
                            <label class="control-label">
                                Send before / after birthday</label>
                            {{ Form::select('sendafter',[''=>'Day/s before']+[-2=>-2,-1=>-1,0=>0,1=>1,2=>2],$birthday->sendafter,['class'=>'form-control selectpicker']) }}
                        </div>
                    </div>
                    @endcan
                    @endcan
                    
                    @can('5.3.1_view_email_birthday')
                    @can('5.3.4_select_email_template_birthday')
                    <div class="form-group">
                    <div class="col-lg-12 col-md-12">
                            <label class="control-label">Select Email Template</label>
                            {{ Form::select('template',[''=>'Select Template']+\App\Models\MailEditor::where('type','Birthday')->pluck('name','id')->all(),$birthday->template_id,['id'=>'templateChose','class'=>'form-control selectpicker','onchange'=>'selectTemplate(this.value)']) }}
                        </div>
                    </div>
                    @endcan
                    @endcan
                </div>
            </div>
            <br>
            <br>
            @can('5.3.1_view_email_birthday')
            <div class="well" id="templatepreview">
                {!! $birthday->template->content !!}
            </div>
            @endcan
            <div class="form-group">
                <div class="row clearfix">
                    <div class="col-lg-12 ">
                        <a class="btn btn-success btn-sm" href=