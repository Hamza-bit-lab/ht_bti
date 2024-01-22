<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
//use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employee.employees');
    }

    public function getEmployees(Request $request)
    {
        $status = $request->input('status', 'active');

        $employees = Employee::query();
        if ($status === 'active') {
            $employees->where('is_employed', true);
        } elseif ($status === 'inactive') {
            $employees->where('is_employed', false);
        }

        return DataTables::of($employees)

            ->addColumn('attendance_id', function ($employee) {
                return '<div style="position: relative;">
                        <span style="padding-right: 25px;">' . $employee->attendance_id . '</span>
                        <a href="#" class="edit-attendance-id" data-employee-id="' . $employee->id . '" style="position: absolute; top: 0; right: 0;">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                    </div>';
            })

            ->addColumn('is_interviewer', function ($employee) {
                $toggleSwitch = '<div class="form-check form-switch d-flex justify-content-center">' .
                    '<input class="form-check-input toggle-switch-interviewer" type="checkbox" id="toggle-interviewer-' . $employee->id . '" data-id="' . $employee->id . '" ' . ($employee->is_interviewer ? 'checked' : '') . '>' .
                    '</div>';

                return $toggleSwitch;
            })

            ->addColumn('is_employed', function ($employee) {
                $toggleSwitch = '<div class="form-check form-switch d-flex justify-content-center">' .
                    '<input class="form-check-input toggle-switch-employed" type="checkbox" id="toggle-employee-' . $employee->id . '" data-id="' . $employee->id . '" ' . ($employee->is_employed ? 'checked' : '') . '>' .
                    '</div>';

                return $toggleSwitch;
            })

            ->addColumn('action', function ($employee) {
                $actions = '<a href="' . route('edit-employee', [$employee->id]) . '"><i class="fa fa-edit text-primary ml-3"></i></a>' .
                    '<a href="' . route('view-employee', [$employee->id]) . '"><i class="fa fa-eye text-primary ml-3"></i></a>' .
                    '<a href="' . route('delete-employee', [$employee->id]) . '"><i class="fa fa-trash text-danger ml-3 mr-3"></i></a>';

                return $actions;
            })

            ->rawColumns(['attendance_id', 'is_interviewer', 'is_employed', 'action'])
            ->make(true);
    }

    public function view($id)
    {
        $employee = Employee::find($id);
        return view('employee.view', compact('employee'));
    }

    public function create()
    {
        return view('employee.create');
    }

    public function saveEmployee(Request $request)
    {
        $request->validate([
            'attendance_id' => 'unique:employees',
        ], [
            'attendance_id.unique' => 'The attendance ID is already taken!',
        ]);

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->attendance_id = $request->attendance_id;
        $employee->position = $request->position;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->salary = $request->salary;
        $employee->degree = $request->degree;
        $employee->joining_date = $request->joining_date;
        $employee->birthday = $request->birthday;
        $employee->contract_end_date = $request->contract_end_date;

        try {
            $employee->save();
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'employee',
                'password' => Hash::make('12345678'),
            ]);
        } catch (QueryException $e) {
            return redirect()->back()->withErrors(['error' => 'Employee not saved.'])->withInput();
        }

        return redirect('employees')->with('success', 'Employee created successfully.');
    }

    public function edit($id)
    {
        $employee = Employee::find($id);
        return view('employee.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        $employee->name = $request->name;
        $employee->position = $request->position;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->salary = $request->salary;
        $employee->degree = $request->degree;
        $employee->joining_date = $request->joining_date;
        $employee->birthday = $request->birthday;
        $employee->contract_end_date = $request->contract_end_date;


        $employee->save();

        return redirect('employees');
    }

    public function delete($id)
    {
        $employee = Employee::find($id);

        if ($employee) {
            $employee->delete();

            $user = User::where('email', $employee->email)->first();
            if ($user) {
                $user->delete();
            }

            return response()->json(['success' => 'Employee deleted!']);
        } else {
            return response()->json(['error' => 'Employee not found!'], 404);
        }
    }

    public function updateInterviewerStatus(Request $request, $id)
    {
        $employee = Employee::find($id);

        $employee->is_interviewer = $request->input('newStatus');
        $employee->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function updateEmployeeStatus(Request $request, $id)
    {
        $employee = Employee::find($id);

        $employee->is_employed = $request->input('newStatus');
        $employee->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function updateAttendanceId(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->attendance_id = $request->input('attendance_id');
        $employee->save();

        return response()->json(['success' => true]);
    }

    private function getMonths()
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    private function getYearsRange()
    {
        $currentYear = now()->year;
        $years = [];

        for ($i = $currentYear - 100; $i <= $currentYear + 10; $i++) {
            $years[$i] = $i;
        }

        // Set default value to 2024
        $defaultYear = 2024;
        $years = array_reverse($years, true);
        $years[$defaultYear] = $defaultYear;

        return $years;
    }

    public function checkEmployees()
    {
        $months = $this->getMonths();
        $years = $this->getYearsRange();
        return view('employee.check-employees', compact('months', 'years'));
    }

    public function getEmployeeEventsByFilter($filter, $selectedMonth, $selectedYear)
    {
        $employeeEvents = [];

        if ($filter === 'joining_date') {
            $employees = Employee::where('is_employed', true)
                ->whereYear('joining_date', $selectedYear)
                ->whereMonth('joining_date', $selectedMonth)
                ->get();
//            dd($employees);

            foreach ($employees as $employee) {
                $color = '#FFA500';
                $employeeEvents[] = [
                    'title' => $employee->name . "'s ",
                    'start' => Carbon::parse($employee->joining_date)->format('Y-m-d'),
                    'color' => $color,
                ];
            }
        }
        elseif ($filter === 'contract_end_date') {
            $employees = Employee::where('is_employed', true)
                ->whereYear('contract_end_date', $selectedYear)
                ->get();

            foreach ($employees as $employee) {
                $color = '#A020F0';
                $employeeEvents[] = [
                    'title' => $employee->name . "'s Ends",
                    'start' => Carbon::parse($employee->contract_end_date)->format('Y-m-d'),
                    'color' => $color,
                ];
            }
        }
        elseif ($filter === 'birthday') {
            $employees = Employee::where('is_employed', true)
                ->whereRaw('DATE_FORMAT(birthday, "%m-%d") >= ?', [now()->format('m-d')])
                ->get();
            foreach ($employees as $employee) {
                $color = '#008000';
                $employeeEvents[] = [
                    'title' => $employee->name . "'s",
                    'start' => Carbon::parse($employee->birthday)->format('Y-m-d'),
                    'color' => $color,
                ];
            }
        }
        elseif ($filter === 'anniversary') {
            $employees = Employee::where('is_employed', true)->get();

            foreach ($employees as $employee) {
                $color = '#008000';
                $anniversaryDate = Carbon::parse($employee->joining_date);
                $today = now();
                $anniversaryYear = $today->diff($anniversaryDate)->y;
                $anniversaryYear++;
                $adjustedAnniversaryDate = $anniversaryDate->year(now()->year);

                if ($adjustedAnniversaryDate->isBetween(now(), now()->addMonths(11))) {
                    $suffix = $this->getNumberSuffix($anniversaryYear);

                    $employeeEvents[] = [
                        'title' => $employee->name . "'s " . $anniversaryYear . $suffix,
                        'start' => $adjustedAnniversaryDate->format('Y-m-d'),
                        'color' => $color,
                    ];
                }
            }
        }
        return $employeeEvents;
    }
    public function getEmployeeEvents(Request $request)
    {
        $filter = $request->input('filter', 'joining_date');
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        $employeeEvents = $this->getEmployeeEventsByFilter($filter, $selectedMonth, $selectedYear);

        return response()->json(['events' => $employeeEvents]);
    }

    public function getNumberSuffix($number) {
        if ($number % 100 >= 11 && $number % 100 <= 13) {
            return 'th';
        }

        switch ($number % 10) {
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
            default: return 'th';
        }
    }


}
