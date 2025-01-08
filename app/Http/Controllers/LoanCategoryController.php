<?php

namespace App\Http\Controllers;

use App\Models\LoanCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loan_category = tableWithBranch('loan_category')->get();
        return view('pages.Product', compact('loan_category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $designation= tableWithBranch('designation')->get();
        return view('pages.CreateProduct', compact('designation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Save LoanCategory
        $loancategory = new LoanCategory();
        $loancategory->Name = $request->product_name;
        $loancategory->Product_code = $request->product_code;
        $loancategory->Loan_amount = $request->loan_amount_from;
        $loancategory->Loan_amount_to = $request->loan_amount_to;
        $loancategory->Interest_method = $request->interest_method;
        $loancategory->Interest_period = $request->interest_period;
        $loancategory->Loan_interest = $request->interest_from;
        $loancategory->Loan_interest_to = $request->interest_to;
        $loancategory->Duration_period = $request->duration_period;
        $loancategory->Loan_period = $request->loan_duration;
        $loancategory->Repayment_type = $request->collection_type;
        $loancategory->Panelty_period = $request->penalty_period;
        $loancategory->Panelty_pecentage = $request->panelty_rate;
        $loancategory->Panelty_date = $request->panelty_rate_date;
        $loancategory->Guarantee_count = $request->witnessCount;
        $loancategory->Interest_Period_Count = $request->period_count;

        $loancategory->enable_saving_process = $request->enable_saving;
        $loancategory->saving_amount_type = $request->saving_account_amount_type;
        $loancategory->saving_amount = $request->saving_amount;
        $loancategory->default_loan_duration_period = $request->default_loan_duration_period;
        $loancategory->branch_id = session('branch_id');


        $loancategory->save();

        $categoryId = $loancategory->getKey();




        // Save other charges
        $otherchargesArray = $request->input('othercharges');
        if (!empty($otherchargesArray) && is_array($otherchargesArray)) {
            foreach ($otherchargesArray as $row) {
                $data = [
                    'Description' => $row[0],
                    'Amount' => $row[2],
                    'charge_type' => $row[1],
                    'Loan_Category_idLoan_Category' => $categoryId,
                ];

// Insert the data with branch-specific logic
                insertWithBranch('other_charges', $data);

            }
        }


        // Save required documents
        $documentArray = $request->input('document');
        if (!empty($documentArray) && is_array($documentArray)) {
            foreach ($documentArray as $row) {
                $data = [
                    'Name' => $row[0],
                    'Loan_Category_idLoan_Category' => $categoryId,
                ];

// Insert the data with branch-specific logic
                insertWithBranch('required_documents', $data);

            }
        }

        $level_data = $request->input('level_data');

// Validate the structure of levelsData array
        if (!is_array($level_data) || count($level_data) === 0) {

        } else {
            // Loop through each level data
            foreach ($level_data as $levelData) {
                $level = $levelData['level'];
                $description = $levelData['description'];

                if ($description == "") {
                    $description = "-";
                }
                $data = [
                    'product_id' => $categoryId,
                    'type' => $level,
                    'description' => $description,
                ];

                // Insert the data with branch-specific logic and get the inserted ID
                $levelId = insertWithBranch('level', $data);

                // Loop through designations for each level
                foreach ($levelData['designations'] as $designation) {
                    $designationId = $designation['id'];
                    $designationName = $designation['name'];

                    $data = [
                        'level_id' => $levelId,
                        'designation_id' => $designationName,
                    ];

                    // Insert the data with branch-specific logic
                    insertWithBranch('level_has_designation', $data);
                }

                // Loop through checklist items for each level
                if (isset($levelData['checklist']) && is_array($levelData['checklist'])) {
                    foreach ($levelData['checklist'] as $checklistItem) {
                        $checklistData = [
                            'level_id' => $levelId,
                            'description' => $checklistItem,
                        ];

                        // Insert the checklist item into the approval_checklist table
                        insertWithBranch('approval_checklist', $checklistData);
                    }
                }
            }
        }

        // Return success response
        return response()->json(['message' => 'Loan category saved successfully'], 200);



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $other_charges = tableWithBranch('other_charges')
            ->where('Loan_Category_idLoan_Category', '=', $id)
            ->get();
        $required_documents = tableWithBranch('required_documents')
            ->where('Loan_Category_idLoan_Category', '=', $id)
            ->get();

        return response()->json(['other_charges' => $other_charges, 'required_documents' => $required_documents], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category=tableWithBranch('loan_category')->where('idLoan_Category','=',$id)->get();
        return response()->json(['item' => $category], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $product_id=$request->product_id;
        $product_code=$request->product_code;
        $product_name=$request->product_name;
        $loan_amount=$request->loan_amount;
        $Loan_amount_to=$request->loan_amount_to;
        $interest_method=$request->interest_method;
        $interest_period=$request->interest_period;
        $interest=$request->interest;
        $Loan_interest_to=$request->interest_to;
        $duration_period=$request->duration_period;
        $loan_duration=$request->loan_duration;
        $collection_type=$request->collection_type;
        $penalty_period=$request->penalty_period;
        $panelty_rate=$request->panelty_rate;
        $panelty_rate_date=$request->panelty_rate_date;
        $witnessCount=$request->witnessCount;


        $data = [
            'Name' => $product_name,
            'Product_code' => $product_code,
            'Loan_amount' => $loan_amount,
            'Loan_amount_to' => $Loan_amount_to,
            'Interest_method' => $interest_method,
            'Interest_period' => $interest_period,
            'Loan_interest' => $interest,
            'Loan_interest_to' => $Loan_interest_to,
            'Duration_period' => $duration_period,
            'Loan_period' => $loan_duration,
            'Repayment_type' => $collection_type,
            'Panelty_period' => $penalty_period,
            'Panelty_pecentage' => $panelty_rate,
            'Panelty_date' => $panelty_rate_date,
            'Guarantee_count' => $witnessCount,
        ];

// Use the updateWithBranch helper function to update the data
        updateWithBranch('loan_category', 'idLoan_Category', $product_id, $data);


        deleteWithBranch('other_charges','Loan_Category_idLoan_Category', $product_id);
        deleteWithBranch('required_documents','Loan_Category_idLoan_Category', $product_id);


        // Save other charges
        $otherchargesArray = $request->input('othercharges');

        if (!empty($otherchargesArray)) {
            foreach ($otherchargesArray as $row) {
                $data = [
                    'Description' => $row[0],
                    'Amount' => $row[2],
                    'charge_type' => $row[1],
                    'Loan_Category_idLoan_Category' => $product_id,
                ];

// Use the insertWithBranch helper function to insert the data with branch_id
                insertWithBranch('other_charges', $data);

            }
        }

// Save required documents
        $documentArray = $request->input('document');

        if (!empty($documentArray)) {
            foreach ($documentArray as $row) {
                $data = [
                    'Name' => $row[0],
                    'Loan_Category_idLoan_Category' => $product_id,
                ];

// Use the insertWithBranch helper function to insert the data with branch_id
                insertWithBranch('required_documents', $data);

            }
        }


        return response()->json(['message' => 'Center updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        // Check if the ID is used in other tables
        $referencesExist = DB::table('customer_loan')
            ->where('Loan_Category_idLoan_Category', '=', $id)
            ->where('branch_id', session('branch_id'))
            ->exists();
        if ($referencesExist) {
            return response()->json(['message' => 'Cannot delete: ID is in use in other tables'], 422);
        }

// If no references exist, proceed with deletion
        deleteWithBranch('loan_category','idLoan_Category', $id);
        deleteWithBranch('other_charges','Loan_Category_idLoan_Category', $id);
        deleteWithBranch('required_documents','Loan_Category_idLoan_Category', $id);

// Return success response
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }


    public function remove_other_charges(string $id)
    {
        deleteWithBranch('other_charges','idOther_Charges', $id);
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }

    public function remove_doc(string $id)
    {
        deleteWithBranch('required_documents','idRequired_Documents', $id);
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }


    public function load_charger(string $id){
        $other_charges = tableWithBranch('other_charges')
            ->where('Loan_Category_idLoan_Category', '=', $id)
            ->get();
        $required_documents = tableWithBranch('required_documents')
            ->where('Loan_Category_idLoan_Category', '=', $id)
            ->get();

        return response()->json(['other_charges' => $other_charges,'required_documents'=>$required_documents], 200);
    }


    public function load_product_details(string $id){
        $product_details = tableWithBranch('loan_category')
            ->where('idLoan_Category', '=', $id)
            ->get();
        return response()->json(['product_details' => $product_details], 200);
    }


    public function saving_update(Request $request){
        // Prepare the data to update
        $data = [
            'enable_saving_process' => $request->enable_saving_process,
            'saving_amount_type' => $request->saving_amount_type,
            'saving_amount' => $request->saving_amount,
        ];

// Use the updateWithBranch helper function to update the data
        updateWithBranch('loan_category', 'idLoan_Category', $request->id, $data);



        return response()->json(['message' => 'Data updated successfully'], 200);
    }


    public function loadChecklist($levelId,$loan_id) {
        $checklist = DB::table('loan_has_approval_checklist')
            ->where('level', $levelId)
            ->where('loan_id', $loan_id)
            ->get();

        return response()->json(['success' => true, 'checklist' => $checklist]);
    }

    public function updateChecklist(Request $request, $itemId) {
        $user_id = (int)session('userid');
        DB::table('loan_has_approval_checklist')
            ->where('id', $itemId)
            ->update(['status' => $request->status,'user_id'=>$user_id]);

        return response()->json(['success' => true]);
    }


}
