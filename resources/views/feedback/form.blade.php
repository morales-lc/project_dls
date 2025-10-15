<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('learningcommons.ico') }}">
</head>

<body>
    @include('navbar')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow rounded-4">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-3" style="color:#d81b60;">Submit Feedback</h2>
                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('feedback.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Your Feedback <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_anonymous" id="isAnonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                                <label class="form-check-label" for="isAnonymous">Submit Anonymously</label>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Even if anonymous, your course and role will be recorded for analysis.</small>
                            </div>
                            <button type="submit" class="btn btn-pink px-4 py-2">Submit Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
</body>

</html>