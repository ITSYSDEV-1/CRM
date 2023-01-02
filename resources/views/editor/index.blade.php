@extends('layouts.master')
@section('title')
    Dashboard | {{ config('app.name') }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel tile " >
                    <div class="x_title">
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
                                <div id="bee-plugin-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
@section('script')
    <script src="https://app-rsrc.getbee.io/plugin/BeePlugin.js" type="text/javascript"></script>
    @endsection