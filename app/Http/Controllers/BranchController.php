<?php

namespace App\Http\Controllers;

use App\Mail\BranchCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = DB::table('branch')->get();
        return view('pages.Branches', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $request->validate([
            'branch' => 'required|string|max:255',
        ]);

        // Insert into the database
        $id = DB::table('branch')->insertGetId([
            'Name' => $request->branch,
            'status' => 0, // Default to Active
        ]);
        $branch = DB::table('branch')->where('branch_id', $id)->first();
        // Array of bank accounts to be inserted
        $Bank = [
            [
                'Bank_Type' => "Bank",
                'code' => "Cash",
                'Bank_Name' => "Cash",
                'Account_Name' => "Cash",
                'Account_No' => "Cash",
                'Bank_Branch' => "-",
                'Account_Balance' => '0.00',
                'type' => "Cash and Bank",
                'cashflow' => "Non Applicable",
                'acc_type_group' => "Assets",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_1",
                'code' => "30003",
                'Bank_Name' => "Loans",
                'Account_Name' => "Loans",
                'Account_No' => "Loans",
                'Bank_Branch' => "Loans",
                'Account_Balance' => '0.00',
                'type' => "Loans",
                'cashflow' => "Financing activities",
                'acc_type_group' => "Assets",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_2",
                'code' => "30004",
                'Bank_Name' => "Interest Receivable",
                'Account_Name' => "Interest Receivable",
                'Account_No' => "Interest Receivable",
                'Bank_Branch' => "Interest Receivable",
                'Account_Balance' => '0.00',
                'type' => "Current Asset",
                'cashflow' => "Financing activities",
                'acc_type_group' => "Assets",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_3",
                'code' => "1001",
                'Bank_Name' => "Interest Income",
                'Account_Name' => "Interest Income",
                'Account_No' => "Interest Income",
                'Bank_Branch' => "Interest Income",
                'Account_Balance' => '0.00',
                'type' => "Non-operating Revenue",
                'cashflow' => "Non-operating activities",
                'acc_type_group' => "Revenue",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_4",
                'code' => "40015",
                'Bank_Name' => "Interest Suspense AC",
                'Account_Name' => "Interest Suspense AC",
                'Account_No' => "Interest Suspense AC",
                'Bank_Branch' => "Interest Suspense AC",
                'Account_Balance' => '0.00',
                'type' => "Liability",
                'cashflow' => "Non Applicable",
                'acc_type_group' => "Liabilities",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_5",
                'code' => "30005",
                'Bank_Name' => "Penalty Receivable",
                'Account_Name' => "Penalty Receivable",
                'Account_No' => "Penalty Receivable",
                'Bank_Branch' => "Penalty Receivable",
                'Account_Balance' => '0.00',
                'type' => "Liability",
                'cashflow' => "Non Applicable",
                'acc_type_group' => "Liabilities",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_6",
                'code' => "1006",
                'Bank_Name' => "Penalty Income",
                'Account_Name' => "Penalty Income",
                'Account_No' => "Penalty Income",
                'Bank_Branch' => "Penalty Income",
                'Account_Balance' => '0.00',
                'type' => "Revenue from Loan",
                'cashflow' => "Operating activities",
                'acc_type_group' => "Revenue",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_7",
                'code' => "2021",
                'Bank_Name' => "Loan Loss Expenses",
                'Account_Name' => "Loan Loss Expenses",
                'Account_No' => "Loan Loss Expenses",
                'Bank_Branch' => "Loan Loss Expenses",
                'Account_Balance' => '0.00',
                'type' => "Financial Expenses",
                'cashflow' => "Operating activities",
                'acc_type_group' => "Expenses",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_8",
                'code' => "40010",
                'Bank_Name' => "Loan Loss Provision",
                'Account_Name' => "Loan Loss Provision",
                'Account_No' => "Loan Loss Provision",
                'Bank_Branch' => "Loan Loss Provision",
                'Account_Balance' => '0.00',
                'type' => "Financial Expenses",
                'cashflow' => "Operating activities",
                'acc_type_group' => "Expenses",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_9",
                'code' => "6020",
                'Bank_Name' => "Loan Processing Fees",
                'Account_Name' => "Loan Processing Fees",
                'Account_No' => "Loan Processing Fees",
                'Bank_Branch' => "Loan Processing Fees",
                'Account_Balance' => '0.00',
                'type' => "Revenue from Loan",
                'cashflow' => "Operating activities",
                'acc_type_group' => "Revenue",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_10",
                'code' => "1225",
                'Bank_Name' => "Customer Savings",
                'Account_Name' => "Customer Savings",
                'Account_No' => "Customer Savings",
                'Bank_Branch' => "Customer Savings",
                'Account_Balance' => '0.00',
                'type' => "Borrower Saving Deposit",
                'cashflow' => "Financing activities",
                'acc_type_group' => "Liabilities",
                'User' => '1',
                'branch_id' => $id,
            ],
            [
                'Bank_Type' => "System_default_12",
                'code' => "1111",
                'Bank_Name' => "Installment Part Payment",
                'Account_Name' => "Installment Part Payment",
                'Account_No' => "Installment Part Payment",
                'Bank_Branch' => "Installment Part Payment",
                'Account_Balance' => '0.00',
                'type' => "Liability",
                'cashflow' => "Financing activities",
                'acc_type_group' => "Liabilities",
                'User' => '1',
                'branch_id' => $id,
            ]
        ];

        // Ensure all bank accounts are inserted successfully
        DB::table('company_bank_accounts')->insert($Bank);

        // Define the agreement types to be inserted
        $agreementTypes = [
            [
                'type' => "Voucher",
                'template' => '',
                'status' => 1,
                'branch_id' => $id,
            ],
            [
                'type' => "Facilities Approval Form",
                'template' => '',
                'status' => 1,
                'branch_id' => $id,
            ],
            [
                'type' => "Template 01",
                'template' => '',
                'status' => 1,
                'branch_id' => $id,
            ],
            [
                'type' => "Template 02",
                'template' => '',
                'status' => 1,
                'branch_id' => $id,
            ],
            [
                'type' => "Template 03",
                'template' => '',
                'status' => 1,
                'branch_id' => $id,
            ],
            [
                'type' => "Template 04",
                'template' => '',
                'status' => 1,
                'branch_id' => $id,
            ],
            [
                'type' => "Template 05",
                'template' => '',
                'status' => 1,
                'branch_id' => $id,
            ]
        ];

        // Insert all agreement types at once
        DB::table('agreement_type')->insert($agreementTypes);


        // Define the SMS templates to be inserted
        $smsTemplates = [
            [
                'type' => "customer_registration",
                'template' => '',
                'status' => 0,
                'branch_id' => $id,
            ],
            [
                'type' => "loan_payment",
                'template' => '',
                'status' => 0,
                'branch_id' => $id,
            ],
            [
                'type' => "payment_reminder",
                'template' => '',
                'status' => 0,
                'branch_id' => $id,
            ],
            [
                'type' => "payment_warning",
                'template' => '',
                'status' => 0,
                'branch_id' => $id,
            ],
            [
                'type' => "loan_issue",
                'template' => '',
                'status' => 0,
                'branch_id' => $id,
            ],
            [
                'type' => "birthday_greeting",
                'template' => '',
                'status' => 0,
                'branch_id' => $id,
            ],
            [
                'type' => "payment_undo",
                'template' => '',
                'status' => 0,
                'branch_id' => $id,
            ]
        ];

        // Insert all SMS templates at once
        DB::table('sms_template')->insert($smsTemplates);



        // Insert into the database
        $company = DB::table('company')->insertGetId([
            'company_name' => '',
            'address' => '',
            'contact_no' => '',
            'customer_num_type' => 'Format',
            'customer_seperate_from' => '/',
            'customer_num_start_from' => '1',
            'customer_format' => '@Auto_Id@',
            'loan_num_type' => 'Format',
            'loan_seperate_from' => '-',
            'loan_format' => '@Auto_Id@',
            'inv_loan_num_type' => 'Format',
            'inv_loan_seperate_from' => '-',
            'inv_loan_format' => '@Auto_Id@',
            'account_saving_type' => 'Format',
            'saving_seperate_from' => '-',
            'saving_format' => '@Auto_Id@',
            'mask' => '',
            'branch' => strtoupper(strlen($branch->Name) >= 3 ? substr($branch->Name, 0, 3) : substr($branch->Name, 0, 2)),
            'saturday_sunday' => '0',
            'points' => '0',
            'points_percentage' => '0',
            'product_editable' => 0,
            'branch_id' => $id,
        ]);

        if ($company) {
            // Generate the activation link (modify this URL as per your application)
            $activationLink = url('/activate-branch/' . $id); // Assuming the URL is like /activate-branch/{id}

            Mail::to('asipiyasoftsolution@gmail.com')->send(new BranchCreated($request->branch, $activationLink));

            return response()->json(['success' => true, 'message' => 'Branch saved successfully and email sent.']);
        } else {
            return response()->json(['success' => true, 'message' => 'Branch saved successfully and email sent.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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


    public function updateBranch(Request $request)
    {
        // Validate the branch ID
        $request->validate([
            'branch_id' => 'required|integer',
        ]);

        $targetBranchId = $request->branch_id;
        $hasAccess = false;
        $branchName = null;

        // Check in session allowed branches
        $allowedBranches = user_data('branches') ?? [];
        foreach ($allowedBranches as $branch) {
            // Handle array access (expected from session storage)
            // dd($branch['idBranch'] );
            if (isset($branch['idBranch']) && $branch['idBranch'] == $targetBranchId) {
                $hasAccess = true;
                $branchName = $branch['Name'];
                break;
            }
        }
        // dd(session('branch_access'),$request->branch_id);

        // Fallback for Super Admin (branch_access == 1)
        if (!$hasAccess && session('branch_access') === 1) {
            $hasAccess = true;
            // Fetch name from DB
            $dbBranch = DB::table('branch')->where('branch_id', $targetBranchId)->first();
            if ($dbBranch) {
                $branchName = $dbBranch->Name;
            }
        }

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'You are not assigned to this branch'], 403);
        }

        // Update session
        session(['branch_id' => $targetBranchId]);
        if ($branchName) {
            session(['branch_name' => $branchName]);
        }


        $userController = new UserController();

        // Call the create_panelty function
        $userController->create_panelty();

        return response()->json(['success' => true, 'message' => 'Branch updated successfully']);
    }

    public function activateBranch($id)
    {
        // Check the current status of the branch
        $branch = DB::table('branch')->where('branch_id', $id)->first();

        if ($branch && $branch->status == 1) {
            return redirect()->route('login')->with('error', 'Branch is already active. No further actions needed.');
        }

        // Update the branch status to 1 (Active)
        $updated = DB::table('branch')->where('branch_id', $id)->update(['status' => 1]);

        if ($updated) {
            return redirect()->route('login')->with('error', 'Branch activated successfully!');
        } else {
            return redirect()->route('login')->with('error', 'Branch activation failed! Please check your activation code.');
        }
    }
}
