@extends('layouts.master')
@section('title')
    Campaign Management | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div  class="modal " id="recepientModal" tabindex="-1" role="dialog" aria-labelledby="recepientModalLabel">
        <div class="modal-dialog  modal-lg " role="document">
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="recepientModalLabel"> Campaign</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-hover subtable responsive js-basic-example"  width="100%" id="recepienttable">
                        <thead>
                        <tr>
                            <th width="20%"> No </th>
                            <th width="60%"> Name </th>
                            <th width="20%"> Status</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div  class="modal " id="ScheduleModal" tabindex="-1" role="dialog" aria-labelledby="ScheduleModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="ScheduleModalLabel">Schedule</h4>
                </div>
                <div class="modal-body">
                    <label for="schedule" class="control-label">Schedule</label>
                    <input type="text" class="form-control datetimepicker" name="scheduleInput" id="scheduleInput">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="UpdateSchedule">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div  class="modal " id="ModalTemplate" tabindex="-1" role="dialog" aria-labelledby="ModalTemplateLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="ModalTemplateLabel"></h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <div  class="modal " id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" >
                                    <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="ModalLabel"> Delivered Email</h4>
                                        </div>
                                    <div class="modal-body">
                                            <table class="table table-bordered table-striped table-hover subtable responsive "  width="100%" id="modaltable">
                                                    <thead>
                                                    <tr>
                                                            <th width="100%"> Recepient</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                        </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                </div>
                        </div>
                </div>

    <div  class="modal " id="Modal2" tabindex="-1" role="dialog" aria-labelledby="Modal2Label">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="Modal2Label"> Delivered Email</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-hover subtable responsive "  width="100%" id="modal2table">
                        <thead>
                        <tr>
                            <th width="100%"> Recepient</th>
                            <th width="100%"> URL </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="x_panel tile ">
                            <div class="x_title">
                                <h3>Campaign Management</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            @can('4.1.1_add_new_campaign')
                                <a href="{{ url('campaign/create') }}" title="Create New Campaign" class=" btn btn-success"><i class="fa fa-plus"> </i> Create New Campaign</a>
                                <a href="{{route('campaign.calendar')}}"><i class="fa fa-calendar"></i> Campaign Calendar</a>
                            @endcan    
                        </div>
                            <div class="x_content" >

                                <div class="row clearfix">
                                    <table id="campaigntable" class="table table-bordered table-striped table-hover js-basic-example ">
                                        <thead>
                                        <tr>
                                            <th width="10px">No</th>
                                            <th>Name</th>
                                            <th>Segment</th>
                                            <th>Status</th>
                                            <th>Schedule</th>
                                            <th>Accepted</th>
                                            <th>Delivered</th>
                                            <th>Opened</th>
                                            <th>Clicked</th>
                                            <th>Unsubscribed</th>
                                            <th>Failed</th>
                                            <th>Rejected</th>
                                            @if(auth()->user()->can('4.1.2_preview_campaign') || 
                                            auth()->user()->can('4.1.3_set_schedule') || 
                                            auth()->user()->can('4.1.4_show_recipient') || 
                                            auth()->user()->can('4.1.5_delete_campaign'))
                                            <th>Manage</th>
                                            @endif
                                        </tr>
                                        </thead>

                                    </table>
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
        $(document).ready(function () {
            var t= $('#campaigntable').DataTable({
                "autoWidth": false,
                "processing": true,
                "language": {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                },
                "serverSide": true,
                "pageLength":15,
                "order": [[ 4, "desc" ]], // Urutkan berdasarkan kolom schedule (index 4) secara descending
                "ajax":{
                    "url": "{{ route('campaignlist') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{
                        _token: "{{csrf_token()}}",
                    },
                    "error": function (error){
                        console.log(error.responseJSON)
                    }
                },
                "columns": [
                    { "data": null },
                    { "data": "name" },
                    { "data": "segment" },
                    { "data": "status" },
                    { "data": "schedule"},
                    { "data": "accepted"},
                    { "data": "delivered"},
                    { "data": "opened"},
                    { "data": "clicked"},
                    { "data": "unsubscribed"},
                    { "data": "failed"},
                    { "data": "rejected"},
                    @if(auth()->user()->can('4.1.2_preview_campaign') || 
                    auth()->user()->can('4.1.3_set_schedule') || 
                    auth()->user()->can('4.1.4_show_recipient') || 
                    auth()->user()->can('4.1.5_delete_campaign'))
                    { "data": null},
                    @endif
                ],
                "columnDefs":[
                {
                    "targets":0,
                    "render":function (data,type,row,meta) {
                        var info = $('#campaigntable').DataTable().page.info();
                        return info.start + meta.row + 1;
                    }
                },
                {
                "targets":3,
                "render":function (data,type,row) {
                    var status = ''
                        if (data==='Scheduled'){
                        status='Scheduled @ '+ moment(row.schedule).format('YYYY MMMM DD HH:mm')
                        }else{
                            status=data
                        }
                    return status
                }
                },
                {
                    "targets":4,
                    "render":function (data,type,row) {
                        var date =moment(data).format('YYYY MMMM DD HH:mm')
                        return date
                    }
                },
                {
                    "targets":[8],
                    "render":function (data,type,row) {
                        var result=[]
                        var urls=[]
                        var res=''
                        $.each(data,function(k,v){
                            urls=v.url.split(';')
                            $.each(urls, function(i, e) {
                                if ($.inArray(e, result) == -1) result.push(e);
                            });
                            var list='<ul>'
                            $.each(result,function(i,v){
                                list+='<li>'+v+'</li>'
                            })
                            list+='</ul>'
                            res+='<tr><td>'+v.recepient+'</td><td>'+list+'</td></tr>'
                        })
                            var count = $.map(data, function(n, i) { return i; }).length;
                            return '<a href="javascript:void(0)" id="'+row.id+'" title="" onclick="openModal2(\'' + res + '\') "> '+count+'</a> '

                    }
                },
                    @if(auth()->user()->can('4.1.2_preview_campaign') || 
                    auth()->user()->can('4.1.3_set_schedule') || 
                    auth()->user()->can('4.1.4_show_recipient') || 
                    auth()->user()->can('4.1.5_delete_campaign'))
                    {
                        "targets":12,
                        "sortable":false,
                        "render":function(data,type,row){
                        // console.log(data.id)
                                if(data.template.length>0){
                                    var str=escape(data.template[0].content)
                                }else{
                                    str=''
                                }
                    
                            var id=data.id
                            var actions = ''
                            
                            // Preview Template - 4.1.2_preview_campaign
                            @can('4.1.2_preview_campaign')
                            actions += '<a href="javascript:void(0)"  title="Preview Template" onclick="openModalTemplate(\''+str+'\')" style="margin-right: 5px;"> <i class="fa fa-eye" style="font-size: 1.5em"></i></a>'
                            @endcan
                            
                            // Set Schedule - 4.1.3_set_schedule
                            if(data.status==='Draft' || data.status==='Scheduled'){
                                @can('4.1.3_set_schedule')
                                actions += ' <a href="javascript:void(0)" title="Set Schedule"  onclick="openModalSchedule(\''+id+'\')" style="margin-right: 5px;"> <i class="fa fa-calendar-check-o " style="font-size: 1.5em"></i></a>'
                                @endcan
                            }
                            
                            // Show Recipient - 4.1.4_show_recipient
                            @can('4.1.4_show_recipient')
                            actions += ' <a href="javascript:void(0)" title="Show Recepient" onclick="selectRecepient(\''+data.id+'\')" style="margin-right: 5px;"><i class="fa fa-users" style="font-size: 1.5em"></i> </a>'
                            @endcan
                            
                            // Delete Campaign - 4.1.5_delete_campaign
                            @can('4.1.5_delete_campaign')
                            actions += '<a href="#" title="Delete Campaign" onclick="return swal({title:\'Delete Confirmation\',text:\'This campaign will permanently deleted\',type:\'warning\',\n' +
                                '                                                            showCancelButton: true,\n' +
                                '                                                            confirmButtonColor: \'#DD6B55\',\n' +
                                '                                                            confirmButtonText:\'Delete\',\n' +
                                '                                                            cancelButtonText: \'Cancel\',\n' +
                                '                                                            closeOnConfirm: true,\n' +
                                '                                                            closeOnCancel: true,\n' +
                                '                                                            showLoaderOnConfirm: true\n' +
                                '                                                            },\n' +
                                '                                                            function(isConfirm){\n' +
                                '                                                            if (isConfirm) {\n' +
                                '                                                               deleteCampaign(\''+data.id+'\')\n' +
                                '                                                            }\n' +
                                '                                                            });" data-campaign-id="'+data.id+'"><i class="fa fa-trash" style="font-size: 1.5em">  </i>\n' +
                                '                                                    </a>'
                            @endcan
                            
                            return actions
                        }
                    },
                    @endif
                ]
            });
        })

        $('[id^=scheduleSave]').on('click',function () {
            var id_=this.id;
            id_=id_.replace('scheduleSave','')
            var val=$('#scheduleInput'+id_).val();
            $.ajax({
                url:'updateschedule',
                type:'POST',
                data:{
                    id:id_,
                    _token:'{{ csrf_token() }}',
                    value:val,
                },
                success:function () {
                    location.reload(true);
                }
            })
        });

        $('[id^=scheduleInput]').each(function () {
            var start = new Date(),
                prevDay,
                startHours = 9;

            // 09:00 AM
            start.setHours(9);
            start.setMinutes(0);

            // If today is Saturday or Sunday set 10:00 AM
            if ([6, 0].indexOf(start.getDay()) != -1) {
                start.setHours(10);
                startHours = 10
            }

            $(this).datepicker({
                timepicker: true,
                language: 'en',
                dateFormat: 'dd M yyyy ',
                timeFormat: 'hh:ii aa',
                minDate: new Date(),
//                    startDate: start,
//                    minHours: startHours,
//                    maxHours: 18,
                onSelect: function (fd, d, picker) {

                    // Do nothing if selection was cleared
                    if (!d) return;

                    var day = d.getDay();

                    // Trigger only if date is changed
                    if (prevDay != undefined && prevDay == day) return;
                    prevDay = day;

                }
            })
        });

    </script>
    <script>
        function deleteCampaign(id){
            // Cek apakah sedang dalam proses delete untuk mencegah double-click
            if (window.deletingCampaign) {
                return false;
            }
            
            // Set flag untuk mencegah double-click
            window.deletingCampaign = true;
            
            // Disable semua tombol delete untuk mencegah multiple delete
            $('a[data-campaign-id]').addClass('disabled').css('pointer-events', 'none');
            
            // Tampilkan loading indicator
            swal({
                title: 'Processing...',
                text: 'Cancelling campaign and notifying Campaign Center...',
                type: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            
            // Simpan halaman saat ini sebelum delete
            var currentPage = $('#campaigntable').DataTable().page();
            
            // Cek apakah campaign ini memiliki children
            var campaignData = $('#campaigntable').DataTable().rows().data().toArray();
            var hasChildren = campaignData.some(function(campaign) {
                return campaign.parent_campaign_id == id;
            });
            
            $.ajax({
                url:'{{ route('campaign.delete') }}',
                type:'POST',
                data:{
                    _token:'{{ csrf_token() }}',
                    id:id
                },
                success:function(response){
                    // Reset flag
                    window.deletingCampaign = false;
                    
                    // Re-enable tombol delete
                    $('a[data-campaign-id]').removeClass('disabled').css('pointer-events', 'auto');
                    
                    if(response === 'ok' || (response.success && response.success === true)){
                        // Tutup loading dan tampilkan success
                        swal({
                            title: 'Deleted!',
                            text: hasChildren ? 'Parent campaign and all children have been deleted successfully.' : 'Campaign has been deleted successfully.',
                            type: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Reload DataTable dan kembali ke halaman yang sama
                        $('#campaigntable').DataTable().ajax.reload(function() {
                            var table = $('#campaigntable').DataTable();
                            var info = table.page.info();
                            
                            // Jika halaman saat ini kosong dan bukan halaman pertama, pindah ke halaman sebelumnya
                            if (info.recordsDisplay > 0 && currentPage > 0 && info.end === info.start - 1) {
                                table.page(currentPage - 1).draw('page');
                            } else if (currentPage < info.pages) {
                                table.page(currentPage).draw('page');
                            }
                        }, false);
                    } else {
                        // Tampilkan error dari response
                        var errorMessage = 'Failed to delete campaign';
                        if (response.message) {
                            errorMessage += ': ' + response.message;
                        }
                        swal('Error!', errorMessage, 'error');
                    }
                },
                error:function(xhr){
                    // Reset flag
                    window.deletingCampaign = false;
                    
                    // Re-enable tombol delete
                    $('a[data-campaign-id]').removeClass('disabled').css('pointer-events', 'auto');
                    
                    var errorMessage = 'Failed to delete campaign';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage += ': ' + response.message;
                        } else {
                            errorMessage += ': ' + xhr.responseText;
                        }
                    } catch (e) {
                        errorMessage += ': ' + xhr.responseText;
                    }
                    
                    swal('Error!', errorMessage, 'error');
                },
                timeout: 30000 // 30 detik timeout
            });
        }
        function selectRecepient(id){
            var loading ='<i class="fa fa-spinner fa-spin fa-3x fa-fw center-align"></i><span class="sr-only">Loading...</span>';
            $('#recepientModal tbody').empty()
            $('#recepientModal tbody').append(loading)
            $('#recepientModal').modal('toggle')
            $.ajax({
                url:'{{ route('campaignrecepient') }}',
                dataType : 'json',
                type:'POST',
                data:{
                    _token:'{{ csrf_token() }}',
                    id:id
                },
                success: function(data){
                    var recepient=''
                    $.each(data,function(i,v){
                        // recepient+="<tr><td>"+parseInt(i)+1+"</td><td>"+v.fname+" "+v.lname+"</td><td>"+v.pivot.status+"</td></tr>"
                        recepient+="<tr><td>"+(i+1)+"</td><td>"+v.fname+" "+v.lname+"</td><td>"+v.pivot.status+"</td></tr>"
                    })
                    $('#recepientModal tbody').empty()
                    $('#recepientModal tbody').append(recepient)
                    var tb=$('#recepientModal #recepienttable').DataTable()
                }
            })

        }
        function openModalSchedule(val) {
            $('#ScheduleModal').modal('toggle')
            $('#UpdateSchedule').on('click', function () {
                var id_=val;
                var value=$('#scheduleInput').val();
                $.ajax({
                    url:'updateschedule',
                    type:'POST',
                    data:{
                        id:id_,
                        _token:'{{ csrf_token() }}',
                        value:value,
                    },
                    success:function () {
                        location.reload(true);
                    }
                })
            })
        }
        function openModal(val){


          $('#Modal tbody').empty()
          $('#Modal tbody').append(val)
          var tb=$('#Modal .subtable').DataTable()
          $('#Modal').modal('toggle')
        }
        function openModal2(val){


            $('#Modal2 tbody').empty()
            $('#Modal2 tbody').append(val)
            var tb=$('#Modal2 .subtable').DataTable()
            $('#Modal2').modal('toggle')
        }
        function openModalTemplate(val) {

            $('#ModalTemplate .modal-body').empty()
            $('#ModalTemplate .modal-body').append(unescape(val))

            $('#ModalTemplate').modal('toggle')

        }
        $('#Modal').on('hidden.bs.modal',function () {
            var tb=$('#Modal .subtable').DataTable()
            tb.clear().draw()
            tb.destroy()
        })
        $('#Modal2').on('hidden.bs.modal',function () {
            var tb=$('#Modal2 .subtable').DataTable()
            tb.clear().draw()
            tb.destroy()
        })
        $('#recepientModal').on('hidden.bs.modal',function () {
            var tb=$('#recepientModal #recepienttable').DataTable()
            tb.clear().draw()
            tb.destroy()
        })
    </script>
@endsection
