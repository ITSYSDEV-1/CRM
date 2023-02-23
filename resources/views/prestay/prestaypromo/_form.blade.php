@if($action=='create')
    {{ Form::model($promoprestay,['route'=>'promo-configuration.store','files'=>'true','id'=>'promoprestayForm']) }}
@else
    {{ Form::model($promoprestay,['route'=>['promo-configuration.update',$promoprestay->id],'files'=>'true','id'=>'promoprestayForm']) }}
    {{ method_field('PUT') }}
@endif
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('name','Event Name') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::text('name',$promoprestay->name,['class'=>'form-control','required']) }}
                {!! $errors->first('name', '<p style="background-color: pink;" class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('eventduration','Event Duration') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::text('eventduration',$promoprestay->event_duration,['class'=>'form-control','required','id'=>'eventduration']) }}
                {!! $errors->first('eventduration', '<p style="background-color: pink;" class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('eventpicture','Event Picture') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            @if($action === 'edit')
                <div class="form-line">
                    <div>Current image</div>
                    <img class="pb-3 pr-3" src="{{$promoprestay->event_picture}}" width="200" alt="">
                </div>
            @endif
            <div class="form-line">
                {{ Form::file('eventpicture',['class'=>'form-control','required','id'=>'eventpicture']) }}
                {!! $errors->first('eventpicture', '<p style="background-color: pink;" class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('eventurl','Event Url') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::text('eventurl',$promoprestay->event_url,['class'=>'form-control','required']) }}
                {!! $errors->first('eventurl', '<p style="background-color: pink;" class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="active" id="priority" value="1" />
<div class="row clearfix">
    <div class="col-lg-12 ">
        <div class="col-lg-6">
            <a class="btn btn-success btn-sm" id="savePromo"> <i class="fa fa-save"></i> Save </a>
        </div>

    </div>
</div>

{{ Form::close() }}
