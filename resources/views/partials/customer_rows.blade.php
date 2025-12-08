
@foreach($customers as $customer)
    <tr>
        <td>{{ $customer->center_name ?? '-' }}</td>
        <td class="d-none d-lg-table-cell">{{ $customer->group_name ?? '-' }}</td>
        <td>{{ $customer->branch_name }}</td>
        <td>{{ $customer->cus_number }}</td>
        <td>{{ $customer->First_Name }} {{ $customer->Last_Name }}</td>
        <td class="d-none d-md-table-cell">{{ $customer->Nic }}</td>
        <td class="d-none d-lg-table-cell">
            {{ $customer->Address }},{{ $customer->Address_02 }},{{ $customer->Address_03 }}
        </td>
        <td>{{ $customer->Contact_No }}</td>
        <td class="d-none d-lg-table-cell">{{ number_format($customer->points,2,'.',',') }}</td>
        <td>{{ $customer->current_loans }}</td>
        <td class="d-none d-md-table-cell">{{ $customer->settled_loons ?? $customer->settled_loans }}</td>
        <td class="text-center">
            <button type="button" class="btn btn-light" onclick="openMap('{{ $customer->Latitude }}', '{{ $customer->Longitude }}')">
                <i class="bi bi-map fs-4"></i>
            </button>
        </td>

        @if($customer->Status == "1")
            <td class="text-center">
                <span class="badge bg-primary">Active</span>
            </td>
            <td><button class="btn btn-warning" onclick="change_status({{$customer->idCustomer}})">Move To Blacklist</button></td>
        @else
            <td class="text-center">
                <span class="badge bg-danger">Blacklisted</span>
            </td>
            <td><button class="btn btn-warning" disabled>Move To Blacklist</button></td>
        @endif
        <td>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light" onclick="viewCustomer({{$customer->idCustomer}})" title="View Customer Details">
                    <i class="bi bi-eye fs-4"></i>
                </button>
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#view-modal" onclick="load_document({{$customer->idCustomer}});">
                    <i class="bi bi-envelope-check fs-4"></i>
                </button>
                <button type="button" class="btn btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#standard-modal"
                        data-first-name="{{$customer->First_Name}}" data-last-name="{{$customer->Last_Name}}" data-title="{{$customer->Title}}" data-civil="{{$customer->civil_status}}"
                        data-email="{{$customer->Email}}" data-contact-no="{{$customer->Contact_No}}" data-nic="{{$customer->Nic}}"
                        data-gender="{{$customer->Gender}}" data-dob="{{$customer->Dob}}" data-address="{{$customer->Address}}" data-address_2="{{$customer->Address_02}}" data-address_3="{{$customer->Address_03}}" data-city="{{$customer->City}}"
                        data-Per_Address_01="{{$customer->Per_Address_01}}" data-Per_Address_02="{{$customer->Per_Address_02}}" data-Per_Address_03="{{$customer->Per_Address_03}}" data-State="{{$customer->State}}" data-landline="{{$customer->Landline}}" data-gua_title="{{$customer->Gua_title}}" data-gua_name="{{$customer->Gua_name}}"
                        data-guardian_gender="{{$customer->Guardian_gender}}" data-gua_relation="{{$customer->Gua_relation}}" data-gua_occu="{{$customer->Gua_occu}}" data-gua_contact="{{$customer->Gua_contact}}"
                        data-gua_address="{{$customer->Gua_address}}" data-cus_number="{{$customer->cus_number}}"
                        data-occu_job_position="{{$customer->occu_job_position}}" data-occu_monthly_salary="{{$customer->occu_monthly_salary}}" data-occu_address_01="{{$customer->occu_address_01}}" data-occu_address_02="{{$customer->occu_address_02}}"
                        data-occu_contact_no="{{$customer->occu_contact_no}}" data-occu_longitude="{{$customer->occu_longitude}}" data-occu_latitude="{{$customer->occu_latitude}}" data-occu_address_03="{{$customer->occu_address_03}}"
                        data-note="{{ $customer->Note !== null ? $customer->Note : '-' }}" data-risk-level="{{$customer->Customer_Risk_Level}}" data-gua_nic="{{$customer->Gua_nic}}"
                        data-customer-id="{{$customer->idCustomer}}" data-longitude="{{$customer->Longitude}}" data-latitude="{{$customer->Latitude}}" data-root="{{$customer->route_id}}"
                >
                    <i class="bi bi-pencil fs-4"></i></button>
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#location-modal" onclick="setLocationId({{$customer->idCustomer}}, '{{$customer->Latitude}}', '{{$customer->Longitude}}')">
                    <i class="bi bi-geo-alt fs-4"></i>
                </button>
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#standard-modal_2" onclick="set_cus({{$customer->idCustomer}})">
                    <i class="bi bi-envelope-paper fs-4"></i>
                </button>
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#bank-modal" onclick="setbankid({{$customer->idCustomer}})">
                    <i class="bi bi-bank2 fs-4"></i>
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteCustomer({{$customer->idCustomer}})">
                    <i class="bi bi-trash fs-4"></i>
                </button>
                @if (!empty($customer->Cus_phto))
                    <a href="{{ Storage::url($customer->Cus_phto) }}" target="_blank" class="btn btn-dark">
                        <i class="bi bi-people fs-4"></i>
                    </a>
                @else
                    <button class="btn btn-dark" disabled>
                        <i class="bi bi-people fs-4"></i>
                    </button>
                @endif

                <a href="/customer_road_map/{{$customer->idCustomer}}" target="_blank" class="btn btn-primary"><i class="bi bi-bar-chart-steps fs-4"></i></a>
                <button type="button"
                        class="btn btn-info"
                        data-bs-toggle="modal"
                        data-bs-target="#upload-photo-modal"
                        onclick="openPhotoUpload({{ $customer->idCustomer }})">
                    <i class="bi bi-camera-fill fs-4"></i>
                </button>

            </div>
        </td>
    </tr>
@endforeach
