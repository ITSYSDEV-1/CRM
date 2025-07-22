@extends('layouts.master')
@section('title')
    Assign Users to Role | {{ $configuration->hotel_name.' '.$configuration->app_title }}
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
                            <h3 style="font-size: {{ $titleFontSize }};">Assign Users to Role: {{ $role->name }}</h3>
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
                            
                            <div class="row">
                                <!-- Assign New Users -->
                                <div class="col-md-6">
                                    <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                        <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">
                                            <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;"><i class="fa fa-user-plus"></i> Assign Users</h4>
                                        </div>
                                        <div class="card-body" style="padding: 15px;">
                                            @if($availableUsers->count() > 0)
                                                <form action="{{ route('role-permissions.store-assignments', $role->id) }}" method="POST">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label style="font-size: {{ $labelFontSize }};">Select Users to Assign:</label>
                                                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                                            @foreach($availableUsers as $user)
                                                                <div class="checkbox" style="margin: 5px 0;">
                                                                    <label style="font-size: {{ $contentFontSize }};">
                                                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}">
                                                                        <span style="margin-left: 5px;">{{ $user->name }} ({{ $user->email }})</span>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-sm btn-info" id="select-all">Select All</button>
                                                        <button type="button" class="btn btn-sm btn-default" id="deselect-all">Deselect All</button>
                                                    </div>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-plus"></i> Assign Selected Users
                                                    </button>
                                                </form>
                                            @else
                                                <p style="font-size: {{ $contentFontSize }}; color: #999;">All users are already assigned to this role.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Currently Assigned Users -->
                                <div class="col-md-6">
                                    <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                        <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">
                                            <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;"><i class="fa fa-users"></i> Currently Assigned Users ({{ $assignedUsers->count() }})</h4>
                                        </div>
                                        <div class="card-body" style="padding: 15px;">
                                            @if($assignedUsers->count() > 0)
                                                <div style="max-height: 400px; overflow-y: auto;">
                                                    @foreach($assignedUsers as $user)
                                                        <div class="user-item" style="padding: 10px; margin-bottom: 10px; border: 1px solid #eee; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;" data-user-id="{{ $user->id }}">
                                                            <div>
                                                                <strong style="font-size: {{ $contentFontSize }};">{{ $user->name }}</strong><br>
                                                                <small style="color: #666;">{{ $user->email }}</small>
                                                            </div>
                                                            <button type="button" class="btn btn-danger btn-sm remove-user" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" title="Remove from role">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p style="font-size: {{ $contentFontSize }}; color: #999;">No users assigned to this role yet.</p>
                                            @endif
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
    $(document).ready(function() {
        // Select all checkboxes
        $('#select-all').on('click', function() {
            $('input[name="user_ids[]"]').prop('checked', true);
        });
        
        // Deselect all checkboxes
        $('#deselect-all').on('click', function() {
            $('input[name="user_ids[]"]').prop('checked', false);
        });
        
        // Remove user from role
        $('.remove-user').on('click', function() {
            var userId = $(this).data('user-id');
            var userName = $(this).data('user-name');
            var roleId = {{ $role->id }};
            
            if (confirm('Are you sure you want to remove ' + userName + ' from this role?')) {
                $.ajax({
                    url: '/role-permissions/' + roleId + '/users/' + userId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the user item from the list
                            $('.user-item[data-user-id="' + userId + '"]').fadeOut(function() {
                                $(this).remove();
                                // Reload page to update available users list
                                location.reload();
                            });
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error removing user from role');
                    }
                });
            }
        });
    });
</script>
@endsection