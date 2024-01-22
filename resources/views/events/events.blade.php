@extends('layouts.sidebar')

@section('events-selected', 'active')

@section('title', 'Events')

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
                <h6 class="m-0 font-weight-bold text-primary text-center">Events</h6>
            </div>
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
@endsection
@include('events.events-modal')
@include('events.edit-modal')

@section('js')


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                events: {
                    url: "{{ route('get-events') }}",
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

                eventClick: function (info) {
                    var title = info.event.title;
                    var description = info.event.extendedProps ? info.event.extendedProps.description || '' : '';

                    $('#editEventTitle').val(title);
                    tinymce.get('editEventDescription').setContent(description);
                    $('#editEventModal').modal('show');

                    $('#updateEventBtn').off('click').on('click', function () {
                        var title = $('#editEventTitle').val();
                        var description = $('#editEventDescription').val();

                        if (!title) {
                            swal({
                                title: "Please enter a title for the event.",
                                icon: "warning",
                                buttons: false,
                                timer: 1500
                            });
                            return;
                        }

                        var id = info.event.id;

                        $.ajax({
                            url: "{{ route('edit-event', '') }}/" + id,
                            type: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                title: title,
                                description: description,
                                start: info.event.startStr
                            },
                            success: function (response) {
                                calendar.refetchEvents();
                                $('#editEventModal').modal('hide');
                                swal({
                                    title: "Event Updated!",
                                    text: " ",
                                    icon: "success",
                                    timer: 1000,
                                    buttons: false
                                });
                            },
                            error: function (xhr, status, error) {
                                swal({
                                    title: "Failed to Update Event!",
                                    text: " ",
                                    icon: "error",
                                    timer: 1000,
                                    buttons: false
                                });
                            }
                        });
                    });

                    $('#deleteEventBtn').off('click').on('click', function () {
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
                                    url: "{{ route('delete-event', '') }}/" + id,
                                    type: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function (response) {
                                        calendar.refetchEvents();
                                        swal({
                                            title: "Event Deleted!",
                                            text: " ",
                                            icon: "success",
                                            timer: 1000,
                                            buttons: false
                                        });
                                        $('#editEventModal').modal('hide');
                                    },
                                    error: function (xhr, status, error) {
                                        swal({
                                            title: "Failed to Delete Event!",
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
                },

                select: function (info) {
                    $('#eventModal').modal('show');
                    $('#eventTitle').val('');
                    tinymce.get('eventDescription').setContent('');

                    $('#saveEventBtn').off('click').on('click', function () {
                        var title = $('#eventTitle').val();
                        var description = $('#eventDescription').val();

                        if (!title) {
                            alert('Please enter a title for the event.');
                            return;
                        }

                        $.ajax({
                            url: "{{ route('save-event') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                title: title,
                                description: description,
                                start: info.startStr
                            },
                            success: function (response) {
                                calendar.refetchEvents();
                                $('#eventModal').modal('hide');
                                swal({
                                    title: "Event Added!",
                                    text: " ",
                                    icon: "success",
                                    timer: 1000,
                                    buttons: false
                                });
                            },
                            error: function (xhr, status, error) {
                                swal({
                                    title: "Failed to Add Event!",
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


