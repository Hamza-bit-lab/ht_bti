@extends('layouts.sidebar')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="card m-auto" style="width: 75%;">
            <div class="card-header">
                <h2 class="text-center py-3">Employee Details</h2>
            </div>
            <div class="card-body p-lg-5 ">

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Name</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->name }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Attendance ID</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->attendance_id }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Position</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->position }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Email</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->email }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Phone</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->phone }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Degree</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->degree }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Joining Date</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->joining_date }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Salary</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->salary }}</h5>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Date of Birth</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->dob }}</h5>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Contract End Date</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $employee->contract_end_date }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
