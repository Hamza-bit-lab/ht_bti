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
                <h6 class="m-0 font-weight-bold text-primary text-center">Edit Interview</h6>
            </div>
            <div class="card-body">
                <div class="container mt-lg-5 text-dark">

                    <form action="{{ route('update-interview', [$interview->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="title" class="col-sm-2 col-form-label">Interview Title</label>
                            <div class="col-sm-10">
                                <input type="text" name="title" value="{{ $interview->title }}" required
                                    class="form-control" id="title">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="name" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ $interview->name }}" name="name" required
                                    class="form-control" id="name">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" value="{{ $interview->email }}" required
                                    class="form-control" id="email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                            <div class="col-sm-10">
                                <input type="number" name="phone" value="{{ $interview->phone }}" required
                                    class="form-control" id="phone">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="c_salary" class="col-sm-2 col-form-label">Current Salary</label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ $interview->c_salary }}" name="c_salary" required
                                    class="form-control" id="c_salary">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="e_salary" class="col-sm-2 col-form-label">Expected Salary</label>
                            <div class="col-sm-10">
                                <input type="number" value="{{ $interview->e_salary }}" name="e_salary" required
                                    class="form-control" id="e_salary">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="notice_period" class="col-sm-2 col-form-label">Notice Period</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ $interview->notice_period }}" name="notice_period" required
                                    class="form-control" id="notice_period">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="date" class="col-sm-2 col-form-label">Date & Time</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" value="{{ $interview->getAttributes()['date'] }}" name="date" required
                                class="form-control" id="date">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="document" class="col-sm-2 col-form-label">Document</label>
                            <div class="col-sm-10">
                                <input type="file" name="document" class="form-control" id="document">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="interviewer" class="col-sm-2 col-form-label">Interviewer</label>
                            <div class="col-sm-10">
                                <select name="interviewer" required class="form-control" id="interviewer">
                                    @foreach($interviewers as $interviewer)
                                        <option value="{{ $interviewer->id }}"
                                            {{ $interview->interviewer == $interviewer->id ? 'selected' : '' }}>
                                            {{ $interviewer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 form-floating justify-content-end">
                            <label class="col-sm-2 col-form-label" for="interviewer_comments">Interviewer Comments</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="interviewer_comments" placeholder="Leave a comment here" id="description"
                                    style="height: 100px">{{ $interview->interviewer_comments }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3 form-floating justify-content-end">
                            <label class="col-sm-2 col-form-label" for="hr_comments">HR Comments</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="hr_comments" placeholder="Leave a comment here" id="description"
                                    style="height: 100px">{{ $interview->hr_comments }}</textarea>
                            </div>
                        </div>

                        <fieldset class="row my-3">
                            <legend class="col-form-label col-sm-2 pt-0">Select Interview Type</legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input physical_int"
                                        {{ $interview->type == 'physical' ? 'checked' : '' }} type="radio"
                                        name="type" id="gridRadios2" value="physical">
                                    <label class="form-check-label " for="gridRadios2">
                                        Physical Interview
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input online_int"
                                        {{ $interview->type == 'online' ? 'checked' : '' }} type="radio" name="type"
                                        id="gridRadios1" value="online">
                                    <label class="form-check-label" for="gridRadios1">
                                        Online Interview
                                    </label>
                                </div>
                                <div class="online_int_link"
                                    style="{{ $interview->type != 'online' ? 'display:none' : '' }}">
                                    <input type="text" class="form-control my-2"
                                        value="{{ $interview->meeting_url }}" name="meeting_url"
                                        placeholder="Meeting Link">
                                </div>
                            </div>
                        </fieldset>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <select name="status" id="" class="form-control">
                                    @foreach ((InterviewStatus::STATUSES) as $status)
                                        <option value="{{ $status }}" {{ $interview->status == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
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
    <script>
        $(document).ready(function(params) {
            $('.online_int').click(function(params) {
                $(".online_int_link").css("display", "block");
            })
            $('.physical_int').click(function(params) {
                $(".online_int_link").css("display", "none");
            })
        })
    </script>
@endsection
