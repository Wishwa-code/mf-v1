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
        return view('pages.Branches',compact('branches'));
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
        $branch = DB::table('branch')->insertGetId([
            'Name' => $request->branch,
            'status' => 0, // Default to Active
        ]);

        // Generate the activation link (modify this URL as per your application)
        $activationLink = url('/activate-branch/'.$branch); // Assuming the URL is like /activate-branch/{id}

        Mail::to('janeesameera@gmail.com')->send(new BranchCreated($request->branch, $activationLink));

        // Return response
        return response()->json(['success' => true, 'message' => 'Branch saved successfully and email sent.']);
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

        // Update the session with the new branch ID
        session(['branch_id' => $request->branch_id]);


        return response()->json(['success' => true, 'message' => 'Branch updated successfully']);
    }

    public function activateBranch($id)
    {
        // Check the current status of the branch
        $branch = DB::table('branch')->where('branch_id', $id)->first();

        if ($branch && $branch->status == 1) {
            // If the branch is already active, do nothing and return success message
            return redirect()->route('login')->with('error', 'Branch is already active. No further actions needed.');
        }

        // Update the branch status to 1 (Active)
        $updated = DB::table('branch')->where('branch_id', $id)->update(['status' => 1]);
        if ($updated) {
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
                ]
            ];

            // Insert multiple records
            foreach ($Bank as $bankData) {
                $insertedId = insertWithBranch('company_bank_accounts', $bankData);

                // Log the account creation
                $bankLogController = new BankLogController();
                $bankLogController->index($insertedId, "Account Creation", "-", "-", "debit", '0.00');
            }

            // Redirect to a confirmation page
            return redirect()->route('login')->with('success', 'Branch activated successfully!');
        } else {
            return redirect()->route('login')->with('error', 'Branch activation failed ! Please check your activation code !');
        }
    }




}
