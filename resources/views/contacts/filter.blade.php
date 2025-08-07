@extends('layouts.master')
@section('title')
    Filter Contacts | {{ config('app.name') }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h2>Filter Contacts</h2>
                            </div>
                            <div class="panel-body">
                                {{ Form::open() }}
                            
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('country_id','Country') }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::select('country_id[]',\App\Models\Country::pluck('country',env('COUNTRY_ISO'))->all(),null,['class'=>'form-control selectpicker country_id','multiple','onchange'=>'checkRecepient()','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('area','Area/Origin') }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::select('area[]',\App\Models\Contact::groupBy('area')->pluck('area','area')->filter(),null,['class'=>'form-control selectpicker area','multiple','onchange'=>'checkRecepient()','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('guest_status','Guest Status') }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::select('guest_status[]',['I'=>'Inhouse','C'=>'Confirm','O'=>'Poststay','X'=>'Cancel','G'=>'Guaranteed'],null,['class'=>'form-control selectpicker guest_status','multiple','actionsBox'=>'true', 'data-live-search'=>'true','onchange'=>'checkRecepient()']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Total Spending') }}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('spending_from',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_from','onchange'=>'checkRecepient()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'From']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('spending_to',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_to','onchange'=>'checkRecepient()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'To']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Stay Duration') }}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('stay_from',null,['class'=>' form-control','id'=>'stay_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','autocomplete'=>'off', 'data-live-search'=>'true','placeholder'=>'From']) }}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">

                                            {{ Form::text('stay_to',null,['class'=>' form-control', 'id'=>'stay_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','autocomplete'=>'off','data-live-search'=>'true','placeholder'=>'To']) }}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Total Stays') }}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('total_stay_from',null,['class'=>'form-control','id'=>'total_stay_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','autocomplete'=>'off', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('total_stay_to',null,['class'=>'form-control', 'id'=>'total_stay_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','autocomplete'=>'off','data-live-search'=>'true','placeholder'=>'To']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Total Nights') }}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('total_night_from',null,['class'=>'form-control','id'=>'total_night_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','autocomplete'=>'off', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('total_night_to',null,['class'=>'form-control','id'=>'total_night_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'autocomplete'=>'off','data-live-search'=>'true','placeholder'=>'To']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('gender','Gender') }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::select('gender[]',['M'=>'Male','F'=>'Female'],null,['class'=>'form-control selectpicker','id'=>'gender','multiple','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','autocomplete'=>'off','oninput'=>'this.onchange()','actionsBox'=>'true','data-live-search'=>'true']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Age') }}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('age_from',null,['class'=>'form-control','id'=>'age_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','autocomplete'=>'off', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('age_to',null,['class'=>'form-control', 'id'=>'age_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','autocomplete'=>'off','data-live-search'=>'true','placeholder'=>'To']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Birthday') }}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('bday_from',null,['class'=>'form-control','id'=>'bday_from','onchange'=>'checkRecepient()','onpaste'=>'this.onchange()','autocomplete'=>'off', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('bday_to',null,['class'=>'form-control', 'id'=>'bday_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','autocomplete'=>'off','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Wedding Anniversary') }}
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('wedding_bday_from',null,['class'=>'form-control','id'=>'wedding_bday_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('wedding_bday_to',null,['class'=>'form-control', 'id'=>'wedding_bday_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('','Booking Source') }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::select('booking_source[]',\App\Models\ProfileFolio::groupBy('source')->pluck('source','source')->all(),null,['class'=>'form-control selectpicker','id'=>'booking_source','multiple','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'actionsBox'=>'true','data-live-search'=>'true']) }}
                                        </div>
                                    </div>
                                </div>

                                {{ Form::close() }}
                                @can('3.3.2_create_segment')
                                <button class="btn btn-sm btn-success" id="createSegment"><i class="fa fa-plus-circle"></i> Create Segment </button>
                                @endcan
                            </div>

                            <hr>
                            <div class="panel-body">
                                <table  id="filter" class="table table-bordered table-striped table-hover" width="100%">
                                    <thead class="bg-teal">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll" ></th>
                                        <th class="align-center">Full Name</th>
                                        <th class="align-center">Birthday</th>
                                        <th class="align-center">Wedding Anniversary</th>
                                        <th class="align-center">Country</th>
                                        <th class="align-center">Area/Origin</th>
                                        <th class="align-center">Status</th>
                                        <th class="align-center">Booking Source</th>
                                        <th class="align-center">Total Stays</th>
                                        {{--<th class="align-center">Last Stay</th>--}}
                                        <th class="align-center">Total Spending (Rp.)</th>
                                        {{--<th class="align-center">Action</th>--}}
                                    </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                        <h4>With Selected </h4>
                        @can('3.3.3_create_campaign')
                        <a href="#" class="btn btn-sm btn-success" id="createCampaign" ><i class="fa fa-plus-circle"></i> Create Campaign</a>
                        @endcan
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="segmentModal" tabindex="-1" role="dialog" aria-labelledby="segmentModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="segmentModalLabel">Create New Segment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="segmentForm">
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('segment','Segment Name') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::text('segment',null,['class'=>'form-control','id'=>'segment','data-live-search'=>'true','required','placeholder'=>'Segment Name']) }}
                                </div>
                                <span class="text-danger">
                                <strong id="segment-error">
                                </strong>
                            </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="saveSegment" class="btn btn-sm btn-success">Save</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="campaignModal" tabindex="-1" role="dialog" aria-labelledby="campaignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="campaignModalLabel">Create New Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="checkboxform">
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('cname','Campaign Name') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::text('cname',null,['class'=>'form-control','id'=>'cname','data-live-search'=>'true','required','placeholder'=>'Campaign Name']) }}
                                </div>
                                <span class="text-danger">
                                <strong id="cname-error">
                                </strong>
                            </span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('template','Select Template') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::select('template',\App\Models\MailEditor::where('type','=','Promo')->pluck('name','id')->all(),null,['class'=>'form-control selectpicker','id'=>'template','actionsBox'=>'true','data-live-search'=>'true','onchange=selectTemplate(this.value)']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('schedule','Schedule') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::text('schedule',null,['class'=>'form-control datetimepicker','id'=>'schedule','required','placeholder'=>'Set Schedule']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('segmentid','Select Segment') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::select('segmentid',\App\Models\Segment::pluck('name','id')->all(),null,['class'=>'form-control selectpicker','id'=>'segmentid','actionsBox'=>'true','data-live-search'=>'true']) }}
                                </div>
                            </div>
                        </div>
                        <div class="previewtemplate">

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="saveCampaign" class="btn btn-sm btn-success">Save</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(document).ready(function () {
            $('#stay_from, #stay_to').datepicker({
                language: 'en',
                dateFormat: 'dd M yyyy ',

                autoClose:true,
                clearButton:true,
                onSelect:function(d,dt,i){
                    checkRecepient()
                }

            })

            $('#spending_from').maskMoney({thousands:'.', decimal:',', precision:0});
            $('#spending_to').maskMoney({thousands:'.', decimal:',', precision:0});
        })
        $('#createSegment').on('click',function (e) {
            e.preventDefault()
            $('#segmentModal').modal('show')
        })
        $('#saveSegment').on('click',function (e) {
            e.preventDefault()
            var sel=[];
            $('.country_id').children('option:selected').each(function () {
                sel.push($(this).val())
            });
            var are=[];
            $('.area').children('option:selected').each(function () {
                are.push($(this).val())
            });
            var gs=[];
            $('.guest_status').children('option:selected').each(function () {
                gs.push($(this).val())
            })
            var booking=[]
            $('#booking_source').children('option:selected').each(function () {
                booking.push($(this).val())
            })
            var gen=[]
            $('#gender').children('option:selected').each(function () {
                gen.push($(this).val())
            })
            $('#gender bs-select-all').on('click',function () {
                gen.length=0;
            })
            $('.country_id bs-select-all').on('click',function () {
                sel.length=0;
            })
            $('.area bs-select-all').on('click',function () {
                are.length=0;
            })
            $('.guest_status bs-select-all').on('click',function () {
                gs.length=0;
            })
            $('#booking_source bs-select-all').on('click',function () {
                booking.length=0;
            })
            var data={
                _token:'{{ csrf_token() }}',
                name:$('#segment').val(),
                guest_status:gs,
                stay_from:$('#stay_from').val(),
                stay_to:$('#stay_to').val(),
                total_night_from:$('#total_night_from').val(),
                total_night_to:$('#total_night_to').val(),
                age_from:$('#age_from').val(),
                age_to:$('#age_to').val(),
                bday_from:$('#bday_from').val(),
                bday_to:$('#bday_to').val(),
                wedding_bday_from:$('#wedding_bday_from').val(),
                wedding_bday_to:$('#wedding_bday_to').val(),
                country_id:sel,
                area:are,
                spending_from:$('#spending_from').val(),
                spending_to:$('#spending_to').val(),
                total_stay_from:$('#total_stay_from').val(),
                total_stay_to:$('#total_stay_to').val(),
                gender:gen,
                booking_source:booking,
            }

            $.ajax({
                url:'{{ route('savesegment') }}',
                data:data,
                type:'POST',
                success:function (data) {
                    if(data['error']){
                        swal('Error',data['error']['name'][0],'warning');
                    }else {

                        swal('Success','Segment Saved','success')
                        $('#segmentModal').modal('hide')
                        $.each(data, function (i, v) {
                            $('#segmentid').append('<option selected="selected" value=' + data['success']['id'] + ' >' + data['success']['name'] + '</option>').selectpicker('refresh')
                        })
                    }
                }

            })



        })
        $('#schedule').datetimepicker({
            minDate:new Date(),
            format: 'DD MMMM YYYY hh:mm A',
        });
        function selectTemplate(id) {
            var url='{{ route('campaign.template') }}';
            console.log(url);
            $.ajax({
                url:url,
                type:'post',
                data:{
                    id:id,
                    _token:'{{ csrf_token() }}'
                },
                success:function (data) {
                    $('.previewtemplate').empty();
                    $('.previewtemplate').append(data.content)
                }
            })

        }

        $('#bday_to , #bday_from,#wedding_bday_from,#wedding_bday_to').datepicker({
            language: 'en',
            dateFormat:'dd M',
            autoClose:true,
            clearButton:true,
            onSelect:function(d,dt,i){
                checkRecepient()
            }
        });
    </script>
    <script>

        function checkRecepient() {
            if ( $.fn.DataTable.isDataTable('#filter') ) {
                $('#filter').DataTable().destroy();
            }
            $('#filter tbody').empty();
            //  $('.table_body').empty()
            var nat=[];
            var gs=[];
            var gen=[];
            var src=[];
            var arr=[];
            var name=$('#name').val()
            var stay_from=$('#stay_from').val()
            var stay_to=$('#stay_to').val()
            var spending_from=$('#spending_from').val()
            var spending_to=$('#spending_to').val()
            var total_stay_from=$('#total_stay_from').val()
            var total_stay_to=$('#total_stay_to').val()
            var total_night_from=$('#total_night_from').val()
            var total_night_to=$('#total_night_to').val()
            var age_from=$('#age_from').val()
            var age_to=$('#age_to').val()
            var bday_from=$('#bday_from').val()
            var bday_to=$('#bday_to').val()
            var wedding_bday_from=$('#wedding_bday_from').val()
            var wedding_bday_to=$('#wedding_bday_to').val()


            $('.country_id').children('option:selected').each(function () {
                nat.push($(this).val())
            });
            $('.area').children('option:selected').each(function () {
                arr.push($(this).val())
            });
            $('.guest_status').children('option:selected').each(function () {
                gs.push($(this).val())
            });
            $('#gender').children('option:selected').each(function () {
                gen.push($(this).val())
            });
            $('#booking_source').children('option:selected').each(function () {
                src.push($(this).val())
            });

            var dt=$('#filter').DataTable({

                'retrieve': true,
                'ajax':{
                    "type":"POST",
                    "url":'filter',
                    "data":function(d){
                        d._token='{{ csrf_token() }}';
                        d.name=name;
                        d.country_id=nat;
                        d.area=arr;
                        d.guest_status=gs;
                        d.spending_from=spending_from;
                        d.spending_to=spending_to;
                        d.gender=gen;
                        d.stay_from=stay_from;
                        d.stay_to=stay_to;
                        d.total_stay_from=total_stay_from;
                        d.total_stay_to=total_stay_to;
                        d.total_night_from=total_night_from;
                        d.total_night_to=total_night_to;
                        d.age_from=age_from;
                        d.age_to=age_to;
                        d.bday_from=bday_from;
                        d.bday_to=bday_to;
                        d.wedding_bday_from=wedding_bday_from;
                        d.wedding_bday_to=wedding_bday_to;
                        d.booking_source=src;
                    },
                    "dataSrc":'',
                },
                "columns":[
                    {"data":null},
                    {"data":null},
                    {"data":"birthday","render":function(bd){
                        if(bd!=null){
                            var b=moment(bd).format('MMM D')
                        }else {
                            b='';
                        }
                        return b;
                    }},
                    {"data":"wedding_bday","render":function(bd){
                        if(bd!=null){
                            var wd=moment(bd).format('MMM D')
                        }else {
                            wd='';
                        }
                        return wd;
                    }},
                    {"data":"country_id","render":function(c){
                        var country='';
                        var json='[{"id":1,"iso2":"AF","iso3":"AFG","country":"Afghanistan","created_at":null,"updated_at":null},{"id":2,"iso2":"AX","iso3":"ALA","country":"Aland Islands","created_at":null,"updated_at":null},{"id":3,"iso2":"AL","iso3":"ALB","country":"Albania","created_at":null,"updated_at":null},{"id":4,"iso2":"DZ","iso3":"DZA","country":"Algeria","created_at":null,"updated_at":null},{"id":5,"iso2":"AS","iso3":"ASM","country":"American Samoa","created_at":null,"updated_at":null},{"id":6,"iso2":"AD","iso3":"AND","country":"Andorra","created_at":null,"updated_at":null},{"id":7,"iso2":"AO","iso3":"AGO","country":"Angola","created_at":null,"updated_at":null},{"id":8,"iso2":"AI","iso3":"AIA","country":"Anguilla","created_at":null,"updated_at":null},{"id":9,"iso2":"AQ","iso3":"ATA","country":"Antarctica","created_at":null,"updated_at":null},{"id":10,"iso2":"AG","iso3":"ATG","country":"Antigua and Barbuda","created_at":null,"updated_at":null},{"id":11,"iso2":"AR","iso3":"ARG","country":"Argentina","created_at":null,"updated_at":null},{"id":12,"iso2":"AM","iso3":"ARM","country":"Armenia","created_at":null,"updated_at":null},{"id":13,"iso2":"AW","iso3":"ABW","country":"Aruba","created_at":null,"updated_at":null},{"id":14,"iso2":"AU","iso3":"AUS","country":"Australia","created_at":null,"updated_at":null},{"id":15,"iso2":"AT","iso3":"AUT","country":"Austria","created_at":null,"updated_at":null},{"id":16,"iso2":"AZ","iso3":"AZE","country":"Azerbaijan","created_at":null,"updated_at":null},{"id":17,"iso2":"BS","iso3":"BHS","country":"Bahamas","created_at":null,"updated_at":null},{"id":18,"iso2":"BH","iso3":"BHR","country":"Bahrain","created_at":null,"updated_at":null},{"id":19,"iso2":"BD","iso3":"BGD","country":"Bangladesh","created_at":null,"updated_at":null},{"id":20,"iso2":"BB","iso3":"BRB","country":"Barbados","created_at":null,"updated_at":null},{"id":21,"iso2":"BY","iso3":"BLR","country":"Belarus","created_at":null,"updated_at":null},{"id":22,"iso2":"BE","iso3":"BEL","country":"Belgium","created_at":null,"updated_at":null},{"id":23,"iso2":"BZ","iso3":"BLZ","country":"Belize","created_at":null,"updated_at":null},{"id":24,"iso2":"BJ","iso3":"BEN","country":"Benin","created_at":null,"updated_at":null},{"id":25,"iso2":"BM","iso3":"BMU","country":"Bermuda","created_at":null,"updated_at":null},{"id":26,"iso2":"BT","iso3":"BTN","country":"Bhutan","created_at":null,"updated_at":null},{"id":27,"iso2":"BO","iso3":"BOL","country":"Bolivia","created_at":null,"updated_at":null},{"id":28,"iso2":"BA","iso3":"BIH","country":"Bosnia and Herzegovina","created_at":null,"updated_at":null},{"id":29,"iso2":"BW","iso3":"BWA","country":"Botswana","created_at":null,"updated_at":null},{"id":30,"iso2":"BV","iso3":"BVT","country":"Bouvet Island","created_at":null,"updated_at":null},{"id":31,"iso2":"BR","iso3":"BRA","country":"Brazil","created_at":null,"updated_at":null},{"id":32,"iso2":"VG","iso3":"VGB","country":"British Virgin Islands","created_at":null,"updated_at":null},{"id":33,"iso2":"IO","iso3":"IOT","country":"British Indian Ocean Territory","created_at":null,"updated_at":null},{"id":34,"iso2":"BN","iso3":"BRN","country":"Brunei Darussalam","created_at":null,"updated_at":null},{"id":35,"iso2":"BG","iso3":"BGR","country":"Bulgaria","created_at":null,"updated_at":null},{"id":36,"iso2":"BF","iso3":"BFA","country":"Burkina Faso","created_at":null,"updated_at":null},{"id":37,"iso2":"BI","iso3":"BDI","country":"Burundi","created_at":null,"updated_at":null},{"id":38,"iso2":"KH","iso3":"KHM","country":"Cambodia","created_at":null,"updated_at":null},{"id":39,"iso2":"CM","iso3":"CMR","country":"Cameroon","created_at":null,"updated_at":null},{"id":40,"iso2":"CA","iso3":"CAN","country":"Canada","created_at":null,"updated_at":null},{"id":41,"iso2":"CV","iso3":"CPV","country":"Cape Verde","created_at":null,"updated_at":null},{"id":42,"iso2":"KY","iso3":"CYM","country":"Cayman Islands","created_at":null,"updated_at":null},{"id":43,"iso2":"CF","iso3":"CAF","country":"Central African Republic","created_at":null,"updated_at":null},{"id":44,"iso2":"TD","iso3":"TCD","country":"Chad","created_at":null,"updated_at":null},{"id":45,"iso2":"CL","iso3":"CHL","country":"Chile","created_at":null,"updated_at":null},{"id":46,"iso2":"CN","iso3":"CHN","country":"China","created_at":null,"updated_at":null},{"id":47,"iso2":"HK","iso3":"HKG","country":"Hong Kong, SAR China","created_at":null,"updated_at":null},{"id":48,"iso2":"MO","iso3":"MAC","country":"Macao, SAR China","created_at":null,"updated_at":null},{"id":49,"iso2":"CX","iso3":"CXR","country":"Christmas Island","created_at":null,"updated_at":null},{"id":50,"iso2":"CC","iso3":"CCK","country":"Cocos (Keeling) Islands","created_at":null,"updated_at":null},{"id":51,"iso2":"CO","iso3":"COL","country":"Colombia","created_at":null,"updated_at":null},{"id":52,"iso2":"KM","iso3":"COM","country":"Comoros","created_at":null,"updated_at":null},{"id":53,"iso2":"CG","iso3":"COG","country":"Congo, (Brazzaville)","created_at":null,"updated_at":null},{"id":54,"iso2":"CD","iso3":"COD","country":"Congo, (Kinshasa)","created_at":null,"updated_at":null},{"id":55,"iso2":"CK","iso3":"COK","country":"Cook Islands","created_at":null,"updated_at":null},{"id":56,"iso2":"CR","iso3":"CRI","country":"Costa Rica","created_at":null,"updated_at":null},{"id":57,"iso2":"CI","iso3":"CIV","country":"Ivory Coast","created_at":null,"updated_at":null},{"id":58,"iso2":"HR","iso3":"HRV","country":"Croatia","created_at":null,"updated_at":null},{"id":59,"iso2":"CU","iso3":"CUB","country":"Cuba","created_at":null,"updated_at":null},{"id":60,"iso2":"CY","iso3":"CYP","country":"Cyprus","created_at":null,"updated_at":null},{"id":61,"iso2":"CZ","iso3":"CZE","country":"Czech Republic","created_at":null,"updated_at":null},{"id":62,"iso2":"DK","iso3":"DNK","country":"Denmark","created_at":null,"updated_at":null},{"id":63,"iso2":"DJ","iso3":"DJI","country":"Djibouti","created_at":null,"updated_at":null},{"id":64,"iso2":"DM","iso3":"DMA","country":"Dominica","created_at":null,"updated_at":null},{"id":65,"iso2":"DO","iso3":"DOM","country":"Dominican Republic","created_at":null,"updated_at":null},{"id":66,"iso2":"EC","iso3":"ECU","country":"Ecuador","created_at":null,"updated_at":null},{"id":67,"iso2":"EG","iso3":"EGY","country":"Egypt","created_at":null,"updated_at":null},{"id":68,"iso2":"SV","iso3":"SLV","country":"El Salvador","created_at":null,"updated_at":null},{"id":69,"iso2":"GQ","iso3":"GNQ","country":"Equatorial Guinea","created_at":null,"updated_at":null},{"id":70,"iso2":"ER","iso3":"ERI","country":"Eritrea","created_at":null,"updated_at":null},{"id":71,"iso2":"EE","iso3":"EST","country":"Estonia","created_at":null,"updated_at":null},{"id":72,"iso2":"ET","iso3":"ETH","country":"Ethiopia","created_at":null,"updated_at":null},{"id":73,"iso2":"FK","iso3":"FLK","country":"Falkland Islands (Malvinas)","created_at":null,"updated_at":null},{"id":74,"iso2":"FO","iso3":"FRO","country":"Faroe Islands","created_at":null,"updated_at":null},{"id":75,"iso2":"FJ","iso3":"FJI","country":"Fiji","created_at":null,"updated_at":null},{"id":76,"iso2":"FI","iso3":"FIN","country":"Finland","created_at":null,"updated_at":null},{"id":77,"iso2":"FR","iso3":"FRA","country":"France","created_at":null,"updated_at":null},{"id":78,"iso2":"GF","iso3":"GUF","country":"French Guiana","created_at":null,"updated_at":null},{"id":79,"iso2":"PF","iso3":"PYF","country":"French Polynesia","created_at":null,"updated_at":null},{"id":80,"iso2":"TF","iso3":"ATF","country":"French Southern Territories","created_at":null,"updated_at":null},{"id":81,"iso2":"GA","iso3":"GAB","country":"Gabon","created_at":null,"updated_at":null},{"id":82,"iso2":"GM","iso3":"GMB","country":"Gambia","created_at":null,"updated_at":null},{"id":83,"iso2":"GE","iso3":"GEO","country":"Georgia","created_at":null,"updated_at":null},{"id":84,"iso2":"DE","iso3":"DEU","country":"Germany","created_at":null,"updated_at":null},{"id":85,"iso2":"GH","iso3":"GHA","country":"Ghana","created_at":null,"updated_at":null},{"id":86,"iso2":"GI","iso3":"GIB","country":"Gibraltar","created_at":null,"updated_at":null},{"id":87,"iso2":"GR","iso3":"GRC","country":"Greece","created_at":null,"updated_at":null},{"id":88,"iso2":"GL","iso3":"GRL","country":"Greenland","created_at":null,"updated_at":null},{"id":89,"iso2":"GD","iso3":"GRD","country":"Grenada","created_at":null,"updated_at":null},{"id":90,"iso2":"GP","iso3":"GLP","country":"Guadeloupe","created_at":null,"updated_at":null},{"id":91,"iso2":"GU","iso3":"GUM","country":"Guam","created_at":null,"updated_at":null},{"id":92,"iso2":"GT","iso3":"GTM","country":"Guatemala","created_at":null,"updated_at":null},{"id":93,"iso2":"GG","iso3":"GGY","country":"Guernsey","created_at":null,"updated_at":null},{"id":94,"iso2":"GN","iso3":"GIN","country":"Guinea","created_at":null,"updated_at":null},{"id":95,"iso2":"GW","iso3":"GNB","country":"Guinea-Bissau","created_at":null,"updated_at":null},{"id":96,"iso2":"GY","iso3":"GUY","country":"Guyana","created_at":null,"updated_at":null},{"id":97,"iso2":"HT","iso3":"HTI","country":"Haiti","created_at":null,"updated_at":null},{"id":98,"iso2":"HM","iso3":"HMD","country":"Heard and Mcdonald Islands","created_at":null,"updated_at":null},{"id":99,"iso2":"VA","iso3":"VAT","country":"Vatican","created_at":null,"updated_at":null},{"id":100,"iso2":"HN","iso3":"HND","country":"Honduras","created_at":null,"updated_at":null},{"id":101,"iso2":"HU","iso3":"HUN","country":"Hungary","created_at":null,"updated_at":null},{"id":102,"iso2":"IS","iso3":"ISL","country":"Iceland","created_at":null,"updated_at":null},{"id":103,"iso2":"IN","iso3":"IND","country":"India","created_at":null,"updated_at":null},{"id":104,"iso2":"ID","iso3":"IDN","country":"Indonesia","created_at":null,"updated_at":null},{"id":105,"iso2":"IR","iso3":"IRN","country":"Iran, Islamic Republic of","created_at":null,"updated_at":null},{"id":106,"iso2":"IQ","iso3":"IRQ","country":"Iraq","created_at":null,"updated_at":null},{"id":107,"iso2":"IE","iso3":"IRL","country":"Ireland","created_at":null,"updated_at":null},{"id":108,"iso2":"IM","iso3":"IMN","country":"Isle of Man","created_at":null,"updated_at":null},{"id":109,"iso2":"IL","iso3":"ISR","country":"Israel","created_at":null,"updated_at":null},{"id":110,"iso2":"IT","iso3":"ITA","country":"Italy","created_at":null,"updated_at":null},{"id":111,"iso2":"JM","iso3":"JAM","country":"Jamaica","created_at":null,"updated_at":null},{"id":112,"iso2":"JP","iso3":"JPN","country":"Japan","created_at":null,"updated_at":null},{"id":113,"iso2":"JE","iso3":"JEY","country":"Jersey","created_at":null,"updated_at":null},{"id":114,"iso2":"JO","iso3":"JOR","country":"Jordan","created_at":null,"updated_at":null},{"id":115,"iso2":"KZ","iso3":"KAZ","country":"Kazakhstan","created_at":null,"updated_at":null},{"id":116,"iso2":"KE","iso3":"KEN","country":"Kenya","created_at":null,"updated_at":null},{"id":117,"iso2":"KI","iso3":"KIR","country":"Kiribati","created_at":null,"updated_at":null},{"id":118,"iso2":"KP","iso3":"PRK","country":"North Korea","created_at":null,"updated_at":null},{"id":119,"iso2":"KR","iso3":"KOR","country":"South Korea","created_at":null,"updated_at":null},{"id":120,"iso2":"KW","iso3":"KWT","country":"Kuwait","created_at":null,"updated_at":null},{"id":121,"iso2":"KG","iso3":"KGZ","country":"Kyrgyzstan","created_at":null,"updated_at":null},{"id":122,"iso2":"LA","iso3":"LAO","country":"Lao PDR","created_at":null,"updated_at":null},{"id":123,"iso2":"LV","iso3":"LVA","country":"Latvia","created_at":null,"updated_at":null},{"id":124,"iso2":"LB","iso3":"LBN","country":"Lebanon","created_at":null,"updated_at":null},{"id":125,"iso2":"LS","iso3":"LSO","country":"Lesotho","created_at":null,"updated_at":null},{"id":126,"iso2":"LR","iso3":"LBR","country":"Liberia","created_at":null,"updated_at":null},{"id":127,"iso2":"LY","iso3":"LBY","country":"Libya","created_at":null,"updated_at":null},{"id":128,"iso2":"LI","iso3":"LIE","country":"Liechtenstein","created_at":null,"updated_at":null},{"id":129,"iso2":"LT","iso3":"LTU","country":"Lithuania","created_at":null,"updated_at":null},{"id":130,"iso2":"LU","iso3":"LUX","country":"Luxembourg","created_at":null,"updated_at":null},{"id":131,"iso2":"MK","iso3":"MKD","country":"Macedonia, Republic of","created_at":null,"updated_at":null},{"id":132,"iso2":"MG","iso3":"MDG","country":"Madagascar","created_at":null,"updated_at":null},{"id":133,"iso2":"MW","iso3":"MWI","country":"Malawi","created_at":null,"updated_at":null},{"id":134,"iso2":"MY","iso3":"MYS","country":"Malaysia","created_at":null,"updated_at":null},{"id":135,"iso2":"MV","iso3":"MDV","country":"Maldives","created_at":null,"updated_at":null},{"id":136,"iso2":"ML","iso3":"MLI","country":"Mali","created_at":null,"updated_at":null},{"id":137,"iso2":"MT","iso3":"MLT","country":"Malta","created_at":null,"updated_at":null},{"id":138,"iso2":"MH","iso3":"MHL","country":"Marshall Islands","created_at":null,"updated_at":null},{"id":139,"iso2":"MQ","iso3":"MTQ","country":"Martinique","created_at":null,"updated_at":null},{"id":140,"iso2":"MR","iso3":"MRT","country":"Mauritania","created_at":null,"updated_at":null},{"id":141,"iso2":"MU","iso3":"MUS","country":"Mauritius","created_at":null,"updated_at":null},{"id":142,"iso2":"YT","iso3":"MYT","country":"Mayotte","created_at":null,"updated_at":null},{"id":143,"iso2":"MX","iso3":"MEX","country":"Mexico","created_at":null,"updated_at":null},{"id":144,"iso2":"FM","iso3":"FSM","country":"Micronesia, Federated States of","created_at":null,"updated_at":null},{"id":145,"iso2":"MD","iso3":"MDA","country":"Moldova","created_at":null,"updated_at":null},{"id":146,"iso2":"MC","iso3":"MCO","country":"Monaco","created_at":null,"updated_at":null},{"id":147,"iso2":"MN","iso3":"MNG","country":"Mongolia","created_at":null,"updated_at":null},{"id":148,"iso2":"ME","iso3":"MNE","country":"Montenegro","created_at":null,"updated_at":null},{"id":149,"iso2":"MS","iso3":"MSR","country":"Montserrat","created_at":null,"updated_at":null},{"id":150,"iso2":"MA","iso3":"MAR","country":"Morocco","created_at":null,"updated_at":null},{"id":151,"iso2":"MZ","iso3":"MOZ","country":"Mozambique","created_at":null,"updated_at":null},{"id":152,"iso2":"MM","iso3":"MMR","country":"Myanmar","created_at":null,"updated_at":null},{"id":153,"iso2":"NA","iso3":"NAM","country":"Namibia","created_at":null,"updated_at":null},{"id":154,"iso2":"NR","iso3":"NRU","country":"Nauru","created_at":null,"updated_at":null},{"id":155,"iso2":"NP","iso3":"NPL","country":"Nepal","created_at":null,"updated_at":null},{"id":156,"iso2":"NL","iso3":"NLD","country":"Netherlands","created_at":null,"updated_at":null},{"id":157,"iso2":"AN","iso3":"ANT","country":"Netherlands Antilles","created_at":null,"updated_at":null},{"id":158,"iso2":"NC","iso3":"NCL","country":"New Caledonia","created_at":null,"updated_at":null},{"id":159,"iso2":"NZ","iso3":"NZL","country":"New Zealand","created_at":null,"updated_at":null},{"id":160,"iso2":"NI","iso3":"NIC","country":"Nicaragua","created_at":null,"updated_at":null},{"id":161,"iso2":"NE","iso3":"NER","country":"Niger","created_at":null,"updated_at":null},{"id":162,"iso2":"NG","iso3":"NGA","country":"Nigeria","created_at":null,"updated_at":null},{"id":163,"iso2":"NU","iso3":"NIU","country":"Niue","created_at":null,"updated_at":null},{"id":164,"iso2":"NF","iso3":"NFK","country":"Norfolk Island","created_at":null,"updated_at":null},{"id":165,"iso2":"MP","iso3":"MNP","country":"Northern Mariana Islands","created_at":null,"updated_at":null},{"id":166,"iso2":"NO","iso3":"NOR","country":"Norway","created_at":null,"updated_at":null},{"id":167,"iso2":"OM","iso3":"OMN","country":"Oman","created_at":null,"updated_at":null},{"id":168,"iso2":"PK","iso3":"PAK","country":"Pakistan","created_at":null,"updated_at":null},{"id":169,"iso2":"PW","iso3":"PLW","country":"Palau","created_at":null,"updated_at":null},{"id":170,"iso2":"PS","iso3":"PSE","country":"Palestinian Territory","created_at":null,"updated_at":null},{"id":171,"iso2":"PA","iso3":"PAN","country":"Panama","created_at":null,"updated_at":null},{"id":172,"iso2":"PG","iso3":"PNG","country":"Papua New Guinea","created_at":null,"updated_at":null},{"id":173,"iso2":"PY","iso3":"PRY","country":"Paraguay","created_at":null,"updated_at":null},{"id":174,"iso2":"PE","iso3":"PER","country":"Peru","created_at":null,"updated_at":null},{"id":175,"iso2":"PH","iso3":"PHL","country":"Philippines","created_at":null,"updated_at":null},{"id":176,"iso2":"PN","iso3":"PCN","country":"Pitcairn","created_at":null,"updated_at":null},{"id":177,"iso2":"PL","iso3":"POL","country":"Poland","created_at":null,"updated_at":null},{"id":178,"iso2":"PT","iso3":"PRT","country":"Portugal","created_at":null,"updated_at":null},{"id":179,"iso2":"PR","iso3":"PRI","country":"Puerto Rico","created_at":null,"updated_at":null},{"id":180,"iso2":"QA","iso3":"QAT","country":"Qatar","created_at":null,"updated_at":null},{"id":181,"iso2":"RE","iso3":"REU","country":"R?union","created_at":null,"updated_at":null},{"id":182,"iso2":"RO","iso3":"ROU","country":"Romania","created_at":null,"updated_at":null},{"id":183,"iso2":"RU","iso3":"RUS","country":"Russian Federation","created_at":null,"updated_at":null},{"id":184,"iso2":"RW","iso3":"RWA","country":"Rwanda","created_at":null,"updated_at":null},{"id":185,"iso2":"BL","iso3":"BLM","country":"Saint Barthélemy","created_at":null,"updated_at":null},{"id":186,"iso2":"SH","iso3":"SHN","country":"Saint Helena","created_at":null,"updated_at":null},{"id":187,"iso2":"KN","iso3":"KNA","country":"Saint Kitts and Nevis","created_at":null,"updated_at":null},{"id":188,"iso2":"LC","iso3":"LCA","country":"Saint Lucia","created_at":null,"updated_at":null},{"id":189,"iso2":"MF","iso3":"MAF","country":"Saint-Martin (French part)","created_at":null,"updated_at":null},{"id":190,"iso2":"PM","iso3":"SPM","country":"Saint Pierre and Miquelon","created_at":null,"updated_at":null},{"id":191,"iso2":"VC","iso3":"VCT","country":"Saint Vincent and Grenadines","created_at":null,"updated_at":null},{"id":192,"iso2":"WS","iso3":"WSM","country":"Samoa","created_at":null,"updated_at":null},{"id":193,"iso2":"SM","iso3":"SMR","country":"San Marino","created_at":null,"updated_at":null},{"id":194,"iso2":"ST","iso3":"STP","country":"Sao Tome and Principe","created_at":null,"updated_at":null},{"id":195,"iso2":"SA","iso3":"SAU","country":"Saudi Arabia","created_at":null,"updated_at":null},{"id":196,"iso2":"SN","iso3":"SEN","country":"Senegal","created_at":null,"updated_at":null},{"id":197,"iso2":"RS","iso3":"SRB","country":"Serbia","created_at":null,"updated_at":null},{"id":198,"iso2":"SC","iso3":"SYC","country":"Seychelles","created_at":null,"updated_at":null},{"id":199,"iso2":"SL","iso3":"SLE","country":"Sierra Leone","created_at":null,"updated_at":null},{"id":200,"iso2":"SG","iso3":"SGP","country":"Singapore","created_at":null,"updated_at":null},{"id":201,"iso2":"SK","iso3":"SVK","country":"Slovakia","created_at":null,"updated_at":null},{"id":202,"iso2":"SI","iso3":"SVN","country":"Slovenia","created_at":null,"updated_at":null},{"id":203,"iso2":"SB","iso3":"SLB","country":"Solomon Islands","created_at":null,"updated_at":null},{"id":204,"iso2":"SO","iso3":"SOM","country":"Somalia","created_at":null,"updated_at":null},{"id":205,"iso2":"ZA","iso3":"ZAF","country":"South Africa","created_at":null,"updated_at":null},{"id":206,"iso2":"GS","iso3":"SGS","country":"South Georgia and the South Sandwich Islands","created_at":null,"updated_at":null},{"id":207,"iso2":"SS","iso3":"SSD","country":"South Sudan","created_at":null,"updated_at":null},{"id":208,"iso2":"ES","iso3":"ESP","country":"Spain","created_at":null,"updated_at":null},{"id":209,"iso2":"LK","iso3":"LKA","country":"Sri Lanka","created_at":null,"updated_at":null},{"id":210,"iso2":"SD","iso3":"SDN","country":"Sudan","created_at":null,"updated_at":null},{"id":211,"iso2":"SR","iso3":"SUR","country":"Suriname","created_at":null,"updated_at":null},{"id":212,"iso2":"SJ","iso3":"SJM","country":"Svalbard and Jan Mayen Islands","created_at":null,"updated_at":null},{"id":213,"iso2":"SZ","iso3":"SWZ","country":"Swaziland","created_at":null,"updated_at":null},{"id":214,"iso2":"SE","iso3":"SWE","country":"Sweden","created_at":null,"updated_at":null},{"id":215,"iso2":"CH","iso3":"CHE","country":"Switzerland","created_at":null,"updated_at":null},{"id":216,"iso2":"SY","iso3":"SYR","country":"Syrian Arab Republic, (Syria)","created_at":null,"updated_at":null},{"id":217,"iso2":"TW","iso3":"TWN","country":"Taiwan, Republic of China","created_at":null,"updated_at":null},{"id":218,"iso2":"TJ","iso3":"TJK","country":"Tajikistan","created_at":null,"updated_at":null},{"id":219,"iso2":"TZ","iso3":"TZA","country":"Tanzania, United Republic of","created_at":null,"updated_at":null},{"id":220,"iso2":"TH","iso3":"THA","country":"Thailand","created_at":null,"updated_at":null},{"id":221,"iso2":"TL","iso3":"TLS","country":"Timor-Leste","created_at":null,"updated_at":null},{"id":222,"iso2":"TG","iso3":"TGO","country":"Togo","created_at":null,"updated_at":null},{"id":223,"iso2":"TK","iso3":"TKL","country":"Tokelau","created_at":null,"updated_at":null},{"id":224,"iso2":"TO","iso3":"TON","country":"Tonga","created_at":null,"updated_at":null},{"id":225,"iso2":"TT","iso3":"TTO","country":"Trinidad and Tobago","created_at":null,"updated_at":null},{"id":226,"iso2":"TN","iso3":"TUN","country":"Tunisia","created_at":null,"updated_at":null},{"id":227,"iso2":"TR","iso3":"TUR","country":"Turkey","created_at":null,"updated_at":null},{"id":228,"iso2":"TM","iso3":"TKM","country":"Turkmenistan","created_at":null,"updated_at":null},{"id":229,"iso2":"TC","iso3":"TCA","country":"Turks and Caicos Islands","created_at":null,"updated_at":null},{"id":230,"iso2":"TV","iso3":"TUV","country":"Tuvalu","created_at":null,"updated_at":null},{"id":231,"iso2":"UG","iso3":"UGA","country":"Uganda","created_at":null,"updated_at":null},{"id":232,"iso2":"UA","iso3":"UKR","country":"Ukraine","created_at":null,"updated_at":null},{"id":233,"iso2":"AE","iso3":"ARE","country":"United Arab Emirates","created_at":null,"updated_at":null},{"id":234,"iso2":"GB","iso3":"GBR","country":"United Kingdom","created_at":null,"updated_at":null},{"id":235,"iso2":"US","iso3":"USA","country":"United States of America","created_at":null,"updated_at":null},{"id":236,"iso2":"UM","iso3":"UMI","country":"US Minor Outlying Islands","created_at":null,"updated_at":null},{"id":237,"iso2":"UY","iso3":"URY","country":"Uruguay","created_at":null,"updated_at":null},{"id":238,"iso2":"UZ","iso3":"UZB","country":"Uzbekistan","created_at":null,"updated_at":null},{"id":239,"iso2":"VU","iso3":"VUT","country":"Vanuatu","created_at":null,"updated_at":null},{"id":240,"iso2":"VE","iso3":"VEN","country":"Venezuela","created_at":null,"updated_at":null},{"id":241,"iso2":"VN","iso3":"VNM","country":"Viet Nam","created_at":null,"updated_at":null},{"id":242,"iso2":"VI","iso3":"VIR","country":"Virgin Islands, US","created_at":null,"updated_at":null},{"id":243,"iso2":"WF","iso3":"WLF","country":"Wallis and Futuna Islands","created_at":null,"updated_at":null},{"id":244,"iso2":"EH","iso3":"ESH","country":"Western Sahara","created_at":null,"updated_at":null},{"id":245,"iso2":"YE","iso3":"YEM","country":"Yemen","created_at":null,"updated_at":null},{"id":246,"iso2":"ZM","iso3":"ZMB","country":"Zambia","created_at":null,"updated_at":null},{"id":247,"iso2":"ZW","iso3":"ZWE","country":"Zimbabwe","created_at":null,"updated_at":null}]'

                        var js =JSON.parse(json);
                        for(var i in js){
                            if(c===js[i]["iso3"]){
                                var d=''
                                d=(js[i]["iso2"])
                                d=d.toLowerCase()
                                country=js[i]["country"];
                            }
                        }
                        return country + '<img src="../flags/blank.gif"  class="flag flag-'+d+' pull-right"  alt="'+country+'" />';


                    }},
                    {"data":"area"},
                    {"data":"transaction.0.status","render":function(s){
                        switch(s){
                            case 'I':{
                                s='Inhouse';
                                break;
                            }
                            case 'C':
                                s='Confirm';
                                break;
                            case 'X':
                                s='Cancel';
                                break;
                            case 'O':
                                s='Checked Out';
                                break;
                            case 'G':
                                s='Guaranteed';
                                break;
                            default:{
                                s=''
                            }
                        }
                        return s;
                    }},

                    {"data":null,"render":function (s) {
                        if(s.profilesfolio[0]){
                            return s.profilesfolio[0].source
                        } else {
                            return ""
                        }
                    }},
                    {"data":"transaction.length"},
                    {"data":"transaction","render":function (d) {
                        var e=0;
                        if(d.length>0){
                            var e=0;
                            if(d.length>=1){
                                for(var j =0 ; j<=d.length-1;j++){
                                    e+=d[j].revenue;

                                }
                            }
                        }
                        e=e.toString().split(".")[0];
                        e=e.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return e;
                    }}



                ],
                'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center',
                    'render': function (data, type, full, meta){
                        return '<input type="checkbox" class="chk" multiple name="selectedItem[]" value="' + $('<div/>').text(data.contactid).html() + '">';
                    }
                },{
                    'targets':1,
                    'searchable': true,
                    'orderable': false,
                    'render': function (data, type, full, meta){
                        @can('3.1.2_view_detail')
                            return '<a href="detail/' + data.contactid + '">' + data.fname + ' ' + data.lname + '</a>';
                        @else
                            return data.fname + ' ' + data.lname;
                        @endcan
                    }
                }],
            });
            dt.ajax.reload();
            $('#selectAll').click(function(event) {  //on click
                var checked = this.checked;
                dt.column(0).nodes().to$().each(function(index) {
                    if (checked) {
                        $(this).find('.chk').prop('checked', 'checked');
                    } else {
                        $(this).find('.chk').removeAttr('checked');
                    }
                });
            });


        }
        $('#createCampaign').on('click',function (e) {
            e.preventDefault();
            if($('#filter input:checked').length==0){
                swal('Error','No Contacts Selected','warning')
            }else{
                $('#campaignModal').modal('show');
            }


        })
        $('#saveCampaign').on('click',function (e) {
    e.preventDefault();

    // Validasi input terlebih dahulu
    if(!$('#cname').val()) {
        swal('', 'Campaign name is required', 'warning');
        return;
    }
    if(!$('#schedule').val()) {
        swal('', 'Schedule is required', 'warning'); 
        return;
    }
    if(!$('#template').val()) {
        swal('', 'Template is required', 'warning');
        return;
    }

    // Dapatkan contacts yang dicentang
    var selectedContacts = [];
    $('input[type=checkbox]:checked').each(function() {
        selectedContacts.push($(this).val());
    });

    if(selectedContacts.length === 0) {
        swal('', 'Please select at least one contact', 'warning');
        return;
    }

    $.ajax({
        url: '/campaign/store-with-reservation',
        type: 'POST',
        data: {
            name: $('#cname').val(),
            segments: [$('#segmentid').val()],
            template: $('#template').val(),
            contacts: selectedContacts,
            schedule: $('#schedule').val(),
            checkexternal: false, // untuk internal campaign
            _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            if(data.success) {
                swal({
                    title: "Sukses",
                    text: "Campaign baru berhasil ditambahkan",
                    type: "success"
                }, function() {
                    window.location.reload();
                });
            } else {
                if(data.errors) {
                    var errorMessage = '';
                    $.each(data.errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                    swal('Error', errorMessage, 'error');
                } else {
                    swal('Error', 'Terjadi kesalahan saat menyimpan campaign', 'error');
                }
            }
        },
        error: function(xhr, status, error) {
            swal('Error', 'Terjadi kesalahan pada server: ' + error, 'error');
        }
    });
});

    </script>


@endsection
