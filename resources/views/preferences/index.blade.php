@extends('layouts.master')
@section('title')
    Preferences | {{ $configuration->hotel_name .' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>Dashboard Setting</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12 ">
                                    <h4>Show/Hide Panel</h4>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeAdded"> Contact Added
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeCountry"> Contacts By Country
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeSpending"> Top 10 Spending
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeIncoming"> Incoming Birthday
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeLong"> Contacts By Longest Stay
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeRoomType"> Contacts By Room Type
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeAge"> Contacts By Age
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeBooking"> Contacts By Booking Source
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeStay"> Contacts By Stay
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="collapseSidebar"> Collapse Sidebar
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeEmail"> Email Reports
                                        </label><br>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="header">
                                <h2>Configuration</h2>
                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">
                                    {{ Form::label('hotel_name','Hotel Name') }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('hotel_name',$configuration->hotel_name,['class'=>' form-control','id'=>'hotel_name', 'data-live-search'=>'true','placeholder'=>'Hotel Name']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">
                                    {{ Form::label('app_title','App Title') }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('app_title',$configuration->app_title,['class'=>' form-control','id'=>'app_title', 'data-live-search'=>'true','placeholder'=>'App Title']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">
                                    {{ Form::label('gm_name','GM Name') }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('gm_name',$configuration->gm_name,['class'=>' form-control','id'=>'gm_name', 'data-live-search'=>'true','placeholder'=>'GM Name']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">
                                    {{ Form::label('sender_email','Sender Email') }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::email('sender_email',$configuration->sender_email,['class'=>' form-control','id'=>'sender_email', 'data-live-search'=>'true','placeholder'=>'Sender Email']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">
                                    {{ Form::label('sender_name','Sender Name') }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::email('sender_name',$configuration->sender_name,['class'=>' form-control','id'=>'sender_name', 'data-live-search'=>'true','placeholder'=>'Sender Name']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">
                                    {{ Form::label('cc_recipient','CC Recipient') }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::text('cc_recipient',$configuration->cc_recipient,['class'=>' form-control','id'=>'cc_recipient', 'data-live-search'=>'true','placeholder'=>'CC Recipient']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">
                                    {{ Form::label('logo','Logo') }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{ Form::file('logo',null,['class'=>' form-control','id'=>'logo']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">

                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            @if(!empty($configuration->logo) || $configuration->logo !=null)
                                                <img src="{{ asset('').'/'.$configuration->logo }}" width="100px">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-6 form-control-label">

                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <a href="#" id="savepreferences" class="btn btn-success btn-sm">Save</a>
                                        </div>
                                    </div>
                                </div>

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
        $(document).ready(function (e) {
            $('#savepreferences').on('click', function () {
                var file_data = $('#logo').prop('files')[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                form_data.append('_token','{{ csrf_token() }}');
                form_data.append('hotel_name',$('#hotel_name').val());
                form_data.append('app_title',$('#app_title').val())
                form_data.append('gm_name',$('#gm_name').val());
                form_data.append('sender_email',$('#sender_email').val());
                form_data.append('sender_name',$('#sender_name').val());
                form_data.append('cc_recipient',$('#cc_recipient').val());
                $.ajax({
                    url: 'savepreferences', // point to server-side controller method
                    //dataType: 'text', // what to expect back from the server
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success:function (d) {
                        if(d==='success'){
                            swal('Success','Preferences updated','success')
                        }
                    }
                });
            });
        });
        $(document).ready(function () {

            $('.dashboard').each(function () {
                var id = $(this).attr('id')
                $(this).iCheck('check')
                $(this).closest("input").attr('checked', true);
                if (readCookie(id) == 0) {
                    $(this).closest('input').removeAttr('checked')
                }
                var state = readCookie(id)
                $(this).on('ifChanged', function () {
                    if (state == 0) {
                        eraseCookie(id)
                    } else {
                        createCookie(id, 0, 1)

                    }
                })

            })
        })
        function createCookie(name, value, days) {
            var expires;
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            } else {
                expires = "";
            }
            document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
        }

        function readCookie(name) {
            var nameEQ = encodeURIComponent(name) + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ')
                    c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0)
                    return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;
        }

        function eraseCookie(name) {
            createCookie(name, "", -1);
        }

    </script>
@endsection