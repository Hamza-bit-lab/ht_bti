<?php

namespace App\Http\Controllers;

use App\Interfaces\InterviewStatus;
use App\Interfaces\InterviewStatusMethods;
use App\Jobs\SendInterviewEmails;
use App\Jobs\SendInterviewReminderEmails;
use App\Mail\InterviewEmail;
use App\Models\Employee;
use App\Models\Interview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use App\Mail\InterviewMail;


class InterviewController extends Controller
{
    public function index($day)
    {
        if (!in_array($day, ['yesterday', 'today', 'tomorrow', 'all'])) {
            abort(404);
        }

        $interviewers = Interview::distinct()->pluck('interviewer');
        $employees = Employee::whereIn('id', $interviewers)->get();
        $statuses = InterviewStatus::STATUSES;

        return view('interview.interviews', compact('day', 'employees', 'statuses'));
    }

    public function getInterviews($day)
    {
        $interviews = Interview::query();
        $date = Carbon::now();

        if ($day === 'yesterday') {
            $date->subDay();
            $interviews->whereDate('date', $date)->orderBy('date', 'desc');
        } elseif ($day === 'today') {
            $interviews->whereDate('date', $date)->orderBy('date', 'asc');
        } elseif ($day === 'tomorrow') {
            $date->addDay();
            $interviews->whereDate('date', $date)->orderBy('date', 'asc');
        } elseif ($day === 'all') {
            $interviews->orderBy('date', 'desc');
        }

        return DataTables::of($interviews)

            ->addColumn('interviewer', function ($interview) {
                return $interview->employee->name;
            })

            ->addColumn('type', function ($interview) use ($day) {
                if ($day === 'today' && $interview->type === 'online') {
                    return '<a class="text-primary" href="' . $interview->meeting_url . '" target="_blank">' . "Join Now" . '</a>';
                } else {
                    return ucfirst($interview->type);
                }
            })

            ->addColumn('status', function ($interview) {

                $text = InterviewStatusMethods::getStyle($interview->status) == 'bg-warning' ? 'text-dark' : 'text-white';
                $selectOptions = '<select name="interview_status" id="interview-status-' . $interview->id . '" style="width: auto;" class="' . $text . ' form-control interview_status ' . InterviewStatusMethods::getStyle($interview->status) . '" data-interview-id="' . $interview->id . '">';

                foreach (InterviewStatus::STATUSES as $statusText) {
                    $selected = $statusText === $interview->status ? 'selected' : '';
                    $selectOptions .= '<option value="' . $statusText . '" class="option-color" ' . $selected . '>' . $statusText . '</option>';
                }

                $selectOptions .= '</select>';

                return $selectOptions;
            })
            ->addColumn('action', function ($interview) use ($day) {
                $actions = '';

                if ($day === 'today') {
                    $actions .= '<a href="' . route('send-reminder-email', ['id' => $interview->id]) . '" onclick="event.preventDefault(); sendReminderEmail(' . $interview->id . ')"><i class="fa fa-bell text-success ml-3"></i></a>';
                }

                $actions .= '<a href="' . route('edit-interview', [$interview->id]) . '"><i class="fa fa-edit text-primary ml-3"></i></a>' .
                    '<a href="' . route('view-interview', [$interview->id]) . '"><i class="fa fa-eye text-primary ml-3"></i></a>' .
                    '<a href="' . asset('assets/cv/' . $interview->document) . '" target="_blank"><i class="fa fa-file-text text-primary ml-3"></i></a>' .
                    '<a href="' . route('delete-interview', [$interview->id]) . '"><i class="fa fa-trash text-danger ml-3 mr-3"></i></a>';

                return $actions;
            })

            ->rawColumns(['status', 'action', 'type'])
            ->make(true);
    }

