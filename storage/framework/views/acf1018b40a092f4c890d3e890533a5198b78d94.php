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
                                <table style="font-size: 13px" class="table table-bordered table-striped table-hover responsive js-basic-example  " id="loadcontacts" width="100%">
                                    <thead class="bg-teal">
                                    <tr>
                                        <th class="align-center">No</th>
                                        <th class="align-center">Full Name</th>
                                        <th class="align-center">Last Name</th>
                                        <th class="align-center">Birthday</th>
                                        <th class="align-center">Wedding Anniversary</th>
                                        <th class="align-center">Country</th>
                                        <th class="align-center">Area/Origin</th>

                                        <th class="align-center">Campaign</th>
                                        <th class="align-center">Total Stays</th>
                                        <th class="align-center">Last Stay</th>
                                        <th class="align-center">Total Spending (Rp.)</th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.1.3_excluded_detail')): ?>
                                        <th class="align-center">Excluded</th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>

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
        $(document).ready(function(){
            var item=[];
            var path="<?php echo e(asset('countries.json')); ?>"
            var json = $.getJSON(path,function (d) {
                $.each(d,function (i) {
                    item.push(d[i])
                })
            })
            var t= $('#loadcontacts').DataTable({
                "autoWidth": true,
                "processing": true,
                "serverSide": true,
                "pageLength":20,
                "ajax":{
                    "url": "<?php echo e(route('contactslist')); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":{
                        _token: "<?php echo e(csrf_token()); ?>",
                        gender:"<?php echo e($gender); ?>",
                        
                    }
                },
                "columns": [
                    { "data": null },
                    { "data": "fname" },
                    { "data": "lname" },
                    { "data": "birthday" },
                    { "data": "wedding_bday"},
                    { "data": "country_id"},
                    { "data": "area"},
                    { "data": "campaign"},
                    { "data": "stay"},
                    { "data": "checkin"},
                    { "data": "revenue","name":"transaction.revenue"},
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.1.3_excluded_detail')): ?>
                    { "data": null},
                    <?php endif; ?>
                ],


                "columnDefs":[
                    {
                        "targets":0,
                        "sortable":false,
                        "render":function (data,type,row,meta) {
                            return meta.row+1
                        }
                    },
                    {
                        "targets":1,
                        "render":function (data,type,row) {

                            var id=row.contactid
                            var lname=""
                            if(row.lname==null){
                                lname=""
                            }else {
                                lname=row.lname
                            }

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.1.2_view_detail')): ?>
                                return '<a href="<?php echo e(url('contacts/detail/')); ?>'+'/'+id+'" >'+ data +' ' +lname+'</a>'
                            <?php else: ?>
                                return data + ' ' + lname;
                            <?php endif; ?>
                        }
                    },
                     {
                        "targets":2,
                        "visible":false,
                    },{
                        "targets":[3,4],
                        "render":function (data,type,row) {
                            if(moment(data).isValid()){
                                return moment(data).format("MMM DD")
                            }else {
                                return ''
                            }
                        }
                    },
                    {
                        "targets":5,
                        "render":function (data,type,row) {
                            for(var i in item){
                                if(data===item[i]["iso3"]){
                                    var d=''
                                    d=(item[i]["iso2"])
                                    d=d.toLowerCase()
                                    var  country=item[i]["country"];
                                    return country +'<i  class="flag flag-'+d+' pull-right"  />';
                                }
                            }
                        }
                    },{
                        "targets":9,
                        "render":function (data,type,row) {
                            if(moment(data).isValid()){
                                return moment(data).format('DD MMM YYYY')
                            }else {
                                return ''
                            }
                        }
                    },
                    {
                        "targets":10,
                        "render":function (data,type,row) {
                            return "Rp. "+Math.round(data).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")
                        }
                    },
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.1.3_excluded_detail')): ?>
                    {
                        'targets': 11,
                        'searchable': false,
                        'orderable': false,
                        'className': 'dt-body-center',
                        'render': function (data, type, full, meta){
                            if(full.excluded !==null){
                                return '<input type="checkbox" class="checkbox"  checked name="complaint" id="complaint'+full.contactid+'" value=1>';
                            }else{
                                return '<input type="checkbox" class="checkbox" name="complaint"   id="complaint'+full.contactid+'" value=0>';
                            }
                        }
                    }
                    <?php endif; ?>
                ],

            });
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('3.1.3_excluded_detail')): ?>
            $('#loadcontacts tbody').on('change', 'input[type="checkbox"]', function(e){
                var chkid=e.target.id.replace('complaint','')
                var el=$(this)
                if(el.is(':checked')){
                    var val=1;
                    el.val(0)
                }else {
                    var val=0;
                    el.val(1)
                }
                swal({
                    title:'Are you Sure?',
                    text:'This action will delete or insert an email from / to the list of excluded emails. The Guest will not receive any emails later if this option checked',
                    type:'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText:'Update',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: true,
                    closeOnCancel: true
                },function (isconfirm) {
                    if(isconfirm){
                        $.ajax({
                            url:'<?php echo e(route('update.exclude')); ?>',
                            type:'POST',
                            data:{
                                _token:'<?php echo e(csrf_token()); ?>',
                                val:val,
                                id:chkid
                            }
                        })

                    }else{
                        location.reload();
                    }
                })
            })
            <?php endif; ?>
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/contacts/list3.blade.php ENDPATH**/ ?>