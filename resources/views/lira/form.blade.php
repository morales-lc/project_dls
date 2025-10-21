
<title>LiRA Request Form</title>
@include('navbar')

<style>
    /* Wrapper card to match ALINET theme */
    .lira-form-card {
        background: #fff0f6; /* light pink */
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        border: 4px solid #4a90e2; /* blue border */
        padding: 2.5rem 2rem 1.5rem 2rem;
        max-width: 900px;
        margin: 48px auto; /* spacing from top */
    }

    .lira-banner {
        width: 100%;
        height: auto;
        object-fit: contain;
    }

    /* Scoped label styles inside LiRA card */
    .lira-form-card .form-label {
        font-weight: 600;
        font-size: 1.05rem;
        color: #222;
    }

    /* Inputs/selects themed like ALINET */
    .lira-form-card .form-control,
    .lira-form-card .form-select {
        background: #ffe3ef; /* soft pink */
        border: 1.5px solid #fcb6d0; /* light pink border */
        color: #d81b60; /* pink text */
        font-size: 1rem;
        border-radius: 0.5rem;
        padding: .65rem .75rem;
    }

    .lira-form-card .form-control:focus,
    .lira-form-card .form-select:focus {
        border-color: #d81b60;
        box-shadow: 0 0 0 0.2rem #fcb6d0;
    }

    /* Buttons to match ALINET buttons */
    .lira-btn-pink {
        background: #fcb6d0;
        color: #fff;
        border: none;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.7rem 2.0rem;
        font-size: 1rem;
        transition: background 0.2s;
    }
    .lira-btn-pink:hover { background: #d81b60; color: #fff; }

    .lira-btn-outline {
        background: #fff;
        color: #d81b60;
        border: 2px solid #fcb6d0;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.7rem 2.0rem;
        font-size: 1rem;
        transition: background 0.2s, color 0.2s;
    }
    .lira-btn-outline:hover { background: #fcb6d0; color: #fff; }

    /* Sections inside LiRA form */
    .lira-form-card .form-section {
        background: #fff8fb;
        padding: 1.25rem;
        border-radius: .75rem;
        margin-bottom: 1.5rem;
        border: 1px solid #fcb6d0;
    }
    .lira-form-card .form-section h5 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #e83e8c;
    }

    /* Checkbox spacing */
    .lira-form-card .form-check { margin-bottom: .5rem; }

    /* Step visuals */
    .lira-step { animation: fadeIn .3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Required note */
    .note-required { font-size: .9rem; color: #666; margin-bottom: 1rem; font-style: italic; }
    .required { color: #e53935; font-weight: bold; margin-left: 3px; }

    /* Optional divider like ALINET */
    .lira-form-divider { border-top: 1.5px solid #fcb6d0; margin: 1.5rem 0; }
</style>

<div style="height: 50px;"></div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="lira-form-card">
                <h2 class="fw-bold text-center mb-3" style="color:#e83e8c;">LiRA (Library Research Assistance)</h2>
                <div class="lira-form-divider"></div>

                @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('lira.submit') }}" id="liraForm" novalidate>
                    @csrf

                        <input type="hidden" name="action" value="{{ $prefill['action'] ?? 'borrow' }}">

                        <div id="liraFormBody">
                            <div class="alert alert-danger d-none" id="liraStepError" role="alert"></div>

                            <!-- Step 1 -->
                            <div class="lira-step" data-step="1">
                                <div class="text-center mb-3">
                                    <img src="{{ asset('images/lira.png') }}" alt="LiRA" style="width: 100%; max-width: 800px; height: auto;">
                                </div>
                                <p class="mb-3">
                                    <strong>Data Protection Clause:</strong> By completing this form, you are granting LC Learning Commons the consent to collect and process the information provided for the purpose of this Library Research Assistance.
                                </p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="consent" name="consent">
                                    <label class="form-check-label" for="consent">Yes</label>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="lira-step d-none" data-step="2">
                                <div class="text-center mb-3">
                                    <img src="{{ asset('images/lira.png') }}" alt="LiRA" class="lira-banner mb-3">
                                </div>

                                <div class="note-required">
                                    All fields marked with <span class="required">*</span> are required.
                                </div>

                                <div class="form-section">
                                    <h5>Personal Information</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name <span class="required">*</span></label>
                                            <input name="first_name" value="{{ old('first_name', $prefill['first'] ?? '') }}" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name</label>
                                            <input name="middle_name" value="{{ old('middle_name', $prefill['middle'] ?? '') }}" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name <span class="required">*</span></label>
                                            <input name="last_name" value="{{ old('last_name', $prefill['last'] ?? '') }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h5>Academic Information</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email <span class="required">*</span></label>
                                            <input name="email" value="{{ old('email', $prefill['email'] ?? '') }}" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Program / Strand / Grade Level</label>
                                            <input name="program_strand_grade_level" value="{{ old('program_strand_grade_level', $prefill['programstrandgradeLevel'] ?? '') }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Designation <span class="required">*</span></label>
                                            <select name="designation" class="form-select">
                                                <option value="Faculty" {{ (old('designation', $prefill['designation'] ?? '')=='Faculty')? 'selected':'' }}>Faculty</option>
                                                <option value="Student" {{ (old('designation', $prefill['designation'] ?? '')=='Student')? 'selected':'' }}>Student</option>
                                                <option value="Staff" {{ (old('designation', $prefill['designation'] ?? '')=='Staff')? 'selected':'' }}>Staff</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Department <span class="required">*</span></label>
                                            <select name="department" class="form-select">
                                                <option value="Grade School" {{ (old('department', $prefill['department'] ?? '')=='Grade School')? 'selected':'' }}>Grade School</option>
                                                <option value="Junior High" {{ (old('department', $prefill['department'] ?? '')=='Junior High')? 'selected':'' }}>Junior High</option>
                                                <option value="Senior High" {{ (old('department', $prefill['department'] ?? '')=='Senior High')? 'selected':'' }}>Senior High</option>
                                                <option value="College" {{ (old('department', $prefill['department'] ?? '')=='College')? 'selected':'' }}>College</option>
                                                <option value="Graduate School" {{ (old('department', $prefill['department'] ?? '')=='Graduate School')? 'selected':'' }}>Graduate School</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="lira-step d-none" data-step="3">
                                <div class="text-center mb-3">
                                    <img src="{{ asset('images/lira.png') }}" alt="LiRA" class="img-fluid w-100 mb-3">
                                </div>
                                <h5>What kind of assistance do you need? Please check <span class="required">*</span></h5>
                                @php
                                $action = $prefill['action'] ?? 'borrow';
                                $oldAssists = old('assistance_types', []);
                                $isOld = is_array($oldAssists) && count($oldAssists) > 0;
                                @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assistance_types[]" value="Document Delivery" id="assist1"
                                        {{ $isOld ? (in_array('Document Delivery', $oldAssists) ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="assist1">Document Delivery (request for materials)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assistance_types[]" value="Library Scanning" id="assist2"
                                        {{ $isOld ? (in_array('Library Scanning', $oldAssists) ? 'checked' : '') : ($action === 'scanning' ? 'checked' : '') }}>
                                    <label class="form-check-label" for="assist2">Library Scanning</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assistance_types[]" value="Book Borrowing" id="assist3"
                                        {{ $isOld ? (in_array('Book Borrowing', $oldAssists) ? 'checked' : '') : ($action !== 'scanning' ? 'checked' : '') }}>
                                    <label class="form-check-label" for="assist3">Book Borrowing</label>
                                </div>

                                <h5 class="mt-3">What type of resource do you need? Please check <span class="required">*</span></h5>
                                @php $types = ['eBooks','Books','eBook Chapter','eJournals','Videos','List of References']; @endphp
                                @foreach($types as $t)
                                @php
                                $oldRes = old('resource_types', []);
                                $isOldRes = is_array($oldRes) && count($oldRes) > 0;
                                $defaultChecked = ($t == 'Books');
                                @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="resource_types[]" value="{{ $t }}" id="res_{{ $loop->index }}"
                                        {{ $isOldRes ? (in_array($t, $oldRes) ? 'checked' : '') : ($defaultChecked ? 'checked' : '') }}>
                                    <label class="form-check-label" for="res_{{ $loop->index }}">{{ $t }}</label>
                                </div>
                                @endforeach
                            </div>

                            <!-- Step 4 -->
                            <div class="lira-step d-none" data-step="4">
                                <div class="mb-3">
                                    <label>Title/s of Resource (Book/eBook/Article/Journal) or Topic/s of Request</label>
                                    <textarea name="titles_of" class="form-control" rows="3">{{ old('titles_of') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label>For BOOK BORROWING and LIBRARY SCANNING: specify TITLE, AUTHOR and CALL NUMBER</label>
                                    <textarea name="for_borrow_scan" class="form-control" rows="3">{{ old('for_borrow_scan', $prefill['for_borrow_scan'] ?? (old('example_purposive', $prefill['title'] ?? ''))) }}</textarea>
                                    <small class="text-muted">Example: Purposive communication, Zobeta, Mr. Antonieto G., 302.2 T74 2018 cl</small>
                                </div>
                                <div class="mb-3">
                                    <label>For List of References: Specify Course Code and Course Description</label>
                                    <textarea name="for_list" class="form-control" rows="3">{{ old('for_list') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label>For Videos</label>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="for_videos[]" value="Downloaded" id="v1"><label class="form-check-label" for="v1">Downloaded</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="for_videos[]" value="Link/s" id="v2"><label class="form-check-label" for="v2">Link/s</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="lira-form-divider"></div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                            <button type="button" class="lira-btn-outline" id="backBtn">Back</button>
                            <div class="d-flex gap-2">
                                <button type="button" class="lira-btn-outline" id="saveBtn">Save</button>
                                <button type="button" class="lira-btn-pink" id="nextBtn">Next</button>
                                <button type="submit" class="lira-btn-pink d-none" id="submitBtn">Submit</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div style="height: 200px;"></div>

@include('footer')

<script>
document.addEventListener('DOMContentLoaded', function() {
    let step = 1;
    const total = 4;
    const steps = Array.from(document.querySelectorAll('.lira-step'));
    const formBody = document.getElementById('liraFormBody');
    const errorBox = document.getElementById('liraStepError');

    const computeAndSetHeight = () => {
        let max = 0;
        steps.forEach(s => {
            s.classList.remove('d-none');
            max = Math.max(max, s.scrollHeight);
        });
        steps.forEach((s, i) => {
            if (i !== (step - 1)) s.classList.add('d-none');
        });
        formBody.style.minHeight = (max + 40) + 'px';
    };
    computeAndSetHeight();

    const showStep = (n) => {
        errorBox.classList.add('d-none');
        document.querySelectorAll('.lira-step').forEach(el => el.classList.add('d-none'));
        const el = document.querySelector('.lira-step[data-step="' + n + '"]');
        if (el) el.classList.remove('d-none');
        document.getElementById('backBtn').style.display = n === 1 ? 'none' : 'inline-block';
        document.getElementById('nextBtn').classList.toggle('d-none', n === total);
        document.getElementById('submitBtn').classList.toggle('d-none', n !== total);
        window.scrollTo({ top: el.offsetTop - 20, behavior: 'smooth' });
    };
    showStep(step);

    const validateStep = (n) => {
        if (n === 1) {
            const consent = document.getElementById('consent');
            if (!consent.checked) return 'You must accept the Data Protection Clause to proceed.';
            return null;
        }
        if (n === 2) {
            const first = document.querySelector('input[name="first_name"]').value.trim();
            const last = document.querySelector('input[name="last_name"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            if (!first) return 'Please enter your First Name.';
            if (!last) return 'Please enter your Last Name.';
            if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) return 'Please enter a valid Email address.';
            return null;
        }
        if (n === 3) {
            const assists = document.querySelectorAll('input[name="assistance_types[]"]:checked');
            const types = document.querySelectorAll('input[name="resource_types[]"]:checked');
            if (assists.length === 0) return 'Please select at least one kind of assistance.';
            if (types.length === 0) return 'Please select at least one resource type.';
            return null;
        }
        return null;
    };

    document.getElementById('nextBtn').addEventListener('click', function() {
        const err = validateStep(step);
        if (err) {
            errorBox.textContent = err;
            errorBox.classList.remove('d-none');
            return;
        }
        if (step < total) step++;
        showStep(step);
    });

    document.getElementById('backBtn').addEventListener('click', function() {
        if (step > 1) step--;
        showStep(step);
    });

    document.getElementById('saveBtn').addEventListener('click', function() {
        const values = {};
        new FormData(document.getElementById('liraForm')).forEach((v, k) => {
            if (values[k]) {
                if (!Array.isArray(values[k])) values[k] = [values[k]];
                values[k].push(v);
            } else values[k] = v;
        });
        localStorage.setItem('lira.form', JSON.stringify(values));
        alert('Form saved locally in your browser.');
    });

    document.getElementById('liraForm').addEventListener('submit', function(e) {
        const err = validateStep(1) || validateStep(2) || validateStep(3);
        if (err) {
            e.preventDefault();
            alert('Please complete required fields before submitting: ' + err);
        }
    });
});
</script>
