@extends('layouts.master')
@section('title')
   Dashboard | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    @can('2.1.1_view_dashboard')
    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel tile">
                    <div class="x_title">
                        <h2>
                            @if(isset($latestDataDate) && $latestDataDate)
                                Data Period: {{ $dateRangeDisplay ?? 'Last 3 months data' }}
                                <small style="display: block; font-size: 12px; color: #666; margin-top: 5px;">
                                    <i class="fa fa-info-circle"></i> Latest available data: {{ \Carbon\Carbon::parse($latestDataDate)->format('M d, Y') }}
                                </small>
                            @else
                                Last 3 months data
                            @endif
                        </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>

                            <li><a class="close-link" id="closeSummary"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height:120px;">
                        <div class="row tile_count" style="display: flex; flex-wrap: nowrap;">
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-user"></i> Total Contacts</span>
                                <div class="count green"> <a href="{{ url('contacts/list') }}" class="green" >{{ \App\Models\Contact::with('transaction')->whereHas('transaction')->count()  }}</a> </div>
                            </div>

                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-user"></i> Total Male</span>
                                <div class="count blue"><a href="{{ url('contacts/f/male') }}" class="green" >{{ \App\Models\Contact::with('transaction')->whereHas('transaction')->where('gender','M')->count() }}</a></div>
                            </div>
                            
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-user"></i> Total Female</span>
                                <div class="count orange"><a href="{{ url('contacts/f/female') }}" class="green" >{{ \App\Models\Contact::with('transaction')->whereHas('transaction')->where('gender','F')->count() }}</a></div>
                            </div>
                            
                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-user"></i> Inhouse </span>
                                <div class="count green"> <a href="{{ url('contacts/f/status/Inhouse') }}" class="green" >{{ \App\Models\Contact::whereHas('profilesfolio',function($q){
                                    return $q->where('foliostatus','=','I')->groupBy('profileid');
                                    })->whereHas('transaction',function($q){
                                        return $q->havingRaw('sum(revenue)>=0');
                                    })->count() }} </a> </div>
                            </div>

                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-refresh"></i> Inhouse Repeaters</span>
                                <div class="count green">
                                    <a href="{{ url('contacts/f/status/InhouseRepeater') }}" class="green">{{ $inhouseRepeaterCount ?? 0 }}</a>
                                </div>
                            </div>

                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-birthday-cake"></i> In-House Birthday</span>
                                <div class="count green">
                                  <a href="{{ route('contacts.inhouse.birthday.today') }}" class="green">{{ $inhouseBirthdayTodayCount ?? 0 }}</a>
                                </div>
                            </div>

                            <div class="tile_stats_count" style="flex: 1; margin: 0 5px;">
                                <span class="count_top"><i class="fa fa-user"></i> Confirm </span>
                                <div class="count red"> <a href="{{ url('contacts/f/status/Confirm') }}" class="green" > {{ \App\Models\Contact::whereHas('profilesfolio', function($q) {
                                    return $q->where('foliostatus', '=', 'C');
                                })->count() }} </a></div>
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
            <!-- Tambahkan setelah panel Email Reports -->
<div class="col-md-4 col-sm-4 col-xs-12 paneld" id="closeRepeater">
    <div class="x_panel tile" style="height: 420px">
        <div class="x_title">
            <h2>Repeater Statistics</h2>
            <div class="btn-group" style="float: left; margin-left: 10px;">
                <button type="button" class="btn btn-xs btn-primary" onclick="updateRepeaterChart(3)" id="btn-3m">3 Months</button>
                <button type="button" class="btn btn-xs btn-default" onclick="updateRepeaterChart(6)" id="btn-6m">6 Months</button>
                <button type="button" class="btn btn-xs btn-default" onclick="updateRepeaterChart(12)" id="btn-12m">12 Months</button>
            </div>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li><a class="close-link" id="closeRepeater"><i class="fa fa-close"></i></a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="dashboard-widget-content">
                <!-- Loading indicator -->
                <div id="repeater_loading" style="display: none; text-align: center; padding: 50px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Loading data...</p>
                </div>
                <div id="repeater_chart" class="dashboard-donut-chart"></div>
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
                                @foreach($data as $key=>$value)
                                    <li>
                                        <div class="block">
                                            <div class="block_content">
                                                <h2 class="title">
                                                    <a href="{{ url('/contacts/detail/').'/'.$value->contactid }}" >  {{ $value->fname .' '.$value->lname }} </a>
                                                </h2>
                                                <div class="byline">
                                                    <span> <div class="number font-30">{{ \Carbon\Carbon::parse($value->birthday)->format('M d') }}</div></span>
                                                </div>

                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            @can('2.2.1_view_incoming_birthdays')
                            {{ Form::open(['url'=>'contacts/birthday/search']) }}
                            {{ Form::hidden('days','7') }}
                            <button class="btn btn-sm" type="submit">More..</button>
                            {{ Form::close() }}
                            @endcan
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
    @else
    <div class="right_col" role="main">
        <div class="alert alert-danger">
            <h4><i class="fa fa-exclamation-triangle"></i> Access Denied</h4>
            <p>You don't have permission to view the dashboard.</p>
        </div>
    </div>
    @endcan
