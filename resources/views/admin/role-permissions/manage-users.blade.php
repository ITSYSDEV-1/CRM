@extends('layouts.master')
@section('title')
    Manage Users | {{ $configuration->hotel_name.' '.$configuration->app_title }}
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
                                <i class="fa fa-users"></i> Manage Users
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
                                    <a href="{{ route('role-permissions.create-user') }}" class="btn btn-primary pull-right">
                                        <i class="fa fa-user-plus"></i> Create New User
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Users Table -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover datatable" id="users-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;">ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th style="width: 150px; text-align: center;">Roles</th>
                                                    <th style="width: 100px; text-align: center;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td style="text-align: center;">
                                                        @foreach($user->roles as $role)
                                                            <span style="font-size: 12px; padding: 3px 6px; background-color: #3498db; color: white; border-radius: 3px; margin-right: 3px;">
                                                                {{ ucwords(strtolower(str_replace('_', ' ', $role->name))) }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <a href="{{ route('role-permissions.edit-user', $user->id) }}" class="btn btn-primary btn-sm" style="font-size: 12px; margin-right: 5px; padding: 5px 8px;" title="Edit User">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
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
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#users-table').DataTable({
            responsive: false,
            order: [[0, 'asc']]
        });
    });
</script>
@endsection