
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
    .alinet-form-control, .alinet-form-select {
        background: #ffe3ef;
        border: 1.5px solid #fcb6d0;
        color: #d81b60;
        font-size: 1.1rem;
        border-radius: 0.5rem;
    }
    .alinet-form-control:focus, .alinet-form-select:focus {
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
    <div class="alinet-form-card">
        <form method="POST" action="{{ route('alinet.submit') }}">
            @csrf
            <div class="row mb-4 align-items-end">
                <div class="col-12 mb-2">
                    <label class="alinet-form-label">Full Name <span class="text-danger">*</span></label>
                </div>
                <div class="col-md-2 col-4 mb-2">
                    <select name="prefix" class="form-select alinet-form-select" style="color:#d81b60;">
                        <option value="">Mr.</option>
                        <option value="Mr." @selected(old('prefix')=='Mr.')>Mr.</option>
                        <option value="Ms." @selected(old('prefix')=='Ms.')>Ms.</option>
                        <option value="Mrs." @selected(old('prefix')=='Mrs.')>Mrs.</option>
                        <option value="Dr." @selected(old('prefix')=='Dr.')>Dr.</option>
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
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="alinet-form-label">Strand/Course</label>
                    <input type="text" name="strand_course" class="form-control alinet-form-control" value="{{ old('strand_course') }}">
                </div>
                <div class="col-md-6">
                    <label class="alinet-form-label">Institution / College</label>
                    <input type="text" name="institution_college" class="form-control alinet-form-control" value="{{ old('institution_college') }}">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="alinet-form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control alinet-form-control" value="{{ old('email') }}" required>
                    <div class="alinet-form-hint">example.example@lccdo.edu.ph</div>
                </div>
                <div class="col-md-6">
                    <label class="alinet-form-label">Appointment Date <span class="text-danger">*</span></label>
                    <input type="date" name="appointment_date" class="form-control alinet-form-control" value="{{ old('appointment_date') }}" required min="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <label class="alinet-form-label">Services to Avail <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Book Borrowing" id="service1" @checked(is_array(old('services')) && in_array('Book Borrowing', old('services', [])))>
                                <label class="form-check-label" for="service1">Book Borrowing</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Scanning Service" id="service2" @checked(is_array(old('services')) && in_array('Scanning Service', old('services', [])))>
                                <label class="form-check-label" for="service2">Scanning Service</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Use of internet/Wi-Fi" id="service3" @checked(is_array(old('services')) && in_array('Use of internet/Wi-Fi', old('services', [])))>
                                <label class="form-check-label" for="service3">Use of internet/Wi-Fi</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Reading Areas" id="service4" @checked(is_array(old('services')) && in_array('Reading Areas', old('services', [])))>
                                <label class="form-check-label" for="service4">Reading Areas</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alinet-form-divider"></div>
            <div class="d-flex justify-content-center align-items-center mt-4">
                <button type="submit" class="alinet-btn-outline">Send</button>
            </div>
        </form>
    </div>
</div>

@include('footer')

