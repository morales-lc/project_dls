<title>ALINET</title>

@include('navbar')
<style>
    body {
        background: #ffffffff !important;
        min-height: 100vh;
    }

    .alinet-form-card {
        background: #fff0f6;
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        border: 4px solid #4a90e2;
        padding: 2.5rem 2rem 1.5rem 2rem;
        max-width: 900px;
        margin: 48px auto;
    }

    .alinet-form-label {
        font-weight: 600;
        font-size: 1.2rem;
        color: #222;
    }

    .alinet-form-control,
    .alinet-form-select {
        background: #ffe3ef;
        border: 1.5px solid #fcb6d0;
        color: #d81b60;
        font-size: 1.1rem;
        border-radius: 0.5rem;
    }

    .alinet-form-control:focus,
    .alinet-form-select:focus {
        border-color: #d81b60;
        box-shadow: 0 0 0 0.2rem #fcb6d0;
    }

    .alinet-btn-pink {
        background: #fcb6d0;
        color: #fff;
        border: none;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.7rem 2.5rem;
        font-size: 1.1rem;
        transition: background 0.2s;
    }

    .alinet-btn-pink:hover {
        background: #d81b60;
        color: #fff;
    }

    .alinet-btn-outline {
        background: #fff;
        color: #d81b60;
        border: 2px solid #fcb6d0;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.7rem 2.5rem;
        font-size: 1.1rem;
        transition: background 0.2s, color 0.2s;
    }

    .alinet-btn-outline:hover {
        background: #fcb6d0;
        color: #fff;
    }

    .alinet-form-hint {
        color: #d81b60;
        font-size: 0.95rem;
        margin-top: 0.2rem;
    }

    .alinet-form-divider {
        border-top: 1.5px solid #fcb6d0;
        margin: 2rem 0 1.5rem 0;
    }
</style>

