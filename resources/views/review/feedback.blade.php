<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review, Comments and Suggestions</title>
    <link rel="stylesheet" href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/wireframe.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert/sweetalert.css')}}">
</head>
<body class="bg-light">
<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="text-center col-md-12 mx-auto"> <i class="fa d-block fa-5x mb-4 text-info"></i>
                <h2><b>Dear Valued Guest</b></h2>

                <p class="lead">We need Your feedback for better service in the future</p>
            </div>
        </div>
    </div>
</div>
<div class="">
    <div class="container">
        <div class="row">
            <div class="col-md-12 order-md-1">
                <h4 class="mb-3"><b>Tell us your experience at our hotel </b></h4>
                <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3"> <label for="cleanliness"><strong><h3>Cleanliness</h3></strong></label>
                          <div class="star" id="cleanliness"></div>
                        </div>
                        <div class="col-md-6 mb-3"> <label for="comfort"><strong><h3>Comfort</h3></strong></label>
                            <div class="star" id="comfort"></div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6 mb-3"> <label for="location"><strong><h3>Location</h3></strong></label>
                        <div class="star" id="location"></div>
                    </div>
                    <div class="col-md-6 mb-3"> <label for="facilities"><strong><h3>Facilities</h3></strong></label>
                         <div class="star" id="facilities"></div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"> <label for="staff"><strong><h3>Staff</h3></strong></label>
                           <div class="star" id="staff"></div>
                        </div>
                        <div class="col-md-6 mb-3"> <label for="vfm"><strong><h3>Value For Money</h3></strong></label>
                            <div class="star" id="vfm"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"> <label for="wifi"><strong><h3>Wifi</h3></strong></label>
                            <div class="star" id="wifi"></div>
                        </div>
                    </div>
                    <hr class="mb-4">
                        <form>
                            <label for="wifi"><strong><h3>Comments & Suggestions </h3></strong></label>
                            <div class="form-group"> <textarea class="form-control" id="comments" rows="3" placeholder="Your message"></textarea> </div> <button id="saveRating" class="btn btn-success btn-primary">Send</button>
                        </form>
                    <hr class="mb-4">

            </div>
        </div>
    </div>
</div>
<div class="py-5 text-muted text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-12 my-4">
                <p class="mb-1">@ {{ \Carbon\Carbon::now()->format('Y') }} Rama Residence Padma</p>
                <ul class="list-inline">
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/jquery.min.js') }}" ></script>
<script src="{{ asset('js/stars.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js')}} " ></script>
<script src="{{ asset('plugins/sweetalert/sweetalert.min.js')}} " ></script>
<script>
    $(document).ready(function () {

        var id='{!! $contacts !!}'
        var review='{!! $review !!}'
        var json = JSON.parse(review)
        $('#comments').val(json[0]["suggestion"]);
        $('.star').each(function () {
           var item= $(this).attr('id')
            var  val=(json[0][item])
            var child=($(this).children())
            $(child).each(function (i) {

               for (var j=0;j<val;j++){
                   if (j===i){
                       $(this).addClass('fa-star').removeClass('fa-star-o').addClass('selected')
                   }
               }
            })

        })
    })
    $('.star').each(function () {
        var id=$(this).attr('id')
        $('#'+id).stars({click:function () {
            $.ajax({
                url:'saverating',
                type:'POST',
                data:{
                    _token:'{{ csrf_token() }}',
                    val:$(this).index()+1,
                    id:id,
                }
            })
        }})
    })
    $('#saveRating').on('click',function (e) {
        e.preventDefault();
        $.ajax({
            url: 'saverating',
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                val: $('#comments').val(),
                id: 'suggestion'
            }, success: function (data) {
                if (data == 'success') {
                    swal({
                        title: "Thank You So Much!",
                        text: "Your comments has been saved",
                        type: "success",
                        showConfirmButton: true
                    }, function () {
                        window.location.href = "http://ramayanasuites.com/";
                    });
                }
            }
        })
    })
</script>
</body>
</html>