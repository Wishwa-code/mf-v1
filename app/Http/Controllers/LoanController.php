<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Models\Reschedule;

class LoanController extends Controller
{

    protected $customerLogController;
    protected $CapitalBalanceController;

    public function __construct(CustomerLogController $customerLogController,CapitalBalanceController $capitalBalanceController)
    {
        $this->customerLogController = $customerLogController;
        $this->CapitalBalanceController = $capitalBalanceController;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = tableWithBranch('customer')->where('Status','=','1')->get();
        $center = tableWithBranch('center')->get();
        $product = tableWithBranch('loan_category')->get();
        $company = DB::table('company')->first();
        $lending_officer = tableWithBranch('user')->where('lending_officer', '=', '1')->get();
        $collector = tableWithBranch('user')
            ->where('collector','=','1')
            ->get();
        return view('pages.IssueLoan', compact('customers', 'center', 'product', 'lending_officer','company','collector'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = tableWithBranch('customer')->where('Status','=','1')->get();
        $center = tableWithBranch('center')->get();
        $product = tableWithBranch('loan_category')->get();
        $company = DB::table('company')->first();
        $lending_officer = tableWithBranch('user')->where('lending_officer', '=', '1')->get();
        $collector = tableWithBranch('user')
            ->where('collector','=','1')
            ->get();
        return view('pages.LoanCalculator', compact('customers', 'center', 'product', 'lending_officer','company','collector'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user_id = (int) session('userid');

            $loan = new Loan();
            $loan->created_at = Carbon::now();
            $date = Carbon::now()->toDateString();

            $customer_id      = $request->customer_id;
            $type_loan_number = $request->type_loan_number;

            // Step 1: Fetch necessary data
            $maxId = DB::table('customer_loan')->where('branch_id', session('branch_id'))->max('idCustomer_Loan') ?? 1;
            Log::info($maxId);
            $maxId++;
            $type = $request->loan_type;

            $company = tableWithBranch('company')->first();
            $branch_no = $company->branch;
            $loan_format = $company->loan_format;

            $loan_num_type = $company->loan_num_type;

            // Initialize the loan_number_txt
            $loan_number_txt = $type_loan_number;
            // Format the ID with leading zeros (e.g., 001, 010, 100, etc.)
            $formatted_loan_id = str_pad($maxId, 3, '0', STR_PAD_LEFT);
            $product_code = tableWithBranch('loan_category')
                ->where('idLoan_Category', '=', $request->loan_cate_id)
                ->first();
            $cus_loan_count = tableWithBranch('customer_loan')
                ->where('Customer_idCustomer', '=', $customer_id)
                ->count();

            // Check max allowed loans limit
            $maxAllowedLoans = DB::table('app_settings')->where('key', 'max_allowed_loans')->value('value') ?? 5;
            $currentActiveLoans = tableWithBranch('customer_loan')
                ->where('Customer_idCustomer', $customer_id)
                ->whereIn('Status', ['0', '-1']) // Current loans (0) + Pending loans (-1)
                ->count();

            if ($currentActiveLoans >= $maxAllowedLoans) {
                DB::rollBack();
                return response()->json(['message' => "Customer already has maximum allowed loans ({$maxAllowedLoans}). Current active loans: {$currentActiveLoans}"], 422);
            }

            // Check guarantees restriction
            $guaranteesRestriction = DB::table('app_settings')->where('key', 'guarantees_restriction')->value('value') ?? 'not_required';
            if ($guaranteesRestriction === 'required') {
                // Get the required guarantee count from the loan product
                $loanProduct = tableWithBranch('loan_category')
                    ->where('idLoan_Category', $request->loan_cate_id)
                    ->first();
                
                if ($loanProduct && $loanProduct->Guarantee_count > 0) {
                    $requiredGuaranteeCount = (int) $loanProduct->Guarantee_count;
                    $witnessesArray = $request->input('witnessesArray', []);
                    
                    // Count valid guarantors (cus_id not empty or "0")
                    $validGuarantorCount = 0;
                    foreach ($witnessesArray as $witness) {
                        if (isset($witness['cus_id']) && $witness['cus_id'] !== '0' && !empty($witness['cus_id'])) {
                            $validGuarantorCount++;
                        }
                    }
                    
                    // Ensure ALL required guarantors are provided
                    if ($validGuarantorCount < $requiredGuaranteeCount) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "All required guarantors must be added. This loan requires {$requiredGuaranteeCount} guarantor(s). (Currently added: {$validGuarantorCount})"
                        ], 422);
                    }
                }
            }

            // Check document upload restriction
            $documentUploadRestriction = DB::table('app_settings')->where('key', 'document_upload_restriction')->value('value') ?? 'not_required';
            if ($documentUploadRestriction === 'required') {
                // Check if this loan category has required documents
                $requiredDocumentsCount = tableWithBranch('required_documents')
                    ->where('Loan_Category_idLoan_Category', $request->loan_cate_id)
                    ->count();
                
                if ($requiredDocumentsCount > 0) {
                    // Get the count of uploaded documents from the request
                    $uploadedDocumentsCount = (int) $request->input('uploaded_documents_count', 0);
                    
                    // Ensure ALL required documents are uploaded
                    if ($uploadedDocumentsCount < $requiredDocumentsCount) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "All required documents must be uploaded. Please upload all {$requiredDocumentsCount} required document(s) before proceeding. (Currently uploaded: {$uploadedDocumentsCount})"
                        ], 422);
                    }
                }
            }

            // Check first installment date restriction
            $issueDate = $request->input('issue_date');
            $installments = $request->input('installment', []);
            
            if ($issueDate && !empty($installments)) {
                // Get the first installment date
                $firstInstallment = is_array($installments) ? reset($installments) : null;
                $firstInstallmentDate = $firstInstallment['installmentDate'] ?? null;
                
                if ($firstInstallmentDate) {
                    // Get loan product to determine the loan type
                    $loanProduct = tableWithBranch('loan_category')
                        ->where('idLoan_Category', $request->loan_cate_id)
                        ->first();
                    
                    if ($loanProduct) {
                        $interestPeriod = $loanProduct->Interest_period;
                        $settingKey = null;
                        
                        // Map Interest_period to the appropriate setting key
                        if (in_array($interestPeriod, ['Daily', 'Per Day'])) {
                            $settingKey = 'first_installment_daily';
                        } elseif (in_array($interestPeriod, ['Weekly', 'Per Week'])) {
                            $settingKey = 'first_installment_weekly';
                        } elseif (in_array($interestPeriod, ['Per Month', 'Monthly'])) {
                            $settingKey = 'first_installment_monthly';
                        }
                        
                        if ($settingKey) {
                            // Get the maximum allowed days from settings
                            $maxDays = (int) DB::table('app_settings')
                                ->where('key', $settingKey)
                                ->value('value');
                            
                            if ($maxDays > 0) {
                                // Calculate the difference in days
                                $issueDateObj = new \DateTime($issueDate);
                                $firstInstallmentDateObj = new \DateTime($firstInstallmentDate);
                                $daysDifference = $issueDateObj->diff($firstInstallmentDateObj)->days;
                                
                                // Check if the first installment date exceeds the allowed days
                                if ($daysDifference > $maxDays) {
                                    DB::rollBack();
                                    $loanTypeText = str_replace(['Per ', 'Per'], '', $interestPeriod);
                                    return response()->json([
                                        'message' => "The first installment date cannot be more than {$maxDays} days from the issue date for {$loanTypeText} loans. Current difference: {$daysDifference} days."
                                    ], 422);
                                }
                            }
                        }
                    }
                }
            }

            if ($type_loan_number == "") {
                if ($loan_num_type === "Customize") {
                    $branch_no_txt = $branch_no . '/';
                    if ($branch_no == "") {
                        $branch_no_txt = "";
                    }
                    $loan_number_txt = $branch_no_txt . $formatted_loan_id;
                } else {

                    if ($type == "0") {
                        $loan_format = $company->inv_loan_format;
                        $cus_root = tableWithBranch('customer', 'customer')
                            ->leftjoin('route', 'route.id_route', '=', 'customer.route_id')
                            ->where('idCustomer', '=', $customer_id)
                            ->first();
                        // Step 1: Get the route_id of the given customer
                        $routeId = DB::table('customer')
                            ->where('idCustomer', $customer_id)
                            ->value('route_id');

                        // Step 2: Count total loans in that route
                        $routewiseloanCount = DB::table('customer_loan as cl')
                            ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
                            ->where('c.route_id', $routeId)
                            ->count();

                        $placeholders = [
                            '@Branch_No@'    => $branch_no,
                            '@Root@'         => $cus_root->root_code ?? '',
                            '@Product_Code@' => $product_code->Product_code,
                            '@Customer_No@'  => str_pad($cus_root->idCustomer, 3, '0', STR_PAD_LEFT),
                            '@Auto_Id@'      => $formatted_loan_id,
                            '@Loan_Count@'   => $cus_loan_count + 1,
                            '@RootlyCount@'  => str_pad($routewiseloanCount + 1, 3, '0', STR_PAD_LEFT),
                        ];

                        // Step 3: Replace placeholders in the loan_format
                        $loan_number_txt = $loan_format;
                        foreach ($placeholders as $placeholder => $value) {
                            $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
                        }
                    } else {
                        $loan_no = tableWithBranch('customer', 'customer')
                            ->leftJoin('group_has_customer', 'group_has_customer.cus_id', '=', 'customer.idCustomer')
                            ->leftJoin('customer_group', 'customer_group.idCustomer_Group', '=', 'group_has_customer.group_id')
                            ->leftJoin('center', 'center.idCenter', '=', 'customer_group.center_id')
                            ->leftJoin('route', 'route.id_route', '=', 'center.route_id')
                            ->where('customer.idCustomer', $customer_id)
                            ->select('customer.*', 'customer_group.*', 'center.*', 'route.root_code as root')
                            ->first();

                        if ($loan_no) {
                            $center_id = $loan_no->idCenter;
                            $center_customer_count = DB::table('customer')
                                ->join('group_has_customer', 'group_has_customer.cus_id', '=', 'customer.idCustomer')
                                ->join('customer_group', 'customer_group.idCustomer_Group', '=', 'group_has_customer.group_id')
                                ->where('customer_group.center_id', $center_id)
                                ->distinct('customer.idCustomer') // Optional if customers can be in multiple groups
                                ->count('customer.idCustomer');

                            // Step 1: Get the route_id of the given customer
                            $routeId = DB::table('customer')
                                ->where('idCustomer', $customer_id)
                                ->value('route_id');

                            // Step 2: Count total loans in that route
                            $routewiseloanCount = DB::table('customer_loan as cl')
                                ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
                                ->where('c.route_id', $routeId)
                                ->count();

                            // Step 2: Define the mapping
                            $placeholders = [
                                '@Branch_No@'        => $branch_no,
                                '@Center_No@'        => $loan_no->No,
                                '@Group_No@'         => $loan_no->Group_No,
                                '@Product_Code@'     => $product_code->Product_code,
                                '@Root@'             => $loan_no->root,
                                '@Customer_No@'      => str_pad($loan_no->idCustomer, 3, '0', STR_PAD_LEFT),
                                '@Auto_Id@'          => $formatted_loan_id,
                                '@Loan_Count@'       => $cus_loan_count + 1,
                                '@Center_Cus_Count@' => $center_customer_count + 1,
                                '@RootlyCount@'      => str_pad($routewiseloanCount + 1, 3, '0', STR_PAD_LEFT),
                            ];

                            // Step 3: Replace placeholders in the loan_format
                            $loan_number_txt = $loan_format;
                            foreach ($placeholders as $placeholder => $value) {
                                $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
                            }
                        }
                    }
                }
            } else {
                $loan_no = tableWithBranch('customer_loan')
                    ->where('Loan_No', '=', $type_loan_number)
                    ->first();
                if ($loan_no) {
                    DB::rollBack();
                    return response()->json(['item' => "1"], 200);
                }
            }

            if (!Schema::hasColumn('customer_loan', 'panelty_method')) {
                DB::statement(
                    "ALTER TABLE `customer_loan`
                 ADD COLUMN `panelty_method` VARCHAR(45) NOT NULL
                 DEFAULT 'every_installment'"
                );
            }

            if (!Schema::hasColumn('customer_loan', 'Panelty_period')) {
                DB::statement(
                    "ALTER TABLE `customer_loan`
                 ADD COLUMN `Panelty_period` VARCHAR(45) NOT NULL
                 DEFAULT 'Daily'"
                );
            }


            if ($type == "0") {
                DB::transaction(function () use ($branch_no, $product_code, $customer_id) {
                    // 1) Lock the company row for this branch to avoid race conditions
                    $company = DB::table('company')
                        ->where('branch_id', session('branch_id'))
                        ->lockForUpdate()
                        ->first();

                    $nextSeq = (int) $company->inv_customer_number;          // current value
                    // If you want zero-padding like 00001, uncomment the next line:
                    // $seqTxt = str_pad((string)$nextSeq, 5, '0', STR_PAD_LEFT);
                    $seqTxt = (string) $nextSeq;

                    // 2) Build the customer number using the locked value
                    $customer_number_txt = $branch_no . '/' . $product_code->Product_code . '/' . $seqTxt;

                    // 3) Log the old value (read with the same branch scope)
                    $customer_old_details = tableWithBranch('customer')
                        ->where('idCustomer', '=', $customer_id)
                        ->first();

                    // 4) Update the customer record
                    updateWithBranch('customer', 'idCustomer', $customer_id, [
                        'cus_number' => $customer_number_txt
                    ]);

                    // 5) Add a customer log
                    $CustomerLogController = new CustomerLogController();
                    $req = new Request([
                        'customer_id'    => $customer_id,
                        'description'    => "Customer Number Changed (Individual Loan) From " . ($customer_old_details->cus_number ?? 'N/A') . " To " . $customer_number_txt,
                        'description_id' => $customer_id,
                        'comment'        => 'Change Customer Number',
                        'type'           => 'Customer Update',
                    ]);
                    $CustomerLogController->store($req);

                    // 6) Increment the sequence atomically
                    DB::table('company')
                        ->where('branch_id', session('branch_id'))
                        ->update(['inv_customer_number' => DB::raw('inv_customer_number + 1')]);
                });
            }



            if (($request->interest_method ?? '') === 'Reducing Balance') {
                $sumCapital = 0.0;
                $sumInterest = 0.0;
                $sumTotalAmount = 0.0;

                $firstTotalBalance = null;
                $maxTotalBalance   = 0.0;
                $lastNonZero       = 0.0;

                foreach (($request->installment ?? []) as $row) {
                    $installmentAmount = (float) data_get($row, 'installmentAmount', 0); // per-row installment
                    $totalAmount       = (float) data_get($row, 'totalAmount', 0);       // if you have a "total" column per row
                    $totalBalance      = (float) data_get($row, 'totalBalance', 0);      // running balance column

                    // interest: prefer explicit, else fall back to installmentBalance if that's your interest column
                    $interestPerRow = (float) (
                        data_get($row, 'interestAmount') ??
                        data_get($row, 'installmentBalance', 0)
                    );

                    // capital: prefer explicit, else derive
                    $capitalPerRow = (float) (
                        data_get($row, 'capitalAmount') ??
                        max(0, $installmentAmount - $interestPerRow)
                    );

                    $sumCapital     += $capitalPerRow;
                    $sumInterest    += $interestPerRow;
                    $sumTotalAmount += $totalAmount;

                    if ($totalBalance > 0) {
                        if ($firstTotalBalance === null) $firstTotalBalance = $totalBalance;
                        if ($totalBalance > $maxTotalBalance) $maxTotalBalance = $totalBalance;
                        $lastNonZero = $totalBalance;
                    }
                }

                $providedGrand = (float) ($request->input('total_balance_total') ?? $request->input('grand_total_balance') ?? 0);
                $grandTotal = $sumCapital + $sumInterest;

                // Overwrite the four fields from table totals
                $loan->Total_Loan_Amount   = round($grandTotal, 2);
                $loan->Balance_Amount      = round($grandTotal, 2);
                $loan->capital_balance     = round($sumCapital, 2);
                $loan->installment_balance = round($sumInterest, 2);
                $loan->Interest_Amount     = round($sumInterest, 2);
            } else {
                $loan->Total_Loan_Amount   = $request->total_loan_amount;
                $loan->Balance_Amount      = $request->total_loan_amount;
                $loan->capital_balance     = $request->total_capital_amount;
                $loan->installment_balance = $request->total_interest_amount;
                $loan->Interest_Amount     = $request->interest_amount;
            }

            $loan->Loan_No                           = $loan_number_txt;
            $loan->Loan_Category_idLoan_Category     = $request->loan_cate_id;
            $loan->Customer_idCustomer               = $request->customer_id;
            $loan->Leasing_type                      = $request->lease_type;
            $loan->Vehicle_No                        = $request->vehicle_num;
            $loan->Date_Time                         = $request->issue_date;
            $loan->Amount                            = $request->loan_amount;
            $loan->Interest_Rate                     = $request->interest;
            $loan->Panalty_Rate                      = $request->panelty_amount;
            $loan->Installment_Count                 = $request->ins_count;

            $loan->Total_Other_Amount                = $request->total_loan_charge;
            $loan->Other_Amount_Balance              = $request->loan_charge_balance;

            $loan->Installment_Amount                = $request->new_interest_amount;
            $loan->Collection_Type                   = $request->collection_type;
            $loan->Collection_Date                   = $request->installment_date_txt;
            $loan->Panalty_Date                      = $request->panelty_date;

            $loan->Status                            = "-1";
            $loan->User_idUser                       = $user_id;

            $loan->type                              = $request->interest_method;
            $loan->Interest_period                   = $request->Interest_period;
            $loan->lending_officer_id                = $request->lending_officer;
            $loan->collector_id                      = $request->collector_officer;
            $loan->cus_bank_account                  = $request->bank_acc;
            $loan->repayment_duration                = $request->repayment_duration_period;
            $loan->loan_broker                       = $request->loan_broker;
            $loan->loan_broker_commission            = $request->loan_broker_commission;
            $loan->saving_amount                     = $request->saving_amount ?? '0.00';
            $loan->branch_id                         = session('branch_id');

            $product = tableWithBranch('loan_category')->where('idLoan_Category', '=', $request->loan_cate_id)->first();
            if ($product) {
                $loan->panelty_method = $product->panelty_method;
                $loan->Panelty_period = $product->Panelty_period;
            }

            $loan->save();

            $id = $loan->id;

            $product = tableWithBranch('loan_category')->where('idLoan_Category', '=', $request->loan_cate_id)->first();
            if ($product) {
                $enable_saving_process = $product->enable_saving_process;
                if ($enable_saving_process == "Yes") {
                    $saving_format = $company->account_saving_type;

                    $saving_number_txt = "";
                    if ($saving_format === "Customize") {

                    } else {
                        $loan_no = tableWithBranch('customer', 'customer')
                            ->leftJoin('group_has_customer', 'group_has_customer.cus_id', '=', 'customer.idCustomer')
                            ->leftJoin('customer_group', 'customer_group.idCustomer_Group', '=', 'group_has_customer.group_id')
                            ->leftJoin('center', 'center.idCenter', '=', 'customer_group.center_id')
                            ->join('route', 'route.id_route', '=', 'center.route_id')
                            ->where('customer.idCustomer', $customer_id)
                            ->first();

                        if ($loan_no) {
                            // Step 2: Define the mapping
                            $placeholders = [
                                '@Branch_No@'   => $branch_no,
                                '@Root@'        => $loan_no->Group_No,
                                '@Center_No@'   => $loan_no->No,
                                '@Group_No@'    => $loan_no->Group_No,
                                '@Customer_No@' => $loan_no->idCustomer,
                                '@Auto_Id@'     => $formatted_loan_id,
                                '@Loan_Count@'  => $cus_loan_count + 1,
                            ];

                            // Step 3: Replace placeholders in the loan_format
                            $saving_number_txt = $loan_format;
                            foreach ($placeholders as $placeholder => $value) {
                                $saving_number_txt = str_replace($placeholder, $value, $saving_number_txt);
                            }
                        }
                    }

                    // Prepare data for Customer_Saving_Accounts
                    $savingData = [
                        'Customer_Id' => $customer_id,
                        'Loan_Id'     => $id,
                        'Loan_No'     => $loan_number_txt,
                        'Created_Date'=> date('Y-m-d H:i:s'),
                        'Account_No'  => $saving_number_txt,
                        'Account_Type'=> "Saving",
                        'Balance'     => "0.00",
                        'Status'      => "1",
                    ];

                    // Insert and get the ID of the saving account
                    $saving = insertWithBranch('Customer_Saving_Accounts', $savingData);

                    // Prepare data for Savings_Account_Log
                    $logData = [
                        'Saving_Acount_Id' => $saving,
                        'Date_Time'        => date('Y-m-d H:i:s'),
                        'Type'             => "Saving Account",
                        'Description'      => "Account Creation",
                        'Credit'           => 0.00,
                        'Debit'            => 0.00,
                        'Balance'          => 0.00,
                        'User'             => $user_id,
                    ];

                    // Insert log entry
                    insertWithBranch('Savings_Account_Log', $logData);
                }
            }

            $saving_check = $request->saving;

            // Flags sent from the frontend
            $routeCollectionType = (string) $request->input('route_collection_type', '-');
            $collectionDateMode  = (string) $request->input('collection_date_type_global', '-');

