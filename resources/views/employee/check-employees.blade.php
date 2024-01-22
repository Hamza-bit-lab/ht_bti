<!-- resources/views/employee/check-employees.blade.php -->

@extends('layouts.sidebar')

@section('check-employees-selected', 'active')

@section('title', 'Employee Events')

@section('css')
    <style>
        .indications {
            display: flex;
            margin-right: 10px;
        }

        .color-container {
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        .color-item {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .color-name {
            font-size: 14px;
            color: #555;
            margin-right: 10px; /* Add right margin for spacing */
        }



        .fc-event {
            background-color: rgb(70, 120, 255);
            border: 1px solid rgb(50, 100, 200);
            color: white;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        /*.fc-event:hover {*/
        /*    background-color: rgb(55, 105, 240);*/
        /*    cursor: pointer;*/
        /*}*/

        .fc-event-title {
            margin-left: 10px;
        }

        /*.fc-event-title:hover {*/
        /*    cursor: pointer;*/
        /*}*/

        .fc-h-event {
            border: none;
            padding: 3px;
        }
        .delete-button {
            background-color: #d33;
            color: white;
        }

        .fc-col-header-cell-cushion,
        .fc-daygrid-day-number {
            color: black;
            text-decoration: none;
        }



        .fc-event:hover {
            background-color: rgb(55, 105, 240);
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
        {{-- Filters --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Filters</h6>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-lg-4">
                        <label for="filter">Filter:</label>
                        <div class="input-group">
                            <select id="filter" class="form-select" onchange="changeFilter()">
                                <option value="joining_date">Joining Date</option>
                                <option value="birthday">Birthday</option>
                                <option value="anniversary">Anniversary</option>
                                <option value="contract_end_date">Contract</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" id="clearFilter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="monthFilter">Month:</label>
                        <div class="input-group">
                            <select id="monthFilter" class="form-select" onchange="changeMonth()">
                                @foreach ($months as $monthNumber => $monthName)
                                    <option value="{{ $monthNumber }}">{{ $monthName }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" id="clearMonthFilter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="yearFilter">Year:</label>
                        <div class="input-group">
                            <select id="yearFilter" class="form-select" onchange="changeYear()">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year == 2024 ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>

                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" id="clearYearFilter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-lg-2 d-flex align-items-end justify-content-end">
                        <button id="searchButton" class="btn btn-primary btn-block" onclick="searchEmployees()">Search</button>
                    </div>
                    <div class="form-group col-lg-2 d-flex align-items-end justify-content-end">
                        <button id="resetButton" class="btn btn-danger btn-block" onclick="resetFilters()">Reset</button>
                    </div>
                </div>
                <div class="indications">
                    <div class="color-container">
                        <div class="color-item" style="background-color: #FFA500;"></div>
                        <div class="color-name">Joining Date</div>
                    </div>

                    <div class="color-container">
                        <div class="color-item" style="background-color: #4678FF;"></div>
                        <div class="color-name">Birthday</div>
                    </div>

                    <div class="color-container">
                        <div class="color-item" style="background-color: #A020F0;"></div>
                        <div class="color-name">Contract End Date</div>
                    </div>

                    <div class="color-container">
                        <div class="color-item" style="background-color: #008000;"></div>
                        <div class="color-name">Anniversary</div>
                    </div>
                </div>

            </div>
        </div>
        {{-- Calendar --}}
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Employee's Event</h6>
            </div>
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        var currentFilter = 'joining_date';
        var calendar;
        var selectedYear;

        function changeFilter() {
            var newFilter = document.getElementById('filter').value;
            // Update the current filter
            if (newFilter !== currentFilter) {
                currentFilter = newFilter;
            }
        }
        $(document).ready(function () {
            initializeCalendar(true);
        });

        function searchEmployees() {
            initializeCalendar(true);
        }

        function resetFilters() {
            document.getElementById('filter').value = 'joining_date';
            document.getElementById('monthFilter').value = new Date().getMonth() + 1;
            document.getElementById('yearFilter').value = new Date().getFullYear();

            currentFilter = 'joining_date';

            initializeCalendar(true);
        }
        function initializeCalendar(fetchEvents) {
            var selectedMonth = document.getElementById('monthFilter').value;
            selectedYear = document.getElementById('yearFilter').value;
            if (calendar) {
                calendar.destroy();
            }
            var calendarEl = document.getElementById('calendar');
            if (fetchEvents) {
                calendar = new FullCalendar.Calendar(calendarEl, {
                    events: function (fetchInfo, successCallback, failureCallback) {
                        $.ajax({
                            url: "{{ route('get-employee-events') }}?filter=" + currentFilter + "&month=" + selectedMonth + "&year=" + selectedYear,
                            type: 'GET',
                            success: function (response) {

                                if (!response || !response.events) {
                                    failureCallback(response);
                                    return;
                                }

                                var dynamicEvents;
                                if (currentFilter === 'joining_date') {
                                    dynamicEvents = response.events.map(function (event) {
                                        return {
                                            title: event.title,
                                            start: event.start,
                                            color: event.color,
                                        };
                                    });
                                }
                                else if (currentFilter === 'contract_end_date') {
                                    dynamicEvents = response.events.map(function (event) {
                                        return {
                                            title: event.title,
                                            start: event.start,
                                            color: event.color,
                                        };
                                    });
                                }
                                else if (currentFilter === 'birthday') {
                                    dynamicEvents = response.events.map(function (event) {
                                        var today = new Date();
                                        var eventDate = new Date(event.start);
                                        eventDate.setFullYear(today.getFullYear());
                                        return {
                                            title: event.title,
                                            start: eventDate,
                                            color: event.color,
                                        };
                                    });
                                } else if (currentFilter === 'anniversary') {
                                    dynamicEvents = response.events.map(function (event) {
                                        return {
                                            title: event.title,
                                            start: event.start,
                                            color: event.color,
                                        };
                                    });

                                }
                                successCallback(dynamicEvents);
                            },
                            error: function (response) {
                                failureCallback(response);
                            }
                        });
                    },
                    eventContent: function (arg) {
                        // Customize event content
                        var title = arg.event.title;
                        return { html: '<div style="color: white" class="fc-content">'  + title + '</div>' };
                    },
                    headerToolbar: {
                        start: 'prev,next today',
                        center: 'title',
                        end: 'dayGridMonth,timeGridWeek,timeGridDay,listYear'
                    },
                    firstDay: 1,
                    selectable: true,
                    height: '675px',
                    slotDuration: '24:00:00',
                });

                calendar.render();
                calendar.gotoDate(new Date(selectedYear, selectedMonth - 1, 1));
            } else {
                // Initialize calendar without fetching events
                calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        start: 'prev',
                        center: 'title',
                        end: 'next'
                    },
                    initialView: 'dayGridMonth',
                    firstDay: 1,
                    selectable: true,
                    height: '675px',
                });

                calendar.render();
            }
        }

        function changeYear() {
            // i will handel later
        }

        function changeMonth() {
            var selectedMonth = document.getElementById('monthFilter').value;
        }

        function isCurrentMonth(month) {
            var today = new Date();
            var currentMonth = today.getMonth() + 1;

            return parseInt(month) === currentMonth;
        }
    </script>
@endsection
