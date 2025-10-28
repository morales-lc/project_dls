<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New LiRA Request Submitted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        table {
            border-collapse: collapse;
        }

        .email-container {
            width: 100%;
            padding: 20px 0;
            background-color: #f8f9fa;
        }

        .email-content {
            width: 600px;
            max-width: 95%;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #e83e8c;
            text-align: center;
            padding: 20px;
        }

        .header img {
            width: 120px;
            height: auto;
            margin: 0 8px;
            vertical-align: middle;
            background:#ffffff; border-radius:6px; padding:4px;
        }
        .brand-title { color:#ffffff; font-weight:700; font-size:18px; margin-top:8px; font-family: Arial, Helvetica, sans-serif; }

        .content {
            padding: 30px;
            color: #333333;
            font-size: 15px;
            line-height: 1.7;
        }

        h2 {
            margin: 0 0 12px 0;
            font-size: 20px;
            color: #111827;
        }

        .btn {
            display: inline-block;
            background: #004080;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            background: #f1f1f1;
            padding: 15px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }

        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .header img {
                width: 100px;
            }

            h2 {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <table class="email-container" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="email-content" cellpadding="0" cellspacing="0">

                    <tr>
                        <td class="header" style="background:#e83e8c; text-align:center; padding:20px;">
                            <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="LC Learning Commons">
                            <div class="brand-title">LC MIDES Digital Library</div>
                        </td>
                    </tr>

                    <tr>
                        <td class="content">
                            <h2>New LiRA request submitted</h2>
                            <p style="color:#6b7280;">A user submitted a new LiRA request. Details are below.</p>

                            <div style="margin-top:12px; font-weight:600;">Requester</div>
                            <table width="100%" style="font-size:14px; color:#374151;">
                                <tr><td width="180" style="color:#6b7280;">Name</td><td>{{ $lira->first_name }} {{ $lira->last_name }}</td></tr>
                                <tr><td style="color:#6b7280;">Email</td><td>{{ $lira->email }}</td></tr>
                                <tr><td style="color:#6b7280;">Designation</td><td>{{ $lira->designation ?? '-' }}</td></tr>
                                <tr><td style="color:#6b7280;">Department</td><td>{{ $lira->department ?? '-' }}</td></tr>
                            </table>

                            <div style="margin:14px 0 6px 0; font-weight:600;">Request Details</div>
                            <table width="100%" style="font-size:14px; color:#374151;">
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
                            </table>

                            <a href="{{ url('/lira/manage') }}" class="btn">Open LiRA Manage</a>
                        </td>
                    </tr>

                    <tr>
                        <td class="footer">Automated notification from LC Learning Commons</td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
