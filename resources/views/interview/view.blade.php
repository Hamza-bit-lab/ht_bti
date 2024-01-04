@extends('layouts.sidebar')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="card m-auto" style="width: 80%;">
            <div class="card-header">
                <h2 class="text-center py-3">Interview Details</h2>
            </div>
            <div class="card-body p-lg-5">

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Interview Title</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $interview->title }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Name</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $interview->name }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Email</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $interview->email }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Phone</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $interview->phone }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Current Salary</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $interview->c_salary }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Expected Salary</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $interview->e_salary }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Notice Period</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{ $interview->notice_period }}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">Interviewer</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{{$interviewer->name}}</h5>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-md-6 col-sm-6">
                        <h4 class="">HR Comments</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h5 class="">{!! $interview->hr_comments !!}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
