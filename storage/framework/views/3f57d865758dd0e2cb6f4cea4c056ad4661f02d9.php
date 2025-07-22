
<?php $__env->startSection('title'); ?>
   System Logs | <?php echo e($configuration->hotel_name.' '.$configuration->app_title); ?>

<?php $__env->stopSection(); ?>

<?php
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
?>

<?php $__env->startSection('content'); ?>
<div class="right_col" role="main">
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile">
                        <div class="x_title">
                            <h3 style="font-size: <?php echo e($titleFontSize); ?>;">System & User Activity Logs</h3>
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
                                    <a href="#user_logs" aria-controls="user_logs" role="tab" data-toggle="tab" style="font-size: <?php echo e($tabFontSize); ?>; font-weight: <?php echo e($tabFontWeight); ?>;">
                                        <i class="fa fa-user"></i> User Activity Logs
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#system_logs" aria-controls="system_logs" role="tab" data-toggle="tab" style="font-size: <?php echo e($tabFontSize); ?>; font-weight: <?php echo e($tabFontWeight); ?>;">
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
                                                    <h4 style="font-size: <?php echo e($headerFontSize); ?>;">User Activity Logs</h4>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-hover datatable" id="user-logs-table">
                                                            <thead>
                                                                <tr>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">ID</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; width: 10%; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">User</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Action</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Description</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Created At</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Details</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $__currentLoopData = $userLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <tr>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e($log->id); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e($log->user ? $log->user->name : 'Unknown'); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">
                                                                        <span class="label 
                                                                            <?php if(strpos($log->action, 'create') !== false): ?> label-success
                                                                            <?php elseif(strpos($log->action, 'update') !== false): ?> label-primary
                                                                            <?php elseif(strpos($log->action, 'delete') !== false): ?> label-danger
                                                                            <?php else: ?> label-default
                                                                            <?php endif; ?>
                                                                        " style="font-size: <?php echo e($labelFontSize); ?>; padding: <?php echo e($actionLabelPadding); ?>; font-weight: <?php echo e($actionLabelFontWeight); ?>; color: #fff; text-shadow: 0 1px 1px rgba(0,0,0,0.2); background-color: 
                                                                            <?php if(strpos($log->action, 'create') !== false): ?> <?php echo e($createActionColor); ?>

                                                                            <?php elseif(strpos($log->action, 'update') !== false): ?> <?php echo e($updateActionColor); ?>

                                                                            <?php elseif(strpos($log->action, 'delete') !== false): ?> <?php echo e($deleteActionColor); ?>

                                                                            <?php else: ?> <?php echo e($defaultActionColor); ?>

                                                                            <?php endif; ?>; opacity: <?php echo e($actionLabelOpacity); ?>;">
                                                                            <?php echo e(ucwords(str_replace('_', ' ', $log->action))); ?>

                                                                        </span>
                                                                    </td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e(ucwords($log->description)); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e(\Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i')); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">
                                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#userLogModal<?php echo e($log->id); ?>" style="font-size: <?php echo e($buttonFontSize); ?>;">
                                                                            <i class="fa fa-search"></i> View
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                                    <h4 style="font-size: <?php echo e($headerFontSize); ?>;">System Logs</h4>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <!-- Status explanation note -->
                                                    <div class="alert alert-info" role="alert" style="font-size: <?php echo e($contentFontSize); ?>; margin-bottom: 15px; background-color: #d9edf7; border-color: #bce8f1; color: #31708f; font-weight: 600; padding: 12px 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                        <i class="fa fa-info-circle" style="margin-right: 8px; font-size: 16px;"></i> <strong>Note:</strong> In system logs, "status S" means "Success" and "status F" means "Failed".
                                                    </div>
                                                    
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-hover datatable" id="system-logs-table">
                                                            <thead>
                                                                <tr>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">ID</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Source</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Type</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Level</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Message</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Created At</th>
                                                                    <th style="font-size: <?php echo e($headerFontSize); ?>; font-weight: <?php echo e($tableHeaderFontWeight); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">Details</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $__currentLoopData = $systemLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <tr>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e($log->id); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e($log->source); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e($log->type); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">
                                                                        <span class="label 
                                                                            <?php if($log->level == 'error'): ?> label-danger
                                                                            <?php elseif($log->level == 'warning'): ?> label-warning
                                                                            <?php elseif($log->level == 'info'): ?> label-info
                                                                            <?php else: ?> label-default
                                                                            <?php endif; ?>
                                                                        " style="font-size: <?php echo e($labelFontSize); ?>; padding: <?php echo e($actionLabelPadding); ?>; font-weight: <?php echo e($actionLabelFontWeight); ?>; color: #fff; text-shadow: 0 1px 1px rgba(0,0,0,0.2); background-color: 
                                                                            <?php if($log->level == 'error'): ?> <?php echo e($errorLevelColor); ?>

                                                                            <?php elseif($log->level == 'warning'): ?> <?php echo e($warningLevelColor); ?>

                                                                            <?php elseif($log->level == 'info'): ?> <?php echo e($infoLevelColor); ?>

                                                                            <?php else: ?> <?php echo e($defaultLevelColor); ?>

                                                                            <?php endif; ?>; opacity: <?php echo e($actionLabelOpacity); ?>;">
                                                                            <?php echo e($log->level); ?>

                                                                        </span>
                                                                    </td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e($log->message); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;"><?php echo e(\Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i')); ?></td>
                                                                    <td style="font-size: <?php echo e($contentFontSize); ?>; vertical-align: <?php echo e($tableCellVerticalAlign); ?>; padding: <?php echo e($tableCellPadding); ?>;">
                                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#systemLogModal<?php echo e($log->id); ?>" style="font-size: <?php echo e($buttonFontSize); ?>;">
                                                                            <i class="fa fa-search"></i> View
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__currentLoopData = $userLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="userLogModal<?php echo e($log->id); ?>" tabindex="-1" role="dialog" aria-labelledby="userLogModalLabel<?php echo e($log->id); ?>">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="userLogModalLabel<?php echo e($log->id); ?>" style="font-size: 18px;">User Log Details #<?php echo e($log->id); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>User:</strong></label>
                            <p style="font-size: 16px;"><?php echo e($log->user ? $log->user->name : 'Unknown'); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Action:</strong></label>
                            <p style="font-size: 16px;"><?php echo e(ucwords(str_replace('_', ' ', $log->action))); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Description:</strong></label>
                            <p style="font-size: 16px;"><?php echo e(ucwords($log->description)); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Model Type:</strong></label>
                            <p style="font-size: 16px;"><?php echo e($log->model_type ?? 'N/A'); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Model ID:</strong></label>
                            <p style="font-size: 16px;"><?php echo e($log->model_id ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>IP Address:</strong></label>
                            <p style="font-size: 16px;"><?php echo e($log->ip_address); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>User Agent:</strong></label>
                            <p style="font-size: 16px;" class="text-muted"><?php echo e($log->user_agent); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Created At:</strong></label>
                            <p style="font-size: 16px;"><?php echo e(\Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i')); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 16px;"><strong>Updated At:</strong></label>
                            <p style="font-size: 16px;"><?php echo e(\Carbon\Carbon::parse($log->updated_at)->setTimezone('Asia/Makassar')->format('d M Y H:i')); ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if($log->old_data && $log->new_data): ?>
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
                                    <?php
                                        $oldData = $log->old_data ? json_decode($log->old_data, true) : [];
                                        $newData = $log->new_data ? json_decode($log->new_data, true) : [];
                                        $allKeys = array_unique(array_merge(array_keys($oldData ?: []), array_keys($newData ?: [])));
                                    ?>
                                    
                                    <?php $__currentLoopData = $allKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $oldValue = isset($oldData[$key]) ? (is_array($oldData[$key]) ? json_encode($oldData[$key], JSON_PRETTY_PRINT) : $oldData[$key]) : null;
                                        $newValue = isset($newData[$key]) ? (is_array($newData[$key]) ? json_encode($newData[$key], JSON_PRETTY_PRINT) : $newData[$key]) : null;
                                        $hasChanged = $oldValue !== $newValue && isset($oldData[$key]) && isset($newData[$key]);
                                    ?>
                                    <tr>
                                        <td style="font-size: 16px;"><strong><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></strong></td>
                                        <td style="font-size: 16px; <?php echo e($hasChanged ? 'background-color: #ffeeee;' : ''); ?>">
                                            <?php if(isset($oldData[$key])): ?>
                                                <?php if(is_array($oldData[$key])): ?>
                                                    <pre style="font-size: 16px;"><?php echo e(json_encode($oldData[$key], JSON_PRETTY_PRINT)); ?></pre>
                                                <?php else: ?>
                                                    <?php echo e($oldData[$key]); ?>

                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not set</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-size: 16px; <?php echo e($hasChanged ? 'background-color: #eeffee;' : ''); ?>">
                                            <?php if(isset($newData[$key])): ?>
                                                <?php if(is_array($newData[$key])): ?>
                                                    <pre style="font-size: 16px;"><?php echo e(json_encode($newData[$key], JSON_PRETTY_PRINT)); ?></pre>
                                                <?php else: ?>
                                                    <?php echo e($newData[$key]); ?>

                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not set</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php elseif($log->old_data || $log->new_data): ?>
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <h4 style="font-size: 18px;"><strong>Data <?php echo e($log->old_data ? 'Before' : 'After'); ?></strong></h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%; font-size: 16px;">Field</th>
                                        <th style="width: 70%; font-size: 16px;">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $data = $log->old_data ? json_decode($log->old_data, true) : json_decode($log->new_data, true);
                                    ?>
                                    
                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td style="font-size: 16px;"><strong><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></strong></td>
                                        <td style="font-size: 16px;">
                                            <?php if(is_array($value)): ?>
                                                <pre style="font-size: 16px;"><?php echo e(json_encode($value, JSON_PRETTY_PRINT)); ?></pre>
                                            <?php else: ?>
                                                <?php echo e($value); ?>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- System Log Modals -->
<?php $__currentLoopData = $systemLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="systemLogModal<?php echo e($log->id); ?>" tabindex="-1" role="dialog" aria-labelledby="systemLogModalLabel<?php echo e($log->id); ?>">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="systemLogModalLabel<?php echo e($log->id); ?>" style="font-size: <?php echo e($modalTitleFontSize); ?>;">System Log Details #<?php echo e($log->id); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: <?php echo e($modalLabelFontSize); ?>;"><strong>Source:</strong></label>
                            <p style="font-size: <?php echo e($modalContentFontSize); ?>;"><?php echo e($log->source); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: <?php echo e($modalLabelFontSize); ?>;"><strong>Type:</strong></label>
                            <p style="font-size: <?php echo e($modalContentFontSize); ?>;"><?php echo e($log->type); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: <?php echo e($modalLabelFontSize); ?>;"><strong>Level:</strong></label>
                            <p style="font-size: <?php echo e($modalContentFontSize); ?>;">
                                <span class="label 
                                    <?php if($log->level == 'error'): ?> label-danger
                                    <?php elseif($log->level == 'warning'): ?> label-warning
                                    <?php elseif($log->level == 'info'): ?> label-info
                                    <?php else: ?> label-default
                                    <?php endif; ?>
                                " style="font-size: <?php echo e($labelFontSize); ?>; padding: <?php echo e($actionLabelPadding); ?>; font-weight: <?php echo e($actionLabelFontWeight); ?>; color: #fff; text-shadow: 0 1px 1px rgba(0,0,0,0.2); background-color: 
                                    <?php if($log->level == 'error'): ?> <?php echo e($errorLevelColor); ?>

                                    <?php elseif($log->level == 'warning'): ?> <?php echo e($warningLevelColor); ?>

                                    <?php elseif($log->level == 'info'): ?> <?php echo e($infoLevelColor); ?>

                                    <?php else: ?> <?php echo e($defaultLevelColor); ?>

                                    <?php endif; ?>; opacity: <?php echo e($actionLabelOpacity); ?>;">
                                    <?php echo e($log->level); ?>

                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size: <?php echo e($modalLabelFontSize); ?>;"><strong>Created At:</strong></label>
                            <p style="font-size: <?php echo e($modalContentFontSize); ?>;"><?php echo e(\Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Makassar')->format('d M Y H:i')); ?></p>
                        </div>
                        <div class="form-group">
                            <label style="font-size: <?php echo e($modalLabelFontSize); ?>;"><strong>Message:</strong></label>
                            <p style="font-size: <?php echo e($modalContentFontSize); ?>;"><?php echo e($log->message); ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if($log->context): ?>
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <h5 style="font-size: <?php echo e($modalTitleFontSize); ?>;"><strong>Context Details</strong></h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%; font-size: <?php echo e($modalLabelFontSize); ?>;">Field</th>
                                        <th style="width: 70%; font-size: <?php echo e($modalLabelFontSize); ?>;">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $contextData = json_decode($log->context, true);
                                    ?>
                                    
                                    <?php if(is_array($contextData)): ?>
                                        <?php $__currentLoopData = $contextData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td style="font-size: <?php echo e($modalContentFontSize); ?>;"><strong><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></strong></td>
                                            <td style="font-size: <?php echo e($modalContentFontSize); ?>;">
                                                <?php if(is_array($value)): ?>
                                                    <?php
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
                                                    ?>
                                                    <pre style="font-size: <?php echo e($modalContentFontSize); ?>; max-height: 300px; overflow-y: auto;"><?php echo e(json_encode($formattedValue, JSON_PRETTY_PRINT)); ?></pre>
                                                <?php elseif(is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)): ?>
                                                    <?php echo e(\Carbon\Carbon::parse($value)->setTimezone('Asia/Makassar')->format('d M Y H:i')); ?>

                                                <?php else: ?>
                                                    <?php echo e(is_string($value) ? $value : json_encode($value)); ?>

                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" style="font-size: <?php echo e($modalContentFontSize); ?>;">
                                                <pre style="font-size: <?php echo e($modalContentFontSize); ?>; max-height: 400px; overflow-y: auto;"><?php echo e(json_encode(json_decode($log->context), JSON_PRETTY_PRINT)); ?></pre>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/logs/index.blade.php ENDPATH**/ ?>