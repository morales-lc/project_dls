<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background:#f8f9fa;}
        table{border-collapse:collapse;}
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
                    <h2 style="margin:0 0 12px 0;font-size:20px;color:#111827;">Update on your LiRA request</h2>

                    <div style="margin:14px 0 18px 0;padding:16px;border-radius:8px;background:#f8fafc;border:1px solid #dbeafe;">
                        <div style="font-weight:bold;color:#004080;margin:0 0 8px 0;font-size:16px;">Message from LC Learning Commons</div>
                        <div style="color:#1f2937;font-size:15px;line-height:1.8;">{!! nl2br(e($body)) !!}</div>
                    </div>

                    <div style="margin-top:10px;color:#6b7280;font-size:14px;">Below are the details of your original request for reference:</div>

                    <table width="100%" style="margin-top:14px;font-size:14px;color:#374151;">
                        <tr><td width="180" style="color:#6b7280;">Submitted</td><td>{{ optional($lira->created_at)->format('F d, Y g:i A') }}</td></tr>
                        @if(!empty($lira->action))
                        <tr><td style="color:#6b7280;">Action</td><td>{{ $lira->action }}</td></tr>
                        @endif
                        @if(!empty($lira->titles_of))
                        <tr><td style="color:#6b7280;">Titles/Topics</td><td>{{ $lira->titles_of }}</td></tr>
                        @endif
                        @if(!empty($lira->for_list))
                        <tr><td style="color:#6b7280;">For list</td><td>{{ $lira->for_list }}</td></tr>
                        @endif
                    </table>

                    <p style="margin-top:22px;color:#6b7280;font-size:13px;">If you have questions, reply to this email and our team will assist you.</p>
                </td>
            </tr>
            <tr><td class="footer">This is an automated message. Please do not share sensitive information via email.</td></tr>
        </table>
    </td></tr></table>
</body>
</html>
