<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $eventmessage;
    public $title;

    public function __construct(Employee $employee, $eventmessage, $title)
    {
        $this->employee = $employee;
        $this->eventmessage = $eventmessage;
        $this->title = $title;
    }

    public function build()
    {
        return $this->subject($this->title)->view('emails.event_notification', [
            'employee' => $this->employee,
            'eventmessage' => $this->eventmessage,
            'title' => $this->title,
        ]);
    }
}
