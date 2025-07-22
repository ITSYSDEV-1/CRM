<?php $__env->startSection('title'); ?>
    Contact Details | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
// Helper functions untuk pengecekan data
function getContactData(data, field, defaultValue = '') {
    if (!data || !data[0]) return defaultValue;
    return data[0][field] || defaultValue;
}

function getCompanyData(data, field) {
    if (!data || !data[0] || !data[0][field] || data[0][field].isEmpty()) {
        return null;
    }
    return data[0][field][0].pivot.value;
}

function getTransactionData(data) {
    if (!data || !data[0] || !data[0].transaction || data[0].transaction.isEmpty()) {
        return {
            count: 0,
            sum: 0,
            average: 0
        };
    }
    
    let sum = 0;
    data[0].transaction.forEach(spending => {
        sum += spending.revenue;
    });
    
    return {
        count: data[0].transaction.length,
        sum: sum,
        average: sum / data[0].transaction.length
    };
}

function formatCurrency(amount) {
    return "Rp " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Inisialisasi data saat dokumen siap
document.addEventListener('DOMContentLoaded', function() {
    const contactData = <?php echo json_encode($data, 15, 512) ?>;
    
    // Update form fields
    const formFields = {
        'fname': getContactData(contactData, 'fname'),
        'lname': getContactData(contactData, 'lname'),
        'salutation': getContactData(contactData, 'salutation'),
        'marital_status': getContactData(contactData, 'marital_status'),
        'gender': getContactData(contactData, 'gender'),
        'email': getContactData(contactData, 'email'),
        'birthday': getContactData(contactData, 'birthday')
    };
    
    // Update company fields
    const companyFields = [
        'company_name', 'company_address', 'company_phone', 
        'company_email', 'company_status', 'company_type',
        'company_area', 'company_nationality', 'company_fax'
    ];
    
    // Set form values
    Object.keys(formFields).forEach(field => {
        const element = document.querySelector(`[name="${field}"]`);
        if (element) {
            element.value = formFields[field];
        }
    });
    
    // Set company values
    companyFields.forEach(field => {
        const element = document.querySelector(`[name="${field}"]`);
        if (element) {
            element.value = getCompanyData(contactData, field.replace('company_', 'company'));
        }
    });
    
    // Update transaction statistics
    const transactionStats = getTransactionData(contactData);
    
    // Update stays/nights count
    const staysElement = document.querySelector('.tile_stats_count .count.green');
    if (staysElement) {
        staysElement.textContent = `${transactionStats.count} / ${totalnight}`;
    }
    
    // Update total spending
    const spendingElement = document.querySelector('.tile_stats_count:nth-child(2) .count.blue');
    if (spendingElement) {
        spendingElement.textContent = formatCurrency(transactionStats.sum);
    }
    
    // Update average spending
    const avgSpendingElement = document.querySelector('.tile_stats_count:nth-child(3) .count.blue');
    if (avgSpendingElement) {
        avgSpendingElement.textContent = formatCurrency(transactionStats.average);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="x_panel tile " >
                        <div class="panel-heading ">
                                <?php if(isset($data[0])): ?>
                                <?php if(!empty($data[0]->excluded->reason)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                    <span class="sr-only">Error:</span>
                                    <strong>This contact is listed in exclusion list</strong>
                                  </div>
                                <?php endif; ?>
                            <h2> <?php echo e($data[0]->fname.' '.$data[0]->lname); ?>   </h2>

                            <?php if(!empty($data[0]->country->country)): ?>
                                <img src="<?php echo e(asset('flags/blank.gif')); ?>" class="flag flag-<?php echo e(strtolower($data[0]->country->iso2)); ?>" alt="<?php echo e($data[0]->country->country); ?>" />
                            <?php endif; ?>
                            <?php else: ?>
                            <h2>No Contact Data Available</h2>
                            <?php endif; ?>
                            <br>
                            <br>

                        </div>
                        <div class="x_content" style="height:120px;">
                            <div class="row tile_count">

                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-user"></i>  STAYS/NIGHTS </span>
                                    <div class="count green">
                                        <?php if(empty($data[0]->transaction) ): ?>
                                            0
                                        <?php else: ?>

                                            <?php echo e(count($data[0]->transaction)); ?> / <?php echo e($totalnight); ?>







                                        <?php endif; ?>
                                    </div>

                                </div>
                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-money"></i> TOTAL SPENDING</span>
                                    <div class="count blue">

                                        <?php if(isset($data[0]) && !$data[0]->transaction->isEmpty()): ?>
                                            <?php
                                                $sum=0;
                                                foreach($data[0]->transaction as $spending){
                                                    $total=$spending->revenue;
                                                    $sum+=$total;
                                                }
                                            ?>
                                            <?php echo e("Rp " . number_format($sum,0,',','.')); ?>

                                        <?php else: ?>
                                          Rp. 0
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-percent"></i> AVG SPENT / STAY</span>
                                    <div class="count blue">
                                        <?php if(isset($data[0]) && !$data[0]->transaction->isEmpty()): ?>
                                            <?php
                                                $sum=0;
                                                    foreach($data[0]->transaction as $spending){
                                                        $total=$spending->revenue;
                                                        $sum+=$total;
                                                    }
                                            ?>
                                            <?php echo e("Rp " . number_format($sum/count($data[0]->transaction),0,',','.')); ?>

                                        <?php else: ?>
                                           Rp. 0
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-calendar"></i>
                                        <?php if(isset($data[0]) && !$data[0]->birthday==NULL): ?>
                                            BIRTHDAY  <?php echo e(\Carbon\Carbon::parse($data[0]->birthday)->format('d M')); ?>

                                        <?php else: ?>
                                            BIRTHDAY
                                        <?php endif; ?>
                                    </span>
                                    <div class="count blue">
                                       <h4><?php
                                            if(isset($data[0]) && isset($data[0]->birthday)) {
                                                $datenow = \Carbon\Carbon::now('Asia/Makassar')->day;
                                                $datebday = \Carbon\Carbon::parse($data[0]->birthday)->day;
                                                $monthnow = \Carbon\Carbon::now('Asia/Makassar')->month;
                                                $monthbday = \Carbon\Carbon::parse($data[0]->birthday)->month;
                                                $bday = \Carbon\Carbon::create(\Carbon\Carbon::now()->year, $monthbday, $datebday);
                                                $now = \Carbon\Carbon::create(\Carbon\Carbon::now()->year, $monthnow, $datenow);

                                                if ($bday > $now) {
                                                    $day = $bday->diffInDays($now);
                                                    echo 'Birthday in '. $day .' day/s';
                                                } elseif ($bday < $now) {
                                                    $y = \Carbon\Carbon::now('Asia/Makassar')->addYear()->year;
                                                    $next = \Carbon\Carbon::create($y, $monthbday, $datebday);
                                                    $day = $next->diffInDays(\Carbon\Carbon::now('Asia/Makassar'));
                                                    echo 'Birthday in '. $day .' day/s';
                                                } else {
                                                    echo 'Today';
                                                }
                                            } else {
                                                echo '';
                                            }
                                        ?>
                                       </h4>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                        <div class="panel-group full-body" id="accordion_18" role="tablist" aria-multiselectable="true">
                                            <div class="panel">
                                                <div class="panel-heading" role="tab" id="headingOne_18">
                                                    <h4 class="panel-title ">
                                                        <a class="collapsed teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseOne_18" aria-expanded="true" aria-controls="collapseOne_18">
                                                            <i class="fa fa-user"></i> PERSONAL INFORMATION
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapseOne_18" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_18">
                                                    <div class="panel-body">
                                                        <ul class="nav nav-tabs" role="tablist">
                                                            <li role="presentation" class="active">
                                                                <a href="#profile_info" data-toggle="tab">
                                                                    <i class="fa fa-user"></i> PROFILE
                                                                </a>
                                                            </li>
                                                            <li role="presentation">
                                                                <a href="#messages_with_icon_title" data-toggle="tab">
                                                                    <i class="fa fa-hotel"></i> STAY
                                                                </a>
                                                            </li>
                                                            <li role="presentation">
                                                                <a href="#settings_with_icon_title" data-toggle="tab">
                                                                    <i class="fa fa-mail-forward"></i> CAMPAIGNS
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <!-- Tab panes -->
                                                        <div class="tab-content">
                                                            <div role="tabpanel" class="tab-pane fade in active" id="profile_info">
                                                                <div class="row clearfix">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <div class="card">
                                                                            <div class="x_content">
                                                                                    <?php if(!empty($action) && !empty($data) && isset($data[0])): ?>
                                                                                        <?php echo e(Form::model($data[0], ['route'=>['contacts.store'],'class'=>'form-horizontal','id'=>'contactForm1'])); ?>

                                                                                    <?php elseif(!empty($data) && isset($data[0])): ?>
                                                                                        <?php echo e(Form::model($data[0], ['route'=>['contacts.update','id'=>$data[0]->contactid],'class'=>'form-horizontal','id'=>'contactForm1'])); ?>

                                                                                    <?php else: ?>
                                                                                        <?php echo e(Form::open(['route'=>['contacts.store'],'class'=>'form-horizontal','id'=>'contactForm1'])); ?>

                                                                                    <?php endif; ?>
                                                                                <?php echo e(Form::hidden('company_name', isset($data[0]) && !empty($data[0]->companyname) && !$data[0]->companyname->isEmpty() ? $data[0]->companyname[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_address', isset($data[0]) && !$data[0]->companyaddress->isEmpty() ? $data[0]->companyaddress[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_phone', isset($data[0]) && !$data[0]->companyphone->isEmpty() ? $data[0]->companyphone[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_email', isset($data[0]) && !$data[0]->companyemail->isEmpty() ? $data[0]->companyemail[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_status', isset($data[0]) && !$data[0]->companystatus->isEmpty() ? $data[0]->companystatus[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_type', isset($data[0]) && !$data[0]->companytype->isEmpty() ? $data[0]->companytype[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_area', isset($data[0]) && !$data[0]->companyarea->isEmpty() ? $data[0]->companyarea[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_nationality', isset($data[0]) && !$data[0]->companynationality->isEmpty() ? $data[0]->companynationality[0]->pivot->value : NULL)); ?>

                                                                                <?php echo e(Form::hidden('company_fax', isset($data[0]) && !$data[0]->companyfax->isEmpty() ? $data[0]->companyfax[0]->pivot->value : NULL)); ?>


                                                                                <div class="row clearfix">
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('fname','First Name')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::text('fname', isset($data[0]) ? $data[0]->fname : '', ['class'=>'form-control','required', 'readonly' => 'readonly'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('lname','Last Name')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::text('lname', isset($data[0]) ? ($data[0]->lname ?: '') : '', ['class'=>'form-control', 'readonly' => 'readonly'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('salutation','Salutation')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php
                                                                                                $salutationOptions = [
                                                                                                    '' => 'Select Salutation',
                                                                                                    'Mr' => 'Mr',
                                                                                                    'Mrs' => 'Mrs', 
                                                                                                    'Ms' => 'Miss', 
                                                                                                    'Miss' => 'Miss',
                                                                                                    'MR.' => 'MR.',
                                                                                                    'MRS.' => 'MRS.',
                                                                                                    'MISS' => 'MISS',
                                                                                                ];
                                                                                                ?>
                                                                                                
                                                                                                <?php echo e(Form::select('salutation', $salutationOptions, isset($data[0]) ? $data[0]->salutation : null, ['class'=>'form-control selectpicker','required', 'disabled' => 'disabled'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('marital_status','Marital Status')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::select('marital_status',[''=>'Select Marital Status','Divorced'=>'Divorced','Married'=>'Married','Single'=>'Single','Widowed'=>'Widowed'], isset($data[0]) ? $data[0]->marital_status : null, ['class'=>'form-control selectpicker'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('gender','Gender')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::select('gender',[''=>'Select Gender','M'=>'Male','F'=>'Female'], isset($data[0]) ? $data[0]->gender : null, ['class'=>'form-control selectpicker','required', 'disabled' => 'disabled'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('birthday','Birthday')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                            <?php echo e(Form::text('birthday', (isset($data[0]) && isset($data[0]->birthday)) ? \Carbon\Carbon::parse($data[0]->birthday)->format('d M Y') : '',['class'=>'form-control','required', 'readonly' => 'readonly'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('wedding_bday','Wedding Anniversary')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::text('wedding_bday', 
                                                                                                    (isset($data[0]) && $data[0]->wedding_bday) 
                                                                                                        ? \Carbon\Carbon::parse($data[0]->wedding_bday)->format('d M Y') 
                                                                                                        : '',
                                                                                                    ['class' => 'form-control', 'autocomplete' => 'off']
                                                                                                )); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('address1','Address Line 1')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::text('address1', isset($data[0]->address1) && !$data[0]->address1->isEmpty() ? $data[0]->address1[0]->pivot->value : '', ['class'=>'form-control', 'readonly' => 'readonly'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('address2','Address Line 2')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::text('address2', isset($data[0]->address2) && !$data[0]->address2->isEmpty() ? $data[0]->address2[0]->pivot->value : '', ['class'=>'form-control'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>


                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    <?php echo e(Form::label('country_id','Nationality')); ?>

                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                    <div class="form-group">
                                                                                        <div class="form-line">
                                                                                        <?php echo e(Form::text('country_id', 
                                                                                            isset($data[0]) && isset($data[0]->country_id) ? 
                                                                                                (\App\Models\Country::where('iso2', $data[0]->country_id)->first()?->country ?? 
                                                                                                \App\Models\Country::where('iso3', $data[0]->country_id)->first()?->country ?? 
                                                                                                'Unknown') : '',
                                                                                            ['class'=>'form-control', 'readonly' => 'readonly'])); ?>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                        <?php echo e(Form::label('area','Origin/Area')); ?>

                                                                                    </div>
                                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                                <?php echo e(Form::select('area',[''=>'Select Origin']+\App\Models\Contact::groupBy('area')->pluck('area','area')->all(), isset($data[0]->area) && $data[0]->area != NULL ? $data[0]->area : '', ['class'=>'form-control selectpicker', 'data-live-search'=>'true','required', 'disabled' => 'disabled'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    <?php echo e(Form::label('email','Email')); ?>

                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                    <div class="form-group">
                                                                                        <div class="form-line">
                                                                                            <?php echo e(Form::email('email', isset($data[0]->email) ? $data[0]->email : '', ['class'=>'form-control','required', 'readonly' => 'readonly'])); ?>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    <?php echo e(Form::label('mobile','Mobile')); ?>

                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                        <div class="form-group">
                                                                                            <div class="form-line">
                                                                                            <?php echo e(Form::text('mobile', isset($data[0]->mobile) ? $data[0]->mobile : '', ['class'=>'form-control','number=number','minlength=6','maxlength=15', 'readonly' => 'readonly'])); ?>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                    <?php echo e(Form::label('idnumber','ID Number')); ?>

                                                                                </div>
                                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                    <div class="form-group">
                                                                                        <div class="form-line">
                                                                                            <?php echo e(Form::text('idnumber', isset($data[0]->idnumber) && $data[0]->idnumber != '' ? $data[0]->idnumber : '', ['class'=>'form-control', 'readonly' => 'readonly'])); ?>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row clearfix">
                                                                                    <div class="col-md-12 text-left">
                                                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.2.1_edit_detail')): ?>
                                                                                        <button class="btn waves-effect btn-success btn-flat updateContact" type="submit" id="updateContact1">Save</button>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                                </div>
                                                                                <?php echo e(Form::close()); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div role="tabpanel" class="tab-pane fade" id="messages_with_icon_title">
                                                                <div class="row clearfix">
                                                                    <div class="x_content">
                                                                    <div class="col-lg-12">

                                                                    </div>
                                                                    </div>
                                                                </div>

                                                                <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%">
                                                                    <thead>
                                                                    <tr class="bg-teal">
                                                                        <th class="align-center">Reservation Number</th>
                                                                        <th class="align-center">Check in</th>
                                                                        <th class="align-center">Check Out</th>
                                                                        <th class="align-center">No. Of Night</th>
                                                                        <th class="align-center">Booking Source</th>
                                                                        <th class="align-center">Room Number</th>
                                                                        <th class="align-center">Room Type</th>
                                                                        <th class="align-center">Total Spending (Rp)</th>
                                                                        <th class="align-center">Status</th>
                                                                        
                                                                    </tr>
                                                                    </thead>

                                                                    <tbody>

                                                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                                            <tr class="align-center">
                                                                                <td><?php echo e($contact->resv_id); ?></td>
                                                                                <td><?php echo e($contact->dateci == null ? "" : \Carbon\Carbon::parse($contact->dateci)->format('d M Y')); ?></td>
                                                                                <td><?php echo e($contact->dateco == null ? "" : \Carbon\Carbon::parse($contact->dateco)->format('d M Y')); ?></td>
                                                                                <td><?php echo e(\Carbon\Carbon::parse($contact->dateci)->diffInDays(\Carbon\Carbon::parse($contact->dateco))); ?></td>
                                                                                <td><?php echo e($contact->source); ?></td>
                                                                                <td><?php echo e($contact->room); ?></td>
                                                                                <td><?php echo e($contact->room_name); ?></td>
                                                                                <td><?php echo e(number_format($contact->revenue,0,',','.')); ?></td>
                                                                                <td>
                                                                                    <?php if($contact->foliostatus=='O'): ?>
                                                                                        Checked Out
                                                                                    <?php elseif($contact->foliostatus=='I'): ?>
                                                                                        Inhouse
                                                                                    <?php elseif($contact->foliostatus=='X'): ?>
                                                                                        Cancel
												                                    <?php elseif($contact->foliostatus=='G'): ?>
													                                    Guaranteed
												                                    <?php elseif($contact->foliostatus=='T'): ?>
													                                    Tentative
                                                                                    <?php else: ?>
                                                                                        Confirm
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div role="tabpanel" class="tab-pane fade" id="settings_with_icon_title">
                                                                <b>Settings Content</b>
                                                                <p>
                                                                 <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%">
                                                                    <thead>
                                                                      <tr class="bg-teal">
                                                                          <th>#</th>
                                                                          <th>Name</th>
                                                                          <th>Status</th>                                                                           '
                                                                          <th>Tracking</th>
                                                                          <th>Schedule</th>
                                                                      </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php if(isset($data[0]->campaign)): ?>
                                                                        <?php $__currentLoopData = $data[0]->campaign; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <tr align="center">
                                                                                <td><?php echo e($key+1); ?></td>
                                                                                <td><?php echo e($campaign->name); ?></td>
                                                                                <td><?php echo e($campaign->status); ?></td>
                                                                                <td>
                                                                                    <?php $__currentLoopData = \App\Models\EmailResponse::where('campaign_id','=',$campaign->id)->where('recepient','=',$data[0]->email)->groupBy('event')->orderBy('created_at')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <?php if($loop->first): ?>
                                                                                        <?php echo e($event->event); ?>

                                                                                        <?php endif; ?>
                                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                </td>
                                                                                <td><?php echo e(isset($campaign->schedule) ? \Carbon\Carbon::parse($campaign->schedule->schedule)->format('d F Y H:i') : ''); ?></td>
                                                                            </tr>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php endif; ?>

                                                                    </tbody>
                                                                </table>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel ">
                                                <div class="panel-heading" role="tab" id="headingTwo_18">
                                                    <h4 class="panel-title">
                                                        <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseTwo_18" aria-expanded="false"
                                                           aria-controls="collapseTwo_18">
                                                            <i class="fa fa-building"></i> COMPANY INFORMATION
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapseTwo_18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_18">
                                                    <div class="panel-body">
                                                        <div class="row clearfix">
                                                            <div class="card">
                                                                <div class="body">
                                                                    <?php if(!empty($action) && isset($data[0])): ?>
                                                                        <?php echo e(Form::model($data[0],['route'=>['contacts.store'],'class'=>'form-horizontal','id'=>'contactForm2'])); ?>

                                                                    <?php elseif(isset($data[0])): ?>
                                                                        <?php echo e(Form::model($data[0],['route'=>['contacts.update','id'=>$data[0]->contactid],'class'=>'form-horizontal','id'=>'contactForm2'])); ?>

                                                                    <?php endif; ?>

                                                                    <?php if(isset($data[0])): ?>
                                                                        <?php echo e(Form::hidden('fname',$data[0]->fname )); ?>

                                                                        <?php echo e(Form::hidden('lname',$data[0]->lname )); ?>

                                                                        <?php echo e(Form::hidden('email',$data[0]->email )); ?>

                                                                        <?php echo e(Form::hidden('mobile', isset($data[0]) && !empty($data[0]->mobile) ? $data[0]->mobile : NULL)); ?>

                                                                        <?php echo e(Form::hidden('salutation',$data[0]->salutation )); ?>

                                                                        <?php echo e(Form::hidden('salutation',$data[0]->salutation )); ?>

                                                                        <?php echo e(Form::hidden('gender',$data[0]->gender )); ?>

                                                                        <?php echo e(Form::hidden('birthday',$data[0]->birthday )); ?>

                                                                        <?php echo e(Form::hidden('wedding_bday',$data[0]->birthday )); ?>

                                                                        <?php echo e(Form::hidden('country_id',$data[0]->country==null ? '':$data[0]->country->id )); ?>

                                                                        <?php echo e(Form::hidden('address1',$data[0]->address1->isEmpty() ? '' :$data[0]->address1[0]->pivot->value)); ?>

                                                                        <?php echo e(Form::hidden('address2',$data[0]->address2->isEmpty() ? '' :$data[0]->address2[0]->pivot->value)); ?>

                                                                    <?php endif; ?>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_name','Name')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::text('company_name', (isset($data[0]) && isset($data[0]->companyname) && !$data[0]->companyname->isEmpty()) ? $data[0]->companyname[0]->pivot->value : '', ['class'=>'form-control', 'data-live-search'=>'true'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_address','Address')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::text('company_address', isset($data[0]) && isset($data[0]->companyaddress) && !$data[0]->companyaddress->isEmpty() ? $data[0]->companyaddress[0]->pivot->value : '', ['class'=>'form-control'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_phone','Phone')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::text('company_phone', isset($data[0]) && isset($data[0]->companyphone) && !$data[0]->companyphone->isEmpty() ? $data[0]->companyphone[0]->pivot->value : '', ['class'=>'form-control','number=number','minlength=6','maxlength=15'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_fax','Fax')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::text('company_fax', isset($data[0]) && !$data[0]->companyfax->isEmpty() ? $data[0]->companyfax[0]->pivot->value : '', ['class'=>'form-control','number=number','minlength=6','maxlength=15'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_email','Email')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::email('company_email', isset($data[0]) && isset($data[0]->companyemail) && !$data[0]->companyemail->isEmpty() ? $data[0]->companyemail[0]->pivot->value : '', ['class'=>'form-control'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_type','Type')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::select('company_type',[''=>'Select Type','Compliment'=>'Compliment','Corporate'=>'Corporate','Direct'=>'Direct','Goverment'=>'Goverment','Group'=>'Group','OTA'=>'OTA','Timeshare'=>'Timeshare', 'Travel Agent'=>'Travel Agent','Whosaler'=>'Wholesaler'], isset($data[0]) && !$data[0]->companytype->isEmpty() ? $data[0]->companytype[0]->pivot->value : '',['class'=>'form-control selectpicker'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_area','Area')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::text('company_area', isset($data[0]) && !$data[0]->companyarea->isEmpty() ? $data[0]->companyarea[0]->pivot->value : '', ['class'=>'form-control'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_nationality','Nationality')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::select('company_nationality',[''=>'Select Nationality']+\App\Models\Country::pluck('country','id')->all(), isset($data[0]) && !$data[0]->companynationality->isEmpty() ? $data[0]->companynationality[0]->pivot->value : '',['class'=>'form-control', 'data-live-search'=>'true'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                        <?php echo e(Form::label('company_status','Status')); ?>

                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <div class="form-line">
                                                                                <?php echo e(Form::select('company_status',[''=>'Select Status','Prospect'=>'Prospect','Active'=>'Active','In Active'=>'In Active'], isset($data[0]) && !$data[0]->companystatus->isEmpty() ? $data[0]->companystatus[0]->pivot->value : '',['class'=>'form-control selectpicker'])); ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row clearfix">
                                                                        <div class="col-md-12 text-left">
                                                                            <?php if(auth()->user()->can('3.2.3_edit_company_information') || auth()->user()->can('3.2.2_add_company_information')): ?>
                                                                            <button class="btn  waves-effect btn-success btn-flat updateContact" id="updateContact2"  type="submit" >Save</button>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                    <?php echo e(Form::close()); ?>

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
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script>



        <?php if(isset($data) && isset($data[0]) && isset($data[0]->contactid)): ?>
        $('#complaint<?php echo e($data[0]->contactid); ?>').on('change',function (e) {
            if($(this).is(':checked')){
                var val=1;
                $(this).val(1)
            }else{
                val=0;
                $(this).val(0)
            }
            $.ajax({
                url:'<?php echo e(route('update.exclude')); ?>',
                type:'POST',
                data:{
                    _token:'<?php echo e(csrf_token()); ?>',
                    val:val,
                    email:"<?php echo e(isset($data[0]->email) ? $data[0]->email : ''); ?>"
                },
            })
        })
        <?php endif; ?>

        $('#birthday , #wedding_bday').datepicker({
            language: 'en',
            dateFormat:'dd M yyyy'
        });

    </script>
    <script>
        if (window.location.pathname=='/contacts/add'){
            $('#updateContact2').attr('disabled','disabled');
        }
        $('.dataTable').dataTable()
    </script>
    <script>
       $('#contactForm1').validate();
       $('#contactForm2').validate(); 
    </script>
    <script>
        $('button[id^=updateContact]').click(function (event) {
            event.preventDefault();
            if(this.id=='updateContact1'){
                var form1=$('#contactForm1');
                form1.submit();
                var error=$('.error:visible').length;
                if (error>0){
                    $('#updateContact2').attr('disabled','disabled');
                    sweetAlert('Warning!', 'Error Found ','warning',7000);
                }else {
                    $('#updateContact2').removeAttr('disabled');
                    sweetAlert('Congratulation!', 'Data updated','success',7000);
                }
            } else {
                var form2=$('#contactForm2');
                form2.submit();

                var error=$('.error:visible').length;
                if (error>0){
                    sweetAlert('Warning!', 'Error Found ','warning',7000);
                }else {
                    sweetAlert('Congratulation!', 'Data updated','success',7000);
                }
            }
        });
    </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/contacts/detail.blade.php ENDPATH**/ ?>