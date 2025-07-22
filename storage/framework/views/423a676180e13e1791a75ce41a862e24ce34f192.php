<?php $__env->startSection('title'); ?>
    Incomplete Contacts  | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>Incomplete Contact List</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>

                            <div class="body">
                                <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example dataTable" id="tbl" width="100%">
                                    <thead class="bg-teal">
                                    <tr>
                                        <th class="align-center">#</th>
                                        <th class="align-center">Folio</th>
                                        <th class="align-center">Folio Master</th>
                                        <th class="align-center">Resv. Date</th>
                                        <th class="align-center">C/I Date</th>
                                        <th class="align-center">C/O Date</th>
                                        <th class="align-center">ID Number</th>
                                        <th class="align-center">Title</th>
                                        <th class="align-center">Full Name</th>
                                        <th class="align-center">Email</th>
                                        <th class="align-center">Problems</th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.4.2_checked_incomplete_contacts')): ?>
                                        <th class="align-center">Checked</th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $incompletes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="tr align-center" id="<?php echo e($contact->folio); ?>" style="font-size: 11px">
                                            <td><?php echo e($key+1); ?></td>
                                            <td><?php echo e($contact->folio); ?></td>
                                            <td><?php echo e($contact->folio_master); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($contact->dateresv)->format('d/m/y')); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($contact->dateci)->format('d/m/y')); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($contact->dateco)->format('d/m/y')); ?></td>
                                            <td class="idnumber"><?php echo e($contact->idnumber); ?></td>
                                            <td><?php echo e($contact->salutation); ?></td>

                                            <td>
                                                   <?php if(!empty($contact->lname)): ?>
                                                       <?php echo e(ucwords(strtolower($contact->fname)).' '.ucwords(strtolower($contact->lname))); ?>

                                                   <?php else: ?>
                                                       <?php echo e(ucwords(strtolower($contact->fname))); ?>

                                                  <?php endif; ?>
                                                </td>
                                            <td><?php echo e($contact->email); ?></td>

                                               <?php
                                               $pr= explode(',',$contact->problems);
                                               echo "<td><ul class=' list-unstyled'>";
                                                foreach ($pr as $p){
                                                   echo "<li class=' list-group-item-danger'>- ".$p."</li>";
                                                }
                                               echo "</ul></td>";
                                                ?>
                                                <input type="hidden" id ="input<?php echo e($contact->folio); ?>" value="<?php echo e($contact->problems); ?>">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.4.2_checked_incomplete_contacts')): ?>
                                                    <td><input type="checkbox" class="chk" id="<?php echo e($contact->folio); ?>" name="check" value="checked" <?php echo e($contact->checked=='Y' ? 'checked':''); ?>></td>
                                                <?php endif; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>

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
        $(document).ready(function () {
            $('#tbl').DataTable({
				// "order": [[ 0, "desc" ]],
                "columnDefs": [
                    {"width":"2%","targets":0},
                    {"width":"3%","targets":1},
                    {"width":"3%","targets":2},
                    {"width":"5%","targets":3},
                    {"width":"5%","targets":4},
                    {"width":"5%","targets":5},
                    {"width":"5%","targets":6},
                    {"width":"3%","targets":7},
                    {"width":"7%","targets":8},
                    {"width":"8%","targets":9},
                    {"width":"11%","targets":10},
                    {"width":"4%","targets":11},
                ],
                "createdRow": function (r,d,i,c) {
                    var id =$(r).attr('id')
                    var val=$('#input'+id).val()
                    var ar = val.split(',')

                    if(ar.indexOf('ID Number empty or invalid format')>-1 || ar.indexOf(' ID Number empty or invalid format')>-1 || ar.indexOf('ID Number need to check')>-1 || ar.indexOf(' ID Number need to check')>-1){
                        $('td',r).eq(6).addClass('alert-error')
                    }
                    if(ar.indexOf('Email use OTA domain')>-1 || ar.indexOf(' Email use OTA domain')>-1 || ar.indexOf('Invalid email address')>-1 || ar.indexOf(' Invalid email address')>-1 || ar.indexOf('Online Travel Agent')>-1 || ar.indexOf(' Online Travel Agent')>-1 || ar.indexOf('Mailbox/account doesn\'t exists')>-1 || ar.indexOf(' Mailbox/account doesn\'t exists')>-1 || ar.indexOf('Domain doesn\'t exists')>-1 || ar.indexOf(' Domain doesn\'t exists')>-1 || ar.indexOf('MX cannot be contacted')>-1 || ar.indexOf(' MX cannot be contacted')>-1|| ar.indexOf('Mailbox not exist')>-1 || ar.indexOf(' Mailbox not exist')>-1){
                        $('td',r).eq(9).addClass('alert-error')
                    }
                    if(ar.indexOf('Title needs to be checked')>-1 || ar.indexOf(' Title needs to be checked')>-1){
                        $('td',r).eq(7).addClass('alert-error')
                    }
                    if(ar.indexOf('Possible duplicate email address/contact')>-1 || ar.indexOf(' Possible duplicate email address/contact')>-1){
                        $(r).addClass('bg-danger')
                    }
                }
            })
        })
        $('.chk').each(function () {
            $(this).on('click',function () {

                var trid=$(this).attr('id')
                swal({
                        title: "Are you sure?",
                        text:"You have updated the PMS data",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#00b35d',
                        confirmButtonText: 'Yes',
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function (isConfirm) {

                        if (isConfirm) {
                            swal("Success", "Data Updated");
                            $.ajax({
                                url:'incomplete/update',
                                type:'POST',
                                data:{
                                    _token:'<?php echo e(csrf_token()); ?>',
                                    id:trid
                                },success:function (d) {
                                    if(d==='success') {
                                        $("tr#"+trid).fadeOut();
                                    }
                                }
                            })
                        } else {
                            swal("Cancelled", "Data Update Cancelled", "error");
                        }
                    })
            })
        })


    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/contacts/incomplete.blade.php ENDPATH**/ ?>