<div class="card p-4">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-user-shield me-2"></i>Guardian Information
    </h5>
    <div class="row g-4">
        <div class="col-md-2">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" value="{{ $customer->Gua_title }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Guardian Name</label>
            <input type="text" class="form-control" value="{{ $customer->Gua_name }}" disabled>
        </div>
        <div class="col-md-3">
            <label class="form-label">Gender</label>
            <input type="text" class="form-control" value="{{ $customer->Guardian_gender }}" disabled>
        </div>
        <div class="col-md-3">
            <label class="form-label">Relation</label>
            <input type="text" class="form-control" value="{{ $customer->Gua_relation }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Occupation</label>
            <input type="text" class="form-control" value="{{ $customer->Gua_occu }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">NIC</label>
            <input type="text" class="form-control" value="{{ $customer->Gua_nic }}" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label">Contact Number</label>
            <input type="text" class="form-control" value="{{ $customer->Gua_contact }}" disabled>
        </div>
        <div class="col-md-12">
            <label class="form-label">Address</label>
            <textarea class="form-control" rows="2" disabled>{{ $customer->Gua_address }}</textarea>
        </div>
    </div>
</div>
