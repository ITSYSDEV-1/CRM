@extends('layouts.master')
@section('title')
    Create Campaign  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel tile ">
                            <div class="x_title">
                                <h3>Create Campaign</h3>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" >

                            </div>
                        </div>
                        @include('campaign._form')
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function () {

        $('.categoryselect').hide()
        $('#checkexternal').on('click', function () {
            if ($(this).prop('checked')) {
                $('.categoryselect').show()
                $('.segmentselect').hide()
                $('.formsegment').hide();
                $('.formsegment').parsley({
                    excluded: '#segmentname'
                })

            } else {
                $('.formsegment').parsley()
                $('.categoryselect').hide()
                $('.segmentselect').show()

            }
        });
    })
    var form = $("#example-form");
    form.validate({
        errorPlacement: function errorPlacement(error, element) { element.before(error); },
        rules: {
            confirm: {
                equalTo: "#password"
            }
        }
    });
    form.children("div").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slideLeft",
        onStepChanging: function (event, currentIndex, newIndex)
        {
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

            $('#schedule').datepicker({
                timepicker: true,
                language: 'en',
                dateFormat: 'dd M yyyy ',
                timeFormat: 'hh:00 aa',
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

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function (event, currentIndex)
        {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinished: function (event, currentIndex)
        {        
            var form=$('#example-form')
            form.submit()
            form.on('submit',function (e) {
                e.preventDefault();
                var form = e.target;
                var data = new FormData(form);
                $.ajax({
                    url: form.action,
                    method: form.method,
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (data) {
                        console.log(data)
                    }
                })
            })
        }
    });


</script>
    @endsection