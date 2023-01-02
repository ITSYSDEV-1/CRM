@extends('layouts.app')
@section('title')
    Segment Management  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('main-content')
    <section class="content">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h3>Segment</h3>

                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="{{ url('segment/create') }}" title="Create New Segment" class="waves-effect btn btn-sm bg-teal"> <i class="material-icons">add</i> Create New Segment</a>
                            </li>
                        </ul>
                    </div>
                    <div class="body">
                        <table class="table table-bordered table-striped table-hover dataTable js-basic-example" width="100%">
                            <thead>
                            <th width="10px">No</th>
                            <th>Name</th>
                            <th >Manage</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection