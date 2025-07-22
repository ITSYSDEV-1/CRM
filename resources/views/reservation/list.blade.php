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
                                <h2>Prestay Contact List</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>

                            @can('8.1.1_view_prestay_contact_list')
                            <div class="body">
                                <table style="font-size: 13px"
                                       class="table table-bordered table-striped table-hover responsive"
                                       id="reservationTable" width="100%">
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
                                        <th class="align-center">Status</th>
                                        <th class="align-center">Booking Source</th>
                                        <th class="align-center">Pre Stay Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($contacts as $key=>$contact)
                                        <tr>

                                        
                                            <td>{{$key+1}}</td>
                                            <td>{{$contact->folio_master}}</td>
                                            <td>{{rtrim(strtoupper($contact->salutation), '.')}}. {{$contact->fname}} {{$contact->lname}}</td>
                                            <td>{{$contact->email}}</td>
                                            <td>{{\Carbon\Carbon::parse($contact->dateci)->format('d M Y')}}</td>
                                            <td>{{\Carbon\Carbon::parse($contact->dateco)->format('d M Y')}}</td>
                                            <td>{{$contact->room}}</td>
                                            <td>{{$contact->roomtype}}</td>
                                            <td>
                                                @if($contact->foliostatus=='O')
                                                    Checked Out
                                                @elseif($contact->foliostatus=='I')
                                                    Inhouse
                                                @elseif($contact->foliostatus=='X')
                                                    Cancel
                                                @elseif($contact->foliostatus=='G')
                                                    Guaranteed
                                                @else
                                                    Confirm
                                                @endif
                                            </td>
                                            <td>{{$contact->source}}</td>
                                            <td style="text-align: center">
                                                @if($contact->prestay_status != null and $contact->prestay_status['next_action'] != 'NOACTION')
                                                    @if($contact->prestay_status['next_action'] == 'SENDTOWEB')
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                    @elseif($contact->prestay_status['next_action'] == 'FETCHFROMWEB' && $contact->sendtoguest_at['sendtoguest_at'] == null)
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                    @elseif($contact->prestay_status['next_action'] == 'FETCHFROMWEB' && $contact->sendtoguest_at['sendtoguest_at'] != null)
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                        <i class="fa fa-envelope-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data sent to guest"></i>
                                                    @elseif($contact->prestay_status['next_action'] == 'COMPLETED')
                                                        <i class="fa fa-check-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data ready to send"></i>
                                                        <i class="fa fa-envelope-square" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Data sent to guest"></i>
                                                        @if($contact->registration_code != null)
                                                            @can('8.1.2_download_form_registration')
                                                                <a href="{{route('registrationformprint',$contact->registration_code['registration_code'])}}"><i class="fa fa-file-pdf-o" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Pre Stay Completed, click to generate registration form"></i></a>
                                                            @else
                                                                <i class="fa fa-file-pdf-o" style="font-size: x-large; color: #ccc;" data-toggle="tooltip" data-placement="top" title="No permission to download registration form"></i>
                                                            @endcan
                                                        @else
                                                            @can('8.1.2_download_form_registration')
                                                                <a href="#"><i class="fa fa-file-pdf-o" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Pre Stay Completed, click to generate registration form"></i></a>
                                                            @else
                                                                <i class="fa fa-file-pdf-o" style="font-size: x-large; color: #ccc;" data-toggle="tooltip" data-placement="top" title="No permission to download registration form"></i>
                                                            @endcan
                                                        @endif
                                                    @else
                                                        <i class="fa fa-times-circle" style="font-size: x-large;" data-toggle="tooltip" data-placement="top" title="Pre Stay Canceled"></i>
                                                    @endif
                                                @else
                                                    {{$contact->prestay_status = '-'}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <div class="body">
                                    
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    @can('8.1.1_view_prestay_contact_list')
    <script>
        $(document).ready(function () {
            $('#reservationTable').DataTable();
        });
    </script>
    @endcan
@endsection
