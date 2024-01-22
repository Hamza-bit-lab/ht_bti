<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeEventController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EventController;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/run-migrate', function () {
    Artisan::call('migrate');
    return 'Migrations have been run successfully!';
});




Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'hr') {
            return redirect('/index'); // Redirect HR to '/index' route
        } elseif (Auth::user()->role === 'employee') {
            return redirect('/employee-dashboard'); // Redirect employee to '/employee-dashboard' route
        }
    }

    return redirect('/login'); // If not authenticated, redirect to login
});

Route::get('logout', function () {
    Auth::logout();
    return redirect('/login');
});

Auth::routes(['register' => false]);

Route::middleware(['auth', 'role:hr'])->group(function () {

    // Dashboard
    Route::get('index', [DashboardController::class, 'index']);
    Route::post('dashboard-filter', [DashboardController::class, 'filterInterviews'])->name('interviews.filter');

    // Interviews
    Route::get('interviews/{day}', [InterviewController::class, 'index']);
    Route::get('get-interviews/{day}', [InterviewController::class, 'getInterviews'])->name('get-interviews');
    Route::get('view-interview/{id}', [InterviewController::class, 'view'])->name('view-interview');
    Route::get('create-interview', [InterviewController::class, 'create']);
    Route::post('save-interview', [InterviewController::class, 'saveInterview'])->name('save-interview');
    Route::get('edit-interview/{id}', [InterviewController::class, 'edit'])->name('edit-interview');
    Route::post('update-interview/{id}', [InterviewController::class, 'update'])->name('update-interview');
    Route::delete('delete-interview/{id}', [InterviewController::class, 'delete'])->name('delete-interview');
    Route::post('update-interview-status/{id}', [InterviewController::class, 'updateStatus'])->name('update-interview-status');
    Route::post('filter-interviews', [InterviewController::class, 'filterInterviews'])->name('filter-interviews');
    Route::post('send-reminder-email/{id}', [InterviewController::class, 'sendInterviewReminderEmail'])->name('send-reminder-email');

    // Employees
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::get('get-employees', [EmployeeController::class, 'getEmployees'])->name('get-employees');
    Route::get('view-employee/{id}', [EmployeeController::class, 'view'])->name('view-employee');
    Route::get('create-employee', [EmployeeController::class, 'create']);
    Route::post('save-employee', [EmployeeController::class, 'saveEmployee'])->name('save-employee');
    Route::get('edit-employee/{id}', [EmployeeController::class, 'edit'])->name('edit-employee');
    Route::post('update-employee/{id}', [EmployeeController::class, 'update'])->name('update-employee');
    Route::delete('delete-employee/{id}', [EmployeeController::class, 'delete'])->name('delete-employee');
    Route::post('update-interviewer-status/{id}', [EmployeeController::class, 'updateInterviewerStatus']);
    Route::post('update-employee-status/{id}', [EmployeeController::class, 'updateEmployeeStatus']);
    Route::patch('update-attendance-id/{id}', [EmployeeController::class, 'updateAttendanceId']);
    Route::get('get-employee-events', [EmployeeController::class, 'getEmployeeEvents'])->name('get-employee-events');
    Route::get('employee-events', [EmployeeController::class, 'checkEmployees'])->name('employee-events');



    // Attendance Management
    Route::get('upload-attendance', [AttendanceController::class, 'uploadAttendance'])->name('upload-attendance');
    Route::post('process-attendance-file', [AttendanceController::class, 'processAttendanceFile'])->name('process-attendance-file');
    Route::post('save-attendance', [AttendanceController::class, 'saveAttendance'])->name('save-attendance');
    Route::get('manage-attendance', [AttendanceController::class, 'manageAttendance'])->name('manage-attendance');
    Route::get('get-attendance-event', [AttendanceController::class, 'getAttendanceEvent'])->name('get-attendance-event');
    Route::get('get-attendance', [AttendanceController::class, 'getAttendance'])->name('get-attendance');
    Route::post('update-attendance', [AttendanceController::class, 'updateAttendance'])->name('update-attendance');
    Route::get('get-working-hours', [AttendanceController::class, 'getWorkingHours'])->name('get-working-hours');
    Route::get('get-working-schedule', [AttendanceController::class, 'getWorkingSchedule'])->name('get-working-schedule');
    Route::get('get-break-time', [AttendanceController::class, 'getBreakTime'])->name('get-break-time');
    Route::get('check-attendance-availability', [AttendanceController::class, 'checkAttendanceAvailability'])->name('check-attendance-availability');

    // Attendance
    Route::get('view-attendance', [AttendanceController::class, 'viewAttendance'])->name('view-attendance');
    Route::get('get-attendance-report', [AttendanceController::class, 'getAttendanceReport'])->name('get-attendance-report');

    // Office Hours
    Route::get('office-hours', [AttendanceController::class, 'officeHours'])->name('office-hours');
    Route::post('office-hours-update', [AttendanceController::class, 'updateOfficeHours'])->name('update-office-hours');
    Route::post('update-office-hours-status', [AttendanceController::class, 'updateOfficeHoursStatus'])->name('update-office-hours-status');

    // Holidays
    Route::get('holidays', [AttendanceController::class, 'holidays'])->name('holidays');
    Route::get('holidays/get', [AttendanceController::class, 'getAllHolidays'])->name('get-holidays');
    Route::post('holidays/store', [AttendanceController::class, 'storeHoliday'])->name('save-holiday');
    Route::put('holidays/update/{id}', [AttendanceController::class, 'updateHoliday'])->name('edit-holiday');
    Route::delete('holidays/delete/{id}', [AttendanceController::class, 'deleteHoliday'])->name('delete-holiday');

    // Notifications / email
    Route::get('notifications', [NotificationController::class, 'notifications'])->name('notifications');
    Route::post('send-notifications', [NotificationController::class, 'sendNotifications'])->name('send.notifications');


    // Events Routes
    Route::get('events', [EventController::class, 'event'])->name('events');
    Route::get('events/get', [EventController::class, 'getAllEvents'])->name('get-events');
    Route::post('events/store', [EventController::class, 'storeEvents'])->name('save-event');
    Route::put('events/update/{id}', [EventController::class, 'updateEvent'])->name('edit-event');
    Route::delete('events/delete/{id}', [EventController::class, 'deleteEvent'])->name('delete-event');

});

