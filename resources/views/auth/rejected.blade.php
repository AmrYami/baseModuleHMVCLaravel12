<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Rejected</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,"Helvetica Neue",Arial; background:#f6f7f9; margin:0}
        .wrap{min-height:100vh; display:flex; align-items:center; justify-content:center;}
        .card{background:#fff; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,.08); padding:28px; width:min(720px, calc(100% - 32px));}
        .title{font-weight:600; font-size:22px; margin:0 0 8px}
        .muted{color:#6b7280; margin:0 0 16px}
        .danger{color:#b91c1c;}
        .actions{display:flex; gap:10px; margin-top:16px}
        .btn{padding:10px 14px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; text-decoration:none; color:#111827}
        .btn-primary{background:#2563eb; color:#fff; border-color:#2563eb}
        .note{background:#fff1f2; color:#9f1239; border:1px solid #fecdd3; padding:10px 12px; border-radius:8px; margin-top:12px}
        .small{font-size:12px; color:#6b7280}
    </style>
    @vite(['resources/js/app.js'])
</head>
<body>
    <div class="wrap">
    <div class="card">
        <h1 class="title danger">Application Rejected</h1>
        <p class="muted">Your account is currently marked as rejected by the entrance exam policy.</p>
        <div class="note">Please reach out to an administrator if you believe this is a mistake.</div>
        <div class="actions">
            <a class="btn btn-primary" href="{{ route('assessments.candidate.exams.index') }}">Go to Exams</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="btn" type="submit">Logout</button>
            </form>
        </div>
        <p class="small">If you believe this is a mistake, please contact support.</p>
    </div>
    </div>
</body>
</html>
