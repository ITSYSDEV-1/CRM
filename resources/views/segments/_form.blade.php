
<div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
    <div class="formsegment">
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('segmentname','Segment Name') }}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('segmentname',null,['class'=>'form-control  segmentname','onchange'=>'checkRecepient()' ]) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('country_id','Country') }}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::select('country_id[]',\App\Models\Country::pluck('country','iso3')->all(),null,['class'=>'form-control selectpicker  country_id','multiple','onchange'=>'checkRecepient()','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('area','Area/Origin') }}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::select('area[]',\App\Models\Contact::groupBy('area')->pluck('area','area')->filter(),null,['class'=>'form-control selectpicker  area','multiple','onchange'=>'checkRecepient()','actionsBox'=>'true', 'data-live-search'=>'true' ]) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('guest_status','Guest Status') }}
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::select('guest_status[]',['I'=>'Inhouse','C'=>'Prestay','O'=>'Poststay','X'=>'Cancel','G'=>'Guaranteed'],null,['class'=>'form-control selectpicker guest_status','multiple','actionsBox'=>'true', 'data-live-search'=>'true','onchange'=>'checkRecepient()']) }}
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
            {{ Form::label('','Stay Period') }}
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('stay_from',null,['class'=>' form-control','id'=>'stay_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('stay_to',null,['class'=>' form-control', 'id'=>'stay_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
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
                    {{ Form::select('gender[]',['M'=>'Male','F'=>'Female'],null,['class'=>'form-control selectpicker','id'=>'gender','multiple','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','actionsBox'=>'true','data-live-search'=>'true']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('','Age') }}
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('age_from',null,['class'=>'form-control','id'=>'age_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('age_to',null,['class'=>'form-control', 'id'=>'age_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('','Birthday') }}
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('bday_from',null,['class'=>'form-control','id'=>'bday_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From','autocomplete'=>'off']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('bday_to',null,['class'=>'form-control', 'id'=>'bday_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To','autocomplete'=>'off']) }}
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('','Wedding Anniversary') }}
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('wedding_bday_from',null,['class'=>'form-control','id'=>'wedding_bday_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From','autocomplete'=>'off']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
            <div class="form-group">
                <div class="form-line">
                    {{ Form::text('wedding_bday_to',null,['class'=>'form-control', 'id'=>'wedding_bday_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To','autocomplete'=>'off']) }}
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
            {{ Form::label('','Booking Source') }}
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                <div class="form-group">
                    <div class="form-line">
                        {{ Form::select('booking_source[]',\App\Models\ProfileFolio::groupBy('source')->pluck('source','source')->all(),null,['class'=>'form-control selectpicker','id'=>'booking_source','multiple','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'actionsBox'=>'true','data-live-search'=>'true']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <button class="btn btn-success" id="saveSegment"><i class="fa fa-save"></i> Save Segment</button>
        </div>

    </div>
</div>
