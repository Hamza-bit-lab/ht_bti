<?php

namespace App\Jobs;

use App\Mail\EventNotificationMail;
use App\Models\Employee;
use App\Mail\NotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $message;
    protected $subject;

    public function __construct(Employee $employee, $message, $subject)
    {
        $this->employee = $employee;
        $this->message = $message;
        $this->subject = $subject;
    }

    public function handle()
    {
        try {
            $activeEmployees = Employee::where('is_employed', 1)->get();

            foreach ($activeEmployees as $employee) {
                Mail::to($employee->email)->send(new EventNotificationMail($employee, 'Event Notification'));
            }
        } catch (\Exception $exception) {
            // Log the exception to the Laravel log
            \Log::error('Error sending event notification emails: ' . $exception->getMessage());
            throw $exception;
        }
    }

}