Route::middleware(['auth', 'role:employee'])->group(function () {

    Route::get('employee-dashboard', [EmployeeDashboardController::class, 'index'])->name('employee-dashboard');
    Route::get('employee-attendance', [EmployeeDashboardController::class, 'attendance'])->name('employee-attendance');
    Route::get('get-employee-attendance-report', [EmployeeDashboardController::class, 'getAttendanceReport'])->name('get-employee-attendance-report');
});

// Route::prefix('')->middleware('auth')->group(function () {

//     // Dashboard
//     Route::get('index', [DashboardController::class, 'index']);
//     Route::post('dashboard-filter', [DashboardController::class, 'filterInterviews'])->name('interviews.filter');

//     // Interviews
//     Route::get('interviews/{day}', [InterviewController::class, 'index']);
//     Route::get('get-interviews/{day}', [InterviewController::class, 'getInterviews'])->name('get-interviews');
//     Route::get('view-interview/{id}', [InterviewController::class, 'view'])->name('view-interview');
//     Route::get('create-interview', [InterviewController::class, 'create']);
//     Route::post('save-interview', [InterviewController::class, 'saveInterview'])->name('save-interview');
//     Route::get('edit-interview/{id}', [InterviewController::class, 'edit'])->name('edit-interview');
//     Route::post('update-interview/{id}', [InterviewController::class, 'update'])->name('update-interview');
//     Route::delete('delete-interview/{id}', [InterviewController::class, 'delete'])->name('delete-interview');
//     Route::post('update-interview-status/{id}', [InterviewController::class, 'updateStatus'])->name('update-interview-status');
//     Route::post('filter-interviews', [InterviewController::class, 'filterInterviews'])->name('filter-interviews');
//     Route::post('send-reminder-email/{id}', [InterviewController::class, 'sendInterviewReminderEmail'])->name('send-reminder-email');

