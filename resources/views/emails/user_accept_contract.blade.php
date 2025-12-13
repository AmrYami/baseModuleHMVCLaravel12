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
        Thank you.,
    </div>

    <div class="content">
        <p>Your contract and required documents have been successfully submitted.</p>

        <p> Our team will review your submission, and you will be contacted soon to arrange your joining.</p>

        <p>In the meantime, please ensure you have taken the required Hajj vaccines:</p>

        <ul>
            <li> Meningitis (ACYW135) .</li>
            <li>Seasonal Influenza vaccine .</li>
            <li>COVID-19 vaccine  .</li>
        </ul>


        <p>Please keep your vaccination records ready, as they will be requested during the onboarding process.</p>

        <p> If you have any questions, feel free to contact us at <a href="mailto:HCMprojects@fakeeh.care">HCMprojects@fakeeh.care</a> .</p>

        <a href="{{ $data['link'] }}" class="btn">
            HCM
        </a>



        <p>Best regards,<br></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>

</body>
</html>
