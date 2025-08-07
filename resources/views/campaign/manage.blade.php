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
                                <h3>Create Campaign6</h3>
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
        onStepChanging: function (event, currentIndex, newIndex) {
            var start = new Date(),
                prevDay,
                startHours = 9;

            start.setHours(9);
            start.setMinutes(0);

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
                onSelect: function (fd, d, picker) {
                    if (!d) return;
                    var day = d.getDay();
                    if (prevDay != undefined && prevDay == day) return;
                    prevDay = day;
                }
            })

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinished: function (event, currentIndex) {
            // Show loading dengan swal (bukan custom loading div)
            swal({
                title: 'Processing Campaign...',
                text: 'Please wait while we process your campaign.',
                type: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            
            $.ajax({
                url: $('#example-form').attr('action'),
                method: 'POST',
                data: new FormData($('#example-form')[0]),
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        // Ganti dengan swal format lama
                        swal({
                            title: 'Success!',
                            text: response.message + '\nCampaigns created: ' + response.campaigns_created,
                            type: 'success',
                            confirmButtonText: 'OK'
                        }, function() {
                            window.location.href = '/campaigns';
                        });
                    } else {
                        // Ganti dengan swal format lama
                        swal('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    // Ganti dengan swal format lama
                    swal('Error!', xhr.responseJSON?.message || 'Failed to process campaign', 'error');
                }
            });
            return false;
        }
    });
    
    // Fungsi baru untuk submit dengan approval
    function submitCampaignWithApproval() {
        // Show loading state dengan swal format lama
        swal({
            title: 'Processing Campaign',
            text: 'Requesting approval and processing recipients...',
            type: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
        
        $.ajax({
            url: $('#example-form').attr('action'),
            method: 'POST',
            data: $('#example-form').serialize(),
            timeout: 60000,
            success: function(response) {
                if(response.success) {
                    let message = response.message;
                    if(response.campaigns_created > 1) {
                        message += '\n\nCampaign has been split into ' + response.campaigns_created + ' separate campaigns for optimal delivery.';
                    }
                    
                    swal({
                        title: 'Success!',
                        text: message,
                        type: 'success',
                        confirmButtonText: 'View Campaigns'
                    }, function() {
                        window.location.href = '/campaigns';
                    });
                } else {
                    swal('Error!', response.message || 'Failed to process campaign', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to process campaign';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                swal('Error!', errorMessage, 'error');
            }
        });
    }
</script>
@endsection
