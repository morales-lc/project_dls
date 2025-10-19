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
    <p><strong>Appointment Date:</strong>
        @if(!empty($appointment->appointment_date))
            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
        @else
            —
        @endif
    </p>
    <p><strong>Mode of Research:</strong> {{ $appointment->mode_of_research }}</p>
    <p><strong>Titles/Topics:</strong></p>
    <div style="white-space: pre-wrap;">{{ $appointment->titles_or_topics }}</div>

    <p><strong>Assistance Requested:</strong></p>
    <ul>
        @foreach((array) $appointment->assistance as $item)
        <li>{{ $item }}</li>
        @endforeach
    </ul>

    <p><strong>Resource Types:</strong></p>
    <ul>
        @foreach((array) $appointment->resource_types as $item)
        <li>{{ $item }}</li>
        @endforeach
    </ul>
</body>

</html>