@endsection
@section('script')
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
        var total={!! $total !!};
        var data={!! $country !!};
        {{--console.log({!! $country !!})--}}
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
            data:{!! $spending !!},
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
            data:{!! $monthcount !!},
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
            data:{!! $longstay !!},
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
            data:{!! $datastay !!},
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
        var troom='{!! $troom !!}';
        Morris.Donut({
            element: 'roomtype',
            data: {!!  $room_type !!},
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
        var tages='{!! $tages !!}'
        Morris.Donut({
            element: 'ages',
            data: {!!  $data_age !!},
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
        var tbookingsource='{!! $tbookingsource !!}'
        Morris.Donut({
            element:'bookingsource',
            data:{!! $databookingsource !!},
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
        var temailcount='{!! $temailcount !!}'
        var month="{{ \Carbon\Carbon::now()->format('M Y') }}"
        Morris.Donut({
            element:'emailreport',
            data:{!! $dataemailreport !!},
            resize:true,
            formatter:function (y) {
                var res=y/temailcount*100;
                res=Math.round(res);
                return '('+y+') '+ res + '% of ' + temailcount +' email sent\n\n'+ month
            }
        })

// Ganti bagian script repeater chart
var repeaterChart;
var currentPeriod = 3; // Default 3 bulan

// Inisialisasi chart repeater
function initRepeaterChart(data) {
    if (repeaterChart) {
        repeaterChart.setData(data);
    } else {
        repeaterChart = Morris.Bar({
            element: 'repeater_chart',
            resize: true,
            data: data,
            xkey: 'x',
            ykeys: ['y'],
            labels: ['Repeaters'],
            barColors: function (row, series, type) {
                return 'rgb(57, 128, 181)';
            },
            hoverCallback: function (index, options, content, row) {
                return row.x + ': ' + row.y + ' repeaters';
            }
        }).on('click', function(i, row) {
            // Link ke halaman list dengan filter bulan (tanpa parameter period)
            var encodedMonth = encodeURIComponent(row.x);
            window.location.href = 'contacts/f/repeater-month/' + encodedMonth;
        });
    }
}

// Function untuk update chart berdasarkan periode
function updateRepeaterChart(months) {
    currentPeriod = months;
    
    // Update button states
    $('.btn-group button').removeClass('btn-primary').addClass('btn-default');
    $('#btn-' + months + 'm').removeClass('btn-default').addClass('btn-primary');
    
    // Show loading indicator
    $('#repeater_loading').show();
    $('#repeater_chart').hide();
    
    // AJAX call untuk mendapatkan data baru
    $.ajax({
        url: '{{ route("dashboard.repeater.data") }}',
        method: 'POST',
        data: {
            months: months,
            _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            $('#repeater_loading').hide();
            $('#repeater_chart').show();
            initRepeaterChart(data);
        },
        error: function(xhr, status, error) {
            $('#repeater_loading').hide();
            $('#repeater_chart').show();
            console.error('Error loading repeater data:', error);
            alert('Gagal memuat data repeater. Silakan coba lagi.');
        }
    });
}

// Inisialisasi chart saat halaman dimuat
$(document).ready(function() {
    // Load data awal via AJAX untuk menghindari loading lambat
    updateRepeaterChart(3);
});


    </script>
    @endsection
