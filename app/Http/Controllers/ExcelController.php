<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expenses;
use App\Models\Guardian;
use App\Models\Loan;
use App\Models\LoanCategory;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{


    protected $customerLogController;
    protected $LoanLogController;

    protected $bankLogController;
    protected $loanLogController;
    protected $capitalBalanceController;
    public function __construct(CustomerLogController $customerLogController,LoanLogController $LoanLogController,BankLogController $bankLogController,LoanLogController $loanLogController,CapitalBalanceController $capitalBalanceController)
    {
        $this->customerLogController = $customerLogController;
        $this->LoanLogController = $LoanLogController;
        $this->bankLogController = $bankLogController;
        $this->loanLogController = $loanLogController;
        $this->capitalBalanceController = $capitalBalanceController;
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {



    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $branchId = session('branch_id');

        $orphans = DB::table('customer as c')
            ->leftJoin('group_has_customer as ghc', function ($join) use ($branchId) {
                $join->on('ghc.cus_id', '=', 'c.idCustomer')
                    ->where('ghc.branch_id', '=', $branchId);
            })
            ->where('c.branch_id', '=', $branchId)
            ->whereNull('ghc.cus_id')
            ->select('c.idCustomer', 'c.cus_number', 'c.First_Name', 'c.Last_Name', 'c.branch_id')
            ->orderBy('c.cus_number')
            ->count();

        return response()->json($orphans);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $data = $request->excelData;

        $route_id = '';
        $insertedCusIds = [];  // 👉 To track already inserted cus_id

        foreach ($data as $key => $row) {
            $customer_name = $row[3];
            $customer_name = strtolower(preg_replace('/\s+/', '', $customer_name));

            $customer = tableWithBranch('customer')
                ->whereRaw("
                LOWER(
                    REPLACE(
                        REPLACE(
                            REPLACE(CONCAT(TRIM(`First_Name`), TRIM(`Last_Name`)), ' ', ''),
                            CHAR(160), ''
                        ),
                        '\t', ''
                    )
                ) = ?", [$customer_name])
                ->first();

            if (!$customer) {
                $customer = tableWithBranch('customer')
                    ->whereRaw("
                    LOWER(
                        REPLACE(
                            REPLACE(
                                REPLACE(TRIM(`First_Name`), ' ', ''),
                                CHAR(160), ''
                            ),
                            '\t', ''
                        )
                    ) = ?", [$customer_name])
                    ->first();

                if (!$customer) {
                    Log::warning("Customer not found after fallback: $customer_name");
                }
            }

            $member_no = $customer->cus_number;

            $customer = DB::table('customer')
                ->where('cus_number', '=', $member_no)
                ->where('branch_id', '=', session('branch_id'))
                ->first();

            if ($customer) {
                if (in_array($customer->idCustomer, $insertedCusIds)) {
                    continue;  // 👉 Skip if cus_id already processed
                }

                $center_name = $row[1] ?? "Default";
                $center_no = $row[1] ?? "Default";
                $center = tableWithBranch('center')->where('Name', '=', $center_name)->first();

                if (!$center) {
                    $centerData = [
                        'No' => $center_no,
                        'Name' => $center_name,
                        'Contact_no' => '-',
                        'Address' => '-',
                        'Route' => '-',
                        'Center_incharge' => 1,
                        'Location' => '-',
                        'Groups' => "0",
                        'Members' => "0",
                        'route_id' => $route_id,
                    ];
                    $center_id = insertWithBranch('center', $centerData);
                } else {
                    $center_id = $center->idCenter;
                }

                $group_name = $row[2] ?? "Default";
                $group = tableWithBranch('customer_group')
                    ->where('Group_No', '=', $group_name)
                    ->where('center_id', '=', $center_id)
                    ->first();

                if (!$group) {
                    $groupData = [
                        'Group_No' => $group_name,
                        'Name' => $group_name,
                        'Leader_name' => '-',
                        'Contact_no' => '-',
                        'center_id' => $center_id,
                    ];
                    $group_id = insertWithBranch('customer_group', $groupData);
                } else {
                    $group_id = $group->idCustomer_Group;
                }

                // Link customer to group only if not already linked
                insertWithBranch('group_has_customer', [
                    'cus_id' => $customer->idCustomer,
                    'group_id' => $group_id
                ]);

                $insertedCusIds[] = $customer->idCustomer;  // 👉 Track inserted cus_id
            }
        }
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

    public function uploadExcelCustomer(Request $request)
    {
        $data = $request->excelData;

        $skipped = [];
        $insertedCustomers = []; // 👈 New array to keep track of new inserts

        foreach ($data as $key => $row) {
            if (DB::table('customer')
                ->where('cus_number', '=', $row[3])
                ->where('branch_id', '=', session('branch_id'))
                ->exists()) {
                $skipped[] = $row[3];
                continue;
            }

            $customer = new Customer();
            $customer->Title = $row[4] ?? '-';
            $customer->Customer_Group_idCustomer_Group = 1;
            $customer->cus_number = $row[3] ?? '';
            $customer->First_Name = $row[5] ?? '-';
            $customer->Last_Name = $row[6] ?? '-';
            $customer->Email = $row[7] ?? '-';
            $customer->Contact_No = $row[8] ?? '-';
            $customer->Nic = $row[10] ?? '-';
            $customer->Gender = $row[11] ?? '-';
            $customer->Dob = $row[12] ?? '-';
            $customer->Address = $row[13] ?? '-';
            $customer->Address_02 = $row[14] ?? '-';
            $customer->Address_03 = $row[15] ?? '-';
            $customer->Per_Address_01 = $row[16] ?? '-';
            $customer->Per_Address_02 = $row[17] ?? '-';
            $customer->Per_Address_03 = $row[18] ?? '-';
            $customer->City = $row[19] ?? '-';
            $customer->State = $row[20] ?? '-';
            $customer->Landline = $row[21] ?? '-';
            $customer->Gua_title = $row[22] ?? '-';
            $customer->Gua_name = $row[23] ?? '-';
            $customer->Guardian_gender = $row[24] ?? '-';
            $customer->Gua_relation = $row[25] ?? '-';
            $customer->Gua_occu = $row[26] ?? '-';
            $customer->Gua_contact = $row[27] ?? '-';
            $customer->Gua_address = $row[28] ?? '-';
            $customer->Gua_nic = $row[29] ?? '-';
            $customer->Customer_Risk_Level = "1";
            $customer->civil_status = $row[30] ?? '-';
            $customer->branch_id = session('branch_id');
            $customer->save();

            $insertedCustomers[$row[3]] = $customer->idCustomer; // 👈 Remember this insert

            if (isset($row[37])) {
                $documentData = [
                    'cus_id' => $customer->idCustomer,
                    'bank_name' => $row[37],
                    'account_name' => $row[38],
                    'account_number' => $row[39],
                    'branch' => session('branch_id'),
                ];
                insertWithBranch('customer_has_bank', $documentData);
            }
        }
//
        Log::info("Skipped Customers: ", $skipped);

//        DB::table('group_has_customer')
//            ->where('branch_id', session('branch_id'))
//            ->delete();

        $routeData = [
            'name' => session('branch_name'),
            'root_code' => 'P001',
            'id_officer' => '1',
        ];
        $route_id = insertWithBranch('route', $routeData);

        foreach ($data as $key => $row) {
            $customer=DB::table('customer')
                ->where('cus_number', '=', $row[3])
                ->where('branch_id', '=', session('branch_id'))
                ->first();
            if ($customer){
                $center_name = $row[1] ?? "Default";  // Assuming center_name is in the 3rd column
                $center_no = $row[1] ?? "Default";
                $center = tableWithBranch('center')->where('Name', '=', $center_name)->first();
                if (!$center) {
                    $centerData = [
                        'No' => $center_no,
                        'Name' => $center_name,
                        'Contact_no' => '-',
                        'Address' => '-',
                        'Route' => '-',
                        'Center_incharge' => 1,
                        'Location' => '-',
                        'Groups' => "0",
                        'Members' => "0",
                        'route_id' => $route_id,
                    ];
                    $center_id = insertWithBranch('center', $centerData);
                } else {
                    $center_id = $center->idCenter;
                }

                // Handle Group creation or fetching existing one
                $group_name = $row[2] ?? "Default";  // Assuming group_name is in the same column
                $group = tableWithBranch('customer_group')->where('Group_No', '=', $group_name)->where('center_id', '=', $center_id)->first();
                if (!$group) {
                    $groupData = [
                        'Group_No' => $group_name,
                        'Name' => $group_name,
                        'Leader_name' => '-',
                        'Contact_no' => '-',
                        'center_id' => $center_id,
                    ];
                    $group_id = insertWithBranch('customer_group', $groupData);
                } else {
                    $group_id = $group->idCustomer_Group;
                }

                // Link customer to group
                insertWithBranch('group_has_customer', [
                    'cus_id' => $customer->idCustomer,
                    'group_id' => $group_id
                ]);
            }
        }




        return response()->json(['message' => 'Data processed successfully.'], 200);
    }

    public function uploadExcelProduct(Request $request)
    {
        $data = $request->input('excelData');
        $productController = app(\App\Http\Controllers\LoanCategoryController::class);

        foreach ($data as $item) {
            $storeRequest = new \Illuminate\Http\Request();
            $storeRequest->replace($item);

            $productController->store($storeRequest);
        }

        return response()->json(['message' => 'Excel products saved successfully.'], 200);
    }



    public function uploadExcelLoan(Request $request){
        $row = $request->row;
        $user_id = (int)session('userid');

        // Do basic validation
        if (!$row || count($row) < 16) {
            return response()->json(['error' => 'Invalid row data.'], 400);
        }

        if ($row[1]!=''){
            $loan_no = $row[1];

            $product_name = $row[2];
            $member_no = $row[3];

            $issue_date = $row[4];
            $first_ins_date = $row[15];
            $loan_amount = $row[5];
            $interest_rate = $row[6];
            $installment_count = $row[8];
            $interest_amount = $row[10];
            $other_charge = $row[11];
            $tot_loan_amount = $row[12];
            $installment_amount = $row[13];
            $collection_type = $row[14];

            if ($collection_type=="WEEKLY"){
                $collection_type="Weekly";
            }else if ($collection_type=="MONTHLY"){
                $collection_type="Per Month";
            }else if ($collection_type=="B/WEEKLY"){
                $collection_type="Twice A Month";
            }else if ($collection_type=="Daily"){
                $collection_type="Daily";
            }

            // Issue Date
            if (is_numeric($issue_date)) {
                // Excel serial date (e.g., 45124)
                $unix_date = ($issue_date - 25569) * 86400;
                $issue_date = gmdate("Y-m-d", $unix_date);
            } else {
                // Check if already in Y-m-d format or convert from m/d/Y etc.
                try {
                    $date = DateTime::createFromFormat('Y-m-d', $issue_date);
                    if ($date && $date->format('Y-m-d') === $issue_date) {
                        // Already correct format
                    } else {
                        // Try to parse alternative formats like m/d/Y
                        $date = DateTime::createFromFormat('n/j/Y', $issue_date);
                        if (!$date) {
                            $date = new DateTime($issue_date); // Fallback general parser
                        }
                        $issue_date = $date->format('Y-m-d');
                    }
                } catch (Exception $e) {
                    return response()->json(['error' => 'Invalid issue date: ' . $issue_date], 400);
                }
            }

// First Installment Date
            if (is_numeric($first_ins_date)) {
                $unix_date_2 = ($first_ins_date - 25569) * 86400;
                $first_ins_date = gmdate("Y-m-d", $unix_date_2);
            } else {
                try {
                    $date = DateTime::createFromFormat('Y-m-d', $first_ins_date);
                    if ($date && $date->format('Y-m-d') === $first_ins_date) {
                        // Already in correct format
                    } else {
                        $date = DateTime::createFromFormat('n/j/Y', $first_ins_date);
                        if (!$date) {
                            $date = new DateTime($first_ins_date); // Fallback general parser
                        }
                        $first_ins_date = $date->format('Y-m-d');
                    }
                } catch (Exception $e) {
                    return response()->json(['error' => 'Invalid first installment date: ' . $first_ins_date], 400);
                }
            }


            $interest_rate = str_replace("%", "", $interest_rate);

            $product=tableWithBranch('loan_category')->where('Name','=',$product_name)->first();
            if ($product){
                $panelty_rate = $product->Panelty_pecentage;
                $panelty_start_day = $product->Panelty_date;

                $customer=tableWithBranch('customer')->where('cus_number','=',$member_no)->first();
                if ($customer){


                    $Collection_Date = new DateTime($first_ins_date); // ✅ Keep as DateTime object
                    $formattedCollectionDate = $Collection_Date->format('Y-m-d'); // For DB use




                    $loan = new Loan();

                    $customer_id=$customer->idCustomer;


                    $loan->Loan_No = $loan_no;
                    $loan->Loan_Category_idLoan_Category = $product->idLoan_Category;
                    $loan->Customer_idCustomer = $customer_id;
                    $loan->Leasing_type = "Cash";
                    $loan->Vehicle_No = null;
                    $loan->Date_Time = $issue_date;
                    $loan->Amount = $loan_amount;
                    $loan->Interest_Rate = $interest_rate;
                    $loan->Panalty_Rate = $panelty_rate;
                    $loan->Installment_Count = $installment_count;
                    $loan->Interest_Amount = $interest_amount;
                    $loan->Total_Other_Amount = $other_charge;
                    $loan->Other_Amount_Balance = '0';
                    $loan->Total_Loan_Amount = $tot_loan_amount;
                    $loan->Installment_Amount = $installment_amount;
                    $loan->Collection_Type = 'Daily';
                    $loan->Collection_Date = $Collection_Date->format('Y-m-d'); // No time
                    $loan->Panalty_Date = $panelty_start_day;
                    $loan->Balance_Amount = $tot_loan_amount;
                    $loan->Status = "0";
                    $loan->User_idUser = $user_id;
                    $loan->capital_balance = $loan_amount;
                    $loan->installment_balance = $interest_amount;
                    $loan->type = "Flat Rate";
                    $loan->Interest_period = $collection_type;
                    $loan->lending_officer_id = $user_id;
                    $loan->collector_id = $user_id;
                    $loan->repayment_duration = 'Days';
                    $loan->cus_bank_account = null;
                    $loan->branch_id = session('branch_id');

                    $loan->save();

                    $id = $loan->id;

                    $company = tableWithBranch('company')->first();
                    $loan_format = $company->loan_format;

                    $enable_saving_process=$product->enable_saving_process;
                    if ($enable_saving_process=="Yes"){
                        $saving_number_txt=$customer_id;


                        // Prepare data for Customer_Saving_Accounts
                        $savingData = [
                            'Customer_Id' => $customer_id,
                            'Loan_Id' => $id,
                            'Loan_No' => $loan_no,
                            'Created_Date' => date('Y-m-d H:i:s'),
                            'Account_No' => $saving_number_txt,
                            'Account_Type' => "Saving",
                            'Balance' => "0.00",
                            'Status' => "1",
                        ];

// Insert and get the ID of the saving account
                        $saving = insertWithBranch('Customer_Saving_Accounts', $savingData);

// Prepare data for Savings_Account_Log
                        $logData = [
                            'Saving_Acount_Id' => $saving,
                            'Date_Time' => date('Y-m-d H:i:s'),
                            'Type' => "Saving Account",
                            'Description' => "Account Creation",
                            'Credit' => 0.00,
                            'Debit' => 0.00,
                            'Balance' => 0.00,
                            'User' => $user_id,
                        ];

// Insert log entry
                        insertWithBranch('Savings_Account_Log', $logData);
                    }

                    $saving_check=$product->enable_saving_process;
                    $saving_payment=$product->saving_payment;
                    $savingBalance=0.0;
                    if ($saving_check=="Yes"){
                        if ($saving_payment!="1"){
                            $savingBalance = $product->saving_amount;
                        }
                    }

                    // Initialize starting variables for the loop
                    $paidAmount = "0.00";  // Initial paid amount
                    $status = '0'; // Default status for new installments
                    $paneltyStatus = '0'; // Default penalty status for new installments
                    $installmentDate=$Collection_Date;
                    // Loop to generate installments based on the installment count
                    for ($i = 0; $i < $installment_count; $i++) {
                        // Calculate the amounts and other details for each installment
                        $capitalAmount = $loan_amount / $installment_count; // Capital per installment
                        $interestForInstallment = $interest_amount / $installment_count; // Interest per installment
                        $totalInstallmentAmount = $installment_amount+$savingBalance; // Total installment amount (capital + interest)


                        // Check if $panelty_start_day has a valid value
                        if (!is_numeric($panelty_start_day) || $panelty_start_day < 0) {
                            $panelty_start_day = 0; // Default value, adjust based on your requirement
                        }

                        $installmentDate = clone $Collection_Date;

                        if ($collection_type == "Weekly") {
                            $installmentDate->modify("+{$i} week");
                        } elseif ($collection_type == "Per Month") {
                            $installmentDate->modify("+{$i} month");
                        } elseif ($collection_type == "Daily") {
                            $installmentDate->modify("+{$i} day");
                        } elseif ($collection_type == "Twice A Month") {
                            $installmentDate->modify("+".($i * 14)." days");
                        } else {
                            $installmentDate->modify("+{$i} month");
                        }

                        $formattedInstallmentDate = $installmentDate->format('Y-m-d');
                        $penaltyDate = (clone $installmentDate)->modify("+{$panelty_start_day} days")->format('Y-m-d');




                        // Save each installment to the database
                        DB::table('installments')->insert([
                            'Customer_Loan_idCustomer_Loan' => $id, // Assuming loan_no is the customer loan reference
                            'No' => $i + 1, // Installment number (1, 2, 3, ...)
                            'Installment_Date' => $installmentDate->format('Y-m-d'),
                            'Installment_Amount' => $installment_amount,
                            'capital_amount' => $capitalAmount,
                            'interest_amount' => $interestForInstallment,
                            'Panalty_Amount' => 0, // Penalty amount (initially 0)
                            'Total_Amount' => $totalInstallmentAmount, // Total amount to be paid
                            'Saving_amount' => $savingBalance, // Total amount to be paid
                            'Paid_Amount' => $paidAmount, // Paid amount (initially 0)
                            'Panalty_Balance' => 0, // Penalty balance starts at 0
                            'Interest_Balance' => $interestForInstallment, // Remaining interest balance
                            'capital_balance' => $capitalAmount, // Remaining capital balance
                            'Total_Balance' => $totalInstallmentAmount, // Total balance
                            'Status' => $status, // Unpaid status
                            'Panelty_date' => $penaltyDate, // Penalty starts after certain days
                            'Panelty_status' => $paneltyStatus, // No penalty initially
                            'Saving_balance' => $savingBalance, // No penalty initially
                            'branch_id' => session('branch_id')
                        ]);





                    }


                    $company_bank=tableWithBranch('company_bank_accounts')->where('Account_No','=','Cash')->value('Idbank');

                    $customer_loan=tableWithBranch('customer_loan')
                        ->where('idCustomer_Loan','=',$id)
                        ->first();

                    $bank = tableWithBranch('company_bank_accounts')->where('Idbank','=',$company_bank)->first();

                    if (!is_null($bank)) {


                        DB::table('customer_loan')
                            ->where('idCustomer_Loan', $id)
                            ->where('branch_id', session('branch_id'))
                            ->update(
                                [
                                    'Status' => '0',
                                    'cus_bank_account' => $request->bank_acc,
                                    'company_bank_account' => $company_bank
                                ]);


                        $bank_log_comment="Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";


                        $bank_id=tableWithBranch('company_bank_accounts')
                            ->where('Bank_Type','=','System_default_1')
                            ->first();


                        $this->bankLogController->index($company_bank,"Issue Loan",$bank_log_comment,"-","credit",$customer_loan->Amount,$bank_id->Idbank);




                        $this->bankLogController->index($bank_id->Idbank,"Issue Loan",$bank_log_comment,"-","debit",$customer_loan->Amount,$company_bank);




                        $customer=tableWithBranch('customer')
                            ->where('idCustomer','=',$customer_loan->Customer_idCustomer)
                            ->first();
                        $sumAmount = DB::table('loan_other_charges')
                            ->where('Customer_Loan_idCustomer_Loan', '=', $id)
                            ->where('branch_id', session('branch_id'))
                            ->sum('Amount');



                        // Check if the sumAmount is greater than zero
                        if ($sumAmount > 0) {
                            $bank_log_doc_comment="Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";

                            $bank_id=tableWithBranch('company_bank_accounts')
                                ->where('Bank_Type','=','System_default_9')
                                ->first();


                            $this->bankLogController->index($company_bank,"Loan Document Chargers",$bank_log_doc_comment,"-","debit",$sumAmount,$bank_id->Idbank);

                            $this->bankLogController->index($bank_id->Idbank,"Loan Document Chargers",$bank_log_doc_comment,"-","credit",$sumAmount,$company_bank);

                            $cate=tableWithBranch('income_category')
                                ->where('description','=','Other')
                                ->first();
                            $user_id = (int)session('userid');
                            if ($cate){

                                // Create a new Expenses instance
                                $expenses = new Expenses();

                                // Set the values for the Expenses instance
                                $expenses->type = "Income";
                                $expenses->reason = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                                $expenses->date = date('Y-m-d');
                                $expenses->amount = $sumAmount;
                                $expenses->category_id = $cate->id;
                                $expenses->bank_id = 1;
                                $expenses->user_id = $user_id;
                                $expenses->branch_id = session('branch_id');

                                $expenses->save();
                            }else{
                                $cate_id=DB::table('income_category')->insertGetId([
                                    'description'=>"Other",
                                    'branch_id'=>session('branch_id')
                                ]);

                                // Create a new Expenses instance
                                $expenses = new Expenses();

                                // Set the values for the Expenses instance
                                $expenses->type = "Income";
                                $expenses->reason = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                                $expenses->date = date('Y-m-d');
                                $expenses->amount = $sumAmount;
                                $expenses->category_id = $cate_id;
                                $expenses->bank_id = 1;
                                $expenses->user_id = $user_id;
                                $expenses->branch_id = session('branch_id');

                                $expenses->save();
                            }




                        }


                        $request = new Request([
                            'customer_id' => $customer_loan->Customer_idCustomer,
                            'description' => "Approve Loan ({$customer_loan->Loan_No})\nLoan Amount : ({$customer_loan->Amount})",
                            'description_id' => $id,
                            'comment' => ' ',
                            'type' => 'Approve Loan',
                        ]);

                        // Call the store method of CustomerLogController
                        $this->customerLogController->store($request);


                        $panelty_balance=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$id)->sum('Panalty_Balance');

                        // Call the store method of LoanLogController
                        $this->LoanLogController->index(
                            $id,
                            'Issue Loan',
                            $id,
                            'Loan Issue',
                            $customer_loan->Amount,
                            '0',
                            '0',
                            '0',
                            '0',
                            $panelty_balance,
                            $customer_loan->Interest_Amount,
                            $customer_loan->capital_balance,
                            $customer_loan->Balance_Amount+$panelty_balance,
                            '0');
                    }
                    if ($collection_type == "Weekly") {
                        $Collection_Date->modify('+7 days');
                    } elseif ($collection_type == "Twice A Month") {
                        $Collection_Date->modify('+14 days');
                    } elseif ($collection_type == "Daily") {
                        $Collection_Date->modify('+1 days');
                    } else {
                        $Collection_Date->modify('+1 month');
                    }


                }else{
                    Log::info($member_no);
                }
            }else{
                Log::info($product_name.'-'.$loan_no);
            }





            return response()->json(['message' => 'Row processed.']);
        }
    }



    public function reverseUploadedLoan(Request $request)
    {

        $row = $request->row;

        // Do basic validation
        if (!$row || count($row) < 16) {
            return response()->json(['error' => 'Invalid row data.'], 400);
        }

        if ($row[1]!=''){
            $loan_no = $row[1];

            // Get loan entry
            $loan = tableWithBranch('customer_loan')->where('Loan_No', $loan_no)->first();

            if (!$loan) {
                return response()->json(['error' => 'Loan not found.'], 404);
            }

            $loan_id = $loan->idCustomer_Loan;
            $branch_id = session('branch_id');

            // 1. Delete installments
            tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan', $loan_id)->delete();

            // 2. Delete expenses with related reason
            Expenses::where('branch_id', $branch_id)
                ->where('reason', 'like', "%loan number: ({$loan->Loan_No})%")
                ->delete();

            // 3. Delete saving account and logs
            $savingAccount = tableWithBranch('Customer_Saving_Accounts')
                ->where('Loan_Id', $loan_id)->first();

            if ($savingAccount) {
                tableWithBranch('Savings_Account_Log')->where('Saving_Acount_Id', $savingAccount->id)->delete();
                tableWithBranch('Customer_Saving_Accounts')->where('id', $savingAccount->id)->delete();
            }

            // 4. Delete loan other charges
            tableWithBranch('loan_other_charges')->where('Customer_Loan_idCustomer_Loan', $loan_id)->delete();

            // 5. Delete customer log (optional)
            tableWithBranch('customer_log')
                ->where('description_id', $loan_id)
                ->where('type', 'Approve Loan')
                ->delete();

            // 6. Delete loan log (optional)
            tableWithBranch('Loan_Log')
                ->where('Loan_ID', $loan_id)
                ->delete();


            $loan_log_comment = "Loan Number : {$loan->Loan_No}\nLoan Amount : {$loan->Amount}\n";

// Delete related bank logs: Loan Document Charges
            tableWithBranch('company_bank_has_log')
                ->where('Description', $loan_log_comment)
                ->where('Type', 'Loan Document Chargers')
                ->delete();

// Delete related bank logs: Issue Loan
            tableWithBranch('company_bank_has_log')
                ->where('Description', $loan_log_comment)
                ->where('Type', 'Issue Loan')
                ->delete();

            $loan_amount = $loan->Amount;

            $bank_default_1 = tableWithBranch('company_bank_accounts')
                ->where('Bank_Type', 'System_default_1')
                ->first();

            if ($bank_default_1) {
                tableWithBranch('company_bank_accounts')
                    ->where('Idbank', $bank_default_1->Idbank)
                    ->update([
                        'Account_Balance' => DB::raw("Account_Balance - {$loan_amount}")
                    ]);
            }
            $sumAmount = DB::table('loan_other_charges')
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)
                ->where('branch_id', session('branch_id'))
                ->sum('Amount');
            $document_charge = $sumAmount;

            $bank_default_9 = tableWithBranch('company_bank_accounts')
                ->where('Bank_Type', 'System_default_9')
                ->first();

            if ($bank_default_9) {
                tableWithBranch('company_bank_accounts')
                    ->where('Idbank', $bank_default_9->Idbank)
                    ->update([
                        'Account_Balance' => DB::raw("Account_Balance - {$document_charge}")
                    ]);
            }



            // 7. Delete the loan itself
            tableWithBranch('customer_loan')->where('idCustomer_Loan', $loan_id)->delete();

        }

        return response()->json(['message' => 'Loan and related records reversed successfully.']);
    }





    public function uploadExcelPayment(Request $request)
    {
        $row = $request->input('row'); // Each row sent as 'row' from frontend

        if (!isset($row[0], $row[1], $row[3])) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        $loan_number = $row[1];
        $excelDate = $row[0];

        if (is_numeric($excelDate)) {
            // Excel serial number
            $date = gmdate('Y-m-d', ($excelDate - 25569) * 86400);
        } else {
            try {
                $parsedDate = DateTime::createFromFormat('Y-m-d', $excelDate);
                if ($parsedDate && $parsedDate->format('Y-m-d') === $excelDate) {
                    $date = $excelDate; // Already correct format
                } else {
                    // Try parsing m/d/Y or n/j/Y formats
                    $parsedDate = DateTime::createFromFormat('n/j/Y', $excelDate);
                    if (!$parsedDate) {
                        $parsedDate = new DateTime($excelDate); // Fallback general parsing
                    }
                    $date = $parsedDate->format('Y-m-d');
                }
            } catch (Exception $e) {
                $date = null; // or handle the error appropriately
                // Example:
                // return response()->json(['error' => 'Invalid date: ' . $excelDate], 400);
            }
        }

        $amount = $row[3];
        $saving_amount = '0';

        if ($amount <= 0) {
            return response()->json(['message' => 'Amount is zero or negative, skipped.']);
        }

        $loan = tableWithBranch('customer_loan')->where('Loan_No', $loan_number)->first();

        if (!$loan) {
            Log::info($loan_number);
        }else{
            $paymentData = [
                'cus_id' => $loan->Customer_idCustomer,
                'payment_amount' => $amount,
                'saving_amount' => $saving_amount,
                'file' => '-',
                'loan_id' => $loan->idCustomer_Loan,
                'payment_date' => $date,
                'payment_type' => 'Cash',
                'bank_account_company' => '1',
                'cheque_issue_bank' => '1',
                'name_on_cheque' => '',
                'chq_number' => '',
                'chq_date' => '',
                'chq_type' => 'Crossed',
            ];

            $paymentController = app(TodayPaymentController::class);
            $paymentController->store(new Request($paymentData));

            return response()->json(['message' => 'Payment stored for loan: ' . $loan_number]);
        }
        return response()->json(['message' => 'Not Saved: ' . $loan_number]);

    }

    function convertExcelDate($excelDate)
    {
        // Excel starts from 1900-01-01, but has a known bug so we offset by 1 more day
        $unixTimestamp = ($excelDate - 25569) * 86400;
        return date('Y-m-d', $unixTimestamp);
    }


    public function uploadExcelCate(Request $request){
        $data = $request->excelData;
        // Loop through each row of Excel data, starting from the 6th row (index 5)
        foreach ($data as $key => $row) {
            Log::info($row[0]);
        }
    }


    public function uploadExcelWitness(Request $request)
    {
        $data = $request->excelData;

        // Loop through each row of Excel data, starting from the 6th row (index 5)
        foreach ($data as $key => $row) {

            $guardian = new Guardian();
            $guardian->Title = $row[4];
            $guardian->First_Name = $row[5];
            $guardian->Last_Name = $row[6];
            $guardian->Email = "";
            $guardian->Contact_No = $row[8];
            $guardian->Nic = $row[10];
            $guardian->Gender = $row[11];
            $guardian->Dob = $row[12];
            $guardian->Address = $row[16];
            $guardian->Address_2 = $row[17];
            $guardian->Address_3 = $row[18];
            $guardian->City = $row[19];
            $guardian->State = "";
            $guardian->Landline = "";
            $guardian->Note = "";
            $guardian->Longitude = "";
            $guardian->Latitude = "";
            $guardian->branch_id = session('branch_id');
            $guardian->save();


        }

        return response()->json(['message' => 'Data processed successfully.'], 200);
    }


    public function undo(Request $request){
        $row = $request->input('row'); // Each row sent as 'row' from frontend

        if (!isset($row[0], $row[1], $row[4])) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        $loan_number = $row[1];
        $amount = $row[4];

        if ($amount <= 0) {
            return response()->json(['message' => 'Amount is zero or negative, skipped.']);
        }

        $loan = tableWithBranch('customer_loan')->where('Loan_No', $loan_number)->first();

        if (!$loan) {
            Log::info($loan_number);
        }else{

            $payment = tableWithBranch('customer_payments')
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan->idCustomer_Loan)
                ->orderByDesc('idCustomer_Payments')
                ->first();

            if ($payment) {
                $payment_id = $payment->idCustomer_Payments;

                $request = new Request([
                    'reason' => 'Mistake',
                ]);

                $paymentController = app(TodayPaymentController::class);
                $paymentController->undoPayment($request, $payment_id); // <-- fix here

                return response()->json(['message' => 'Payment stored for loan: ' . $loan_number]);
            }


        }
        return response()->json(['message' => 'Not Saved: ' . $loan_number]);
    }


    public function balance_change(Request $request) {
        $row = $request->input('row'); // Expecting one row as array

        if (!isset($row[12], $row[13], $row[14])) {
            Log::info('Invalid data');
            return false;
        }

        $excel_loan_amount =(float) $row[5] ?? 0;
        $excel_interest_amount =(float) $row[8] ?? 0;
        $excel_balance_amount =(float) $row[12] ?? 0;
        $excel_panelty_amount =(float) $row[13] ?? 0;
        $excel_Loan_No = $row[14] ?? 0;
        $payment_amount=($excel_loan_amount+$excel_interest_amount)-($excel_balance_amount-$excel_panelty_amount);


        if ($excel_panelty_amount > 0) {
            $excel_balance_amount = round($excel_balance_amount - $excel_panelty_amount, 2);
        }

        $loan = tableWithBranch('customer_loan')
            ->where('Loan_No', $excel_Loan_No)
            ->first();


        if (!$loan) {
            Log::info('Loan not found for: ' . $excel_Loan_No);
            return false;
        }
        $balance_amount = tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan->idCustomer_Loan)->sum('Total_Balance');
        $new_balance = round($excel_balance_amount - $balance_amount, 2);


        $bank_account_company = DB::table('company_bank_accounts')
            ->where('branch_id', session('branch_id'))
            ->whereRaw('LOWER(Account_No) = ?', ['cash'])  // Case-insensitive comparison
            ->value('Idbank');

        $paymentData = [
            'cus_id' => $loan->Customer_idCustomer,
            'payment_amount' => $payment_amount,
            'saving_amount' => '0.00',
            'file' => '-',
            'loan_id' => $loan->idCustomer_Loan,
            'payment_date' => date('Y-m-d'),
            'payment_type' => 'Cash',
            'bank_account_company' => $bank_account_company,
            'cheque_issue_bank' => '1',
            'name_on_cheque' => '',
            'chq_number' => '',
            'chq_date' => '',
            'chq_type' => 'Crossed',
        ];

        $paymentController = app(TodayPaymentController::class);
        $paymentController->store(new Request($paymentData));


        if ($excel_panelty_amount > 0){

            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                ->where('Status','=', '0')
                ->orderByDesc('idInstallments')
                ->first();

            if ($installment){
                tableWithBranch('installments')
                    ->where('idInstallments', $installment->idInstallments)
                    ->update([
                        'Panalty_Amount' => $excel_panelty_amount,
                        'Panalty_Balance' => $excel_panelty_amount,
                        'Total_Balance' => DB::raw("Total_Balance + $excel_panelty_amount")
                    ]);

                $last_loan_log=tableWithBranch('Loan_Log')
                    ->where('Loan_ID', $loan->idCustomer_Loan)
                    ->orderByDesc('Loan_Log_ID')
                    ->first();

                // Call the store method of LoanLogController
                $this->LoanLogController->index(
                    $loan->idCustomer_Loan,
                    'Penalty',
                    $loan->idCustomer_Loan,
                    'Penalty-Installment No :'.$installment->idInstallments,
                    $excel_panelty_amount,
                    '0',
                    '0',
                    '0',
                    '0',
                    $last_loan_log->Panelty_Balance+$excel_panelty_amount,
                    $last_loan_log->Interest_Balance,
                    $last_loan_log->Capital_Balance,
                    $last_loan_log->Total_Pending_Balance+$excel_panelty_amount,
                    '0');

                $bankLogController = new BankLogController();

                $System_default_5=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_5')
                    ->first();
                $System_default_6=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_6')
                    ->first();
                $bankLogController->index($System_default_5->Idbank,"Penalty","Penalty","-","debit",$excel_panelty_amount,$System_default_6->Idbank);
                $bankLogController->index($System_default_6->Idbank,"Penalty","Penalty","-","credit",$excel_panelty_amount,$System_default_5->Idbank);
            }
        }

        return response()->json(['message' => 'Row processed successfully']);
    }


    public function uploadExcelLoanGreenLanka(Request $request){
        $row = $request->row;
        $user_id = (int)session('userid');


//        $branch = $row[0];
//        if (trim($branch) !== 'Gampola') {
//            return false;  // Stop further processing for this row
//        }
        $customer_type='';
        if ($row[0]!=''){
            $loan_no = $row[14];

            $product_name = $row[4];


            $product=tableWithBranch('loan_category')->where('Name','=',$product_name)->first();
            if ($product_name=='1000'){
                $product=tableWithBranch('loan_category')
                    ->where('Loan_amount','=',$row[5])
                    ->where('Name','=',$product_name)->first();
            }else if($product_name=='2580'){
                $product=tableWithBranch('loan_category')
                    ->where('Loan_amount','=',$row[5])
                    ->where('Name','=',$product_name)->first();
            }

            if ($product){
                $customer_name=$row[3];
                $customer_name_excel=$row[3];
                $customer_name = strtolower(preg_replace('/\s+/', '', $customer_name));

                // Match customer by removing all spaces in DB fields and lowercasing
                $customer = tableWithBranch('customer')
                    ->whereRaw("
            LOWER(
                REPLACE(
                    REPLACE(
                        REPLACE(CONCAT(TRIM(`First_Name`), TRIM(`Last_Name`)), ' ', ''),
                        CHAR(160), ''
                    ),
                    '\t', ''
                )
            ) = ?", [$customer_name])
                    ->first();

                // Fallback: try First_Name alone (after removing all spaces and lowercasing)
                if (!$customer) {
                    $customer = tableWithBranch('customer')
                        ->whereRaw("
                LOWER(
                    REPLACE(
                        REPLACE(
                            REPLACE(TRIM(`First_Name`), ' ', ''),
                            CHAR(160), ''
                        ),
                        '\t', ''
                    )
                ) = ?", [$customer_name])
                        ->first();

                    if (!$customer) {
                        Log::warning("Customer not found after fallback: $customer_name");
                        $customer=tableWithBranch('customer')->where('First_Name','=','Default')->first();
                        $customer_type='Default';
                    }
                }


                $member_no = $customer->cus_number;

                $excelDate = $row[7];

// Convert Excel numeric date to string date
                if (is_numeric($excelDate)) {
                    $unixDate = ($excelDate - 25569) * 86400;
                    $issue_date = gmdate("Y-m-d", $unixDate);
                } else {
                    $issue_date = $excelDate; // Already a valid date string
                }

// Create DateTime object from $issue_date
                $issue_date_obj = new DateTime($issue_date);

// Add 7 days for first installment date
                $issue_date_obj->modify('+7 days');
                $first_ins_date = $issue_date_obj->format('Y-m-d');


                $loan_amount = (float) $row[5];
                $interest_rate = $row[6];
                $installment_count = $product->Loan_period;
                $interest_amount = (float) $row[8];
                $other_charge = '0.00';
                $tot_loan_amount = $loan_amount+$interest_amount;
                $installment_amount = $tot_loan_amount/$installment_count;
                $collection_type = 'Weekly';

                // Issue Date
                if (is_numeric($issue_date)) {
                    $unix_date = ($issue_date - 25569) * 86400;
                    $issue_date = gmdate("Y-m-d", $unix_date);
                } else {
                    try {
                        $issue_date = (new DateTime($issue_date))->format('Y-m-d');
                    } catch (Exception $e) {
                        return response()->json(['error' => 'Invalid issue date: ' . $issue_date], 400);
                    }
                }

// First Installment Date
                if (is_numeric($first_ins_date)) {
                    $unix_date_2 = ($first_ins_date - 25569) * 86400;
                    $first_ins_date = gmdate("Y-m-d", $unix_date_2);
                } else {
                    try {
                        $first_ins_date = (new DateTime($first_ins_date))->format('Y-m-d');
                    } catch (Exception $e) {
                        return response()->json(['error' => 'Invalid first installment date: ' . $first_ins_date], 400);
                    }
                }

                $interest_rate = str_replace("%", "", $interest_rate);

                $product=tableWithBranch('loan_category')->where('Name','=',$product_name)->first();
                if ($product){
                    $panelty_rate = $product->Panelty_pecentage;
                    $panelty_start_day = $product->Panelty_date;

                    $customer=tableWithBranch('customer')->where('cus_number','=',$member_no)->first();
                    if ($customer){


                        $Collection_Date = new DateTime($first_ins_date); // ✅ Keep as DateTime object
                        $formattedCollectionDate = $Collection_Date->format('Y-m-d'); // For DB use




                        $loan = new Loan();

                        $customer_id=$customer->idCustomer;


                        $loan->Loan_No = $loan_no;
                        $loan->Loan_Category_idLoan_Category = $product->idLoan_Category;
                        $loan->Customer_idCustomer = $customer_id;
                        $loan->Leasing_type = "Cash";
                        $loan->Vehicle_No = null;
                        $loan->Date_Time = $issue_date;
                        $loan->Amount = $loan_amount;
                        $loan->Interest_Rate = $interest_rate;
                        $loan->Panalty_Rate = $panelty_rate;
                        $loan->Installment_Count = $installment_count;
                        $loan->Interest_Amount = $interest_amount;
                        $loan->Total_Other_Amount = $other_charge;
                        $loan->Other_Amount_Balance = '0';
                        $loan->Total_Loan_Amount = $tot_loan_amount;
                        $loan->Installment_Amount = $installment_amount;
                        $loan->Collection_Type = 'Daily';
                        $loan->Collection_Date = $Collection_Date->format('Y-m-d'); // No time
                        $loan->Panalty_Date = $panelty_start_day;
                        $loan->Balance_Amount = $tot_loan_amount;
                        $loan->Status = "0";
                        $loan->User_idUser = $user_id;
                        $loan->capital_balance = $loan_amount;
                        $loan->installment_balance = $interest_amount;
                        $loan->type = "Flat Rate";
                        $loan->Interest_period = $collection_type;
                        $loan->lending_officer_id = $user_id;
                        $loan->collector_id = $user_id;
                        $loan->repayment_duration = 'Days';
                        $loan->cus_bank_account = null;
                        $loan->branch_id = session('branch_id');

                        $loan->save();

                        $id = $loan->id;

                        $company = tableWithBranch('company')->first();
                        $loan_format = $company->loan_format;

                        $enable_saving_process=$product->enable_saving_process;
                        if ($enable_saving_process=="Yes"){
                            $saving_number_txt=$customer_id;


                            // Prepare data for Customer_Saving_Accounts
                            $savingData = [
                                'Customer_Id' => $customer_id,
                                'Loan_Id' => $id,
                                'Loan_No' => $loan_no,
                                'Created_Date' => date('Y-m-d H:i:s'),
                                'Account_No' => $saving_number_txt,
                                'Account_Type' => "Saving",
                                'Balance' => "0.00",
                                'Status' => "1",
                            ];

// Insert and get the ID of the saving account
                            $saving = insertWithBranch('Customer_Saving_Accounts', $savingData);

// Prepare data for Savings_Account_Log
                            $logData = [
                                'Saving_Acount_Id' => $saving,
                                'Date_Time' => date('Y-m-d H:i:s'),
                                'Type' => "Saving Account",
                                'Description' => "Account Creation",
                                'Credit' => 0.00,
                                'Debit' => 0.00,
                                'Balance' => 0.00,
                                'User' => $user_id,
                            ];

// Insert log entry
                            insertWithBranch('Savings_Account_Log', $logData);
                        }

                        $saving_check=$product->enable_saving_process;
                        $saving_payment=$product->saving_payment;
                        $savingBalance=0.0;
                        if ($saving_check=="Yes"){
                            if ($saving_payment!="1"){
                                $savingBalance = $product->saving_amount;
                            }
                        }

                        // Initialize starting variables for the loop
                        $paidAmount = "0.00";  // Initial paid amount
                        $status = '0'; // Default status for new installments
                        $paneltyStatus = '0'; // Default penalty status for new installments
                        $installmentDate=$Collection_Date;
                        // Loop to generate installments based on the installment count
                        for ($i = 0; $i < $installment_count; $i++) {
                            // Calculate the amounts and other details for each installment
                            $capitalAmount = $loan_amount / $installment_count; // Capital per installment
                            $interestForInstallment = $interest_amount / $installment_count; // Interest per installment
                            $totalInstallmentAmount = $installment_amount+$savingBalance; // Total installment amount (capital + interest)


                            // Check if $panelty_start_day has a valid value
                            if (!is_numeric($panelty_start_day) || $panelty_start_day < 0) {
                                $panelty_start_day = 0; // Default value, adjust based on your requirement
                            }

                            $installmentDate = clone $Collection_Date;

                            if ($collection_type == "Weekly") {
                                $installmentDate->modify("+{$i} week");
                            } elseif ($collection_type == "Per Month") {
                                $installmentDate->modify("+{$i} month");
                            } elseif ($collection_type == "Daily") {
                                $installmentDate->modify("+{$i} day");
                            } elseif ($collection_type == "Twice A Month") {
                                $installmentDate->modify("+".($i * 14)." days");
                            } else {
                                $installmentDate->modify("+{$i} month");
                            }

                            $formattedInstallmentDate = $installmentDate->format('Y-m-d');
                            $penaltyDate = (clone $installmentDate)->modify("+{$panelty_start_day} days")->format('Y-m-d');




                            // Save each installment to the database
                            DB::table('installments')->insert([
                                'Customer_Loan_idCustomer_Loan' => $id, // Assuming loan_no is the customer loan reference
                                'No' => $i + 1, // Installment number (1, 2, 3, ...)
                                'Installment_Date' => $installmentDate->format('Y-m-d'),
                                'Installment_Amount' => $installment_amount,
                                'capital_amount' => $capitalAmount,
                                'interest_amount' => $interestForInstallment,
                                'Panalty_Amount' => 0, // Penalty amount (initially 0)
                                'Total_Amount' => $totalInstallmentAmount, // Total amount to be paid
                                'Saving_amount' => $savingBalance, // Total amount to be paid
                                'Paid_Amount' => $paidAmount, // Paid amount (initially 0)
                                'Panalty_Balance' => 0, // Penalty balance starts at 0
                                'Interest_Balance' => $interestForInstallment, // Remaining interest balance
                                'capital_balance' => $capitalAmount, // Remaining capital balance
                                'Total_Balance' => $totalInstallmentAmount, // Total balance
                                'Status' => $status, // Unpaid status
                                'Panelty_date' => $penaltyDate, // Penalty starts after certain days
                                'Panelty_status' => $paneltyStatus, // No penalty initially
                                'Saving_balance' => $savingBalance, // No penalty initially
                                'branch_id' => session('branch_id')
                            ]);





                        }


                        $company_bank=tableWithBranch('company_bank_accounts')->where('Account_No','=','Cash')->value('Idbank');

                        $customer_loan=tableWithBranch('customer_loan')
                            ->where('idCustomer_Loan','=',$id)
                            ->first();

                        $bank = tableWithBranch('company_bank_accounts')->where('Idbank','=',$company_bank)->first();

                        if (!is_null($bank)) {


                            DB::table('customer_loan')
                                ->where('idCustomer_Loan', $id)
                                ->where('branch_id', session('branch_id'))
                                ->update(
                                    [
                                        'Status' => '0',
                                        'cus_bank_account' => $request->bank_acc,
                                        'company_bank_account' => $company_bank
                                    ]);


                            $bank_log_comment="Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";


                            $bank_id=tableWithBranch('company_bank_accounts')
                                ->where('Bank_Type','=','System_default_1')
                                ->first();


                            $this->bankLogController->index($company_bank,"Issue Loan",$bank_log_comment,"-","credit",$customer_loan->Amount,$bank_id->Idbank);




                            $this->bankLogController->index($bank_id->Idbank,"Issue Loan",$bank_log_comment,"-","debit",$customer_loan->Amount,$company_bank);




                            $customer=tableWithBranch('customer')
                                ->where('idCustomer','=',$customer_loan->Customer_idCustomer)
                                ->first();
                            $sumAmount = DB::table('loan_other_charges')
                                ->where('Customer_Loan_idCustomer_Loan', '=', $id)
                                ->where('branch_id', session('branch_id'))
                                ->sum('Amount');



                            // Check if the sumAmount is greater than zero
                            if ($sumAmount > 0) {
                                $bank_log_doc_comment="Loan Number : {$customer_loan->Loan_No}\nLoan Amount : {$customer_loan->Amount}\n";

                                $bank_id=tableWithBranch('company_bank_accounts')
                                    ->where('Bank_Type','=','System_default_9')
                                    ->first();


                                $this->bankLogController->index($company_bank,"Loan Document Chargers",$bank_log_doc_comment,"-","debit",$sumAmount,$bank_id->Idbank);

                                $this->bankLogController->index($bank_id->Idbank,"Loan Document Chargers",$bank_log_doc_comment,"-","credit",$sumAmount,$company_bank);

                                $cate=tableWithBranch('income_category')
                                    ->where('description','=','Other')
                                    ->first();
                                $user_id = (int)session('userid');
                                if ($cate){

                                    // Create a new Expenses instance
                                    $expenses = new Expenses();

                                    // Set the values for the Expenses instance
                                    $expenses->type = "Income";
                                    $expenses->reason = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                                    $expenses->date = date('Y-m-d');
                                    $expenses->amount = $sumAmount;
                                    $expenses->category_id = $cate->id;
                                    $expenses->bank_id = 1;
                                    $expenses->user_id = $user_id;
                                    $expenses->branch_id = session('branch_id');

                                    $expenses->save();
                                }else{
                                    $cate_id=DB::table('income_category')->insertGetId([
                                        'description'=>"Other",
                                        'branch_id'=>session('branch_id')
                                    ]);

                                    // Create a new Expenses instance
                                    $expenses = new Expenses();

                                    // Set the values for the Expenses instance
                                    $expenses->type = "Income";
                                    $expenses->reason = "Other loan charges for loan number: ({$customer_loan->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                                    $expenses->date = date('Y-m-d');
                                    $expenses->amount = $sumAmount;
                                    $expenses->category_id = $cate_id;
                                    $expenses->bank_id = 1;
                                    $expenses->user_id = $user_id;
                                    $expenses->branch_id = session('branch_id');

                                    $expenses->save();
                                }




                            }

                            $request = new Request([
                                'customer_id' => $customer_loan->Customer_idCustomer,
                                'description' => "Loan Issue By Excel ({$customer_loan->Loan_No})\nLoan Amount : ({$customer_loan->Amount})",
                                'description_id' => $id,
                                'comment' => ' ',
                                'type' => 'Approve Loan',
                            ]);
                            if ($customer_type=='Default'){
                                $request = new Request([
                                    'customer_id' => $customer_loan->Customer_idCustomer,
                                    'description' => "Loan Issue By Excel {$customer_loan->Loan_No})\n Customer Name : ({$customer_name_excel})",
                                    'description_id' => $id,
                                    'comment' => ' ',
                                    'type' => 'Approve Loan',
                                ]);
                            }


                            // Call the store method of CustomerLogController
                            $this->customerLogController->store($request);


                            $panelty_balance=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$id)->sum('Panalty_Balance');

                            // Call the store method of LoanLogController
                            $this->LoanLogController->index(
                                $id,
                                'Issue Loan',
                                $id,
                                'Loan Issue',
                                $customer_loan->Amount,
                                '0',
                                '0',
                                '0',
                                '0',
                                $panelty_balance,
                                $customer_loan->Interest_Amount,
                                $customer_loan->capital_balance,
                                $customer_loan->Balance_Amount+$panelty_balance,
                                '0');
                        }
                        if ($collection_type == "Weekly") {
                            $Collection_Date->modify('+7 days');
                        } elseif ($collection_type == "Twice A Month") {
                            $Collection_Date->modify('+14 days');
                        } elseif ($collection_type == "Daily") {
                            $Collection_Date->modify('+1 days');
                        } else {
                            $Collection_Date->modify('+1 month');
                        }


                    }else{
                        Log::info($member_no);
                    }
                }else{
                    Log::info($product_name.'-'.$loan_no);
                }





                return response()->json(['message' => 'Row processed.']);
            }else{
                Log::info($row[3]);
            }
        }
    }



    public function storeCustomer(Request $request)
    {
        $row = $request->input('row'); // Expecting one row as array

        try {
            DB::beginTransaction();

            if (!empty($row[0])) {
                $loan_no = $row[14];

                $customer = tableWithBranch('customer')->where('cus_number', 'Default')->first();

                if ($customer) {
                    $cus_id = $customer->idCustomer;

                    $loans = tableWithBranch('customer_loan')
                        ->where('Customer_idCustomer', $cus_id)
                        ->get();

                    foreach ($loans as $item) {
                        if ($loan_no == $item->Loan_No) {
                            $loan_id = $item->idCustomer_Loan;

                            $firstRoute = tableWithBranch('route')->orderBy('id_route')->first();

                            if (!$firstRoute) {
                                return response()->json(['message' => 'No route found'], 404);
                            }

                            $customer_id = DB::table('customer')->insertGetId([
                                'Customer_Group_idCustomer_Group' => 1,
                                'cus_number' => '-',
                                'Title' => '-',
                                'First_Name' => $row[3], // FIXED HERE
                                'Last_Name' => '-',
                                'Email' => '-',
                                'Contact_No' => '-',
                                'contact_number_2' => '-',
                                'Nic' => '-',
                                'Gender' => '-',
                                'Dob' => null, // should be NULL, not '-'
                                'Customer_Risk_Level' => '-',
                                'Address' => '-',
                                'Address_02' => '-',
                                'Address_03' => '-',
                                'Per_Address_01' => '-',
                                'Per_Address_02' => '-',
                                'Per_Address_03' => '-',
                                'City' => '-',
                                'State' => '-',
                                'Landline' => '-',
                                'Note' => '-',
                                'Longitude' => '-',
                                'Latitude' => '-',
                                'Gua_title' => '-',
                                'Gua_name' => '-',
                                'Guardian_gender' => '-',
                                'Gua_relation' => '-',
                                'Gua_occu' => '-',
                                'Gua_contact' => '-',
                                'Gua_address' => '-',
                                'Gua_nic' => '-',
                                'Cus_phto' => '-',
                                'Status' => '-',
                                'civil_status' => '-',
                                'occu_job_position' => '-',
                                'occu_monthly_salary' => '-',
                                'occu_address_01' => '-',
                                'occu_address_02' => '-',
                                'occu_address_03' => '-',
                                'occu_contact_no' => '-',
                                'occu_longitude' => '-',
                                'occu_latitude' => '-',
                                'points' => 0,
                                'route_id' => $firstRoute->id_route,
                                'Comment' => '-',
                                'business_registration' => '-',
                                'branch_id' => session('branch_id'),
                            ]);

                            tableWithBranch('customer_loan')
                                ->where('idCustomer_Loan', $loan_id)
                                ->update(['Customer_idCustomer' => $customer_id]);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Customer created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function uploadExcelCenters(Request $request)
    {
        $data = $request->excelData;

        foreach ($data as $key => $row) {

            $branch=$row[0];
            if ($branch==='Walimada'){
                $branch_id=8;
            }else if ($branch==='Gampola'){
                $branch_id=3;
            }else if ($branch==='Haputhale'){
                $branch_id=4;
            }else if ($branch==='Nuwaraeliya'){
                $branch_id=5;
            }else if ($branch==='Ragala'){
                $branch_id=6;
            }else if ($branch==='Rikillagaskada'){
                $branch_id=7;
            }


            if (DB::table('customer')
                ->where('cus_number', '=', $row[3])
                ->where('branch_id', '=', $branch_id)
                ->exists()) {
                continue;
            }




            $customer_name = $row[3];
            $customer_name = strtolower(preg_replace('/\s+/', '', $customer_name));

            $customer = DB::table('customer')
                ->whereRaw("
                LOWER(
                    REPLACE(
                        REPLACE(
                            REPLACE(CONCAT(TRIM(`First_Name`), TRIM(`Last_Name`)), ' ', ''),
                            CHAR(160), ''
                        ),
                        '\t', ''
                    )
                ) = ?", [$customer_name])->where('branch_id','=',$branch_id)
                ->first();

            if (!$customer) {
                $customer = DB::table('customer')
                    ->whereRaw("
                    LOWER(
                        REPLACE(
                            REPLACE(
                                REPLACE(TRIM(`First_Name`), ' ', ''),
                                CHAR(160), ''
                            ),
                            '\t', ''
                        )
                    ) = ?", [$customer_name])->where('branch_id','=',$branch_id)
                    ->first();

                if (!$customer) {
                    Log::warning("Customer not found after fallback: $customer_name");
                    $customer = DB::table('customer')->where('branch_id','=',$branch_id)->where('First_Name', '=', 'Default')->first();
                }
            }

            $member_no = $customer->cus_number;

            $customer = DB::table('customer')
                ->where('cus_number', '=', $member_no)
                ->where('branch_id', '=', $branch_id)
                ->first();

            if ($customer) {

                $center_name = $row[1] ?? "Default";
                $center_no = $row[1] ?? "Default";
                $center = DB::table('center')->where('Name', '=', $center_name)->where('branch_id','=',$branch_id)->first();
                $route_id=DB::table('route')->where('branch_id','=',$branch_id)->first();
                if (!$center) {
                    $centerData = [
                        'No' => $center_no,
                        'Name' => $center_name,
                        'Contact_no' => '-',
                        'Address' => '-',
                        'Route' => '-',
                        'Center_incharge' => 1,
                        'Location' => '-',
                        'Groups' => "0",
                        'Members' => "0",
                        'route_id' => $route_id,
                    ];
                    $center_id = insertWithBranch('center', $centerData);
                } else {
                    $center_id = $center->idCenter;
                }

                $group_name = $row[2] ?? "Default";
                $group = tableWithBranch('customer_group')
                    ->where('Group_No', '=', $group_name)
                    ->where('center_id', '=', $center_id)
                    ->first();

                if (!$group) {
                    $groupData = [
                        'Group_No' => $group_name,
                        'Name' => $group_name,
                        'Leader_name' => '-',
                        'Contact_no' => '-',
                        'center_id' => $center_id,
                    ];
                    $group_id = insertWithBranch('customer_group', $groupData);
                } else {
                    $group_id = $group->idCustomer_Group;
                }

                // Link customer to group only if not already linked
                insertWithBranch('group_has_customer', [
                    'cus_id' => $customer->idCustomer,
                    'group_id' => $group_id
                ]);
            }
        }
        return response()->json(['message' => 'Data processed successfully.'], 200);
    }



}
