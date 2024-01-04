<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Interviewer Invitation</title>
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
            <p>Dear {{ $interviewerName }},</p>
            <p>
                We would like to inform you about your upcoming role as an
                interviewer for an important job interview at {{ $companyName }}.
                Your participation in the interview process is
                greatly appreciated, and your insights will be valuable in
                selecting the right candidate for the {{ $title }} position.
            </p>
            <div class="details">
                <p><strong>Candidate:</strong> {{ $candidateName }}</p>
                <p><strong>Interview Date:</strong> {{ $date }}</p>
                @if ($meeting_url)
                    <p><strong>Meeting URL:</strong> {{ $meeting_url }}</p>
                @else
                    <p><strong>Location:</strong> {{ $location }}</p>
                @endif
            </div>
            <p>
                Please make sure to be prepared for the interview. Review
                the candidate's resume and any relevant information
                beforehand, and have your questions and evaluation criteria
                ready.
            </p>
            <p class="signature">
                Best regards,<br />{{ $hrName }}<br />HR<br />{{ $companyName }}
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
