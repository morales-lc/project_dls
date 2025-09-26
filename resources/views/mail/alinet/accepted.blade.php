<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALINET Appointment Accepted</title>
    <style>
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #004080; padding: 20px; text-align: center; }
        .header img { height: 60px; vertical-align: middle; }
        .content { padding: 28px; color: #333333; font-size: 15px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif; }
        .date { font-size: 16px; color: #004080; font-weight: bold; }
        .footer { background: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666666; font-family: Arial, Helvetica, sans-serif; }
        .btn { display: inline-block; padding: 10px 16px; background: #1976d2; color: #ffffff !important; text-decoration: none; border-radius: 6px; margin-top: 10px; }
    </style>
</head>
<body style="margin:0; padding:20px; background-color:#f8f9fa;">
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/lourdes_college.jpg')) }}" alt="Lourdes College" style="margin-right:15px;">
            <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="Learning Commons">
        </div>
        <div class="content">
            <p>Dear <strong>{{ $appointment->firstname }}</strong>,</p>
            <p>Your <strong>ALINET appointment request</strong> has been <span style="color:green; font-weight:bold;">accepted</span>.</p>
            <p>Please visit us on your scheduled date:</p>
            <p class="date">{{ $appointment->appointment_date->format('F d, Y') }}</p>
            <p>We look forward to assisting you.</p>
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


