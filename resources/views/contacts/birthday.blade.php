@extends('layouts.master')
@section('content')
<div class="right_col" role="main">
<section class="content">
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Contacts Birthday</h2>
                    </div>
                    <div class="body">
                       {{ Form::open(['url'=>'contacts/birthday/search']) }}
                            <div class="row clearfix">
                                <div class="col-lg-2 col-md-6 col-sm-4 col-xs-3 form-control-label">
                                    {{ Form::label('days','Birthday in') }}
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('days',$days,['class'=>'form-control','required']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label">
                                    {{ Form::label('','Days') }}
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12 col-lg-push-2">
                                        <button class="btn bg-teal waves-effect btn-sm" type="submit">Search</button>
                                </div>
                            </div>

                        {{ Form::close() }}
                        <div class="row clearfix">
                            <table class="table table-bordered table-striped table-hover dataTable js-basic-example " width="100%">
                                <thead class="bg-teal">
                                <tr>
                                    <th>Full Name</th>
                                    <th>Birthday</th>
                                    <th>Country</th>
                                    <th>Total Stays</th>
                                    <th>Last Stay</th>
                                    <th>Total Spending (Rp.)</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contacts as $contact)

                                    <tr>
                                        <td>
                                            @if(!empty($contact->lname))
                                                <a href="{{ url('contacts/detail/').'/'.$contact->contactid }}" >{{ $contact->fname.' '.$contact->lname }}</a>
                                            @else
                                                <a href="{{ url('contacts/detail/').'/'.$contact->contactid }}" >{{ $contact->fname }}</a>
                                            @endif
                                            @if(\Carbon\Carbon::parse($contact->birthday)->format('m-d')==\Carbon\Carbon::now()->format('m-d'))
                                                 <i class="fa fa-birthday-cake " style="color: #009688" > </i>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($contact->birthday)->format('d M Y') }}</td>
                                        <td>{{ $contact->country->country }}</td>
                                        <td>
                                            {{ count($contact->transaction) }}
                                        </td>
                                        <td>
                                            {{ $contact->transaction->max('checkin')==NULL ? "": \Carbon\Carbon::parse($contact->transaction->max('checkin'))->format('d M Y') }}
                                        </td>
                                        <td>
                                            {{ number_format($contact->transaction->sum('revenue'),0,'.',',')}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
    @endsection