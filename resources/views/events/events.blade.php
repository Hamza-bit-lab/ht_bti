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

                // When an Event is Clicked
                eventClick: function (info) {
                    // Set values in the edit modal for editing
                    console.log('Event clicked:', info.event);

                    var title = info.event.title;
                    var description = info.event.extendedProps ? info.event.extendedProps.description || '' : '';
                    console.log('Title:', title);
                    console.log('Description:', description);

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
                        // Perform AJAX request to update the event
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
                                // Add any other form fields you need
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

                    // Add a delete button in the edit modal
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
                                // Perform AJAX request to delete the event
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

                // New Event
                select: function (info) {
                    // Open the modal when selecting a date
                    $('#eventModal').modal('show');

                    $('#saveEventBtn').off('click').on('click', function () {
                        var title = $('#eventTitle').val();
                        var description = $('#eventDescription').val();

                        // Validate if title is not empty
                        if (!title) {
                            // You can show an alert or handle the validation as needed
                            alert('Please enter a title for the event.');
                            return;
                        }

                        // Perform AJAX request to save the event
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
                                // Add any other form fields you need
                            },
                            success: function (response) {
                                calendar.refetchEvents();
                                $('#eventModal').modal('hide'); // Hide the modal after success
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


