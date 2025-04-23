<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\table;

class KYCController extends Controller
{
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
        //
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
        $customer = DB::table('customer')->where('idCustomer', $id)->first();

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
                $documents = DB::table('customer_documents')
                    ->where('Customer_idCustomer', $id)
                    ->get();
                return view('pages.Insurance.kyc.documents', compact('documents'));
            case 'loans':
                $loans = DB::table('customer_loan')
                    ->where('Customer_idCustomer', $id)
                    ->orderByDesc('Date_Time')
                    ->get();

                return view('pages.Insurance.kyc.loans', compact('loans'));
            case 'loanSummary':
                $guaranteedLoans = DB::table('witness as w')
                    ->join('customer_loan as cl', 'w.Customer_Loan_idCustomer_Loan', '=', 'cl.idCustomer_Loan')
                    ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
                    ->where('w.cus_id', $id)
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
                return view('pages.Insurance.kyc.insurance', compact('designation','insurance_category'));
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


    public function getHistory()
    {
        $history = DB::table('insurance as i')
            ->join('insurance_category as c', 'i.id_insurance_category', '=', 'c.id_insurance_category')
            ->leftJoin('user as u1', 'i.user_id', '=', 'u1.id') // optional
            ->leftJoin('user as u2', 'i.user_id', '=', 'u2.id') // optional
            ->select(
                'i.created_at as date',
                'c.description as category',
                'i.total_amount',
                'i.note',
                DB::raw("COALESCE(u1.Full_Name, 'Admin') as created_by"),
                DB::raw("COALESCE(u2.Full_Name, 'Manager') as approved_by"),
                'i.status'
            )
            ->orderByDesc('i.created_at')
            ->get();

        return response()->json($history);
    }


}
