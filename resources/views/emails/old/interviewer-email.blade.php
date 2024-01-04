<!DOCTYPE html>
<html>
<head>
    <title>Interview Invitation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f2f2f2; margin: 0; padding: 0;">
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h3>Interview Invitation</h3>
            </div>
            <div class="card-body">
                <p class="lead">Dear {{ $interviewerName }},</p>
                <p class="h5">You have been appointed as an interviewer:</p>
                <ul class="list-unstyled">
                    <li>
                        <strong>Candidate:</strong> {{ $candidateName }}
                    </li>
                    <li>
                        <strong>Title:</strong> {{ $title }}
                    </li>
                    <li>
                        <strong>Interview Time:</strong> {{ $date }}
                    </li>
                    @if ($meeting_url)
                    <li>
                        <strong>Meeting URL:</strong> <a href="{{ $meeting_url }}">{{ $meeting_url }}</a>
                    </li>
                    @else
                    <li>
                        <strong>Interview Location:</strong> {{ $location }}
                    </li>
                    @endif
                </ul>
                <p class="h5">Please follow the provided instructions for the interview.</p>
                <p class="text-center">Best regards,</p>
                <p class="text-center">HR,</p>
                <p class="text-center">Brown Tech Int.</p>
            </div>
        </div>
    </div>
</body>
</html>
