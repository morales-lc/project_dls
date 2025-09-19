<p>Dear {{ $appointment->firstname }},</p>
<p>Your ALINET appointment request has been <strong>accepted</strong>. Please visit on your scheduled date: <strong>{{ $appointment->appointment_date->format('F d, Y') }}</strong>.</p>
<p>Thank you!</p>
<p>Lourdes College Library</p>
