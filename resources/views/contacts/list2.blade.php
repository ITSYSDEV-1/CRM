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
								<h2>Contact List</h2>
							</div>
							<div class="row clearfix">
								<div class="col-lg-12">
								</div>
							</div>

							<div class="body">
								<table style="font-size: 13px" class="table table-bordered table-striped table-hover responsive js-basic-example  " id="loadcontacts" width="100%">
									<thead class="bg-teal">
									<tr>
										<th class="align-center">No</th>
										<th class="align-center">Full Name</th>
										<th class="align-center">Last Name</th>
										<th class="align-center">Birthday</th>
										<th class="align-center">Wedding Anniversary</th>
										<th class="align-center">Country</th>
										<th class="align-center">Area/Origin</th>
										<th class="align-center">Status</th>
										<th class="align-center">Campaign</th>
										<th class="align-center">Total Stays</th>
										<th class="align-center">Last Stay</th>
										<th class="align-center">Total Spending (Rp.)</th>

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
		    var item=[];
		    var path="{{ asset('countries.json') }}"
		    var json = $.getJSON(path,function (d) {
				$.each(d,function (i) {
					item.push(d[i])
                })
            })

			var t=$('#loadcontacts').DataTable({
               //"deferRender":    true,
             //  "scrollY":        500,
             //  "scrollCollapse": true,
			//	"scroller":       true,
				"paging":true,
             //  "lengthChange": false,
				"pageLength":25,
				"stateSave":true,
                "ajax":{
                    "url":"{{ route('loadcontacts') }}",
                    "dataSrc":"",
                    "type":"POST",
                    "data":{
                        "_token":"{{ csrf_token() }}",
                    }
                },
                "processing": true,
                "language":{
                  "processing":"<i class='fa fa-spinner fa-spin fa-3x fa-fw'></i><span class='sr-only'>Loading...</span> "
				},
                "columns": [
                    {"data":null},
                    { "data": "fname" },
                    { "data": "lname" },
                    { "data": "birthday" },
                    { "data": "wedding_bday" },
                    { "data": "country_id" },
                    { "data": "area" },
                    { "data": "transaction.0.status" },
                    { "data": "campaign_count" },
                    { "data": "transaction_count" },
                    { "data": "transaction.0.checkin" },
                    { "data": "transaction" },


                ],
                "columnDefs": [
                    {
                        "targets": 0,
                        "data": "id",
                    },{
                        "targets":1,
                        "render":function (data,type,row) {
                            var id=row.contactid
							if(row.lname===null) {
                               var lname='';
                            } else {
                                var lname = row.lname
							}
                            return '<a href="{{ url('contacts/detail/') }}'+'/'+id+'" >'+ data +' ' +lname+'</a>'
//							data +' '+ row.lname
                        }
                    },{
                        "visible":false,"targets":2
                    },{
                    	"targets":[3,4],
						"render":function (data,type,row) {
							if(moment(data).isValid()){
                                return moment(data).format("MMM DD")
							}else {
							    return ''
							}
                        }
					},{
                    	"targets":5,
						"render":function (data,type,row) {
							for(var i in item){
							    if(data===item[i]['iso2']){
                                    var d=''
                                    d=(item[i]["iso2"])
                                    d=d.toLowerCase()
                                 	var  country=item[i]["country"];
                                    return country +'<img src="../flags/blank.gif"  class="flag flag-'+d+' pull-right"  alt="'+data+'" />';
								}
							}
                        }
					},{
                    	"targets":7,
						"render":function (data,type,row) {
							switch (data){
								case 'C':
								    return "Confirm";
								    break
								case 'O':
								    return "Checkout"
									break
								case "I":
								    return "Inhouse"
									break
								case "X":
								    return "Cancel"
									break
								case "G":
								    return "Guaranteed"
									break
								case "N":
									return "No Show"
									break
								case "":
									return ""
									break
								case null:
									return ""
									break
								//default:""
							}
                        }
					},{
                    	"targets":8,
						"render":function (data,type,row) {
                            if(data>0){
                                return "<i class='fa fa-envelope success'></i> "+ data +" Campaign"
							}else
							{
							    return ""
							}
                        }
					},{
                    	"targets":10,
						"render":function (data,type,row) {
							if(data!==''){
							    return moment(data).format('DD MMM YYYY')
							}
                        }
					},{
                    	"targets":11,
						"render":function (d,t,r) {
                    	    var s=0
							for(var i=0; i<=d.length-1;i++){
                    	        s+=d[i].revenue
							}
                            d=Math.floor(s)
							return d.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                        }
					}
				],

                "createdRow": function( row, data, dataIndex){
                    $('td',row).eq(1).css('text-transform', "capitalize")
                },

			})
            t.on( 'order.dt search.dt ', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        })

	</script>

@endsection
