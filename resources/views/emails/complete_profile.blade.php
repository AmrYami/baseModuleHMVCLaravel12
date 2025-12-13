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
        <p>We noticed that you have not yet completed your application to join our medical teams for the Hajj project. </p>

        <p>

            This is a reminder that ACLS certification is NOT required at this stage, so you can proceed with your application even if you don’t have it.
        </p>

        <p> We are currently hiring for the following positions: </p>

        <ul>
            <li>
                Paramedics (Diploma or Bachelor holders)
            </li>
            <li>
                Nurses
            </li>
            <li>
                General Practitioners (GPs)
            </li>
        </ul>
        <p> Nature of Work: </p>
        <p>
            You will be part of a mobile medical team providing care and first aid to pilgrims during the Hajj season. Teams will be stationed across designated areas in Makkah, working 12-hour shifts for 10 days, in addition to mandatory training before deployment. This is a meaningful opportunity to serve the pilgrims during one of the world’s largest humanitarian gatherings.
        </p>

        <p> Compensation: </p>
        <ul>
            <li>
                12,000 SAR total for Bachelor degree holders (120 hours)
            </li>
            <li>
                10,000 SAR total for Diploma holders (120 hours)
            </li>
            <li>
                This includes work during the 10 Hajj days and mandatory training days
            </li>
        </ul>


        <p> We encourage you to complete your application as soon as possible to secure your place.  <a href="hcm.fakeeh.care">HCM</a> .</p>

        <p> For any questions or support, feel free to contact us.  <a href="mailto:hcmprojects@fakeeh.care">HCMprojects@fakeeh.care</a>  with the subject line: Password Reset </p>
        <p> Best regards, </p>

        <hr>


        <p>عزيزي المتقدم،</p>
        <p>
            نلاحظ أنك لم تُكمل طلبك للانضمام إلى الفرق الطبية في مشروع الحج حتى الآن.
        </p>

        <p>
            نود تذكيرك بأن شهادة ACLS ليست مطلوبة في هذه المرحلة، ويمكنك إكمال طلبك بدونها.
        </p>

        <p> نقوم حالياً بالتوظيف في الوظائف التالية: </p>

        <ul>
            <li>
                فنيي طب طوارئ (دبلوم أو بكالوريوس)
            </li>
            <li>
                التمريض
            </li>
            <li>
                الأطباء العموميين (GPs)

            </li>
        </ul>
        <p> طبيعة العمل:</p>
        <p>
            ستكون ضمن فريق طبي ميداني متنقل يقدّم الرعاية والإسعافات الأولية للحجاج خلال موسم الحج. سيتم توزيع الفرق في مواقع محددة داخل مكة المكرمة، بنظام نوبات يومية 12 ساعة لمدة 10 أيام، بالإضافة إلى تدريب إلزامي قبل بدء المهمة. إنها فرصة عظيمة للمشاركة في مهمة إنسانية مميزة لخدمة ضيوف الرحمن.

        </p>

        <p> المكافأة: </p>
        <ul>
            <li>
                12,000 ريال سعودي لحملة البكالوريوس (120 ساعة)
            </li>
            <li>
                10,000 ريال سعودي لحملة الدبلوم (120 ساعة)
            </li>
            <li>
                تشمل المكافأة أيام العمل العشرة خلال الحج بالإضافة إلى التدريب الإلزامي
            </li>
        </ul>


        <p> نرجو منك استكمال طلبك في أقرب وقت لضمان فرصتك.  <a href="hcm.fakeeh.care">HCM</a> .</p>

        <p> لأي استفسار، لا تتردد في التواصل معنا  <a href="mailto:hcmprojects@fakeeh.care">HCMprojects@fakeeh.care</a>  with the subject line: Password Reset </p>

    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>

</body>
</html>
