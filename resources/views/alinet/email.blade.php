<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New ALINET Appointment Request</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background:#f8f9fa;}
        table{border-collapse:collapse;}
        .container{width:100%;padding:20px 0;background:#f8f9fa;}
        .card{width:600px;max-width:95%;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05);}        
        .header{background:#e83e8c;text-align:center;padding:20px;}
        .header img{height:60px;background:#ffffff;border-radius:6px;padding:4px;}
        .brand{color:#fff;font-weight:700;font-size:18px;margin-top:8px;}
        .content{padding:28px;color:#333;font-size:15px;line-height:1.7;}
        .footer{background:#f1f1f1;padding:15px;font-size:12px;color:#666;text-align:center;}
        .muted{color:#6b7280;}
        .kv td{padding:4px 0;vertical-align:top;}
        .kv td:first-child{color:#6b7280;width:190px;}
    </style>
    </head>
<body>
    <table class="container" width="100%" cellpadding="0" cellspacing="0">
        <tr><td align="center">
            <table class="card" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header">
                        <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="LC Learning Commons">
                        <div class="brand">LC MIDES Digital Library</div>
                    </td>
                </tr>
                <tr><td class="content">
                    <h2 style="margin:0 0 12px 0;font-size:20px;">New ALINET Appointment Request</h2>

                    <table class="kv" width="100%" style="font-size:14px;">
                        <tr><td>Name</td><td>{{ $appointment->prefix ? $appointment->prefix . ' ' : '' }}{{ $appointment->firstname }} {{ $appointment->lastname }}</td></tr>
                        <tr><td>Email</td><td>{{ $appointment->email }}</td></tr>
                        <tr><td>Strand/Course</td><td>{{ $appointment->strand_course }}</td></tr>
                        <tr><td>Institution/College</td><td>{{ $appointment->institution_college }}</td></tr>
                        <tr><td>Requested</td><td>{{ optional($appointment->created_at)->format('F d, Y g:i A') }}</td></tr>
                        <tr><td>Appointment Date</td><td>
                            @if(!empty($appointment->appointment_date))
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
                            @else
                                —
                            @endif
                        </td></tr>
                        <tr><td>Mode of Research</td><td>{{ $appointment->mode_of_research }}</td></tr>
                    </table>

                    <div style="margin-top:14px;font-weight:600;">Titles/Topics</div>
                    <div style="white-space:pre-wrap;">{{ $appointment->titles_or_topics }}</div>

                    <div style="margin-top:14px;font-weight:600;">Assistance Requested</div>
                    <ul style="margin-top:6px;">
                        @foreach((array) $appointment->assistance as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <div style="margin-top:8px;font-weight:600;">Resource Types</div>
                    <ul style="margin-top:6px;">
                        @foreach((array) $appointment->resource_types as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <div style="text-align:center; margin:24px 0 12px 0;">
                        <a href="{{ route('alinet.manage') }}" style="display:inline-block; background:#e83e8c; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:6px; font-weight:600; font-size:15px;">Manage ALINET Appointments</a>
                    </div>
                    <p style="font-size:12px; color:#6b7280; text-align:center; margin:0;">Login required to view and manage appointments.</p>
                </td></tr>
                <tr><td class="footer">Automated notification from LC Learning Commons</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>