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
            @if(($appointment->mode_of_research ?? '') === 'Online (Virtual)')
                <p>Mode: <strong>Online (Virtual)</strong></p>
                <p>Please use the following guest account to access the resources:</p>
                <div style="background:#f1f7ff; border:1px solid #cfe3ff; padding:12px 14px; border-radius:6px; margin:12px 0;">
                    <div><strong>Email:</strong> {{ $guestEmail ?? config('services.alinet.guest_email', 'guest@example.com') }}</div>
                    <div><strong>Password:</strong> {{ $guestPassword ?? config('services.alinet.guest_password', 'guest12345') }}</div>
                </div>
                <p>Requested assistance:</p>
                <ul>
                    @foreach((array) $appointment->assistance as $s)
                    <li>{{ $s }}</li>
                    @endforeach
                </ul>
                <p>Resource types:</p>
                <ul>
                    @foreach((array) $appointment->resource_types as $s)
                    <li>{{ $s }}</li>
                    @endforeach
                </ul>
            @else
                <p>Mode: <strong>Onsite</strong></p>
                @if(!empty($appointment->appointment_date))
                    <p>Please visit us on:</p>
                    <p class="date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }} — 8:00am–3:00pm</p>
                @endif
                <p>Requested assistance:</p>
                <ul>
                    @foreach((array) $appointment->assistance as $s)
                    <li>{{ $s }}</li>
                    @endforeach
                </ul>
                <p>Resource types:</p>
                <ul>
                    @foreach((array) $appointment->resource_types as $s)
                    <li>{{ $s }}</li>
                    @endforeach
                </ul>
            @endif
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


