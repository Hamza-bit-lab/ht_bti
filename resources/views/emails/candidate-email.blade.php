<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Candidate Invitation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .logo-container {
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 50px;
            margin-bottom: 50px;
            background-color: #ffffff;
            box-shadow: 1px 1px 15px 0px rgba(0, 0, 0, 0.5);
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
        }

        .header {
            background-color: #e77816;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .header h1 {
            font-size: 24px;
        }

        .content {
            padding: 20px;
        }

        .details {
            font-size: 16px;
        }

        .signature {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-container text-center">
            <img class="logo" src="{{ $message->embed(public_path() . '/logo/logo.png') }}" alt="" />
        </div>
        <div class="header">
            <h1>Interview Invitation</h1>
        </div>
        <div class="content">
            <p>Dear {{ $candidateName }},</p>
            <p>
                We are excited to inform you that your interview for the
                position of <b>{{ $title }}</b> with <b>{{ $companyName }}</b> has been
                scheduled. We appreciate your interest in joining our team
                and look forward for the interview.
            </p>
            <div class="details">
                <p><strong>Interview Date:</strong> {{ $date }}</p>
                @if ($meeting_url)
                    <p><strong>Meeting URL:</strong> {{ $meeting_url }}</p>
                @else
                    <p><strong>Location:</strong> {{ $location }}</p>
                @endif
                <p><strong>Interviewer:</strong> {{ $interviewerName }}</p>
            </div>
            @if ($meeting_url)
                <p>
                    Ensure that you have a stable internet connection and
                    access to the necessary video conferencing platform.
                </p>
            @else
                <p>
                    Please make sure to arrive on time,
                    we are looking forward for the interview.
                </p>
            @endif
            <p class="signature">
                Best regards,<br />{{ $hrName }}<br />HR<br />{{ $companyName }}
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
