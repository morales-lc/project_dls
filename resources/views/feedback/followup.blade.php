<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
</head>
<body>
    <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow rounded-4">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-3" style="color:#1976d2;">Follow Up Feedback</h2>
                    <div class="mb-3">
                        <strong>Message:</strong>
                        <div class="border rounded-3 p-3 bg-light">{{ $feedback->message }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>User:</strong>
                        @if($feedback->user)
                            {{ $feedback->user->name }}<br>
                            <small class="text-muted">{{ $feedback->user->email }}</small>
                        @else
                            <span class="text-muted">Anonymous</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Course:</strong> {{ $feedback->course ?? '-' }}<br>
                        <strong>Role:</strong> {{ ucfirst($feedback->role ?? '-') }}
                    </div>
                    <div class="mb-3">
                        <strong>Submitted:</strong> {{ $feedback->created_at->format('Y-m-d H:i') }}
                    </div>
                    <a href="{{ route('feedback.admin') }}" class="btn btn-outline-secondary">Back to Feedback List</a>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
