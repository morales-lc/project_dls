<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LiRA Request {{ ucfirst($decision) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* same CSS as above */
        body {font-family: Arial, Helvetica, sans-serif;margin:0;padding:0;background:#f8f9fa;}
        table {border-collapse: collapse;}
        .email-container{width:100%;padding:20px 0;background:#f8f9fa;}
        .email-content{width:600px;max-width:95%;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05);}
    .header{background:#e83e8c;text-align:center;padding:20px;}
    .header img{width:120px;height:auto;margin:0 8px;background:#ffffff;border-radius:6px;padding:4px;}
    .brand-title{color:#ffffff;font-weight:700;font-size:18px;margin-top:8px;font-family:Arial,Helvetica,sans-serif;}
        .content{padding:30px;color:#333;font-size:15px;line-height:1.7;}
        .footer{background:#f1f1f1;padding:15px;font-size:12px;color:#666;text-align:center;}
        @media(max-width:600px){.content{padding:20px;}.header img{width:100px;}}
    </style>
</head>

<body>
    <table class="email-container"><tr><td align="center">
        <table class="email-content">
            <tr>
                <td class="header" style="background:#e83e8c;text-align:center;padding:20px;">
                    <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="LC Learning Commons">
                    <div class="brand-title">LC MIDES Digital Library</div>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p>Dear <strong>{{ $lira->first_name }}</strong>,</p>

                    @if($decision === 'accepted')
                    <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:12px 14px;border-radius:8px;margin:10px 0 16px 0;">
                        <strong>Status:</strong> Accepted — We will process your request shortly. Expect a response within <strong>3–5 working days</strong>.
                    </div>
                    @else
                    <div style="background:#fff1f2;border:1px solid #fecdd3;color:#9f1239;padding:12px 14px;border-radius:8px;margin:10px 0 12px 0;">
                        <strong>Status:</strong> Rejected
                    </div>
                    @if(!empty($reason))
                    <div style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;padding:12px 14px;border-radius:8px;margin:8px 0 14px 0;">
                        <strong>Reason provided:</strong><br>{{ $reason }}
                    </div>
                    @endif
                    <p style="color:#6b7280;">If you have questions, feel free to reply to this email or contact LC Learning Commons.</p>
                    @endif

                    <div style="margin-top:20px;font-weight:600;color:#111827;">Your Request Details</div>
                    <table width="100%" style="margin-top:10px;font-size:14px;color:#374151;">
                        <tr><td width="180" style="color:#6b7280;">Submitted</td><td>{{ optional($lira->created_at)->format('F d, Y g:i A') }}</td></tr>
                        <tr><td style="color:#6b7280;">Action</td><td>{{ $lira->action ?? '-' }}</td></tr>
                        @if(!empty($lira->assistance_types))
                        <tr><td style="color:#6b7280;">Assistance types</td>
                            <td>{{ is_array($lira->assistance_types) ? implode(', ', $lira->assistance_types) : $lira->assistance_types }}</td></tr>
                        @endif
                        @if(!empty($lira->resource_types))
                        <tr><td style="color:#6b7280;">Resource types</td>
                            <td>{{ is_array($lira->resource_types) ? implode(', ', $lira->resource_types) : $lira->resource_types }}</td></tr>
                        @endif
                        @if(!empty($lira->for_borrow_scan))
                        <tr><td style="color:#6b7280;">For borrow/scan</td><td>{{ $lira->for_borrow_scan }}</td></tr>
                        @endif
                        @if(!empty($lira->titles_of))
                        <tr><td style="color:#6b7280;">Titles/Topics</td><td>{{ $lira->titles_of }}</td></tr>
                        @endif
                        @if(!empty($lira->for_list))
                        <tr><td style="color:#6b7280;">For list</td><td>{{ $lira->for_list }}</td></tr>
                        @endif
                        @if(!empty($lira->for_videos))
                        <tr><td style="color:#6b7280;">Videos requested</td>
                            <td>{{ is_array($lira->for_videos) ? implode(', ', $lira->for_videos) : $lira->for_videos }}</td></tr>
                        @endif
                    </table>

                    <p style="margin-top:22px;">Sincerely,<br><strong>LC Learning Commons</strong></p>
                </td>
            </tr>
            <tr><td class="footer">This is an automated message. Please do not share sensitive information via email.</td></tr>
        </table>
    </td></tr></table>
</body>
</html>
