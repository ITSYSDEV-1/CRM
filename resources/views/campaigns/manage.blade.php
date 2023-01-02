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
                                @include('campaigns._form')
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
        function setSwitchery(switchElement, checkedBool) {
            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
                switchElement.setPosition(true);
                switchElement.handleOnchange(true);
            }
        }
        var mySwitch = new Switchery($('#segment')[0], {
            size:"small",
            color: '#0D74E9'
        });

    </script>
    <script>
        $(document).ready(function(){
            $('.categoryselect').hide()

            $('#spending_from').maskMoney({thousands:'.', decimal:',', precision:0});
            $('#spending_to').maskMoney({thousands:'.', decimal:',', precision:0});
           $('.selectsegment').hide();
           $('#campaignForm').parsley();
            $("#saveBtn").on('click', function(event) {
                // validate form with parsley.
                $('#campaignForm').parsley().validate();

                // if this form is valid
                if (!$('#campaignForm').parsley().isValid()) {
                    // show alert message
                    swal('Error','Some fields contain error','warning');
                } else {
                    $('#campaignForm').submit();
                }
                event.preventDefault();
            });
        });
    </script>
    <script>
        $('#stay_from').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function(){
            checkRecepient()
        });
        $('#schedule').datetimepicker({
            format: 'DD MMMM YYYY hh:mm',
            showClear:true,

        })
        $('#stay_to').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function() {
            checkRecepient();
        });
        $('#bday_from').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function(){
            checkRecepient()
        });
        $('#bday_to').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function() {
            checkRecepient();
        });
        $('#wedding_bday_from').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function(){
            checkRecepient()
        });
        $('#wedding_bday_to').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function() {
            checkRecepient();
        });






    </script>


        <script>
            $('.selectpicker').selectpicker({
                actionsBox:true,
                selectOnTab:true,
            });
            $(document).ready(function () {
                $('.preview').hide();
                checkRecepient();
               var data= {!!  json_encode($model) !!};
              if(data['template'].length>0 ){
                  var id_=data['template'][0]['id'];
                  var name_=data['template'][0]['name'];
                 $('#template').append('<option value="'+id_+'" selected="selected">'+name_+'</option>');
              }
            });
            function checkRecepient(){

                var url='{{ route('campaign.recepient') }}';
                var sel=[];
                $('.country_id').children('option:selected').each(function () {
                    sel.push($(this).val())
                });
                var ar=[];
                $('.area').children('option:selected').each(function () {
                    ar.push($(this).val())
                })
                var guest=[];
                $('.guest').children('option:selected').each(function () {
                    guest.push($(this).val())
                })
                var booking=[]
                $('#booking_source').children('option:selected').each(function () {
                    booking.push($(this).val())
                })
                var gen=[]
                $('#gender').children('option:selected').each(function () {
                    gen.push($(this).val())
                })
                   $('#gender bs-select-all').on('click',function () {
                       gen.length=0;
                   })
                $('.country_id bs-select-all').on('click',function () {
                    sel.length=0;
                })
                $('.area bs-select-all').on('click',function () {
                    ar.length=0;
                })
                $('.guest bs-select-all').on('click',function () {
                    guest.length=0;
                })
                $.blockUI({message: '<h1><i class="fa fa-spinner fa-spin"></i>Just a moment...</h1>'})
                $.ajax({
                    url:url,
                    data:{
                        country_id:sel,
                        area:ar,
                        _token:'{{ csrf_token() }}',
                        guest_status:guest,
                        spending_from:$('#spending_from').val(),
                        spending_to:$('#spending_to').val(),
                        stay_from:$('#stay_from').val(),
                        stay_to:$('#stay_to').val(),
                        bday_from:$('#bday_from').val(),
                        bday_to:$('#bday_to').val(),
                        wedding_bday_from:$('#wedding_bday_from').val(),
                        wedding_bday_to:$('#wedding_bday_to').val(),
                        total_stay_from:$('#total_stay_from').val(),
                        total_stay_to:$('#total_stay_to').val(),
                        total_night_from:$('#total_night_from').val(),
                        total_night_to:$('#total_night_to').val(),
                        gender:gen,
                        age_from:$('#age_from').val(),
                        age_to:$('#age_to').val(),
                        booking_source:booking,

                    },
                    type:'POST',
                    success:function (data) {
                        $.unblockUI();
                       var num=data.length;
                        $('.recepient').empty();
                       if (num >0 ){

                           $('.recepient').append( num + ' contacts match this criteria');
                       } else {
                           $('.recepient').append( 'No contacts match this criteria');
                       }
                    }

                });
            }
        </script>
    <script>
        function selectTemplate(id) {
            var url='{{ route('campaign.template') }}';
            $.blockUI({message: '<h1><i class="fa fa-spinner fa-spin"></i>Just a moment...</h1>'})
            $.ajax({
                url:url,
                type:'post',
                data:{
                    id:id,
                    _token:'{{ csrf_token() }}'
                },
                success:function (data) {
                    $.unblockUI()
                    $('.preview').show();
                    $('#summernote').summernote({
                        toolbar: [

                        ],
                       // height:400,
                    });
                   $('#summernote').summernote('code', '');
                 $('#summernote').summernote('editor.pasteHTML',data['content']);
                }
            })
        }
    </script>
    <script>
        $(document).ready(function () {

            $('#checkexternal').on('click',function () {
                if($(this).prop('checked')){
                    $('.categoryselect').show()
                    $('.segmentselect').hide()
                    $('.formsegment').hide();
                    $('.formsegment').parsley({
                        excluded:'#segmentname'
                    })

                } else {
                    $('.formsegment').parsley()
                    $('.categoryselect').hide()
                    $('.segmentselect').show()

                }
            });

            var status= '{{ $model->status }}';
            if (status==='Inactive'){
                $('#myonoffswitch').prop('checked',false);
            }else {
                $('#myonoffswitch').prop('checked',true);
            }
            $('#myonoffswitch').on('change',function () {
                if ($('#myonoffswitch').is(':checked')){
                    var state='on';
                }else {
                    state='off';
                }
                var id='{{ $model->id }}';
                $.blockUI({message: '<h1><i class="fa fa-spinner fa-spin"></i>Just a moment...</h1>'})
                $.ajax({
                    url:"{{ route('campaign.activate')}}",
                    type:'post',
                    data:{
                        id:id,
                        status:state,
                        _token:'{{ csrf_token() }}',
                    },
                    success:function (data) {
                        $.unblockUI()
                        if(data['active']==true){
                            $('#myonoffswitch').prop('checked',true);
                        }else {
                            $('#myonoffswitch').prop('checked',false);
                        }
                    }

                })
            })
        })
    </script>
    <script>
        function getType(val) {
            var value=val;
            $.blockUI({message: '<h1><i class="fa fa-spinner fa-spin"></i>Just a moment...</h1>'})
            $.ajax({
                url:'gettype',
                type:'post',
                data:{
                    _token:'{{ csrf_token() }}',
                    type:value
                },

                success:function (data) {
                        $.unblockUI()
                       $('#template').empty();
                       $('#template').append('<option value="">Select Template</option>');
                       $.each(data, function (i, v) {
                           $('#template').append('<option value=' + i + ' >' + v + '</option>').selectpicker('refresh')
                       })
                }
            })
            console.log(value);
        }
    </script>
    <script>

        $('#headingThree_11').on('click',function () {
            $('#namecopy').empty().append("Campaign Name: "+$('#name').val());
            $('#tempnamecopy').empty().append($('#template option:selected').val() !=='' ? 'Template Name: '+$('#template option:selected').text():'Template Name: -');
            $('#countrycopy').empty();
            var txt='';
            $('.country option:selected').each(function () {
                txt+=$(this).text()+','
            });
            txt=txt.substr(0,txt.length-1);
            $('#countrycopy').append('Selected Country: '+txt);
            if($('#spending_from').val()==='' && $('#spending_to').val()===''){
                $('#spendingrange').empty().append('Spending : -');
            }else if($('#spending_from').val()!=='' && $('#spending_to').val()===''){
                $('#spendingrange').empty().append('Spending : More than / equal Rp. '+ $('#spending_from').val());
            }else if ($('#spending_from').val()==='' && $('#spending_to').val()!==''){
                $('#spendingrange').empty().append('Spending : Less than / equal Rp. '+ $('#spending_to').val());
            }else {
                $('#spendingrange').empty().append('Spending : Between Rp. '+ $('#spending_from').val()+' and Rp. '+ $('#spending_to').val());
            }
            if($('#total_stay_from').val()!=='' && $('#total_stay_to').val()!==''){
                $('#totalstay').empty().append('Total Stay '+$('#total_stay_from').val()+' day/s to '+$('#total_stay_to').val()+ ' day/s')
            }else if($('#total_stay_from').val()!=='' && $('#total_stay_to').val()===''){
                $('#totalstay').empty().append('Total Stay >= '+$('#total_stay_from').val()+' day/s')
            }else if($('#total_stay_from').val()==='' && $('#total_stay_to').val()!==''){
                $('#totalstay').empty().append('Total Stay <= '+$('#total_stay_to').val()+' day/s')
            }else {
                $('#totalstay').empty().append('Total Stay : -')
            }
            if($('#gender option:selected').val() !== 'M' && $('#gender option:selected').val() !== 'F' ){
                $('#gendercopy').empty().append('Gender : All');
            } else {
                $('#gendercopy').empty();
                var gen='';
                $('#gender option:selected').each(function () {
                    gen+=$(this).text()+',';
                })
                gen=gen.substr(0,gen.length-1);
                $('#gendercopy').append('Gender : '+gen);
            }
            $('#statuscopy').empty();
            var stt='';
            $('.guest option:selected').each(function () {
                stt+=$(this).text()+',';
            })
            stt=stt.substr(0,stt.length-1);
            if(stt===''){
                $('#statuscopy').append('Guest Status : -');
            }else {
                $('#statuscopy').append('Guest Status : '+stt);
            }
        });
    </script>
    <script>

        $('#segment').on('click',function () {
            var $this=$('#segment');
            if($this.is(':checked')){
                setSwitchery(mySwitch, true);
                $('.formsegment').hide();
                $('.selectsegment').show();
                $('#saveSegment').hide()
                $.each($('.formsegment input'),function(i,v){
                    $(v).val('')
                })
            }else {
                setSwitchery(mySwitch, false);
                $('.formsegment').show();
                $('.selectsegment').hide();
                $('#saveSegment').show()


            }
        })


       $('#segments').on('change',function () {
           $('.formsegment').show();

           var id_=$('#segments option:selected').val();
            $.blockUI({message: '<h1><i class="fa fa-spinner fa-spin"></i>Just a moment...</h1>'})
           $.ajax({
              url:'getsegment',
              type:'POST',
              data:{
                  id:id_,
                  _token:'{{ csrf_token() }}',
              } ,
               success:function (data) {
                   var options=data[1];
                   $('.country_id').selectpicker();
                   $('.country_id').selectpicker('val','');
                   $('.country_id').selectpicker('val',options);

                   var area=data[5];
                   $('.area').selectpicker();
                   $('.area').selectpicker('val','');
                   $('.area').selectpicker('val',area);

                   var gueststatus=data[2];
                   $('.guest').selectpicker();
                   $('.guest').selectpicker('val','');
                   $('.guest').selectpicker('val',gueststatus);
                   var gender=data[3]
                   if(gender!=null){
                       $('#gender').selectpicker();
                       $('#gender').selectpicker('val','');
                       $('#gender').selectpicker('val',gender);
                   }else
                   {
                       $('#gender').selectpicker();
                       $('#gender').selectpicker('val','');

                   }
                   var campaign=data[0];
                   $('#segmentname').val('');
                   $('#segmentname').val(campaign["name"]);
                   $('#spending_from').val('');
                   $('#spending_from').val(campaign['spending_from']);
                   $('#spending_to').val('');
                   $('#spending_to').val(campaign['spending_to']);
                   $('#stay_from').val('');
                   if(campaign['stay_from']==null){
                       $('#stay_from').val('');
                   }else {
                       $('#stay_from').val(moment(campaign['stay_from']).format('D MMMM YYYY'));
                   }
                   if(campaign['stay_to']==null){
                       $('#stay_to').val('');
                   }else {
                       $('#stay_to').val(moment(campaign['stay_to']).format('D MMMM YYYY'));
                   }

                   if(campaign['bday_from']==null){
                       $('#bday_from').val('');
                   }else {
                       $('#bday_from').val(moment(campaign['bday_from']).format('D MMMM'));
                   }
                   if(campaign['bday_to']==null){
                       $('#bday_to').val('');
                   }else {
                       $('#bday_to').val(moment(campaign['bday_to']).format('D MMMM'));
                   }
                   if(campaign['wedding_bday_from']==null){
                       $('#wedding_bday_from').val('');
                   }else {
                       $('#wedding_bday_from').val(moment(campaign['wedding_bday_from']).format('D MMMM'));
                   }
                   if(campaign['wedding_bday_to']==null){
                       $('#wedding_bday_to').val('');
                   }else {
                       $('#wedding_bday_to').val(moment(campaign['wedding_bday_to']).format('D MMMM'));
                   }

                   $('#total_stay_from').val('');
                   $('#total_stay_from').val(campaign['total_stay_from']);
                   $('#total_stay_to').val('');
                   $('#total_stay_to').val(campaign['total_stay_to']);
                   $('#total_night_from').val('')
                   $('#total_night_from').val(campaign['total_night_from']);
                   $('#total_night_to').val('');
                   $('#total_night_to').val(campaign['total_night_to']);
                   $('#age_from').val('');
                   $('#age_from').val(campaign['age_from']);
                   $('#age_to').val()
                   $('#age_to').val(campaign['age_to'])
                   $('#booking_source').selectpicker()
                   $('#booking_source').selectpicker('val','')
                   $('#booking_source').selectpicker('val',data[4])
                   $.unblockUI();
                   checkRecepient();
                   }



           });
       })
    </script>
    <script>
        if($('#collapseOne_10').hasClass('error')){
            console.log('Error');
        }
       $('#collapseOne_10').find('.error').each(function () {
            console.log('.error').text();
        })
        $('#saveSegment').on('click',function (e) {
            e.preventDefault()
            var sel=[];
            $('.country_id').children('option:selected').each(function () {
                sel.push($(this).val())
            });
            var guest=[];
            $('.guest').children('option:selected').each(function () {
                guest.push($(this).val())
            })
            var booking=[]
            $('#booking_source').children('option:selected').each(function () {
                booking.push($(this).val())
            })
            var gen=[]
            $('#gender').children('option:selected').each(function () {
                gen.push($(this).val())
            })
            $('#gender bs-select-all').on('click',function () {
                gen.length=0;
            })
            $('.country_id bs-select-all').on('click',function () {
                sel.length=0;
            })
            $('.guest bs-select-all').on('click',function () {
                guest.length=0;
            })

            var data={
                _token:'{{ csrf_token() }}',
                name:$('#segmentname').val(),
                guest_status:guest,
                stay_from:$('#stay_from').val(),
                stay_to:$('#stay_to').val(),
                total_night_from:$('#total_night_from').val(),
                total_night_to:$('#total_night_to').val(),
                age_from:$('#age_from').val(),
                age_to:$('#age_to').val(),
                country_id:sel,
                spending_from:$('#spending_from').val(),
                spending_to:$('#spending_to').val(),
                total_stay_from:$('#total_stay_from').val(),
                total_stay_to:$('#total_stay_to').val(),
                gender:gen,
                booking_source:booking,
            }
            console.log(data)
            $.ajax({
                url:'savesegment',
                type:'POST',
                data:data,
                success:function (data) {
                  if(data['error']){
                      swal('Error',data['error']['name'][0],'warning');
                  }else {


                      swal('Success','Segment Saved','success')
                      setSwitchery(mySwitch, true);
                      $('#segments').append('<option selected="selected" value="' + data['success']['id'] + '">' + data['success']['name'] + '</option>')
                      $('#segments').trigger('change', true);
                      $('#segments').selectpicker('refresh')

                  }
                }
            })
        })
    </script>
    @endsection