<?php

namespace App\Interfaces;
class InterviewStatusMethods implements InterviewStatus
{
    public static function getStyle(string $status): string
    {
        switch ($status) {
            case 'Pending':
            case 'Scheduled':
                return 'bg-warning';
            case 'Shortlisted':
            case 'Offer Sent':
            case 'Offer Accepted':
                return 'bg-success';
            case 'Rejected':
            case 'Offer Rejected':
            case 'Canceled':
                return 'bg-danger';
            default:
                return '';
        }
    }
}
