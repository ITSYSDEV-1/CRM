@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile">
                        <div class="x_title">
                            <h3>Create Email Template</h3>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            @include('email.manage._manage')
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
    $('#summernote').summernote({
       // height:100,
        popover: {
            image: [
                ['custom', ['imageAttributes']],
                ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']]
            ],

        },
        lang: 'en-US', // Change to your chosen language
        imageAttributes:{
            icon:'<i class="note-icon-pencil"/>',
            removeEmpty:false, // true = remove attributes | false = leave empty if present
            disableUpload: false // true = don't display Upload Options | Display Upload Options
        },
        fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36', '48' , '64', '82', '150']


    });

</script>
@endsection