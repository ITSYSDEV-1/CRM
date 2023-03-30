<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon"  href="{{ asset('images/favicon.png') }}"  />

  <title>@yield('title')</title>

  <!-- Bootstrap -->
  <link href="{{ asset('vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('plugins/summernote/dist/summernote.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css') }}" rel="stylesheet">

  <!-- NProgress -->
  <link href="{{ asset('vendors/nprogress/nprogress.css') }}" rel="stylesheet">
  <!-- iCheck -->
  <link href="{{ asset('vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
  <link href="{{ asset('plugins/bootstrap-select/css/bootstrap-select.css') }} " rel="stylesheet" />
  <link href="{{ asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">

  <!-- bootstrap-progressbar -->
  <link href="{{ asset('vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet">
  <!-- JQVMap -->
  <link href="{{ asset('vendors/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet"/>
  <!-- bootstrap-daterangepicker -->
  <link href="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/switchery/dist/switchery.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/morris/morris.css') }}" rel="stylesheet">
  <!-- Custom Theme Style -->
  <link href="{{ asset('build/css/custom.min.css') }}" rel="stylesheet">
  <link href="{{ asset('flags/flags.css') }}" rel="stylesheet">
  <link href="{{ asset('css/jquery.steps.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/airdatepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
  {{--<link href="{{ asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css') }}" rel="stylesheet">--}}
  <style>
    .datepicker{
      z-index:9999 !important;
    }
    #loadcontacts tbody .checkbox{
      width:18px;
      height:18px;
      margin-top:2px;

    }
    #loadcontacts tbody .checkbox:checked {
      width:20px;
      height:20px;
      margin-top:2px;
    }
  </style>

</head>

<body class="nav-md">
<div class="container body">
  <div class="main_container">
    <div class="col-md-3 left_col">
      <div class="left_col scroll-view">
        <!-- menu profile quick info -->
        <div class="profile clearfix">
          <div class="profile_pic">
            <img src="{{ asset('').'/'.$configuration->logo }}" alt="logo"  class="img-thumbnail profile_img">
          </div>
          <div class="profile_info">

          </div>
        </div>
        <!-- /menu profile quick info -->

        <br />

        <!-- sidebar menu -->
      @include('layouts._sidebar')
      <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
      {{--<div class="sidebar-footer hidden-small">--}}
      {{--<a data-toggle="tooltip" data-placement="top" title="Settings">--}}
      {{--<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>--}}
      {{--</a>--}}
      {{--<a data-toggle="tooltip" data-placement="top" title="FullScreen">--}}
      {{--<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>--}}
      {{--</a>--}}
      {{--<a data-toggle="tooltip" data-placement="top" title="Lock">--}}
      {{--<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>--}}
      {{--</a>--}}
      {{--<a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">--}}
      {{--<span class="glyphicon glyphicon-off" aria-hidden="true"></span>--}}
      {{--</a>--}}
      {{--</div>--}}
      <!-- /menu footer buttons -->
      </div>
    </div>

    <!-- top navigation -->
  @include('layouts._navbar')
  <!-- /top navigation -->

    <!-- page content -->
  @yield('content')
  @yield('main-content')
  <!-- /page content -->

    <!-- footer content -->
  @include('layouts._footer')
  <!-- /footer content -->
  </div>
</div>

<!-- jQuery -->
<script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>

<script src="{{ asset('js/popper.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('vendors/fastclick/lib/fastclick.js') }}"></script>
<!-- NProgress -->
<script src="{{ asset('vendors/nprogress/nprogress.js') }}"></script>
<!-- Chart.js -->
<script src="{{ asset('vendors/Chart.js/dist/Chart.min.js') }}"></script>
<!-- gauge.js -->
<script src="{{asset('plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>
<script src="{{ asset('plugins/summernote/dist/summernote.js') }}"></script>
<script src="{{ asset('plugins/summernote/dist/plugin/image/summernote-image-attributes.js')}}"></script>


<script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
{{--<script src="{{ asset('plugins/jquery-validation/jquery.validate.js') }}"></script>--}}
<script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

<script src="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>


<script src="{{ asset('vendors/gauge.js/dist/gauge.min.js') }}"></script>
<!-- bootstrap-progressbar -->
<script src="{{ asset('vendors/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('vendors/iCheck/icheck.min.js') }}"></script>
<!-- Skycons -->
<script src="{{ asset('vendors/skycons/skycons.js') }}"></script>
<!-- Flot -->
<script src="{{ asset('vendors/Flot/jquery.flot.js') }}"></script>
<script src="{{ asset('vendors/Flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('vendors/Flot/jquery.flot.time.js') }}"></script>
<script src="{{ asset('vendors/Flot/jquery.flot.stack.js') }}"></script>
<script src="{{ asset('vendors/Flot/jquery.flot.resize.js') }}"></script>
<!-- Flot plugins -->
<script src="{{ asset('vendors/flot.orderbars/js/jquery.flot.orderBars.js') }}"></script>
<script src="{{ asset('vendors/flot-spline/js/jquery.flot.spline.min.js') }}"></script>
<script src="{{ asset('vendors/flot.curvedlines/curvedLines.js') }}"></script>
<!-- DateJS -->
<script src="{{ asset('vendors/DateJS/build/date.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('vendors/jqvmap/dist/jquery.vmap.js') }}"></script>
<script src="{{ asset('vendors/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
<script src="{{ asset('vendors/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script>
<!-- bootstrap-daterangepicker -->

<script src="{{ asset('vendors/raphael/raphael.js') }}"></script>
<script src="{{ asset('vendors/morris/morris.js') }}"></script>
{{--<script src="{{ asset('plugins/jquery-validation/jquery.validate.js') }}"></script>--}}
<script src="{{ asset('vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js')}}"></script>
<script src="{{ asset('vendors/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
<script src="{{ asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js')}}"></script>
<script src="{{ asset('vendors/datatables.net-scroller/js/dataTables.scroller.min.js')}}"></script>
<script src="{{ asset('js/require.js') }}"></script>
<script src="{{ asset('js/jquery.maskMoney.min.js') }}"></script>
<script src="{{ asset('js/jquery.blockUI.js') }}"></script>
<script src="{{ asset('vendors/switchery/dist/switchery.min.js')}}"></script>
<script src="{{ asset('vendors/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('vendors/parsleyjs/dist/parsley.min.js') }}"></script>
<script src="{{ asset('js/jquery.steps.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('vendors/airdatepicker/dist/js/datepicker.min.js') }}"></script>
<script src="{{ asset('vendors/airdatepicker/dist/js/i18n/datepicker.en.js') }}"></script>


<!-- Custom Theme Scripts -->
<script src="{{ asset('build/js/custom.js') }}"></script>


@yield('script')
<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js-na1.hs-scripts.com/24132201.js"></script>
<!-- End of HubSpot Embed Code -->
</body>
</html>
