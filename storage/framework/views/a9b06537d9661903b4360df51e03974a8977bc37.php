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
								<h2>Contact List</h2>
							</div>
							<div class="row clearfix">
								<div class="col-lg-12">
								</div>
							</div>

							<div class="body">
								<table class="table table-bordered table-striped table-hover datatable responsive js-basic-example" id="datatable-responsive" width="100%">
									<thead class="bg-teal">
									<tr>
										<th class="align-center">#</th>
										<th class="align-center">Full Name</th>
										<th class="align-center">Birthday</th>
										<th class="align-center">Wedding Anniversary</th>
										<th class="align-center">Country</th>
										<th class="align-center">Area/Origin</th>
										<th class="align-center">Status</th>
										<th class="align-center">Campaign</th>
										<th class="align-center">Total Stays</th>
										<th class="align-center">Guest Type</th>
										<th class="align-center">Last Stay</th>
										<th class="align-center">Total Spending (Rp.)</th>
									</tr>
									</thead>
									<tbody>
									<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($contact->transaction->sum('revenue')>=0): ?>
                                        <tr class="align-center">
                                        <td><?php echo e($key+1); ?></td>
                                        <td>
                                            <?php if(!empty($contact->lname)): ?>
                                                <a href="<?php echo e(url('contacts/detail/').'/'.$contact->contactid); ?>" ><?php echo e(ucwords(strtolower($contact->fname)).' '.ucwords(strtolower($contact->lname))); ?></a>
                                            <?php else: ?>
                                                <a href="<?php echo e(url('contacts/detail/').'/'.$contact->contactid); ?>" ><?php echo e(ucwords(strtolower($contact->fname))); ?></a>
                                            <?php endif; ?>
                                            <?php if( $contact->birthday=='' ? '': \Carbon\Carbon::parse($contact->birthday)->format('m-d')==\Carbon\Carbon::now()->format('m-d')): ?>
                                                <i class="fa fa-birthday-cake " style="color: #009688" ></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($contact->birthday=='' ? "": \Carbon\Carbon::parse($contact->birthday)->format('M d')); ?></td>
                                        <td><?php echo e($contact->wedding_bday=='' ? "": \Carbon\Carbon::parse($contact->wedding_bday)->format('M d')); ?></td>
                                        <td>
                                            <?php
                                                $countryData = \App\Models\Country::where(env('COUNTRY_ISO'), $contact->country_id)->first();
                                            ?>
                                            
                                            <?php if($countryData): ?>
                                                <?php echo e($countryData['country']); ?>

                                                <img src="<?php echo e(asset('flags/blank.gif')); ?>" class="flag flag-<?php echo e(strtolower($countryData['iso2'])); ?> pull-right" alt="<?php echo e($countryData['country']); ?>" />
                                            <?php elseif($contact->country): ?>
                                                <?php echo e($contact->country->country); ?>

                                                <img src="<?php echo e(asset('flags/blank.gif')); ?>" class="flag flag-<?php echo e(strtolower($contact->country->iso2)); ?> pull-right" alt="<?php echo e($contact->country->country); ?>" />
                                            <?php else: ?>
                                                Unknown Country
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($contact->area); ?></td>
                                        <td>
                                            <?php if(isset($status)): ?>
                                                <?php echo e($status); ?>

                                            <?php elseif(count($contact->profilesfolio)<>0): ?>
                                                <?php if($contact->profilesfolio->max()->foliostatus=='I'): ?>
                                                    Inhouse
                                                <?php elseif($contact->profilesfolio->max()->foliostatus=='C'): ?>
                                                    Confirm
                                                <?php elseif($contact->profilesfolio->max()->foliostatus=='X'): ?>
                                                    Cancel
                                                <?php elseif($contact->profilesfolio->max()->foliostatus=='G'): ?>
                                                    Guaranteed
                                                <?php elseif($contact->profilesfolio->max()->foliostatus=='T'): ?>
                                                    Tentative
                                                <?php elseif($contact->profilesfolio->max()->foliostatus=='O'): ?>
                                                    Check Out
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>

                                        


                                        <td> <?php if(count($contact->campaign) <> 0): ?>
                                                    <i class="fa fa-envelope success"></i>
                                                    <?php echo e($contact->campaign->where('status','=','Sent')->count()); ?> Campaign
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if($contact->transaction_count): ?>
                                                <?php echo e($contact->transaction_count); ?>

                                            <?php elseif($contact->transaction->count()): ?>
                                                <?php echo e($contact->transaction->count()); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(($contact->transaction_count ?? $contact->transaction->count()) > 1): ?>
                                                <i class="fa fa-refresh"></i> Repeater
                                            <?php else: ?>
                                                New Guest
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo e($contact->profilesfolio->max('dateci')==NULL ? "": \Carbon\Carbon::parse($contact->profilesfolio->max('dateci'))->format('d M Y')); ?>

                                        </td>
                                        <td>
                                            <?php echo e(number_format($contact->transaction->sum('revenue'),0,'.',',')); ?>

                                        </td>
                                        
                                            
                                        
                                    </tr>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/contacts/list.blade.php ENDPATH**/ ?>