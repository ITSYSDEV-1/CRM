@extends('layouts.master')
@section('title')
    Campaign Calendar | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@section('content')
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Campaign Calendar</h2>
                        <button type="button" class="btn btn-secondary back-button" onclick="goBack()">
                            <i class="fa fa-arrow-left"></i> Back
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load FullCalendar setelah semua library master dimuat -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<link href="{{ asset('css/campaign-calendar.css') }}" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="{{ asset('js/campaign-calendar.js') }}"></script>
@endsection