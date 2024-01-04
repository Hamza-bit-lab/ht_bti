@extends('layouts.sidebar')

@section('create-interview-selected', 'active')

@section('title', 'Create Interview')

@section('css')
    <style>
        .online_int_link {
            display: none;
        }
        #loading-message {
            font-size: 10px;
            color: #4caf50;
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-center">Create Interview</h6>
            </div>
            <div class="card-body">
                <div class="container mt-lg-5 text-dark">

                    <form action="{{ route('save-interview') }}" method="post" enctype="multipart/form-data"  onsubmit="showLoadingMessage()">
                        @csrf

                        <div class="row mb-3">
                            <label for="title" class="col-sm-2 col-form-label">Interview Title</label>
                            <div class="col-sm-10">
                                <input type="text" name="title" required class="form-control" id="title">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" required class="form-control" id="name">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" required class="form-control" id="email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                            <div class="col-sm-10">
                                <input type="number" name="phone" required class="form-control" id="phone">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="c_salary" class="col-sm-2 col-form-label">Current Salary</label>
                            <div class="col-sm-10">
                                <input type="number" name="c_salary" required class="form-control" id="c_salary">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="e_salary" class="col-sm-2 col-form-label">Expected Salary</label>
                            <div class="col-sm-10">
                                <input type="number" name="e_salary" required class="form-control" id="e_salary">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="notice_period" class="col-sm-2 col-form-label">Notice Period</label>
                            <div class="col-sm-10">
                                <input type="text" name="notice_period" required class="form-control" id="notice_period">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="date" class="col-sm-2 col-form-label">Date & Time</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" name="date" required class="form-control" id="date">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="document" class="col-sm-2 col-form-label">Document</label>
                            <div class="col-sm-10">
                                <input type="file" name="document" required class="form-control" id="document">
                            </div>
                        </div>

                        <div class="row mb-3 form-floating justify-content-end">
                            <label class="col-sm-2 col-form-label" for="hr_comments">HR Comments</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="hr_comments" placeholder="Leave a comment here" id="description"
                                    style="height: 100px"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="interviewer" class="col-sm-2 col-form-label">Interviewer</label>
                            <div class="col-sm-10">
                                <select name="interviewer" required class="form-control" id="interviewer">
                                    <option value="" disabled selected>Select Interviewer</option>
                                    @foreach($interviewers as $interviewer)
                                        <option value="{{ $interviewer->id }}">{{ $interviewer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <fieldset class="row my-3">
                            <legend class="col-form-label col-sm-2 pt-0">Select Interview Type</legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input physical_int" checked type="radio" name="type"
                                        id="physical" value="physical">
                                    <label class="form-check-label " for="physical">
                                        Physical Interview
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input online_int" type="radio" name="type"
                                        id="online" value="online">
                                    <label class="form-check-label" for="online">
                                        Online Interview
                                    </label>
                                </div>
                                <div class="online_int_link">
                                    <input type="text" class="form-control my-2" name="meeting_url"
                                        placeholder="Meeting Link">
                                </div>
                            </div>
                        </fieldset>
                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">

                            </div>
                        </div>
                        <button type="submit" id="submitBtn" class="btn btn-primary submit_btn float-right">Submit</button>
                        <div id="loading-message" class="float-right mt-3" style="display: none;">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(params) {
            $('.online_int').click(function(params) {
                $(".online_int_link").css("display", "block");
            })
            $('.physical_int').click(function(params) {
                $(".online_int_link").css("display", "none");
            })
        });

        function showLoadingMessage() {
            // Display the loader (spinner) and hide the submit button
            document.getElementById('loading-message').style.display = 'block';
            document.getElementById('submitBtn').style.display = 'none';
        }
    </script>
@endsection
