<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use App\Models\LoanCategory;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DataMigrateController extends Controller
{

    protected $bankLogController;
    protected $LoanLogController;

    // Single constructor to inject both controllers
    public function __construct(BankLogController $bankLogController,LoanLogController $LoanLogController)
    {
        $this->bankLogController = $bankLogController;
        $this->LoanLogController = $LoanLogController;
    }
    /**
     * Display a listing of the resource.
     */
    public function create_loan(Store $session)
    {
        $session->put('branch_id',1);
        $minvence_loans = DB::connection('mysql_second')->table('customer_loan')->get();

        foreach ($minvence_loans as $item) {
            // Get repayment_duration from corresponding loan category
            $repayment_duration = DB::table('loan_category')
                ->where('idLoan_Category', $item->Loan_Category_idLoan_Category)
                ->value('Duration_period') ?? '-';
            $newLoanId=$item->idCustomer_Loan;
            Log::info($newLoanId);
            // Insert into new customer_loan table
            DB::table('customer_loan')->insert([
                'idCustomer_Loan' => $newLoanId,
                'Loan_No' => 'K/'.$item->Loan_No,
                'Loan_Category_idLoan_Category' => $item->Loan_Category_idLoan_Category,
                'Customer_idCustomer' => $item->Customer_idCustomer,
                'Leasing_type' => $item->Leasing_type,
                'Vehicle_No' => $item->Vehicle_No,
                'Date_Time' => $item->Date_Time,
                'Amount' => $item->Amount,
                'Interest_Rate' => $item->Interest_Rate,
                'Panalty_Rate' => $item->Panalty_Rate,
                'Installment_Count' => $item->Installment_Count,
                'Interest_Amount' => $item->Interest_Amount,
                'Total_Other_Amount' => $item->Total_Other_Amount,
                'Other_Amount_Balance' => $item->Other_Amount_Balance,
                'Total_Loan_Amount' => $item->Total_Loan_Amount,
                'Installment_Amount' => $item->Installment_Amount,
                'Collection_Type' => $item->Collection_Type,
                'Collection_Date' => $item->Collection_Date,
                'Panalty_Date' => $item->Panalty_Date,
                'Balance_Amount' => $item->Balance_Amount,
                'Status' => $item->Status,
                'reason' => $item->reason,
                'User_idUser' => '114',
                'capital_balance' => $item->capital_balance,
                'installment_balance' => $item->installment_balance,
                'type' => $item->type,
                'Interest_period' => $item->Interest_period,
                'cus_bank_account' => $item->cus_bank_account,
                'company_bank_account' => $item->company_bank_account,
                'lending_officer_id' => '114',
                'collector_id' => '114',
                'repayment_duration' => $repayment_duration,
                'loan_broker' => '1',
                'loan_broker_commission' => "0",
                'saving_amount' => "0.00",
                'branch_id' => session('branch_id'),
            ]);

            // Import related installments
            $installments = DB::connection('mysql_second')
                ->table('installments')
                ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                ->get();

            foreach ($installments as $ins) {
                DB::table('installments')->insert([
                    'Customer_Loan_idCustomer_Loan' => $newLoanId,
                    'No' => $ins->No,
                    'Installment_Date' => $ins->Installment_Date,
                    'Installment_Amount' => $ins->Installment_Amount,
                    'capital_amount' => $ins->capital_amount,
                    'interest_amount' => $ins->interest_amount,
                    'Panalty_Amount' => $ins->Panalty_Amount,
                    'Saving_amount' => 0,
                    'Total_Amount' => $ins->Total_Amount,
                    'Paid_Amount' => '0.00',
                    'Panalty_Balance' => $ins->Panalty_Amount,
                    'Interest_Balance' => $ins->interest_amount,
                    'capital_balance' => $ins->capital_amount,
                    'Saving_balance' => 0,
                    'Total_Balance' => $ins->Installment_Amount,
                    'Status' => '0',
                    'Panelty_date' => $ins->Panelty_date,
                    'Panelty_status' => $ins->Panelty_status,
                    'Paid_Date' => null,
                    'branch_id' => session('branch_id'),
                ]);
            }



            $panelty_balance=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$newLoanId)->sum('Panalty_Balance');

            // Call the store method of LoanLogController
            $this->LoanLogController->index(
                $newLoanId,
                'Issue Loan',
                $newLoanId,
                'Loan Issue',
                $item->Amount,
                '0',
                '0',
                '0',
                '0',
                $panelty_balance,
                $item->Interest_Amount,
                $item->capital_balance,
                $item->Balance_Amount+$panelty_balance,
                '0');


            $company_bank='33';

            $bank_log_comment="Loan Number : {$item->Loan_No}\nLoan Amount : {$item->Amount}\n";


            $bank_id=tableWithBranch('company_bank_accounts')
                ->where('Bank_Type','=','System_default_1')
                ->first();


            $this->bankLogController->index($company_bank,"Issue Loan",$bank_log_comment,"-","credit",$item->Amount,$bank_id->Idbank);




            $this->bankLogController->index($bank_id->Idbank,"Issue Loan",$bank_log_comment,"-","debit",$item->Amount,$company_bank);






            // Import related loan_other_charges
            $otherCharges = DB::connection('mysql_second')
                ->table('loan_other_charges')
                ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                ->get();

            foreach ($otherCharges as $charge) {
                DB::table('loan_other_charges')->insert([
                    'Description' => $charge->Description,
                    'Amount' => $charge->Amount,
                    'Type' => $charge->Type,
                    'Customer_Loan_idCustomer_Loan' => $newLoanId,
                    'branch_id' => session('branch_id'),
                ]);
            }

            $customer=tableWithBranch('customer')
                ->where('idCustomer','=',$item->Customer_idCustomer)
                ->first();
            $sumAmount = DB::table('loan_other_charges')
                ->where('Customer_Loan_idCustomer_Loan', '=', $newLoanId)
                ->where('branch_id', session('branch_id'))
                ->sum('Amount');



            // Check if the sumAmount is greater than zero
            if ($sumAmount > 0) {
                $bank_log_doc_comment="Loan Number : {$item->Loan_No}\nLoan Amount : {$item->Amount}\n";

                $bank_id=tableWithBranch('company_bank_accounts')
                    ->where('Bank_Type','=','System_default_9')
                    ->first();


                $this->bankLogController->index($company_bank,"Loan Document Chargers",$bank_log_doc_comment,"-","debit",$sumAmount,$bank_id->Idbank);

                $this->bankLogController->index($bank_id->Idbank,"Loan Document Chargers",$bank_log_doc_comment,"-","credit",$sumAmount,$company_bank);


                $user_id = '1';
                if ($customer){
                    $cate=tableWithBranch('income_category')
                        ->where('description','=','Other')
                        ->first();
                    if ($cate){

                        // Create a new Expenses instance
                        $expenses = new Expenses();

                        // Set the values for the Expenses instance
                        $expenses->type = "Income";
                        $expenses->reason = "Other loan charges for loan number: ({$item->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                        $expenses->date = date('Y-m-d');
                        $expenses->amount = $sumAmount;
                        $expenses->category_id = $cate->id;
                        $expenses->bank_id = 33;
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
                        $expenses->reason = "Other loan charges for loan number: ({$item->Loan_No}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                        $expenses->date = date('Y-m-d');
                        $expenses->amount = $sumAmount;
                        $expenses->category_id = $cate_id;
                        $expenses->bank_id = 33;
                        $expenses->user_id = $user_id;
                        $expenses->branch_id = session('branch_id');

                        $expenses->save();
                    }
                }




            }

            // Import related witnesses
            $witnesses = DB::connection('mysql_second')
                ->table('witness')
                ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                ->get();

            foreach ($witnesses as $wit) {
                DB::table('witness')->insert([
                    'Customer_Loan_idCustomer_Loan' => $newLoanId,
                    'cus_id' => $wit->cus_id,
                    'type' => $wit->type,
                    'branch_id' => session('branch_id'),
                ]);
            }

        }

        $customerLogs = DB::connection('mysql_second')->table('customer_log')->get();

        foreach ($customerLogs as $log) {
            DB::table('customer_log')->insert([
                'customer_id' => $log->customer_id,
                'customer_name' => $log->customer_name,
                'date' => $log->date,
                'time' => $log->time,
                'description' => $log->description,
                'description_id' => $log->description_id,
                'comment' => $log->comment,
                'type' => $log->type,
                'user' => $log->user,
                'points' => $log->points,
                'branch_id' => session('branch_id'), // or assign appropriately
            ]);
        }


        return response()->json(['message' => 'Loans and related data imported successfully.']);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create_product(Store $session)
    {
        $session->put('branch_id',1);
        $minvence_loans = DB::connection('mysql_second')->table('loan_category')->get();

        foreach ($minvence_loans as $minvence_loan) {
            // Step 1: Save loan category
            $loancategory = new LoanCategory();
            $loancategory->Name = $minvence_loan->Name;

            $prefix = strtoupper(substr($minvence_loan->Name, 0, 1));

// Get last product_code with same prefix
            $lastCode = LoanCategory::where('Product_code', 'like', $prefix . '%')
                ->orderByDesc('Product_code')
                ->value('Product_code');

            if ($lastCode) {
                // Extract numeric part and increment
                $lastNumber = (int)substr($lastCode, 1);
                $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '001';
            }

            $loancategory->idLoan_Category = $minvence_loan->idLoan_Category;
            $loancategory->Product_code = $prefix . $nextNumber;

            $loancategory->Loan_amount = $minvence_loan->Loan_amount;
            $loancategory->Loan_amount_to = $minvence_loan->Loan_amount;
            $loancategory->Interest_method = $minvence_loan->Interest_method;
            $loancategory->Interest_period = $minvence_loan->Interest_period;
            $loancategory->Loan_interest = $minvence_loan->Loan_interest;
            $loancategory->Loan_interest_to =  $minvence_loan->Loan_interest;
            $loancategory->Duration_period = $minvence_loan->Duration_period;
            $loancategory->Loan_period = $minvence_loan->Loan_period;
            $loancategory->Repayment_type = $minvence_loan->Repayment_type;
            $loancategory->Panelty_period = $minvence_loan->Panelty_period;
            $loancategory->Panelty_pecentage = $minvence_loan->Panelty_pecentage;
            $loancategory->Panelty_date = $minvence_loan->Panelty_date;
            $loancategory->Guarantee_count = $minvence_loan->Guarantee_count;
            $loancategory->Interest_Period_Count = $minvence_loan->Interest_Period_Count;

            // Optional fields
            $loancategory->enable_saving_process = "No";
            $loancategory->saving_amount_type = "pre_defined";
            $loancategory->saving_amount = "0";
            $loancategory->saving_payment = "0";
            $loancategory->default_loan_duration_period = $minvence_loan->Duration_period;
            $loancategory->branch_id = session('branch_id');

            $loancategory->save();
            $newCategoryId = $loancategory->getKey();

            // Step 2: Import other charges for this loan
            $oldCategoryId = $minvence_loan->idLoan_Category;

            $otherCharges = DB::connection('mysql_second')
                ->table('other_charges')
                ->where('Loan_Category_idLoan_Category', $oldCategoryId)
                ->get();

            foreach ($otherCharges as $charge) {
                $data = [
                    'Description' => $charge->Description,
                    'Amount' => $charge->Amount,
                    'charge_type' => $charge->charge_type,
                    'Loan_Category_idLoan_Category' => $newCategoryId,
                ];
                insertWithBranch('other_charges', $data);
            }

            // Step 3: Import required documents for this loan
            $requiredDocs = DB::connection('mysql_second')
                ->table('required_documents')
                ->where('Loan_Category_idLoan_Category', $oldCategoryId)
                ->get();

            foreach ($requiredDocs as $doc) {
                $data = [
                    'Name' => $doc->Name,
                    'Loan_Category_idLoan_Category' => $newCategoryId,
                ];
                insertWithBranch('required_documents', $data);
            }

            // Step 4: Import levels and their designations
            $levels = DB::connection('mysql_second')
                ->table('level')
                ->where('product_id', $oldCategoryId)
                ->get();

            foreach ($levels as $level) {
                $data = [
                    'product_id' => $newCategoryId,
                    'type' => $level->type,
                    'description' => $level->description ?: '-',
                ];

                // Insert and get new level ID
                $newLevelId = insertWithBranch('level', $data);

                // Fetch associated designations from old DB
                $designations = DB::connection('mysql_second')
                    ->table('level_has_designation')
                    ->where('level_id', $level->id)
                    ->get();

                foreach ($designations as $designation) {
                    $designationData = [
                        'level_id' => $newLevelId,
                        'designation_id' => $designation->designation_id,
                    ];
                    insertWithBranch('level_has_designation', $designationData);
                }
            }

        }

        return response()->json(['message' => 'Loan categories with charges and documents imported successfully.']);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function create_user(Store $session)
    {
        $session->put('branch_id',1);
        $minvence_users = DB::connection('mysql_second')->table('user')->get();

        foreach ($minvence_users as $min_user) {
            DB::table('user')->insert([
                'Full_Name' => $min_user->Full_Name,
                'password' => $min_user->password, // assuming already hashed
                'email' => $min_user->email,
                'TP' => $min_user->TP,
                'Designation' => $min_user->Designation,
                'Epf_no' => $min_user->Epf_no,
                'Nic' => $min_user->Nic,
                'Status' => $min_user->Status,

                // Permissions - all set to 1
                'lending_officer' => 1,
                'customer' => 1,
                'add_customer' => 1,
                'view_customer' => 1,
                'loan_center' => 1,
                'create_loan_center' => 1,
                'view_center' => 1,
                'create_group' => 1,
                'view_group' => 1,
                'assign_customer_to_group' => 1,
                'guarantee' => 1,
                'add_guarantee' => 1,
                'view_guarantee' => 1,
                'product' => 1,
                'add_product' => 1,
                'view_product' => 1,
                'issue_loan' => 1,
                'pending_loan' => 1,
                'current_loan' => 1,
                'loan_in_arrease' => 1,
                'payment' => 1,
                'add_re_payment' => 1,
                'daily_payment' => 1,
                'add_bulk_re_payment' => 1,
                'view_repayment' => 1,
                'pending_approval_repayment' => 1,
                'approval_repayment' => 1,
                'agent_collection' => 1,
                'loan_calculator' => 1,
                'calender' => 1,
                'expenses' => 1,
                'add_expenses' => 1,
                'view_expenses' => 1,
                'income' => 1,
                'add_income' => 1,
                'view_income' => 1,
                'user' => 1,
                'create_user' => 1,
                'user_privilage' => 1,
                'account' => 1,
                'bank_details' => 1,
                'chq_details' => 1,
                'report' => 1,
                'report_1' => 1,
                'report_2' => 1,
                'report_3' => 1,
                'report_4' => 1,
                'report_5' => 1,
                'report_6' => 1,
                'report_7' => 1,
                'report_8' => 1,
                'report_9' => 1,
                'report_10' => 1,
                'report_11' => 1,
                'report_12' => 1,
                'report_13' => 1,
                'report_14' => 1,
                'report_15' => 1,
                'report_16' => 1,
                'report_17' => 1,
                'collector' => 1,
                'branch_id' => session('branch_id')
            ]);
        }

        return response()->json(['message' => 'Users created successfully.']);
    }


    /**
     * Display the specified resource.
     */
    public function create_customer(Store $session)
    {

        $session->put('branch_id',1);

        $branch_id = session('branch_id');

        // 1. Import centers
        $minvence_centers = DB::connection('mysql_second')->table('center')->get();
        foreach ($minvence_centers as $center) {
            DB::table('center')->insert([
                'idCenter' => $center->idCenter,
                'No' => $center->No,
                'Name' => $center->Name,
                'Contact_no' => $center->Contact_no,
                'Address' => $center->Address,
                'Route' => $center->Route,
                'Center_incharge' => $center->Center_incharge,
                'Groups' => $center->Groups,
                'Members' => $center->Members,
                'Location' => '-',
                'route_id' => '3',
                'branch_id' => $branch_id,
            ]);
        }

        // 2. Import customer groups
        $minvence_groups = DB::connection('mysql_second')->table('customer_group')->get();
        foreach ($minvence_groups as $group) {
            DB::table('customer_group')->insert([
                'idCustomer_Group' => $group->idCustomer_Group,
                'Group_No' => $group->Group_No,
                'Name' => $group->Name,
                'Leader_name' => $group->Leader_name,
                'Contact_no' => $group->Contact_no,
                'center_id' => $group->center_id,
                'branch_id' => $branch_id,
            ]);
        }

        // 3. Import customers
        $minvence_customers = DB::connection('mysql_second')->table('customer')->get();
        foreach ($minvence_customers as $cus) {
            DB::table('customer')->insert([
                'idCustomer' => $cus->idCustomer,
                'Customer_Group_idCustomer_Group' => $cus->Customer_Group_idCustomer_Group,
                'cus_number' => $cus->cus_number,
                'Title' => $cus->Title,
                'First_Name' => $cus->First_Name,
                'Last_Name' => $cus->Last_Name,
                'Email' => $cus->Email,
                'Contact_No' => $cus->Contact_No,
                'contact_number_2' => $cus->contact_number_2,
                'Nic' => $cus->Nic,
                'Gender' => $cus->Gender,
                'Dob' => $cus->Dob,
                'Customer_Risk_Level' => $cus->Customer_Risk_Level,
                'Address' => $cus->Address,
                'Address_02' => $cus->Address_02,
                'Address_03' => $cus->Address_03,
                'Per_Address_01' => $cus->Per_Address_01,
                'Per_Address_02' => $cus->Per_Address_02,
                'Per_Address_03' => $cus->Per_Address_03,
                'City' => $cus->City,
                'State' => $cus->State,
                'Landline' => $cus->Landline,
                'Note' => $cus->Note,
                'Longitude' => $cus->Longitude,
                'Latitude' => $cus->Latitude,
                'Gua_title' => $cus->Gua_title,
                'Gua_name' => $cus->Gua_name,
                'Guardian_gender' => $cus->Guardian_gender,
                'Gua_relation' => $cus->Gua_relation,
                'Gua_occu' => $cus->Gua_occu,
                'Gua_contact' => $cus->Gua_contact,
                'Gua_address' => $cus->Gua_address,
                'Gua_nic' => $cus->Gua_nic,
                'Cus_phto' => $cus->Cus_phto,
                'Status' => $cus->Status,
                'civil_status' => $cus->civil_status,
                'occu_job_position' => $cus->occu_job_position,
                'occu_monthly_salary' => $cus->occu_monthly_salary,
                'occu_address_01' => $cus->occu_address_01,
                'occu_address_02' => $cus->occu_address_02,
                'occu_address_03' => $cus->occu_address_03,
                'occu_contact_no' => $cus->occu_contact_no,
                'occu_longitude' => $cus->occu_longitude,
                'occu_latitude' => $cus->occu_latitude,
                'points' => $cus->points,
                'route_id' => "3",
                'Comment' => "-",
                'business_registration' => "-",
                'branch_id' => $branch_id,
            ]);
        }

        // 4. Import group_has_customer
        $minvence_group_map = DB::connection('mysql_second')->table('group_has_customer')->get();
        foreach ($minvence_group_map as $row) {
            DB::table('group_has_customer')->insert([
                'cus_id' => $row->cus_id,
                'group_id' => $row->group_id,
                'branch_id' => $branch_id,
            ]);
        }

        return response()->json(['message' => 'Customer, groups, and centers imported successfully.']);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function create_payment(Store $session)
    {
        $session->put('branch_id', 1);

        // Step 1: Get all loans from new DB
        $loans = tableWithBranch('customer_loan')->get();

        foreach ($loans as $loan) {
            $cleanLoanNo = $loan->Loan_No;
            // Step 2: Find matching loan in old DB using Loan_No
            $oldLoan = DB::connection('mysql_second')->table('customer_loan')
                ->where('Loan_No', $cleanLoanNo)
                ->first();

            if (!$oldLoan) {
                Log::info('Old loan not found for Loan_No: ' . $loan->Loan_No);
                continue;
            }

            // Step 3: Get related payments from old DB
            $oldPayments = DB::connection('mysql_second')
                ->table('customer_payments')
                ->where('Customer_Loan_idCustomer_Loan', $oldLoan->idCustomer_Loan)
                ->get();

            foreach ($oldPayments as $payment) {
                $amount = $payment->Amount ?? 0;
                $saving_amount = 0;
                $date = $payment->Date ?? now()->format('Y-m-d');

                $paymentData = [
                    'cus_id' => $loan->Customer_idCustomer,
                    'payment_amount' => $amount,
                    'saving_amount' => $saving_amount,
                    'file' => $payment->Slip ?? '-',
                    'loan_id' => $loan->idCustomer_Loan,
                    'payment_date' => $date,
                    'payment_type' => $payment->Payment_type ?? 'Cash',
                    'bank_account_company' => '33',
                    'cheque_issue_bank' => '33',
                    'name_on_cheque' => '',
                    'chq_number' => '',
                    'chq_date' => '',
                    'chq_type' => 'Crossed',
                ];

                $paymentController = app(TodayPaymentController::class);
                $paymentController->store(new Request($paymentData));

                Log::info("Imported payment for Loan_No: {$loan->Loan_No}, Amount: {$amount}");
            }
        }

        return response()->json(['message' => 'Old payments imported into new loan system.']);
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
}