<div class="container" style="margin-top: 2.5rem;">
    <div class="text-center mb-4" style="max-width:900px; margin:auto;">
        <h2 class="fw-bold mb-2" style="color:#1976d2; letter-spacing:1px;">ALINET</h2>
        <div class="mb-2" style="font-size:1.08rem; color:#d81b60; font-weight:500;">(Academic Libraries Information Network in Mindanao, Inc.)</div>
        <div class="mb-0" style="color:#444; font-size:1.05rem;">
            The Lourdes College Learning Commons is a member of ALINET. LC students and faculty can do research in the member libraries by securing the ALINET permit form at the Concierge. There are specific library hours for research in the libraries; ask for the schedule at the Concierge. The visited library may ask for a visitor's fee.
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="max-width:900px; margin:auto;">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width:900px; margin:auto;">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width:900px; margin:auto;">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="alinet-form-card">
        <form method="POST" action="{{ route('alinet.submit') }}">
            @csrf
            <!-- Full Name -->
            <div class="row mb-4 align-items-end">
                <div class="col-12 mb-2">
                    <label class="alinet-form-label">Full Name <span class="text-danger">*</span></label>
                </div>
                <div class="col-md-2 col-4 mb-2">
                    <select name="prefix" class="form-select alinet-form-select" style="color:#d81b60;">
                        <option value="">Mr.</option>
                        <option value="Mr." @selected(old('prefix')=='Mr.' )>Mr.</option>
                        <option value="Ms." @selected(old('prefix')=='Ms.' )>Ms.</option>
                        <option value="Mrs." @selected(old('prefix')=='Mrs.' )>Mrs.</option>
                        <option value="Dr." @selected(old('prefix')=='Dr.' )>Dr.</option>
                    </select>
                    <div class="form-text text-center" style="color:#d81b60;">Prefix</div>
                </div>
                <div class="col-md-5 col-8 mb-2">
                    <input type="text" name="firstname" class="form-control alinet-form-control" placeholder="First Name" value="{{ old('firstname') }}" required>
                    <div class="form-text text-center" style="color:#d81b60;">First Name</div>
                </div>
                <div class="col-md-5 col-12 mb-2">
                    <input type="text" name="lastname" class="form-control alinet-form-control" placeholder="Last Name" value="{{ old('lastname') }}" required>
                    <div class="form-text text-center" style="color:#d81b60;">Last Name</div>
                </div>
            </div>

            <!-- Strand / Institution -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="alinet-form-label">Strand/Course</label>
                    <input type="text" name="strand_course" class="form-control alinet-form-control" value="{{ old('strand_course') }}">
                </div>
                <div class="col-md-6">
                    <label class="alinet-form-label">Institution / College <span class="text-danger">*</span></label>
                    <input type="text" name="institution_college" class="form-control alinet-form-control" value="{{ old('institution_college') }}" required>
                </div>
            </div>

            <!-- Email Row -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="alinet-form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control alinet-form-control" value="{{ old('email') }}" required>
                    <div class="alinet-form-hint">example.example@lccdo.edu.ph</div>
                </div>
            </div>
            <!-- Mode of Research -->
            <div class="row mb-4">
                <div class="col-12">
                    <label class="alinet-form-label d-block mb-2">Mode of Research <span class="text-danger">*</span></label>
                    
                    @auth
                        @if(Auth::user()->role === 'guest')
                            <!-- Guest users already have online access, show only onsite option -->
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Note:</strong> You already have guest account access to online resources. Use this form only if you need onsite library access.
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode_of_research" id="modeOnsite" value="Onsite (Saturday 8:00am3:00pm)" checked required>
                                <label class="form-check-label" for="modeOnsite">
                                    <strong>Onsite (Saturday 8:00am3:00pm)</strong>
                                </label>
                                <div class="alinet-form-hint mt-2 ms-4">
                                    Visit the library in person. We'll schedule you for this week's Saturday 8:00am3:00pm (next Saturday if you submit on a Sunday).
                                </div>
                            </div>
                        @else
                            <!-- Non-guest authenticated users see both options -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode_of_research" id="modeOnline" value="Online (Virtual)" @checked(old('mode_of_research')==='Online (Virtual)' ) required>
                                <label class="form-check-label" for="modeOnline">
                                    <strong>Online (Virtual)</strong>
                                </label>
                                <div class="alert alert-info mt-2 ms-4" style="font-size: 0.9rem;">
                                    <strong> Guest Account Access (Valid for 7 days):</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Research papers and academic journals</li>
                                        <li>Online database libraries partnered with Lourdes College</li>
                                        <li>E-resources and downloadable materials</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="radio" name="mode_of_research" id="modeOnsite" value="Onsite (Saturday 8:00am3:00pm)" @checked(old('mode_of_research')==='Onsite (Saturday 8:00am3:00pm)' ) required>
                                <label class="form-check-label" for="modeOnsite">
                                    <strong>Onsite (Saturday 8:00am3:00pm)</strong>
                                </label>
                                <div class="alinet-form-hint mt-2 ms-4">
                                    Visit the library in person. We'll schedule you for this week's Saturday 8:00am3:00pm (next Saturday if you submit on a Sunday).
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Unauthenticated users see both options -->
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode_of_research" id="modeOnline" value="Online (Virtual)" @checked(old('mode_of_research')==='Online (Virtual)' ) required>
                            <label class="form-check-label" for="modeOnline">
                                <strong>Online (Virtual)</strong>
                            </label>
                            <div class="alert alert-info mt-2 ms-4" style="font-size: 0.9rem;">
                                <strong> Guest Account Access (Valid for 7 days):</strong>
                                <ul class="mb-0 mt-1">
                                    <li>Research papers and academic journals</li>
                                    <li>Online database libraries partnered with Lourdes College</li>
                                    <li>E-resources and downloadable materials</li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="radio" name="mode_of_research" id="modeOnsite" value="Onsite (Saturday 8:00am3:00pm)" @checked(old('mode_of_research')==='Onsite (Saturday 8:00am3:00pm)' ) required>
                            <label class="form-check-label" for="modeOnsite">
                                <strong>Onsite (Saturday 8:00am3:00pm)</strong>
                            </label>
                            <div class="alinet-form-hint mt-2 ms-4">
                                Visit the library in person. We'll schedule you for this week's Saturday 8:00am3:00pm (next Saturday if you submit on a Sunday).
                            </div>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Titles/Topics Row - Only for Onsite -->
            <div class="row mb-4" id="titlesSection" style="display: none;">
                <div class="col-12">
                    <label class="alinet-form-label">Title/s of Resource or Topic/s of Request: (Please be as specific as possible)</label>
                    <div class="alert alert-warning mb-3" style="background-color: #fff3cd; border-left: 4px solid #d81b60;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2" style="font-size: 1.5rem; color: #d81b60;"></i>
                            <div>
                                <strong>Need help finding specific titles?</strong><br>
                                <a href="{{ Auth::check() && Auth::user()->role === 'guest' ? route('guest.dashboard') : route('dashboard') }}" target="_blank" class="btn btn-sm btn-pink mt-2">
                                    <i class="bi bi-search"></i> Search Library Catalog
                                </a>
                            </div>
                        </div>
                    </div>
                    <textarea name="titles_or_topics" class="form-control alinet-form-control" rows="3" placeholder="e.g., Topic on sustainable development; Book: Author - Title (Year)">{{ old('titles_or_topics') }}</textarea>
                </div>
            </div>
            <!-- Assistance - Only for Onsite -->
            <div class="row mb-4" id="assistanceSection" style="display: none;">
                <div class="col-12">
                    <label class="alinet-form-label">What kind of assistance do you need? Please check</label>
                    <div class="row g-2">
                        @php $oldAssist = old('assistance', []); @endphp
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assistance[]" value="Document Delivery (request for materials)" id="assist1" @checked(is_array($oldAssist) && in_array('Document Delivery (request for materials)', $oldAssist))>
                                <label class="form-check-label" for="assist1">Document Delivery (request for materials)</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assistance[]" value="Library Scanning" id="assist2" @checked(is_array($oldAssist) && in_array('Library Scanning', $oldAssist))>
                                <label class="form-check-label" for="assist2">Library Scanning</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assistance[]" value="Downloading" id="assist3" @checked(is_array($oldAssist) && in_array('Downloading', $oldAssist))>
                                <label class="form-check-label" for="assist3">Downloading</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Resources - Only for Onsite -->
            <div class="row mb-4" id="resourcesSection" style="display: none;">
                <div class="col-12">
                    <label class="alinet-form-label">What type of resource do you need? Please check</label>
                    <div class="row g-2">
                        @php $oldRes = old('resource_types', []); @endphp
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resource_types[]" value="eBooks" id="res1" @checked(is_array($oldRes) && in_array('eBooks', $oldRes))>
                                <label class="form-check-label" for="res1">eBooks</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resource_types[]" value="Books" id="res2" @checked(is_array($oldRes) && in_array('Books', $oldRes))>
                                <label class="form-check-label" for="res2">Books</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resource_types[]" value="eBook Chapter" id="res3" @checked(is_array($oldRes) && in_array('eBook Chapter', $oldRes))>
                                <label class="form-check-label" for="res3">eBook Chapter</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resource_types[]" value="eJournals" id="res4" @checked(is_array($oldRes) && in_array('eJournals', $oldRes))>
                                <label class="form-check-label" for="res4">eJournals</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="resource_types[]" value="Theses and Dissertations" id="res5" @checked(is_array($oldRes) && in_array('Theses and Dissertations', $oldRes))>
                                <label class="form-check-label" for="res5">Theses and Dissertations</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="alinet-form-divider"></div>
            <div class="d-flex justify-content-center align-items-center mt-4">
                <button type="submit" class="alinet-btn-outline">Send</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show/hide conditional sections based on mode selection
    document.addEventListener('DOMContentLoaded', function() {
        const modeOnline = document.getElementById('modeOnline');
        const modeOnsite = document.getElementById('modeOnsite');
        const titlesSection = document.getElementById('titlesSection');
        const assistanceSection = document.getElementById('assistanceSection');
        const resourcesSection = document.getElementById('resourcesSection');

        function toggleSections() {
            if (modeOnsite && modeOnsite.checked) {
                // Show sections for onsite mode
                titlesSection.style.display = 'block';
                assistanceSection.style.display = 'block';
                resourcesSection.style.display = 'block';
            } else {
                // Hide sections for online mode
                titlesSection.style.display = 'none';
                assistanceSection.style.display = 'none';
                resourcesSection.style.display = 'none';

                // Clear all onsite-specific fields when switching to online mode
                clearOnsiteFields();
            }
        }

        function clearOnsiteFields() {
            // Clear textarea
            const titlesTextarea = document.querySelector('textarea[name="titles_or_topics"]');
            if (titlesTextarea) {
                titlesTextarea.value = '';
            }

            // Uncheck all assistance checkboxes
            const assistanceCheckboxes = document.querySelectorAll('input[name="assistance[]"]');
            assistanceCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Uncheck all resource type checkboxes
            const resourceCheckboxes = document.querySelectorAll('input[name="resource_types[]"]');
            resourceCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        // Listen for changes (check if elements exist first)
        if (modeOnline) {
            modeOnline.addEventListener('change', toggleSections);
        }
        if (modeOnsite) {
            modeOnsite.addEventListener('change', toggleSections);
        }

        // Check on page load (for old input persistence or guest users with only onsite option)
        toggleSections();
    });
</script>

@include('footer')