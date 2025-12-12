<div class="card p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="text-primary"><i class="fas fa-user me-2"></i>Personal Information</h5>
        @if($customer->Cus_phto)
            <div class="text-end">
                <img src="{{ asset('storage/' . $customer->Cus_phto) }}" alt="Customer Photo"
                     class="rounded-circle shadow border border-2 border-primary"
                     style="width: 120px; height: 120px; object-fit: cover;">
            </div>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-md-2">
            <label class="form-label">Customer No</label>
            <input type="text" class="form-control" value="{{ $customer->cus_number }}" disabled>
        </div>
        <div class="col-md-2">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" value="{{ $customer->Title }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" value="{{ $customer->First_Name }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" value="{{ $customer->Last_Name }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">NIC</label>
            <input type="text" class="form-control" value="{{ $customer->Nic }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Gender</label>
            <input type="text" class="form-control" value="{{ $customer->Gender }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" class="form-control" value="{{ $customer->Dob }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Civil Status</label>
            <input type="text" class="form-control" value="{{ $customer->civil_status }}" disabled>
        </div>
    </div>

    <hr class="my-4">

    <h5 class="mb-3 text-primary"><i class="fas fa-phone me-2"></i>Contact Information</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ $customer->Email }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Mobile No</label>
            <input type="text" class="form-control" value="{{ $customer->Contact_No }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Alternate No</label>
            <input type="text" class="form-control" value="{{ $customer->contact_number_2 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Landline</label>
            <input type="text" class="form-control" value="{{ $customer->Landline }}" disabled>
        </div>
    </div>

    <hr class="my-4">

    <h5 class="mb-3 text-primary"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Current Address</label>
            <input type="text" class="form-control" value="{{ $customer->Address }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Address Line 2</label>
            <input type="text" class="form-control" value="{{ $customer->Address_02 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Address Line 3</label>
            <input type="text" class="form-control" value="{{ $customer->Address_03 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Permanent Address 01</label>
            <input type="text" class="form-control" value="{{ $customer->Per_Address_01 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Permanent Address 02</label>
            <input type="text" class="form-control" value="{{ $customer->Per_Address_02 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Permanent Address 03</label>
            <input type="text" class="form-control" value="{{ $customer->Per_Address_03 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">City</label>
            <input type="text" class="form-control" value="{{ $customer->City }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">State</label>
            <input type="text" class="form-control" value="{{ $customer->State }}" disabled>
        </div>
    </div>

    <hr class="my-4">

    <h5 class="mb-3 text-primary"><i class="fas fa-briefcase me-2"></i>Occupation Details</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Job Position</label>
            <input type="text" class="form-control" value="{{ $customer->occu_job_position }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Monthly Salary</label>
            <input type="text" class="form-control" value="{{ $customer->occu_monthly_salary }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Work Contact No</label>
            <input type="text" class="form-control" value="{{ $customer->occu_contact_no }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Work Address 01</label>
            <input type="text" class="form-control" value="{{ $customer->occu_address_01 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Work Address 02</label>
            <input type="text" class="form-control" value="{{ $customer->occu_address_02 }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Work Address 03</label>
            <input type="text" class="form-control" value="{{ $customer->occu_address_03 }}" disabled>
        </div>
    </div>

    <hr class="my-4">

    <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Additional Info</h5>
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label">Customer Location</label>
            <div id="map" style="height: 300px; border-radius: 10px;" data-lat="{{ $customer->Latitude }}" data-lng="{{ $customer->Longitude }}"></div>

        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <input type="text" class="form-control" value="{{ $customer->Status == 1 ? 'Active' : 'Inactive' }}" disabled>
        </div>
        <div class="col-md-3">
            <label class="form-label">Business Registration</label>
            <input type="text" class="form-control" value="{{ $customer->business_registration }}" disabled>
        </div>
        <div class="col-md-12">
            <label class="form-label">Note / Comment</label>
            <textarea class="form-control" rows="3" disabled>{{ $customer->Note }}</textarea>
        </div>
    </div>
</div>