// Only use the two extra fields in this mode:
            $useRouteCollection = ($routeCollectionType === 'fixed' && $collectionDateMode === 'according_to_route');

            foreach ($request->installment as $item) {
                $customerLoanId     = $id;
                $no                 = $item['No'];
                $installmentDate    = $item['installmentDate'];
                $installmentAmount  = $item['installmentAmount'];
                $capitalAmount      = $item['capitalAmount'];
                $interestAmount     = $item['interestAmount'];
                $panaltyDate        = $item['panaltyDate'];
                $panaltyAmount      = $item['panaltyAmount'];
                $totalAmount        = $item['totalAmount'];

                $paidAmount         = "0.00";
                $panaltyBalance     = $item['panaltyBalance'];
                $installmentBalance = $item['installmentBalance'];
                $totalBalance       = $item['totalBalance'];

                // From UI (only meaningful in 'fixed' + 'according_to_route')
                $collectionDate = $useRouteCollection
                    ? ($item['collectionDate'] ?? $installmentDate)
                    : $installmentDate;
                $difference     = $useRouteCollection
                    ? (isset($item['difference']) && $item['difference'] !== '' ? (int)$item['difference'] : null)
                    : null;

                // Base payload (common)
                $insert = [
                    'Customer_Loan_idCustomer_Loan' => $customerLoanId,
                    'No'                 => $no,
                    'Installment_Date'   => $installmentDate,
                    'Installment_Amount' => $installmentAmount,
                    'capital_amount'     => $capitalAmount,
                    'interest_amount'    => $interestAmount,
                    'Panalty_Amount'     => $panaltyAmount,
                    'Total_Amount'       => $totalAmount,
                    'Paid_Amount'        => $paidAmount,
                    'Panalty_Balance'    => $panaltyBalance,
                    'Interest_Balance'   => $interestAmount,
                    'capital_balance'    => $capitalAmount,
                    'Total_Balance'      => $totalBalance,
                    'Status'             => '0',
                    'Panelty_date'       => $panaltyDate,
                    'Panelty_status'     => '0',
                    'branch_id'          => session('branch_id'),

                    // Always present in schema; set them by mode
                    'Collection_Date'    => $collectionDate,   // null if not the route-based mode
                    'Collection_Diff'    => $difference,       // null if not the route-based mode
                ];

                // Savings on/off
                if (($saving_check ?? 'No') === "Yes") {
                    $insert['Saving_amount']  = $item['savingAmount'];
                    $insert['Saving_balance'] = $item['savingBalance'];
                } else {
                    // Ensure zeros if columns exist and you want explicit values when saving is off
                    $insert['Saving_amount']  = 0.00;
                    $insert['Saving_balance'] = 0.00;
                }

                DB::table('installments')->insert($insert);
            }


            // $HolidayController=new HolidayController();
            // $HolidayController->store($id);

            if (isset($request->witnessesArray) && count($request->witnessesArray) > 0) {
                foreach ($request->witnessesArray as $item) {
                    $newtype = $item['guatantor_type'];
                    $newtypeshow = "Guarantor";
                    if ($newtype == "0") {
                        $newtypeshow = "Cross Customer";
                    }

                    $witnessData = [
                        'Customer_Loan_idCustomer_Loan' => $id,
                        'cus_id' => $item['cus_id'],
                        'type'   => $newtypeshow,
                    ];

                    insertWithBranch('witness', $witnessData);
                }
            }

            if (isset($request->loan_charge_table) && count($request->loan_charge_table) > 0) {
                foreach ($request->loan_charge_table as $item) {
                    $customerLoanId = $id;
                    $Description    = $item['Description'];
                    $Type           = $item['Type'];
                    $Amount         = $item['Amount'];

                    $loanOtherChargesData = [
                        'Description'                      => $Description,
                        'Type'                             => $Type,
                        'Amount'                           => $Amount,
                        'Customer_Loan_idCustomer_Loan'    => $customerLoanId,
                    ];

                    insertWithBranch('loan_other_charges', $loanOtherChargesData);
                }
            }

            $product = tableWithBranch('loan_category')->where('idLoan_Category', '=', $request->loan_cate_id)
                ->first();

            $level = tableWithBranch('level')->where('product_id', '=', $request->loan_cate_id)->get();
            foreach ($level as $item) {
                $loanApprovalData = [
                    'loan_id'    => $id,
                    'level'      => $item->type,
                    'level_id'   => $item->id,
                    'description'=> $item->description,
                    'comment'    => '',
                    'user_id'    => 0,
                    'date'       => '-',
                ];

                insertWithBranch('loan_has_approval', $loanApprovalData);

                $checklist = tableWithBranch('approval_checklist')->where('level_id', '=', $item->id)->get();
                foreach ($checklist as $check_item) {
                    $loanChecklistData = [
                        'loan_id'    => $id,
                        'level'      => $item->id,
                        'description'=> $check_item->description,
                        'status'     => '0',
                    ];
                    insertWithBranch('loan_has_approval_checklist', $loanChecklistData);
                }
            }

            // Don't overwrite the main $request. Use a separate Request instance for logging.
            $logRequest = new Request([
                'customer_id'    => $request->customer_id,
                'description'    => "Created new loan ({$request->loan_number_txt})\nLoan Amount : {$request->loan_amount}\nProduct name : {$product->Name}",
                'description_id' => $id,
                'comment'        => ' ',
                'type'           => 'Create Loan',
            ]);

            // Call the store method of CustomerLogController
            $this->customerLogController->store($logRequest);
            $this->CapitalBalanceController->create($id);

            $skip = DB::table('app_settings')
                ->where('key', '=','due_skip_type')
                ->value('value');
            Log::info($skip);
            $skipType="installment";
            if ($skip=="skip_day"){
                $skipType="day";
            }

            $holiday=new HolidayController();
            $holiday->index('loan',$id,$skipType);

            DB::commit();

            // Fetch customer once
            $customer = tableWithBranch('customer', 'customer')
                ->select([
                    'idCustomer',
                    'cus_number',
                    'Title',
                    'First_Name',
                    'Last_Name',
                    'Email',
                    'Contact_No',
                    'contact_number_2',
                    'Nic',
                    'Gender',
                    'Dob',
                ])
                ->where('idCustomer', $customer_id)
                ->first();

            $customerPayload = null;
            if ($customer) {
                $fullName = trim(implode(' ', array_filter([
                    $customer->First_Name,
                    $customer->Last_Name
                ])));

                $customerPayload = [
                    'cus_number'       => (string) $customer->cus_number,
                    'name'             => $fullName,
                ];
            }



            return response()->json([
                'item' => $loan->getKey(),
                'type' => $type,
                'loan' => [
                    'loan_no'           => $loan->Loan_No,
                    'amount'            => $loan->Amount,
                    'interest_rate'     => $loan->Interest_Rate,
                    'installments'      => $loan->Installment_Count,
                    'interest_amt'      => $loan->Interest_Amount,
                ],
                'customer' => $customerPayload,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            // You can log the error if needed:
             \Log::error('Create Loan failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Create loan failed. Transaction rolled back.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customers = tableWithBranch('customer')
            ->where('idCustomer', '=', $id)->first();
        $category = tableWithBranch('loan_category')->get();
        return view('pages.IssueLoan_2page', compact('id', 'customers', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        // Fetch the list of centers
        $centers = DB::table('center')->where('branch_id', session('branch_id'))->get();

        // Fetch the list of groups
        $groups = DB::table('customer_group')->where('branch_id', session('branch_id'))->select('Group_No as group_name')->distinct()->get();

        // Initialize the query for fetching loans
        $query = tableWithBranch('customer_loan', 'customer_loan')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin(DB::raw('(SELECT group_has_customer.cus_id, IFNULL(customer_group.Group_No, "-") as group_name
                 FROM group_has_customer
                 LEFT JOIN customer_group ON group_has_customer.group_id = customer_group.idCustomer_Group) as subquery'),
                'customer.idCustomer', '=', 'subquery.cus_id')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select('customer_loan.*',
                'loan_category.Name as loan_name',
                DB::raw('IFNULL(center.No, "-") as center_no'),
                DB::raw('IFNULL(subquery.group_name, "-") as group_name'),
                'customer.First_Name as First_Name',
                'customer.cus_number as cus_number',
                'customer.Last_Name as Last_Name',
                'loan_category.Name as Name')
            ->where('customer_loan.Status', '!=', '-1');

        // Apply center filter if center_id is provided
        if ($request->has('center_id') && $request->center_id != '') {
            $query->where('center.idCenter', $request->center_id);
        }

        // Apply group filter if group_name is provided
        if ($request->has('group_name') && $request->group_name != '') {
            $query->where('subquery.group_name', $request->group_name);
        }

        // Execute the query and get the loan data
        $loan = $query->get();

        // Pass loan, centers, and groups data to the view
        return view('pages.AllLoanReport', compact('loan', 'centers', 'groups'));
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


//    public function saveFiles(Request $request)
//    {
//        // Define the directory where the file will be stored
//        $directory = 'documents';
//
//// Check if the directory exists, create it if not
//        if (!Storage::disk('public')->exists($directory)) {
//            Storage::disk('public')->makeDirectory($directory);
//        }
//
//        $documentsArray = $request->file('documents');
//
//        foreach ($documentsArray as $index => $file) {
//            if ($file) {
//                // Generate a unique filename to prevent overwriting files with the same name
//                $fileName = uniqid() . '_' . $file->getClientOriginalName();
//
//                // Move the file to the storage directory
//                $storedFile = Storage::disk('public')->putFileAs($directory, $file, $fileName);
//
//                // Retrieve document name and checked status from the request
//                $documentName = $request->documentNames[$index];
//                $issue_checked = $request->issue_checked[$index];
//
//                // Store document information in the database
//                $documentData = [
//                    'Name' => $documentName,
//                    'Path' => $storedFile,
//                    'Customer_Loan_idCustomer_Loan' => $request->id,
//                    'create_loan_check' => $issue_checked,
//                ];
//
//                DB::table('documents')->insert($documentData);
//            }
//        }
//
//        return response()->json(['message' => 'Documents saved successfully'], 200);
//
//
//
//        // Return a success response if the documents were processed successfully
//        return response()->json(['message' => 'Documents processed successfully.']);
//
//
//
//
//
//    }

    public function saveFiles(Request $request) {
        $documentNamesArray = $request->input('documentNames');
        $issueCheckedArray = $request->input('issue_checked');
        $documentsArray = $request->file('documents');

        if ($documentNamesArray && is_array($documentNamesArray) && $issueCheckedArray && is_array($issueCheckedArray)) {
            foreach ($documentNamesArray as $index => $documentName) {
                $file = $documentsArray[$index] ?? null;
                $issue_checked = $issueCheckedArray[$index];

                if ($file) {
                    $fileName = uniqid() . '_' . $file->getClientOriginalName();
                    $storedFile = Storage::disk('public')->putFileAs('documents', $file, $fileName);

                    // Prepare data for the document insertion
                    $documentData = [
                        'Name' => $documentName,
                        'Path' => $storedFile,
                        'Customer_Loan_idCustomer_Loan' => $request->id,
                        'create_loan_check' => $issue_checked,
                    ];

// Insert the document data with branch scoping
                    insertWithBranch('documents', $documentData);

                } else {
                    // Prepare data for the document insertion
                    $documentData = [
                        'Name' => $documentName,
                        'Path' => "-", // Assuming the path is intentionally set to "-"
                        'Customer_Loan_idCustomer_Loan' => $request->id,
                        'create_loan_check' => $issue_checked,
                    ];

// Insert the document data with branch scoping
                    insertWithBranch('documents', $documentData);

                }
            }
            return response()->json(['message' => 'Documents saved successfully']);
        }

        return response()->json(['message' => 'Invalid input'], 400);
    }

    public function saveFiles_Pending_loan(Request $request)
    {


        // Handle file upload
        if ($request->hasFile('documents')) {
            $file = $request->file('documents');
            // Define a path and filename for the uploaded file
            $filePath = 'documents/';
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store the file on the server (in storage/app/public/loan_documents)
            $file->storeAs('public/' . $filePath, $fileName);

            $documentData = [
                'Name' => $request->documentNames,
                'Path' => $filePath.$fileName,
                'Customer_Loan_idCustomer_Loan' => $request->id,
            ];

            // Insert the document data with branch scoping
            insertWithBranch('documents', $documentData);

            // Return a success response
            return response()->json(['success' => true], 200);
        }

        // If no file is provided or validation fails
        return response()->json(['error' => 'File upload failed'], 400);
    }





    public function loan_view(string $id)
    {

        $company = DB::table('company')->first(); // Assuming company does not need branch filtering
        $customers = tableWithBranch('customer')->get(); // Get customers with branch filtering
        $center = tableWithBranch('center')->get(); // Get centers with branch filtering
        $product = tableWithBranch('loan_category')->get(); // Get loan categories with branch filtering
        $loan = tableWithBranch('customer_loan', 'idCustomer_Loan') // Get loan with branch filtering
        ->where('idCustomer_Loan', $id)
            ->first();

        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }
        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();


        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = []; // Initialize an array to store details

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                // Add guarantor details to the array
                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                // Add customer details to the array
                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer
                ];
            }
        }
        $witnessCount = count($witnessDetails); // Get the length of the array


        return view('pages.LoanView', compact('customers','company', 'center', 'product', 'id', 'loan', 'installments', 'witnessDetails', 'witnessCount'));
    }


    public function loan_view_Np(string $id,int $type=0)
    {
        $this->ensureResheduleTable();
        $company = DB::table('company')->first();
        // Fetch the loan
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }

        // Fetch the installments
        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->orderBy('idInstallments')->get();
        $Saving_amountSum = $installments->sum('Saving_amount');
        $Panalty_BalanceSum = $installments->sum('Panalty_Balance');

        $Total_Balance = $installments->sum('Total_Balance');


        $loan_log_sum=tableWithBranch('Loan_Log')->where('Loan_ID','=',$id)->where('Type','=','Customer Payment')->get();
        $Panalty_Amount = $loan_log_sum->sum('Panelty_Payment');

//        $savingBalanceSum=$Saving_amountSum-$Saving_balance;
        $last_log = DB::table('Loan_Log')->where('Loan_ID','=',$id)->orderBy('Loan_Log_ID', 'desc')->first();
        $savingBalanceSum=0.00;
        if ($last_log){
            $savingBalanceSum = $last_log->Saving_Account_Balance;
        }
        if ($savingBalanceSum==0){
            $savingBalanceSum = $installments->sum('Saving_balance');
        }

        // Extracting installment IDs from installments
        $installmentIds = $installments->pluck('idInstallments');

        // Fetch the installment logs based on the extracted IDs
        $installment_logs = tableWithBranch('installment_log')
            ->whereIn('Installments_idInstallments', $installmentIds)
            ->get();


        $customer_payments = tableWithBranch('customer_payments', 'customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->orderBy('Date', 'asc') // 'asc' for ascending, 'desc' for descending
            ->get();

        $total_paid_amount = $customer_payments->sum('Amount');

        // Fetch the customer
        $customers = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();

        // Fetch the user
        $User = DB::table('user')->where('id', $loan->User_idUser)->first();
        $Lending_Officer = DB::table('user')->where('id', $loan->lending_officer_id)->first();



        // Fetch the loan category
        $Loan_Category = DB::table('loan_category')->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->first();

        // Fetch the customer bank
        $Customer_Bank = tableWithBranch('customer_has_bank')->where([
            ['id', $loan->cus_bank_account],
            ['cus_id', $loan->Customer_idCustomer]
        ])->first();

        $Other_Charges = tableWithBranch('loan_other_charges')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();


        $documents = tableWithBranch('documents')->where([
            ['Customer_Loan_idCustomer_Loan', $id]
        ])->get();


        $customer_documents = tableWithBranch('customer_documents')->where([
            ['Customer_idCustomer', $loan->Customer_idCustomer]
        ])->get();




        // Fetch the witnesses
        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = [];

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null]
                ];
            }
        }
        $user_id = (int)session('userid');

        $payment_delete=DB::table('user')->where('id','=',$user_id)->first();
        $payment_delete_status=0;
        if ($payment_delete){
            $payment_delete_status=(int)$payment_delete->payment_delete;
        }

        $loanQuery = tableWithBranch('customer_loan', 'customer_loan')
            ->leftJoin(DB::raw('(
        SELECT loan_id, COUNT(*) as approval_count, 
               SUM(CASE WHEN date = "-" THEN 1 ELSE 0 END) as pending_approvals 
        FROM loan_has_approval 
        GROUP BY loan_id
    ) as approval_subquery'), 'customer_loan.idCustomer_Loan', '=', 'approval_subquery.loan_id')
            ->leftJoin(DB::raw('(
        SELECT Customer_Loan_idCustomer_Loan, SUM(Amount) as total_other_charges 
        FROM loan_other_charges 
        GROUP BY Customer_Loan_idCustomer_Loan
    ) as charges_subquery'), 'customer_loan.idCustomer_Loan', '=', 'charges_subquery.Customer_Loan_idCustomer_Loan')
            ->where('idCustomer_Loan','=',$id)
            ->select(
                'customer_loan.idCustomer_Loan',
                DB::raw('IFNULL(approval_subquery.approval_count, 0) as approval_count'),
                DB::raw('IFNULL(approval_subquery.pending_approvals, 0) as pending_approvals'),
                DB::raw('IFNULL(charges_subquery.total_other_charges, 0) as total_other_charges')
            );

        $loans = $loanQuery->first();

        if ($loans->pending_approvals == '0' && str_contains(url()->previous(), 'pendingloan')) {
            return redirect('/pendingloan');
        }

        $loan_saving_balance=tableWithBranch('Customer_Saving_Accounts')->where('Loan_Id','=',$id)->value('Balance');

        // Guard against legacy reshedule tables missing 'loan_id' column
        $exists = 0;
        try {
            if (Schema::hasTable('reshedule') && Schema::hasColumn('reshedule', 'loan_id')) {
                $exists = DB::table('reshedule')->where('loan_id', $loan->idCustomer_Loan)->exists() ? 1 : 0;
            }
        } catch (\Throwable $e) {
            $exists = 0; // Fallback silently to avoid breaking the view
        }

        // Fetch customer summary data (route, center, group, group members)
        $customerSummary = tableWithBranch('customer', 'customer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->where('customer.idCustomer', $loan->Customer_idCustomer)
            ->select(
                'customer.idCustomer',
                DB::raw('COALESCE(route.name, "-") as route_name'),
                DB::raw('COALESCE(route.root_code, "-") as route_code'),
                DB::raw('COALESCE(center.No, "-") as center_no'),
                DB::raw('COALESCE(center.Name, "-") as center_name'),
                DB::raw('COALESCE(customer_group.Group_No, "-") as group_no'),
                DB::raw('COALESCE(customer_group.Name, "-") as group_name'),
                'group_has_customer.group_id',
                'route.collection_type',
                'route.collection_date'
            )
            ->first();

        // Fetch other customers in the same group
        $groupMembers = collect();
        if ($customerSummary && $customerSummary->group_id) {
            $isHeadOffice = (int)session('branch_id') === -1;
            $branch_id = session('branch_id');

            if ($isHeadOffice) {
                $groupMembers = DB::table('customer as c')
                    ->join('group_has_customer as ghc', 'ghc.cus_id', '=', 'c.idCustomer')
                    ->where('ghc.group_id', $customerSummary->group_id)
                    ->where('c.idCustomer', '!=', $loan->Customer_idCustomer)
                    ->select('c.idCustomer', 'c.cus_number', 'c.First_Name', 'c.Last_Name', 'c.Nic', 'c.Contact_No')
                    ->orderBy('c.First_Name')
                    ->get();
            } else {
                $groupMembers = DB::table('customer as c')
                    ->join('group_has_customer as ghc', 'ghc.cus_id', '=', 'c.idCustomer')
                    ->where('c.branch_id', $branch_id)
                    ->where('ghc.group_id', $customerSummary->group_id)
                    ->where('c.idCustomer', '!=', $loan->Customer_idCustomer)
                    ->select('c.idCustomer', 'c.cus_number', 'c.First_Name', 'c.Last_Name', 'c.Nic', 'c.Contact_No')
                    ->orderBy('c.First_Name')
                    ->get();
            }
        }


        $latest = DB::table('extra_charger')
            ->where('loan_id', $loan->idCustomer_Loan)
            ->orderByDesc('id_extra_charger')
            ->first();
        $extraChargelatestBalance = 0;
        if ($latest && isset($latest->balance)) {
            $extraChargelatestBalance = (float) $latest->balance;
        }


        $loan_balance=DB::table('installments')
            ->where('installments.Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
            ->where('installments.Status', '=', '0')
            ->sum('installments.Total_Balance');


        $loan_balance=$loan_balance ?? 0;

        $loan_Total_Amount=DB::table('installments')
            ->where('installments.Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
            ->sum('installments.Total_Amount');


        $loan_Total_Amount=$loan_Total_Amount ?? 0;

        $ins_count=DB::table('installments')
            ->where('installments.Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
            ->count();

        // --- Extra charger aggregates for this loan ---
        $extraAgg = DB::table('extra_charger')
            ->where('loan_id', $loan->idCustomer_Loan)
            ->where('branch_id', session('branch_id')) // keep branch scope like the rest
            ->selectRaw('SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END)  AS total_extra_charges')
            ->selectRaw('SUM(CASE WHEN amount < 0 THEN -amount ELSE 0 END) AS total_extra_payments')
            ->first();

        $totalExtraCharges  = (float)($extraAgg->total_extra_charges  ?? 0);
        $totalExtraPayments = (float)($extraAgg->total_extra_payments ?? 0);

        $Collecting_Officer = DB::table('user')->where('id', $loan->collector_id)->first();

        // Pass the data to the view with compact and handle potential nulls
        return view('pages.LoanView', compact(
            'Collecting_Officer',
            'ins_count',
            'Total_Balance',
            'loan_balance',
            'loan_Total_Amount',
            'type',
            'exists',
            'id',
            'customers',
            'loan',
            'installments',
            'company',
            'witnessDetails',
            'User',
            'Lending_Officer',
            'Loan_Category',
            'Other_Charges',
            'documents',
            'customer_documents',
            'installment_logs',
            'customer_payments',
            'Panalty_BalanceSum',
            'Panalty_Amount',
            'total_paid_amount',
            'Customer_Bank',
            'savingBalanceSum',
            'Saving_amountSum',
            'payment_delete_status',
            'loan_saving_balance',
            'extraChargelatestBalance',
            'customerSummary',
            'groupMembers',
            'totalExtraCharges',
            'totalExtraPayments'
        ));
  }






    public function loan_view_ajax(string $id)
    {
        $loan = DB::table('customer_loan')->where('branch_id', session('branch_id'))->where('idCustomer_Loan', $id)->first();
        if ($loan) {
            $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();


            $nextInstallment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $id)
                ->where('Installment_Date', '>', Carbon::now()->toDateString())
                ->where('Status', '=', "1")
                ->orderBy('Installment_Date', 'desc') // Order by date descending
                ->first(); // Get the first record in this order

            if (!$nextInstallment){
                $nextInstallment = tableWithBranch('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $id)
                    ->where('Installment_Date', '<', Carbon::now()->toDateString())
                    ->orderBy('Installment_Date', 'desc') // Order by date descending
                    ->first(); // Get the first record in this order
            }

            $nextInstallmentDate = Carbon::parse($nextInstallment->Installment_Date);

            $startDate = Carbon::parse($nextInstallment->Installment_Date);
            $endDate = Carbon::parse(date('Y-m-d'));
            $Paid_Date = $nextInstallment->Paid_Date;

            if ($startDate < $Paid_Date) {
                $startDate = Carbon::parse($nextInstallment->Paid_Date);
            }

            $daysCount = $startDate->diffInDays($endDate);

            $capital = $nextInstallment->capital_amount;
            $interest = $nextInstallment->interest_amount;
            $paid_amount = $nextInstallment->Paid_Amount;


            return response()->json(['paid_amount'=>$paid_amount,'loan' => $loan, 'Paid_Date' => $Paid_Date, 'installments' => $installments, 'success' => true, 'next_installment_date' => $nextInstallmentDate->format('Y-m-d'), 'days_from_last_payment_date' => $daysCount, 'capital' => $capital, 'interest' => $interest]);
        }
        return response()->json(['success' => false]);
    }

    public function repaymentreport()
    {
        $dates = date('Y-m-d');
        $date_2 = date('Y-m-d');
        $collection = tableWithBranch('customer_payments','customer_payments')
            ->join('user', 'customer_payments.User_idUser', '=', 'user.id')
            ->whereBetween('customer_payments.Date', [$dates, $date_2])
            ->get();


        $paymentsGroupedByDate = [];

        foreach ($collection as $item) {
            if (!isset($paymentsGroupedByDate[$item->Date])) {
                $paymentsGroupedByDate[$item->Date] = [];
            }

            if (!isset($paymentsGroupedByDate[$item->Date][$item->User_idUser])) {
                $paymentsGroupedByDate[$item->Date][$item->User_idUser] = [
                    'confirm_user' => $item->confirm_user,
                    'user_id' => $item->User_idUser,
                    'username' => $item->Full_Name,
                    'date' => $item->Date,
                    'total_amount' => 0
                ];
            }

            $paymentsGroupedByDate[$item->Date][$item->User_idUser]['total_amount'] += $item->Amount;
        }

        $userPayments = [];
        foreach ($paymentsGroupedByDate as $date => $users) {
            foreach ($users as $user) {
                $userPayments[] = $user;
            }
        }

        return view('pages.CollectionReport', compact('userPayments', 'dates', 'date_2'));
    }


    public function loadbank(string $id)
    {

        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        $bank = tableWithBranch('customer_has_bank')
            ->where('cus_id', $loan->Customer_idCustomer)->get();
        return response()->json(['bank' => $bank,'bank_id' => $loan->cus_bank_account,'success' => true]);
    }


    public function reduce_capital(Request $request)
    {
        $id = $request->reduce_balance_loan_id;

        // Validate and process the data here
        // ...

        // Store data in session
        Session::put('reduce_capital_data', [
            'reduce_balance_loan_id' => $request->reduce_balance_loan_id,
            'pending_amount' => $request->pending_amount,
            'next_payment_date' => $request->next_payment_date,
            'days_from_last_payment_date' => $request->days_from_last_payment_date,
            'ins_capital' => $request->ins_capital,
            'installment_interest' => $request->installment_interest,
            'installment_interest_today' => $request->installment_interest_today,
            'required_payment_before_capital' => $request->required_payment_before_capital,
        ]);

        // Respond with JSON including the redirect URL
        return response()->json([
            'status' => 'success',
            'redirect_url' => route('loan.reduce_capital_view', ['id' => $id])
        ]);
    }

    public function reduce_capital_view($id)
    {
        $customers = tableWithBranch('customer')->get();
        $center = tableWithBranch('center')->get();
        $product = tableWithBranch('loan_category')->get();
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        $company= DB::table('company')->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Loan not found.');
        }

        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();

        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessDetails = [];

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idGuardian', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address')
                    ->where('idCustomer', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer
                ];
            }
        }

        $witnessCount = count($witnessDetails);
        $installments_log = tableWithBranch('installment_log','installment_log')
            ->join('installments', 'installment_log.Installments_idInstallments', '=', 'installments.idInstallments')
            ->where('installments.Customer_Loan_idCustomer_Loan', $id)
            ->get();

        // Retrieve data from session
        $reduce_capital_data = Session::get('reduce_capital_data');

        return view('pages.ReduceCapital', compact(
            'customers',
            'installments_log',
            'center',
            'product',
            'id',
            'loan',
            'installments',
            'witnessDetails',
            'witnessCount',
            'reduce_capital_data',
            'company'
        ));
    }

    public function invoice($id)
    {
        $company = DB::table('company')->first();
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $id)->first();
        $customers = tableWithBranch('customer')->where('idCustomer', $loan->Customer_idCustomer)->first();

        $loan_category = tableWithBranch('loan_category')->where('idLoan_Category', '=', $loan->Loan_Category_idLoan_Category)->first();

        $other_charges = tableWithBranch('other_charges')->where('Loan_Category_idLoan_Category', $loan_category->idLoan_Category)->get();
        $required_documents = tableWithBranch('required_documents')->where('Loan_Category_idLoan_Category', $loan_category->idLoan_Category)->get();
        $installments = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witness = tableWithBranch('witness')->where('Customer_Loan_idCustomer_Loan', $id)->get();
        $witnessData = [];

        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address', 'Address_02', 'Address_03', 'Gender', 'Dob')
                    ->where('idGuardian', $item->cus_id)->first();

                $guarantorAddress = array_filter([
                    $guarantor->Address ?? null,
                    $guarantor->Address_2 ?? null,
                    $guarantor->Address_3 ?? null
                ]);

                $witnessData[] = [
                    'type' => 'Guarantor',
                    'First_Name' => $guarantor->First_Name,
                    'Last_Name' => $guarantor->Last_Name,
                    'Contact_No' => $guarantor->Contact_No,
                    'Nic' => $guarantor->Nic,
                    'Gender' => $guarantor->Gender,
                    'Dob' => $guarantor->Dob,
                    'Address' => !empty($guarantorAddress) ? implode(', ', $guarantorAddress) : '-'
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address', 'Address_02', 'Address_03', 'Gender', 'Dob')
                    ->where('idCustomer', $item->cus_id)->first();

                $customerAddress = array_filter([
                    $customer->Address ?? null,
                    $customer->Address_02 ?? null,
                    $customer->Address_03 ?? null
                ]);

                $witnessData[] = [
                    'type' => 'Customer',
                    'First_Name' => $customer->First_Name,
                    'Last_Name' => $customer->Last_Name,
                    'Contact_No' => $customer->Contact_No,
                    'Nic' => $customer->Nic,
                    'Dob' => $customer->Dob,
                    'Gender' => $customer->Gender,
                    'Address' => !empty($customerAddress) ? implode(', ', $customerAddress) : '-'
                ];
            }
        }


        return view('pages.Issueinvoice', compact('id', 'company', 'required_documents', 'loan', 'customers', 'other_charges', 'installments', 'witnessData'));
    }

    public function getInstallments($loan_id)
    {
        // Fetch the installments where the loan ID matches
        $installments = DB::table('installments')->where('branch_id', session('branch_id'))->where('Customer_Loan_idCustomer_Loan', $loan_id)->get();
        $loan = DB::table('customer_loan')->where('branch_id', session('branch_id'))->where('idCustomer_Loan', $loan_id)->first();
        $company = DB::table('company')->first();

        $Interest_period=$loan->Collection_Type;
        $saturday_sunday=$company->saturday_sunday;
        $panelty_date_count=$loan->Panalty_Date;

        // Return the installments as JSON response
        return response()->json([
            'installment' => $installments,
            'interest' => $Interest_period,
            'panelty_date_count' => $panelty_date_count,
            'saturday_sunday' => $saturday_sunday
        ]);
    }

    public function updateInstallments(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'loan_id' => 'required',
            'installments' => 'required',
        ]);

        // Get loan details for description
        $loan = DB::table('customer_loan')
            ->where('idCustomer_Loan', $request->loan_id)
            ->where('branch_id', session('branch_id'))
            ->first();
        
        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }
        
        // Get customer name
        $customer = DB::table('customer')
            ->where('idCustomer', $loan->Customer_idCustomer)
            ->first();
        
        $customerName = $customer ? ($customer->First_Name . ' ' . $customer->Last_Name) : 'Unknown';
        
        // Get current installments for comparison
        $currentInstallments = [];
        foreach ($request->installments as $installment) {
            $current = DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $request->loan_id)
                ->where('No', $installment['no'])
                ->where('branch_id', session('branch_id'))
                ->first();
            
            if ($current) {
                $currentInstallments[] = [
                    'no' => $installment['no'],
                    'old_installment_date' => $current->Installment_Date,
                    'new_installment_date' => $installment['installment_date'],
                    'old_penalty_date' => $current->Panelty_date,
                    'new_penalty_date' => $installment['penalty_date'],
                ];
            }
        }
        
        // Store installment modification data for approval
        $requestData = [
            'loan_id' => $request->loan_id,
            'customer_id' => $loan->Customer_idCustomer,
            'installments' => $request->installments,
            'changes' => $currentInstallments,
        ];

        // Create approval request
        DB::table('approval_request')->insert([
            'type' => 'Loan Installment Modification',
            'typeid' => 403,
            'description' => 'Loan Installment Modification: Loan #' . $request->loan_id . ' (Customer: ' . $customerName . ', ' . count($request->installments) . ' installment(s))',
            'data' => json_encode($requestData),
            'userid' => session('userid'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);

        return response()->json(['message' => 'Installment modification request sent for approval!']);
        
        // OLD CODE - keeping for approval handler reference
        /*
        foreach ($request->installments as $installment) {
            DB::table('installments')->where('Customer_Loan_idCustomer_Loan', $request->loan_id)
                ->where('No', $installment['no'])
                ->where('branch_id', session('branch_id'))
                ->update([
                    'Installment_Date' => $installment['installment_date'],
                    'Panelty_date' => $installment['penalty_date'],
                ]);
        }
        return response()->json(['message' => 'Installments updated successfully!']);
        */
    }


    public function reschedule_index($loan_id, $balance)
    {
        $center = DB::table('center')->get();
        $product = DB::table('loan_category')->get();
        $company = DB::table('company')->first();
        $lending_officer = DB::table('user')->where('lending_officer', '=', '1')->get();
        $collector = DB::table('user')
            ->where('collector','=','1')
            ->get();
        $loan=DB::table('customer_loan')
            ->where('idCustomer_Loan','=',$loan_id)
            ->first();
        $product_id=$loan->Loan_Category_idLoan_Category;
        $loan_id=$loan->idCustomer_Loan;
        $customer = DB::table('customer')->where('idCustomer','=',$loan->Customer_idCustomer)->first();
        return view('pages.RescheduleIssueLoan', compact('customer','loan_id','product_id','loan', 'center', 'product', 'lending_officer','company','collector','loan_id','balance'));
    }


    public function save_reschedule(Request $request)
    {

        $this->ensureResheduleTable();

        $loan_id         = (int) $request->loan_id;
        $reschedule_type = (string) $request->reschedule_type;
        $user_id         = (int) session('userid');
        $today           = Carbon::now()->toDateString();

        // minimal validation of required inputs you actually use below
        $request->validate([
            'loan_id'            => 'required|integer',
            'installment'        => 'required|array|min:1',
            'collection_type'    => 'required|string',
            'interest_method'    => 'required|string',
            'installment_date_txt' => 'required|date',
        ]);

        // simple num sanitizer for strings like "27,000.00"
        $num = function($v) {
            if ($v === null) return null;
            return (float) str_replace([',',' '], '', (string)$v);
        };

        $savingFlag = ($request->saving ?? '') === 'Yes';

        $result = DB::transaction(function () use ($loan_id, $request, $today, $user_id, $num, $savingFlag) {

            // 1) Lock & fetch the current loan row
            $loanRow = DB::table('customer_loan')
                ->where('idCustomer_Loan', $loan_id)
                ->lockForUpdate()
                ->first();

            if (!$loanRow) {
                abort(404, 'Loan not found');
            }

            // 2) Copy current loan to Reschedule (keeps history)
            $data = (array) $loanRow;

// keep original data columns that match Reschedule, then add meta
            $data = Arr::only($data, (new Reschedule)->getFillable());

// add your new meta fields
            $data['loan_id']    = $loanRow->idCustomer_Loan;
            $data['created_by'] = $user_id;

// if your model has $timestamps=false, set created_at manually:
            $data['created_at'] = now();

// also keep who owns the current loan (if you want to stamp this too)
            $data['User_idUser'] = $user_id;

            Reschedule::create($data);


            // 3) Update fields on customer_loan (only those you are posting)
            $update = [
                'Date_Time'           => $today,
                'Amount'              => $num($request->loan_amount),
                'Interest_Rate'       => $num($request->interest),
                'Panalty_Rate'        => $num($request->panelty_amount),
                'Interest_Amount'     => $num($request->interest_amount),
                'Total_Other_Amount'  => $num($request->total_loan_charge),
                'Total_Loan_Amount'   => $num($request->total_loan_amount),
                'Installment_Amount'  => $num($request->new_interest_amount),
                'Collection_Type'     => $request->collection_type,
                'Collection_Date'     => $request->installment_date_txt,
                'Panalty_Date'        => $request->panelty_date,
                'Balance_Amount'      => $num($request->total_loan_amount),
                'User_idUser'         => $user_id,
                'capital_balance'     => $num($request->total_capital_amount),
                'installment_balance' => $num($request->total_interest_amount),
                'type'                => $request->interest_method,
                'Interest_period'     => $request->Interest_period,
                'saving_amount'       => $num($request->saving_amount) ?? 0.00,
                'branch_id'           => session('branch_id'),
            ];

            // Optional fields: only set if present in request
            foreach ([
                         'Loan_Category_idLoan_Category' => 'loan_cate_id',
                         'Customer_idCustomer'           => 'customer_id',
                         'Leasing_type'                  => 'lease_type',
                         'Vehicle_No'                    => 'vehicle_num',
                         'Other_Amount_Balance'          => 'loan_charge_balance',
                         'repayment_duration'            => 'repayment_duration_period',
                     ] as $col => $reqKey) {
                if ($request->filled($reqKey)) {
                    $update[$col] = $request->input($reqKey);
                }
            }

            // drop null/empty to avoid overwriting with blanks
            $update = array_filter($update, fn($v) => !is_null($v) && $v !== '');

            DB::table('customer_loan')
                ->where('idCustomer_Loan', $loan_id)
                ->update($update);

            // 4) Replace installments from first new date onward
            $items = $request->installment ?? [];
            $firstDate = $items[0]['installmentDate'] ?? null;

            if ($firstDate) {
                DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                    ->whereDate('Installment_Date', '>=', $firstDate)
                    ->delete();
            }

            // determine next No after the delete
            $lastNo = DB::table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                ->orderByRaw('CAST(`No` AS UNSIGNED) DESC')
                ->value('No');
            $nextNo = $lastNo ? ((int) $lastNo + 1) : 1;

            foreach ($items as $it) {
                $base = [
                    'Customer_Loan_idCustomer_Loan' => $loan_id,
                    'No'                 => $nextNo++,
                    'Installment_Date'   => $it['installmentDate'] ?? $today,
                    'Installment_Amount' => $num($it['installmentAmount'] ?? 0),
                    'capital_amount'     => $num($it['capitalAmount'] ?? 0),
                    'interest_amount'    => $num($it['interestAmount'] ?? 0),
                    'Panalty_Amount'     => $num($it['panaltyAmount'] ?? 0),
                    'Total_Amount'       => $num($it['totalAmount'] ?? 0),
                    'Paid_Amount'        => '0.00',
                    'Panalty_Balance'    => $num($it['panaltyBalance'] ?? 0),
                    'Interest_Balance'   => $num($it['interestAmount'] ?? 0),
                    'capital_balance'    => $num($it['capitalAmount'] ?? 0),
                    'Total_Balance'      => $num($it['totalBalance'] ?? 0),
                    'Status'             => '0',
                    'Panelty_date'       => $it['panaltyDate'] ?? $today,
                    'Panelty_status'     => '0',
                    'branch_id'          => session('branch_id'),
                ];

                if ($savingFlag) {
                    $base['Saving_amount']  = $num($it['savingAmount'] ?? 0);
                    $base['Saving_balance'] = $num($it['savingBalance'] ?? 0);
                }

                DB::table('installments')->insert($base);
            }

            return ['ok' => true];
        });

        return response()->json(['status' => 'success'] + $result, 200);
    }

    private function ensureResheduleTable(): void
    {
        // Create table if missing
        if (!Schema::hasTable('reshedule')) {
            Schema::create('reshedule', function (Blueprint $table) {
                $table->bigIncrements('idReschedule');
                $table->unsignedBigInteger('loan_id')->index();
                $table->string('Loan_No', 64)->nullable();
                $table->unsignedBigInteger('Loan_Category_idLoan_Category')->nullable();
                $table->unsignedBigInteger('Customer_idCustomer')->nullable();
                $table->string('Leasing_type', 50)->nullable();
                $table->string('Vehicle_No', 100)->nullable();
                $table->string('Date_Time')->nullable();
                $table->string('Amount', 15, 2)->nullable();
                $table->string('Interest_Rate', 10, 4)->nullable();
                $table->string('Panalty_Rate', 10, 4)->nullable();
                $table->string('Installment_Count')->nullable();
                $table->string('Interest_Amount', 15, 2)->nullable();
                $table->string('Total_Other_Amount', 15, 2)->nullable();
                $table->string('Other_Amount_Balance', 15, 2)->nullable();
                $table->string('Total_Loan_Amount', 15, 2)->nullable();
                $table->string('Installment_Amount', 15, 2)->nullable();
                $table->string('Collection_Type', 50)->nullable();
                $table->string('Collection_Date')->nullable();
                $table->string('Panalty_Date')->nullable();
                $table->string('Balance_Amount', 15, 2)->nullable();
                $table->tinyInteger('Status')->default(0);
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('User_idUser')->nullable();
                $table->decimal('capital_balance', 15, 2)->nullable();
                $table->decimal('installment_balance', 15, 2)->nullable();
                $table->string('type', 100)->nullable();
                $table->string('Interest_period', 50)->nullable();
                $table->unsignedBigInteger('cus_bank_account')->nullable();
                $table->unsignedBigInteger('company_bank_account')->nullable();
                $table->unsignedBigInteger('lending_officer_id')->nullable();
                $table->unsignedBigInteger('collector_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamp('created_at')->nullable();
            });
            return;
        }

        // Add the new columns if they’re missing (safe to call repeatedly) without relying on column order
        if (Schema::hasTable('reshedule')) {
            if (!Schema::hasColumn('reshedule', 'loan_id')) {
                try {
                    Schema::table('reshedule', function (Blueprint $t) {
                        $t->unsignedBigInteger('loan_id')->nullable()->index();
                    });
                } catch (\Throwable $e) {
                    // ignore if fails due to permissions or legacy engine
                }
            }
            if (!Schema::hasColumn('reshedule', 'created_by')) {
                try {
                    Schema::table('reshedule', function (Blueprint $t) {
                        $t->unsignedBigInteger('created_by')->nullable()->index();
                    });
                } catch (\Throwable $e) {
                    // ignore
                }
            }
            if (!Schema::hasColumn('reshedule', 'created_at')) {
                try {
                    Schema::table('reshedule', function (Blueprint $t) {
                        $t->timestamp('created_at')->nullable();
                    });
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }
    }


    public function getCustomerBankDetails(Request $request)
    {
        $customerIds = $request->input('customer_ids', []);
        $results = DB::table('customer_has_bank')
            ->join('customer','customer_has_bank.cus_id','=','customer.idCustomer')
            ->whereIn('customer.idCustomer', $customerIds)
            ->select('customer.idCustomer','customer_has_bank.bank_name','customer_has_bank.account_number')
            ->get();

        $data = [];
        foreach ($results as $row) {
            $data[$row->idCustomer] = $row->bank_name . ' - ' . $row->account_number;
        }

        return response()->json($data);
    }


}
