@extends('layouts.master')
@section('title')
    Create Promo Prestay Configuration  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel tile">
                            <div class="x_title">
                                <h3>Promo Prestay</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                @include('prestay.prestaypromo._form')
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
        //Date range picker
        $('#eventduration').daterangepicker()
    </script>
    <script>
        $('#savePromo').on('click',function () {
            $('#promoprestayForm').submit()
        })
    </script>
@endsection
