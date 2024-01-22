@extends('layouts.sidebar')

@section('create-employee-selected', 'active')

@section('title', 'Create Employee')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Add Employee</h6>
            </div>
            <div class="card-body">
                <div class="container mt-lg-5 text-dark">

                    <form action="{{ route('save-employee') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" required class="form-control" id="name"
                                    value="{{ old('name') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="attendance_id" class="col-sm-2 col-form-label">Attendance ID</label>
                            <div class="col-sm-10">
                                <input type="number" name="attendance_id" required class="form-control" id="attendance_id"
                                    value="{{ old('attendance_id') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="position" class="col-sm-2 col-form-label">Position</label>
                            <div class="col-sm-10">
                                <input type="text" name="position" required class="form-control" id="position"
                                    value="{{ old('position') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" required class="form-control" id="email"
                                    value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                            <div class="col-sm-10">
                                <input type="number" name="phone" required class="form-control" id="phone"
                                    value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="salary" class="col-sm-2 col-form-label">Salary</label>
                            <div class="col-sm-10">
                                <input type="number" name="salary" required class="form-control" id="salary"
                                    value="{{ old('salary') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="degree" class="col-sm-2 col-form-label">Degree</label>
                            <div class="col-sm-10">
                                <input type="text" name="degree" required class="form-control" id="degree"
                                    value="{{ old('degree') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="joining_date" class="col-sm-2 col-form-label">Joining Date</label>
                            <div class="col-sm-10">
                                <input type="date" name="joining_date" value="{{ now()->format('Y-m-d') }}" required
                                    class="form-control" id="joining_date" value="{{ old('joining_date') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="birthday" class="col-sm-2 col-form-label">Birthday</label>
                            <div class="col-sm-10">
                                <input type="date" name="birthday" value="{{ now()->format('Y-m-d') }}" required
                                       class="form-control" id="birthday" value="{{ old('birthday') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="contract_end_date" class="col-sm-2 col-form-label">Contract Expiry Date</label>
                            <div class="col-sm-10">
                                <input type="date" name="contract_end_date" value="{{ now()->format('Y-m-d') }}" required
                                       class="form-control" id="contract_end_date" value="{{ old('contract_end_date') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">

                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary submit_btn float-right">Submit</button>

                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    @if ($errors->has('attendance_id'))
        <script>
            swal({
                text: '{{ $errors->first('attendance_id') }}',
                icon: 'error',
            });
        </script>
    @endif
@endsection
