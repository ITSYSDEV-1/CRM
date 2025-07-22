@extends('layouts.master')
@section('title')
    Create New User | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@php
// ======== STYLING CONFIGURATION ========
$titleFontSize = '18px';
$headerFontSize = '16px';
$contentFontSize = '14px';
$labelFontSize = '14px';
$cardHeaderBg = '#f8f9fa';
$cardBorderColor = '#e9ecef';
// ======== END CONFIGURATION ========
@endphp

@section('content')
<div class="right_col" role="main">
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile">
                        <div class="x_title">
                            <h3 style="font-size: {{ $titleFontSize }};">
                                <i class="fa fa-user-plus"></i> Create New User
                            </h3>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <!-- Alert Messages -->
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    {{ session('error') }}
                                </div>
                            @endif
                            
                            <!-- Back Button -->
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-md-12">
                                    <a href="{{ route('role-permissions.index') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to Roles
                                    </a>
                                </div>
                            </div>
                            
                            <!-- User Form -->
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                        <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">
                                            <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;">
                                                <i class="fa fa-user"></i> User Information
                                            </h4>
                                        </div>
                                        <div class="card-body" style="padding: 15px;">
                                            <form action="{{ route('role-permissions.store-user') }}" method="POST">
                                                @csrf
                                                
                                                <div class="form-group">
                                                    <label for="name" style="font-size: {{ $labelFontSize }};">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="email" style="font-size: {{ $labelFontSize }};">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                                    @error('email')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="password" style="font-size: {{ $labelFontSize }};">Password</label>
                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                    @error('password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="password_confirmation" style="font-size: {{ $labelFontSize }};">Confirm Password</label>
                                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                                </div>
                                                
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Create User
                                                    </button>
                                                </div>
                                            </form>
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