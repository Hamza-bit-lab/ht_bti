@extends('layouts.sidebar')

@if ($day == 'yesterday')
    @section('yesterday-interview-selected', 'active')
    @section('title', 'Yesterday Interviews')
@elseif ($day == 'today')
    @section('today-interview-selected', 'active')
    @section('title', 'Today Interviews')
@elseif ($day == 'tomorrow')
    @section('tomorrow-interview-selected', 'active')
    @section('title', 'Tomorrow Interviews')
@elseif ($day == 'all')
    @section('all-interview-selected', 'active')
    @section('title', 'Interviews')
@endif

@section('css')
    <style>
        .option-color {
            background: white;
            color: black;
        }

        table.dataTable tbody td {
            vertical-align: middle;
        }

        #dataTable td:nth-child(8) {
            width: 11%;
        }

        #dataTable td:nth-child(9) {
            white-space: nowrap;
            text-align: center;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        #dataTable tbody td {
            border-bottom: 1px solid #ddd;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Filters --}}
        @if ($day == 'all')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">Filters</h6>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-lg-4">
                            <label for="interviewerFilter">Interviewer:</label>
                            <div class="input-group">
                                <select id="interviewerFilter" class="form-select">
                                    <option value="all interviewers" selected>All Interviewers</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->name }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="clearInterviewerFilter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-lg-4">
                            <label for="statusFilter">Status:</label>
                            <div class="input-group">
                                <select id="statusFilter" class="form-select">
                                    <option value="all status" selected>All Statuses</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="clearStatusFilter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="typeFilter">Type:</label>
                            <div class="input-group">
                                <select id="typeFilter" class="form-select">
                                    <option value="all types" selected>All Types</option>
                                    <option value="physical">Physical Interviews</option>
                                    <option value="online">Online Interviews</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="clearTypeFilter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-4">
                            <label for="startDate">Start Date:</label>
                            <div class="input-group">
                                <input type="date" id="startDate" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="clearStartDate">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="endDate">End Date:</label>
                            <div class="input-group">
                                <input type="date" id="endDate" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="clearEndDate">
                                        <i class="fas fa-times"></i>
                                    </b utton>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-lg-2 d-flex align-items-end justify-content-end">
                            <button id="resetButton" class="btn btn-danger btn-block">Reset</button>
                        </div>
                        <div class="form-group col-lg-2 d-flex align-items-end justify-content-end">
                            <button id="searchButton" class="btn btn-primary btn-block">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- DataTable --}}
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">{{ Str::ucfirst($day) }} Interviews</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Date</th>
                                <th>Interview</th>
                                <th>Interviewer</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div class="modal fade" id="interviewModal" tabindex="-1" role="dialog" aria-labelledby="interviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="interviewModalLabel">Interview Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="interviewModalBody">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Initialize dataTable
        $(document).ready(function() {
            var day = '{{ $day }}';
            $('#dataTable').DataTable({
                order: [
                    [4, 'desc']
                ],
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('get-interviews', '') }}/" + day,
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'interviewer',
                        name: 'interviewer'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                    }
                ]
            });
        });

        // Search dataTable
        $('#searchButton').click(function() {
            const interviewer = $('#interviewerFilter').val();
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const status = $('#statusFilter').val();
            const interviewType = $('#typeFilter').val();

            $('#dataTable').DataTable().destroy();

            $('#dataTable').DataTable({
                order: [
                    [4, 'desc']
                ],
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('filter-interviews') }}",
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'interviewer': interviewer,
                        'status': status,
                        'interviewType': interviewType,
                        'startDate': startDate,
                        'endDate': endDate,
                    },
                },
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'interviewer',
                        name: 'interviewer'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                    }
                ]
            });
        });

        // Enter button trigger
        $('#interviewerFilter, #statusFilter, #typeFilter, #startDate, #endDate').keydown(function(event) {
            if (event.key === "Enter") {
                $('#searchButton').trigger('click');
                event.preventDefault();
            }
        });

        // Escape button trigger
        $(document).keydown(function(event) {
            if (event.key === "Escape") {
                $('#resetButton').trigger('click');
                event.preventDefault();
            }
        });

        // Reset dataTable
        $('#resetButton').click(function() {
            $('#interviewerFilter').val('all interviewers');
            $('#statusFilter').val('all status');
            $('#typeFilter').val('all types');
            $('#startDate').val('');
            $('#endDate').val('');

            $('#searchButton').trigger('click');
        });

        // Send reminder email (from "Today" interviews)
        function sendReminderEmail(id) {
            $.ajax({
                url: "{{ route('send-reminder-email', '') }}/" + id,
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': id
                },
                success: function(response) {
                    swal({
                        title: "Email Sent!",
                        text: " ",
                        icon: "success",
                        timer: 1000,
                        buttons: false
                    });
                },
                error: function(xhr) {
                    swal({
                        title: "Email Sending Failed!",
                        text: " ",
                        icon: "error",
                        timer: 1000,
                        buttons: false
                    });
                }
            });
        }

        // View interview modal
        $(document).on('click', 'a[href*="view-interview"]', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            $.get(url, function(data) {
                var interviewDetails = $(data).find('.card-body').html();

                $('#interviewModalBody').html(interviewDetails);
                $('#interviewModal').modal('show');
            });
        });

        // Status update
        $(document).ready(function() {
            $('#dataTable').on('change', 'select.interview_status', function() {
                const id = $(this).data('interview-id');
                const status = $(this).val();

                $.ajax({
                    url: "{{ route('update-interview-status', '') }}/" + id,
                    method: "POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'status': status,
                        'id': id
                    },
                    success: function(response) {
                        const responseColor = response.color;
                        const responseText = response.text;
                        const selectElement = $(`#interview-status-${id}`);
                        selectElement.removeClass();
                        selectElement.addClass(
                            `${responseText} form-control interview_status ${responseColor}`
                        );
                        swal({
                            title: "Status Updated!",
                            text: " ",
                            icon: "success",
                            timer: 800,
                            buttons: false
                        });
                    },
                    error: function(xhr, status, error) {
                        swal({
                            title: "Error!",
                            text: "An error occurred while updating interview status.",
                            icon: "error",
                            timer: 1000,
                            buttons: false
                        });
                    },
                });
            });
        });

        // Delete interview
        $(document).on('click', 'a[href*="delete-interview"]', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this event!",
                    icon: "warning",
                    buttons: {
                        cancel: "No",
                        confirm: {
                            text: "Yes",
                            value: true,
                            visible: true,
                            closeModal: false
                        }
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function(response) {
                                var table = $('#dataTable').DataTable();
                                var row = table.row($(this).closest('tr'));
                                row.remove().draw(false);
                                swal({
                                    title: "Record Deleted!",
                                    text: " ",
                                    icon: "success",
                                    timer: 1000,
                                    buttons: false
                                });
                            },
                            error: function(xhr, status, error) {
                                swal({
                                    title: "Error Deleting!",
                                    text: " ",
                                    icon: "error",
                                    timer: 1000,
                                    buttons: false
                                });
                            }
                        });
                    }
                });
        });

        // Filter reset buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('clearInterviewerFilter').addEventListener('click', function() {
                document.getElementById('interviewerFilter').selectedIndex = 0;
            });

            document.getElementById('clearStatusFilter').addEventListener('click', function() {
                document.getElementById('statusFilter').selectedIndex = 0;
            });

            document.getElementById('clearTypeFilter').addEventListener('click', function() {
                document.getElementById('typeFilter').selectedIndex = 0;
            });

            document.getElementById('clearStartDate').addEventListener('click', function() {
                document.getElementById('startDate').value = '';
            });

            document.getElementById('clearEndDate').addEventListener('click', function() {
                document.getElementById('endDate').value = '';
            });
        });
    </script>
@endsection