    public function filterInterviews(Request $request)
    {
        $interviewer = $request->input('interviewer');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $status = $request->input('status');
        $interviewType = $request->input('interviewType');

        $interviews = Interview::query();

        if ($interviewer && $interviewer !== 'all interviewers') {
            $interviews->whereHas('employee', function ($query) use ($interviewer) {
                $query->where('name', 'like', '%' . $interviewer . '%');
            });
        }
        if ($status && $status !== 'all status') {
            $interviews->where('status', $status);
        }
        if ($interviewType && $interviewType !== 'all types') {
            $interviews->where('type', $interviewType);
        }
        if ($startDate && !$endDate) {
            $interviews->whereDate('date', '=', date('Y-m-d', strtotime($startDate)));
        }
        if ($startDate) {
            $interviews->where('date', '>=', date('Y-m-d', strtotime($startDate)));
        }
        if ($endDate) {
            $endDatePlusOne = date('Y-m-d', strtotime($endDate . ' + 1 day'));
            $interviews->where('date', '<', $endDatePlusOne);
        }

        return DataTables::of($interviews)

            ->addColumn('interviewer', function ($interview) {
                return $interview->employee->name;
            })

            ->addColumn('type', function ($interview) {
                return ucfirst($interview->type);
            })

            ->addColumn('status', function ($interview) {
                $text = InterviewStatusMethods::getStyle($interview->status) == 'bg-warning' ? 'text-dark' : 'text-white';
                $selectOptions = '<select name="interview_status" id="interview-status-' . $interview->id . '" class="' . $text . ' form-control interview_status ' . InterviewStatusMethods::getStyle($interview->status) . '" data-interview-id="' . $interview->id . '">';

                foreach (InterviewStatus::STATUSES as $statusText) {
                    $selected = $statusText === $interview->status ? 'selected' : '';
                    $selectOptions .= '<option value="' . $statusText . '" class="option-color" ' . $selected . '>' . $statusText . '</option>';
                }
                $selectOptions .= '</select>';

                return $selectOptions;
            })

            ->addColumn('action', function ($interview) {
                return '<a href="/edit-interview/' . $interview->id . '"><i class="fa fa-edit text-primary ml-3"></i></a>' .
                    '<a href="/view-interview/' . $interview->id . '"><i class="fa fa-eye text-primary ml-3"></i></a>' .
                    '<a href="/assets/cv/' . $interview->document . '" target="_blank"><i class="fa fa-file-text text-primary ml-3"></i></a>' .
                    '<a href="/delete-interview/' . $interview->id . '"><i class="fa fa-trash text-danger ml-3"></i></a>';
            })

            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function view($id)
    {
        $interview = Interview::find($id);
        $interviewer = DB::table('employees')->where('id', $interview->interviewer)->first();
        return view('interview.view', compact('interview', 'interviewer'));
    }

    public function create()
    {
        $interviewers = DB::table('employees')->where('is_interviewer', 1)->get();
        return view('interview.create', compact('interviewers'));
    }

    public function saveInterview(Request $request)
    {
        $interview = new Interview();
        $interview->title = $request->title;
        $interview->name = $request->name;
        $interview->email = $request->email;
        $interview->phone = $request->phone;
        $interview->c_salary = $request->c_salary;
        $interview->e_salary = $request->e_salary;
        $interview->notice_period = $request->notice_period;
        $interview->date = $request->date;
        $interview->type = $request->type;
        $interview->hr_comments = $request->hr_comments;
        $interview->interviewer = $request->interviewer;
        $interview->meeting_url = $request->meeting_url;

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $extension = $file->getClientOriginalExtension();
            $filename =  $request->name . ' ' . $request->title . ' - ' . time() . '.' . $extension;
            $file->move('assets/cv/', $filename);
            $interview->document = $filename;
        }

        $interview->save();

        SendInterviewEmails::dispatch($interview);


        return redirect('interviews/all');
    }

    public function edit($id)
    {
        $interview = Interview::find($id);
        $interviewers = DB::table('employees')->where('is_interviewer', 1)->get();
        return view('interview.edit', compact('interview', 'interviewers'));
    }

    public function update(Request $request, $id)
    {
        $interview = Interview::find($id);
        $interview->title = $request->title;
        $interview->name = $request->name;
        $interview->email = $request->email;
        $interview->phone = $request->phone;
        $interview->c_salary = $request->c_salary;
        $interview->e_salary = $request->e_salary;
        $interview->notice_period = $request->notice_period;
        $interview->date = $request->date;
        $interview->interviewer_comments = $request->interviewer_comments;
        $interview->hr_comments = $request->hr_comments;
        $interview->type = $request->type;
        $interview->meeting_url = $request->meeting_url;
        $interview->status = $request->status;
        $interview->interviewer = $request->interviewer;

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $extension = $file->getClientOriginalExtension();
            $filename =  $request->name . ' ' . $request->title . ' - ' . time() . '.' . $extension;
            $file->move('assets/cv/', $filename);
            $interview->document = $filename;
        }

        $interview->save();

        return redirect('interviews/all');
    }

    public function updateStatus(Request $request)
    {
        $id = $request->input('id');
        $interview = Interview::find($id);

        $interview->status = $request->input('status');
        $interview->save();

        $color = InterviewStatusMethods::getStyle($request->input('status'));
        $text = ($color == 'bg-warning') ? 'text-dark' : 'text-white';
        return response()->json(['color' => $color, 'text' => $text]);
    }

    public function sendInterviewReminderEmail(Request $request)
    {
        $id = $request->input('id');
        $interview = Interview::find($id);
        SendInterviewReminderEmails::dispatch($interview);
        return;
    }

    public function delete($id)
    {
        $interview = Interview::find($id);
        if ($interview) {
            $interview->delete();
            return response()->json(['success' => 'Record Deleted!']);
        } else {
            return response()->json(['error' => 'Error Deleting!'], 404);
        }
    }
}
