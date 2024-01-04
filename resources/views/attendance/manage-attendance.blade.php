@extends('layouts.sidebar')

@section('manage-attendance-selected', 'active')

@section('title', 'Manage Attendance')

@section('css')
    <style>
        .delete-button {
            background-color: #d33;
            color: white;
        }

        .fc-col-header-cell-cushion,
        .fc-daygrid-day-number {
            color: black;
            text-decoration: none;
        }

        .fc-col-header-cell-cushion:hover,
        .fc-daygrid-day-number:hover {
            color: black;
            cursor: default;
            text-decoration: none;
        }

        .fc-daygrid-day-frame:hover {
            background-color: rgb(240, 240, 240);
            cursor: pointer;
        }

        .fc-event {
            background-color: rgb(70, 120, 255);
        }

        .fc-event:hover {
            background-color: rgb(55, 105, 240);
            cursor: pointer;
        }

        .fc-event-title {
            margin-left: 10px;
        }

        .fc-event-title:hover {
            cursor: pointer;
        }

        .fc-h-event {
            border: none;
            padding: 3px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="attendanceForm" action="{{ route('update-attendance') }}" method="POST">
            @csrf
            <div class="card shadow mb-4" id="calenderSection">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">Manage Attendance</h6>
                </div>
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
            <div id="buttons" style="display: none;">
                <button id="backButton" type="button" class="btn btn-secondary mb-3">Back</button>
                <button id="saveButton" type="submit" class="btn btn-primary float-end mb-3">Save</button>
            </div>
            <div id="contentsSection" style="display: none;">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="form-group">
                            <input type="date" class="form-control" id="lockedDate">
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="startingTime">Office Starting</label>
                                <input type="text" class="form-control" id="startingTime" disabled>
                            </div>
                            <div class="col">
                                <label for="breakStarting">Break Starting</label>
                                <input type="text" class="form-control" id="breakStarting" disabled>
                            </div>
                            <div class="col">
                                <label for="breakEnding">Break Closing</label>
                                <input type="text" class="form-control" id="breakEnding" disabled>
                            </div>
                            <div class="col">
                                <label for="closingTime">Office Closing</label>
                                <input type="text" class="form-control" id="closingTime" disabled>
                            </div>
                            <div class="col">
                                <label for="officeStart">Working Hours</label>
                                <input type="text" class="form-control" id="displayRequiredTime" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow my-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary text-center">Attendance</h6>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>First Check-In</th>
                                    <th>Last Check-Out</th>
                                    <th>Minute Adjustment</th>
                                    <th></th>
                                    <th>Total Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="dataBody">
                                <!-- Attendance data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        var originalDate = '';

        // View calender
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('{{ route('get-attendance-event') }}')
                        .then(response => response.json())
                        .then(data => {
                            const events = data.reduce((acc, entry) => {
                                const date = entry.date;
                                if (!acc[date]) {
                                    acc[date] = {
                                        date: date,
                                        events: []
                                    };
                                }
                                acc[date].events.push(entry);
                                return acc;
                            }, {});

                            const transformedEvents = Object.values(events).map(group => {
                                return {
                                    title: 'Manage Attendance',
                                    start: group.date,
                                    allDay: true
                                };
                            });
                            successCallback(transformedEvents);
                        })
                        .catch(error => {
                            console.error('Error fetching attendance data:', error);
                            failureCallback(error);
                        });
                },
                headerToolbar: {
                    start: 'prev',
                    center: 'title',
                    end: 'next'
                },
                initialView: 'dayGridMonth',
                firstDay: 1,
                selectable: true,
                height: '675px',
                timeZone: 'Asia/Karachi',
                eventClick: function(info) {
                    document.getElementById('calenderSection').style.display = 'none';

                    var contentsSection = document.getElementById('contentsSection');
                    contentsSection.style.display = 'block';

                    var buttons = document.getElementById('buttons');
                    buttons.style.display = 'block';

                    var eventDate = info.event.start;

                    var formattedDate = eventDate.toISOString().split('T')[0];

                    originalDate = formattedDate;

                    var lockedDateInput = document.getElementById('lockedDate');
                    lockedDateInput.value = formattedDate;

                    var previousDate = lockedDateInput.value;

                    // Set the previous date as a data attribute
                    lockedDateInput.dataset.previousDate = previousDate;

                    $.ajax({
                        url: "{{ route('get-attendance') }}",
                        type: 'GET',
                        data: {
                            'date': formattedDate
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
                            if ('attendanceData' in response) {
                                swal({
                                    title: " ",
                                    text: "Data Found!",
                                    icon: "success",
                                    timer: 1000,
                                    buttons: false
                                });

                                let userDataBody = $('#dataBody');
                                userDataBody.empty();

                                const requiredTime = response.attendanceData[0]
                                    .requiredTime;

                                // Format the time to display as hh:mm
                                const formattedRequiredTime = requiredTime.slice(0,
                                    5); // Extracting hh:mm

                                // Update the input field value with the formatted requiredTime
                                $('#displayRequiredTime').val(formattedRequiredTime);

                                $.each(response.attendanceData, function(
                                    key, value) {

                                    let bgColorClass = value
                                        .totalTime >= value.requiredTime ?
                                        'bg-success' : 'bg-danger';

                                    let adjustmentValue = value.adjustmentValue ? value.adjustmentValue : '';
                                    let adjustmentType = value.adjustmentType ? value.adjustmentType : '';

                                    let minusBtnClass = 'btn btn-light';
                                    let plusBtnClass = 'btn btn-light';

                                    if (adjustmentValue > 0) {
                                        if (adjustmentType === '+') {
                                            plusBtnClass = 'btn btn-primary';
                                        } else if (adjustmentType === '-') {
                                            minusBtnClass = 'btn btn-primary';
                                        }
                                    }

                                    console.log("Adjustment: "+ adjustmentValue);
                                    console.log("plusBtnClass: "+ plusBtnClass);
                                    console.log("minusBtnClass: "+ minusBtnClass);

                                    let totalTime = formatTime(
                                        value.totalTime);

                                    let leaveChecked = value
                                        .status === 'leave' ?
                                        'checked' : '';

                                    let remoteChecked = value
                                        .status === 'remote' ?
                                        'checked' : '';


                                    let row = `<tr>

                                        <td class="align-middle">${value.attendance_id}</td>

                                        <td class="align-middle">${value.name}</td>

                                        <td class="align-middle"><input type="time" class="form-control" value="${value.checkIn ? value.checkIn : ''}" name="checkIn"></td>

                                        <td class="align-middle"><input type="time" class="form-control" value="${value.checkOut ? value.checkOut : ''}" name="checkOut"></td>

                                        <td class="align-middle" style="width: 200px;"><input type="number" class="form-control" name="adjustmentMinutes" value="${value.adjustmentValue}" min="0"></td>

                                        <td class="align-middle">
                                            <button  class="${minusBtnClass}" data-operation="-" id="minusButton">-</button>
                                            <button class="${plusBtnClass}" data-operation="+" id="plusButton">+</button>
                                        </td>

                                        <td class="align-middle" style="width: 200px;">
                                            <input type="text" class="text-white form-control ${bgColorClass}" value="${totalTime}" disabled name="totalTime">
                                        </td>

                                        <input type="hidden" class="form-control" value="${value.requiredTime}" disabled name="requiredTime">
                                        <input type="hidden" name="storedTotalTime" value="${value.totalTime}">
                                        <input type="hidden" name="date" value="${value.date}">

                                        <td>
                                            <div class="row">
                                                <div class="col-6 text-left">
                                                    <label>Leave</label>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <input type="checkbox" name="leave" class="form-check-input leave-checkbox" value="leave" ${leaveChecked}>
                                                </div>
                                                <div class="col-6 text-left">
                                                    <label>Remote</label>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <input type="checkbox" name="remote" class="form-check-input remote-checkbox" value="remote" ${remoteChecked}>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`;
                                    userDataBody.append(row);
                                });
                                $('#contentsSection').show();
                            } else {
                                $('#contentsSection').hide();
                                swal({
                                    title: "No data found!",
                                    text: " ",
                                    icon: "error",
                                    timer: 1000,
                                    buttons: false
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                    $.ajax({
                        url: "{{ route('get-working-schedule') }}",
                        type: 'GET',
                        data: {
                            'selectedDate': formattedDate,
                        },
                        success: function(scheduleResponse) {
                            // Populate schedule details in respective fields
                            $('#startingTime').val(scheduleResponse.startingTime);
                            $('#breakStarting').val(scheduleResponse.breakStarting);
                            $('#breakEnding').val(scheduleResponse.breakEnding);
                            $('#closingTime').val(scheduleResponse.closingTime);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                },
            });
            calendar.render();
        });

        // Back button
        document.addEventListener('DOMContentLoaded', function() {
            var backButton = document.getElementById('backButton');
            backButton.addEventListener('click',
                function() {
                    document.getElementById('contentsSection').style.display = 'none';
                    document.getElementById('buttons').style.display = 'none';
                    document.getElementById('calenderSection').style.display = 'block';
                });
        });

        // Changing Date
        $('#lockedDate').change(function() {
            let selectedDate = $(this).val();

            let previousDate = $(this).data('previous-date');
            $(this).data('previous-date', selectedDate);

            // Make an AJAX request to check attendance availability for the selected date
            $.ajax({
                url: "{{ route('check-attendance-availability') }}",
                type: 'GET',
                data: {
                    selectedDate: selectedDate
                },
                success: function(response) {
                    if (response.exists && selectedDate !== originalDate) {
                        $('#lockedDate').val(previousDate);
                        // Attendance for this date already exists
                        swal({
                            title: "",
                            text: "Attendance for this date already exists!",
                            icon: "error",
                        });
                    } else {
                        // Proceed to fetch working schedule based on the selected date
                        $.ajax({
                            url: "{{ route('get-working-schedule') }}",
                            type: 'GET',
                            data: {
                                selectedDate: selectedDate
                            },
                            success: function(scheduleResponse) {
                                $('#startingTime').val(scheduleResponse
                                    .startingTime);
                                $('#breakStarting').val(scheduleResponse
                                    .breakStarting);
                                $('#breakEnding').val(scheduleResponse
                                    .breakEnding);
                                $('#closingTime').val(scheduleResponse
                                    .closingTime);

                                // Fetch working hours and break time
                                $.when(
                                    $.ajax({
                                        url: "{{ route('get-working-hours') }}",
                                        type: 'GET',
                                        data: {
                                            selectedDate: selectedDate
                                        }
                                    }),
                                    $.ajax({
                                        url: "{{ route('get-break-time') }}",
                                        type: 'GET',
                                        data: {
                                            selectedDate: selectedDate
                                        }
                                    })
                                ).done(function(workingHoursResponse,
                                    breakTimeResponse) {
                                    let workingHours =
                                        workingHoursResponse[0]
                                        .workingHours;
                                    let breakTime = breakTimeResponse[0]
                                        .breakTime;

                                    $('input[name="requiredTime"]').val(
                                        workingHours);

                                    // Iterate through each row to update total time
                                    $('#dataBody').find('tr').each(
                                        function() {
                                            let row = $(this);
                                            let checkIn = row.find(
                                                'input[name="checkIn"]'
                                            ).val();
                                            let checkOut = row.find(
                                                'input[name="checkOut"]'
                                            ).val();

                                            let requiredTimeValue =
                                                $(
                                                    'input[name="requiredTime"]'
                                                )
                                                .first().val();
                                            $('#displayRequiredTime')
                                                .val(
                                                    requiredTimeValue
                                                );

                                            if (checkIn &&
                                                checkOut) {
                                                let checkInTime =
                                                    new Date(
                                                        "1970-01-01 " +
                                                        checkIn);
                                                let checkOutTime =
                                                    new Date(
                                                        "1970-01-01 " +
                                                        checkOut);

                                                let timeDifference =
                                                    checkOutTime -
                                                    checkInTime;

                                                let breakTimeParts =
                                                    breakTime.split(
                                                        ':');
                                                let breakTimeInMinutes =
                                                    parseInt(
                                                        breakTimeParts[
                                                            0]) *
                                                    60 + parseInt(
                                                        breakTimeParts[
                                                            1]);

                                                let totalTimeDifference =
                                                    Math.abs(
                                                        timeDifference
                                                    ) / 1000 /
                                                    60;

                                                if (totalTimeDifference >=
                                                    breakTimeInMinutes
                                                ) {
                                                    totalTimeDifference
                                                        -=
                                                        breakTimeInMinutes;
                                                }

                                                let plusButton = row
                                                    .find(
                                                        '#plusButton'
                                                    );
                                                let minusButton =
                                                    row.find(
                                                        '#minusButton'
                                                    );

                                                let adjustment = 0;
                                                if (plusButton
                                                    .hasClass(
                                                        'btn-primary'
                                                    )) {
                                                    adjustment =
                                                        parseInt(row
                                                            .find(
                                                                'input[name="adjustmentMinutes"]'
                                                            )
                                                            .val()
                                                        ) || 0;
                                                } else if (
                                                    minusButton
                                                    .hasClass(
                                                        'btn-primary'
                                                    )) {
                                                    adjustment = -(
                                                        parseInt(
                                                            row
                                                            .find(
                                                                'input[name="adjustmentMinutes"]'
                                                            )
                                                            .val()
                                                        ) ||
                                                        0);
                                                }

                                                totalTimeDifference
                                                    += adjustment;

                                                let hours = Math
                                                    .floor(
                                                        totalTimeDifference /
                                                        60);
                                                let minutes =
                                                    totalTimeDifference %
                                                    60;

                                                let adjustedTotalTime =
                                                    pad(hours) +
                                                    ':' + pad(
                                                        minutes);
                                                row.find(
                                                    'input[name="totalTime"]'
                                                ).val(
                                                    adjustedTotalTime
                                                );

                                                let requiredTimeInSeconds =
                                                    timeToSeconds(
                                                        workingHours
                                                    );
                                                let totalMinutes =
                                                    totalTimeDifference;
                                                let requiredMinutes =
                                                    requiredTimeInSeconds /
                                                    60;

                                                let bgColorClass =
                                                    totalMinutes >=
                                                    requiredMinutes ?
                                                    'bg-success' :
                                                    'bg-danger';
                                                row.find(
                                                        'input[name="totalTime"]'
                                                    )
                                                    .removeClass(
                                                        'bg-success bg-danger'
                                                    ).addClass(
                                                        bgColorClass
                                                    );
                                            }
                                        });
                                }).fail(function(jqXHR, textStatus,
                                    errorThrown) {
                                    console.error(errorThrown);
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        // Form saving
        $('#attendanceForm').submit(function(event) {
            event.preventDefault(); // Prevents the default form submission

            // Gather table data
            let formData = [];
            $('#dataBody tr').each(function() {
                let leaveCheckbox = $(this).find('.leave-checkbox');
                let remoteCheckbox = $(this).find('.remote-checkbox');

                let adjustmentMinutes = $(this).find('input[name="adjustmentMinutes"]').val();
                let adjustmentType = $(this).find('#minusButton').hasClass('btn-primary') ? '-' : '+';

                let selectedStatus = '';
                if (leaveCheckbox.is(':checked')) {
                    selectedStatus = 'leave';
                } else if (remoteCheckbox.is(':checked')) {
                    selectedStatus = 'remote';
                } else {
                    selectedStatus = 'present';
                }
                let rowData = {
                    'attendance_id': $(this).find('td:eq(0)').text(),
                    'name': $(this).find('td:eq(1)').text(),
                    'date': $(this).find('input[name="date"]').val(),
                    'updateDate': $('#lockedDate').val(),
                    'checkIn': $(this).find('input[name="checkIn"]').val(),
                    'checkOut': $(this).find('input[name="checkOut"]').val(),
                    'adjustmentValue': adjustmentMinutes,
                    'adjustmentType': adjustmentType,
                    'totalTime': $(this).find('input[name="totalTime"]').val(),
                    'requiredTime': $(this).find('input[name="requiredTime"]').val(),
                    'status': selectedStatus,
                };
                formData.push(rowData);
            });

            // Append table data as JSON to a hidden input field
            $('<input>').attr({
                type: 'hidden',
                name: 'attendanceData',
                value: JSON.stringify(formData)
            }).appendTo('#attendanceForm');

            // Submit the form
            this.submit();
        });

        // Checkboxes operation
        $('body').on('change', '.leave-checkbox, .remote-checkbox', function() {
            let checkboxes = $(this).closest('tr').find('.leave-checkbox, .remote-checkbox');
            checkboxes.not(this).prop('checked', false);
        });

        // Data Operations
        $('body').on('click', 'button[data-operation]', function(event) {
            event.preventDefault(); // Prevents the default form submission

            let row = $(this).closest('tr');
            let adjustmentMinutes = parseInt(row.find('input[name="adjustmentMinutes"]').val()) || 0;
            let operation = $(this).data('operation');

            let plusButton = row.find('#plusButton');
            let minusButton = row.find('#minusButton');

            plusButton.removeClass('btn-light btn-primary');
            minusButton.removeClass('btn-light btn-primary');

            if (operation === '+') {
                plusButton.addClass('btn-primary');
                minusButton.addClass('btn-light');
            } else if (operation === '-') {
                plusButton.addClass('btn-light');
                minusButton.addClass('btn-primary');
            }

            let totalTimeField = row.find('input[name="totalTime"]');
            let checkIn = row.find('input[name="checkIn"]').val();
            let checkOut = row.find('input[name="checkOut"]').val();

            if (checkIn && checkOut) {
                let checkInTime = new Date("1970-01-01 " + checkIn);
                let checkOutTime = new Date("1970-01-01 " + checkOut);

                let timeDifference = checkOutTime - checkInTime;

                // Fetch break time for the selected date
                let selectedDate = $('#lockedDate').val();
                $.ajax({
                    url: "{{ route('get-break-time') }}",
                    type: 'GET',
                    data: {
                        selectedDate: selectedDate
                    },
                    success: function(response) {
                        let breakTime = response.breakTime;
                        let breakTimeParts = breakTime.split(':');
                        let breakTimeInMinutes = parseInt(breakTimeParts[0]) * 60 +
                            parseInt(breakTimeParts[1]);

                        let totalTimeDifference = Math.abs(timeDifference) / 1000 / 60;
                        let requiredTime = row.find('input[name="requiredTime"]').val();
                        let requiredTimeInSeconds = timeToSeconds(requiredTime);
                        let requiredMinutes = requiredTimeInSeconds / 60;

                        // Calculate the condition threshold based on half of the required time plus break time
                        let conditionThreshold = (requiredMinutes / 2) + breakTimeInMinutes;

                        if (totalTimeDifference < conditionThreshold) {
                            // If the condition is met, set break time to zero
                            breakTimeInMinutes = 0;
                        }

                        // Subtract break time from total time difference in minutes
                        totalTimeDifference -= breakTimeInMinutes;

                        // Adjust total time based on adjustmentMinutes and operation (+/-)
                        if (operation == '+') {
                            totalTimeDifference += adjustmentMinutes;
                        } else if (operation == '-') {
                            totalTimeDifference -= adjustmentMinutes;
                        }

                        // Set total time value in HH:mm format
                        let hours = Math.floor(totalTimeDifference / 60);
                        let minutes = totalTimeDifference % 60;

                        let adjustedTotalTime = pad(hours) + ':' + pad(minutes);
                        totalTimeField.val(adjustedTotalTime);

                        let totalMinutes = totalTimeDifference;
                        let bgColorClass = totalMinutes >= requiredMinutes ? 'bg-success' :
                            'bg-danger';
                        totalTimeField.removeClass('bg-success bg-danger').addClass(
                            bgColorClass);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        // CheckIn & CheckOut Changing
        $('#dataBody').on('change', 'input[name="checkIn"], input[name="checkOut"]', function() {
            let row = $(this).closest('tr');
            let checkIn = row.find('input[name="checkIn"]').val();
            let checkOut = row.find('input[name="checkOut"]').val();

            if (checkIn && checkOut) {
                let checkInTime = new Date("1970-01-01 " + checkIn);
                let checkOutTime = new Date("1970-01-01 " + checkOut);

                let timeDifference = checkOutTime - checkInTime;

                // Fetch break time for the selected date
                let selectedDate = $('#lockedDate').val();
                $.ajax({
                    url: "{{ route('get-break-time') }}",
                    type: 'GET',
                    data: {
                        selectedDate: selectedDate
                    },
                    success: function(response) {
                        let breakTime = response.breakTime;
                        let breakTimeParts = breakTime.split(':');
                        let breakTimeInMinutes = parseInt(breakTimeParts[0]) * 60 +
                            parseInt(breakTimeParts[1]);

                        // Fetch required time
                        let requiredTime = timeToSeconds(row.find(
                            'input[name="requiredTime"]').val()) / 60;

                        // Condition to check if break time should be subtracted or not
                        if (Math.abs(timeDifference) / 1000 / 60 < (requiredTime / 2 +
                                breakTimeInMinutes)) {
                            breakTimeInMinutes =
                                0; // If condition meets, set break time to zero
                        }

                        // Fetch adjustment minutes based on button class
                        let adjustment = 0;
                        let plusButton = row.find('#plusButton');
                        let minusButton = row.find('#minusButton');

                        if (plusButton.hasClass('btn-primary')) {
                            adjustment = parseInt(row.find(
                                'input[name="adjustmentMinutes"]').val()) || 0;
                        } else if (minusButton.hasClass('btn-primary')) {
                            adjustment = -(parseInt(row.find(
                                'input[name="adjustmentMinutes"]').val()) || 0);
                        }

                        // Subtract break time and apply adjustment to the total time difference in minutes
                        let totalTimeDifference = Math.abs(timeDifference) / 1000 / 60 -
                            breakTimeInMinutes + adjustment;

                        // Set total time value in HH:mm format
                        let hours = Math.floor(totalTimeDifference / 60);
                        let minutes = totalTimeDifference % 60;

                        row.find('input[name="totalTime"]').val(pad(hours) + ':' + pad(
                            minutes));

                        // No need to modify adjustment or buttons' classes here

                        // Update background color based on total time
                        $('#dataBody').find('tr').each(function() {
                            let totalField = $(this).find(
                                'input[name="totalTime"]');
                            let requiredTime = timeToSeconds($(this).find(
                                'input[name="requiredTime"]').val()) / 60;
                            let totalMinutes = timeToSeconds(totalField.val()) / 60;

                            let bgColorClass = totalMinutes >= requiredTime ?
                                'bg-success' : 'bg-danger';

                            totalField.removeClass('bg-success bg-danger').addClass(
                                bgColorClass);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        // Function to convert time to seconds
        function timeToSeconds(timeString) {
            let timeParts = timeString.split(':');
            return (+timeParts[0]) * 3600 + (+timeParts[1]) * 60 + (+timeParts[2] || 0);
        }

        // Convert seconds to time format (HH:mm:ss)
        function secondsToTime(seconds) {
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds % 3600) / 60);
            let remainingSeconds = seconds % 60;

            // return `${pad(hours)}:${pad(minutes)}:${pad(remainingSeconds)}`;
            return `${pad(hours)}:${pad(minutes)}`;
        }

        // Pad single digit with leading zero
        function pad(number) {
            return (number < 10 ? '0' : '') + number;
        }

        // Change total time from 00:00:00 to 00:00
        function formatTime(time) {
            if (!time) return '00:00';

            let parts = time.split(':');
            let hours = parseInt(parts[0]);
            let minutes = parseInt(parts[1]);

            if (isNaN(hours) || isNaN(minutes)) return '00:00';

            let formattedHours = (hours < 10) ? `0${hours}` : hours;
            let formattedMinutes = (minutes < 10) ? `0${minutes}` : minutes;

            return `${formattedHours}:${formattedMinutes}`;
        }

        // Enter Press Prevent
        $(document).on('keypress', function(event) {
            if (event.which === 13) {
                event.preventDefault();
                return false;
            }
        });

        // Alerts
        document.addEventListener('DOMContentLoaded', function() {
            let successMessage = "{{ session('success') }}";
            if (successMessage) {
                swal({
                    title: "Updated Successfully!",
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
