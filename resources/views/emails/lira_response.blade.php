<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="font-family: Arial, Helvetica, sans-serif; margin:0; padding:0; background-color:#f8f9fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa; padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td align="center" style="padding:20px; background-color:#004080;">
                            <img src="{{ $message->embed(public_path('images/lourdes_college.jpg')) }}" alt="Lourdes College" style="margin-right:15px;">
                            <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="Learning Commons">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px; color:#333333; font-size:15px; line-height:1.6;">
                            <p>{!! nl2br(e($body)) !!}</p>
                            <div style="margin-top:20px;">
                                <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#374151;">
                                    <tr>
                                        <td style="width:180px; color:#6b7280; padding:6px 0;">Request submitted</td>
                                        <td style="padding:6px 0;">{{ optional($lira->created_at)->format('F d, Y g:i A') }}</td>
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