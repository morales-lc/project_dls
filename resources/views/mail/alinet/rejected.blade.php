<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALINET Appointment Rejected</title>
    <style>
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #e83e8c; padding: 20px; text-align: center; }
        .header img { height: 60px; vertical-align: middle; background:#ffffff; border-radius:6px; padding:3px; }
        .brand-title { color:#ffffff; font-weight:700; font-size:18px; margin-top:8px; font-family: Arial, Helvetica, sans-serif; }
        .content { padding: 28px; color: #333333; font-size: 15px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif; }
        .footer { background: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666666; font-family: Arial, Helvetica, sans-serif; }
        .btn { display: inline-block; padding: 10px 16px; background: #6c757d; color: #ffffff !important; text-decoration: none; border-radius: 6px; margin-top: 10px; }
        .kv { font-size:14px; }
        .kv td { padding:4px 0; vertical-align:top; }
        .kv td:first-child { width: 190px; color:#6b7280; }
    </style>
</head>
<body style="margin:0; padding:20px; background-color:#f8f9fa;">
    <div class="container">
        <div class="header" style="background:#e83e8c; padding:20px; text-align:center;">
            <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="LC Learning Commons">
            <div class="brand-title">LC MIDES Digital Library</div>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $appointment->firstname }}</strong>,</p>
            <p>We regret to inform you that your <strong>ALINET appointment request</strong> has been <strong>rejected</strong>.</p>
            <table class="kv" width="100%">
                <tr><td>Email</td><td>{{ $appointment->email }}</td></tr>
                <tr><td>Strand/Course</td><td>{{ $appointment->strand_course }}</td></tr>
                <tr><td>Institution/College</td><td>{{ $appointment->institution_college }}</td></tr>
                <tr><td>Requested</td><td>{{ optional($appointment->created_at)->format('F d, Y g:i A') }}</td></tr>
                <tr><td>Mode requested</td><td>{{ $appointment->mode_of_research }}</td></tr>
            </table>
            @if(!empty($reason))
            <div style="background:#fff7f7; border:1px solid #f3d6d6; padding:12px 14px; border-radius:6px; margin:12px 0; color:#8a2a2a;">
                <div style="font-weight:bold; margin-bottom:6px;">Reason:</div>
                <div style="white-space:pre-wrap;">{{ $reason }}</div>
            </div>
            @endif
            <p>If you have questions or would like to reschedule, please contact the library.</p>
            <p style="margin-top:24px;">
                Sincerely,<br>
                <strong>Lourdes College Library</strong>
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Lourdes College Library — All rights reserved
        </div>
    </div>
    <div style="text-align:center; font-size:12px; color:#888; margin-top:8px; font-family: Arial, Helvetica, sans-serif;">
        If images don’t load, please enable images or add us to your safe senders.
    </div>
</body>
<!-- Images referenced from public/images/learningcommons.png and public/images/lourdes_college.png -->
</html>


