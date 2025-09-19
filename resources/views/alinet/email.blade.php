<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>New ALINET Appointment Request</h2>
    <p><strong>Name:</strong> {{ $appointment->prefix ? $appointment->prefix . ' ' : '' }}{{ $appointment->firstname }} {{ $appointment->lastname }}</p>
    <p><strong>Email:</strong> {{ $appointment->email }}</p>
    <p><strong>Strand/Course:</strong> {{ $appointment->strand_course }}</p>
    <p><strong>Institution/College:</strong> {{ $appointment->institution_college }}</p>
    <p><strong>Appointment Date:</strong> {{ $appointment->appointment_date->format('F d, Y') }}</p>
    <p><strong>Services to Avail:</strong></p>
    <ul>
        @foreach($appointment->services as $service)
        <li>{{ $service }}</li>
        @endforeach
    </ul>
</body>

</html>