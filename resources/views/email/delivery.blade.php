@extends('layouts.master')
@section('title')
    Email Delivery Status  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel title">
                            <div class="x_title">
                                <h3>Email Delivery Status</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix">
                                    <div class="card">
                                        <div class="header">

                                        </div>
                                        <div class="body">
                                            <div class="dashboard-widget-content col-md-4 col-sm-12 col-lg-4">
                                                <div id="poststayChart" class="dashboard-donut-chart"></div>
                                            </div>
                                            <div class="dashboard-widget-content col-md-4 col-sm-12 col-lg-4">
                                                <div id="birthdayChart" class="dashboard-donut-chart"></div>
                                            </div>
                                            <div class="dashboard-widget-content col-md-4 col-sm-12 col-lg-4">
                                                <div id="missyouChart" class="dashboard-donut-chart"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--<a href="{{ url('email/create') }}" title="Create New Template" class=" btn btn-sm btn-success"> <i class="fa fa-plus"></i> Create New Template</a>--}}
                            </div>
                            <div class="x_content">
                                <div class="panel-group full-body" id="accordion_18" role="tablist" aria-multiselectable="true">
                                    <div class="panel">
                                        <div class="panel-heading" role="tab" id="headingOne_18">
                                            <h4 class="panel-title ">
                                                <a class="collapsed teal"  role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseOne_18" aria-expanded="true" aria-controls="collapseOne_18">
                                                    <i class="fa fa-hotel"></i> Poststay
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_18" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_18">
                                            <div class="panel-body">
                                                <div class="row clearfix">
                                                    <div class="card">
                                                        <div class="body">
                                                            <table class="table table-bordered table-striped table-hover responsive js-basic-example " style="font-size: 11px" id="poststay" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Event</th>                                                                   
                                                                    <th>URL</th>
                                                                    <th>Recipient</th>
                                                                    <th>Status</th>
                                                                    <th>Sent</th>
                                                                </tr>
                                                                </thead>
                                                            </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="panel ">
                                        <div class="panel-heading" role="tab" id="headingTwo_18">
                                            <h4 class="panel-title">
                                                <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseTwo_18" aria-expanded="false"                              aria-controls="collapseTwo_18">
                                                    <i class="fa fa-birthday-cake"></i> Birthday
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo_18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_18">
                                            <div class="panel-body">
                                                <div class="row clearfix">
                                                    <div class="card">
                                                        <div class="body">
                                                            <table class="table table-bordered table-striped table-hover responsive js-basic-example " style="font-size: 11px" id="birthday" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Event</th>                                                                   
                                                                    <th>URL</th>
                                                                    <th>Recipient</th>
                                                                    <th>Status</th>
                                                                    <th>Sent</th>
                                                                </tr>
                                                                </thead>
                                                            </table>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel ">
                                        <div class="panel-heading" role="tab" id="headingThree_18">
                                            <h4 class="panel-title">
                                                <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseThree_18" aria-expanded="false"  aria-controls="collapseThree_18">
                                                    <i class="fa fa-cloud"></i> Miss You
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseThree_18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree_18">
                                            <div class="panel-body">
                                                <div class="row clearfix">
                                                    <div class="card">
                                                        <div class="body">
                                                            <table class="table table-bordered table-striped table-hover responsive js-basic-example " style="font-size: 11px" id="missyou" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Event</th>                                                                   
                                                                    <th>URL</th>
                                                                    <th>Recipient</th>
                                                                    <th>Status</th>
                                                                    <th>Date</th>
                                                                </tr>
                                                                </thead>
                                                            </table>
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


