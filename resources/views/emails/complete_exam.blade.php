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
        <p>Dear Applicant,</p>
        <p>Thank you for applying to join our medical teams for the Hajj projects.</p>
        <p>We’re pleased to inform you that you have already met the initial criteria and are progressing well in the selection process. The next step is to complete the assessment, which is mandatory for all shortlisted candidates.</p>
        <p>Completing this step is required to move forward and be considered for an offer. We encourage you to take a few moments to finalize it as soon as possible.</p>

        <p> please visit: <a href="hcm.fakeeh.care">HCM</a> .</p>
        <p> Your expertise is valued, and we look forward to having you as part of this national mission.</p>

        <hr>


        <p>عزيزي المتقدم،</p>
        <p>
            شكرًا لتقديمك للانضمام إلى فرقنا الطبية في مشاريع موسم الحج.
        </p>
        <p>
            يسرّنا إبلاغك بأنك قد اجتزت بالفعل المعايير الأولية، والخطوة التالية هي إكمال التقييم، والذي يُعد إجباريًا لجميع المرشحين المدرجين في القائمة المختصرة.
        </p>
        <p>إكمال هذه الخطوة ضروري للانتقال إلى المرحلة التالية. نشجّعك على تخصيص بضع دقائق لإتمامه في أقرب وقت ممكن.</p>
        <p>يُرجى زيارة الرابط التالي: <a href="hcm.fakeeh.care">HCM</a></p>
        {{--        <a href="{{ url('/login') }}" class="btn">Login Now</a>--}}

        <p>نُقدّر خبراتك، ونتطلع إلى انضمامك إلى هذه المهمة الوطنية.</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>

</body>
</html>
