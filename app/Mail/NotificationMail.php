<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailmessage;
    public $employee;
    public $hrName;
    public  $companyName;
    public $subject;

    public function __construct($message, $employee, $subject)
    {
        $this->subject = $subject;
        $this->mailmessage = $message;
        $this->employee = $employee;
//        $this->hrName = $hrName;
//        $this->companyName = $companyName;
    }

    public function build()
    {
        return $this->subject($this->subject)->view('emails.notificationmail');
    }
}