@endsection
@section('script')
<script>
    $('.datetimepicker').datetimepicker({
        format: 'DD MMM YYYY H:mm',
        showClear:true,
    });
    function escapeHtml(text) {
        'use strict';
        return text.replace(/[\"&'\/<>]/g, function (a) {
            return {
                '"': '&quot;', '&': '&amp;', "'": '&#39;',
                '/': '&#47;',  '<': '&lt;',  '>': '&gt;'
            }[a];
        });
    }
    function getClick(recipient) {
        var arr = "";
        $.ajax({
            type: "POST",
            async: false,
            url: "{{ route('getClick') }}",
            data:{
                recipient:recipient,
                type:'clicked',
                _token:"{{ csrf_token() }}"
            },
            success: function (data) {
                arr=data

            }
        });
        return arr
    }

    $(document).ready(function () {

        $.each(['poststay','birthday','missyou'],function (i,v) {
            $.ajax({
                url:"{{ route('deliverychart') }}" ,
                type:'POST',
                data:{
                    d:v,
                    _token:"{{ csrf_token() }}"
                },success:function (d) {
                    var op=0;
                    var cl=0;
                    var fa=0;
                    var de=0;
                    var un=0;
                    var pr=0;
                    for(var i =0;i<=d.length-1;i++){
                        if(d[i]['event']==='opened'){
                            op+=1
                        }
                        if (d[i]['event']==='clicked'){
                            cl+=1
                        }
                        if(d[i]['event']==='failed'){
                            fa+=1
                        }
                        if(d[i]['event']==='delivered'){
                            de+=1
                        }
                        if(d[i]['event']==='unsubscribed'){
                            un+=1
                        }
                        if(d[i]['event']=='processed'){
                            pr+=1
                        }
                    }
                    var res=[{'label':'Clicked','value':cl},{'label':'Opened','value':op},{'label':'Failed','value':fa},{'label':'Delivered','value':de},{'label':'Unsubscribed','value':un},{'label':'Processed','value':pr}];
                    //res.push([{'label':'Opened','value':op}],[{'label':'Clicked','value':cl}],[{'label':'Failed','value':fa}],[{'label':'Delivered','value':de}])
                    if(d.length>0){
                        Morris.Donut({
                            element: v+'Chart',
                            data:  res,
                            resize:true,
                            colors: ['rgb(77, 163, 3)', 'rgb(52,152,219)', 'rgb(231,76,60)', 'rgb(0, 150, 136)', 'rgb(243,156,18)','rgb(209, 236, 241)'],

                            formatter: function (y) {
                                var xx=y/d.length * 100;
                                xx=Math.round(xx);
                                return v.toUpperCase()+' \n'+ xx  + '%'
                            }
                        })
                    }
                }
            });

            var t=$('#'+v).DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":{
                    "url":"{{ route('deliverystatus') }}",
                   // "dataSrc":"",
                    "type":"POST",
                    "dataType": "json",
                    "data":{
                        "d":v,
                        "_token":"{{ csrf_token() }}"
                    }
                }, 
                "columnDefs": [
                    {
                        "targets": 0,
                        "data": "id",
                        "width":"15px"

                    },
                    {
                        "targets":1,
                        "width":"20px"
                    },
                    {
                        "targets":3,
                        "width":"25px"
                    },{
                        "targets":5,
                        "render":function (d) {
                            return moment(d).format('DD MMM YYYY H:mm')
                        },
                        "width":"35px"
                    },{
                        "targets":2,
                        "visible":false,
                    },{
                        "targets":4,
                        "width":"35px",
                        "render":function (d,t,r) {                           
                            if(r.event==='clicked'){
                             var tt= getClick(r.recipient)
                             var text='<ul class="list-group">';
                             for(var i =0;i<=tt.length-1;i++){
                                 text+='<li class="list-group-item">'+tt[i].url+'</li>'
                             }
                             text+='</ul>'
                                return " <a href='#' onclick='event.preventDefault()' class='' data-toggle='popover' title='Detail' data-content='"+text+"'>Detail</a>"
                            }else if(r.event==='failed'){
                              var txt=r.delivery_status
                                return " <a href='#' onclick='event.preventDefault()' class='' data-toggle='popover' title='Detail' data-content='"+escapeHtml(txt)+"'>Detail</a>"
                            }else if(r.event==='delivered'){
                                return ''
                            } else if(r.event==='unsubscribed') {
                                return  " <a href='#' onclick='event.preventDefault()' class='' data-toggle='popover' title='Detail' data-content='Unsubscribed'>Detail</a>"
                            } else if(r.event==='opened'){
                                return " "
                            }else if(r.event==='processed'){
                                return " "
                            }
                        }
                    }],
                    "createdRow": function( row, data, dataIndex){
                    $('td',row).eq(1).css('text-transform', "capitalize")
                  //  $('td',row).eq(2).css('text-transform', "capitalize")
                    switch (data['event']){
                        case "failed":
                            return $('td',row).eq(1).addClass('alert alert-danger')
                            break
                        case "delivered":
                            return $('td',row).eq(1).addClass('alert')
                            break
                        case "opened":
                            return $('td',row).eq(1).addClass('alert alert-info')
                            break
                        case "clicked":
                            return $('td',row).eq(1).addClass('alert alert-success')
                            break
                        case "unsubscribed":
                            return $('td',row).eq(1).addClass('alert alert-warning')
                            break
                        case "processed":
                            return $('td',row).eq(1).addClass('alert ')
                            break
                        default: $('td',row).eq(1).addClass('alert alert-light')
                    }
                },
                "pageLength":25,              
                "columns": [
                    {"data":"id"},
                    { "data": "event" },                   
                    { "data": "url" },
                    { "data": "recipient" },
                    { "data": "delivery_status" },
                    { "data": 'timestamp' }

                ],                
            })
            t.on('draw.dt', function () {
                var info = t.page.info();
                t.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1 + info.start;
                });
            });

        })
        $(document).popover({
            selector: '[data-toggle=popover]',
            html: true,
            trigger: 'click',
            placement:'left',container: 'body'
        }).on("show.bs.popover", function() {
            return $(this).data("bs.popover").tip().css({
                width: "700px"
            });
        });
        $('html').on('click', function(e) {
            if (typeof $(e.target).data('original-title') == 'undefined' &&
                !$(e.target).parents().is('.popover.in')) {
                $('[data-original-title]').popover('hide');
            }
        });
    })
</script>
@endsection