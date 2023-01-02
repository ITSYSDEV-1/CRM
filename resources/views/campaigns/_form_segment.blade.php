@if($action=='create')
    {{ Form::model($model,['route'=>'segment.store','files'=>'true','id'=>'segmentForm']) }}
@else
    {{ Form::model($model,['route'=>['segment.update',$model->id],'files'=>'true','id'=>'segmentForm']) }}
    {{ method_field('PUT') }}
@endif
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('name','Segment Name') }}
    </div>
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <div class="form-line">
                {{ Form::text('name',$model->name,['class'=>'form-control','required']) }}
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
        {{ Form::label('type','Type') }}
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
        <div class="form-group">
            <div class="form-line">
                {{ Form::select('type',[''=>'Select Type','Poststay'=>'Poststay','Birthday'=>'Birthday','Checkin Reminder'=>'Checkin Reminder','Promo'=>'Promo','Special Event'=>'Special Event'],$model->type,['class'=>'form-control','onchange'=>'getType(this.value)', 'data-live-search'=>'true','id'=>'type']) }}
            </div>
        </div>
    </div>
</div>


<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('country_id','Country') }}
</div>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <div class="form-group">
        <div class="form-line">
            {{ Form::select('country_id',[''=>'All Country']+\App\Country::pluck('country','iso2')->all(),$model->country_id,['class'=>'form-control','onchange'=>'checkRecepient()', 'data-live-search'=>'true']) }}
        </div>
    </div>
</div>

<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('guest_status','Guest Status') }}
</div>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <div class="form-group">
        <div class="form-line">
            {{ Form::select('guest_status',[''=>'All']+['I'=>'Inhouse','C'=>'Prestay','O'=>'Poststay','X'=>'Cancel'],$model->guest_status,['class'=>'form-control', 'data-live-search'=>'true','onchange'=>'checkRecepient()']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('','Total Spending') }}
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('spending_from',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'From']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('spending_to',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'To']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('','Stay Duration') }}
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('stay_from',null,['class'=>'datepicker form-control','id'=>'stay_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('stay_to',null,['class'=>'datepicker form-control', 'id'=>'stay_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('','Total Stays') }}
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('total_stay_from',null,['class'=>'form-control','id'=>'total_stay_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('total_stay_to',null,['class'=>'form-control', 'id'=>'total_stay_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
        </div>
    </div>
</div>

<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('','Total Nights') }}
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('total_night_from',null,['class'=>'form-control','id'=>'total_night_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
    <div class="form-group">
        <div class="form-line">
            {{ Form::text('total_night_to',null,['class'=>'form-control','id'=>'total_night_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'To']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('gender','Gender') }}
</div>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <div class="form-group">
        <div class="form-line">
            {{ Form::select('gender',[''=>'All']+['M'=>'Male','F'=>'Female'],$model->gender,['class'=>'form-control','id'=>'gender','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true']) }}
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
    {{ Form::label('template','Template') }}
</div>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <div class="form-group">
        <div class="form-line ">
            {{ Form::select('template',[''=>'Select Template'],$model->template,['class'=>'form-control','id'=>'template','onchange=selectTemplate(this.value)', 'data-live-search'=>'true']) }}
        </div>
    </div>
</div>
<div class="row clearfix">
    <div class="col-lg-12 ">
        <div class="col-lg-6">
            <a class="btn bg-teal waves-effect btn-xs" href="{{ url('campaign') }}" title="Back"><i class="material-icons">arrow_back</i> Back</a>
            <button class="btn bg-teal waves-effect btn-xs" type="submit"> <i class="material-icons">save</i> Save</button>
        </div>

    </div>
</div>



{{ Form::close() }}

