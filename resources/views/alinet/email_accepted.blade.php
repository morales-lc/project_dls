<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Accepted</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; margin:0; padding:0; background-color:#f8f9fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa; padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                    
                    <!-- Header with Logos -->
                    <tr>
                        <td align="center" style="padding:20px; background-color:#004080;">
                            <img src="{{ asset('images/lourdes_college.png') }}" alt="Lourdes College" style="height:60px; margin-right:15px;">
                            <img src="{{ asset('images/learningcommons.png') }}" alt="Learning Commons" style="height:60px;">
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

                            <p>
                                Please visit us on your scheduled date:<br>
                                <strong style="font-size:16px; color:#004080;">
                                    {{ $appointment->appointment_date->format('F d, Y') }}
                                </strong>
                            </p>

                            <p>We look forward to assisting you!</p>

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
