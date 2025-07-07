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
    public function __construct(CustomerLogController $customerLogController,LoanLogController $LoanLogController,BankLogController $bankLogController)
    {
        $this->customerLogController = $customerLogController;
        $this->LoanLogController = $LoanLogController;
        $this->bankLogController = $bankLogController;
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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




//         Loop through each row of Excel data, starting from the 6th row (index 5)
         $skipped = [];
         foreach ($data as $key => $row) {
             Log::info($row[3]);
             if (DB::table('customer')
                 ->where('cus_number', '=', $row[3])
                 ->where('branch_id', '=', session('branch_id'))
                 ->exists()) {
                 $skipped[] = $row[3];  // Log skipped customer numbers
                 continue;
             }



             // Instantiate a new Customer object
             $customer = new Customer();

             // Map fields from Excel to Customer object
             $customer->Title = $row[4] ?? '-';  // Assuming Title is in 5th column
             $customer->Customer_Group_idCustomer_Group = 1;  // Default group

             // Handle cus_number and format
             $customer->cus_number = $row[3] ?? '';
             // Assigning other customer details from Excel
             $customer->First_Name = $row[5] ?? '-';
             $customer->Last_Name = $row[6] ?? '-';
             $customer->Email = $row[7] ?? '-';
             $customer->Contact_No = $row[8] ?? '-';
             $customer->Nic = $row[10] ?? '-';
             $customer->Gender = $row[11] ?? '-';
             $customer->Dob = $row[12] ?? '-';

             // Address details
             $customer->Address = $row[13] ?? '-';
             $customer->Address_02 = $row[14] ?? '-';
             $customer->Address_03 = $row[15] ?? '-';
             $customer->Per_Address_01 = $row[16] ?? '-';
             $customer->Per_Address_02 = $row[17] ?? '-';
             $customer->Per_Address_03 = $row[18] ?? '-';
             $customer->City = $row[19] ?? '-';
             $customer->State = $row[20] ?? '-';
             $customer->Landline = $row[21] ?? '-';

             // Guardian information
             $customer->Gua_title = $row[22] ?? '-';
             $customer->Gua_name = $row[23] ?? '-';
             $customer->Guardian_gender = $row[24] ?? '-';
             $customer->Gua_relation = $row[25] ?? '-';
             $customer->Gua_occu = $row[26] ?? '-';
             $customer->Gua_contact = $row[27] ?? '-';
             $customer->Gua_address = $row[28] ?? '-';
             $customer->Gua_nic = $row[29] ?? '-';

             // Additional fields
             $customer->Customer_Risk_Level = "1";  // Default risk level
             $customer->civil_status = $row[30] ?? '-';

             // Assign branch_id
             $customer->branch_id = session('branch_id');

             // Save the customer data
             $customer->save();

             // If bank details exist, save them
             if (isset($row[37])) {
                 $documentData = [
                     'cus_id' => $customer->id,  // Customer ID
                     'bank_name' => $row[37],    // Bank name
                     'account_name' => $row[38], // Account name
                     'account_number' => $row[39], // Account number
                     'branch' => session('branch_id'), // Bank branch
                 ];
                 insertWithBranch('customer_has_bank', $documentData);
             }
         }
         Log::info("Skipped Customers: ", $skipped);


        DB::table('group_has_customer')
            ->where('branch_id', session('branch_id'))
            ->delete();

        $routeData = [
            'name' => 'Gampola',
            'root_code' => 'G001',
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
                $center_no = $row[43] ?? "Default";
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

        if (!isset($row[0], $row[1], $row[4])) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        $loan_number = $row[1];
        $excelDate = $row[0];
        if (is_numeric($excelDate)) {
            $date = date('Y-m-d', ($excelDate - 25569) * 86400);
        } else {
            $date = date('Y-m-d', strtotime($excelDate));
        }
        $amount = $row[4];
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




}
