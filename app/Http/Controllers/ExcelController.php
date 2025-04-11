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
    public function __construct(CustomerLogController $customerLogController,LoanLogController $LoanLogController)
    {
        $this->customerLogController = $customerLogController;
        $this->LoanLogController = $LoanLogController;
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

        // Loop through each row of Excel data, starting from the 6th row (index 5)
        $skipped = [];
        foreach ($data as $key => $row) {
            if (DB::table('customer')
                ->where('cus_number', '=', $row[3])
                ->where('branch_id', '=', session('branch_id'))
                ->exists()) {
                $skipped[] = $row[3];  // Log skipped customer numbers
                continue;
            }



            // Instantiate a new Customer object
            $customer = new Customer();

            // Handle Center creation or fetching existing one
            $center_name = $row[1] ?? "Default";  // Assuming center_name is in the 3rd column
            $center = tableWithBranch('center')->where('Name', '=', $center_name)->first();
            if (!$center) {
                $centerData = [
                    'No' => '-',
                    'Name' => $center_name,
                    'Contact_no' => '-',
                    'Address' => '-',
                    'Route' => '-',
                    'Center_incharge' => 1,
                    'Location' => '-',
                    'Groups' => "0",
                    'Members' => "0",
                    'route_id' => 1,
                ];
                $center_id = insertWithBranch('center', $centerData);
            } else {
                $center_id = $center->idCenter;
            }

            // Handle Group creation or fetching existing one
            $group_name = $row[2] ?? "Default";  // Assuming group_name is in the same column
            $group = tableWithBranch('customer_group')->where('Group_No', '=', $group_name)->first();
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

            // Link customer to group
            insertWithBranch('group_has_customer', [
                'cus_id' => $customer->id,
                'group_id' => $group_id
            ]);
        }
        Log::info("Skipped Customers: ", $skipped);
        return response()->json(['message' => 'Data processed successfully.'], 200);
    }

    public function uploadExcelProduct(Request $request)
    {
        $data = $request->excelData;

        foreach ($data as $row) {

        }

        return response()->json(['message' => 'Excel products imported successfully.']);
    }



    public function uploadExcelLoan(Request $request){
        $data = $request->excelData;
        $user_id = (int)session('userid');
        $maxRows = 12; // Limit to first 30 rows
        // Loop through each row of Excel data, starting from the 6th row (index 5)
        foreach ($data as $key => $row) {
            if ($key >= $maxRows) {
                break; // Stop processing after 30 rows
            }
            $loan_no = $row[1];
            $product_name = $row[2];
            $member_no = $row[3];
            $issue_date = $row[4];
            $loan_amount = $row[5];
            $interest_rate = $row[6];
            $panelty_rate = $row[7];
            $installment_count = $row[8];
            $interest_amount = $row[9];
            $other_charge = $row[10];
            $tot_loan_amount = $row[11];
            $installment_amount = $row[12];
            $collection_type = $row[13];
            $panelty_start_day = $row[14];
            $maturity_date = $row[15];

            if ($collection_type=="WEEKLY"){
                $collection_type="Weekly";
            }else if ($collection_type=="MONTHLY"){
                $collection_type="Per Month";
            }else if ($collection_type=="B/WEEKLY"){
                $collection_type="Twice A Month";
            }else if ($collection_type=="Daily"){
                $collection_type="Daily";
            }

            $issue_date = str_replace("f", "", $issue_date);
            $interest_rate = str_replace("%", "", $interest_rate);

            $product=tableWithBranch('loan_category')->where('Name','=',$product_name)->first();
            $customer=tableWithBranch('customer')->where('cus_number','=',$member_no)->first();


            if ($customer){


                $Collection_Date = new DateTime($issue_date);  // Create a DateTime object

                if ($collection_type == "Weekly") {
                    // Add 7 days for WEEKLY collection type
                    $Collection_Date->modify('+7 days');
                } else if($collection_type=="Twice A Month"){
                    $Collection_Date->modify('+14 days');
                }else if($collection_type=="Daily"){
                    $Collection_Date->modify('+1 days');
                }else{
                    // Add 1 month for other collection types
                    $Collection_Date->modify('+1 month');
                }
                Log::info($collection_type);
// Format the updated date if needed
                $Collection_Date = $Collection_Date->format('Y-m-d');


                $loan = new Loan();
                $date = Carbon::now()->toDateString();

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
                $loan->Other_Amount_Balance = '-1';
                $loan->Total_Loan_Amount = $tot_loan_amount;
                $loan->Installment_Amount = $installment_amount;
                $loan->Collection_Type = 'Daily';
                $loan->Collection_Date = $Collection_Date;
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


                // Initialize starting variables for the loop
                $paidAmount = "0.00";  // Initial paid amount
                $status = '0'; // Default status for new installments
                $paneltyStatus = '0'; // Default penalty status for new installments

                // Loop to generate installments based on the installment count
                for ($i = 0; $i < $installment_count; $i++) {
                    if ($collection_type == "Weekly") {
                        // Calculate installment dates in weekly intervals
                        $installmentDate = date('Y-m-d', strtotime("+$i week", strtotime($Collection_Date)));
                    } else if ($collection_type == "Per Month") {
                        // Calculate installment dates in monthly intervals
                        $installmentDate = date('Y-m-d', strtotime("+$i month", strtotime($Collection_Date)));
                    } else if ($collection_type == "Daily") {
                        // Calculate installment dates in monthly intervals
                        $installmentDate = date('Y-m-d', strtotime("+$i day", strtotime($Collection_Date)));
                    }else{
                        $installmentDate = date('Y-m-d', strtotime("+$i month", strtotime($Collection_Date)));
                    }
                    Log::info($collection_type);
                    // Calculate the amounts and other details for each installment
                    $capitalAmount = $loan_amount / $installment_count; // Capital per installment
                    $interestForInstallment = $interest_amount / $installment_count; // Interest per installment
                    $totalInstallmentAmount = $installment_amount; // Total installment amount (capital + interest)
                    // Check if $panelty_start_day has a valid value
                    if (!is_numeric($panelty_start_day) || $panelty_start_day < 0) {
                        $panelty_start_day = 0; // Default value, adjust based on your requirement
                    }

// Calculate penalty date using a valid $panelty_start_day
                    $penaltyDate = date('Y-m-d', strtotime("+$panelty_start_day days", strtotime($installmentDate)));


                    // Save each installment to the database
                    DB::table('installments')->insert([
                        'Customer_Loan_idCustomer_Loan' => $id, // Assuming loan_no is the customer loan reference
                        'No' => $i + 1, // Installment number (1, 2, 3, ...)
                        'Installment_Date' => $installmentDate,
                        'Installment_Amount' => $totalInstallmentAmount,
                        'capital_amount' => $capitalAmount,
                        'interest_amount' => $interestForInstallment,
                        'Panalty_Amount' => 0, // Penalty amount (initially 0)
                        'Total_Amount' => $totalInstallmentAmount, // Total amount to be paid
                        'Paid_Amount' => $paidAmount, // Paid amount (initially 0)
                        'Panalty_Balance' => 0, // Penalty balance starts at 0
                        'Interest_Balance' => $interestForInstallment, // Remaining interest balance
                        'capital_balance' => $capitalAmount, // Remaining capital balance
                        'Total_Balance' => $totalInstallmentAmount, // Total balance
                        'Status' => $status, // Unpaid status
                        'Panelty_date' => $penaltyDate, // Penalty starts after certain days
                        'Panelty_status' => $paneltyStatus, // No penalty initially
                        'branch_id' => session('branch_id')
                    ]);
                }




                if ($other_charge>0){
                    // Prepare data for the loan other charges
                    $loanOtherChargesData = [
                        'Description' => "Other Charge Total",
                        'Type' => "Amount",
                        'Amount' => number_format($other_charge,2,'.',''),
                        'Customer_Loan_idCustomer_Loan' => $id,
                    ];

// Insert the loan other charges data with branch scoping
                    insertWithBranch('loan_other_charges', $loanOtherChargesData);


                    // Create a new Expenses instance
                    $expenses = new Expenses();

                    // Set the values for the Expenses instance
                    $expenses->type = "Income";
                    $expenses->reason = "Other loan charges for loan number: ({$loan_no}), Customer name: ({$customer->First_Name} {$customer->Last_Name})";
                    $expenses->date = date('Y-m-d');
                    $expenses->amount = number_format($other_charge,2,'.','');
                    $expenses->category_id = 1;
                    $expenses->bank_id = 1;
                    $expenses->branch_id = session('branch_id');

                    $expenses->save();



                }

                $request = new Request([
                    'customer_id' => $customer_id,
                    'description' => "Created new loan ({$loan_no})\nLoan Amount : {$loan_amount}\nProduct name : {$product_name}",
                    'description_id' => $id,
                    'comment' => ' ',
                    'type' => 'Create Loan',
                ]);

// Call the store method of CustomerLogController
                $this->customerLogController->store($request);


                $loanApprovalData = [
                    'loan_id' => $id,
                    'level' => "01",
                    'description' => "Approved",
                    'comment' => 'Approved',
                    'user_id' => $user_id,
                    'date' => $date,
                ];

// Insert the loan approval data with branch scoping
                insertWithBranch('loan_has_approval', $loanApprovalData);
                $this->LoanLogController->index(
                    $id,
                    'Issue Loan',
                    $id,
                    'Loan Issue',
                    $loan_amount,
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    $interest_amount,
                    $loan_amount,
                    $tot_loan_amount,
                    '0');
            }

        }
        return response()->json(['message' => 'Data processed successfully.'], 200);
    }

    public function uploadExcelPayment(Request $request){
        $data = $request->excelData;
        // Loop through each row of Excel data, starting from the 6th row (index 5)
        foreach ($data as $key => $row) {
            $loan_number=$row[0];

            $loan=tableWithBranch('customer_loan')->where('Loan_No','=',$loan_number)->first();

            $amount=$row[1];
            if ($amount>0){
                $data = [
                    'cus_id' => $loan->Customer_idCustomer,
                    'payment_amount' => $amount,
                    'file' => '-',
                    'loan_id' => $loan->idCustomer_Loan,
                    'payment_date' => date('Y-m-d'),
                    'payment_type' => 'Cash',
                    'bank_account_company' => '1',
                    'cheque_issue_bank' => '1',
                    'name_on_cheque' => '',
                    'chq_number' => '',
                    'chq_date' => '',
                    'chq_type' => 'Crossed',
                ];

                $request = new Request($data);

                $paymentController = app(TodayPaymentController::class);

                $paymentController->store($request);



            }
        }

        return response()->json(['message' => 'Payment processed successfully']);
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
