@extends('layouts.master')
@section('title')
    Email Quota Usage {{ $currentYear }} | {{ \App\Models\Configuration::first()->hotel_name.' '.\App\Models\Configuration::first()->app_title }}
@endsection
@section('content')

<div class="right_col" role="main">
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Email Quota Usage - {{ $currentYear }}</h2>
                            <!-- Default pagination view options -->
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-6">
                                    <div class="dataTables_length">
                                        <label>Show 
                                            <select name="datatable-responsive_length" class="form-control input-sm" style="display: inline-block; width: auto;">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> entries
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="dataTables_filter" style="text-align: right;">
                                        <label>Search: 
                                            <input type="search" class="form-control input-sm" placeholder="" style="display: inline-block; width: auto;">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-lg-12">
                            </div>
                        </div>

                        <div class="body">
                            <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example" id="datatable-responsive" width="100%">
                                <thead class="bg-teal">
                                <!-- First header row -->
                                <tr>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 5%;">No</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 10%;">Month</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 12%;">Billing Period</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 12%;">Quota Usage</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 12%;">Quota Remaining</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 10%;">Usage %</th>
                                    <th class="text-center align-middle" colspan="3" style="vertical-align: middle; width: 24%; border-right: 2px solid #ddd;">Sent</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 8%;">Bounced</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 7%;">Drop</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 10%;">Action</th>
                                </tr>
                                <!-- Second header row -->
                                <tr>
                                    <th class="text-center align-middle" style="vertical-align: middle;">opened</th>
                                    <th class="text-center align-middle" style="vertical-align: middle;">clicked</th>
                                    <th class="text-center align-middle" style="vertical-align: middle; border-right: 2px solid #ddd;">unsubscribe</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($monthlyData as $key => $data)
                                @php
                                    $sent = $data['metrics']['sent'] ?? 0;
                                    $opened = $data['metrics']['open'] ?? 0;
                                    $clicked = $data['metrics']['click'] ?? 0;
                                    $unsubscribed = $data['metrics']['unsub'] ?? 0;
                                @endphp
                                <!-- First row showing Total Sent -->
                                <tr class="text-center bg-light">
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">{{ $key+1 }}</td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                        <a href="{{ url('email/quota/usage/').'/'.$data['month'] }}" >
                                            {{ $data['month_name'] }}
                                        </a>
                                    </td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">{{ Carbon\Carbon::parse($data['period_start'])->format('M d') }} - {{ Carbon\Carbon::parse($data['period_end'])->format('M d') }}</td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                        <span class="badge {{ $data['usage_percentage'] > 80 ? 'alert-danger' : ($data['usage_percentage'] > 60 ? 'alert-warning' : 'alert-success') }}">
                                            {{ number_format($data['quota_used'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">{{ number_format($data['quota_remaining'], 0, ',', '.') }}</td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                        <div class="progress" style="height: 20px; margin: 0 auto; width: 80%;" title="Usage: {{ number_format($data['usage_percentage'], 2) }}% ({{ number_format($data['quota_used'], 0, ',', '.') }} / {{ number_format($data['quota_used'] + $data['quota_remaining'], 0, ',', '.') }})">
                                            <div class="progress-bar {{ $data['usage_percentage'] > 80 ? 'progress-bar-danger' : ($data['usage_percentage'] > 60 ? 'progress-bar-warning' : 'progress-bar-success') }}" 
                                                 style="width: {{ $data['usage_percentage'] }}%">
                                                {{ number_format($data['usage_percentage'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="3" class="text-center align-middle" style="vertical-align: middle; border-right: 2px solid #ddd;">
                                        <strong style="font-size: 14px;">Total Sent: {{ number_format($sent, 0, ',', '.') }}</strong>
                                    </td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                        <span class="badge {{ ($data['metrics']['bounce'] ?? 0) > 0 ? 'alert-danger' : 'alert-secondary' }}">
                                            {{ number_format($data['metrics']['bounce'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                        <span class="badge alert-secondary">
                                            {{ number_format($data['metrics']['dropped'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                        <a href="{{ url('email/quota/usage/').'/'.$data['month'] }}" title="View Detailed Analytics">
                                            <i class="fa fa-eye" style="font-size: 1.5em;"></i>
                                        </a>
                                    </td>
                                </tr>
                                <!-- Second row showing individual metrics -->
                                <tr class="text-center">
                                    <td class="text-center align-middle" style="vertical-align: middle;">
                                        <span class="badge {{ $opened > 0 ? 'alert-info' : 'alert-secondary' }}">
                                            {{ number_format($opened, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle" style="vertical-align: middle;">
                                        <span class="badge {{ $clicked > 0 ? 'alert-success' : 'alert-secondary' }}">
                                            {{ number_format($clicked, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle" style="vertical-align: middle; border-right: 2px solid #ddd;">
                                        <span class="badge {{ $unsubscribed > 0 ? 'alert-warning' : 'alert-secondary' }}">
                                            {{ number_format($unsubscribed, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div class="row" style="margin-top: 15px;">
                                <div class="col-md-6">
                                    <div class="dataTables_info">
                                        Showing 1 to {{ count($monthlyData) }} of {{ count($monthlyData) }} entries
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers" style="text-align: right;">
                                        <ul class="pagination">
                                            <li class="paginate_button previous disabled">
                                                <a href="#">Previous</a>
                                            </li>
                                            <li class="paginate_button active">
                                                <a href="#">1</a>
                                            </li>
                                            <li class="paginate_button next disabled">
                                                <a href="#">Next</a>
                                            </li>
                                        </ul>
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
@endsection