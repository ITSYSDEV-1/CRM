@extends('layouts.master')

@section('title')
   User Profile | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@php
// ======== STYLING CONFIGURATION ========
// General Font Sizes
$titleFontSize = '18px';
$headerFontSize = '16px';
$contentFontSize = '15px';
$labelFontSize = '14px';

// Form Styling
$formLabelFontWeight = '600';
$formInputPadding = '10px 12px';
$formInputFontSize = '14px';

// Button Styling
$buttonFontSize = '14px';
$buttonPadding = '10px 20px';

// Card Styling
$cardTitleFontSize = '16px';
$cardTitleFontWeight = '600';

// Badge Styling
$badgeFontSize = '12px';
$badgePadding = '6px 10px';
$badgeMargin = '3px';
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
                            <h3 style="font-size: {{ $titleFontSize }};"><i class="fa fa-user"></i> User Profile</h3>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <!-- Profile Information Card -->
                                <div class="col-md-4">
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h4 style="font-size: {{ $cardTitleFontSize }}; font-weight: {{ $cardTitleFontWeight }};"><i class="fa fa-info-circle"></i> Profile Information</h4>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <div class="profile-info" style="text-align: center; padding: 20px 0;">
                                                <!-- User Icon -->
                                                <div class="profile-icon-container" style="margin-bottom: 20px;">
                                                    <i class="fa fa-user-circle" style="font-size: 80px; color: #2980b9;"></i>
                                                </div>
                                                
                                                <!-- User Details -->
                                                <div class="user-details" style="text-align: left; margin-top: 20px;">
                                                    <div class="detail-item" style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                                                        <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 5px; display: block;"><i class="fa fa-user"></i> Name:</label>
                                                        <span style="font-size: {{ $contentFontSize }}; color: #333;">{{ $user->name }}</span>
                                                    </div>
                                                    
                                                    <div class="detail-item" style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                                                        <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 5px; display: block;"><i class="fa fa-envelope"></i> Email:</label>
                                                        <span style="font-size: {{ $contentFontSize }}; color: #333;">{{ $user->email }}</span>
                                                    </div>
                                                    
                                                    <div class="detail-item" style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                                                        <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 8px; display: block;"><i class="fa fa-users"></i> Roles:</label>
                                                        <div class="roles-container">
                                                            @if($roles->count() > 0)
                                                                @foreach($roles as $role)
                                                                    <span class="badge badge-primary" style="font-size: {{ $badgeFontSize }}; padding: {{ $badgePadding }}; margin: {{ $badgeMargin }}; background-color: #3498db; border-radius: 8px;">{{ $role }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted" style="font-size: {{ $contentFontSize }};">No roles assigned</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="detail-item" style="margin-bottom: 0; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                                                        <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 5px; display: block;"><i class="fa fa-calendar"></i> User Since:</label>
                                                        <span style="font-size: {{ $contentFontSize }}; color: #333;">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Edit Profile & Change Password -->
                                <div class="col-md-8">
                                    <!-- Edit Profile Form -->
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h4 style="font-size: {{ $cardTitleFontSize }}; font-weight: {{ $cardTitleFontWeight }};"><i class="fa fa-edit"></i> Edit Profile</h4>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            @if(session('success'))
                                                <div class="alert alert-success" style="font-size: {{ $contentFontSize }};"><i class="fa fa-check-circle"></i> {{ session('success') }}</div>
                                            @endif
                                            
                                            <form method="POST" action="{{ route('profile.update') }}">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 8px; display: block;"><i class="fa fa-user"></i> Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required 
                                                           style="font-size: {{ $formInputFontSize }}; padding: {{ $formInputPadding }}; border: 1px solid #ddd; border-radius: 4px;">
                                                    @error('name')<span class="text-danger" style="font-size: {{ $labelFontSize }}; margin-top: 5px; display: block;"><i class="fa fa-exclamation-triangle"></i> {{ $message }}</span>@enderror
                                                </div>
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 8px; display: block;"><i class="fa fa-envelope"></i> Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required 
                                                           style="font-size: {{ $formInputFontSize }}; padding: {{ $formInputPadding }}; border: 1px solid #ddd; border-radius: 4px;">
                                                    @error('email')<span class="text-danger" style="font-size: {{ $labelFontSize }}; margin-top: 5px; display: block;"><i class="fa fa-exclamation-triangle"></i> {{ $message }}</span>@enderror
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary" 
                                                        style="font-size: {{ $buttonFontSize }}; padding: {{ $buttonPadding }}; background-color: #3498db; border-color: #3498db;">
                                                    <i class="fa fa-save"></i> Update Profile
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Change Password Form -->
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h4 style="font-size: {{ $cardTitleFontSize }}; font-weight: {{ $cardTitleFontWeight }};"><i class="fa fa-lock"></i> Change Password</h4>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <form method="POST" action="{{ route('profile.password.update') }}">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 8px; display: block;"><i class="fa fa-key"></i> Current Password</label>
                                                    <input type="password" name="current_password" class="form-control" required 
                                                           style="font-size: {{ $formInputFontSize }}; padding: {{ $formInputPadding }}; border: 1px solid #ddd; border-radius: 4px;">
                                                    @error('current_password')<span class="text-danger" style="font-size: {{ $labelFontSize }}; margin-top: 5px; display: block;"><i class="fa fa-exclamation-triangle"></i> {{ $message }}</span>@enderror
                                                </div>
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 8px; display: block;"><i class="fa fa-lock"></i> New Password</label>
                                                    <input type="password" name="password" class="form-control" required 
                                                           style="font-size: {{ $formInputFontSize }}; padding: {{ $formInputPadding }}; border: 1px solid #ddd; border-radius: 4px;">
                                                    @error('password')<span class="text-danger" style="font-size: {{ $labelFontSize }}; margin-top: 5px; display: block;"><i class="fa fa-exclamation-triangle"></i> {{ $message }}</span>@enderror
                                                </div>
                                                
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: {{ $labelFontSize }}; font-weight: {{ $formLabelFontWeight }}; color: #555; margin-bottom: 8px; display: block;"><i class="fa fa-lock"></i> Confirm New Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control" required 
                                                           style="font-size: {{ $formInputFontSize }}; padding: {{ $formInputPadding }}; border: 1px solid #ddd; border-radius: 4px;">
                                                </div>
                                                
                                                <button type="submit" class="btn btn-warning" 
                                                        style="font-size: {{ $buttonFontSize }}; padding: {{ $buttonPadding }}; background-color: #26B99A; border: 1px solid #169F85;">
                                                    <i class="fa fa-key"></i> Change Password
                                                </button>
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