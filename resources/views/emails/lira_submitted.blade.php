<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New LiRA Request Submitted</title>
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
                            <img src="{{ $message->embed(public_path('images/lourdes_college.jpg')) }}" alt="Lourdes College" style="margin-right:15px;">
                            <img src="{{ $message->embed(public_path('images/learningcommons.png')) }}" alt="Learning Commons">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#333333; font-size:15px; line-height:1.6;">
                            <h2 style="margin:0 0 12px 0; font-size:20px;">New LiRA request submitted</h2>
                            <p style="margin:0 0 18px 0; color:#6b7280;">A user submitted a new LiRA request. Details are below.</p>

                            <div style="margin:0 0 6px 0; font-weight:600;">Requester</div>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#374151;">
                                <tr>
                                    <td style="width:180px; color:#6b7280; padding:6px 0;">Name</td>
                                    <td style="padding:6px 0;">{{ $lira->first_name }} {{ $lira->last_name }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280; padding:6px 0;">Email</td>
                                    <td style="padding:6px 0;">{{ $lira->email }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280; padding:6px 0;">Designation</td>
                                    <td style="padding:6px 0;">{{ $lira->designation ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280; padding:6px 0;">Department</td>
                                    <td style="padding:6px 0;">{{ $lira->department ?? '-' }}</td>
                                </tr>
                            </table>

                            <div style="margin:10px 0 6px 0; font-weight:600;">Request</div>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#374151;">
                                <tr>
                                    <td style="width:180px; color:#6b7280; padding:6px 0;">Submitted</td>
                                    <td style="padding:6px 0;">{{ optional($lira->created_at)->format('F d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280; padding:6px 0;">Action</td>
                                    <td style="padding:6px 0;">{{ $lira->action ?? '-' }}</td>
                                </tr>
                                @if(!empty($lira->assistance_types))
                                <tr>
                                    <td style="color:#6b7280; padding:6px 0;">Assistance types</td>
                                    <td style="padding:6px 0;">
                                        @if(is_array($lira->assistance_types))
                                        {{ implode(', ', $lira->assistance_types) }}
                                        @else
                                        {{ $lira->assistance_types }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($lira->resource_types))
                                <tr>
                                    <td style="color:#6b7280; padding:6px 0;">Resource types</td>
                                    <td style="padding:6px 0;">
                                        @if(is_array($lira->resource_types))
                                        {{ implode(', ', $lira->resource_types) }}
                                        @else
                                        {{ $lira->resource_types }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
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

                            <p style="margin-top:25px;">Open the LiRA management page to review and take action.</p>
                            <p>
                                <a href="{{ url('/lira/manage') }}" style="display:inline-block; background:#004080; color:#ffffff; text-decoration:none; padding:10px 16px; border-radius:6px; font-weight:bold;">Open LiRA Manage</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background:#f1f1f1; padding:15px; font-size:12px; color:#666;">
                            Automated notification from LC Learning Commons
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>