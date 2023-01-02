@extends('layouts.master')
@section('title')
	Contact List | {{ \App\Models\Configuration::first()->hotel_name.' '.\App\Models\Configuration::first()->app_title }}
@endsection
@section('content')

	<div class="right_col" role="main">
		<section class="content">
			<div class="container-fluid">
				<div class="row clearfix">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="card">
							<div class="header">
								<h2>Contact List</h2>
							</div>
							<div class="row clearfix">
								<div class="col-lg-12">
								</div>
							</div>

							<div class="body">
								<table class="table table-bordered table-striped table-hover datatable responsive js-basic-example" id="datatable-responsive" width="100%">
									<thead class="bg-teal">
									<tr>
										<th class="align-center">#</th>
										<th class="align-center">Full Name</th>
										<th class="align-center">Birthday</th>
										<th class="align-center">Wedding Anniversary</th>
										<th class="align-center">Country</th>
										<th class="align-center">Area/Origin</th>
										<th class="align-center">Status</th>
										<th class="align-center">Campaign</th>
										<th class="align-center">Total Stays</th>
										<th class="align-center">Last Stay</th>
										<th class="align-center">Total Spending (Rp.)</th>
									</tr>
									</thead>
									<tbody>
									@foreach($data as $key=>$contact)
                                        @if($contact->transaction->sum('revenue')>=0)
                                        <tr class="align-center">
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @if(!empty($contact->lname))
                                                <a href="{{ url('contacts/detail/').'/'.$contact->contactid }}" >{{ ucwords(strtolower($contact->fname)).' '.ucwords(strtolower($contact->lname)) }}</a>
                                            @else
                                                <a href="{{ url('contacts/detail/').'/'.$contact->contactid }}" >{{ ucwords(strtolower($contact->fname)) }}</a>
                                            @endif
                                            @if( $contact->birthday=='' ? '': \Carbon\Carbon::parse($contact->birthday)->format('m-d')==\Carbon\Carbon::now()->format('m-d'))
                                                <i class="fa fa-birthday-cake " style="color: #009688" ></i>
                                            @endif
                                        </td>
                                        <td>{{ $contact->birthday=='' ? "": \Carbon\Carbon::parse($contact->birthday)->format('M d') }}</td>
                                        <td>{{ $contact->wedding_bday=='' ? "": \Carbon\Carbon::parse($contact->wedding_bday)->format('M d') }}</td>
                                        <td>{{ \App\Models\Country::where(env('COUNTRY_ISO'),$contact->country_id)->first()['country'] }}
                                            <img src="{{ asset('flags/blank.gif') }}" class="flag flag-{{strtolower($contact->country['iso2'])}} pull-right" alt="{{$contact->country['country']}}" />
                                        </td>
                                        <td>{{ $contact->area }}</td>
                                        <td>
                                            @if(count($contact->profilesfolio)<>0)
                                                @if($contact->profilesfolio->max()->foliostatus=='I')
                                                    Inhouse
                                                @elseif($contact->profilesfolio->max()->foliostatus=='C')
                                                    Confirm
                                                @elseif($contact->profilesfolio->max()->foliostatus=='X')
                                                    Cancel
                                                @elseif($contact->profilesfolio->max()->foliostatus=='G')
                                                    Guaranteed
                                                @elseif($contact->profilesfolio->max()->foliostatus=='T')
                                                    Tentative
                                                @elseif($contact->profilesfolio->max()->foliostatus=='O')
                                                    Check Out
                                                @endif
                                            @endif
                                        </td>


                                        <td> @if(count($contact->campaign) <> 0)
                                                    <i class="fa fa-envelope success"></i>
                                                    {{ $contact->campaign->where('status','=','Sent')->count()}} Campaign
                                            @endif
                                        </td>

                                        <td>
                                            {{ $contact->profilesfolio->whereIn('foliostatus',['O','I'])->count() }}
                                        </td>
                                        <td>
                                            {{ $contact->profilesfolio->max('dateci')==NULL ? "": \Carbon\Carbon::parse($contact->profilesfolio->max('dateci'))->format('d M Y') }}
                                        </td>
                                        <td>
                                            {{ number_format($contact->transaction->sum('revenue'),0,'.',',')}}
                                        </td>
                                        {{--<td>--}}
                                            {{--<a href="{{url('contacts/stay/add/').'/'.$contact->contactid}}" title="Add Stay"><i class="fa fa-hotel fa-2x "></i></a>--}}
                                        {{--</td>--}}
                                    </tr>
									@endif
									@endforeach
									</tbody>
								</table>

							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection
@section('script')
@endsection
