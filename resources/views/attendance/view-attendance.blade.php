@extends('layouts.sidebar')

@section('view-attendance-selected', 'active')

@section('title', 'View Attendance')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row pb-3" id="cardSection" style="display: none;">
            <div class="col-lg mb-2">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">Total Days</div>
                        <div id="totalDays" class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg mb-2">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase">Working Days Passed</div>
                        <div id="workingDaysPassed" class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg mb-2">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase">Working Hours Passed</div>
                        <div id="workingHoursPassed" class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg mb-2">
                <div class="card border-left-danger shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase">Public Holidays</div>
                        <div id="publicHolidays" class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                </div>
            </div>
        </div>



        <div class="card shadow" id="tableSection" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center"></h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input class="form-control" type="month" value="{{ now()->format('Y-m') }}" name="monthSelect">
                </div>
                <div class="table-responsive">
                    <table class="table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Leave</th>
                                <th class="text-center">Remote</th>
                                <th class="text-center">Hours Required</th>
                                <th class="text-center">Hours Worked</th>
                                <th class="text-center">Difference</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data is populated here! --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // For initial loading of current month
            let currentMonth = document.querySelector('input[name="monthSelect"]').value;
            getAttendanceData(currentMonth);

            // Retrieve data
            function getAttendanceData(month) {
                updateCardHeader(month);
                $.ajax({
                    url: '{{ route('get-attendance-report') }}',
                    method: 'GET',
                    data: {
                        month: month
                    },
                    beforeSend: function() {
                        swal({
                            title: "Fetching Data!",
                            text: "Please Wait!",
                            icon: "warning",
                            buttons: false
                        });
                    },
                    success: function(response) {
                        updateTable(response.employees); // Update table with employee data
                        if (response.employees.length === 0) {
                            displayNoDataMessage();
                        } else {
                            updateAttendanceSummary(response.summary); // Update cards with summary
                            showSections();
                        }
                        swal.close();
                    },
                    error: function(xhr, status, error) {
                        swal.close();
                        displayNoDataMessage();
                    }
                });
            }

            // Show data
            function showSections() {
                const cardSection = document.getElementById('cardSection');
                const tableSection = document.getElementById('tableSection');
                cardSection.style.display = 'flex'; // Displaying cardSection as flex
                tableSection.style.display = 'block'; // Displaying tableSection as block
            }

            // Summary
            function updateAttendanceSummary(summary) {
                const totalDaysElement = document.getElementById('totalDays');
                const workingDaysPassedElement = document.getElementById('workingDaysPassed');
                const workingHoursPassedElement = document.getElementById('workingHoursPassed');
                const publicHolidaysElement = document.getElementById('publicHolidays');

                totalDaysElement.textContent = summary.totalDays;
                workingDaysPassedElement.textContent = summary.workingDaysPassed;
                workingHoursPassedElement.textContent = summary.workingHoursPassed;
                publicHolidaysElement.textContent = summary.publicHolidays;
            }

            // Populate table
            function updateTable(data) {
                let tableBody = document.querySelector('table tbody');
                tableBody.innerHTML = '';

                if (data.every(employee => employee.totalHoursWorked === '00:00' && employee.totalHoursRequired ===
                        '00:00' && employee.difference === '00:00')) {
                    displayNoAttendanceDataMessage();

                } else {
                    if (data.length > 0) {

                        data.forEach(function(employee) {
                            let row = `<tr class="mt-2">
                                <td>${employee.name}</td>
                                <td class="text-center fw-bold ${employee.leave < 2 ? 'text-success' : 'text-danger'}">${employee.leave}</td>
                                <td class="text-center fw-bold ${employee.remote < 2 ? 'text-success' : 'text-danger'}">${employee.remote}</td>
                                <td class="text-center">${employee.totalHoursRequired}</td>
                                <td class="text-center">${employee.totalHoursWorked}</td>
                                <td class="text-center fw-bold ${employee.differenceClass}">${employee.difference}</td>
                            </tr>`;

                            tableBody.innerHTML += row;
                        });
                    } else {
                        displayNoDataMessage();
                    }
                }
            }

            // Update header
            function updateCardHeader(month) {
                const monthName = getMonthName(month);
                const cardHeader = document.querySelector('.card-header h6');
                cardHeader.textContent = `${monthName}, ${getYear(month)}`;
            }

            // Month name
            function getMonthName(date) {
                const options = {
                    month: 'long'
                };
                return new Date(date + '-01').toLocaleDateString('en-US', options);
            }

            // Year
            function getYear(date) {
                return new Date(date + '-01').getFullYear();
            }

            // No data available
            function displayNoAttendanceDataMessage() {
                let tableBody = document.querySelector('table tbody');
                tableBody.innerHTML =
                    '<tr><td colspan="6" class="text-center">No attendance data available for the selected month.</td></tr>';
            }

            // Month changing
            document.querySelector('input[name="monthSelect"]').addEventListener('change', function() {
                document.querySelector('input[name="monthSelect"]').blur();
                let month = this.value;
                let tableBody = document.querySelector('table tbody');
                tableBody.innerHTML = ''; // Empty the table on date change
                getAttendanceData(month); // Resend request for the changed month
            });

        });
    </script>
@endsection
