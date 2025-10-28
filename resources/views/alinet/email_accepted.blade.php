<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Accepted</title>
    <style>
        .header{background:#e83e8c;text-align:center;padding:20px;}
        .header img{height:60px;background:#ffffff;border-radius:6px;padding:4px;}
        .brand{color:#ffffff;font-weight:700;font-size:18px;margin-top:8px;font-family:Arial,Helvetica,sans-serif;}
        .kv td{padding:4px 0;vertical-align:top;}
        .kv td:first-child{color:#6b7280;width:190px;}
    </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; margin:0; padding:0; background-color:#f8f9fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa; padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" class="header">
                            <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="LC Learning Commons">
                            <div class="brand">LC MIDES Digital Library</div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#333333; font-size:15px; line-height:1.6;">
                            <p>Dear <strong>{{ $appointment->firstname }}</strong>,</p>

                            <p>
                                Your <strong>ALINET appointment request</strong> has been 
                                <span style="color:green; font-weight:bold;">accepted</span>.
                            </p>

                            <table class="kv" width="100%" style="font-size:14px;">
                                <tr><td>Email</td><td>{{ $appointment->email }}</td></tr>
                                <tr><td>Strand/Course</td><td>{{ $appointment->strand_course }}</td></tr>
                                <tr><td>Institution/College</td><td>{{ $appointment->institution_college }}</td></tr>
                                <tr><td>Requested</td><td>{{ optional($appointment->created_at)->format('F d, Y g:i A') }}</td></tr>
                                @if(!empty($appointment->appointment_date))
                                <tr><td>Appointment Date</td><td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}</td></tr>
                                @endif
                                <tr><td>Mode of Research</td><td>{{ $appointment->mode_of_research }}</td></tr>
                            </table>

                            <p style="margin-top:14px;">We look forward to assisting you!</p>

                            <p style="margin-top:25px;">
                                Sincerely,<br>
                                <strong>Lourdes College Library</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background:#f1f1f1; padding:15px; font-size:12px; color:#666;">
                            © {{ date('Y') }} Lourdes College Library - All Rights Reserved
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
