<?php

namespace App\Jobs;

use App\Mail\InterviewEmail;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInterviewReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $interview;

    public function __construct($interview)
    {
        $this->interview = $interview;
    }

    public function handle()
    {
        $interview = $this->interview;
        $interviewer = Employee::where('id', $interview->interviewer)->first();

        $candidateDetails=[
            'subject' => 'Interview Reminder',
            'view' => 'emails.candidate-reminder',
            'interviewData' => $interview,
            'interviewerData' => $interviewer,
        ];
        $candidateEmail = new InterviewEmail($candidateDetails);
        Mail::to($interview->email)->send($candidateEmail);

        $interviewerDetails=[
            'subject' => 'Interview Reminder',
            'view' => 'emails.interviewer-reminder',
            'interviewData' => $interview,
            'interviewerData' => $interviewer,
        ];
        $interviewerEmail = new InterviewEmail($interviewerDetails);
        Mail::to($interviewer->email)->send($interviewerEmail);
    }
}
