<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerController extends Controller
{


    protected $customerLogController;
    protected $smsLogController;

    // Single constructor to inject both controllers
    public function __construct(CustomerLogController $customerLogController, SmsController $smsLogController)
    {
        $this->customerLogController = $customerLogController;
        $this->smsLogController = $smsLogController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $customers = tableWithBranch('customer')->where('idCustomer', '=', $id)->get();
        $customer_loan = tableWithBranch('customer_loan','customer_loan')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->select('customer_loan.*', 'loan_category.Name as loan_name')
            ->where('Customer_idCustomer', '=', $id)->get();


        return response()->json(['item' => $customers,'loan' => $customer_loan], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        // Validation rules
        $validatedData = $request->validate([
            'customer' => 'required',
            'description' => 'required',
        ]);

// Retrieve data from the request
        $customer = $validatedData['customer'];
        $description = $validatedData['description'];
        $file = $request->file('file');

// Define the directory where the file will be stored
        $directory = 'documents';

// Check if the directory exists on the public disk, create it if not
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

// Store the file on the public disk
        $documentPath = Storage::disk('public')->putFile($directory, $file);

// $documentPath now contains the path to the stored file relative to the 'public' disk

    // Get customer details for description
    $customerData = tableWithBranch('customer')
        ->where('idCustomer', $customer)
        ->first();
    
    $customerName = $customerData ? ($customerData->First_Name . ' ' . $customerData->Last_Name) : 'Unknown';
    
    // Store document upload data for approval
    $requestData = [
        'customer_id' => $customer,
        'description' => $description,
        'document_path' => $documentPath,
    ];

    // Create approval request
    DB::table('approval_request')->insert([
        'type' => 'Customer Document Upload',
        'typeid' => 305,
        'description' => 'Upload Document: ' . $description . ' (Customer: ' . $customerName . ')',
        'data' => json_encode($requestData),
        'userid' => session('userid'),
        'branch_id' => session('branch_id'),
        'data_time' => now(),
        'status' => 0
    ]);

    return response()->json(['message' => 'Document upload request sent for approval!'], 200);
    
    // OLD CODE - keeping for approval handler reference
    /*
    insertWithBranch('customer_documents', [
        'Customer_idCustomer' => $customer,
        'Description' => $description,
        'Path' => $documentPath,
    ]);
    return response()->json(['message' => 'Document saved successfully'], 200);
    */
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if (DB::table('customer')
            ->Where('Nic', '=', $request->new_nic)
            ->where('branch_id', '=', session('branch_id')) // Check within the same branch
            ->exists()) {
            return response()->json(['message' => 'This customer nic already exists!', 'id' => '0'], 200);
        }  else if (DB::table('customer')
            ->where('cus_number', '=', $request->cus_number)
            ->where('branch_id', '=', session('branch_id')) // Check within the same branch
            ->exists()) {
            return response()->json(['message' => 'This customer number already exists!', 'id' => '0'], 200);
        } else if (DB::table('customer')
            ->Where('Contact_No', '=', $request->contact_number)
            ->where('branch_id', '=', session('branch_id')) // Check within the same branch
            ->exists()) {
            return response()->json(['message' => 'This customer contact number already exists!', 'id' => '0'], 200);
        }else{

            // Instantiate a new Customer object
            $customer = new Customer();
            $customer->Title = $request->title;
            $customer->Customer_Group_idCustomer_Group = 1;




// Set final customer number with branch prefix
            $customer->cus_number =  '-';
            $customer->First_Name = $request->f_name;
            $customer->Last_Name = $request->last_name;
            $customer->Email = $request->email;
            $customer->Contact_No = $request->contact_number;
            $customer->contact_number_2 = $request->contact_number_2;
            $customer->business_registration = $request->business_registration;
            $customer->Nic = $request->nic;
            $customer->Gender = $request->gender;
            $customer->Dob = $request->dob;

            $customer->Address = $request->curr_address_01;
            $customer->Address_02 = $request->curr_address_02;
            $customer->Address_03 = $request->curr_address_03;

            $customer->Per_Address_01 = $request->per_address_01;
            $customer->Per_Address_02 = $request->per_address_02;
            $customer->Per_Address_03 = $request->per_address_03;


            $customer->City = $request->city;
            $customer->State = $request->state;
            $customer->Landline = $request->landline;
            $customer->Note = $request->note;
            $customer->Longitude = $request->longitude;
            $customer->Latitude = $request->latitude;
            $customer->Gua_title = $request->gua_title;
            $customer->Gua_name = $request->gua_name;
            $customer->Guardian_gender = $request->guardian_gender;
            $customer->Gua_relation = $request->gua_relation;
            $customer->Gua_occu = $request->gua_occu;
            $customer->Gua_contact = $request->gua_contact;

            $customer->Gua_address = $request->gua_address_01.','.$request->gua_address_02.','.$request->gua_address_03;


            $customer->Gua_nic = $request->gua_nic;
            $customer->Customer_Risk_Level = $request->risk_level;
            $customer->civil_status = $request->civil_status;


            $customer->occu_job_position = $request->occu_job_position;
            $customer->occu_monthly_salary = $request->occu_monthly_salary;
            $customer->occu_address_01 = $request->occu_address_01;
            $customer->occu_address_02 = $request->occu_address_02;
            $customer->occu_address_03 = $request->occu_address_03;
            $customer->occu_contact_no = $request->occu_contact_no;
            $customer->occu_longitude = $request->occu_longitude;
            $customer->occu_latitude = $request->occu_latitude;
            $customer->route_id = $request->root;
            $customer->branch_id = session('branch_id');



            // Handle file upload
            if ($request->hasFile('cus_phto')) {
                $file = $request->file('cus_phto');
                $directory = 'documents';

                // Check if the directory exists on the public disk, create it if not
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Store the file on the public disk
                $documentPath = Storage::disk('public')->putFile($directory, $file);
                $customer->Cus_phto = $documentPath;
            }





            // Store customer data for approval instead of direct save
            $customerData = [
                'Title' => $customer->Title,
                'Customer_Group_idCustomer_Group' => $customer->Customer_Group_idCustomer_Group,
                'cus_number' => $customer->cus_number,
                'First_Name' => $customer->First_Name,
                'Last_Name' => $customer->Last_Name,
                'Email' => $customer->Email,
                'Contact_No' => $customer->Contact_No,
                'contact_number_2' => $customer->contact_number_2,
                'business_registration' => $customer->business_registration,
                'Nic' => $customer->Nic,
                'Gender' => $customer->Gender,
                'Dob' => $customer->Dob,
                'Address' => $customer->Address,
                'Address_02' => $customer->Address_02,
                'Address_03' => $customer->Address_03,
                'Per_Address_01' => $customer->Per_Address_01,
                'Per_Address_02' => $customer->Per_Address_02,
                'Per_Address_03' => $customer->Per_Address_03,
                'City' => $customer->City,
                'State' => $customer->State,
                'Landline' => $customer->Landline,
                'Note' => $customer->Note,
                'Longitude' => $customer->Longitude,
                'Latitude' => $customer->Latitude,
                'Gua_title' => $customer->Gua_title,
                'Gua_name' => $customer->Gua_name,
                'Guardian_gender' => $customer->Guardian_gender,
                'Gua_relation' => $customer->Gua_relation,
                'Gua_occu' => $customer->Gua_occu,
                'Gua_contact' => $customer->Gua_contact,
                'Gua_address' => $customer->Gua_address,
                'Gua_nic' => $customer->Gua_nic,
                'Customer_Risk_Level' => $customer->Customer_Risk_Level,
                'civil_status' => $customer->civil_status,
                'occu_job_position' => $customer->occu_job_position,
                'occu_monthly_salary' => $customer->occu_monthly_salary,
                'occu_address_01' => $customer->occu_address_01,
                'occu_address_02' => $customer->occu_address_02,
                'occu_address_03' => $customer->occu_address_03,
                'occu_contact_no' => $customer->occu_contact_no,
                'occu_longitude' => $customer->occu_longitude,
                'occu_latitude' => $customer->occu_latitude,
                'route_id' => $customer->route_id,
                'branch_id' => $customer->branch_id,
                'Cus_phto' => $customer->Cus_phto,
            ];

            $requestData = [
                'customer_data' => $customerData,
            ];

            // Create approval request
            DB::table('approval_request')->insert([
                'type' => 'Customer Creation',
                'typeid' => 301,
                'description' => 'Customer Creation: ' . $customer->First_Name . ' ' . $customer->Last_Name . ' (NIC: ' . $customer->Nic . ')',
                'data' => json_encode($requestData),
                'userid' => session('userid'),
                'branch_id' => session('branch_id'),
                'data_time' => now(),
                'status' => 0
            ]);

            return response()->json(['message' => 'Customer creation request sent for approval!', 'id' => '0'], 200);

            // OLD CODE - keeping for approval handler reference
            /*
            if ($customer->save()) {
                $id = $customer->id;
                $request = new Request([
                    'customer_id' => $id,
                    'description' => 'Customer registration for '.$request->f_name.' '.$request->last_name,
                    'description_id' => $id,
                    'comment' => ' ',
                    'type' => 'Customer Registration',
                ]);
                $this->customerLogController->store($request);
                // If the data is saved successfully, return a success response

                customer_number($id);

                $sms_template = tableWithBranch('sms_template')->where('type', '=', 'customer_registration')->where('status', '=', '1')->first();
                if ($sms_template) {

                    $customer_table = tableWithBranch('customer')->where('idCustomer', '=', $id)->first();

                    $placeholders = [
                        '@Member_No@' => $customer_table->cus_number,  // Example: Member number
                        '@Member_Name@' => $customer_table->First_Name.' '.$customer_table->Last_Name,  // Example: First and last name
                    ];

                    // Step 3: Replace placeholders in the loan_format
                    $loan_number_txt = $sms_template->template;
                    foreach ($placeholders as $placeholder => $value) {
                        $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
                    }

                    // Log the SMS message
                    $this->smsLogController->index($id, $loan_number_txt, "Customer Registration");


                }


                return response()->json(['message' => 'Data saved successfully','id'=>$id], 200);
            } else {
                // If the data failed to save, return an error response
                return response()->json(['message' => 'Failed to save data'], 500);
            }
            */
        }




    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer_doc = tableWithBranch('customer_documents')->where('Customer_idCustomer','=',$id)->get();
        return response()->json(['item' => $customer_doc], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $isHeadOffice = (int)session('branch_id') === -1;

        if ($isHeadOffice) {
            // Unscoped (all branches) when Head Office
            $loanSub = DB::table('customer_loan')
                ->select(
                    'Customer_idCustomer',
                    DB::raw('SUM(CASE WHEN Status = 0 THEN 1 ELSE 0 END) as current_loans'),
                    DB::raw('SUM(CASE WHEN Status = 1 THEN 1 ELSE 0 END) as settled_loans')
                )
                ->groupBy('Customer_idCustomer');

            $customers = DB::table('customer as customer')
                ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
                ->leftJoin('branch', 'customer.branch_id', '=', 'branch.branch_id')
                ->leftJoinSub($loanSub, 'loan_counts', function ($join) {
                    $join->on('customer.idCustomer', '=', 'loan_counts.Customer_idCustomer');
                })
                ->select(
                    'customer.*',
                    'customer_group.Name as group_name',
                    'center.Name as center_name',
                    'branch.Name as branch_name',
                    DB::raw('IFNULL(loan_counts.current_loans, 0) as current_loans'),
                    DB::raw('IFNULL(loan_counts.settled_loans, 0) as settled_loans')
                )
                ->get();
        } else {
            // Branch-scoped for regular branches
            $loanSub = tableWithBranch('customer_loan')
                ->select(
                    'Customer_idCustomer',
                    DB::raw('SUM(CASE WHEN Status = 0 THEN 1 ELSE 0 END) as current_loans'),
                    DB::raw('SUM(CASE WHEN Status = 1 THEN 1 ELSE 0 END) as settled_loans')
                )
                ->groupBy('Customer_idCustomer');

            $customers = tableWithBranch('customer', 'customer')
                ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
                ->leftJoin('branch', 'customer.branch_id', '=', 'branch.branch_id')
                ->leftJoinSub($loanSub, 'loan_counts', function ($join) {
                    $join->on('customer.idCustomer', '=', 'loan_counts.Customer_idCustomer');
                })
                ->select(
                    'customer.*',
                    'customer_group.Name as group_name',
                    'center.Name as center_name',
                    'branch.Name as branch_name',
                    DB::raw('IFNULL(loan_counts.current_loans, 0) as current_loans'),
                    DB::raw('IFNULL(loan_counts.settled_loans, 0) as settled_loans')
                )
                ->get();
        }

        if ($isHeadOffice) {
            $group = DB::table('customer_group')->get();
            $center = DB::table('center')->get();
            // At HO there can be multiple companies; pick none or any — retaining prior behavior of single
            $company = DB::table('company')->first();
            $route = DB::table('route')->get();
        } else {
            $group = tableWithBranch('customer_group')->get();
            $center = tableWithBranch('center')->get();
            $company = DB::table('company')->first();
            $route= tableWithBranch('route')->get();
        }

        return view('pages.ViewCustomer', compact('customers','route','group','center','company'));
    }



    public function blacklist()
    {
        $customers = tableWithBranch('customer','customer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->where('customer.Status','=','0')
            ->select('customer.*', 'customer_group.Name as group_name', 'center.Name as center_name')
            ->get();
        $group = tableWithBranch('customer_group')->get();
        $center = tableWithBranch('center')->get();
        $company = DB::table('company')->first();
        $route= tableWithBranch('route')->get();
        return view('pages.BlacklistCustomer', compact('customers','route','group','center','company'));
    }


//ViewCustomerSaving
    public function edit_saving()
    {
        $customers = tableWithBranch('customer','customer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->join('Customer_Saving_Accounts', 'customer.idCustomer', '=', 'Customer_Saving_Accounts.Customer_Id')
            ->distinct()
            ->select('customer.*', 'customer_group.Name as group_name', 'center.Name as center_name')
            ->get();


        $group = tableWithBranch('customer_group')->get();
        $center = tableWithBranch('center')->get();
        $company = DB::table('company')->first();
        return view('pages.ViewCustomerSaving', compact('customers','group','center','company'));
    }

    public function recovery()
    {
        $recoveryAccounts = DB::table('recovery_account as ra')
            ->join('customer as c', 'c.idCustomer', '=', 'ra.customer_id')
            ->where('ra.branch_id', session('branch_id'))
            ->select(
                'ra.idRecovery_Account',
                'ra.customer_id',
                'c.cus_number',
                DB::raw("CONCAT(c.First_Name, ' ', c.Last_Name) as customer_name"),
                'c.Nic',
                'c.Contact_No',
                'ra.current_balance',
                'ra.status'
            )
            ->get();

        $company = DB::table('company')->first(); // you already seem to pass $company->company_name

        return view('pages.recovery_accounts', compact('recoveryAccounts','company'));

    }

    public function logs($id)
    {
        $branchId = session('branch_id');

        // Verify the recovery account exists in this branch
        $account = DB::table('recovery_account')
            ->where('idRecovery_Account', $id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$account) {
            return response()->json([
                'logs'    => [],
                'message' => 'Not found or no access'
            ], 404);
        }

        $logs = DB::table('recovery_account_log as ral')
            ->leftJoin('customer_loan as cl', function($join) {
                $join->on('ral.loan_id', '=', 'cl.idCustomer_Loan');
            })
            ->where('ral.recovery_account_id', $id)
            ->where('ral.branch_id', $branchId)
            ->orderBy('ral.idRecovery_Account_Log', 'desc')
            ->get([
                'ral.created_at',
                'ral.action_type',
                'ral.description',
                'ral.amount',
                'ral.balance_after',
                'cl.Loan_No as loan_no',
            ]);

        return response()->json([
            'logs' => $logs
        ], 200);
    }





    public function customer_saving($id){
        $customers =tableWithBranch('customer')
            ->where('idCustomer','=',$id)
            ->first();
        $saving_account=tableWithBranch('Customer_Saving_Accounts')
            ->where('Customer_Id','=',$id)
            ->get();

        $total_balance=0;
        foreach ($saving_account as $item) {
            $total_balance+=$item->Balance;
        }
        $saving_account_count=tableWithBranch('Customer_Saving_Accounts')
            ->where('Customer_Id','=',$id)
            ->count();
        return view('pages.customerAccountReport', compact('customers','saving_account','saving_account_count','total_balance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        $customers = tableWithBranch('customer','customer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select('customer.*', 'customer_group.Name as group_name', 'center.Name as center_name')
            ->get();
        return view('pages.ViewCustomer', compact('customers'));
    }

    public function borrower()
    {
        $customers = tableWithBranch('customer','customer')
            ->join('customer_loan', 'customer.idCustomer', '=', 'customer_loan.Customer_idCustomer') // Ensure only customers with loans are selected
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select('customer.*', 'customer_group.Name as group_name', 'center.Name as center_name')
            ->distinct() // Ensure unique customer records
            ->get();
        return view('pages.BorrowerReport', compact('customers'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Get document data before requesting deletion
        $document = tableWithBranch('customer_documents')
            ->where('idCustomer_Documents', '=', $id)
            ->first();
        
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        
        // Get customer details for description
        $customer = tableWithBranch('customer')
            ->where('idCustomer', $document->Customer_idCustomer)
            ->first();
        
        $customerName = $customer ? ($customer->First_Name . ' ' . $customer->Last_Name) : 'Unknown';
        
        // Store document delete data for approval
        $requestData = [
            'document_id' => $id,
            'document_data' => (array)$document,
            'customer_id' => $document->Customer_idCustomer,
        ];

        // Create approval request
        DB::table('approval_request')->insert([
            'type' => 'Customer Document Delete',
            'typeid' => 305,
            'description' => 'Delete Document: ' . $document->Description . ' (Customer: ' . $customerName . ')',
            'data' => json_encode($requestData),
            'userid' => session('userid'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);
        
        return response()->json(['message' => 'Document delete request sent for approval!'], 200);
        
        // OLD CODE - keeping for approval handler reference
        /*
        tableWithBranch('customer_documents')->where('idCustomer_Documents', '=', $id)->delete();
        return response()->json(['message' => 'Data deleted successfully'], 200);
        */
    }

    public function updateCustomer(Request $request) {

        $documentPath=null;
        // Handle file upload
        if ($request->hasFile('cus_phto')) {
            $file = $request->file('cus_phto');
            $directory = 'documents';

            // Check if the directory exists on the public disk, create it if not
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the file on the public disk
            $documentPath = Storage::disk('public')->putFile($directory, $file);
        }

        // Assuming you have the request object available
        $data = [
            'Title' => $request->title,
            'First_Name' => $request->f_name,
            'Last_Name' => $request->last_name,
            'Email' => $request->email,
            'Contact_No' => $request->contact_number,
            'Nic' => $request->nic,
            'Gender' => $request->gender,
            'Dob' => $request->dob,
            'Address' => $request->curr_address_01,
            'Address_02' => $request->curr_address_02,
            'Address_03' => $request->curr_address_03,
            'Per_Address_01' => $request->per_address_01,
            'Per_Address_02' => $request->per_address_02,
            'Per_Address_03' => $request->per_address_03,
            'City' => $request->city,
            'State' => $request->state,
            'Landline' => $request->landline,
            'Gua_title' => $request->gua_title,
            'Gua_name' => $request->gua_name,
            'Guardian_gender' => $request->guardian_gender,
            'Gua_relation' => $request->gua_relation,
            'Gua_occu' => $request->gua_occu,
            'Gua_contact' => $request->gua_contact,
            'Gua_address' => $request->gua_address,
            'Note' => $request->note,
            'Longitude' => $request->longitude,
            'Latitude' => $request->latitude,
            'gua_nic' => $request->gua_nic,
            'occu_job_position' => $request->occu_job_position,
            'occu_monthly_salary' => $request->occu_monthly_salary,
            'occu_address_01' => $request->occu_address_01,
            'occu_address_02' => $request->occu_address_02,
            'occu_address_03' => $request->occu_address_03,
            'occu_contact_no' => $request->occu_contact_no,
            'occu_longitude' => $request->occu_longitude,
            'occu_latitude' => $request->occu_latitude,
            'route_id' => $request->root,
        ];

        if ($documentPath) {
            $data['Cus_phto'] = $documentPath;
        }

// Get old customer data for comparison
    $oldCustomer = DB::table('customer')->where('idCustomer', $request->id)->first();
    
    // Store customer update data for approval
    $requestData = [
        'customer_id' => $request->id,
        'old_data' => (array)$oldCustomer,
        'new_data' => $data,
        'photo_path' => $documentPath,
    ];

    // Create approval request
    DB::table('approval_request')->insert([
        'type' => 'Customer Details Update',
        'typeid' => 302,
        'description' => 'Customer Update: ' . $request->f_name . ' ' . $request->last_name . ' (NIC: ' . $request->nic . ')',
        'data' => json_encode($requestData),
        'userid' => session('userid'),
        'branch_id' => session('branch_id'),
        'data_time' => now(),
        'status' => 0
    ]);

    return response()->json(['message' => 'Customer update request sent for approval!'], 200);

    // OLD CODE - keeping for approval handler reference
    /*
    updateWithBranch('customer', 'idCustomer', $request->id, $data);
    customer_number($request->id);
    $request = new Request([
        'customer_id' =>  $request->id,
        'description' => 'Customer Update',
        'description_id' =>  $request->id,
        'comment' => ' ',
        'type' => 'Customer Update',
    ]);
    $this->customerLogController->store($request);
    return response()->json(['message' => 'Customer updated successfully'], 200);
    */
    }

    public function updateCustomerLocation(Request $request) {
        try {
            // Validate the request
            $request->validate([
                'customer_id' => 'required|integer',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);

            // Update only latitude and longitude
            $data = [
                'Latitude' => $request->latitude,
                'Longitude' => $request->longitude,
            ];

            // Use the same helper function as the main update method
            updateWithBranch('customer', 'idCustomer', $request->customer_id, $data);

            return response()->json(['message' => 'Location updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update location: ' . $e->getMessage()], 500);
        }
    }

    public function load(){
        $center= tableWithBranch('center')->get();
        $company= tableWithBranch('company')->first();
        $route= tableWithBranch('route')->get();

        // Fetch the maximum customer ID
        $customer_max = tableWithBranch('customer')->max('idCustomer');

        // Increment the maximum ID by 1
        $customer_max = $customer_max + 1;

        // Format the ID with leading zeros (e.g., ##0 -> 001, 010, 100, etc.)
        // Adjust the length as needed (e.g., 3 means the format will be "001")
        $formatted_customer_id = str_pad($customer_max, 3, '0', STR_PAD_LEFT);
        return view('pages.Customer',compact('center','company','formatted_customer_id','route'));
    }

    public function saveFiles(Request $request)
    {

        if (!$request->hasFile('documents')) {
            return response()->json(['success' => false, 'message' => 'No files were received.']);
        }


        // Define the directory where the file will be stored
        $directory = 'customer_documents';

// Check if the directory exists, create it if not
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        foreach ($request->file('documents') as $index => $file) {

            // Store the file on the public disk
            $storedFile = Storage::disk('public')->putFile($directory, $file);

            // Retrieve document name from the array sent via AJAX
            $documentName = $request->documentNames[$index];

            // Store document information in the database
            $documentData = [
                'Description' => $documentName, // Store the unique file name
                'Path' => $storedFile, // Path relative to the storage directory
                'Customer_idCustomer' => $request->id, // Adjust this according to your needs
            ];

// Use the insertWithBranch helper function to insert the document data
            insertWithBranch('customer_documents', $documentData);

        }



        return response()->json(['success' => true]);

    }

    public function change_status(Request $request) {
        // Validate the request
        $validatedData = $request->validate([
            'id' => 'required', // Ensures id is present and valid
            'note' => '' // Note is required, a string, and max length of 255
        ]);

        $id = $validatedData['id'];
        $note = $validatedData['note'];

        // Retrieve the customer
        $customer = tableWithBranch('customer')->where('idCustomer', $id)->first();

        // Toggle the status
        $newStatus = $customer->Status == 1 ? 0 : 1;

        // Determine the action description
        $actionDescription = $newStatus == 1 ? 'Removed from Blacklist' : 'Added to Blacklist';

        // Determine the action type
        $type = $newStatus == 1 ? 'Remove Blacklist' : 'Blacklist';

        // Store blacklist change data for approval
        $requestData = [
            'customer_id' => $id,
            'customer_data' => (array)$customer,
            'old_status' => $customer->Status,
            'new_status' => $newStatus,
            'note' => $note,
            'action_type' => $type,
            'action_description' => $actionDescription,
        ];

        // Create approval request
        DB::table('approval_request')->insert([
            'type' => 'Customer Status Change',
            'typeid' => 304,
            'description' => $type . ': ' . $customer->First_Name . ' ' . $customer->Last_Name . ' (NIC: ' . $customer->Nic . ')',
            'data' => json_encode($requestData),
            'userid' => session('userid'),
            'branch_id' => session('branch_id'),
            'data_time' => now(),
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Status change request sent for approval!',
            'requiresApproval' => true
        ], 200);

        // OLD CODE - keeping for approval handler reference
        /*
        $updated = updateWithBranch('customer', 'idCustomer', $id, [
            'Status' => $newStatus,
            'Comment' => $note
        ]);

        $customer_table = tableWithBranch('customer')->where('idCustomer', $id)->first();
        $user_id = (int)session('userid');

        DB::table('customer_log')->insert([
            'customer_id' => $id,
            'customer_name' => $customer_table->First_Name.' '.$customer_table->Last_Name,
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
            'description' => $note ?? $actionDescription,
            'description_id' => $id,
            'comment' => ' ',
            'type' => $type,
            'user' => $user_id,
            'branch_id' => session('branch_id')
        ]);

        if ($updated) {
            return response()->json([
                'message' => 'Status updated successfully',
                'newStatus' => $newStatus
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to update status'
            ], 500);
        }
        */
    }

    public function load_customers(string $id){
        $customer = tableWithBranch('customer','customer')
            ->join('group_has_customer','customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->where('group_id', $id)
            ->where('customer.Status','=','1')
            ->get();
        return response()->json(['message' => 'Customers updated successfully','item' => $customer], 200);
    }

    public function load_individual_customer(){
        $customer = tableWithBranch('customer')->where('customer.Status','=','1')->get();
        return response()->json(['message' => 'Customers updated successfully','item' => $customer], 200);
    }

    public function deletecus(string $id){
        $group=tableWithBranch('group_has_customer')->where('cus_id', '=', $id)->get();
        $customer_loan=tableWithBranch('customer_loan')->where('Customer_idCustomer', '=', $id)->get();

        if ($group->isNotEmpty()) {
            return response()->json(['message' => 'Customer has group','item'=>'error'], 200);
        }else if($customer_loan->isNotEmpty()){
            return response()->json(['message' => 'Customer has loan','item'=>'error'], 200);
        }else{
            deleteWithBranch('customer','idCustomer', $id);
            return response()->json(['message' => 'Data deleted successfully','item'=>'success'], 200);
        }
    }

    public function customer_road_map(string $id){

        $customer = tableWithBranch('customer')
            ->where('idCustomer', '=', $id)
            ->first();

        if ($customer){
            $customer_name = $customer->First_Name.' '.$customer->Last_Name;

            $customer_log=tableWithBranch('customer_log','customer_log')
                ->join('user','customer_log.user', '=', 'user.id')
                ->where('customer_id','=',$id)
                ->get();

            return view('pages.CustomerRoadMap',compact('id','customer_name','customer_log'));
        }

        return redirect()->back();


    }


    public function saveBank(Request $request){

        // Retrieve the general ID
        $id = $request->input('id');


        // Retrieve the table data arrays
        $tableBankNames = $request->input('tableBankNames');
        $tableAccountNames = $request->input('tableAccountNames');
        $tableAccountNumbers = $request->input('tableAccountNumbers');
        $tableBranches = $request->input('tableBranches');
        $tableBankCodes = $request->input('tableBankCodes');



        // Example of saving the table data
        if (!empty($tableBankNames) && !empty($tableAccountNames) && !empty($tableAccountNumbers) && !empty($tableBranches)) {
            foreach ($tableBankNames as $index => $tableBankName) {
                $tableAccountName = $tableAccountNames[$index];
                $tableAccountNumber = $tableAccountNumbers[$index];
                $tableBranch = $tableBranches[$index];
                $tableBankCode = $tableBankCodes[$index] ?? null;


                // Prepare the bank data for insertion
                $documentData = [
                    'cus_id' => $id, // Customer ID
                    'bank_name' => $tableBankName, // Bank name
                    'account_name' => $tableAccountName, // Account name
                    'account_number' => $tableAccountNumber, // Account number
                    'branch' => $tableBranch, // Bank branch
                    'bank_code' => $tableBankCode, // Bank code
                ];

// Use the insertWithBranch helper function to insert the bank data
                insertWithBranch('customer_has_bank', $documentData);

            }
        }

        // Return a response
        return response()->json(['message' => 'Bank details saved successfully']);

    }


    public function load_bank(string $id){
        $customer_acc = tableWithBranch('customer_has_bank')
            ->where('cus_id', '=', $id)
            ->get();
        return response()->json(['message' => 'Bank details saved successfully','item'=>$customer_acc], 200);
    }


    public function saveBankSingle(Request $request){

        $id = $request->input('id');

        $tableBankNames = $request->input('bankName');
        $tableAccountNames = $request->input('accountName');
        $tableAccountNumbers = $request->input('accountNumber');
        $tableBranches = $request->input('branch');
        $tableBankCode = $request->input('bankCode');

        // Prepare the bank data for insertion
        $documentData = [
            'cus_id' => $id, // Customer ID
            'bank_name' => $tableBankNames, // Bank name
            'account_name' => $tableAccountNames, // Account name
            'account_number' => $tableAccountNumbers, // Account number
            'branch' => $tableBranches, // Bank branch
            'bank_code' => $tableBankCode, // Bank code
        ];

// Use the insertWithBranch helper function to insert the bank data
        insertWithBranch('customer_has_bank', $documentData);


        return response()->json(['message' => 'Bank details saved successfully']);
    }

    public function remove_bank(string $id){
        $customer_acc = tableWithBranch('customer_has_bank')
            ->where('id', '=', $id)
            ->delete();
        return response()->json(['message' => 'Bank details saved successfully'], 200);
    }

    public function get_account_transactions($id){
        $transactions = tableWithBranch('Savings_Account_Log','Savings_Account_Log')
            ->leftJoin('user', 'Savings_Account_Log.User', '=', 'user.id')
            ->where('Saving_Acount_Id', $id)
            ->select('Savings_Account_Log.Date_Time as date', 'Savings_Account_Log.Type as type',
                'Savings_Account_Log.Description as description', 'Savings_Account_Log.Credit as credit',
                'Savings_Account_Log.Debit as debit', 'Savings_Account_Log.Balance as balance',
                'user.Full_Name as user')
            ->orderBy('Savings_Account_Log.id', 'asc')
            ->get();

        return response()->json($transactions);
    }

    public function load_customer_route($id)
    {
        $customer = tableWithBranch('customer','customer')
            ->join('route','customer.route_id','=','route.id_route')
            ->where('idCustomer', $id)
            ->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $type = strtolower($customer->collection_type ?? '');
        $date = ($type === 'fixed') ? ($customer->collection_date ?? '') : '';

        return response()->json([
            'message'          => 'Customers loaded successfully',
            'collection_type'  => $customer->collection_type,
            'collection_date'  => $date, // '' when customizable
        ], 200);
    }


}
