@extends('layouts.sidebar')

@section('notifications-selected', 'active')

@section('title', 'Notification')
@section('css')
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .btn-dropdown {
            padding: 10px;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .employee-checkbox {
            display: block;
            padding: 8px;
        }

        .dropdown-content label {
            display: block;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
        #previewModal .modal-dialog {
            max-width: 800px;
        }

        #previewModal .modal-content {
            padding: 20px;
        }
    </style>
@endsection
@section('content')


    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Create Notification</h6>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="notification-form">
                        <form action="{{ route('send.notifications') }}" method="post">
                            @csrf
                            <div class="mb-2">
                                <label for="subject" class="col-sm-2 col-form-label">Subject</label>
                                    <input type="text" name="subject" required class="form-control" id="subject">
                            </div>
                            <textarea style="line-height: 0.3" name="message" id="local-upload" class="form-control" rows="5" placeholder="Type your notification here..."></textarea>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="row col-md-12 mt-4">
                                <div class="float-left">
                                    <button type="button" class="btn btn-info" id="previewButton" data-toggle="modal" data-target="#previewModal">Preview</button>
                                    <button type="submit" id="sendNotification" class="btn btn-success">Send Notifications</button>
                                </div>
                            </div>

                            <div class="row col-md-12 mt-4">
                                <div class="employee-list">
                                    <h4>Select Employees</h4>
                                    <ul style="list-style: none; padding: 0;">
                                        @php $employeesArray = $employees->toArray(); @endphp
                                        @php $employeesCount = count($employeesArray); @endphp
                                        @for ($i = 0; $i < $employeesCount; $i += 3)
                                            <div class="row">
                                                @foreach(array_slice($employeesArray, $i, 3) as $employee)
                                                    <div class="col-md-4">
                                                        <li>
                                                            <label class="employee-checkbox">
                                                                <input type="checkbox" name="selected_employees[]" value="{{ $employee['id'] }}" checked>
                                                                {{ $employee['name'] }} ({{ $employee['email'] }})
                                                            </label>
                                                        </li>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endfor
                                    </ul>
                                </div>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Email Template Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="previewContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




    <script>
        $(document).ready(function() {
            $('#previewButton').click(function() {
                $.ajax({
                    url: '/preview-notification', // Replace with your actual URL
                    type: 'GET',
                    success: function(data) {
                        console.log(data); // Check the console for the message
                        $('#previewContent').html(data.message);
                        $('#previewModal').modal('show'); // Show the modal
                    },
                    error: function() {
                        alert('Error loading preview.');
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
{{--    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>--}}

    <style>
        .ck-editor__editable_inline{
            height: 400px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#sendNotification').on('click', function (e) {
                e.preventDefault(); // Prevent the default form submission

                var selectedEmployees = $('input[name="selected_employees[]"]:checked');

                if (selectedEmployees.length > 0) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You are about to send notifications!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, send it!',
                        cancelButtonText: 'No, cancel',
                        preConfirm: () => {
                            return new Promise((resolve) => {
                                resolve();
                            });
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('form').submit();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'At least one employee should be selected.',
                    });
                }
            });
        });
    </script>


@endsection

