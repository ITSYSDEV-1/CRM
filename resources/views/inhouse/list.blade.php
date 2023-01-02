@extends('layouts.master')
@section('title')
    Contact List | {{ \App\Models\Configuration::first()->hotel_name.' '.\App\Models\Configuration::first()->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>In House Guest Contact List</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>

                            <div class="body">
                                <table style="font-size: 13px" class="table table-bordered table-striped table-hover responsive" id="inhouseTable" width="100%">
                                    <thead class="bg-teal">
                                    <tr class="tr align-center">
                                        <th class="align-center">No</th>
                                        <th class="align-center">Folio Master</th>
                                        <th class="align-center">Full Name</th>
                                        <th class="align-center">Email</th>
                                        <th class="align-center">CI Date</th>
                                        <th class="align-center">CO Date</th>
                                        <th class="align-center">Room</th>
                                        <th class="align-center">Room Type</th>
                                        <th class="align-center">Booking Source</th>
                                        <th class="align-center">Room Night</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($contacts as $key=>$contact)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$contact->folio_master}}</td>
                                            <td>{{$contact->salutation}} {{$contact->fname}} {{$contact->lname}}</td>
                                            <td>{{$contact->email}}</td>
                                            <td>{{\Carbon\Carbon::parse($contact->dateci)->format('d M Y')}}</td>
                                            <td>{{\Carbon\Carbon::parse($contact->dateco)->format('d M Y')}}</td>
                                            <td>{{$contact->room}}</td>
                                            <td>{{$contact->roomtype}}</td>
                                            <td>{{$contact->source}}</td>
                                            <td>{{\Carbon\Carbon::parse($contact->dateci)->diffInDays(\Carbon\Carbon::parse($contact->dateco))}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
            $('#inhouseTable').DataTable({
                // order: [[0, 'desc']],
            });
        });
    </script>
@endsection
