<?php

namespace App\Jobs;

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
        Mail::to($this->employee->email)->send(new NotificationMail($this->message, $this->employee, $this->subject));
    }
}
