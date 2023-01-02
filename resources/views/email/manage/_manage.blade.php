@if($action=='create')
    {{ Form::model($template,['route'=>'email.store','files'=>'true','id'=>'templateForm']) }}
@else
    {{ Form::model($template,['route'=>['email.update',$template->id],'files'=>'true','id'=>'templateForm']) }}
    {{ method_field('PUT') }}
@endif
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('name','Template Name') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::text('name',$template->name,['class'=>'form-control','required']) }}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('type','Type') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::select('type',[''=>'Select Type','Poststay'=>'Poststay','Birthday'=>'Birthday','Promo'=>'Promotion','Prestay'=>'Prestay','Miss You'=>'Miss You'],$template->type,['class'=>'form-control selectpicker','required']) }}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('subject','Subject') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::text('subject',$template->subject,['class'=>'form-control','required']) }}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card-inside-title">Source</div>
    <div class="demo-radio-button">
    <input name="group1" type="radio" id="radio_1" checked />
    <label for="radio_1">Editor</label>
    <input name="group1" type="radio" id="radio_2"/>
    <label for="radio_2">Import</label>
    </div>
</div>
<div class="fileImport col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('file','Import File') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::file('file',['class'=>'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="editorInput col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            {{--{{ Form::textarea('contents',$template->content,['class'=>'form-control','id'=>'summernote']) }}--}}
            {{ Form::textarea('contents',$template->content,['class'=>'form-control','id'=>'mytiny']) }}
        </div>
    </div>
</div>
<input type="hidden" name="active" id="priority" value="1" />
<div class="row clearfix">
    <div class="col-lg-12 ">
        <div class="col-lg-6">
            <a class="btn btn-sm btn-success" href="{{ url('email/template') }}" title="Back"><i class="fa fa-arrow-circle-o-left"></i> Back</a>
            <a class="btn btn-success btn-sm" id="saveTemplate"> <i class="fa fa-save"></i> Save </a>
        </div>

    </div>
</div>

{{ Form::close() }}
