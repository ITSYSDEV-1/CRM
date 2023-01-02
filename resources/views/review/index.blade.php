@extends('layouts.master')
@section('title')
   Reviews  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Reviews</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="panel-group full-body" id="accordion_18" role="tablist" aria-multiselectable="true">
                            <div class="panel">
                                <div class="panel-heading" role="tab" id="headingOne_18">
                                    <h4 class="panel-title ">
                                        <a class="collapsed teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseOne_18" aria-expanded="true" aria-controls="collapseOne_18">
                                            <i class="fa fa-tripadvisor"></i> TRIPADVISOR.COM
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne_18" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_18">
                                    <div class="panel-body">
                                        <div class="row clearfix">
                                            <div class="card">
                                                <div class="body">
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <div class="x_panel tile " style="height: 420px">
                                                            <div class="x_title">
                                                                <h2>Rating </h2>
                                                            <div class="clearfix"></div>
                                                            </div>
                                                            <div class="x_content" >
                                                                <div class="dashboard-widget-content">
                                                                    <div class="star" style="color:#1ABB9C;padding: 20px">
                                                                        <h4>Rating {{ $tripadvisor->aggregateRating->rating }} of {{ $tripadvisor->aggregateRating->totalreview }} Travelers </h4>
                                                                        {{--@for($i=0;$i<5;$i++ )--}}
                                                                            {{--@if($i<$tripadvisor->aggregateRating->rating)--}}
                                                                                {{--<span><i class="fa fa-star fa-2x"></i> </span>--}}
                                                                            {{--@else--}}
                                                                                {{--<span><i class="fa fa-star-o fa-2x"></i> </span>--}}
                                                                            {{--@endif--}}

                                                                        {{--@endfor--}}
                                                                        @php
                                                                        $number = $tripadvisor->aggregateRating->rating;
                                                                        // Make it integer:
                                                                        $stars = round( $number * 2, 0, PHP_ROUND_HALF_UP);

                                                                        // Add full stars:
                                                                        $i = 1;
                                                                        while ($i <= $stars - 1) {
                                                                        echo "<span><i class='fa fa-star fa-2x'></i> </span>";
                                                                        $i += 2;
                                                                        }
                                                                        // Add half star if needed:
                                                                        if ( $stars & 1 ) echo "<span><i class='fa fa-star-half-o fa-2x'></i> </span>";
                                                                        @endphp
                                                                    </div>
                                                                    @foreach($tripadvisor->rating as $rating)
                                                                        <div class="widget_summary">
                                                                            <div class="w_left w_25">

                                                                                <span>{{ $rating->label }}</span>
                                                                            </div>
                                                                            <div class="w_center w_55">
                                                                                <div class="progress">
                                                                                    <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $rating->value }}" aria-valuemax=" {{ $tripadvisor->aggregateRating->totalreview  }}" aria-valuemin="0" style="width: {{$rating->value/$tripadvisor->aggregateRating->totalreview*100 }}%" >
                                                                                        <span class="sr-only">{{ $rating->value }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="w_right w_25">
                                                                                <span>{{ $rating->value }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                                        <div class="x_panel tile " style="height: 420px">
                                                            <div class="x_title">
                                                                <h2>Review</h2>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:350px;">
                                                                <div class="dashboard-widget-content">

                                                                    <ul  class="list-unstyled ">
                                                                        @foreach(\App\Models\Reviews::where('source','=','Tripadvisor')->orderBy('created_at','desc')->take(20)->get() as $review)
                                                                            <li>
                                                                                <div class="block" style="border-bottom: 1px solid">
                                                                                    <a class="pull-left border-green profile_thumb">
                                                                                        <img src="{{ url($review->avatar) }} "  width="75px" alt="" style="padding-top: 20px" >
                                                                                    </a>
                                                                                    <div class="media" style="padding: 10px">
                                                                                        <h4 class="title">
                                                                                            <a href="{{ url($review->link_review) }}">{{ $review->title }}</a>
                                                                                        </h4>
                                                                                        <div>
			                                                                       <div class="byline">{{ $review->author }}
                                                                                       <div class="star" style="color:#1ABB9C">
                                                                                           {{ $review->origin }}
                                                                                       </div>
                                                                                            <div class="star" style="color:#1ABB9C">
                                                                                                @for($i=0;$i<5;$i++ )
                                                                                                    @if($i<$review->rating/10)
                                                                                                        <span><i class="fa fa-star "></i> </span>
                                                                                                    @else
                                                                                                        <span><i class="fa fa-star-o"></i> </span>
                                                                                                    @endif
                                                                                                @endfor
                                                                                            </div>
                                                                                            <span> <small>{{ $review->date_posted }}</small></span>
                                                                                        </div>
                                                                                        <p class="excerpt">
                                                                                            {{ $review->review }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                     </div>
                                </div>
                            </div>
                            <div class="panel ">
                                <div class="panel-heading" role="tab" id="headingTwo_18">
                                    <h4 class="panel-title">
                                        <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseTwo_18" aria-expanded="false"                              aria-controls="collapseTwo_18">
                                            <i class="fa fa-building"></i> BOOKING.COM
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo_18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_18">
                                    <div class="panel-body">
                                        <div class="row clearfix">
                                            <div class="card">
                                                <div class="body">
                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                                            <div class="x_panel tile " style="height: 420px">
                                                                <div class="x_title">
                                                                    <h2>Booking.com Rating </h2>
                                                                    <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                                <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:340px";>
                                                                    <div class="dashboard-widget-content">
                                                                        <div class="star" style="color:#1ABB9C;padding: 20px">
                                                                            <h4>Rating {{ $booking["reviews"]["score"] }}   of  {{ $booking["reviews"]["total"] }} Travelers</h4>
                                                                        </div>
                                                                        <h4>Breakdown</h4>
                                                                        @foreach($booking["reviews"]["breakdown"] as $bd)
                                                                            <div class="widget_summary">
                                                                                <div class="w_left w_25">
                                                                                    <span>{{ $bd["name"] }}</span>
                                                                                </div>
                                                                                <div class="w_center w_55">
                                                                                    <div class="progress">
                                                                                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $bd["value"] }}" aria-valuemax="10" aria-valuemin="0" style="width: {{ $bd["value"]/10*100  }}%" >
                                                                                            <span class="sr-only">{{ $bd["value"] }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="w_right w_25">
                                                                                    <span>{{ $bd["value"] }}</span>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                                            <div class="x_panel tile " style="height: 420px">
                                                                <div class="x_title">
                                                                    <h2>Booking.com Review</h2>
                                                                    <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                                <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:350px;">
                                                                    <div class="dashboard-widget-content">

                                                                        <ul  class="list-unstyled ">
                                                                            @foreach(\App\Models\Reviews::where('source','=','Booking')->orderBy('date_posted','asc')->take(20)->get()  as $key=> $reviewlist)

                                                                                <li >
                                                                                    <div class="block" style="border-bottom: 1px solid">
                                                                                        <div class="pull-left ">
                                                                                            <h1><span class="badge badge-secondary">{{ $reviewlist["score"] }}</span></h1>
                                                                                        </div>
                                                                                        <div class="media" style="padding: 10px">
                                                                                            <h4 class="title">
                                                                                                {{ $reviewlist["title"] }}
                                                                                            </h4>
                                                                                            <div class="byline">  {{ $reviewlist["author"] }}
                                                                                                <div class="star" style="color:#1ABB9C">
                                                                                                    {{ $reviewlist["origin"] }}
                                                                                                </div>
                                                                                                <span> <small> {{ str_replace("Reviewed:","",$reviewlist["date_posted"]) }}</small></span>
                                                                                            </div>
                                                                                            <span><i class="fa fa-minus-circle left" style="color:#cc0000"></i> </span>
                                                                                            <p class="excerpt " style="margin-left: 20px">
                                                                                                {{ $reviewlist["negative"] }}
                                                                                            </p>
                                                                                            <span><i class="fa fa-plus-circle left" style="color:#1ABB9C"></i> </span>
                                                                                            <p class="excerpt" style="margin-left: 20px">
                                                                                                {{ $reviewlist["positive"] }}
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>

                                                                            @endforeach

                                                                        </ul>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel ">
                                <div class="panel-heading" role="tab" id="headingThree_18">
                                    <h4 class="panel-title">
                                        <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseThree_18" aria-expanded="false"  aria-controls="collapseThree_18">
                                            <i class="fa fa-building"></i> HOTELS.COM
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseThree_18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree_18">
                                    <div class="panel-body">
                                        <div class="row clearfix">
                                            <div class="card">
                                                <div class="body">
                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                                            <div class="x_panel tile " style="height: 420px">
                                                                <div class="x_title">
                                                                    <h2>Hotels.com Rating </h2>
                                                                    <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                                <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:340px";>
                                                                    <div class="dashboard-widget-content">
                                                                        <div class="star" style="color:#1ABB9C;padding: 20px">

                                                                            <h4>Rating {{ $hotels->rating }}   of  {{ $hotels->total }} Travelers</h4>
                                                                        </div>
                                                                        <h4>Breakdown</h4>

                                                                        @foreach($hotels->breakdown as $bd)
                                                                            <div class="widget_summary">
                                                                                <div class="w_left w_25">
                                                                                <span>{{ $bd->score }}</span>
                                                                                </div>
                                                                                    <div class="w_center w_55">
                                                                                        <div class="progress">
                                                                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $bd->score }}" aria-valuemax="{{ $hotels->total}}" aria-valuemin="0" style="width: {{ $bd->count / $hotels->total * 100 }}%" >
                                                                                                <span class="sr-only">{{ $bd->count }}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="w_right w_25">
                                                                                <span>{{ $bd->count }}</span>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach


                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                                            <div class="x_panel tile " style="height: 420px">
                                                                <div class="x_title">
                                                                    <h2>Hotels.com Review</h2>
                                                                    <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                                <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:350px;">
                                                                    <div class="dashboard-widget-content">

                                                                        <ul  class="list-unstyled ">
                                                                            @foreach(\App\Models\Reviews::where('source','=','Hotels')->orderBy('date_posted','asc')->take(20)->get()  as $key=> $reviewlist)

                                                                                <li >
                                                                                    <div class="block" style="border-bottom: 1px solid">
                                                                                        <div class="pull-left ">
                                                                                            <h1><span class="badge badge-secondary">{{ $reviewlist->rating }}</span></h1>
                                                                                        </div>
                                                                                        <div class="media" style="padding: 10px">
                                                                                            <h4 class="title">
                                                                                                {{ $reviewlist->title }}
                                                                                            </h4>
                                                                                            <div class="byline">  {{ $reviewlist->author }}
                                                                                                <div class="star" style="color:#1ABB9C">
                                                                                                   {{ \App\Country::where('iso2','=',$reviewlist->origin)->value('country') }}
                                                                                                </div>
                                                                                                <span> <small> {{ $reviewlist->date_posted }}</small></span>
                                                                                            </div>
                                                                                            <span><i class="fa fa-comment-o left" style="color:#0b97c4"></i> </span>
                                                                                            <p class="excerpt " style="margin-left: 20px">
                                                                                                {{ $reviewlist->review }}
                                                                                            </p>
                                                                                            <span><i class="fa fa-comments left" style="color:#1ABB9C"></i> </span>
                                                                                            <p class="excerpt" style="margin-left: 20px">
                                                                                                {{ $reviewlist->reply }}
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>

                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection
