@extends('layouts.master')
@section('title')
    Contact Details | {{ config('app.name') }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="x_panel tile " >
                        <div class="panel-heading ">
                                @if(!empty($data[0]->excluded->reason))
                                <div class="alert alert-danger" role="alert">
                                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                    <span class="sr-only">Error:</span>
                                    <strong>This contact is listed in exclusion list</strong>
                                  </div>
                                @endif
                            <h2> {{ $data[0]->fname.' '.$data[0]->lname }}   </h2>

                            @if(!empty($data[0]->country->country))
                                <img src="{{ asset('flags/blank.gif') }}" class="flag flag-{{strtolower($data[0]->country->iso2)}}" alt="{{$data[0]->country->country}}" />
                            @endif
                            <br>
                            <br>

                        </div>
                        <div class="x_content" style="height:120px;">
                            <div class="row tile_count">

                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-user"></i>  STAYS/NIGHTS </span>
                                    <div class="count green">
                                        @if(empty($data[0]->transaction) )
                                            0
                                        @else
{{--                                            @php--}}
{{--                                                $contactTransaction = DB::table('contact_transaction')->where('contact_id', $data[0]->profileid)->get();--}}
{{--                                            @endphp--}}
                                            {{ count($data[0]->transaction) }} / {{$totalnight}}
{{--                                            @if(!$data[0]->transaction->isEmpty())--}}
{{--                                                @php--}}
{{--                                                    $sum=0;--}}
{{--                                                    foreach($data[0]->transaction as $night){--}}
{{--                                                    $total= \Carbon\Carbon::parse($night->dateco)->diffInDays(\Carbon\Carbon::parse($night->dateci));--}}
{{--                                                    $sum+=$total;--}}
{{--                                                    }--}}
{{--                                                @endphp--}}
{{--                                                {{ $sum }}--}}
{{--                                            @else--}}
{{--                                                0--}}
{{--                                            @endif--}}
                                        @endif
                                    </div>

                                </div>
                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-money"></i> TOTAL SPENDING</span>
                                    <div class="count blue">

                                        @if(!$data[0]->transaction->isEmpty())
                                            @php
                                                $sum=0;
                                                foreach($data[0]->transaction as $spending){
                                                    $total=$spending->revenue;
                                                    $sum+=$total;
                                                }
                                            @endphp
                                            {{ "Rp " . number_format($sum,0,',','.') }}
                                        @else
                                          Rp. 0
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-percent"></i> AVG SPENT / STAY</span>
                                    <div class="count blue">
                                        @if(!$data[0]->transaction->isEmpty())
                                            @php
                                                $sum=0;
                                                    foreach($data[0]->transaction as $spending){
                                                        $total=$spending->revenue;
                                                        $sum+=$total;
                                                    }
                                            @endphp
                                            {{"Rp " . number_format($sum/count($data[0]->transaction),0,',','.') }}
                                        @else
                                           Rp. 0
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-calendar"></i>
                                        @if(!$data[0]->birthday==NULL)
                                            BIRTHDAY  {{ \Carbon\Carbon::parse($data[0]->birthday)->format('d M') }}
                                        @else
                                            BIRTHDAY
                                        @endif
                                    </span>
                                    <div class="count blue">
                                       <h4>@php
                                            $datenow=\Carbon\Carbon::now('Asia/Makassar')->day;
                                            $datebday=\Carbon\Carbon::parse($data[0]->birthday)->day;
                                             $monthnow=\Carbon\Carbon::now('Asia/Makassar')->month;
                                              $monthbday=\Carbon\Carbon::parse($data[0]->birthday)->month;
                                           $bday=\Carbon\Carbon::create(\Carbon\Carbon::now()->year,$monthbday,$datebday);
                                           $now=\Carbon\Carbon::create(\Carbon\Carbon::now()->year,$monthnow,$datenow);

                                        if ($bday>$now){
                                            $day=$bday->diffInDays($now);
                                            echo 'Birthday in '. $day .' day/s';
                                        }elseif($bday<$now){


                                             $y=\Carbon\Carbon::now('Asia/Makassar')->addYear()->year;
                                            $next=\Carbon\Carbon::create($y,$monthbday,$datebday);
                                            $day=$next->diffInDays(\Carbon\Carbon::now('Asia/Makassar'));
                                              echo 'Birthday in '. $day .' day/s';
                                        } elseif($data[0]->birthday==NULL){
                                            echo '';
                                        } else{
                                            echo 'Today';
                                        }
                                        @endphp
                                       </h4>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                        <div class="panel-group full-body" id="accordion_18" role="tablist" aria-multiselectable="true">
                                            <div class="panel">
                                                <div class="panel-heading" role="tab" id="headingOne_18">
                                                    <h4 class="panel-title ">
                                                        <a class="collapsed teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseOne_18" aria-expanded="true" aria-controls="collapseOne_18">
                                                            <i class="fa fa-user"></i> PERSONAL INFORMATION
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapseOne_18" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_18">
                                                    <div class="panel-body">
                                                        <ul class="nav nav-tabs" role="tablist">
                                                            <li role="presentation" class="active">
                                                                <a href="#profile_info" data-toggle="tab">
                                                                    <i class="fa fa-user"></i> PROFILE
                                                                </a>
                                                            </li>
                                                            <li role="presentation">
                                                                <a href="#messages_with_icon_title" data-toggle="tab">
                                                                    <i class="fa fa-hotel"></i> STAY
                                                                </a>
                                                            </li>
                                                            <li role="presentation">
                                                                <a href="#settings_with_icon_title" data-toggle="tab">
                                                                    <i class="fa fa-mail-forward"></i> CAMPAIGNS
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <!-- Tab panes -->
                                                        <div class="tab-content">
                                                            <div role="tabpanel" class="tab-pane fade in active" id="profile_info">
                                                                <div class="row clearfix">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <div class="card">
                                                                            <div class="x_content">
                                                                                @if(!empty($action))
                                                                                    {{ Form::model($data[0],['route'=>['contacts.store'],'class'=>'form-horizontal','id'=>'contactForm1']) }}
                                                                                @else
                                                                                    {{ Form::model($data[0],['route'=>['contacts.update','id'=>$data[0]->contactid],'class'=>'form-horizontal','id'=>'contactForm1']) }}

                                                                                @endif
                                                                                {{ Form::hidden('company_name',$data[0]->companyname->isEmpty() ? NULL: $data[0]->companyname[0]->pivot->value) }}
                                                                                {{ Form::hidden('company_address',$data[0]->companyaddress->isEmpty() ? NULL :$data[0]->companyaddress[0]->pivot->value) }}
                                                                                {{ Form::hidden('company_phone',$data[0]->companyphone->isEmpty() ? NULL : $data[0]->companyphone[0]->pivot->value )}}
                                                                                {{ Form::hidden('company_email',$data[0]->companyemail->isEmpty() ? NULL:$data[0]->companyemail[0]->pivot->value) }}
                                                                                {{ Form::hidden('company_status',$data[0]->companystatus->isEmpty() ? NULL : $data[0]->companystatus[0]->pivot->value) }}
                                                                                {{ Form::hidden('company_type',$data[0]->companytype->isEmpty() ? NULL :$data[0]->companytype[0]->pivot->value) }}
                                                                                {{ Form::hidden('company_area',$data[0]->companyarea->isEmpty() ? NULL:$data[0]->companyarea[0]->pivot->value) }}
                                                                                {{ Form::hidden('company_nationality',$data[0]->companynationality->isEmpty() ? NULL : $data[0]->companynationality[0]->pivot->value) }}
                                                                                {{ Form::hidden('company_fax',$data[0]->companyfax->isEmpty() ? NULL : $data[0]->companyfax[0]->pivot->value) }}



                                                                                <div class="row clearfix">
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('fname','First Name') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::text('fname',$data[0]->fname,['class'=>'form-control','required']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('lname','Last Name') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::text('lname',$data[0]->lname=='' ? '':$data[0]->lname,['class'=>'form-control']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('salutation','Salutation') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::select('salutation',[''=>'Select Salutation','Mr'=>'Mr','Mrs'=>'Mrs','Miss'=>'Miss'],$data[0]->salutation,['class'=>'form-control selectpicker','required']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('marital_status','Marital Status') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::select('marital_status',[''=>'Select Marital Status','Divorced'=>'Divorced','Married'=>'Married','Single'=>'Single','Widowed'=>'Widowed'],$data[0]->marital_status,['class'=>'form-control selectpicker']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('gender','Gender') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::select('gender',[''=>'Select Gender','M'=>'Male','F'=>'Female'],$data[0]->gender,['class'=>'form-control selectpicker','required']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('birthday','Birthday') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::text('birthday',$data[0]->birthday==NULL ? '': \Carbon\Carbon::parse($data[0]->birthday)->format('d M Y'),['class'=>'form-control','required']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('wedding_bday','Wedding Anniversary') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::text('wedding_bday',$data[0]->wedding_bday==NULL ? '': \Carbon\Carbon::parse($data[0]->wedding_bday)->format('d M Y'),['class'=>' form-control','autocomplete'=>'off']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('address1','Address Line 1') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::text('address1',$data[0]->address1->isEmpty() ? '': $data[0]->address1[0]->pivot->value,['class'=>'form-control']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('address2','Address Line 2') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::text('address2',$data[0]->address2->isEmpty() ? '': $data[0]->address2[0]->pivot->value,['class'=>'form-control']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>


                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    {{ Form::label('country_id','Nationality') }}
                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                    <div class="form-group">
                                                                                        <div class="form-line">
                                                                                            {{ Form::select('country_id',[''=>'Select Nationality']+\App\Models\Country::pluck('country',env('COUNTRY_ISO'))->all(),$data[0]->country==NULL ? '' : $data[0]->country_id,['class'=>'form-control selectpicker', 'data-live-search'=>'true','required']) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        {{ Form::label('area','Origin/Area') }}
                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                {{ Form::select('area',[''=>'Select Origin']+\App\Models\Contact::groupBy('area')->pluck('area','area')->all(),$data[0]->area==NULL ? '' : $data[0]->area,['class'=>'form-control selectpicker', 'data-live-search'=>'true','required']) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    {{ Form::label('email','Email') }}
                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                    <div class="form-group">
                                                                                        <div class="form-line">
                                                                                            {{ Form::email('email',$data[0]->email,['class'=>'form-control','required']) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    {{ Form::label('mobile','Mobile') }}
                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                    <div class="form-group">
                                                                                        <div class="form-line">
                                                                                         {{ Form::text('mobile',$data[0]->mobile,['class'=>'form-control','number=number','minlength=6','maxlength=15']) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    {{ Form::label('idnumber','ID Number') }}
                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                    <div class="form-group">
                                                                                        <div class="form-line">
                                                                                            {{ Form::text('idnumber',$data[0]->idnumber=='' ? '': $data[0]->idnumber,['class'=>'form-control']) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row clearfix">
                                                                                    <div class="col-md-12 text-left">
                                                                                        <button class="btn  waves-effect btn-success btn-flat updateContact" type="submit" id="updateContact1">Save</button>
                                                                                    </div>
                                                                                </div>
                                                                                </div>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div role="tabpanel" class="tab-pane fade" id="messages_with_icon_title">
                                                                <div class="row clearfix">
                                                                    <div class="x_content">
                                                                    <div class="col-lg-12">

                                                                    </div>
                                                                    </div>
                                                                </div>

                                                                <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%">
                                                                    <thead>
                                                                    <tr class="bg-teal">
                                                                        <th class="align-center">Reservation Number</th>
                                                                        <th class="align-center">Check in</th>
                                                                        <th class="align-center">Check Out</th>
                                                                        <th class="align-center">No. Of Night</th>
                                                                        <th class="align-center">Booking Source</th>
                                                                        <th class="align-center">Room Number</th>
                                                                        <th class="align-center">Room Type</th>
                                                                        <th class="align-center">Total Spending (Rp)</th>
                                                                        <th class="align-center">Status</th>
                                                                        {{--<th class="align-center">Action</th>--}}
                                                                    </tr>
                                                                    </thead>

                                                                    <tbody>

                                                                    @foreach($data as $key=>$contact)

                                                                            <tr class="align-center">
                                                                                <td>{{$contact->resv_id}}</td>
                                                                                <td>{{$contact->dateci == null ? "" : \Carbon\Carbon::parse($contact->dateci)->format('d M Y')}}</td>
                                                                                <td>{{$contact->dateco == null ? "" : \Carbon\Carbon::parse($contact->dateco)->format('d M Y')}}</td>
                                                                                <td>{{\Carbon\Carbon::parse($contact->dateci)->diffInDays(\Carbon\Carbon::parse($contact->dateco))}}</td>
                                                                                <td>{{$contact->source }}</td>
                                                                                <td>{{$contact->room}}</td>
                                                                                <td>{{$contact->room_name}}</td>
                                                                                <td>{{number_format($contact->revenue,0,',','.')}}</td>
                                                                                <td>
                                                                                    @if($contact->foliostatus=='O')
                                                                                        Checked Out
                                                                                    @elseif($contact->foliostatus=='I')
                                                                                        Inhouse
                                                                                    @elseif($contact->foliostatus=='X')
                                                                                        Cancel
												                                    @elseif($contact->foliostatus=='G')
													                                    Guaranteed
												                                    @elseif($contact->foliostatus=='T')
													                                    Tentative
                                                                                    @else
                                                                                        Confirm
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div role="tabpanel" class="tab-pane fade" id="settings_with_icon_title">
                                                                <b>Settings Content</b>
                                                                <p>
                                                                 <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%">
                                                                    <thead>
                                                                      <tr class="bg-teal">
                                                                          <th>#</th>
                                                                          <th>Name</th>
                                                                          <th>Status</th>                                                                           '
                                                                          <th>Tracking</th>
                                                                          <th>Schedule</th>
                                                                      </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($data[0]->campaign as $key=>$campaign)
                                                                        <tr align="center">
                                                                            <td>{{ $key+1 }}</td>
                                                                            <td>{{ $campaign->name }}</td>
                                                                            <td>{{ $campaign->status }}</td>
                                                                            <td>
                                                                                @foreach(\App\Models\EmailResponse::where('campaign_id','=',$campaign->id)->where('recepient','=',$data[0]->email)->groupBy('event')->orderBy('created_at')->get() as $event)
                                                                                @if($loop->first)
                                                                                    {{ $event->event }}
                                                                                    @endif
                                                                                @endforeach
                                                                            </td>
                                                                            <td>{{ \Carbon\Carbon::parse($campaign->schedule->schedule)->format('d F Y H:i') }}</td>
                                                                        </tr>
                                                                    @endforeach

                                                                    </tbody>
                                                                </table>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel ">
                                                <div class="panel-heading" role="tab" id="headingTwo_18">
                                                    <h4 class="panel-title">
                                                        <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseTwo_18" aria-expanded="false"
                                                           aria-controls="collapseTwo_18">
                                                            <i class="fa fa-building"></i> COMPANY INFORMATION
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapseTwo_18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_18">
                                                    <div class="panel-body">
                                                        <div class="row clearfix">
                                                            <div class="card">
                                                                <div class="body">
                                                                    @if(!empty($action))
                                                                        {{ Form::model($data[0],['route'=>['contacts.store'],'class'=>'form-horizontal','id'=>'contactForm2']) }}
                                                                    @else
                                                                        {{ Form::model($data[0],['route'=>['contacts.update','id'=>$data[0]->contactid],'class'=>'form-horizontal','id'=>'contactForm2']) }}
                                                                    @endif

                                                                    {{ Form::hidden('fname',$data[0]->fname )}}
                                                                    {{ Form::hidden('lname',$data[0]->lname )}}
                                                                    {{ Form::hidden('email',$data[0]->email )}}
                                                                    {{ Form::hidden('mobile',$data[0]->mobile )}}
                                                                    {{ Form::hidden('salutation',$data[0]->salutation )}}
                                                                    {{ Form::hidden('salutation',$data[0]->salutation )}}
                                                                    {{ Form::hidden('gender',$data[0]->gender )}}
                                                                    {{ Form::hidden('birthday',$data[0]->birthday )}}
                                                                    {{ Form::hidden('wedding_bday',$data[0]->birthday )}}
                                                                    {{ Form::hidden('country_id',$data[0]->country==null ? '':$data[0]->country->id )}}
                                                                    {{ Form::hidden('address1',$data[0]->address1->isEmpty() ? '' :$data[0]->address1[0]->pivot->value) }}
                                                                    {{ Form::hidden('address2',$data[0]->address2->isEmpty() ? '' :$data[0]->address2[0]->pivot->value) }}

                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_name','Name') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::text('company_name',$data[0]->companyname->isEmpty() ? '': $data[0]->companyname[0]->pivot->value,['class'=>'form-control', 'data-live-search'=>'true']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_address','Address') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::text('company_address',$data[0]->companyaddress->isEmpty() ? '':$data[0]->companyaddress[0]->pivot->value,['class'=>'form-control']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_phone','Phone') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::text('company_phone',$data[0]->companyphone->isEmpty() ? '': $data[0]->companyphone[0]->pivot->value,['class'=>'form-control','number=number','minlength=6','maxlength=15']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_fax','Fax') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::text('company_fax',$data[0]->companyfax->isEmpty() ? '': $data[0]->companyfax[0]->pivot->value,['class'=>'form-control','number=number','minlength=6','maxlength=15']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_email','Email') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::email('company_email',$data[0]->companyemail->isEmpty()  ? '': $data[0]->companyemail[0]->pivot->value,['class'=>'form-control']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_type','Type') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::select('company_type',[''=>'Select Type','Compliment'=>'Compliment','Corporate'=>'Corporate','Direct'=>'Direct','Goverment'=>'Goverment','Group'=>'Group','OTA'=>'OTA','Timeshare'=>'Timeshare', 'Travel Agent'=>'Travel Agent','Whosaler'=>'Wholesaler'],$data[0]->companytype->isEmpty() ? '' : $data[0]->companytype[0]->pivot->value,['class'=>'form-control selectpicker']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_area','Area') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::text('company_area',$data[0]->companyarea->isEmpty() ? '': $data[0]->companyarea[0]->pivot->value,['class'=>'form-control']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_nationality','Nationality') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::select('company_nationality',[''=>'Select Nationality']+\App\Models\Country::pluck('country','id')->all(),$data[0]->companynationality->isEmpty() ? '' : $data[0]->companynationality[0]->pivot->value,['class'=>'form-control', 'data-live-search'=>'true']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        {{ Form::label('company_status','Status') }}
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                {{ Form::select('company_status',[''=>'Select Status','Prospect'=>'Prospect','Active'=>'Active','In Active'=>'In Active'],$data[0]->companystatus->isEmpty() ? '' : $data[0]->companystatus[0]->pivot->value,['class'=>'form-control selectpicker']) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row clearfix">
                                                                        <div class="col-md-12 text-left">
                                                                            <button class="btn  waves-effect btn-success btn-flat updateContact" id="updateContact2"  type="submit" >Save</button>
                                                                        </div>
                                                                    </div>
                                                                    {{ Form::close() }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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



        $('#complaint{{ $data[0]->contactid }}').on('change',function (e) {
            if($(this).is(':checked')){
                var val=1;
                $(this).val(1)
            }else{
                val=0;
                $(this).val(0)
            }
            $.ajax({
                url:'{{ route('update.exclude') }}',
                type:'POST',
                data:{
                    _token:'{{ csrf_token() }}',
                     val:val,
                    email:"{{ $data[0]->email }}"
                },

            })
        })

        $('#birthday , #wedding_bday').datepicker({
            language: 'en',
            dateFormat:'dd M yyyy'
        });

    </script>
    <script>
        if (window.location.pathname=='/contacts/add'){
            $('#updateContact2').attr('disabled','disabled');
        }
        $('.dataTable').dataTable()
    </script>
    <script>
       $('#contactForm1').validate();
       $('#contactForm2').validate();
    </script>
    <script>
        $('button[id^=updateContact]').click(function (event) {
            event.preventDefault();
            if(this.id=='updateContact1'){
                var form1=$('#contactForm1');
                form1.submit();
                var error=$('.error:visible').length;
                if (error>0){
                    $('#updateContact2').attr('disabled','disabled');
                    sweetAlert('Warning!', 'Error Found ','warning',7000);
                }else {
                    $('#updateContact2').removeAttr('disabled');
                    sweetAlert('Congratulation!', 'Data updated','success',7000);
                }
            } else {
                var form2=$('#contactForm2');
                form2.submit();

                var  error=$('.error:visible').length;
                if (error>0){
                    sweetAlert('Warning!', 'Error Found ','warning',7000);
                }else
                {
                    sweetAlert('Congratulation!', 'Data updated','success',7000);
                }
            }

            //var form = $('#contactForm');
            //form.submit();
           // var error=$('.error:visible').length;
           // if (error>0){
            //    $('#updateContact2').attr('disabled','disabled');
            //    sweetAlert('Warning!', 'Error Found ','warning',7000);
            //}else {
              //  $('#updateContact2').removeAttr('disabled');
            //    sweetAlert('Congratulation!', 'Data updated','success',7000);
           // }
        });
    </script>
@endsection


