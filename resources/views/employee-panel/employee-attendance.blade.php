@extends('layouts.sidebar')

@section('employee-attendance-selected', 'active')

@section('title', 'Attendance')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row pb-3" id="cardSection">
            <div class="col-lg mb-2">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">Total Days</div>
                        <div id="totalDays" class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg mb-2">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">Working Days Passed</div>
                        <div id="workingDaysPassed" class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg mb-2">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">Hours Passed</div>
                        <div id="workingHoursPassed" class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg mb-2">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">Hours Worked</div>
                        <div id="workingHoursWorked" class="h5 mb-0 font-weight-bold text-gray-800"></div>
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

        <div class="card shadow" id="tableSection">
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
                                <th>Date</th>
                                <th class="text-center">CheckIn</th>
                                <th class="text-center">CheckOut</th>
                                <th class="text-center">Hours Required</th>
                                <th class="text-center">Hours Worked</th>
                                <th class="text-center">Adjustment</th>
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
                    url: '{{ route('get-employee-attendance-report') }}',
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
                        swal.close(); // Close the loading message first

                        updateTable(response.attendance);
                        if (response.attendance.length === 0) {
                            displayNoDataMessage();
                        } else {
                            updateAttendanceSummary(response.summary);
                            showSections();
                        }
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
                const workingHoursWorkedElement = document.getElementById('workingHoursWorked');
                const publicHolidaysElement = document.getElementById('publicHolidays');

                totalDaysElement.textContent = summary.totalDays;
                workingDaysPassedElement.textContent = summary.workingDaysPassed;
                workingHoursPassedElement.textContent = summary.workingHoursPassed;
                workingHoursWorkedElement.textContent = summary.workingHoursWorked;
                publicHolidaysElement.textContent = summary.publicHolidays;
            }

            // Populate table
            function updateTable(data) {
                let tableBody = document.querySelector('table tbody');
                tableBody.innerHTML = '';

                if (data.length === 0 || data.every(employee => employee.totalTime === '00:00' && employee
                        .requiredTime === '00:00')) {
                    displayNoAttendanceDataMessage();
                } else {
                    if (data.length > 0) {
                        data.forEach(function(employee) {
                            let row = `<tr class="mt-2">
                                <td>${employee.date}</td>
                                <td class="text-center">${employee.checkIn}</td>
                                <td class="text-center">${employee.checkOut}</td>
                                <td class="text-center">${employee.requiredTime}</td>
                                <td class="text-center">${employee.totalTime}</td>
                                <td class="text-center">${employee.adjustmentType}${employee.adjustmentValue}</td>
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

            // Update cards when no data message is displayed
            function displayNoDataMessage() {
                const cardSections = document.querySelectorAll(
                    '.card.border-left-primary, .card.border-left-success, .card.border-left-danger');
                cardSections.forEach(section => {
                    const cardBody = section.querySelector('.card-body .h5');
                    cardBody.textContent = '0';
                });
                displayNoAttendanceDataMessage(); // Display no data message in the table
            }

            // Update cards and table when there's no data
            function displayNoAttendanceDataMessage() {
                let tableBody = document.querySelector('table tbody');
                tableBody.innerHTML =
                    '<tr><td colspan="7" class="text-center">No attendance data available for the selected month.</td></tr>';
                // Reset card values to zero
                const cardSections = document.querySelectorAll(
                    '.card.border-left-primary, .card.border-left-success, .card.border-left-danger');
                cardSections.forEach(section => {
                    const cardBody = section.querySelector('.card-body .h5');
                    cardBody.textContent = '0';
                });
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
