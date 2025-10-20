<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LiRA Request {{ ucfirst($decision) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,date=no,address=no,email=no,url=no">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; margin:0; padding:0; background-color:#f8f9fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa; padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                    
                    <!-- Header with Logos -->
                    <tr>
                                                <td align="center" style="padding:20px; background-color:#004080;">
                                                        <img src="{{ asset('images/LCCDO.png') }}" alt="Institution" style="height:60px; margin-right:15px;">
                                                        <img src="{{ asset('images/learningcommons.png') }}" alt="Learning Commons" style="height:60px;">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#333333; font-size:15px; line-height:1.6;">
                            <p>Dear <strong>{{ $lira->first_name }}</strong>,</p>

                            @if($decision === 'accepted')
                                <p>Your <strong>LiRA request</strong> has been <span style="color:green; font-weight:bold;">accepted</span>. We will process it shortly.</p>
                                <p style="color:#374151; margin-top:6px;">Expect a response within <strong>3 to 5 working days</strong>.</p>
                            @else
                                <p>We’re sorry to inform you that your <strong>LiRA request</strong> has been <span style="color:#c1121f; font-weight:bold;">rejected</span>.</p>
                                @if(!empty($reason))
                                    <p style="background:#fff1f2; border:1px solid #fecdd3; padding:12px 14px; border-radius:6px; color:#9f1239;">
                                        <strong>Reason:</strong> {{ $reason }}
                                    </p>
                                @endif
                                <p style="color:#6b7280;">If you have questions or need assistance, feel free to reply to this email or contact the LC Learning Commons.</p>
                            @endif

                            <!-- Request details -->
                            <div style="margin-top:20px;">
                                <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#374151;">
                                    <tr>
                                        <td style="width:180px; color:#6b7280; padding:6px 0;">Submitted</td>
                                        <td style="padding:6px 0;">{{ optional($lira->created_at)->format('F d, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#6b7280; padding:6px 0;">Action</td>
                                        <td style="padding:6px 0;">{{ $lira->action ?? '-' }}</td>
                                    </tr>
                                    @if(!empty($lira->for_borrow_scan))
                                    <tr>
                                        <td style="color:#6b7280; padding:6px 0;">For borrow/scan</td>
                                        <td style="padding:6px 0;">{{ $lira->for_borrow_scan }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($lira->titles_of))
                                    <tr>
                                        <td style="color:#6b7280; padding:6px 0;">Titles/Topics</td>
                                        <td style="padding:6px 0;">{{ $lira->titles_of }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($lira->for_list))
                                    <tr>
                                        <td style="color:#6b7280; padding:6px 0;">For list</td>
                                        <td style="padding:6px 0;">{{ $lira->for_list }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <p style="margin-top:25px;">Sincerely,<br>
                                <strong>LC Learning Commons</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background:#f1f1f1; padding:15px; font-size:12px; color:#666;">
                            This is an automated message. Please do not share sensitive information via email.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>