<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ALINET Appointment Rejected</title>
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
					<p>Dear <strong>{{ $appointment->firstname }}</strong>,</p>
					<p>Your ALINET appointment request has been <strong>rejected</strong>.</p>

					<table class="kv" width="100%" style="font-size:14px;">
						<tr><td>Email</td><td>{{ $appointment->email }}</td></tr>
						<tr><td>Strand/Course</td><td>{{ $appointment->strand_course }}</td></tr>
						<tr><td>Institution/College</td><td>{{ $appointment->institution_college }}</td></tr>
						<tr><td>Requested</td><td>{{ optional($appointment->created_at)->format('F d, Y g:i A') }}</td></tr>
						<tr><td>Mode requested</td><td>{{ $appointment->mode_of_research }}</td></tr>
					</table>

					@if(!empty($reason ?? null))
					<div style="background:#fff7f7;border:1px solid #f3d6d6;padding:12px 14px;border-radius:6px;margin:12px 0;color:#8a2a2a;">
						<div style="font-weight:bold;margin-bottom:6px;">Reason from the librarian</div>
						<div style="white-space:pre-wrap;">{{ $reason }}</div>
					</div>
					@endif

					<p>If you have questions or would like to reschedule, please contact the library.</p>
					<p style="margin-top:22px;">Sincerely,<br><strong>Lourdes College Library</strong></p>
				</td></tr>
				<tr><td class="footer">© {{ date('Y') }} Lourdes College Library — All rights reserved</td></tr>
			</table>
		</td></tr>
	</table>
</body>
</html>
