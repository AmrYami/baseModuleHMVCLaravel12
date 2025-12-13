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
        Dear Respected Candidate,
    </div>

    <div class="content">
        <p>Thank you for your interest in joining our medical team for the Hajj projects.</p>

        <p>We are pleased to inform you that you have been proceeded to the next stage of the selection process. As part
            of this stage, you are required to complete a competency exam to evaluate your clinical knowledge and
            practical understanding.</p>


        <p>Please access the exam through the following link:</p>

        <a href="{{ $data['examLink'] }}" class="btn">
            Access the Exam
        </a>

        <p> To assist with your preparation, the exam will reference materials from: </p>
        <ul>
            <li> Mosby’s Paramedic Textbook</li>
            <li> ACLS (Advanced Cardiovascular Life Support) Course</li>
            <li> BLS (Basic Life Support) Course</li>
            <li> PHTLS (Prehospital Trauma Life Support) Course</li>
            <li> NRP (Neonatal Resuscitation Program) Course</li>
            <li> PALS (Pediatric Advanced Life Support) Course</li>
        </ul>
        <p>Should you have any questions or require further clarification, feel free to contact us.</p>

        <p>Thanks,<br></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>

</body>
</html>
