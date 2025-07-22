@extends('layouts.master')
@section('title')
    Edit User | {{ $configuration->hotel_name.' '.$configuration->app_title }}
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
                                <i class="fa fa-user-edit"></i> Edit User: {{ $user->name }}
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
                                    <a href="{{ route('role-permissions.manage-users') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to Users
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
                                            <form action="{{ route('role-permissions.update-user', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="form-group">
                                                    <label for="name" style="font-size: {{ $labelFontSize }};">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="email" style="font-size: {{ $labelFontSize }};">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                                    @error('email')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="password" style="font-size: {{ $labelFontSize }};">New Password (leave blank to keep current password)</label>
                                                    <input type="password" class="form-control" id="password" name="password">
                                                    <small class="text-muted">Minimum 6 characters</small>
                                                    @error('password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="password_confirmation" style="font-size: {{ $labelFontSize }};">Confirm New Password</label>
                                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                                </div>
                                                
                                                <div class="form-group text-center">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Update User
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Roles -->
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                        <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">
                                            <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;">
                                                <i class="fa fa-key"></i> User Roles
                                            </h4>
                                        </div>
                                        <div class="card-body" style="padding: 15px;">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Role Name</th>
                                                            <th>Code</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if($user->roles->count() > 0)
                                                            @foreach($user->roles as $role)
                                                                <tr>
                                                                    <td>{{ ucwords(strtolower(str_replace('_', ' ', $role->name))) }}</td>
                                                                    <td>{{ $role->name }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="2" class="text-center">No roles assigned</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="text-center">
                                                <p>To manage user roles, please use the role assignment page for each role.</p>
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