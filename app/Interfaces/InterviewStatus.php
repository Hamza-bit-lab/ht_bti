<?php

namespace App\Interfaces;

interface InterviewStatus
{
    public const STATUSES = [
        'Pending',
        'Scheduled',
        'Shortlisted',
        'Rejected',
        'Offer Sent',
        'Offer Accepted',
        'Offer Rejected',
        'Canceled',
    ];

    public static function getStyle(string $status): string;
}
