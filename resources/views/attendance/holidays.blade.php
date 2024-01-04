@extends('layouts.sidebar')

@section('holidays-selected', 'active')

@section('title', 'Holidays')

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
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Holidays</h6>
            </div>
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                events: {
                    url: "{{ route('get-holidays') }}",
                    type: 'GET',
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

                // When a Holiday is Clicked
                eventClick: function(info) {
                    swal({
                        title: 'What do you want to do?',
                        text: 'Edit or Delete?',
                        buttons: {
                            edit: {
                                text: "Edit",
                                value: "edit",
                            },
                            delete: {
                                text: "Delete",
                                value: "delete",
                                className: "delete-button",
                            },
                        },
                        dangerMode: true,
                    }).then((value) => {
                        // Holiday Editing
                        if (value === "edit") {
                            swal({
                                text: 'New Holiday Name:',
                                content: {
                                    element: 'input',
                                    attributes: {
                                        value: info.event.title,
                                    },
                                },
                            }).then((title) => {
                                if (!title) return;
                                var id = info.event.id;
                                $.ajax({
                                    url: "{{ route('edit-holiday', '') }}/" + id,
                                    type: 'PUT',
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]').attr(
                                            'content')
                                    },
                                    data: {
                                        title: title,
                                        start: info.event.startStr
                                    },
                                    success: function(response) {
                                        calendar.refetchEvents();
                                        swal({
                                            title: "Holiday Updated!",
                                            text: " ",
                                            icon: "success",
                                            timer: 1000,
                                            buttons: false
                                        });
                                    },
                                    error: function(xhr, status, error) {
                                        swal({
                                            title: "Failed to Update Holiday!",
                                            text: " ",
                                            icon: "error",
                                            timer: 1000,
                                            buttons: false
                                        });
                                    }
                                });
                            });
                        }
                        // Holiday Deleting
                        else if (value === "delete") {
                            swal({
                                title: "Are you sure?",
                                text: "Once deleted, you will not be able to recover this record!",
                                icon: 'warning',
                                buttons: {
                                    confirm: "Yes",
                                    cancel: "No",
                                },
                                dangerMode: true,
                            }).then((confirmed) => {
                                if (confirmed) {
                                    var id = info.event.id;
                                    $.ajax({
                                        url: "{{ route('delete-holiday', '') }}/" + id,
                                        type: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': $(
                                                    'meta[name="csrf-token"]')
                                                .attr('content')
                                        },
                                        success: function(response) {
                                            calendar.refetchEvents();
                                            swal({
                                                title: "Holiday Deleted!",
                                                text: " ",
                                                icon: "success",
                                                timer: 1000,
                                                buttons: false
                                            });
                                        },
                                        error: function(xhr, status, error) {
                                            swal({
                                                title: "Failed to Delete Holiday!",
                                                text: " ",
                                                icon: "error",
                                                timer: 1000,
                                                buttons: false
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                },

                // New Holiday
                select: function(info) {
                    swal({
                        text: 'Holiday Name:',
                        content: 'input',
                    }).then((title) => {
                        if (!title) return;
                        $.ajax({
                            url: "{{ route('save-holiday') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                    .attr('content')
                            },
                            data: {
                                title: title,
                                start: info.startStr
                            },
                            success: function(response) {
                                calendar.refetchEvents();
                                swal({
                                    title: "Holiday Added!",
                                    text: " ",
                                    icon: "success",
                                    timer: 1000,
                                    buttons: false
                                });
                            },
                            error: function(xhr, status, error) {
                                swal({
                                    title: "Failed to Add Holiday!",
                                    text: " ",
                                    icon: "error",
                                    timer: 1000,
                                    buttons: false
                                });
                            }
                        });
                    });
                }
            });
            calendar.render();
        });
    </script>
@endsection
