<?php

namespace App\Jobs;

use App\Mail\EventNotificationMail;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventEmails implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Dispatchable;

    protected $employee;
    protected $eventmessage;
    protected $title;

    public function __construct($employee, $title, $eventmessage)
    {
        $this->employee = $employee;
        $this->title = $title;
        $this->eventmessage = $eventmessage;
    }

    public function handle()
    {
        Mail::to($this->employee->email)->send(new EventNotificationMail(
            $this->employee,
            $this->eventmessage,
            $this->title,
        ));
    }
}
