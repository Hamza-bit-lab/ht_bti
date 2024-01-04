<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Employee;
use Exception;

class AttendanceController extends Controller
{
    // +++++ Attendance Methods Starting +++++

    public function uploadAttendance()
    {
        return view('attendance.upload-attendance');
    }

    public function processAttendanceFile(Request $request)
    {
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $extension = $file->getClientOriginalExtension();


            $employees = Employee::select('attendance_id', 'name')
                ->where('is_employed', 1)
                ->get();
            // Creating an associative array to store employees' attendance_id and name
            $employeeData = [];

            foreach ($employees as $employee) {
                $employeeData[$employee->attendance_id]['attendance_id'] = $employee->attendance_id;


                $employeeData[$employee->attendance_id]['name'] = $employee->name;
            }

            if (strtolower($extension) === 'csv') {
                try {
                    $import = new AttendanceImport();
                    $data = Excel::toCollection($import, $file);
//                    dd($extension);

                    $processedData = [];
                    foreach ($data as $row) {
                        foreach ($row as $entry) {
                            $dateTime = $entry['datetime'];
                            $date = date('Y-m-d', strtotime($dateTime));
                            $no = $entry['no'];
                            $status = $entry['status'];
                            $time = date('H:i', strtotime($dateTime));

                            $dayOfWeek = date('l', strtotime($date));
                            // Fetch office hours for the corresponding day of the week
                            $officeHours = DB::table('office_hours')
                                ->select('starting_time', 'break_starting', 'break_ending', 'closing_time')
                                ->where('day', $dayOfWeek)
                                ->first();

                            $startingTime = strtotime($officeHours->starting_time);
                            $breakStarting = strtotime($officeHours->break_starting);
                            $breakEnding = strtotime($officeHours->break_ending);
                            $closingTime = strtotime($officeHours->closing_time);

                            $requiredHours = ($closingTime - $startingTime) - ($breakEnding - $breakStarting);
                            $requiredHoursFormatted = gmdate('H:i', $requiredHours); // Format required hours as HH:MM

                            $startingTimeFormatted = date('h:i A', $startingTime);
                            $breakStartingFormatted = date('h:i A', $breakStarting);
                            $breakEndingFormatted = date('h:i A', $breakEnding);
                            $closingTimeFormatted = date('h:i A', $closingTime);

                            $processedData[$date][$no]['requiredTime'] = $requiredHoursFormatted;
                            $processedData[$date][$no]['startingTime'] = $startingTimeFormatted;
                            $processedData[$date][$no]['breakStarting'] = $breakStartingFormatted;
                            $processedData[$date][$no]['breakEnding'] = $breakEndingFormatted;
                            $processedData[$date][$no]['closingTime'] = $closingTimeFormatted;


                            if (isset($employeeData[$no])) {
                                $processedData[$date][$no]['name'] = $employeeData[$no]['name'];
                            } else {
//                                dd("Debug: \$employeeData array", $employeeData);

                                $processedData[$date][$no]['name'] = "Unknown";
                            }


                            if ($status === 'C/In' && !isset($processedData[$date][$no]['firstCheckIn'])) {
                                $processedData[$date][$no]['firstCheckIn'] = $time;
                            } elseif ($status === 'C/Out') {
                                $processedData[$date][$no]['lastCheckOut'] = $time;

                                if (isset($processedData[$date][$no]['firstCheckIn'])) {
                                    $firstCheckIn = strtotime($processedData[$date][$no]['firstCheckIn']);
                                    $lastCheckOut = strtotime($time);

                                    $totalTime = $lastCheckOut - $firstCheckIn;
                                    $totalTimeWithoutBreak = $totalTime;
                                    $totalTime -= ($breakEnding - $breakStarting);

                                    $totalTimeFormatted = $totalTime < 0 ? gmdate('H:i', $totalTimeWithoutBreak) : gmdate('H:i', $totalTime);

                                    $processedData[$date][$no]['totalTime'] = $totalTimeFormatted;
                                }
                            }
                        }
                    }

                    // If first check is empty
                    foreach ($processedData as &$users) {
                        foreach ($users as &$userData) {
                            if (!isset($userData['firstCheckIn'])) {
                                $userData['firstCheckIn'] = "";
                            }
                            if (!isset($userData['lastCheckOut'])) {
                                $userData['lastCheckOut'] = "";
                            }
                            if (!isset($userData['totalTime'])) {
                                $userData['totalTime'] = "00:00";
                            }
                        }
                    }

                    foreach ($employeeData as $employee) {
                        $attendanceId = $employee['attendance_id'];

                        // If the employee's attendance_id is not in processed data, add an empty entry
                        if (!isset($processedData[$date][$attendanceId])) {
                            $processedData[$date][$attendanceId]['attendance_id'] = $attendanceId;
                            $processedData[$date][$attendanceId]['name'] = $employee['name'];
                            $processedData[$date][$attendanceId]['firstCheckIn'] = "";
                            $processedData[$date][$attendanceId]['lastCheckOut'] = "";
                            $processedData[$date][$attendanceId]['totalTime'] = "00:00";
                            $processedData[$date][$attendanceId]['requiredTime'] = $requiredHoursFormatted;
                        }
                    }

                    return response()->json([$processedData]);
                } catch (Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
            return response()->json(['error' => 'Invalid file format. Please upload an CSV file.'], 400);
        }
        return response()->json(['error' => 'File not found'], 400);
    }

    public function saveAttendance(Request $request)
    {
        $attendanceData = json_decode($request->input('attendanceData'), true);

        try {
            foreach ($attendanceData as $data) {
                $checkIn = isset($data['checkIn']) && $data['checkIn'] !== '' ?
                    $data['checkIn'] : null;

                $checkOut = isset($data['checkOut']) && $data['checkOut'] !== '' ?
                    $data['checkOut'] : null;

                // Check if the record already exists based on attendance_id and date
                $existingRecord = DB::table('attendance_logs')
                    ->where('attendance_id', $data['attendance_id'])
                    ->where('date', $data['date'])
                    ->first();

                if (!$existingRecord) {
                    // If the record doesn't exist, insert a new one
                    DB::table('attendance_logs')->insert([
                        'attendance_id' => $data['attendance_id'],
                        'name' => $data['name'],
                        'date' => $data['date'],
                        'checkIn' => $checkIn,
                        'checkOut' => $checkOut,
                        'adjustmentValue' => $data['adjustmentValue'],
                        'adjustmentType' => $data['adjustmentType'],
                        'totalTime' => $data['totalTime'],
                        'requiredTime' => $data['requiredTime'],
                        'status' => $data['status'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    return redirect('upload-attendance')->with('error', 'Attendance for this date already exists!');
                }
            }

            return redirect('manage-attendance')->with('success', 'Saved Successfully');
        } catch (Exception $e) {
            return redirect('upload-attendance')->with('error', '');
        }
    }

    public function manageAttendance()
    {
        return view('attendance.manage-attendance');
    }

    public function getAttendanceEvent()
    {
        $attendanceData = DB::table('attendance_logs')
            ->get();


        return response()->json($attendanceData);
    }

    public function getAttendance(Request $request)
    {
        $selectedDate = $request->input('date');
        $attendanceData = DB::table('attendance_logs')
            ->whereDate('date', $selectedDate)
            ->get();

        if ($attendanceData->isEmpty()) {
            return response()->json(['message' => 'No data present for this date']);
        }

        return response()->json(['attendanceData' => $attendanceData]);
    }

    public function updateAttendance(Request $request)
    {
        $attendanceData = json_decode($request->input('attendanceData'), true);

        $check = reset($attendanceData);
        if ($check['date'] !== $check['updateDate']) {
            $existingRecordWithUpdateDate = DB::table('attendance_logs')
                ->where('date', $check['updateDate'])
                ->first();

            if ($existingRecordWithUpdateDate) {
                return redirect('manage-attendance')->with('error', 'Attendance already exist for the date!');
            }
        }

        try {
            foreach ($attendanceData as $data) {
                $checkIn = isset($data['checkIn']) && $data['checkIn'] !== '' ? $data['checkIn'] : null;
                $checkOut = isset($data['checkOut']) && $data['checkOut'] !== '' ? $data['checkOut'] : null;

                // Find the record based on attendance_id and date
                $existingRecord = DB::table('attendance_logs')
                    ->where('attendance_id', $data['attendance_id'])
                    ->where('date', $data['date'])
                    ->first();

                // Fetch office hours for the corresponding day of the week
                $dayOfWeek = date('l', strtotime($data['updateDate']));
                $officeHours = DB::table('office_hours')
                    ->select('starting_time', 'break_starting', 'break_ending', 'closing_time')
                    ->where('day', $dayOfWeek)
                    ->first();

                $startingTime = strtotime($officeHours->starting_time);
                $breakStarting = strtotime($officeHours->break_starting);
                $breakEnding = strtotime($officeHours->break_ending);
                $closingTime = strtotime($officeHours->closing_time);

                $requiredHours = ($closingTime - $startingTime) - ($breakEnding - $breakStarting);
                $requiredHoursFormatted = gmdate('H:i', $requiredHours); // Format required hours as HH:MM

                if ($existingRecord) {
                    // If the record exists, update it
                    DB::table('attendance_logs')
                        ->where('attendance_id', $data['attendance_id'])
                        ->where('date', $data['date'])
                        ->update([
                            'checkIn' => $checkIn,
                            'checkOut' => $checkOut,
                            'date' => $data['updateDate'],
                            'adjustmentValue' => $data['adjustmentValue'],
                            'adjustmentType' => $data['adjustmentType'],
                            'totalTime' => $data['totalTime'],
                            'requiredTime' => $requiredHoursFormatted,
                            'status' => $data['status'],
                            'updated_at' => now(),
                        ]);
                } else {
                    return redirect('manage-attendance')->with('error');
                }
            }

            return redirect('manage-attendance')->with('success', 'Saved Successfully');
        } catch (Exception $e) {
            return redirect('manage-attendance')->with('error', '');
        }
    }

    public function getWorkingHours(Request $request)
    {
        $selectedDate = $request->input('selectedDate');
        $dayOfWeek = date('l', strtotime($selectedDate));

        $officeHours = DB::table('office_hours')
            ->select('starting_time', 'break_starting', 'break_ending', 'closing_time')
            ->where('day', $dayOfWeek)
            ->first();

        if ($officeHours) {
            $startingTime = strtotime($officeHours->starting_time);
            $breakStarting = strtotime($officeHours->break_starting);
            $breakEnding = strtotime($officeHours->break_ending);
            $closingTime = strtotime($officeHours->closing_time);

            $requiredHours = ($closingTime - $startingTime) - ($breakEnding - $breakStarting);
            $requiredHoursFormatted = gmdate('H:i', $requiredHours); // Format required hours as HH:MM

            return response()->json(['workingHours' => $requiredHoursFormatted]);
        }

        // If office hours are not found for the selected date, return a default value or handle accordingly
        return response()->json(['workingHours' => '00:00']);
    }

    public function getWorkingSchedule(Request $request)
    {
        $selectedDate = $request->input('selectedDate');
        $dayOfWeek = date('l', strtotime($selectedDate));

        $officeHours = DB::table('office_hours')
            ->select('starting_time', 'break_starting', 'break_ending', 'closing_time')
            ->where('day', $dayOfWeek)
            ->first();

        if ($officeHours) {
            $startingTime = date('h:i A', strtotime($officeHours->starting_time));
            $breakStarting = date('h:i A', strtotime($officeHours->break_starting));
            $breakEnding = date('h:i A', strtotime($officeHours->break_ending));
            $closingTime = date('h:i A', strtotime($officeHours->closing_time));

            return response()->json([
                'startingTime' => $startingTime,
                'breakStarting' => $breakStarting,
                'breakEnding' => $breakEnding,
                'closingTime' => $closingTime,
            ]);
        }

        // If office hours are not found for the selected date, return default or handle accordingly
        return response()->json([
            'startingTime' => '00:00',
            'breakStarting' => '00:00',
            'breakEnding' => '00:00',
            'closingTime' => '00:00',
        ]);
    }

    public function getBreakTime(Request $request)
    {
        $selectedDate = $request->input('selectedDate');
        $dayOfWeek = date('l', strtotime($selectedDate));

        $officeHours = DB::table('office_hours')
            ->select('break_starting', 'break_ending')
            ->where('day', $dayOfWeek)
            ->first();

        if ($officeHours) {
            $breakStarting = strtotime($officeHours->break_starting);
            $breakEnding = strtotime($officeHours->break_ending);

            $breakDuration = $breakEnding - $breakStarting;
            $breakDurationFormatted = gmdate('H:i', $breakDuration); // Format break duration as HH:MM

            return response()->json(['breakTime' => $breakDurationFormatted]);
        }

        // If break time is not found for the selected date, return a default value or handle accordingly
        return response()->json(['breakTime' => '00:00']);
    }

    public function checkAttendanceAvailability(Request $request)
    {
        $selectedDate = $request->input('selectedDate');

        $attendanceExists = DB::table('attendance_logs')
            ->whereDate('date', $selectedDate)
            ->exists();

        return response()->json(['exists' => $attendanceExists]);
    }

    // ----- Manage Attendance Methods Ending -----





    // +++++ Attendance Methods Starting +++++

    public function viewAttendance()
    {
        return view('attendance.view-attendance');
    }

    public function getAttendanceReport(Request $request)
    {
        $employees = Employee::where('is_employed', '1')->get();
        $month = $request->input('month');

        foreach ($employees as &$employee) {
            $attendanceId = $employee['attendance_id'];

            $attendanceData = DB::table('attendance_logs')
                ->where('attendance_id', $attendanceId)
                ->whereIn('status', ['present', 'remote'])
                ->whereYear('date', '=', date('Y', strtotime($month)))
                ->whereMonth('date', '=', date('m', strtotime($month)))
                ->select(
                    DB::raw("DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(totalTime))), '%H:%i') as total_worked"),
                    DB::raw("DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(requiredTime))), '%H:%i') as total_required"),
                    DB::raw("DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(totalTime)) - SUM(TIME_TO_SEC(requiredTime))), '%H:%i') as difference")
                )
                ->first();

            // Get the count of different attendance statuses for the employee in the given month
            $attendanceStatusCounts = DB::table('attendance_logs')
                ->where('attendance_id', $attendanceId)
                ->whereYear('date', '=', date('Y', strtotime($month)))
                ->whereMonth('date', '=', date('m', strtotime($month)))
                ->select(
                    DB::raw('COUNT(CASE WHEN status = "remote" THEN 1 END) as remote_count'),
                    DB::raw('COUNT(CASE WHEN status = "leave" THEN 1 END) as leave_count')
                )
                ->first();

            // Merge the obtained data with the employee
            $employee['remote'] = $attendanceStatusCounts->remote_count ?? 0;
            $employee['leave'] = $attendanceStatusCounts->leave_count ?? 0;
            $employee['totalHoursWorked'] = $attendanceData->total_worked ?? '00:00';
            $employee['totalHoursRequired'] = $attendanceData->total_required ?? '00:00';
            $employee['difference'] = $attendanceData->difference ?? '00:00';
            $employee['differenceClass'] = $attendanceData->difference >= 0 ? 'text-success' : 'text-danger';
        }

        $totalDays = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($month)), date('Y', strtotime($month)));

        $year = date('Y', strtotime($month));

        // Public holidays
        $publicHolidays = DB::table('holidays')
            ->whereYear('start', $year)
            ->whereMonth('start', date('m', strtotime($month)))
            ->count();

        // Working days passed
        $attendanceLogs = DB::table('attendance_logs')
            ->whereYear('date', '=', date('Y', strtotime($month)))
            ->whereMonth('date', '=', date('m', strtotime($month)))
            ->get(['date', 'requiredTime']);

        $uniqueDates = $attendanceLogs->pluck('date')->unique();
        $workingDaysPassed = $uniqueDates->count();

        // Working hours passed
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

        // Passing summary
        $summary = [
            'totalDays' => $totalDays,
            'workingDaysPassed' => $workingDaysPassed,
            'workingHoursPassed' => $workingHoursPassed,
            'publicHolidays' => $publicHolidays,
        ];

        return response()->json(['employees' => $employees, 'summary' => $summary]);
    }

    // ----- Attendance Methods Ending -----





    // +++++ Office Hours Methods Starting +++++

    public function officeHours()
    {
        $officeHours = DB::table('office_hours')->get();
        $officeHours = $officeHours->map(function ($officeHours) {
            $officeHours->visible = $officeHours->isON ? '' : 'disabled';
            return $officeHours;
        });
        return view('attendance.office-hours', compact('officeHours'));
    }

    public function updateOfficeHours(Request $request)
    {
        $dayIndex = $request->input('day');
        $fieldName = $request->input('field');
        $fieldValue = $request->input('value');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $day = $days[$dayIndex];

        try {
            $updateData = [$fieldName => $fieldValue];

            DB::table('office_hours')
                ->where('day', $day)
                ->update($updateData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function updateOfficeHoursStatus(Request $request)
    {
        $dayId = $request->input('dayId');
        $isON = $request->input('isON') === 'true' ? 1 : 0;

        DB::table('office_hours')->where('id', $dayId)->update(['isON' => $isON]);

        return response()->json(['success' => true]);
    }

    // ----- Office Hours Methods Ending -----





    // +++++ Holidays Methods Starting +++++

    public function holidays()
    {
        return view('attendance.holidays');
    }

    public function getAllHolidays()
    {
        $holidays = DB::table('holidays')->get();

        return response()->json($holidays);
    }

    public function storeHoliday(Request $request)
    {
        DB::table('holidays')->insert([
            'title' => $request->title,
            'start' => $request->start,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Holiday added successfully']);
    }

    public function updateHoliday(Request $request, $id)
    {
        DB::table('holidays')->where('id', $id)->update([
            'title' => $request->title,
            'start' => $request->start,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Holiday updated successfully']);
    }

    public function deleteHoliday($id)
    {
        try {
            DB::table('holidays')->where('id', $id)->delete();

            return response()->json(['message' => 'Holiday deleted successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete holiday', 'message' => $e->getMessage()], 500);
        }
    }

    // ----- Holidays Methods Ending -----
}