//     // Employees
//     Route::get('employees', [EmployeeController::class, 'index']);
//     Route::get('get-employees', [EmployeeController::class, 'getEmployees'])->name('get-employees');
//     Route::get('view-employee/{id}', [EmployeeController::class, 'view'])->name('view-employee');
//     Route::get('create-employee', [EmployeeController::class, 'create']);
//     Route::post('save-employee', [EmployeeController::class, 'saveEmployee'])->name('save-employee');
//     Route::get('edit-employee/{id}', [EmployeeController::class, 'edit'])->name('edit-employee');
//     Route::post('update-employee/{id}', [EmployeeController::class, 'update'])->name('update-employee');
//     Route::delete('delete-employee/{id}', [EmployeeController::class, 'delete'])->name('delete-employee');
//     Route::post('update-interviewer-status/{id}', [EmployeeController::class, 'updateInterviewerStatus']);
//     Route::post('update-employee-status/{id}', [EmployeeController::class, 'updateEmployeeStatus']);
//     Route::patch('update-attendance-id/{id}', [EmployeeController::class, 'updateAttendanceId']);

//     // Attendance Management
//     Route::get('upload-attendance', [AttendanceController::class, 'uploadAttendance'])->name('upload-attendance');
//     Route::post('process-attendance-file', [AttendanceController::class, 'processAttendanceFile'])->name('process-attendance-file');
//     Route::post('save-attendance', [AttendanceController::class, 'saveAttendance'])->name('save-attendance');
//     Route::get('manage-attendance', [AttendanceController::class, 'manageAttendance'])->name('manage-attendance');
//     Route::get('get-attendance-event', [AttendanceController::class, 'getAttendanceEvent'])->name('get-attendance-event');
//     Route::get('get-attendance', [AttendanceController::class, 'getAttendance'])->name('get-attendance');
//     Route::post('update-attendance', [AttendanceController::class, 'updateAttendance'])->name('update-attendance');
//     Route::get('get-working-hours', [AttendanceController::class, 'getWorkingHours'])->name('get-working-hours');
//     Route::get('get-working-schedule', [AttendanceController::class, 'getWorkingSchedule'])->name('get-working-schedule');
//     Route::get('get-break-time', [AttendanceController::class, 'getBreakTime'])->name('get-break-time');
//     Route::get('check-attendance-availability', [AttendanceController::class, 'checkAttendanceAvailability'])->name('check-attendance-availability');

//     // Attendance
//     Route::get('view-attendance', [AttendanceController::class, 'viewAttendance'])->name('view-attendance');
//     Route::get('get-attendance-report', [AttendanceController::class, 'getAttendanceReport'])->name('get-attendance-report');

//     // Office Hours
//     Route::get('office-hours', [AttendanceController::class, 'officeHours'])->name('office-hours');
//     Route::post('office-hours-update', [AttendanceController::class, 'updateOfficeHours'])->name('update-office-hours');
//     Route::post('update-office-hours-status', [AttendanceController::class, 'updateOfficeHoursStatus'])->name('update-office-hours-status');

//     // Holidays
//     Route::get('holidays', [AttendanceController::class, 'holidays'])->name('holidays');
//     Route::get('holidays/get', [AttendanceController::class, 'getAllHolidays'])->name('get-holidays');
//     Route::post('holidays/store', [AttendanceController::class, 'storeHoliday'])->name('save-holiday');
//     Route::put('holidays/update/{id}', [AttendanceController::class, 'updateHoliday'])->name('edit-holiday');
//     Route::delete('holidays/delete/{id}', [AttendanceController::class, 'deleteHoliday'])->name('delete-holiday');
// });

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route::get('/preview-notification', [NotificationController::class, 'preview'])->name('preview.notification');
//Route::post('/upload', [NotificationController::class, 'uploadimage'])->name('ckeditor.upload');
//Route::post('/upload', [NotificationController::class,'upload']);
