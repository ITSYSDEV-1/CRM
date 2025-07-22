@extends('layouts.master')
@section('title')
    Excluded Email/Domain  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')

    <div class="modal fade" id="addExcluded" tabindex="-1" role="dialog" aria-labelledby="addExcludedLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    @can('3.6.1_add_exclude_email')
                    <h5 class="modal-title" id="addExcludedLabel">Add Email / Domain to Exclude</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    @endcan
                </div>
                <div class="modal-body">
                    <form id="addEmailForm" >
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('email','Email/Domain') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::text('email',null,['class'=>'form-control','id'=>'email','data-live-search'=>'true','required','placeholder'=>'Email/Domain']) }}
                                </div>
                                <span class="text-danger">
                                            <strong id="email-error">
                                            </strong>
                                            </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="submitBtn" class="btn btn-sm btn-success">Save</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteExcluded" tabindex="-1" role="dialog" aria-labelledby="deleteExcludedLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteExcludedLabel">Remove Email / Domain from Exclusion List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="deleteEmailForm">
                        <input type="hidden" id="delete_id" name="delete_id" value="">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p>Are you sure you want to remove <strong id="email-to-delete"></strong> from the exclusion list?</p>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                            {{ Form::label('delete_reason','Reason for Removal') }}
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::textarea('delete_reason', null, ['class'=>'form-control','id'=>'delete_reason','required','placeholder'=>'Please provide a reason for removing this email/domain from the exclusion list', 'rows'=>'3']) }}
                                </div>
                                <span class="text-danger">
                                    <strong id="delete-reason-error"></strong>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-sm btn-danger">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                        @can('3.6.1_add_exclude_email')
                            <div class="header">
                                <h2>Excluded Email / Domain List</h2>
                            </div>
                            <div class="">
                                <a href="#addExcluded" class="btn btn-xs btn-success" data-toggle="modal" data-target="#addExcluded" id="addEmailDomain" title="Add More Email / Domain to Exclude " ><i class="fa fa-plus" style="font-size: 1.5em"></i> Add Email/Domain</a>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>
                        @endcan

                            <div class="body">
                                <table class="table table-bordered table-hover table-striped" id="tbl" width="100%">
                                    <thead class="bg-teal">
                                    <tr>
                                        <th class="align-center">#</th>
                                        <th>Email/Domain</th>
                                        <th>Reason</th>
                                        @can('3.6.2_delete_exclude_email')
                                        <th>Action</th>
                                        @endcan
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $key=>$d)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $d->email }}</td>
                                            <td>{{ $d->reason }}</td>
                                            @can('3.6.2_delete_exclude_email')
                                            <td>
                                                <a href="#"
                                                data-id="{{ $d->id }}"
                                                data-email="{{ $d->email }}"
                                                data-toggle="modal"
                                                data-target="#deleteExcluded">
                                                <i class="fa fa-trash" style="font-size: 1.8em"></i>
                                                </a>
                                            </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                    </tbody>
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
            $('#tbl').DataTable({
                responsive: true
            });

            $('#submitBtn').click(function (e) {
                e.preventDefault();
                var email = $('#email').val();
                $.ajax({
                    url: '{{ url('contacts/excluded/addemail') }}',
                    type: 'POST',
                    data: {
                        email: email,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (data === 'success') {
                            swal({
                                title: "Success",
                                text: "Email/Domain added to exclusion list",
                                type: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#email-error').html(data.errors.email);
                        }
                    }
                });
            });

            // Handle delete button click - FIXED
            $(document).on('click', '.delete-btn', function() {
                // Get the data attributes
                var id = $(this).data('id');
                var email = $(this).data('email');
                
                console.log("Delete button clicked for ID:", id, "Email:", email);
                
                // Clear previous errors
                $('#delete-reason-error').text('');
                $('#delete_reason').val('');
                
                // Set values in the form - make sure this is working
                $('#delete_id').val(id);
                $('#email-to-delete').text(email);
                
                // Debug - check if ID is set correctly
                console.log("ID value after setting:", $('#delete_id').val());
            });

            // Handle confirm delete button click - FIXED
            $('#confirmDeleteBtn').click(function(e) {
                e.preventDefault();
                
                // Get form data
                var id = $('#delete_id').val();
                var reason = $('#delete_reason').val();
                
                console.log("Confirm delete clicked. ID:", id, "Reason:", reason);
                
                // Client-side validation
                if (!id) {
                    $('#delete-reason-error').text('Error: ID is missing. Please try again or refresh the page.');
                    return;
                }
                
                if (!reason) {
                    $('#delete-reason-error').text('Please provide a reason for removal');
                    return;
                }
                
                // Send AJAX request
                $.ajax({
                    url: '{{ url('contacts/excluded/delete') }}',
                    type: 'POST',
                    data: {
                        id: id,
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        console.log("Sending request with ID:", id, "Reason:", reason);
                    },
                    success: function(response) {
                        console.log("Success response:", response);
                        if (response.status === 'success') {
                            swal({
                                title: "Success",
                                text: "Email/Domain removed from exclusion list",
                                type: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else if (response.errors) {
                            var errorMsg = '';
                            $.each(response.errors, function(key, value) {
                                errorMsg += value + '<br>';
                            });
                            $('#delete-reason-error').html(errorMsg);
                        } else {
                            swal({
                                title: "Error",
                                text: "Failed to remove email/domain",
                                type: "error",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error details:", xhr.responseText);
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.errors) {
                                var errorMsg = '';
                                $.each(response.errors, function(key, value) {
                                    errorMsg += value + '<br>';
                                });
                                $('#delete-reason-error').html(errorMsg);
                            } else {
                                $('#delete-reason-error').html('An error occurred while processing your request');
                            }
                        } catch (e) {
                            $('#delete-reason-error').html('An error occurred while processing your request');
                        }
                    }
                });
            });
        });
    </script>
@endsection