<!DOCTYPE html>
<html>
<head>
    <title>ALINET Appointment Accepted</title>
    <style>
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #e83e8c; padding: 20px; text-align: center; }
        .header img { height: 60px; vertical-align: middle; background:#ffffff; border-radius:6px; padding:3px; }
        .brand-title { color:#ffffff; font-weight:700; font-size:18px; margin-top:8px; font-family: Arial, Helvetica, sans-serif; }
        .content { padding: 28px; color: #333333; font-size: 15px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif; }
        .date { font-size: 16px; color: #004080; font-weight: bold; }
        .footer { background: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666666; font-family: Arial, Helvetica, sans-serif; }
        .kv { font-size:14px; }
        .kv td { padding:4px 0; vertical-align:top; }
        .kv td:first-child { width: 190px; color:#6b7280; }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body style="margin:0; padding:20px; background-color:#f8f9fa;">
    <div class="container">
        <div class="header" style="background:#e83e8c; padding:20px; text-align:center;">
            <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="LC Learning Commons">
            <div class="brand-title">LC MIDES Digital Library</div>
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
                    @if(isset($expiresAt))
                    <div style="margin-top:8px; padding-top:8px; border-top:1px solid #cfe3ff; color:#d97706; font-weight:bold;">
                        ⚠️ Account expires on: {{ $expiresAt }}
                    </div>
                    <div style="font-size:13px; color:#6b7280; margin-top:4px;">
                        This guest account is valid for 7 days. After expiration, you'll need to submit a new ALINET request.
                    </div>
                    @endif
                </div>
                <div style="text-align:center; margin:20px 0;">
                    <a href="{{ route('login') }}" style="display:inline-block; background:#e83e8c; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:6px; font-weight:600; font-size:15px;">Login to Access Resources</a>
                </div>
                <p style="font-size:13px; color:#6b7280; text-align:center;">Click the button above to login with your guest account credentials.</p>
            @else
                <p>Mode: <strong>Onsite</strong></p>
                @if(!empty($appointment->appointment_date))
                    <p>Please visit us on:</p>
                    <p class="date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }} — 8:00am–3:00pm</p>
                @endif

                @if(!empty($appointment->titles_or_topics))
                    <p><strong>Title/s of Resource or Topic/s of Request:</strong></p>
                    <div style="background:#f9fafb; border-left:3px solid #e83e8c; padding:10px 14px; margin:12px 0; white-space:pre-wrap;">{{ $appointment->titles_or_topics }}</div>
                @endif
            @endif

            <p>Requested assistance:</p>
            <ul>
                @forelse((array) $appointment->assistance as $s)
                    <li>{{ $s }}</li>
                @empty
                    <li>—</li>
                @endforelse
            </ul>

            <p>Resource types:</p>
            <ul>
                @forelse((array) $appointment->resource_types as $s)
                    <li>{{ $s }}</li>
                @empty
                    <li>—</li>
                @endforelse
            </ul>

            <!-- Requester information (after resource types) -->
            <table class="kv" width="100%">
                <tr><td>Email</td><td>{{ $appointment->email }}</td></tr>
                <tr><td>Strand/Course</td><td>{{ $appointment->strand_course }}</td></tr>
                <tr><td>Institution/College</td><td>{{ $appointment->institution_college }}</td></tr>
                <tr><td>Requested</td><td>{{ optional($appointment->created_at)->format('F d, Y g:i A') }}</td></tr>
                @if(!empty($appointment->appointment_date))
                    <tr><td>Appointment Date</td><td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}</td></tr>
                @endif
                <tr><td>Mode of Research</td><td>{{ $appointment->mode_of_research }}</td></tr>
            </table>

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


