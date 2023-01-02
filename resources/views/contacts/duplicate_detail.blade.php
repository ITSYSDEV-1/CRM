@extends('layouts.master')
@section('title')
    Duplicate Contacts Detail| {{ config('app.name') }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>Duplicate Contact Detail</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>

                            <div class="body">
                                <table class="table  table-hover datatable responsive js-basic-example" width="100%">
                                    <thead class="bg-teal">
                                    <tr>

                                        <th class="align-center">Folio</th>
                                        <th class="align-center">Folio Master</th>
                                        <th class="align-center">Full Name</th>
                                        <th class="align-center">Status</th>
                                        <th class="align-center">Room</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                        <tr class="align-center">

                                            <td>{{ $dups->folio }}</td>
                                            <td>{{ $dups->folio_master }}</td>
                                            <td>
                                                @if(!empty($dups->lname))
                                                    {{ ucwords(strtolower($dups->fname)).' '.ucwords(strtolower($dups->lname)) }}
                                                @else
                                                    {{ ucwords(strtolower($dups->fname)) }}
                                                @endif
                                            </td>


                                            <td>
                                                {{ $dups->status }}
                                            </td>


                                            <td>
                                                {{ $dups->room }}
                                            </td>
                                        </tr>


                                    </tbody>
                                </table>

                            </div>


                                <div class="header">
                                    <h2>Possible Duplicate</h2>
                                </div>
                                <div class="body">
                                    <table  class="table table-bordered table-striped table-hover  responsive js-basic-example" id="datatable-responsive" width="100%">
                                        <thead class="bg-teal">
                                        <tr>

                                            <th class="align-center">Folio</th>
                                            <th class="align-center">Folio Master</th>
                                            <th class="align-center">Full Name</th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr class="align-center">
                                            @foreach($dups->contact as $contact)
                                                <td>{{ $contact->profilesfolio[0]->folio }}</td>
                                                <td>{{ $contact->profilesfolio[0]->folio_master }}</td>
                                                <td> @if(!empty($contact->lname))
                                                    {{ ucwords(strtolower($contact->fname)).' '.ucwords(strtolower($contact->lname))}}
                                                    @else
                                                       {{  ucwords(strtolower($contact->fname))}}
                                                    @endif

                                                </td>

                                                @endforeach
                                        </tr>
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
        $(document).ready(function() {
            $('.datatable').DataTable( {
                "paging":   false,
                "ordering": false,
                "info":     false,
                "searching":false,
            } );
        } );
    </script>
@endsection
