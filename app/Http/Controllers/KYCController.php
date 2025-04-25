<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\table;

class KYCController extends Controller
{
    protected $bankLogController;

    public function __construct(BankLogController $bankLogController)
    {
        $this->bankLogController = $bankLogController;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers=tableWithBranch('customer')->get();
        return view('pages.Insurance.KYC',compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = tableWithBranch('customer')->select('idCustomer', 'First_Name', 'Last_Name')->get();
        $categories = tableWithBranch('insurance_category')->select('id_insurance_category', 'description')->get();
        return view('pages.Insurance.Insurance',compact('customers','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_insurance_category' => 'required|integer',
            'day_count' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'documents.*' => 'nullable|file|max:5120' // each file max 5MB
        ]);
        $branch_id=session('branch_id');
        $user_id=session('userid');
        DB::beginTransaction();
        try {
            $insuranceId = DB::table('insurance')->insertGetId([
                'id_insurance_category' => $request->id_insurance_category,
                'customer_id' => $request->customer_id,
                'day_count' => $request->day_count,
                'amount' => $request->amount,
                'total_amount' => $request->total_amount,
                'note' => $request->note,
                'status' => '0',
                'branch_id' => $branch_id,
                'user_id' => $user_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);



            $categoryLevels = tableWithBranch('insurance_category_has_level')
                ->where('category_id', $request->id_insurance_category)
                ->get();

            foreach ($categoryLevels as $level) {
                $designations = tableWithBranch('insurance_category_level_has_designations')
                    ->where('insurance_category_level_id', $level->id_insurance_category_has_level)
                    ->get();

                foreach ($designations as $designation) {
                    DB::table('insurance_approval_status')->insert([
                        'insurance_id'    => $insuranceId,
                        'level_id'        => $level->id_insurance_category_has_level,
                        'designation_id'  => $designation->designation_id,
                        'designation'     => $designation->designation,
                        'user_id'     => $user_id,
                        'status'          => 'Pending',
                        'branch_id'       => $branch_id, // or $designation->branch_id if that's preferred
                    ]);
                }
            }



// Ensure directory exists before storing files
            $directory = 'insurance_documents';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store($directory, 'public');

                    DB::table('insurance_has_document')->insert([
                        'id_insurance' => $insuranceId,
                        'path' => $path,
                        'branch_id' => $branch_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Insurance request saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customers=tableWithBranch('customer')->get();
        return view('pages.Insurance.KYC',compact('customers','id'));
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

    // KYCController.php
    public function loadSection($section, $id)
    {
        $customer = tableWithBranch('customer')->where('idCustomer', $id)->first();
        $branch_id=session('branch_id');
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
        Log::info($section);
        switch ($section) {
            case 'basic':
                return view('pages.Insurance.kyc.basic', compact('customer'));
            case 'guardian':
                return view('pages.Insurance.kyc.guardian', compact('customer'));
            case 'documents':
                $documents = tableWithBranch('customer_documents')
                    ->where('Customer_idCustomer', $id)
                    ->get();
                return view('pages.Insurance.kyc.documents', compact('documents'));
            case 'loans':
                $loans = tableWithBranch('customer_loan')
                    ->where('Customer_idCustomer', $id)
                    ->orderByDesc('Date_Time')
                    ->get();

                return view('pages.Insurance.kyc.loans', compact('loans'));
            case 'loanSummary':
                $guaranteedLoans = DB::table('witness as w')
                    ->join('customer_loan as cl', 'w.Customer_Loan_idCustomer_Loan', '=', 'cl.idCustomer_Loan')
                    ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
                    ->where('w.cus_id', $id)
                    ->where('w.branch_id', $branch_id)
                    ->select(
                        'cl.idCustomer_Loan',
                        'cl.Loan_No',
                        'cl.Date_Time',
                        'cl.Amount',
                        'cl.Interest_Rate',
                        'cl.Installment_Count',
                        'cl.Balance_Amount',
                        'cl.Status',
                        'c.cus_number',
                        'c.First_Name',
                        'c.Last_Name'
                    )
                    ->orderByDesc('cl.Date_Time')
                    ->get();
                return view('pages.Insurance.kyc.guranteed_loan', compact('guaranteedLoans'));
            case 'RoadMap':
                $customer_log=tableWithBranch('customer_log','customer_log')
                    ->join('user','customer_log.user', '=', 'user.id')
                    ->where('customer_id','=',$id)
                    ->get();
                return view('pages.Insurance.kyc.RoadMap', compact('customer_log'));
            case 'insurance':
                $designation=tableWithBranch('designation')->get();
                $insurance_category=tableWithBranch('insurance_category')->get();
                return view('pages.Insurance.kyc.insurance', compact('designation','insurance_category','id'));
            case 'history':
                return view('pages.Insurance.kyc.history', compact('customer'));
            default:
                return response()->json(['error' => 'Invalid section'], 400);
        }
    }


    public function insurance_category(Request $request)
    {
        try {
            DB::beginTransaction();
            $branch_id=session('branch_id');
            $categoryId = DB::table('insurance_category')->insertGetId([
                'description' => $request->category_name,
                'type' => $request->type,
                'amount' => $request->amount,
                'branch_id' => $branch_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($request->levels as $level) {
                $levelId = DB::table('insurance_category_has_level')->insertGetId([
                    'category_id' => $categoryId,
                    'level' => $level['level'],
                    'description' => $level['description'],
                    'branch_id' => $branch_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($level['designations'] as $des) {
                    DB::table('insurance_category_level_has_designations')->insert([
                        'insurance_category_level_id' => $levelId,
                        'designation_id' => $des['id'],
                        'designation' => $des['name'],
                        'branch_id' => $branch_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Insurance category and levels saved successfully.',
                'category' => [
                    'id' => $categoryId,
                    'description' => $request->category_name,
                    'type' => $request->type,
                    'amount' => $request->amount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save data: ' . $e->getMessage()
            ]);
        }
    }


    public function getHistory($id)
    {
        $branch_id = session('branch_id');

        $history = DB::table('insurance as i')
            ->join('insurance_category as c', 'i.id_insurance_category', '=', 'c.id_insurance_category')
            ->leftJoin('user as created', 'i.user_id', '=', 'created.id')
            ->select(
                'i.id_insurance',
                'i.created_at as date',
                'c.description as category',
                'i.total_amount',
                'i.note',
                DB::raw("COALESCE(created.Full_Name, 'Admin') as created_by"),
                'i.status'
            )
            ->where('i.customer_id', '=', $id)
            ->where('i.branch_id', '=', $branch_id)
            ->orderByDesc('i.created_at')
            ->get();

        // Get approval info with Designation (User)
        $approvals = DB::table('insurance_approval_status as a')
            ->join('user as u', 'a.user_id', '=', 'u.id')
            ->select(
                'a.insurance_id',
                DB::raw("GROUP_CONCAT(CONCAT(a.designation, ' (', u.Full_Name, ')') SEPARATOR ', ') as approved_by")
            )
            ->where('a.status', 'Approved')
            ->where('a.branch_id', '=', $branch_id)
            ->groupBy('a.insurance_id')
            ->get()
            ->keyBy('insurance_id');

        // Merge approvals into history
        foreach ($history as $row) {
            $row->approved_by = $approvals[$row->id_insurance]->approved_by ?? '-';
        }

        return response()->json($history);
    }



    public function evidence($id)
    {
        $evidence = tableWithBranch('insurance_has_document')
            ->where('id_insurance', $id)
            ->get()
            ->map(function ($doc) {
                $path = $doc->path;
                $mime = Storage::exists($path) ? Storage::mimeType($path) : 'application/octet-stream';

                return [
                    'name' => basename($path),
                    'url' => Storage::url($path),
                    'type' => $mime
                ];
            });

        return response()->json($evidence);
    }



    public function loadInsurances(Request $request)
    {
        $branch_id = session('branch_id');
        $subApproved = DB::table('insurance_approval_status as s')
            ->join('insurance_category_level_has_designations as d', function($join) {
                $join->on('s.level_id', '=', 'd.insurance_category_level_id')
                    ->on('s.designation_id', '=', 'd.designation_id');
            })
            ->where('s.status', 'Approved')
            ->select(
                's.insurance_id',
                DB::raw('s.level_id'),
                DB::raw('COUNT(*) as approved_designations'),
                DB::raw('(SELECT COUNT(*) FROM insurance_category_level_has_designations WHERE insurance_category_level_id = s.level_id) as required_approvals')
            )
            ->groupBy('s.insurance_id', 's.level_id')
            ->havingRaw('approved_designations = required_approvals');

        $approvedLevels = DB::table($subApproved, 'approved_levels')
            ->select('insurance_id', DB::raw('COUNT(*) as approved_count'))
            ->groupBy('insurance_id');

        $totalLevels = DB::table('insurance_category_has_level')
            ->select('category_id', DB::raw('COUNT(*) as total_count'))
            ->groupBy('category_id');

        $insurances = DB::table('insurance')
            ->join('insurance_category as c', 'insurance.id_insurance_category', '=', 'c.id_insurance_category')
            ->join('customer as cust', 'insurance.customer_id', '=', 'cust.idCustomer')
            ->leftJoinSub($approvedLevels, 'ap', function ($join) {
                $join->on('ap.insurance_id', '=', 'insurance.id_insurance');
            })
            ->leftJoinSub($totalLevels, 'tl', function ($join) {
                $join->on('tl.category_id', '=', 'insurance.id_insurance_category');
            })
            ->select(
                'insurance.*',
                'c.description as category',
                'cust.First_Name',
                'cust.Last_Name',
                DB::raw('IFNULL(ap.approved_count, 0) as approved_count'),
                DB::raw('IFNULL(tl.total_count, 0) as total_count')
            )
            ->where('insurance.branch_id', '=', $branch_id)
            ->when($request->status !== null, function ($q) use ($request) {
                $q->where('insurance.status', $request->status);
            })
            ->when($request->start_date, function ($q) use ($request) {
                $q->whereDate('insurance.created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                $q->whereDate('insurance.created_at', '<=', $request->end_date);
            })
            ->when($request->customer_id, function ($q) use ($request) {
                $q->where('insurance.customer_id', $request->customer_id);
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('insurance.id_insurance_category', $request->category_id);
            })
            ->get();

        return response()->json($insurances);
    }


    public function changeStatus(Request $request)
    {
        $branch_id = session('branch_id');
        DB::table('insurance')
            ->where('id_insurance', $request->id)
            ->where('branch_id', $branch_id)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
    public function getApprovalLevels($categoryId, $insuranceId)
    {
        $levels = tableWithBranch('insurance_category_has_level')
            ->where('category_id', $categoryId)
            ->orderBy('level')
            ->get();

        $results = [];

        foreach ($levels as $level) {
            // Get all designations under this level
            $designations = tableWithBranch('insurance_category_level_has_designations')
                ->where('insurance_category_level_id', $level->id_insurance_category_has_level)
                ->get();

            $designationStatuses = [];

            foreach ($designations as $designation) {
                // Check status from insurance_approval_status
                $statusRow = tableWithBranch('insurance_approval_status')
                    ->where('insurance_id', $insuranceId)
                    ->where('level_id', $level->id_insurance_category_has_level)
                    ->where('designation_id', $designation->designation_id)
                    ->first();

                $designationStatuses[] = [
                    'designation' => $designation->designation,
                    'note' => $statusRow->description,
                    'designation_id' => $designation->designation_id,
                    'status' => $statusRow ? $statusRow->status : 'Pending',
                    'user_id' => $statusRow->user_id ?? null,
                    'approved_at' => $statusRow->updated_at ?? null
                ];
            }

            $isLevelApproved = collect($designationStatuses)->contains('status', 'Approved');

            $results[] = [
                'id' => $level->id_insurance_category_has_level,
                'level' => $level->level,
                'description' => $level->description,
                'designations' => $designationStatuses,
                'is_approved' => $isLevelApproved
            ];

        }

        return response()->json($results);
    }

    public function approveLevel(Request $request)
    {
        $userDesignation = session('designation');
        $branch_id = session('branch_id');
        $user_id = session('userid');

        // Get all allowed designation_ids for this level
        $designationIds = tableWithBranch('insurance_category_level_has_designations')
            ->where('insurance_category_level_id', $request->level_id)
            ->pluck('designation_id')
            ->toArray();

        // Check if user is authorized (unless Admin)
        if ($userDesignation !== 'Admin') {
            $userHasDesignation = tableWithBranch('designation')
                ->whereIn('idDesignation', $designationIds)
                ->where('name', $userDesignation)
                ->exists();

            if (!$userHasDesignation) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Update all pending records for this level & insurance
        $updated = DB::table('insurance_approval_status')
            ->where('insurance_id', $request->insurance_id)
            ->where('level_id', $request->level_id)
            ->whereIn('designation_id', $designationIds)
            ->where('status', '!=', 'Approved')
            ->where('branch_id', $branch_id)
            ->update([
                'user_id' => $user_id,
                'description' => $request->note,
                'status' => 'Approved',
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'No pending approval found to update.'], 404);
        }
    }



    public function rejectInsurance(Request $request)
    {
        $insuranceId = $request->insurance_id;
        $userId = session('userid');
        $branchId = session('branch_id');
        $note = $request->note ?? null;

        // 1. Update the insurance status to "Rejected"
        DB::table('insurance')
            ->where('id_insurance', $insuranceId)
            ->where('branch_id', '=', $branchId)
            ->update(['status' => '-1']);

        // 2. Fetch all levels for the insurance's category
        $insurance = tableWithBranch('insurance')->where('id_insurance', $insuranceId)->first();

        if ($insurance) {
            $levels = tableWithBranch('insurance_category_has_level')
                ->where('category_id', $insurance->id_insurance_category)
                ->get();

            foreach ($levels as $level) {
                // 3. Fetch all designations under this level
                $designations = tableWithBranch('insurance_category_level_has_designations')
                    ->where('insurance_category_level_id', $level->id_insurance_category_has_level)
                    ->get();

                foreach ($designations as $designation) {
                    // 4. Update status to "Rejected" for each designation in this level
                    DB::table('insurance_approval_status')
                        ->where('insurance_id', $insuranceId)
                        ->where('level_id', $level->id_insurance_category_has_level)
                        ->where('designation_id', $designation->designation_id)
                        ->where('branch_id', $branchId)
                        ->update([
                            'status' => 'Rejected',
                            'user_id' => $userId,
                            'description' => $note,
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }


// YourController.php
    public function getBankAccounts()
    {
        $accounts = tableWithBranch('company_bank_accounts')->where('Bank_Type','=','Bank')->get();
        return response()->json($accounts);
    }


    public function issueInsurance(Request $request)
    {
        $bankId = $request->bank_id;
        $insuranceId = $request->insurance_id;
        $userId = session('userid'); // or auth()->id()
        $branch_id = session('branch_id'); // or auth()->id()
        try {
            DB::beginTransaction();

            $updated = DB::table('insurance')
                ->where('id_insurance', $insuranceId)
                ->where('branch_id', '=', $branch_id)
                ->update([
                    'status' => '1',
                ]);

            if (!$updated) {
                DB::rollBack();
                return response()->json(['error' => 'Insurance update failed.'], 400);
            }

            $system_bank=tableWithBranch('company_bank_accounts')->where('Bank_Type','=','System_default_11')->first();
            if ($system_bank){
                $system_bank_id=$system_bank->Idbank;

            }else{
                $system_bank_id=DB::table('company_bank_accounts')->insertGetId([
                    'Bank_Type' => "System_default_11",
                    'code' => "1121",
                    'Bank_Name' => "Insurance Payable",
                    'Account_Name' => "Insurance Payable",
                    'Account_No' => "Insurance Payable",
                    'Bank_Branch' => "Insurance Payable",
                    'Account_Balance' => '0.00',
                    'type' => "Liability",
                    'cashflow' => "Financing activities",
                    'acc_type_group' => "Liabilities",
                    'User' => $userId,
                    'branch_id' => $branch_id,
                ]);
            }

            $insurance=tableWithBranch('insurance')->where('id_insurance','=',$insuranceId)->first();
            $fromAmount=$insurance->total_amount;

            $this->bankLogController->index($bankId,"Insurance",'Insurance Claim'.' ('.$insuranceId.')','Insurance Claim',"credit",$fromAmount,$system_bank_id);
            $this->bankLogController->index($system_bank_id,"Insurance",'Insurance Claim'.' ('.$insuranceId.')','Insurance Claim',"debit",$fromAmount,$bankId);


            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
