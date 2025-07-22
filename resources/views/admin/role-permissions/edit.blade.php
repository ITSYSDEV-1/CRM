@extends('layouts.master')
@section('title')
    Edit Role Permissions | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@php
// ======== STYLING CONFIGURATION ========
// General Font Sizes
$titleFontSize = '18px';
$headerFontSize = '16px';
$contentFontSize = '15px';
$labelFontSize = '14px';

// Card Styling
$cardHeaderBg = '#f8f9fa';
$cardBorderColor = '#e9ecef';

// Permission Group Colors
$groupHeaderBg = '#f8f9fa';
$subGroupHeaderBg = '#f1f3f5';
$permissionItemBg = '#ffffff';

// Checkbox Styling
$checkboxSize = '18px';
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
                            <h3 style="font-size: {{ $titleFontSize }};">Edit Permissions for Role: {{ ucwords(strtolower(str_replace('_', ' ', $role->name))) }}</h3>
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
                            
                            <!-- Role Info Card -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                        <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">
                                            <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;"><i class="fa fa-id-card"></i> Role Information</h4>
                                        </div>
                                        <div class="card-body" style="padding: 15px;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <p style="font-size: {{ $contentFontSize }};"><strong>Role ID:</strong> {{ $role->id }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p style="font-size: {{ $contentFontSize }};"><strong>Role Name:</strong> {{ ucwords(strtolower(str_replace('_', ' ', $role->name))) }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p style="font-size: {{ $contentFontSize }};"><strong>Users with this role:</strong> {{ \App\Models\User::role($role->name)->count() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Permissions Form -->
                            <form action="{{ route('role-permissions.update', $role->id) }}" method="POST" id="permissions-form">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card" style="border: 1px solid {{ $cardBorderColor }}; margin-bottom: 20px;">
                                            <div class="card-header" style="background-color: {{ $cardHeaderBg }}; padding: 15px; border-bottom: 1px solid {{ $cardBorderColor }};">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h4 style="margin: 0; font-size: {{ $headerFontSize }}; font-weight: 600;"><i class="fa fa-key"></i> Permissions</h4>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-primary" id="expand-all">
                                                                <i class="fa fa-plus-square"></i> Expand All
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-default" id="collapse-all">
                                                                <i class="fa fa-minus-square"></i> Collapse All
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-success" id="check-all">
                                                                <i class="fa fa-check-square"></i> Check All
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" id="uncheck-all">
                                                                <i class="fa fa-square"></i> Uncheck All
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body" style="padding: 15px;">
                                                <div class="permission-tree">
                                                    @foreach($permissionStructure->permission_groups as $group)
                                                        <div class="permission-group" style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px;">
                                                            <div class="group-header" style="padding: 12px 15px; background-color: {{ $groupHeaderBg }}; border-bottom: 1px solid #ddd; cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                                                                <div style="display: flex; align-items: center;">
                                                                    <div class="checkbox" style="margin: 0; margin-right: 10px;">
                                                                        <label style="font-size: {{ $contentFontSize }}; font-weight: 600;">
                                                                            <input type="checkbox" class="group-checkbox" data-group="{{ $group->id }}" style="width: {{ $checkboxSize }}; height: {{ $checkboxSize }};">
                                                                            <span style="margin-left: 5px;">{{ $group->group_number }}. {{ $group->name }}</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <i class="fa fa-chevron-down toggle-icon"></i>
                                                                </div>
                                                            </div>
                                                            <div class="group-content" style="padding: 0 15px; display: none;">
                                                                <div style="padding: 10px 0;">
                                                                    <p style="font-size: {{ $labelFontSize }}; color: #666;">{{ $group->description }}</p>
                                                                </div>
                                                                
                                                                @foreach($group->sub_groups as $subGroup)
                                                                    <div class="sub-group" style="margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">
                                                                        <div class="sub-group-header" style="padding: 10px 15px; background-color: {{ $subGroupHeaderBg }}; border-bottom: 1px solid #ddd; cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                                                                            <div style="display: flex; align-items: center;">
                                                                                <div class="checkbox" style="margin: 0; margin-right: 10px;">
                                                                                    <label style="font-size: {{ $contentFontSize }}; font-weight: 600;">
                                                                                        <input type="checkbox" class="sub-group-checkbox" data-group="{{ $group->id }}" data-sub-group="{{ $subGroup->id }}" style="width: {{ $checkboxSize }}; height: {{ $checkboxSize }};">
                                                                                        <span style="margin-left: 5px;">{{ $subGroup->sub_group_number }}. {{ $subGroup->name }}</span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div>
                                                                                <i class="fa fa-chevron-down toggle-icon"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="sub-group-content" style="padding: 15px; display: none;">
                                                                            @if(!empty($subGroup->permissions))
                                                                                <div class="row">
                                                                                    @foreach($subGroup->permissions as $permission)
                                                                                        <div class="col-md-4">
                                                                                            <div class="permission-item" style="padding: 10px; margin-bottom: 10px; background-color: {{ $permissionItemBg }}; border: 1px solid #eee; border-radius: 4px;">
                                                                                                <div class="checkbox">
                                                                                                    <label style="font-size: {{ $labelFontSize }};">
                                                                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="permission-checkbox" 
                                                                                                            data-group="{{ $group->id }}" 
                                                                                                            data-sub-group="{{ $subGroup->id }}" 
                                                                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                                                                            style="width: {{ $checkboxSize }}; height: {{ $checkboxSize }};">
                                                                                                        <span style="margin-left: 5px;">{{ $permission->name }}</span>
                                                                                                    </label>
                                                                                                </div>
                                                                                                <p style="margin: 5px 0 0 25px; font-size: 12px; color: #777;">
                                                                                                    <i class="fa fa-info-circle"></i> {{ $permission->permission_number }} - {{ $permission->action }}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @else
                                                                                <p style="font-size: {{ $labelFontSize }}; color: #999; font-style: italic;">No permissions in this subgroup</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-m">
                                            <i class="fa fa-save"></i> Save Permissions
                                        </button>
                                    </div>
                                </div>
                            </form>
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
        // Toggle group content
        $('.group-header').on('click', function(e) {
            // Jangan lakukan toggle jika yang diklik adalah checkbox
            if ($(e.target).is('input[type="checkbox"]') || $(e.target).closest('label').length > 0) {
                return;
            }
            var $groupContent = $(this).next('.group-content');
            $groupContent.slideToggle();
            $(this).find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');
        });
        
        // Toggle subgroup content
        $('.sub-group-header').on('click', function(e) {
            // Jangan lakukan toggle jika yang diklik adalah checkbox
            if ($(e.target).is('input[type="checkbox"]') || $(e.target).closest('label').length > 0) {
                return;
            }
            var $subGroupContent = $(this).next('.sub-group-content');
            $subGroupContent.slideToggle();
            $(this).find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');
        });
        
        // Expand all groups and subgroups
        $('#expand-all').on('click', function() {
            $('.group-content, .sub-group-content').slideDown();
            $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });
        
        // Collapse all groups and subgroups
        $('#collapse-all').on('click', function() {
            $('.group-content, .sub-group-content').slideUp();
            $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        });
        
        // Check all permissions
        $('#check-all').on('click', function() {
            $('.permission-checkbox, .sub-group-checkbox, .group-checkbox').prop('checked', true);
            updateParentCheckboxes();
        });
        
        // Uncheck all permissions
        $('#uncheck-all').on('click', function() {
            $('.permission-checkbox, .sub-group-checkbox, .group-checkbox').prop('checked', false);
        });
        
        // Initialize parent checkboxes based on children
        updateParentCheckboxes();
        
        // Handle group checkbox change
        $('.group-checkbox').on('change', function() {
            var groupId = $(this).data('group');
            var isChecked = $(this).prop('checked');
            
            // Check/uncheck all subgroups in this group
            $('.sub-group-checkbox[data-group="' + groupId + '"]').prop('checked', isChecked);
            
            // Check/uncheck all permissions in this group
            $('.permission-checkbox[data-group="' + groupId + '"]').prop('checked', isChecked);
        });
        
        // Handle subgroup checkbox change
        $('.sub-group-checkbox').on('change', function() {
            var groupId = $(this).data('group');
            var subGroupId = $(this).data('sub-group');
            var isChecked = $(this).prop('checked');
            
            // Check/uncheck all permissions in this subgroup
            $('.permission-checkbox[data-group="' + groupId + '"][data-sub-group="' + subGroupId + '"]').prop('checked', isChecked);
            
            // Update parent group checkbox
            updateGroupCheckbox(groupId);
        });
        
        // Handle permission checkbox change
        $('.permission-checkbox').on('change', function() {
            var groupId = $(this).data('group');
            var subGroupId = $(this).data('sub-group');
            
            // Update parent subgroup checkbox
            updateSubGroupCheckbox(groupId, subGroupId);
            
            // Update parent group checkbox
            updateGroupCheckbox(groupId);
        });
        
        // Function to update all parent checkboxes
        function updateParentCheckboxes() {
            // Update all subgroup checkboxes
            $('.sub-group-checkbox').each(function() {
                var groupId = $(this).data('group');
                var subGroupId = $(this).data('sub-group');
                updateSubGroupCheckbox(groupId, subGroupId);
            });
            
            // Update all group checkboxes
            $('.group-checkbox').each(function() {
                var groupId = $(this).data('group');
                updateGroupCheckbox(groupId);
            });
        }
        
        // Function to update a specific subgroup checkbox
        function updateSubGroupCheckbox(groupId, subGroupId) {
            var $subGroupCheckbox = $('.sub-group-checkbox[data-group="' + groupId + '"][data-sub-group="' + subGroupId + '"]');
            var $permissions = $('.permission-checkbox[data-group="' + groupId + '"][data-sub-group="' + subGroupId + '"]');
            var totalPermissions = $permissions.length;
            var checkedPermissions = $permissions.filter(':checked').length;
            
            if (totalPermissions > 0) {
                $subGroupCheckbox.prop('checked', checkedPermissions === totalPermissions);
            }
        }
        
        // Function to update a specific group checkbox
        function updateGroupCheckbox(groupId) {
            var $groupCheckbox = $('.group-checkbox[data-group="' + groupId + '"]');
            var $subGroups = $('.sub-group-checkbox[data-group="' + groupId + '"]');
            var totalSubGroups = $subGroups.length;
            var checkedSubGroups = $subGroups.filter(':checked').length;
            
            if (totalSubGroups > 0) {
                $groupCheckbox.prop('checked', checkedSubGroups === totalSubGroups);
            }
        }
        
        // Open the first group by default
        $('.permission-group:first .group-header').click();
    });
</script>
@endsection