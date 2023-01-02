@extends('layouts.master')
@section('title')
    Campaign Management | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    @foreach($campaigns as $item)

        @if($item->status=='Draft' || $item->status=='Scheduled')
        <div  class="modal " id="ScheduleModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="ScheduleModalLabel">
            <div class="modal-dialog " role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="ScheduleModalLabel">{{ $item->name }} Schedule</h4>
                    </div>
                    <div class="modal-body">
                        <label for="schedule{{$item->id}}}" class="control-label">Schedule</label>
                        <input type="text" class="form-control datetimepicker" name="scheduleInput{{$item->id}}" id="scheduleInput{{ $item->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="scheduleSave{{$item->id}}">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

        <div  class="modal " id="myModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{ $item->name }}</h4>
                    </div>
                    <div class="modal-body">
                        @if(!empty($item->template[0]))
                            {!!   $item->template[0]->content !!}
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        <div  class="modal " id="acceptedModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="acceptedModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="acceptedModalLabel"> Sent Email</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="100%"> Recepient</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->emailresponse()->whereIn('event',['processed','sent','open','click'])->groupBy('email_id')->get() as $email)
                                <tr>
                                    <td> {{ $email->recepient }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div  class="modal " id="deliveredModel{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="deliveredModelLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="deliveredModelLabel"> Delivered Email</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="100%"> Recepient</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->emailresponse()->whereIn('event',['sent','open','click'])->groupBy('email_id')->get() as $email)
                                <tr>
                                    <td> {{ $email->recepient }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        <div  class="modal " id="openedModel{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="openedModelLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="openedModelLabel"> Opened Email</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="100%"> Opened </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->emailresponse()->whereIn('event',['open','click'])->groupBy('email_id')->get() as $email)
                                <tr>
                                    <td> {{ $email->recepient }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        <div  class="modal " id="unsubModel{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="unsubModelLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="unsubModelLabel"> Unsubscribe </h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="25%"> Recipient </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->emailresponse()->whereIn('event',['unsubscribe'])->groupBy('email_id')->get() as $email)
                                <tr>
                                    <td> {{ $email->recepient }} </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div  class="modal " id="clckedModel{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="clickedModelLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="clickedModelLabel"> Clicked Email</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="25%"> Recepient</th>
                                <th width="75%"> URL</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['click']) as $url)
                            
                                <tr>
                                    <td>                                       
                                    <strong>{{ $url->recepient }}</strong>
                                    </td>
                                    <td>
                                      @if(count(explode(';',$url->url))>1)
                                      <ul>
                                        @foreach(explode(';',$url->url) as $exp)
                                        <li>{{ $exp }}</li>
                                        @endforeach
                                      </ul>
                                      @else
                                      <ul>
                                        <li>{{ $url->url }}</li>
                                      </ul>
                                      @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        <div  class="modal " id="failedModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="failedModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="failedModalLabel"> Clicked Email</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="25%"> Recepient</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['hardbounce','softbounce','invalid']) as $email)
                                <tr>
                                    <td>
                                        {{ $email->recepient }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div  class="modal " id="rejectedModel{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectedModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="rejectedModalLabel"> Rejected Email</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="25%"> Recepient</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['dropped']) as $email)
                                <tr>
                                    <td>
                                        {{ $email->recepient }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

       
        @if($item->type=='internal')
        <div  class="modal " id="recepientModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="recepientModalLabel">
            <div class="modal-dialog  modal-lg " role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="recepientModalLabel"> Campaign {{ $item->name }} </h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="20%"> No </th>
                                <th width="60%"> Name
                                <th width="20%"> Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item->contact as $key=>$contact)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $contact->fname.' '.$contact->lname }}</td>
                                    <td>{{ $contact->pivot->status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @else    
        <div  class="modal " id="recepientModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="recepientModalLabel">
            <div class="modal-dialog  modal-lg " role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="recepientModalLabel"> Campaign {{ $item->name }} </h4>
                    </div>
                    <div class="modal-body">                
                        <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$item->id}}">
                            <thead>
                            <tr>
                                <th width="20%"> No </th>
                                <th width="60%"> Name
                                <th width="20%"> Status</th>
                            </tr>
                            </thead>
                            <tbody>                    
                            @foreach($item->external as $key=>$contact)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $contact->fname.' '.$contact->lname }}</td>
                                    <td>{{ $contact->pivot->status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

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
                                <a href="{{ url('campaign/create') }}" title="Create New Campaign" class=" btn btn-success"><i class="fa fa-plus"> </i> Create New Campaign</a>
                            </div>
                            <div class="x_content" >

                                <div class="row clearfix">
                                    <table class="table  table-striped table-hover dataTable js-basic-example">
                                        <thead>
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
                                        <th>Manage</th>
                                        </thead>
                                        <tbody>

                                        @foreach($campaigns->where('type','=','internal') as $key=>$tem)                                    
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $tem->name }}  </td>
                                                <td> @if(count($tem->segment)){{ $tem->segment[0]->name }}@endif</td>
                                                <td class="status{{ $tem->id }}"> {{ $tem->status }} {{ $tem->status=='Scheduled' ? '@'.\Carbon\Carbon::parse($tem->schedule->schedule)->format('d M Y H:i'):'' }}</td>
                                                <td> {{ $tem->schedule->schedule }}</td>
                                                <td><a href="#acceptedModal{{ $tem->id }}" data-toggle="modal" data-target="#acceptedModal{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['processed','sent','open','click'])->groupBy('email_id')->count() }}</a></td>
                                                <td><a href="#deliveredModel{{ $tem->id }}"  data-toggle="modal" data-target="#deliveredModel{{$tem->id}}" title="" >{{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['sent','open','click'])->groupBy('email_id')->count() }}</a></td>
                                                <td><a href="#openedModel{{$tem->id}}" data-toggle="modal" data-target="#openedModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['open','click'])->groupBy('email_id')->count() }}</a></td>
                                                <td><a href="#clckedModel{{ $tem->id }}" data-toggle="modal" data-target="#clckedModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['click'])->count() }}</a></td>
                                                <td><a href="#unsubModel{{ $tem->id }}" data-toggle="modal" data-target="#unsubModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['unsubscribe'])->count() }}</a></td>
                                                <td><a href="#failedModal{{ $tem->id }}" data-toggle="modal" data-target="#failedModal{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['hardbounce','softbounce','invalid'])->count() }}</a></td>
                                                <td><a href="#rejectedModel{{ $tem->id }}" data-toggle="modal" data-target="#rejectedModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['dropped'])->groupBy('email_id')->count() }}</a></td>
                                                <td>{!! Form::open(['method' => 'DELETE','route' => ['campaign.destroy', $tem->id],'id'=>$tem->id]) !!}
                                                    {!! Form::close() !!}
                                                    <a href="#myModal{{$tem->id}}" data-toggle="modal" title="Preview Template" data-target="#myModal{{$tem->id}}"> <i class="fa  fa-eye" style="font-size: 1.5em"> </i>  </a>
                                                    @if($tem->status=='Draft' || $tem->status=='Scheduled')
                                                        <a href="#ScheduleModal{{$tem->id}}" data-toggle="modal" data-target="#ScheduleModal{{$tem->id}}" title="Set Schedule"> <i class="fa fa-calendar-check-o"  style="font-size: 1.5em"> </i></a>
                                                    @endif
                                                    <a href="#recepientModal{{$tem->id}}" data-toggle="modal" data-target="#recepientModal{{$tem->id}}" title="Show Recepient"><i class="fa fa-users" style="font-size: 1.5em"></i> </a>
                                                    <a href="#" title="Delete Template" onclick="return swal({title:'Delete Confirmation',text:'This Template will permanently deleted',type:'warning',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#DD6B55',
                                                            confirmButtonText:'Delete',
                                                            cancelButtonText: 'No',
                                                            closeOnConfirm: false,
                                                            closeOnCancel: false
                                                            },
                                                            function(isConfirm){
                                                            if (isConfirm) {
                                                            $('#{{$tem->id}}').submit();
                                                            } else {
                                                            swal('Cancelled', 'Delete Template Cancelled','error');
                                                            }
                                                            });"><i class="fa fa-trash" style="font-size: 1.5em">  </i>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($campaigns->where('type','=','external') as $key=>$tem)                                    
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $tem->name }}  </td>
                                                <td> @if(count($tem->externalSegment)){{ $tem->externalSegment[0]->category }}@endif</td>
                                                <td class="status{{ $tem->id }}"> {{ $tem->status }} {{ $tem->status=='Scheduled' ? '@'.\Carbon\Carbon::parse($tem->schedule->schedule)->format('d M Y H:i'):'' }}</td>
                                                <td> {{ $tem->schedule->schedule }}</td>
                                                <td><a href="#acceptedModal{{ $tem->id }}" data-toggle="modal" data-target="#acceptedModal{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['processed','sent','open','click'])->groupBy('email_id')->count() }}</a></td>
                                                <td><a href="#deliveredModel{{ $tem->id }}"  data-toggle="modal" data-target="#deliveredModel{{$tem->id}}" title="" >{{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['sent','open','click'])->groupBy('email_id')->count() }}</a></td>
                                                <td><a href="#openedModel{{$tem->id}}" data-toggle="modal" data-target="#openedModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['open','click'])->groupBy('email_id')->count() }}</a></td>
                                                <td><a href="#clckedModel{{ $tem->id }}" data-toggle="modal" data-target="#clckedModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['click'])->count() }}</a></td>
                                                <td><a href="#unsubModel{{ $tem->id }}" data-toggle="modal" data-target="#unsubModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['unsubscribe'])->count() }}</a></td>
                                                <td><a href="#failedModal{{ $tem->id }}" data-toggle="modal" data-target="#failedModal{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['hardbounce','softbounce','invalid'])->groupBy('email_id')->count() }}</a></td>
                                                <td><a href="#rejectedModel{{ $tem->id }}" data-toggle="modal" data-target="#rejectedModel{{ $tem->id }}" title="" > {{ $tem->emailresponse->where('campaign_id','=',$tem->id)->whereIn('event',['dropped'])->groupBy('email_id')->count() }}</a></td>
                                                <td>{!! Form::open(['method' => 'DELETE','route' => ['campaign.destroy', $tem->id],'id'=>$tem->id]) !!}
                                                    {!! Form::close() !!}
                                                    <a href="#myModal{{$tem->id}}" data-toggle="modal" title="Preview Template" data-target="#myModal{{$tem->id}}"> <i class="fa  fa-eye" style="font-size: 1.5em"> </i>  </a>
                                                    @if($tem->status=='Draft' || $tem->status=='Scheduled')
                                                        <a href="#ScheduleModal{{$tem->id}}" data-toggle="modal" data-target="#ScheduleModal{{$tem->id}}" title="Set Schedule"> <i class="fa fa-calendar-check-o"  style="font-size: 1.5em"> </i></a>
                                                    @endif
                                                    <a href="#recepientModal{{$tem->id}}" data-toggle="modal" data-target="#recepientModal{{$tem->id}}" title="Show Recepient"><i class="fa fa-users" style="font-size: 1.5em"></i> </a>
                                                    <a href="#" title="Delete Campaign" onclick="return swal({title:'Delete Confirmation',text:'This Campaign will permanently deleted',type:'warning',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#DD6B55',
                                                            confirmButtonText:'Delete',
                                                            cancelButtonText: 'No',
                                                            closeOnConfirm: false,
                                                            closeOnCancel: false
                                                            },
                                                            function(isConfirm){
                                                            if (isConfirm) {
                                                            $('#{{$tem->id}}').submit();
                                                            } else {
                                                            swal('Cancelled', 'Delete Campaign Cancelled','error');
                                                            }
                                                            });"><i class="fa fa-trash" style="font-size: 1.5em">  </i>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
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
            $('table.datatable').dataTable()
           // $('table.dataTable').dataTable()
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
@endsection
