<?php $__env->startSection('title'); ?>
   Dashboard | <?php echo e($configuration->hotel_name.' '.$configuration->app_title); ?>

    <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('2.1.1_view_dashboard')): ?>
    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel tile " >
                    <div class="x_title">
                        <h2>Last 3 months data</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>

                            <li><a class="close-link" id="closeSummary"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height:120px;">
                        <div class="row tile_count">
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Contacts</span>
                                <div class="count green"> <a href="<?php echo e(url('contacts/list')); ?>" class="green" ><?php echo e(\App\Models\Contact::with('transaction')->whereHas('transaction')->count()); ?></a> </div>

                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Male</span>
                                <div class="count blue"><a href="<?php echo e(url('contacts/f/male')); ?>" class="green" ><?php echo e(\App\Models\Contact::with('transaction')->whereHas('transaction')->where('gender','M')->count()); ?></a></div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Female</span>
                                <div class="count orange"><a href="<?php echo e(url('contacts/f/female')); ?>" class="green" ><?php echo e(\App\Models\Contact::with('transaction')->whereHas('transaction')->where('gender','F')->count()); ?></a></div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Inhouse </span>
                                <div class="count green"> <a href="<?php echo e(url('contacts/f/status/Inhouse')); ?>" class="green" ><?php echo e(\App\Models\Contact::whereHas('profilesfolio',function($q){
                                    return $q->where('foliostatus','=','I')->groupBy('profileid');
                                    })->whereHas('transaction',function($q){
                                        return $q->havingRaw('sum(revenue)>=0');
                                    })->count()); ?> </a> </div>
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Confirm </span>
                                <div class="count red"> <a href="<?php echo e(url('contacts/f/status/Confirm')); ?>" class="green" > <?php echo e(\App\Models\Contact::whereHas('profilesfolio', function($q) {
                                    return $q->where('foliostatus', '=', 'C');
                                })->count()); ?> </a></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- top tiles -->
        <!-- /top tiles -->
        <div class="row">

            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeAdded">
                <div class="x_panel tile " style="height: 420px">
                    <div class="x_title">
                        <h2>Contacts Added Dashboard</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            <li><a class="close-link" id="closeAdded"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" >
                        <div class="dashboard-widget-content">
                            <div id="added" class="dashboard-donut-chart "></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeCountry">
                <div class="x_panel tile  "style="height: 420px; ">
                    <div class="x_title">
                        <h2>Contact by Country</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link" id="closeCountry"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:350px;">

                        <table class="" style="width:100%">

                            <tr>
                                <td>
                                    <div id="donut_chart" class="dashboard-donut-chart "></div>
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeSpending">
                <div class="x_panel tile " style="height: 420px">
                    <div class="x_title">
                        <h2>Top 10 Spending </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>

                            <li><a class="close-link" id="closeSpending"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="dashboard-widget-content">
                            <div id="top10rev" class="dashboard-donut-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeEmail">
                <div class="x_panel tile " style="height: 420px">
                    <div class="x_title">
                        <h2>Email Reports</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            <li><a class="close-link" id="closeEmail"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" >
                        <div class="dashboard-widget-content">
                            <div id="emailreport" class="dashboard-donut-chart "></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeIncoming">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Incoming Birthdays </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link" ><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link" id="closeIncoming"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="overflow-y: scroll;height: 350px" >
                        <div class="dashboard-widget-content">
                            <ul class="list-unstyled timeline widget">
                                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <div class="block">
                                            <div class="block_content">
                                                <h2 class="title">
                                                    <a href="<?php echo e(url('/contacts/detail/').'/'.$value->contactid); ?>" >  <?php echo e($value->fname .' '.$value->lname); ?> </a>
                                                </h2>
                                                <div class="byline">
                                                    <span> <div class="number font-30"><?php echo e(\Carbon\Carbon::parse($value->birthday)->format('M d')); ?></div></span>
                                                </div>

                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('2.2.1_view_incoming_birthdays')): ?>
                            <?php echo e(Form::open(['url'=>'contacts/birthday/search'])); ?>

                            <?php echo e(Form::hidden('days','7')); ?>

                            <button class="btn btn-sm" type="submit">More..</button>
                            <?php echo e(Form::close()); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeLong">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Top 10 Longest Stay </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link" id="closeLong"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height:350px">
                        <div class="dashboard-widget-content">
                            <div id="longest" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeRoomType">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Contacts By Room Type</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link" id="closeRoomType"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height: 350px">
                        <div  class="dashboard-widget-content">
                            <div id="roomtype" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeAge">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Contacts by Ages</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link" id="closeAge"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height: 350px;">
                        <div  class="dashboard-widget-content">
                            <div id="ages" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                    <!-- end of weather widget -->
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeBooking">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Contacts By Booking Source</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link" id="closeBooking"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height: 350px;">
                        <div  class="dashboard-widget-content">
                            <div id="bookingsource" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeStay">
                <div class="x_panel tile " style="height: 420px">
                    <div class="x_title">
                        <h2>Top 10 Stays </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>

                            <li><a class="close-link" id="closeStay"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="dashboard-widget-content">
                            <div id="top10stay" class="dashboard-donut-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php else: ?>
    <div class="right_col" role="main">
        <div class="alert alert-danger">
            <h4><i class="fa fa-exclamation-triangle"></i> Access Denied</h4>
            <p>You don't have permission to view the dashboard.</p>
        </div>
    </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function () {

            var items=[]
            $('.close-link').each(function () {
                items.push($(this).attr('id'))
            })
           $.each(items,function (i,v) {
               if(readCookie(v)==1){
                   if (v=='closeSummary'){
                       var target=$('#'+v).parents('.col-md-12')
                       target.hide('slow',function () {
                           target.remove()
                       })
                   }
                   var target=$('#'+v).parents('.col-md-4')
                   target.hide('slow',function () {
                       target.remove()
                   })
               }
           })
        })
    </script>
    <script>
        function createCookie(name, value, days) {
            var expires;
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            } else {
                expires = "";
            }
            document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
        }

        function readCookie(name) {
            var nameEQ = encodeURIComponent(name) + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ')
                    c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0)
                    return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;
        }

        function eraseCookie(name) {
            createCookie(name, "", -1);
        }

    $('.close-link').each(function () {
        $(this).on('click',function () {
            var id=$(this).attr('id')
            createCookie(id,0,1)
            var target=$(this).parents('.col-md-4')
                target.hide('slow',function () {
                target.remove()
            })
        })
    })
        $(document).ready(function () {
            $('div.paneld').each(function () {
                var panelid=$(this).attr('id')
                var state=readCookie(panelid)
                if(state==0){
                    $(this).hide('slow',function () {
                        $(this).remove()
                    })
                }
            })


            if(readCookie('collapseSidebar')==0){
                $('body').removeAttr('class')
                $('body').attr('class','nav-md')
            }else {
                $('body').removeAttr('class')
                $('body').attr('class','nav-sm')
            }

        })
    </script>
    <script>
        var total=<?php echo $total; ?>;
        var data=<?php echo $country; ?>;
        
        Morris.Donut({
            element: 'donut_chart',
            data:  data,
            resize:true,
            // colors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],

            formatter: function (y) {
                var res=y/total * 100;
                res=Math.round(res);
                return res  + '%'
            }
        }).on('click',function (i,row) {
            window.location.href='contacts/f/country/'+row.label;
        });

        Morris.Bar({
            element: 'top10rev',
            resize: true,
            data:<?php echo $spending; ?>,
            xkey: 'x',
            ykeys: ['y'],
            labels: [''],
            axes:'x',
            hoverCallback:function (index, options, content, row) {
                return row.x ;
            },
            barColors: function (row, series, type) {
                var blue = Math.ceil((255 * row.y / this.ymax)+255/10 );
                return 'rgb(57, 128, 181)';
            }
     //           ['rgb(255,0,80)']
        }).on('click',function (i,row) {
            window.location.href='contacts/f/spending/'+row.x;
        });
        Morris.Bar({
            element:'added',
            resize:true,
            data:<?php echo $monthcount; ?>,
            xkey:'x',
            ykeys:['y'],
            labels:['Contact Added'],
            barColors:function (row, series, type) {
                var red = Math.ceil((255 * row.y / this.ymax)+255/6);
                return 'rgb(57, 128, 181)';
            }
        }).on('click',function(i,row){
            window.location.href='contacts/f/created/'+row.x;
        });
        Morris.Bar({
            element:'longest',
            resize:true,
            data:<?php echo $longstay; ?>,
            xkey:'x',
            ykeys:['y'],
            labels:['Nights'],
            barColors:function (row, series, type) {
                var red = Math.ceil((255 * row.y / this.ymax)+255/5 );
                return 'rgb(57, 128, 181)';
            }
        }).on('click',function(i,row){
            window.location.href='contacts/f/longest/'+row.x;
        });
        Morris.Bar({
            element:'top10stay',
            resize:true,
            data:<?php echo $datastay; ?>,
            xkey:'x',
            ykeys:['y'],
            labels:['Stays'],
            barColors:function (row, series, type) {
                var red = Math.ceil((255 * row.y / this.ymax)+255/5 );
                return 'rgb(57, 128, 181)';
            }

        }).on('click',function(i,row){
            window.location.href='contacts/f/longest/'+row.x;
        });
        var troom='<?php echo $troom; ?>';
        Morris.Donut({
            element: 'roomtype',
            data: <?php echo $room_type; ?>,
            resize:true,
            // colors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],

            formatter: function (y) {
                var res=y/troom * 100;
                res=Math.round(res);
                return res  + '%'
            }
        }).on('click',function (i,row) {
            window.location.href='contacts/f/roomtype/'+row.label;
        });
        var tages='<?php echo $tages; ?>'
        Morris.Donut({
            element: 'ages',
            data: <?php echo $data_age; ?>,
            resize:true,
            // colors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],

            formatter: function (y) {
                var res=y/tages * 100;
                res=Math.round(res);
                return res  + '%'
            }
        }).on('click',function (i,row) {
            window.location.href='contacts/f/ages/'+row.type;
        });
        var tbookingsource='<?php echo $tbookingsource; ?>'
        Morris.Donut({
            element:'bookingsource',
            data:<?php echo $databookingsource; ?>,
            resize:true,
            formatter:function (y) {
				// console.log(y)
                var res=y/tbookingsource*100;
                res=Math.round(res);
                return res + '%'
            }
        }).on('click',function (i,row) {
            window.location.href='contacts/f/source/'+row.label;
        });
        var temailcount='<?php echo $temailcount; ?>'
        var month="<?php echo e(\Carbon\Carbon::now()->format('M Y')); ?>"
        Morris.Donut({
            element:'emailreport',
            data:<?php echo $dataemailreport; ?>,
            resize:true,
            formatter:function (y) {
                var res=y/temailcount*100;
                res=Math.round(res);
                return '('+y+') '+ res + '% of ' + temailcount +' email sent\n\n'+ month
            }
        })
    </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jalakdev/www/crm/resources/views/main/index.blade.php ENDPATH**/ ?>