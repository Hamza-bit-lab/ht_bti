@extends('layouts.sidebar')

@section('all-employees-selected', 'active')

@section('title', 'Employees')

@section('css')
    <style>
        table.dataTable tbody td {
            vertical-align: middle;
        }

        #dataTable td:nth-child(6) {
            white-space: nowrap;
            text-align: center;
        }

        #dataTable tbody td {
            border-bottom: 1px solid #ddd;
        }
    </style>
@endsection

@section('content')

    <!-- Dropdown -->
    <div class="mb-3 container-fluid">
        <label for="employeeStatus">Select Employee Status:</label>
        <select class="form-control" id="employeeStatus">
            <option value="active" selected>Active Employees</option>
            <option value="inactive">Inactive Employees</option>
        </select>
    </div>
    {{-- Table --}}
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Employees</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Attendance ID</th>
                                <th>Position</th>
                                <th>Phone</th>
{{--                                <th>Joining Date</th>--}}
                                <th>Interviewer</th>
                                <th>Employed</th>
                                <th>Birthday</th>
{{--                                <th>Work Anniversary</th>--}}
                                <th>Contract Expiry Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Employee Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="employeeModalBody">
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        // Initialize dataTable
        function calculateAnniversaryCount(joiningDate, today) {
            var anniversaryCount = 1;
            var tempDate = new Date(joiningDate);
            var currentAnniversary = new Date(tempDate);

            while (currentAnniversary < today) {
                currentAnniversary.setFullYear(tempDate.getFullYear() + anniversaryCount);
                anniversaryCount++;
            }

            return anniversaryCount - 1;
        }
        function getOrdinalSuffix(number) {
            if (number === 1) {
                return 'st';
            } else if (number === 2) {
                return 'nd';
            } else if (number === 3) {
                return 'rd';
            } else {
                return 'th';
            }
        }
        function calculateMonthsDifference(date1, date2) {
            var months;
            months = (date2.getFullYear() - date1.getFullYear()) * 12;
            months -= date1.getMonth() + 1;
            months += date2.getMonth();
            return months <= 0 ? 0 : months;
        }
        $(document).ready(function() {
            var dataTable = $('#dataTable').DataTable({
                order: [
                    [4, 'desc']
                ],
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('get-employees') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'attendance_id', name: 'attendance_id' },
                    { data: 'position', name: 'position' },
                    { data: 'phone', name: 'phone' },
                    { data: 'is_interviewer', name: 'is_interviewer' },
                    { data: 'is_employed', name: 'is_employed' },
                    {
                        data: 'birthday',
                        name: 'birthday',
                        render: function(data, type, row) {
                            if (!data) {
                                return 'N/A';
                            }

                            var dob = new Date(data);
                            dob.setHours(0, 0, 0, 0);

                            // Format the date as YYYY-MM-DD
                            var formattedDOB = dob.toISOString().split('T')[0];

                            return formattedDOB;
                        }
                    },


                    {
                        data: 'contract_end_date',
                        name: 'contract_end_date',
                        render: function (data, type, row) {
                            var contractEndDate = new Date(data);
                            var today = new Date();

                            contractEndDate.setHours(0, 0, 0, 0);
                            today.setHours(0, 0, 0, 0);

                            if (contractEndDate < today) {
                                return '<span style="color: red; font-weight: bold">Expired</span>';
                            }

                            var timeDifference = contractEndDate - today;
                            var daysLeft = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));

                            if (daysLeft >= 30) {
                                var monthsLeft = Math.floor(daysLeft / 30);
                                return monthsLeft + ' months left';
                            } else {
                                var color = daysLeft <= 10 ? '#FFAD01' : 'black';
                                return '<span style="color: ' + color + ';">' + daysLeft + ' days left</span>';
                            }
                        }
                    },

                    { data: 'action', name: 'action' }
                ]
            });

            $('#employeeStatus').on('change', function () {
                var employeeStatus = $(this).val();

                var isVisible = employeeStatus === 'active';
                dataTable.column(6).visible(isVisible);
                dataTable.column(7).visible(isVisible);
                dataTable.column(8).visible(isVisible);


                // Reload the DataTable to apply the changes
                dataTable.ajax.url("{{ route('get-employees') }}?status=" + employeeStatus).load();
            });
        });

        // Edit Employee ID
        $(document).on('click', '.edit-attendance-id', function(e) {
            e.preventDefault();
            var attendanceIdField = $(this).siblings('span');
            var originalAttendanceId = attendanceIdField.text();

            swal({
                text: 'Attendance ID:',
                content: {
                    element: 'input',
                    attributes: {
                        value: originalAttendanceId,
                    },
                },
                buttons: {
                    cancel: true,
                    confirm: {
                        text: 'Update',
                        closeModal: false,
                    },
                },
            }).then((value) => {
                if (value) {
                    var newAttendanceId = $('.swal-content__input').val();
                    var token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        type: 'PATCH',
                        url: '/update-attendance-id/' + $(this).data('employee-id'),
                        data: {
                            attendance_id: newAttendanceId,
                            _token: token,
                        },
                        success: function(response) {
                            attendanceIdField.text(newAttendanceId);
                            swal.close();
                            swal({
                                title: " ",
                                icon: "success",
                                timer: 800,
                                buttons: false,
                            });
                        },
                        error: function(error) {
                            swal({
                                text: "The attendance ID is already taken!",
                                icon: "error",
                            });
                        },
                    });
                }
            });
        });

        // View employee modal
        $(document).on('click', 'a[href*="view-employee"]', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            $.get(url, function(data) {
                var employeeDetails = $(data).find('.card-body').html();

                $('#employeeModalBody').html(employeeDetails);
                $('#employeeModal').modal('show');
            });
        });

        // Delete employee
        $(document).on('click', 'a[href*="delete-employee"]', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this record!",
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
                                    text: "Employee is associated with one or more interviews.",
                                    icon: "error",
                                    timer: 1500,
                                    buttons: false
                                });
                            }
                        });
                    }
                });
        });

        // Update Interviewer status
        $(document).ready(function() {
            $('#dataTable').on('change', '.toggle-switch-interviewer', function() {
                const checkbox = $(this);
                const employeeId = checkbox.data('id');
                const newStatus = checkbox.prop('checked') ? 1 : 0;

                $.ajax({
                    url: `/update-interviewer-status/${employeeId}`,
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'newStatus': newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            checkbox.data('is-interviewer', newStatus);
                            swal({
                                title: " ",
                                icon: "success",
                                timer: 1000,
                                buttons: false
                            });
                        }
                    },
                    error: function(error) {
                        swal({
                            title: "Error!",
                            text: "An error occurred while updating employee status.",
                            icon: "error",
                            timer: 1000,
                            buttons: false
                        });
                        // Reset checkbox to previous state if update fails
                        checkbox.prop('checked', !checkbox.prop('checked'));
                    }
                });
            });
        });

        // Update Employee status
        $(document).ready(function() {
            $('#dataTable').on('change', '.toggle-switch-employed', function() {
                const checkbox = $(this);
                const employeeId = checkbox.data('id');
                const newStatus = checkbox.prop('checked') ? 1 : 0;

                $.ajax({
                    url: `/update-employee-status/${employeeId}`,
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'newStatus': newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            checkbox.data('is-employed', newStatus);
                            swal({
                                title: " ",
                                icon: "success",
                                timer: 1000,
                                buttons: false
                            });
                        }
                    },
                    error: function(error) {
                        swal({
                            title: "Error!",
                            text: "An error occurred while updating employee status.",
                            icon: "error",
                            timer: 1000,
                            buttons: false
                        });
                        // Reset checkbox to previous state if update fails
                        checkbox.prop('checked', !checkbox.prop('checked'));
                    }
                });
            });
        });

        // Alerts
        document.addEventListener('DOMContentLoaded', function() {
            let successMessage = "{{ session('success') }}";
            if (successMessage) {
                swal({
                    title: "Saved Successfully!",
                    text: " ",
                    icon: "success",
                    timer: 1000,
                    buttons: false,
                });
            }

            let errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                swal({
                    title: "Task Failed!",
                    text: errorMessage,
                    icon: "error",
                });
            }
        });
    </script>
@endsection
