@extends('layouts.master')
@section('title')
    Email Quota Usage Analytics {{ $monthName }} | {{ config('app.name') }}
@endsection

@section('content')
<div class="right_col" role="main">
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="x_panel tile">
                    <div class="panel-heading">
                        <a href="{{ url('email/quota/usage') }}" class="btn btn-primary pull-right">
                            <i class="fa fa-arrow-left"></i> Back to Overview
                        </a>
                        <h2>Email Quota Usage Analytics - {{ $monthName }}</h2>
                        <br><br>
                    </div>
                    <div class="x_content" style="height:120px;">
                        <div class="row tile_count" style="display: flex; flex-wrap: nowrap;">
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-paper-plane"></i> TOTAL REQUESTS</span>
                                <div class="count blue">
                                    {{ number_format($quotaData['calculated_metrics']['total_requests'] ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="count_bottom">Grand total/span>
                            </div>
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-check-circle"></i> SUCCESSFULLY SENT</span>
                                <div class="count green">
                                    {{ number_format($quotaData['metrics']['sent'] ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="count_bottom">{{ number_format($quotaData['calculated_metrics']['sent_rate'] ?? 0, 1) }}% of requests</span>
                            </div>
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-exclamation-triangle"></i> BOUNCED</span>
                                <div class="count red">
                                    {{ number_format($quotaData['metrics']['bounce'] ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="count_bottom">{{ number_format($quotaData['calculated_metrics']['bounce_rate'] ?? 0, 1) }}% of requests</span>
                            </div>
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-times-circle"></i> DROPPED</span>
                                <div class="count gray">
                                    {{ number_format($quotaData['metrics']['dropped'] ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="count_bottom">{{ number_format($quotaData['calculated_metrics']['drop_rate'] ?? 0, 1) }}% of requests</span>
                            </div>
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-eye"></i> OPENED</span>
                                <div class="count blue">
                                    {{ number_format($quotaData['metrics']['open'] ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="count_bottom">{{ number_format($quotaData['calculated_metrics']['open_rate'] ?? 0, 1) }}% open rate</span>
                            </div>
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-mouse-pointer"></i> CLICKED</span>
                                <div class="count green">
                                    {{ number_format($quotaData['metrics']['click'] ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="count_bottom">{{ number_format($quotaData['calculated_metrics']['click_rate'] ?? 0, 1) }}% click rate</span>
                            </div>
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-user-times"></i> UNSUBSCRIBED</span>
                                <div class="count orange">
                                    {{ number_format($quotaData['metrics']['unsub'] ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="count_bottom">Opted out</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                       
                        <div class="body">
                            <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example" id="datatable-responsive" width="100%">
                                <thead class="bg-teal">
                                <!-- First header row -->
                                <tr>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 6%;">No</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 14%;">Date</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 14%;">Day of Week</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 14%;">Quota Usage</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 14%;">Quota Remaining</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 12%;">Usage %</th>
                                    <th class="text-center align-middle" colspan="3" style="vertical-align: middle; width: 26%; border-right: 2px solid #ddd;">Sent</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 10%;">Bounced</th>
                                    <th class="text-center align-middle" rowspan="2" style="vertical-align: middle; width: 10%;">Drop</th>
                                </tr>
                                <!-- Second header row -->
                                <tr>
                                    <th class="text-center align-middle" style="vertical-align: middle;">opened</th>
                                    <th class="text-center align-middle" style="vertical-align: middle;">clicked</th>
                                    <th class="text-center align-middle" style="vertical-align: middle; border-right: 2px solid #ddd;">unsubscribe</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($quotaData['daily_breakdown']))
                                    @php $counter = 1; @endphp
                                    @foreach($quotaData['daily_breakdown'] as $date => $dayData)
                                    <!-- First row showing Total Sent -->
                                    <tr class="text-center bg-light">
                                        <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">{{ $counter++ }}</td>
                                        <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">{{ Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                                        <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">{{ Carbon\Carbon::parse($date)->format('l') }}</td>
                                        <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                            <span class="badge {{ $dayData['requests'] > 2000 ? 'alert-danger' : ($dayData['requests'] > 1000 ? 'alert-warning' : 'alert-success') }}">
                                                {{ number_format($dayData['quota_used'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">{{ number_format($dayData['quota_remaining'], 0, ',', '.') }}</td>
                                        <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                            <div class="progress" style="height: 20px; margin: 0 auto; width: 80%;" title="Usage: {{ number_format($dayData['usage_percentage'], 2) }}% | Used: {{ number_format($dayData['quota_used'], 0, ',', '.') }} | Daily Limit: {{ number_format($dayData['daily_quota_limit'] ?? 3000, 0, ',', '.') }}">
                                                <div class="progress-bar {{ $dayData['usage_percentage'] > 80 ? 'progress-bar-danger' : ($dayData['usage_percentage'] > 60 ? 'progress-bar-warning' : 'progress-bar-success') }}" 
                                                     style="width: {{ $dayData['usage_percentage'] }}%">
                                                        {{ number_format($dayData['usage_percentage'], 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td colspan="3" class="text-center align-middle" style="vertical-align: middle; border-right: 2px solid #ddd;">
                                                <strong style="font-size: 14px;">Total Sent: {{ number_format($dayData['sent'], 0, ',', '.') }}</strong>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                                <span class="badge {{ $dayData['bounced'] > 0 ? 'alert-danger' : 'alert-secondary' }}">
                                                    {{ number_format($dayData['bounced'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle" style="vertical-align: middle;">
                                                <span class="badge alert-secondary">
                                                    {{ number_format($dayData['dropped'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <!-- Second row showing individual metrics -->
                                        <tr class="text-center">
                                            <td class="text-center align-middle" style="vertical-align: middle;">
                                                <span class="badge {{ $dayData['opened'] > 0 ? 'alert-info' : 'alert-secondary' }}">
                                                    {{ number_format($dayData['opened'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle" style="vertical-align: middle;">
                                                <span class="badge alert-success">
                                                    {{ number_format($dayData['clicked'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle" style="vertical-align: middle; border-right: 2px solid #ddd;">
                                                <span class="badge alert-warning">
                                                    {{ number_format($dayData['unsubscribed'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
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