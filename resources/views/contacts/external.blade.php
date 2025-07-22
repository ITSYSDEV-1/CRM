@extends('layouts.master')
@section('title')
    External Contact | {{ \App\Models\Configuration::first()->hotel_name.' '.\App\Models\Configuration::first()->app_title }}
@endsection
@section('content')

    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            @canany(['3.5.2_import_file_new_category', '3.5.1_download_template'])
                            <div class="header">
                                <h1>Contact Upload</h1>
                            </div>
                            @endcanany
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="body">
                            @can('3.5.1_download_template')
                                <br>
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5">
                                    
                                        <a href="{{ url('externalcontact/template') }}"> <i class="fa fa-download"></i> Download Template</a>
                                        
                                    </div>
                                </div>
                                <br>
                            @endcan
                                <br>
                            @can('3.5.2_import_file_new_category')
                                <form id="saveBlast" action="saveexternalcontact" method="post" files="true" enctype="multipart/form-data">
                                    {!! Form::token() !!}
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('file','Import Email Address') }}
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                                            <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                                                {{ Form::file('file') }}
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','') }}
                                        </div>
                                        <div class="form-group">
                                            New Category <input type="checkbox" class="js-switch" name="getcategory" id="getcategory"   onchange="selectCategory()"/>  Use Existing Category
                                        </div>
                                    </div>
                                    <br>

                               
                                    <div class="row" id="newcategory">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('new_category','Category') }}
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4" id="parent">
                                            <div class="form-group {{ $errors->has('new_category') ? 'has-error' : '' }} input-group">
                                                {{ Form::text('new_category[]',null,['class'=>'form-control','id'=>'new_category.0','placeholder'=>'New Category' ]) }}
                                                <span class="input-group-btn">
                                                <a href="#" onclick="event.preventDefault()" id="addField" class="btn btn-success"><i class="fa fa-plus" ></i></a>
                                                </span>
                                            </div>
                                        </div>
                               

                                    </div>
                                    <div class="row" id="pickcategory">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('pick_category','Pick Category') }}
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4">
                                            <div class="form-group {{ $errors->has('pick_category') ? 'has-error' : '' }}">
                                                {{ Form::select('pick_category[]',\App\Models\ExternalContactCategory::pluck('category','id')->all(),null,['class'=>'form-control selectpicker','multiple','id'=>'pick_category','actionsBox'=>'true','data-live-search'=>'true']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button class="btn btn-sm btn-success" id="saveEmailBlast" >Upload</button>
                                </form>
                            @endcan
                            </div>
                        </div>
                        <hr>
                        <div class="card">
                            <div class="header">
                                <h1>External Contact List</h1>
                            </div>
                            <div class="body">
                                <div id="loader" style="display: none">
                                    <i class='fa fa-spinner fa-spin fa-3x fa-fw'></i><span class='sr-only'>Loading...</span>
                                </div>
                                <table class="table table-bordered table-striped table-hover  responsive " id="categoryList" width="100%">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Category</th>
                                        @can('3.5.3_delete_external_contatcs_category')
                                        <th>Action</th>
                                        @endcan
                                    </tr>
                                    </thead>
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


        $(document).ajaxStart(function(){
            $('#loader').show();
        }).ajaxStop(function(){
            $('#loader').hide();
        })

        function setSwitchery(switchElement, checkedBool) {
            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
                switchElement.setPosition(true);
                switchElement.handleOnchange(true);
            }
        }
        var mySwitch = new Switchery($('#getcategory')[0], {
            size:"small",
            color: '#0D74E9'
        });
        setSwitchery(mySwitch, false);
        $('#newcategory').show();
        $('#pickcategory').hide();
    </script>
    <script>
        function selectCategory(){

            var $this=$('#getcategory');
            if($this.is(':checked')){
                setSwitchery(mySwitch, true);
                $('#newcategory').hide();
                $('#pickcategory').show();
            }else {
                setSwitchery(mySwitch, false);
                $('#newcategory').show();
                $('#pickcategory').hide();
            }
        }
        $('#schedule').datetimepicker({
            format: 'DD MMMM YYYY hh:mm',
            minDate: new Date(),
            showClear:true,

        })

        function delElement() {
            $('form input, form select').removeClass('error');
            $('span.text-danger').remove();
        }
        $(document).ready(function(){
            var t=$('#categoryList').DataTable({
                //"deferRender":    true,
                //  "scrollY":        500,
                //  "scrollCollapse": true,
                //	"scroller":       true,
                "paging":true,
                //  "lengthChange": false,
                "stateSave":true,
                "ajax":{
                    "url":"{{ route('loadcategory') }}",
                    "dataSrc":"",
                    "type":"POST",
                    "data":{
                        "_token":"{{ csrf_token() }}"
                    }
                },
                "processing": true,
                "columns": [
                    {"data":null},
                    { "data": "category" },
                    @can('3.5.3_delete_external_contatcs_category')
                    { "data": null },
                    @endcan

                ],
                "columnDefs": [
                    {
                        "targets": 0,
                        "data": "id",
                    },{
                        "targets":1,
                        "render":function (data,type,row) {
                            @can('3.5.4_view_detail_external_contatcs')
                                return '<a href="category/'+row.id+'">'+data+'  <b>| ('+row.email_count+' Emails )</b></a>';
                            @else
                                return data+'  <b>| ('+row.email_count+' Emails )</b>';
                            @endcan
                        }
                    }
                    @can('3.5.3_delete_external_contatcs_category')
                    ,{
                        "targets":2,
                        "render":function (data,type,row) {
                            return '<a href="#" onclick="event.preventDefault(); delCategory(this.id)" class="btn btn-default"  id="category'+data.id+'"><i class="fa fa-trash"></i> </a>'
                        }
                    }
                    @endcan
                ],
                "pageLength":20,
                "createdRow": function( row, data, dataIndex){
                    $('td',row).eq(1).css('text-transform', "capitalize")
                },

            })
            t.on( 'order.dt search.dt ', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();




            var counter=0;
            $('#addField').on('click',function () {
                counter+=1
                var el=$(document.createElement('div')).attr('class','form-group  input-group remField'+counter+'');
                var el2='<input class="form-control" id="new_category.'+counter+'" placeholder="New Category" name="new_category[]" type="text"><span class="input-group-btn"> <a href="" class="btn btn-danger" onclick="event.preventDefault();remField(this.id)" id="remField'+counter+'"><i class="fa fa-minus"></i> </a>  </span>'
                el.append(el2)
                var par=$('#parent').append(el)
            })
            //based on: http://stackoverflow.com/a/9622978

            $('#saveBlast').on('submit', function(e){
                e.preventDefault();
                delElement()
                var form = e.target;
                var data = new FormData(form);
                $.ajax({
                    url: form.action,
                    method: form.method,
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function(data){
                        if (data.errors){
                            $.each(data.errors,function(i,v){
                                console.log(i)
                                var msg = '<span class="text-danger" id="'+i+'">'+v+'</span>';
                                $('input[name="' + i + '"], select[id="' + i + '"],input[id="' + i + '"]').addClass('error').after(msg);
                            })
                        } else {
                            swal({title:'Success',text:'Emails has been added',type:'success'},function () {
                                location.reload()
                            })
                        }
                    }
                })
            })
        })
        function remField(id) {
            var el=$('.'+id).remove()

        }
        function delCategory(id) {
            swal({title:'Delete Confirmation',text:'This Category will permanently deleted',type:'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText:'Delete',
                    cancelButtonText: 'No',
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        var idnum=id.replace('category','')
                        $.ajax({
                            url:'{{ route('delcategory') }}',
                            type:'POST',
                            data:{
                                _token:'{{ csrf_token() }}',
                                id:idnum
                            },success:function (d) {
                                if(d==='success') {
                                    location.reload()
                                }
                            }
                        })
                    } else {
                        swal('Cancelled', 'Delete Template Cancelled','error');
                    }
                })
        }
    </script>
@endsection
