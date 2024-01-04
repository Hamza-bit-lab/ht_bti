<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        return view('employee-panel.index');
    }

    public function attendance()
    {
        return view('employee-panel.employee-attendance');
    }

    public function getAttendanceReport(Request $request)
    {
        $userEmail = Auth::user()->email;

        $employee = Employee::where('email', $userEmail)->first();

        $attendanceId = $employee->attendance_id;

        $month = $request->input('month');

        $attendance = DB::table('attendance_logs')
            ->where('attendance_id', $attendanceId)
            ->whereYear('date', '=', date('Y', strtotime($month)))
            ->whereMonth('date', '=', date('m', strtotime($month)))
            ->orderBy('date', 'asc')
            ->get();

        $totalTimeSum = 0;
        foreach ($attendance as $record) {
            $record->date = Carbon::createFromFormat('Y-m-d', $record->date)->format('j F, Y');

            if ($record->checkIn !== null) {
                $record->checkIn = Carbon::createFromFormat('H:i:s', $record->checkIn)->format('h:i A');
            } else {
                $record->checkIn = 'N/A';
            }

            if ($record->checkOut !== null) {
                $record->checkOut = Carbon::createFromFormat('H:i:s', $record->checkOut)->format('h:i A');
            } else {
                $record->checkOut = 'N/A';
            }

            // Calculate total time in minutes
            if ($record->totalTime !== 'N/A') {
                $totalTime = explode(':', $record->totalTime);
                $totalTimeSum += ($totalTime[0] * 60) + $totalTime[1];
            }

            $record->totalTime = $record->totalTime ? Carbon::createFromFormat('H:i:s', $record->totalTime)->format('H:i') : 'N/A';
            $record->requiredTime = $record->requiredTime ? Carbon::createFromFormat('H:i:s', $record->requiredTime)->format('H:i') : 'N/A';

            // Calculate difference
            $totalTime = explode(':', $record->totalTime);
            $requiredTime = explode(':', $record->requiredTime);

            $totalMinutes = $totalTime[0] * 60 + $totalTime[1];
            $requiredMinutes = $requiredTime[0] * 60 + $requiredTime[1];

            $differenceMinutes = $totalMinutes - $requiredMinutes;

            $hours = floor(abs($differenceMinutes) / 60);
            $minutes = abs($differenceMinutes) % 60;

            $sign = ($differenceMinutes < 0) ? '-' : '';

            $record->difference = sprintf("%s%02d:%02d", $sign, $hours, $minutes);

            // Assign color class to difference
            $record->differenceClass = $record->difference >= 0 ? 'text-success' : 'text-danger';
        }

        $totalHours = floor($totalTimeSum / 60);
        $totalMinutes = $totalTimeSum % 60;
        $totalTimeSumFormatted = sprintf('%02d:%02d', $totalHours, $totalMinutes);

        $attendanceLogs = DB::table('attendance_logs')
            ->whereYear('date', '=', date('Y', strtotime($month)))
            ->whereMonth('date', '=', date('m', strtotime($month)))
            ->get(['date', 'requiredTime']);

        $uniqueDates = $attendanceLogs->pluck('date')->unique();
        $workingDaysPassed = $uniqueDates->count();

        // Initialize variables with default values
        $totalDays = 0;
        $publicHolidays = 0;
        $workingHoursPassed = '00:00';

        // Calculate total days and public holidays only if data is present
        if ($attendanceLogs->isNotEmpty()) {
            $totalDays = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($month)), date('Y', strtotime($month)));

            $year = date('Y', strtotime($month));

            $publicHolidays = DB::table('holidays')
                ->whereYear('start', $year)
                ->whereMonth('start', date('m', strtotime($month)))
                ->count();

            // Calculate working hours passed only if data is present
            $totalSeconds = 0;
            foreach ($uniqueDates as $uniqueDate) {
                $record = DB::table('attendance_logs')
                    ->select('requiredTime')
                    ->whereDate('date', $uniqueDate)
                    ->first();

                if ($record) {
                    list($hours, $minutes, $seconds) = explode(':', $record->requiredTime);
                    $totalSeconds += $hours * 3600 + $minutes * 60 + $seconds;
                }
            }

            $totalHours = floor($totalSeconds / 3600);
            $totalMinutes = floor(($totalSeconds % 3600) / 60);

            $workingHoursPassed = sprintf('%02d:%02d', $totalHours, $totalMinutes);
        }

        // Passing summary
        $summary = [
            'totalDays' => $totalDays,
            'workingDaysPassed' => $workingDaysPassed,
            'workingHoursPassed' => $workingHoursPassed,
            'publicHolidays' => $publicHolidays,
            'workingHoursWorked' => $totalTimeSumFormatted,
        ];

        return response()->json(['attendance' => $attendance, 'summary' => $summary]);
    }
}
