<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>Menu</h3>
        <ul class="nav side-menu">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('2.1.1_view_dashboard')): ?>
            <li><a href="<?php echo e(url('/')); ?>"><i class="fa fa-home"></i>Dashboard</a></li>
            <?php endif; ?>
            <li><a><i class="fa fa-user"></i> Contact Management <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu" style="display: none;">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.1.1_view_contacts_list')): ?>
                    <li><a href="<?php echo e(url('contacts/list')); ?>">View Contacts</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.3.1_save_filter_contacts')): ?>
                    <li><a href="<?php echo e(url('contacts/filter')); ?>">Filter Contact</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.4.1_view_detail_incomplete')): ?>
                    <li><a href="<?php echo e(url('contacts/incomplete')); ?>">Incomplete Contact</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.5.1_download_template')): ?>
                    <li><a href="<?php echo e(url('contacts/external')); ?>">External Contacts</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.6.1_add_exclude_email')): ?>
                    <li><a href="<?php echo e(url('contacts/excluded/email')); ?>">Exclude Email / Domain </a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li><a><i class="fa fa-envelope"></i> Email Marketing <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('4.1.1_add_new_campaign')): ?>
                        <li><a href="<?php echo e(url('campaigns')); ?>">Campaign Management</a></li>
                    <?php endif; ?>    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('4.2.1_add_new_segment')): ?>
                        <li><a href="<?php echo e(url('segments')); ?>">Segment Management</a></li>
                    <?php endif; ?>
                        
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('5.1.1_view_email_pre_stay')): ?>
                        <li><a href="<?php echo e(url('email/config/prestay')); ?>">Pre-Stay Configuration</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('5.2.1_view_email_post_stay')): ?>
                        <li><a href="<?php echo e(url('email/config/poststay')); ?>">Post-Stay Configuration</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('5.3.1_view_email_birthday')): ?>
                        <li><a href="<?php echo e(url('email/config/birthday')); ?>">Birthday Configuration</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('5.4.1_view_email_miss_you')): ?>
                        <li><a href="<?php echo e(url('email/config/miss')); ?>">We Miss You Configuration</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('6.1.7_view_list_template')): ?>
                        <li><a href="<?php echo e(url('email/template')); ?>">Email Template</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('7.1.1_view_delivery_status')): ?>
                        <li><a href="<?php echo e(url('email/delivery/status')); ?>">Email Delivery Status</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li><a><i class="fa fa-envelope"></i> Pre Stay <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('8.1.1_view_prestay_contact_list')): ?>
                        <li><a href="<?php echo e(url('reservation')); ?>">Folio</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('8.2.5_view_pre_stay_promo_list')): ?>
                        <li><a href="<?php echo e(route('promo-configuration.index')); ?>">Pre-Stay Promo Configuration</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li><a><i class="fa fa-cogs"></i> System Management <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('1.1.3_manage_roles')): ?>
                        <li><a href="<?php echo e(route('role-permissions.index')); ?>">User Role Permission</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('1.1.1_view_preferences')): ?>
                    <li><a href="<?php echo e(url('preferences')); ?>">System Preferences</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo e(route('logs.index')); ?>">System & User Logs</a></li>
                </ul>
            </li>

            
            

        </ul>
    </div>
    

</div>
<?php /**PATH /home/jalakdev/www/crm/resources/views/layouts/_sidebar.blade.php ENDPATH**/ ?>