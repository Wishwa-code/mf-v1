<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{


    protected $customerLogController;

    public function __construct(CustomerLogController $customerLogController)
    {
        $this->customerLogController = $customerLogController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $center = tableWithBranch('center')
            ->get();
        $group = tableWithBranch('customer_group','customer_group')
            ->join('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->select('customer_group.*', 'center.Name as center_name')
            ->get();
        return view('pages.Group', compact('group','center'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {

        $customer = tableWithBranch('customer','customer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->where('customer_group.idCustomer_Group', '=', $id)->get();
        $group = tableWithBranch('customer_group')->where('idCustomer_Group', '=', $id)->first();
        $customercount = tableWithBranch('customer','customer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->where('customer_group.idCustomer_Group', '=', $id)->count();
        return view('pages.ViewCustomerGroup', compact('customer','group','customercount','id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'group_number' => 'required|string|max:255',
            'group_name' => 'required|string|max:255',
            'leader' => 'required|string|max:255',
            'center_id' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
        ]);

// Prepare the data for insertion
        $groupData = [
            'Group_No' => $validatedData['group_number'],
            'Name' => $validatedData['group_name'],
            'Leader_name' => $validatedData['leader'],
            'Contact_no' => $validatedData['contact'],
            'center_id' => $validatedData['center_id'],
        ];

// Check if the group already exists
        $check = tableWithBranch('customer_group', 'customer_group')
            ->where('Group_No', $validatedData['group_number'])
            ->where('center_id', $validatedData['center_id'])
            ->first();

        if ($check) {
            return response()->json(['message' => 'Group already exists', 'id' => '0'], 200);
        } else {
            // Insert the new group with branch ID
            $groupId = insertWithBranch('customer_group', $groupData);

            if ($groupId) {
                // Increment the Groups count in the center table
                DB::table('center')
                    ->where('idCenter', $validatedData['center_id'])
                    ->where('branch_id', session('branch_id'))
                    ->increment('Groups');

                return response()->json(['message' => 'Data saved successfully', 'id' => '1'], 200);
            } else {
                // If the data failed to save, return an error response
                return response()->json(['message' => 'Failed to save data', 'id' => '1'], 500);
            }
        }


    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = tableWithBranch('customer')->where('Customer_Group_idCustomer_Group','=',$id)->get();
        return response()->json(['item' => $customer], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $group = tableWithBranch('customer_group')
            ->where('center_id','=', $id)
            ->get();
        return response()->json(['item' => $group], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

// Prepare the data for the update
        $updateData = [
            'Group_No' => $request->group_number,
            'Name' => $request->group_name,
            'Leader_name' => $request->leader,
            'Contact_no' => $request->contact,
            'center_id' => $request->center_id,
        ];

// Update the group with branch ID scoping
        $updated = updateWithBranch('customer_group', 'idCustomer_Group', $request->group_id, $updateData);

        if ($updated) {
            return response()->json(['message' => 'Data updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to update data'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the group exists and has associated customers
        $group = tableWithBranch('customer_group','customer_group')
            ->join('group_has_customer', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->where('customer_group.idCustomer_Group', '=', $id)
            ->first();

        if ($group) {
            // Group has associated customers
            return response()->json(['message' => 'Group has associated customers. Cannot delete.', 'id' => '0'], 200);
        }

        $customer_group=tableWithBranch('customer_group')->where('idCustomer_Group', $id)->first();
        DB::table('center')
            ->where('idCenter', $customer_group->center_id)
            ->where('branch_id', session('branch_id'))
            ->decrement('Groups');
        // Proceed to delete the group
        DB::table('customer_group')->where('branch_id', session('branch_id'))->where('idCustomer_Group', '=', $id)->delete();


        return response()->json(['message' => 'Data deleted successfully', 'id' => '1'], 200);
    }


    public function assigngrouomember(Request $request){
        $id = $request->id;
        $customer = $request->customer;

        $assign_group = tableWithBranch('group_has_customer')
            ->where('cus_id', $customer)
            ->first();

        if ($assign_group) {
            return response()->json(['message' => 'Already in group', 'id' => '0'], 200);
        } else {
            insertWithBranch('group_has_customer', [
                'cus_id' => $customer,
                'group_id' => $id
            ]);


            $customer_group=tableWithBranch('customer_group')->where('idCustomer_Group', $id)->first();


            DB::table('center')
                ->where('idCenter', $customer_group->center_id)
                ->where('branch_id', session('branch_id'))
                ->increment('Members');


            $group=tableWithBranch('customer_group','customer_group')
                ->join('center', 'customer_group.center_id', '=', 'center.idCenter')
                ->select('customer_group.*', 'center.Name as center_name')
                ->where('idCustomer_Group', $id)->first();


            customer_number($customer);

            $request = new Request([
                'customer_id' => $customer,
                'description' => "Assign To This \nGroup : ({$group->Name})\nCenter Name : ({$group->center_name})",
                'description_id' => $id,
                'comment' => ' ',
                'type' => 'Assign To A Group',
            ]);

            // Call the store method of CustomerLogController
            $this->customerLogController->store($request);


            return response()->json(['message' => 'Customer assigned to group successfully', 'id' => '1'], 200);
        }





//        foreach ($customer as $item){






//            DB::table('center')
//                ->where('idCenter', $center)
//                ->increment('Members');
//        }



    }


    public function updateassigngrouomember(Request $request){
        $id=$request->id;
        $customer=$request->customer;
        $center=$request->center;

        DB::table('group_has_customer')
            ->where('group_id', $id)
            ->where('cus_id', $customer)
            ->where('branch_id', session('branch_id'))
            ->delete();

        DB::table('center')
            ->where('idCenter', $center)
            ->where('branch_id', session('branch_id'))
            ->decrement('Members');

        return response()->json(['message' => 'Customers updated successfully'], 200);
    }


    public function load_group(){
        $center = DB::table('center')
            ->where('branch_id', session('branch_id'))
            ->get();
        return view('pages.CreateGroup', compact('center'));
    }


    public function load_group_details(string $id){
        $group = DB::table('customer_group')
            ->where('center_id', '=', $id)
            ->where('branch_id', session('branch_id'))
            ->get();
        return response()->json(['message' => 'Customers updated successfully','item' => $group], 200);
    }


    public function assign_group(){

        $group = tableWithBranch('customer_group','customer_group')
            ->join('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->orderBy('center.No', 'asc')
            ->orderBy('customer_group.Group_No', 'asc')
            ->select('customer_group.*', 'center.Name as center_name','center.No as center_no')
            ->get();

        $customerIdsInGroup = tableWithBranch('customer','customer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->pluck('customer.idCustomer'); // Assuming the primary key is idCustomer

        // Get customers who are not in the specified group
        $customersNotInGroup = tableWithBranch('customer')
            ->get();



        return view('pages.CustomerGroup', compact('group','customersNotInGroup'));
    }

    public function getCustomerDetails(Request $request)
    {
        $idCustomerGroup = $request->idCustomerGroup;

        // Query to get customer details based on idCustomer_Group
        $customers = tableWithBranch('customer','customer')
            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->where('group_has_customer.group_id', $idCustomerGroup)
            ->select('customer.First_Name','customer.idCustomer', 'customer.Last_Name', 'customer.Contact_No', 'customer.Nic')
            ->get();

        return response()->json($customers);
    }

    public function removeCustomerFromGroup(Request $request)
    {
        $customerId = $request->customerId;
        $groupId = $request->groupId;

        DB::table('group_has_customer')
            ->where('cus_id', $customerId)
            ->where('group_id', $groupId)
            ->where('branch_id', session('branch_id'))
            ->delete();

        $customer_group = tableWithBranch('customer_group')->where('idCustomer_Group', $groupId)->first();

        DB::table('center')
            ->where('idCenter', $customer_group->center_id)
            ->where('branch_id', session('branch_id'))
            ->decrement('Members');

        $company = tableWithBranch('company')->first();
        $type = $company->customer_format ?? '';

        $customer = DB::table('customer')
            ->where('idCustomer', '=', $customerId)
            ->where('branch_id', '=', session('branch_id'))
            ->first();

        if ($customer) {
            $oldnum = $customer->cus_number;
            $newnum = $oldnum; // Keep the existing number as default

            $center = DB::table('center')
                ->where('idCenter', $customer_group->center_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            // Remove Center_No if it exists in the format
            if (strpos($type, '@Center_No@') !== false && $center) {
                $centerNo = $center->No; // Assuming 'No' is the center number column
                if (strpos($oldnum, $centerNo) !== false) {
                    $newnum = str_replace($centerNo, 'C000', $oldnum);
                }
            }

            // Remove Group_No if it exists in the format
            if (strpos($type, '@Group_No@') !== false && $customer_group) {
                $groupNo = $customer_group->Group_No; // Assuming 'Group_No' is the correct column
                if (strpos($newnum, $groupNo) !== false) {
                    $newnum = str_replace($groupNo, 'G000', $newnum);
                }
            }

            // Update only if the number was modified
            if ($newnum !== $oldnum) {
                updateWithBranch('customer', 'idCustomer', $customerId, [
                    'cus_number' => $newnum
                ]);
            }
        }

        $logRequest = new Request([
            'customer_id' => $customerId,
            'description' => 'Remove From Group',
            'description_id' => $groupId,
            'comment' => ' ',
            'type' => 'Remove From Group',
        ]);

        // Call the store method of CustomerLogController
        $this->customerLogController->store($logRequest);

        return response()->json(['message' => 'Customer removed from group successfully.']);
    }






}
