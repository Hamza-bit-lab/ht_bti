<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $companyName;
    public $hrName;
    public $interviewDetails;
    public $interviewerName;
    public $candidateName;
    public $title;
    public $date;
    public $location;
    public $meeting_url;

    public function __construct($interviewDetails)
    {
        $this->hrName = "Yusra";
        $this->companyName = "Brown Tech Int";
        $this->interviewDetails = $interviewDetails;
        $this->interviewerName = $interviewDetails['interviewerData']->name;
        $this->candidateName = $interviewDetails['interviewData']->name;
        $this->title = $interviewDetails['interviewData']->title;
        $this->date = $interviewDetails['interviewData']->date;
        $this->location = "Office-47, 2nd Floor, Big City Plaza, Liberty Rounabout, Lahore";
        $this->meeting_url = $interviewDetails['interviewData']->meeting_url;
    }

    public function build()
    {
        return $this->view($this->interviewDetails['view'])
            ->subject($this->interviewDetails['subject']);
    }
}

