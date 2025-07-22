@extends('layouts.master')
@section('title')
    Role & Permission Management | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@php
// ======== STYLING CONFIGURATION ========
// General Font Sizes
$titleFontSize = '16x';
$headerFontSize = '16px';
$contentFontSize = '12px';
$labelFontSize = '14px';

// Table Header Styling
$tableHeaderFontWeight = '600';

// Table Cell Styling
$tableCellVerticalAlign = 'middle';
$tableCellPadding = '10px 8px';

// Button Styling
$buttonFontSize = '14px';

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
                            <h3 style="font-size: {{ $titleFontSize }};">Role & Permission Management</h3>
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
                            
                            <!-- Introduction Card -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                        <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">    
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;"><i class="fa fa-info-circle"></i> Role & Permission Management</h4>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <div class="btn-group">
                                                        <a href="{{ route('role-permissions.create') }}" class="btn btn-success" style="margin-right: 5px;">
                                                            <i class="fa fa-plus"></i> Add New Role
                                                        </a>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa fa-user"></i> User Management <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-right">
                                                                <li>
                                                                    <a href="{{ route('role-permissions.create-user') }}">
                                                                        <i class="fa fa-user-plus"></i> Create New User
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('role-permissions.manage-users') }}">
                                                                        <i class="fa fa-users"></i> Manage Users
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body" style="padding: 15px;">
                                            <p style="font-size: {{ $contentFontSize }};">
                                                This page allows you to manage roles and their associated permissions. Select a role from the list below to edit its permissions.
                                            </p>
                                            <p style="font-size: {{ $contentFontSize }};">
                                                <strong>Note:</strong> Permissions are hierarchical. Selecting a group or subgroup will automatically select all permissions within it.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Roles Table -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover datatable" id="roles-table">
                                            <thead>
                                                <tr>
                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; width: 50px;">ID</th>
                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; width: 150px;">Role Name</th>
                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; width: 120px;">Code</th>
                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; width: 80px; text-align: center;">Users</th>
                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; width: 100px; text-align: center;">Permissions</th>
                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; width: 120px; text-align: center;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($roles as $role)
                                                <tr>
                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $role->id }}</td>
                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ ucwords(strtolower(str_replace('_', ' ', $role->name))) }}</td>
                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $role->name }}</td>
                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; text-align: center;">
                                                        <span style="font-size: 12px; padding: 3px 6px; background-color: #3498db; color: white; border-radius: 3px;">
                                                            {{ \App\Models\User::role($role->name)->count() }}
                                                        </span>
                                                    </td>
                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; text-align: center;">
                                                        <span style="font-size: 12px; padding: 3px 6px; background-color: #2ecc71; color: white; border-radius: 3px;">
                                                            {{ $role->permissions->count() }}
                                                        </span>
                                                    </td>
                                                    <!-- Dalam tabel actions, tambahkan tombol delete -->
                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }}; text-align: center;">
                                                        <a href="{{ route('role-permissions.edit', $role->id) }}" class="btn btn-primary btn-sm" style="font-size: 12px; margin-right: 5px; padding: 5px 8px;" title="Edit Permissions">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-info btn-sm view-users" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}" style="font-size: 12px; margin-right: 5px; padding: 5px 8px;" title="View Users">
                                                            <i class="fa fa-users"></i>
                                                        </button>
                                                        <a href="{{ route('role-permissions.assign-users', $role->id) }}" class="btn btn-success btn-sm" style="font-size: 12px; margin-right: 5px; padding: 5px 8px;" title="Assign Users">
                                                            <i class="fa fa-user-plus"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm delete-role" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}" style="font-size: 12px; padding: 5px 8px;" title="Delete Role">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
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
            </div>
        </div>
    </section>
</div>

<!-- Users Modal -->
<div class="modal fade" id="usersModal" tabindex="-1" role="dialog" aria-labelledby="usersModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usersModalLabel" style="font-size: 18px;">Users with Role: <span id="role-name"></span></h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="users-table">
                        <thead>
                            <tr>
                                <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }};">ID</th>
                                <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }};">Name</th>
                                <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }};">Email</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            <!-- Users will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div id="no-users-message" style="display: none; text-align: center; padding: 20px;">
                    <p style="font-size: {{ $contentFontSize }};">No users assigned to this role.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#roles-table').DataTable({
            responsive: false,
            order: [[0, 'asc']]
        });
        
        // View Users Button Click
        $('.view-users').on('click', function() {
            var roleId = $(this).data('role-id');
            var roleName = $(this).data('role-name');
            
            // Format role name properly (convert underscores to spaces and capitalize)
            var formattedRoleName = roleName.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            
            // Set the formatted role name in the modal
            $('#role-name').text(formattedRoleName);
            
            // Clear previous data
            $('#users-table-body').empty();
            $('#no-users-message').hide();
            
            // Show the modal
            $('#usersModal').modal('show');
            
            // Load users via AJAX
            $.ajax({
                url: '/role-permissions/' + roleId + '/users',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.users.length > 0) {
                            // Populate the table with users
                            $.each(response.users, function(index, user) {
                                $('#users-table-body').append(
                                    '<tr>' +
                                    '<td style="font-size: {{ $contentFontSize }};">' + user.id + '</td>' +
                                    '<td style="font-size: {{ $contentFontSize }};">' + user.name + '</td>' +
                                    '<td style="font-size: {{ $contentFontSize }};">' + user.email + '</td>' +
                                    '</tr>'
                                );
                            });
                        } else {
                            // Show no users message
                            $('#no-users-message').show();
                        }
                    } else {
                        alert('Error loading users');
                    }
                },
                error: function() {
                    alert('Error loading users');
                }
            });
        });
        
        // Delete Role Button Click
        $('.delete-role').on('click', function() {
            var roleId = $(this).data('role-id');
            var roleName = $(this).data('role-name');
            
            // Format role name properly
            var formattedRoleName = roleName.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            
            if (confirm('Are you sure you want to delete the role: ' + formattedRoleName + '?')) {
                // Create form for DELETE request
                var form = $('<form>', {
                    'method': 'POST',
                    'action': '{{ url("/role-permissions") }}/' + roleId
                });
                
                form.append('@method("DELETE")');
                form.append('@csrf');
                
                // Append form to body and submit
                $('body').append(form);
                form.submit();
            }
        });
    });
</script>
@endsection