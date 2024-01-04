@extends('layouts.sidebar')

@section('office-hours-selected', 'active')

@section('title', 'Office Hours')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Office Hours</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="editDataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Starting</th>
                                <th>Break Starting</th>
                                <th>Break Closing</th>
                                <th>Closing</th>
                                <th class="text-center">ON/OFF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($officeHours as $day)
                                <tr>
                                    <td>{{ $day->day }}</td>
                                    <input type="hidden" name="office_hours[{{ $loop->index }}][day]"
                                        value="{{ $day->day }}">
                                    <td>
                                        <input type="time" name="starting_time" value="{{ $day->starting_time }}"
                                            required class="form-control" {{ $day->visible }}>
                                    </td>
                                    <td>
                                        <input type="time" name="break_starting" value="{{ $day->break_starting }}"
                                            required class="form-control" {{ $day->visible }}>
                                    </td>
                                    <td>
                                        <input type="time" name="break_ending" value="{{ $day->break_ending }}" required
                                            class="form-control" {{ $day->visible }}>
                                    </td>
                                    <td>
                                        <input type="time" name="closing_time" value="{{ $day->closing_time }}" required
                                            class="form-control" {{ $day->visible }}>
                                    </td>
                                    <input type="hidden" name="office_hours[{{ $loop->index }}][isOn]" value="true">
                                    <td class="text-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-switch" type="checkbox"
                                                id="toggle-{{ $day->id }}" data-id="{{ $day->id }}"
                                                {{ $day->isON ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Time Updating
            $('input[type="time"]').on('blur', function() {
                var dayIndex = $(this).closest('tr').index();
                var fieldName = $(this).attr('name');
                var fieldValue = $(this).val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('update-office-hours') }}",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'day': dayIndex,
                        'field': fieldName,
                        'value': fieldValue,
                    },
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: " ",
                                icon: "success",
                                timer: 1000,
                                buttons: false,
                            });
                        }
                    }
                });
            });

            // Toggle Updating
            $('.toggle-switch').change(function() {
                var dayId = $(this).data('id');
                var isChecked = $(this).is(':checked');
                var row = $(this).closest('tr');
                if (!isChecked) {
                    row.find('input[type="time"]').prop('disabled', true);
                    row.find('input[name^="office_hours"]').filter('[name*="isOn"]').val(false);
                } else {
                    row.find('input[type="time"]').prop('disabled', false);
                    row.find('input[name^="office_hours"]').filter('[name*="isOn"]').val(true);
                }
                $.ajax({
                    type: 'POST',
                    url: "{{ route('update-office-hours-status') }}",
                    data: {
                        dayId: dayId,
                        isON: isChecked,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: " ",
                                icon: "success",
                                timer: 1000,
                                buttons: false,
                            });
                        }
                    }
                });
            });

            // Enter press
            $('input[type="time"]').on('keyup', function(event) {
                if (event.key === "Enter") {
                    $(this).blur();
                }
            });
        });
    </script>
@endsection
