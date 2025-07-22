
<?php $__env->startSection('title'); ?>
    Manage Users | <?php echo e($configuration->hotel_name.' '.$configuration->app_title); ?>

<?php $__env->stopSection(); ?>

<?php
// ======== STYLING CONFIGURATION ========
$titleFontSize = '18px';
$headerFontSize = '16px';
$contentFontSize = '14px';
$labelFontSize = '14px';
$cardHeaderBg = '#f8f9fa';
$cardBorderColor = '#e9ecef';
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
                            <h3 style="font-size: <?php echo e($titleFontSize); ?>;">
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
                            <?php if(session('success')): ?>
                                <div class="alert alert-success alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <?php echo e(session('success')); ?>

                                </div>
                            <?php endif; ?>
                            
                            <?php if(session('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <?php echo e(session('error')); ?>

                                </div>
                            <?php endif; ?>
                            
                            <!-- Back Button -->
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-md-12">
                                    <a href="<?php echo e(route('role-permissions.index')); ?>" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to Roles
                                    </a>
                                    <a href="<?php echo e(route('role-permissions.create-user')); ?>" class="btn btn-primary pull-right">
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
                                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($user->id); ?></td>
                                                    <td><?php echo e($user->name); ?></td>
                                                    <td><?php echo e($user->email); ?></td>
                                                    <td style="text-align: center;">
                                                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span style="font-size: 12px; padding: 3px 6px; background-color: #3498db; color: white; border-radius: 3px; margin-right: 3px;">
                                                                <?php echo e(ucwords(strtolower(str_replace('_', ' ', $role->name)))); ?>

                                                            </span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <a href="<?php echo e(route('role-permissions.edit-user', $user->id)); ?>" class="btn btn-primary btn-sm" style="font-size: 12px; margin-right: 5px; padding: 5px 8px;" title="Edit User">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
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
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#users-table').DataTable({
            responsive: false,
            order: [[0, 'asc']]
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/admin/role-permissions/manage-users.blade.php ENDPATH**/ ?>