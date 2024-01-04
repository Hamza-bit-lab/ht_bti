<?php

namespace App\Http\Controllers;

use App\Interfaces\InterviewStatus;
use App\Interfaces\InterviewStatusMethods;
use App\Models\Employee;
use App\Models\Interview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $statuses = InterviewStatus::STATUSES;
        $interviewers = Interview::distinct()->pluck('interviewer');
        $employees = Employee::whereIn('id', $interviewers)->get();
        $totalInterviews = Interview::count();
        $statusCounts = $this->getStatusCounts();
        $allInterviewsData = Interview::select(DB::raw('DATE_FORMAT(date, "%b, %Y") as month'))
            ->where('date', '>=', Carbon::now()->startOfMonth()->subMonths(11))
            ->where('date', '<=', Carbon::now()->endOfMonth())
            ->get()
            ->groupBy('month')
            ->map
            ->count()
            ->sortBy(function ($value, $key) {
                return Carbon::createFromFormat('M, Y', $key);
            });
        $lastTwelveMonths = $this->getLastTwelveMonths();
        $interviewsData = [];

        foreach ($lastTwelveMonths as $month) {
            $interviewsData[$month] = $allInterviewsData[$month] ?? 0;
        }

        return view('dashboard.index', compact('interviewsData', 'statuses', 'employees', 'totalInterviews', 'statusCounts'));
    }

    public function getStatusCounts()
    {
        $statusCounts = [];

        foreach (InterviewStatus::STATUSES as $status) {
            $count = Interview::where('status', $status)->count();
            $color = InterviewStatusMethods::getStyle($status);

            $parts = explode('-', $color);
            $color = (count($parts) > 1) ? trim($parts[1]) : '';

            $statusCounts[] = [
                'status' => $status,
                'count' => $count,
                'color' => $color,
            ];
        }

        return $statusCounts;
    }

    private function getLastTwelveMonths()
    {
        $months = [];

        for ($i = 11; $i >= 0; $i--) {
            $currentDate = Carbon::now()->startOfMonth();
            $month = $currentDate->subMonths($i)->format('M, Y');
            $months[] = $month;
        }

        return $months;
    }
    
    public function filterInterviews(Request $request)
    {
        $selectedStatus = $request->input('status');
        $selectedInterviewer = $request->input('interviewer');
        $selectedType = $request->input('type');

        $query = Interview::select(DB::raw('DATE_FORMAT(date, "%b, %Y") as month'))
            ->where('date', '>=', Carbon::now()->startOfMonth()->subMonths(11))
            ->where('date', '<=', Carbon::now()->endOfMonth());

        if ($selectedStatus != 'all status') {
            $query->where('status', $selectedStatus);
        }

        if ($selectedInterviewer != 'all interviewers') {
            $query->whereHas('employee', function ($query) use ($selectedInterviewer) {
                $query->where('name', 'like', '%' . $selectedInterviewer . '%');
            });
        }

        if ($selectedType != 'all types') {
            $query->where('type', $selectedType);
        }

        $interviewsData = $query->get()
            ->groupBy('month')
            ->map
            ->count()
            ->sortBy(function ($value, $key) {
                return Carbon::createFromFormat('M, Y', $key);
            });

        return response()->json($interviewsData);
    }
}
