<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            <div class="navbar navbar-brand navbar-dark">
                <h1><?php echo e($configuration->hotel_name); ?></h1>
            </div>

            <!-- Email Quota Info -->
            <div class="email-quota-info-modern">
                <div class="quota-badge quota-brand" title="Sharing Account">RCD-RMS</div>
                <div class="quota-badge quota-today" title="Today (<?php echo e(now()->format('d M Y')); ?>) | Limit: 3.000/day">
                    Today: <span class="quota-number"><?php echo e(number_format($quotaInfo['today_quota']['used'], 0, ',', '.')); ?>/<?php echo e(number_format($quotaInfo['today_quota']['remaining'], 0, ',', '.')); ?></span>
                </div>
                <div class="quota-badge quota-period" title="Period: <?php echo e(Carbon\Carbon::parse($quotaInfo['billing_cycle']['start'])->format('d M Y')); ?> - <?php echo e(Carbon\Carbon::parse($quotaInfo['billing_cycle']['end'])->format('d M Y')); ?> | Total Limit: 150.000">
                    Period: <span class="quota-number"><?php echo e(number_format($quotaInfo['quota_used'], 0, ',', '.')); ?>/<?php echo e(number_format($quotaInfo['quota_remaining'], 0, ',', '.')); ?></span>
                </div>
            </div>


            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <?php echo e(Auth::user() ? Auth::user()->name:''); ?>

                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <li><a href="<?php echo e(route('profile.index')); ?>"><i class="fa fa-user pull-right"></i> Profile</a></li>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('1.1.1_view_preferences')): ?>
                        <!-- <li><a href="<?php echo e(url('preferences')); ?>"><i class="fa fa-cog pull-right"></i> Preferences</a></li> -->
                        <?php endif; ?>
                        <li><a href="#" onclick="$('#logout').submit()"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                    </ul>
                    <form id="logout" method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                    </form>
                </li>


            </ul>
        </nav>
    </div>
</div>

<style>
    .email-quota-info-modern {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        margin-right: 20px;
        float: right;
        font-family: 'Segoe UI', sans-serif;
        font-size: 13px;
    }

    .quota-badge {
        background: #f1f5f9;
        color: #333;
        padding: 5px 10px;
        border-radius: 999px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .quota-badge:hover {
        background: #e2e8f0;
    }

    .quota-brand {
        background: #3498db;
        color: white;
        font-weight: bold;
    }

    .quota-brand:hover {
        background: white;
        color: #3498db;
    }

    .quota-number {
        font-weight: 600;
        color: #2563eb;
    }
</style><?php /**PATH /home/jalakdev/www/crm/resources/views/layouts/_navbar.blade.php ENDPATH**/ ?>