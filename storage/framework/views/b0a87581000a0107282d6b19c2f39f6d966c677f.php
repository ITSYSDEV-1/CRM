<?php $__env->startSection('title'); ?>
    Contact List | <?php echo e(\App\Models\Configuration::first()->hotel_name.' '.\App\Models\Configuration::first()->app_title); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>Prestay Contact List</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('8.1.1_view_prestay_contact_list')): ?>
                            <div class="body">
                                <table style="font-size: 13px"
                                       class="table table-bordered table-striped table-hover responsive"
                                       id="reservationTable" width="100%">
                                    <thead class="bg-teal">
                                    <tr class="tr align-center">
                                        <th class="align-center">No</th>
                                        <th class="align-center">Folio Master</th>
                                        <th class="align-center">Full Name</th>
                                        <th class="align-center">Email</th>
                                        <th class="align-center">CI Date</th>
                                        <th class="align-center">CO Date</th>
                                        <th class="align-center">Room</th>
                                        <th class="align-center">Room Type</th>
                                        <th class="align-center">Status</th>
                                        <th class="align-center">Booking Source</th>
                                        <th class="align-center">Pre Stay Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>

                                        
                                            <td><?php echo e($key+1); ?></td>
                                            <td><?php echo e($contact->folio_master); ?></td>
                                            <td><?php echo e(rtrim(strtoupper($contact->salutation), '.')); ?>. <?php echo e($contact->fname); ?> <?php echo e($contact->lname); ?></td>
                                            <td><?php echo e($contact->email); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($contact->dateci)->format('d M Y')); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($contact->dateco)->format('d M Y')); ?></td>
                                            <td><?php echo e($contact->room); ?></td>
                                            <td><?php echo e($contact->roomtype); ?></td>
                                            <td>
                                                <?php if($contact->foliostatus=='O'): ?>
                                                    Checked Out
                                                <?php elseif($contact->foliostatus=='I'): ?>
                                                    Inhouse
                                                <?php elseif($contact->foliostatus=='X'): ?>
                                                    Cancel
                                                <?php elseif($contact->foliostatus=='G'): ?>
                                                    Guaranteed
                                                <?php else: ?>
                                                    Confirm
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($contact->source); ?></td>
                                            <td style="text-align: center">
                                                <?php if($contact->prestay_status != null and $contact->prestay_status['next_action'] != 'NOACTION'): ?>
                                                    <?php if($contact->prestay_status['next_action'] == 'SENDTOWEB'): ?>
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                    <?php elseif($contact->prestay_status['next_action'] == 'FETCHFROMWEB' && $contact->sendtoguest_at['sendtoguest_at'] == null): ?>
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                    <?php elseif($contact->prestay_status['next_action'] == 'FETCHFROMWEB' && $contact->sendtoguest_at['sendtoguest_at'] != null): ?>
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                        <i class="fa fa-envelope-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data sent to guest"></i>
                                                    <?php elseif($contact->prestay_status['next_action'] == 'COMPLETED'): ?>
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                        <i class="fa fa-envelope-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data sent to guest"></i>
                                                        <?php if($contact->registration_code != null): ?>
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('8.1.2_download_form_registration')): ?>
                                                                <a href="<?php echo e(route('registrationformprint',$contact->registration_code['registration_code'])); ?>"><i class="fa fa-file-pdf-o" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Pre Stay Completed, click to generate registration form"></i></a>
                                                            <?php else: ?>
                                                                <i class="fa fa-file-pdf-o" style="font-size: x-large; color: #ccc;" data-toggle="tooltip" data-placement="top" title="No permission to download registration form"></i>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('8.1.2_download_form_registration')): ?>
                                                                <a href="#"><i class="fa fa-file-pdf-o" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Pre Stay Completed, click to generate registration form"></i></a>
                                                            <?php else: ?>
                                                                <i class="fa fa-file-pdf-o" style="font-size: x-large; color: #ccc;" data-toggle="tooltip" data-placement="top" title="No permission to download registration form"></i>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <i class="fa fa-times-circle" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Pre Stay Canceled"></i>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php echo e($contact->prestay_status = '-'); ?>

                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                                <div class="body">
                                    
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('8.1.1_view_prestay_contact_list')): ?>
    <script>
        $(document).ready(function () {
            $('#reservationTable').DataTable();
        });
    </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/reservation/list.blade.php ENDPATH**/ ?>