@include('component.header.styles')

@include('component.header.modals.cashier-start')
@include('component.header.modals.day-end')

<?php
$query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
$banner = DB::select($query);
?>
@foreach($banner as $item)
    @if($item->banner_status==1)
        <div class="installment-banner">
            <p>📢 <strong>Reminder:</strong> {{$item->banner}}</p>
            <button class="close-banner" onclick="closeBanner()">✖</button>
        </div>

    @endif
@endforeach

<script>
    function closeBanner() {
        document.querySelector('.installment-banner').style.display = 'none';
    }
</script>

@php

    if (!\Illuminate\Support\Facades\Schema::hasTable('user_privileges_has_user')) {
        \Illuminate\Support\Facades\Schema::create('user_privileges_has_user', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('permission_key');
            $table->tinyInteger('value')->default(0);
        });
    }

    // ✅ Now whether the table was just created or already exists, check if the user has permissions
    $userId = (int)session('userid');
    $hasPermissions = DB::table('user_privileges_has_user')
        ->where('user_id', $userId)
        ->exists();

    if (!$hasPermissions) {
        // Permissions arrays
        $mainPermissions = [
            'Customer' => ['customer','add_customer', 'view_customer', 'view_blacklist_customer', 'customer_saving_acc', 'kyc', 'insurance'],
            'Loan Center' => ['loan_center','create_route', 'create_center', 'view_center', 'create_group', 'view_group', 'add_customer_to_group'],
            'Guarantee' => ['guarantee','add_guarantee', 'view_guarantee'],
            'Product' => ['product','add_product', 'view_product', 'create_loan', 'change_collector', 'pending_loan', 'loan_disbursement', 'current_loans', 'settled_loans'],
            'Payment Details' => ['payment_details','add_repayment', 'bulk_repayment', 'loan_settlement', 'loan_reschedule', 'view_payment', 'collector_wise_collection'],
            'Account Center' => ['account_center','bank_cash_account', 'internal_bank_transfer', 'collector_account', 'cheque_details'],
            'Account Department' => ['account_department','add_asset', 'asset_management', 'bank_reconciliation', 'manual_journal', 'chart_of_account'],
            'Loan Calculator' => ['loan_calculator'],
            'Calender' => ['calender','calendar'],
            'Expenses' => ['expenses','add_expenses', 'view_expenses'],
            'User' => ['user','create_user', 'user_privileges'],
            'Reports' => [
                'reports','main_reports_dashboard','prediction_report', 'loan_disbursement_performance', 'payment_detail_report', 'full_loan_detail', 'loan_summary',
                'par_monthly', 'par_weekly', 'loan_status', 'cashflow_accumulated', 'cashflow_monthly', 'profit_loss', 'balance_sheet',
                'trial_balance', 'daily_collection_sheet', 'center_collection_detail', 'center_collection_summary', 'route_collections',
                'repayment_sheet_01', 'repayment_sheet_02', 'repayment_sheet_03', 'repayment_sheet_04', 'repayment_sheet_05','repayment_sheet_06','repayment_sheet_07','repayment_sheet_08','repayment_sheet_09', 'other_charges_report',
                'center_dashboard', 'repayment_summary', 'savings_report', 'arrears_report', 'arrears_overview', 'datewise_cashflow',
                'loan_detail_report', 'collector_report', 'sms_history', 'customer_detail_report', 'officer_customer_detail', 'guardian_detail_report'
            ]
        ];

        $settingsPermissions = [
            'Settings Privilege' => ['my_account', 'settings', 'sms_format', 'document_format', 'company_holidays', 'branches', 'cashier_start', 'cashier_close']
        ];

        $deletePermissions = [
            'Access' => ['dashboard','payment_delete','current_loan_delete','loan_extra_charges','current_loan_agreement','branch_access']
        ];

        $allPermissions = array_merge(
            ...array_values($mainPermissions),
            ...array_values($settingsPermissions),
            ...array_values($deletePermissions)
        );

        $insertData = [];
        foreach ($allPermissions as $perm) {
            $insertData[] = [
                'user_id' => $userId,
                'permission_key' => $perm,
                'value' => 1
            ];
        }

        DB::table('user_privileges_has_user')->insert($insertData);
    }



        $user_id = session('userid');
        $permissions = DB::table('user_privileges_has_user')
            ->where('user_id', $user_id)
            ->pluck('value', 'permission_key')
            ->toArray();

        $privilege = new \stdClass();
        foreach ($permissions as $key => $value) {
            $privilege->$key = $value;
        }
@endphp

@include('component.header.navbar')

@php
    $query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
    $company = DB::select($query);
@endphp
<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="/" class="logo logo-light">
        @foreach($company as $item)
            @php
                $logoPath = 'storage/' . $item->logo;
            @endphp
                        @if ($item->logo && file_exists(public_path($logoPath)))
                            <img src="{{ asset($logoPath) }}" class="logo-img rounded-logo">
                        @else
                            <img src="https://via.placeholder.com/150" class="logo-img rounded-logo">
                        @endif
        @endforeach
    </a>

    <!-- Brand Logo Dark -->
    <a href="/" class="logo logo-dark">
        @foreach($company as $item)
            @php
                $logoPath = 'storage/' . $item->logo;
            @endphp
                        @if ($item->logo && file_exists(public_path($logoPath)))
                            <img src="{{ asset($logoPath) }}" class="logo-img rounded-logo">
                        @else
                            <img src="https://via.placeholder.com/150" class="logo-img rounded-logo">
                        @endif
        @endforeach
    </a>



@include('layout.sidebar')
   
</div>

@include('component.header.scripts')
