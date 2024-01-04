<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notification</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .logo-container {
            text-align: center;
        }

        .logo {
            width: 100px;
            height: auto;
        }

        .header {
            background-color: #e77816;
            color: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .content {
            padding: 20px;
            text-align: justify;
            line-height: 1.6;
        }

        .content p {
            margin-bottom: 15px;
        }

        .signature {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            margin-top: 20px;
        }

        .social-icons {
            margin-top: 10px;
        }

        .social-icons a {
            display: inline-block;
            margin: 0 10px;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
        }
        .msga{
            line-height: 0.3em;
        }
    </style>
</head>

<body>
<div class="container" style="background-color: #eeeaea">
    <div class="logo-container text-center">
        <img class="logo" src="{{ $message->embed(public_path('/logo/logo.png')) }}" alt="Company Logo" />
    </div>
    <div class="header">
        <h1>{{ $subject }}</h1>
    </div>
    <div class="content">
        <p>Dear {{ $employee->name }},</p>
        <div class="msga">{!! $mailmessage !!}</div>
    </div>
    <div class="footer">
        <div class="social-icons">
            <a href="https://www.facebook.com/browntechint/" target="_blank">Facebook</a>
            <a href="https://www.instagram.com/browntechint/?igshid=YmMyMTA2M2Y%3D" target="_blank">Instagram</a>
            <a href="https://www.linkedin.com/company/browntech/" target="_blank">LinkedIn</a>
        </div>
        <p>&copy; 2024 <a href="https://www.browntech.co/" class="text-white" target="_blank">Brown Tech Int</a>. All rights reserved.</p>
    </div>
</div>
</body>

</html>
