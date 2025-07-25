@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="x_panel tile ">
                            <div class="x_title">
                                <h3>Segment Management</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                                @can('4.2.4_view_list_segment')
                                @canany(['4.2.1_add_new_segment', '3.3.2_create_segment'])
                                    <a href="{{ url('segments/create') }}" title="Create New Segment" class="btn btn-success"><i class="fa fa-plus"></i> Create New Segment</a>
                                @endcanany
                                @endcan
                            </div>
                            <div class="x_content" >

                                @can('4.2.4_view_list_segment')
                                <div class="row clearfix">
                                    <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%" >
                                        <thead>
                                        <th width="10px">No</th>
                                        <th>Name</th>
                                        <th>Used in</th>
                                        @can('4.2.3_delete_segment')
                                        <th>Manage</th>
                                        @endcan
                                        </thead>
                                        <tbody>
                                        @foreach(\App\Models\Segment::all() as $key=>$item)
                                            <tr>

                                                <td>{{ $key+1 }}</td>
                                                <td>
                                                    @can('4.2.2_view_detail_segment')
                                                        <a href="{{ url('segments').'/'.$item->id}}"> {{ $item->name }}</a>
                                                    @else
                                                        {{ $item->name }}
                                                    @endcan
                                                </td>
                                                <td>@if($item->campaign->count()>0)
                                                        <ul class="list-unstyled">
                                                            @foreach($item->campaign as $campaign)
                                                                <li>{{ $campaign->name }} </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif</td>
                                                @can('4.2.3_delete_segment')
                                                <td>{!! Form::open(['method' => 'DELETE','route' => ['segments.destroy', $item->id],'id'=>$item->id]) !!}
                                                    {!! Form::close() !!}
                                                    <a href="#" title="Delete Segment" onclick="return swal({title:'Delete Confirmation',text:'This Segment will permanently deleted',type:'warning',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#DD6B55',
                                                            confirmButtonText:'Delete',
                                                            cancelButtonText: 'No',
                                                            closeOnConfirm: false,
                                                            closeOnCancel: false
                                                            },
                                                            function(isConfirm){
                                                            if (isConfirm) {
                                                            $('#{{$item->id}}').submit();
                                                            } else {
                                                            swal('Cancelled', 'Delete Segment Cancelled','error');
                                                            }
                                                            });"><i class="fa fa-trash" style="font-size: 1.5em">  </i>
                                                    </a>
                                                </td>
                                                @endcan
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endcan
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
            $('.dataTable').dataTable()
        });
    </script>
@endsection
