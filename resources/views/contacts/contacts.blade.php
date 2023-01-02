@extends('layouts.master')
@section('title')
    Contact List | {{ \App\Models\Configuration::first()->hotel_name.' '.\App\Models\Configuration::first()->app_title }}
@endsection
@section('content')

    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h1>Contact List</h1>
                            </div>
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
                            </div>
                        </div>
                        <hr>
                        <div class="card">
                            <div class="header">
                                <h1></h1>
                            </div>
                                <div class="body">
                                    <table style="font-size: 13px" class="table table-bordered table-striped table-hover responsive js-basic-example  " id="loadcontacts" width="100%">
                                            <thead class="bg-teal">
                                            <tr>
                                                <th class="align-center">No</th>
                                                <th class="align-center">Real Name</th>
                                                <th class="align-center">Email</th>
                                                <th class="align-center">Category</th>
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
    $(document).ready(function () {
        loadContacts()
    })
    function loadContacts() {
        var path=window.location.pathname
        path=path.replace('/contacts/category/','')
        var t= $('#loadcontacts').DataTable({
                "autoWidth": true,
                "processing": true,
                "serverSide": true,
                "pageLength":20,
                "ajax":{
                    "url": "{{ route('listexternalcontact') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{
                        _token: "{{csrf_token()}}",
                        cat:path,
                    }
                },
                "columns": [
                    { "data": null },
                    { "data": "fname" },
                    { "data": "email" },
                    { "data": "category" },

                ],
                "columnDefs":[
                    {
                        "targets":0,
                        "render":function(data,type,row,meta){
                            return meta.row+1
                        }
                    }
                ]

            });
    }

    function deleteContact(id){
        swal({title:'Delete Confirmation',text:'This Contacts will permanently deleted',type:'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText:'Delete',
                    cancelButtonText: 'No',
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        var idnum=id.replace('contact','')
                        $.ajax({
                            url:'{{ route('delcontact') }}',
                            type:'POST',
                            data:{
                                _token:'{{ csrf_token() }}',
                                id:idnum
                            },success:function (d) {
                                if(d==='success') {
                                    swal({title:"Delete Success",text:"Contact deleted",type:"success",confirmButtonColor: '#5fba7d',confirmButtonText:'OK',closeOnConfirm: false},function(isConfirm){
                                        if(isConfirm){
                                            location.reload()
                                        }
                                    })

                                }
                            }
                        })
                    } else {
                        swal('Cancelled', 'Delete Cancelled','error');
                    }
        })
    }
</script>
@endsection
