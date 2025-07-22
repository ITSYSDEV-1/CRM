@extends('layouts.master')
@section('title')
    Create New Role | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@php
// ======== STYLING CONFIGURATION ========
// General Font Sizes
$titleFontSize = '16x';
$headerFontSize = '16px';
$contentFontSize = '12px';
$labelFontSize = '14px';

// Card Styling
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
                            <h3 style="font-size: {{ $titleFontSize }};">Create New Role</h3>
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
                            
                            <!-- Create Role Form -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                        <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">
                                            <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;"><i class="fa fa-plus-circle"></i> Create New Role</h4>
                                        </div>
                                        <div class="card-body" style="padding: 15px;">
                                            <form method="POST" action="{{ route('role-permissions.store') }}">
                                                @csrf
                                                
                                                <div class="form-group">
                                                    <label for="name" style="font-size: {{ $labelFontSize }};">Role Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                                    <small class="form-text text-muted" style="font-size: {{ $contentFontSize }};">Enter a unique role name. This will be used as the role identifier in the system.</small>
                                                    @error('name')
                                                        <span class="text-danger" style="font-size: {{ $contentFontSize }};">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group mt-4">
                                                    <a href="{{ route('role-permissions.index') }}" class="btn btn-default">Cancel</a>
                                                    <button type="submit" class="btn btn-primary">Create Role</button>
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