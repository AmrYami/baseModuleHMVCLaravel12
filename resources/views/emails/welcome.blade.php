<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 20px;
            font-size: 16px;
            color: #333;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #666;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        Welcome to {{ config('app.name') }}
    </div>

    <div class="content">
        <p>Hello {{ $data['name'] }},</p>
        <p>Welcome to <strong>{{ config('app.name') }}</strong>! Your account has been successfully created.</p>
        <p>Here are your login details:</p>

        <ul>
            <li><strong>Username:</strong> {{ $data['user_name'] }}</li>
            <li><strong>Email:</strong> {{ $data['email'] }}</li>
        </ul>

        <p>You can log in using the button below:</p>
        <a href="{{ url('/login') }}" class="btn">Login Now</a>

{{--        <p>For security reasons, please change your password after logging in.</p>--}}

        <p>Best regards,<br> {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>

</body>
</html>
