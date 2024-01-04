@extends('layouts.sidebar')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Edit Employee</h6>
            </div>
            <div class="card-body">
                <div class="container mt-lg-5 text-dark">

                    <form action="{{ route('update-employee', [$employee->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ $employee->name }}" name="name" required
                                    class="form-control" id="name">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="position" class="col-sm-2 col-form-label">Position</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ $employee->position }}" name="position" required
                                    class="form-control" id="position">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" value="{{ $employee->email }}" required
                                    class="form-control" id="email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                            <div class="col-sm-10">
                                <input type="number" name="phone" value="{{ $employee->phone }}" required
                                    class="form-control" id="phone">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="salary" class="col-sm-2 col-form-label">Salary</label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ $employee->salary }}" name="salary" required
                                    class="form-control" id="salary">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="degree" class="col-sm-2 col-form-label">Degree</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ $employee->degree }}" name="degree" required
                                    class="form-control" id="degree">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="joining_date" class="col-sm-2 col-form-label">Joining Date</label>
                            <div class="col-sm-10">
                                <input type="date" value="{{ Carbon::parse($employee->joining_date)->format('Y-m-d') }}" name="joining_date" required
                                class="form-control" id="joining_date">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="dob" class="col-sm-2 col-form-label">Birthday</label>
                            <div class="col-sm-10">
                                <input type="date" value="{{ Carbon::parse($employee->dob)->format('Y-m-d') }}" name="dob" required
                                       class="form-control" id="dob">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="contract_end_date" class="col-sm-2 col-form-label">Contract End Date</label>
                            <div class="col-sm-10">
                                <input type="date" value="{{ Carbon::parse($employee->contract_end_date)->format('Y-m-d') }}" name="contract_end_date" required
                                       class="form-control" id="contract_end_date">
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

@endsection
