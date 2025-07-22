@extends('layouts.master')
@section('title')
   System Logs | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@php
// ======== STYLING CONFIGURATION ========
// General Font Sizes
$titleFontSize = '16x';
$headerFontSize = '16px';
$contentFontSize = '12px';
$labelFontSize = '12px';

// Table Header Styling
$tableHeaderFontWeight = '600';

// Action Label Colors
$createActionColor = '#2d9d3a';
$updateActionColor = '#2980b9';
$deleteActionColor = '#c0392b';
$defaultActionColor = '#555';
$actionLabelOpacity = '0.95';
$actionLabelFontWeight = '500';
$actionLabelPadding = '5px 8px';

// Table Cell Styling
$tableCellVerticalAlign = 'middle';
$tableCellPadding = '10px 8px';

// System Log Level Colors
$errorLevelColor = '#d9534f';
$warningLevelColor = '#f0ad4e';
//$infoLevelColor = '#5bc0de';
$infoLevelColor = '#2980b9';
$defaultLevelColor = '#777';

// Button Styling
$buttonFontSize = '12px';

// Tab Navigation
$tabFontSize = '14px';
$tabFontWeight = '600';

// Modal Styling
$modalTitleFontSize = '16px';
$modalLabelFontSize = '14px';
$modalContentFontSize = '12px';
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
                            <h3 style="font-size: {{ $titleFontSize }};">System & User Activity Logs</h3>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#user_logs" aria-controls="user_logs" role="tab" data-toggle="tab" style="font-size: {{ $tabFontSize }}; font-weight: {{ $tabFontWeight }};">
                                        <i class="fa fa-user"></i> User Activity Logs
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#system_logs" aria-controls="system_logs" role="tab" data-toggle="tab" style="font-size: {{ $tabFontSize }}; font-weight: {{ $tabFontWeight }};">
                                        <i class="fa fa-server"></i> System Logs
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- User Logs Tab -->
                                <div role="tabpanel" class="tab-pane active" id="user_logs">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="x_panel">
                                                <div class="x_title">
                                                    <h4 style="font-size: {{ $headerFontSize }};">User Activity Logs</h4>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-hover datatable" id="user-logs-table">
                                                            <thead>
                                                                <tr>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">ID</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; width: 10%; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">User</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Action</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Description</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Created At</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Details</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($userLogs as $log)
                                                                <tr>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $log->id }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $log->user ? $log->user->name : 'Unknown' }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">
                                                                        <span class="label 
                                                                            @if(strpos($log->action, 'create') !== false) label-success
                                                                            @elseif(strpos($log->action, 'update') !== false) label-primary
                                                                            @elseif(strpos($log->action, 'delete') !== false) label-danger
                                                                            @else label-default
                                                                            @endif
                                                                        " style="font-size: {{ $labelFontSize }}; padding: {{ $actionLabelPadding }}; font-weight: {{ $actionLabelFontWeight }}; color: #fff; text-shadow: 0 1px 1px rgba(0,0,0,0.2); background-color: 
                                                                            @if(strpos($log->action, 'create') !== false) {{ $createActionColor }}
                                                                            @elseif(strpos($log->action, 'update') !== false) {{ $updateActionColor }}
                                                                            @elseif(strpos($log->action, 'delete') !== false) {{ $deleteActionColor }}
                                                                            @else {{ $defaultActionColor }}
                                                                            @endif; opacity: {{ $actionLabelOpacity }};">
                                                                            {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                                                        </span>
                                                                    </td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ ucwords($log->description) }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i') }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">
                                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#userLogModal{{ $log->id }}" style="font-size: {{ $buttonFontSize }};">
                                                                            <i class="fa fa-search"></i> View
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

                                <!-- System Logs Tab -->
                                <div role="tabpanel" class="tab-pane" id="system_logs">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="x_panel">
                                                <div class="x_title">
                                                    <h4 style="font-size: {{ $headerFontSize }};">System Logs</h4>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <!-- Status explanation note -->
                                                    <div class="alert alert-info" role="alert" style="font-size: {{ $contentFontSize }}; margin-bottom: 15px; background-color: #d9edf7; border-color: #bce8f1; color: #31708f; font-weight: 600; padding: 12px 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                        <i class="fa fa-info-circle" style="margin-right: 8px; font-size: 16px;"></i> <strong>Note:</strong> In system logs, "status S" means "Success" and "status F" means "Failed".
                                                    </div>
                                                    
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-hover datatable" id="system-logs-table">
                                                            <thead>
                                                                <tr>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">ID</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Source</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Type</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Level</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Message</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Created At</th>
                                                                    <th style="font-size: {{ $headerFontSize }}; font-weight: {{ $tableHeaderFontWeight }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">Details</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($systemLogs as $log)
                                                                <tr>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $log->id }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $log->source }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $log->type }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">
                                                                        <span class="label 
                                                                            @if($log->level == 'error') label-danger
                                                                            @elseif($log->level == 'warning') label-warning
                                                                            @elseif($log->level == 'info') label-info
                                                                            @else label-default
                                                                            @endif
                                                                        " style="font-size: {{ $labelFontSize }}; padding: {{ $actionLabelPadding }}; font-weight: {{ $actionLabelFontWeight }}; color: #fff; text-shadow: 0 1px 1px rgba(0,0,0,0.2); background-color: 
                                                                            @if($log->level == 'error') {{ $errorLevelColor }}
                                                                            @elseif($log->level == 'warning') {{ $warningLevelColor }}
                                                                            @elseif($log->level == 'info') {{ $infoLevelColor }}
                                                                            @else {{ $defaultLevelColor }}
                                                                            @endif; opacity: {{ $actionLabelOpacity }};">
                                                                            {{ $log->level }}
                                                                        </span>
                                                                    </td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ $log->message }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i') }}</td>
                                                                    <td style="font-size: {{ $contentFontSize }}; vertical-align: {{ $tableCellVerticalAlign }}; padding: {{ $tableCellPadding }};">
                                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#systemLogModal{{ $log->id }}" style="font-size: {{ $buttonFontSize }};">
                                                                            <i class="fa fa-search"></i> View
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
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- User Log Modals -->
@foreach($userLogs as $log)
<div class="modal fade" id="userLogModal{{ $log->id }}" tabindex="-1" role="dialog" aria-labelledby="userLogModalLabel{{ $log->id }}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="userLogModalLabel{{ $log->id }}" style="font-size: 18px;">User Log Details #{{ $log->id }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>User:</strong></label>
                            <p style="font-size: 16px;">{{ $log->user ? $log->user->name : 'Unknown' }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Action:</strong></label>
                            <p style="font-size: 16px;">{{ ucwords(str_replace('_', ' ', $log->action)) }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Description:</strong></label>
                            <p style="font-size: 16px;">{{ ucwords($log->description) }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Model Type:</strong></label>
                            <p style="font-size: 16px;">{{ $log->model_type ?? 'N/A' }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Model ID:</strong></label>
                            <p style="font-size: 16px;">{{ $log->model_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>IP Address:</strong></label>
                            <p style="font-size: 16px;">{{ $log->ip_address }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>User Agent:</strong></label>
                            <p style="font-size: 16px;" class="text-muted">{{ $log->user_agent }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Created At:</strong></label>
                            <p style="font-size: 16px;">{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i') }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Updated At:</strong></label>
                            <p style="font-size: 16px;">{{ \Carbon\Carbon::parse($log->updated_at)->setTimezone('Asia/Makassar')->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                @if($log->old_data && $log->new_data)
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <h4 style="font-size: 18px;"><strong>Data Changes</strong></h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 20%; font-size: 16px;">Field</th>
                                        <th style="width: 40%; font-size: 16px;">Old Value</th>
                                        <th style="width: 40%; font-size: 16px;">New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $oldData = $log->old_data ? json_decode($log->old_data, true) : [];
                                        $newData = $log->new_data ? json_decode($log->new_data, true) : [];
                                        $allKeys = array_unique(array_merge(array_keys($oldData ?: []), array_keys($newData ?: [])));
                                    @endphp
                                    
                                    @foreach($allKeys as $key)
                                    @php
                                        $oldValue = isset($oldData[$key]) ? (is_array($oldData[$key]) ? json_encode($oldData[$key], JSON_PRETTY_PRINT) : $oldData[$key]) : null;
                                        $newValue = isset($newData[$key]) ? (is_array($newData[$key]) ? json_encode($newData[$key], JSON_PRETTY_PRINT) : $newData[$key]) : null;
                                        $hasChanged = $oldValue !== $newValue && isset($oldData[$key]) && isset($newData[$key]);
                                    @endphp
                                    <tr>
                                        <td style="font-size: 16px;"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                                        <td style="font-size: 16px; {{ $hasChanged ? 'background-color: #ffeeee;' : '' }}">
                                            @if(isset($oldData[$key]))
                                                @if(is_array($oldData[$key]))
                                                    <pre style="font-size: 16px;">{{ json_encode($oldData[$key], JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $oldData[$key] }}
                                                @endif
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td style="font-size: 16px; {{ $hasChanged ? 'background-color: #eeffee;' : '' }}">
                                            @if(isset($newData[$key]))
                                                @if(is_array($newData[$key]))
                                                    <pre style="font-size: 16px;">{{ json_encode($newData[$key], JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $newData[$key] }}
                                                @endif
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @elseif($log->old_data || $log->new_data)
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <h4 style="font-size: 18px;"><strong>Data {{ $log->old_data ? 'Before' : 'After' }}</strong></h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%; font-size: 16px;">Field</th>
                                        <th style="width: 70%; font-size: 16px;">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $data = $log->old_data ? json_decode($log->old_data, true) : json_decode($log->new_data, true);
                                    @endphp
                                    
                                    @foreach($data as $key => $value)
                                    <tr>
                                        <td style="font-size: 16px;"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                                        <td style="font-size: 16px;">
                                            @if(is_array($value))
                                                <pre style="font-size: 16px;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- System Log Modals -->
@foreach($systemLogs as $log)
<div class="modal fade" id="systemLogModal{{ $log->id }}" tabindex="-1" role="dialog" aria-labelledby="systemLogModalLabel{{ $log->id }}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="systemLogModalLabel{{ $log->id }}" style="font-size: {{ $modalTitleFontSize }};">System Log Details #{{ $log->id }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: {{ $modalLabelFontSize }};"><strong>Source:</strong></label>
                            <p style="font-size: {{ $modalContentFontSize }};">{{ $log->source }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: {{ $modalLabelFontSize }};"><strong>Type:</strong></label>
                            <p style="font-size: {{ $modalContentFontSize }};">{{ $log->type }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: {{ $modalLabelFontSize }};"><strong>Level:</strong></label>
                            <p style="font-size: {{ $modalContentFontSize }};">
                                <span class="label 
                                    @if($log->level == 'error') label-danger
                                    @elseif($log->level == 'warning') label-warning
                                    @elseif($log->level == 'info') label-info
                                    @else label-default
                                    @endif
                                " style="font-size: {{ $labelFontSize }}; padding: {{ $actionLabelPadding }}; font-weight: {{ $actionLabelFontWeight }}; color: #fff; text-shadow: 0 1px 1px rgba(0,0,0,0.2); background-color: 
                                    @if($log->level == 'error') {{ $errorLevelColor }}
                                    @elseif($log->level == 'warning') {{ $warningLevelColor }}
                                    @elseif($log->level == 'info') {{ $infoLevelColor }}
                                    @else {{ $defaultLevelColor }}
                                    @endif; opacity: {{ $actionLabelOpacity }};">
                                    {{ $log->level }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: {{ $modalLabelFontSize }};"><strong>Created At:</strong></label>
                            <p style="font-size: {{ $modalContentFontSize }};">{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i') }}</p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: {{ $modalLabelFontSize }};"><strong>Message:</strong></label>
                            <p style="font-size: {{ $modalContentFontSize }};">{{ $log->message }}</p>
                        </div>
                    </div>
                </div>
                
                @if($log->context)
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <h5 style="font-size: {{ $modalTitleFontSize }};"><strong>Context Details</strong></h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%; font-size: {{ $modalLabelFontSize }};">Field</th>
                                        <th style="width: 70%; font-size: {{ $modalLabelFontSize }};">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $contextData = json_decode($log->context, true);
                                    @endphp
                                    
                                    @if(is_array($contextData))
                                        @foreach($contextData as $key => $value)
                                        <tr>
                                            <td style="font-size: {{ $modalContentFontSize }};"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                                            <td style="font-size: {{ $modalContentFontSize }};">
                                                @if(is_array($value))
                                                    @php
                                                        // Check if there are date fields in the array and format them
                                                        $formattedValue = $value;
                                                        foreach($formattedValue as $subKey => $subValue) {
                                                            if(is_string($subValue) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $subValue)) {
                                                                try {
                                                                    $formattedValue[$subKey] = \Carbon\Carbon::parse($subValue)->setTimezone('Asia/Makassar')->format('d M Y H:i');
                                                                } catch(\Exception $e) {
                                                                    // Keep original if parsing fails
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <pre style="font-size: {{ $modalContentFontSize }}; max-height: 300px; overflow-y: auto;">{{ json_encode($formattedValue, JSON_PRETTY_PRINT) }}</pre>
                                                @elseif(is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value))
                                                    {{ \Carbon\Carbon::parse($value)->setTimezone('Asia/Makassar')->format('d M Y H:i') }}
                                                @else
                                                    {{ is_string($value) ? $value : json_encode($value) }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" style="font-size: {{ $modalContentFontSize }};">
                                                <pre style="font-size: {{ $modalContentFontSize }}; max-height: 400px; overflow-y: auto;">{{ json_encode(json_decode($log->context), JSON_PRETTY_PRINT) }}</pre>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize datatables with responsive features
        var userLogsTable = $('#user-logs-table').DataTable({
            responsive: true,
            order: [[0, 'desc']], // Sort by ID (newest first)
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columnDefs: [
                { targets: 0, width: '5%' },   // ID column
                { targets: 1, width: '8%' },   // User column - reduced width
                { targets: 2, width: '10%' },  // Action column
                { targets: 3, width: '35%' },  // Description column
                { targets: 4, width: '12%' },  // Created At column
                { targets: 5, width: '8%', className: 'text-center' }   // Details column
            ],
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true
        });
        
        var systemLogsTable = $('#system-logs-table').DataTable({
            responsive: true,
            order: [[0, 'desc']], // Sort by ID (newest first)
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columnDefs: [
                { targets: 0, width: '5%' },   // ID column
                { targets: 1, width: '10%' },  // Source column
                { targets: 2, width: '8%' },   // Type column
                { targets: 3, width: '8%' },   // Level column
                { targets: 4, width: '35%' },  // Message column
                { targets: 5, width: '12%' },  // Created At column
                { targets: 6, width: '8%', className: 'text-center' }   // Details column
            ],
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true
        });
        
        // Function to properly adjust tables
        function adjustTables() {
            setTimeout(function() {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }, 10);
            
            // Double-check adjustment after a longer delay to catch any late rendering
            setTimeout(function() {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }, 200);
        }
        
        // Handle tab change to redraw datatables
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            adjustTables();
            
            // Force window resize event which helps DataTables recalculate dimensions
            $(window).trigger('resize');
        });
        
        // Fix for modal and tab interaction issues
        $('.modal').on('hidden.bs.modal', function () {
            // Clean up any modal-related styles
            $('body').css({
                'padding-right': '',
                'overflow': ''
            });
            
            // Remove modal-open class if it's still there
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            
            // Adjust tables after modal is fully closed
            adjustTables();
        });
        
        // Fix for switching tabs while modal is open
        $('a[data-toggle="tab"]').on('click', function(e) {
            // If any modal is open, close it before switching tabs
            if ($('body').hasClass('modal-open') || $('.modal.in').length > 0) {
                e.preventDefault(); // Prevent the tab from activating immediately
                
                // Hide all open modals
                $('.modal').modal('hide');
                
                // Clean up modal artifacts
                $('body').css({
                    'padding-right': '',
                    'overflow': ''
                });
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                
                // Wait for modal to fully close before switching tab
                setTimeout(function() {
                    // Now activate the tab that was clicked
                    $(e.target).tab('show');
                }, 300);
                
                return false;
            }
        });
        
        // Additional fix: adjust tables when window is resized
        $(window).on('resize', function() {
            adjustTables();
        });
        
        // Initial adjustment
        adjustTables();
    });
</script>
@endsection