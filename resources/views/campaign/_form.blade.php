<form id="example-form" action="{{ route('campaign.store.reservation') }}" method="POST" role="form" class="form-horizontal">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div>
        <h3>Campaign</h3>
        <section>
            <div class="form-group">
            <div class="control-label col-sm-3">
                {{ Form::label('name','Campaign Name') }}
            </div>
            <div class="col-sm-8">
                <div class="form-group form-float">
                    <div class="form-line">
                        {{ Form::text('name',$model->name,['class'=>'form-control data-parsley-trigger="change"','autocomplete'=>'off','required']) }}
                    </div>
                    <div class="help-info"></div>
                </div>
            </div>
            </div>
            <div class="form-group">
            <div class="control-label col-sm-3">
                {{ Form::label('template','Template') }}
            </div>
            <div class="col-sm-8">
                <div class="form-group form-float">
                    <div class="form-line">
                        {{ Form::select('template',[''=>'Select Template']+\App\Models\MailEditor::where('type','=','Promo')->pluck('name','id')->all(),null,['class'=>'form-control template','required']) }}
                    </div>
                    <div class="help-info"></div>
                </div>
            </div>
            </div>
        </section>
        <h3>Segment</h3>
        <section>
            <input type="checkbox" name="external" id="checkexternal" value=""> External Contact
            <br>
            <div class="categoryselect">
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                    {{ Form::label('category','Category') }}
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                    <div class="form-group">
                        <div class="form-line">
                            {{ Form::select('category',[''=>'Select from category lists']+\App\Models\ExternalContactCategory::pluck('category','id')->all(),null,['class'=>'form-control  category','actionsBox'=>'true','data-size'=>8,'required','data-live-search'=>'true']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="segmentselect">
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                    {{ Form::label('segments','Segment') }}
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                    <div class="form-group">
                        <div class="form-line">
                            {{ Form::select('segments',[''=>'Select from segment lists']+\App\Models\Segment::pluck('name','id')->all(),null,['class'=>'form-control  segments','actionsBox'=>'true','required','data-size'=>8,'data-live-search'=>'true']) }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <h3>Schedule</h3>
        <section>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                {{ Form::label('','Schedule') }}
            </div>
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                <div class="form-group">
                    <div class="form-line">
                        {{ Form::text('schedule',null,['class'=>'form-control','id'=>'schedule','placeholder'=>'Schedule','autocomplete'=>'off' ,'required']) }}
                    </div>
                </div>

            </div>
        </section>
    </div>
</form>
