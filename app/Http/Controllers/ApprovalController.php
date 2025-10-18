<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ApprovalController extends Controller
{
    public function pending_approval(Request $request)
    {
        // Get branch access info
        $branch_access = session('branch_access', 0);
        $user_branch_id = session('branch_id');
        $selectedBranch = $request->get('branch_id', '');
        $selectedType = $request->get('type', '');
        
        // Get branches for filter dropdown
        $branches = DB::table('branch')->where('status', 1)->get();
        
        // Types list
        $types = [
            'User Management' => [
                '101' => 'User Creation',
                '102' => 'User Details Update',
                '103' => 'User Privilege Change',
                '104' => 'User Designation Change'
            ],
            'Designation Management' => [
                '201' => 'Designation Privileges Update'
            ],
            'Customer Management' => [
                '301' => 'Customer Creation',
                '302' => 'Customer Details Update',
                '303' => 'Customer Status Change',
                '304' => 'Customer Blacklist',
                '305' => 'Customer Document Update'
            ],
            'Loan Management' => [
                '401' => 'Loan Approval',
                '402' => 'Loan Rejection',
                '403' => 'Loan Modification'
            ],
            'Financial Transactions' => [
                '501' => 'Bank Account Transfer',
                '502' => 'Payment Undo',
                '503' => 'Payment Reversal'
            ],
            'Expenses' => [
                '601' => 'Expense Creation',
                '602' => 'Expense Approval',
                '603' => 'Expense Modification'
            ]
        ];
        
        // Build query for pending approvals with branch and user information
        $query = DB::table('approval_request as ar')
            ->leftJoin('branch as b', 'ar.branch_id', '=', 'b.branch_id')
            ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
            ->select(
                'ar.*',
                'b.Name as branch_name',
                'u.Full_Name as user_full_name'
            )
            ->where('ar.status', 0); // Pending status
            
        // Apply branch filtering
        // If not head office (-1), force filter to user's branch only
        if ($user_branch_id != -1) {
            // Non-head office users can only see their own branch data
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            // Head office can filter by selected branch
            $query->where('ar.branch_id', $selectedBranch);
        }
        // If head office and no branch selected, show all branches
        
        // Apply type filtering
        if (!empty($selectedType)) {
            $query->where('ar.typeid', $selectedType);
        }
        
        $pendingApprovals = $query->orderBy('ar.data_time', 'desc')->get();

        return view('pages.PendingApproval', compact('pendingApprovals', 'branches', 'branch_access', 'selectedBranch', 'selectedType', 'types'));
    }

    public function approved_history(Request $request)
    {
        // Get branch access info
        $branch_access = session('branch_access', 0);
        $user_branch_id = session('branch_id');
        $selectedBranch = $request->get('branch_id', '');
        $selectedType = $request->get('type', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        // Get branches for filter dropdown
        $branches = DB::table('branch')->where('status', 1)->get();
        
        // Types list
        $types = [
            'User Management' => [
                '101' => 'User Creation',
                '102' => 'User Details Update',
                '103' => 'User Privilege Change',
                '104' => 'User Designation Change'
            ],
            'Designation Management' => [
                '201' => 'Designation Privileges Update'
            ],
            'Customer Management' => [
                '301' => 'Customer Creation',
                '302' => 'Customer Details Update',
                '303' => 'Customer Status Change',
                '304' => 'Customer Blacklist',
                '305' => 'Customer Document Update'
            ],
            'Loan Management' => [
                '401' => 'Loan Approval',
                '402' => 'Loan Rejection',
                '403' => 'Loan Modification'
            ],
            'Financial Transactions' => [
                '501' => 'Bank Account Transfer',
                '502' => 'Payment Undo',
                '503' => 'Payment Reversal'
            ],
            'Expenses' => [
                '601' => 'Expense Creation',
                '602' => 'Expense Approval',
                '603' => 'Expense Modification'
            ]
        ];
        
        // Build query for approved requests
        $query = DB::table('approval_request as ar')
            ->leftJoin('branch as b', 'ar.branch_id', '=', 'b.branch_id')
            ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
            ->leftJoin('user as au', 'ar.approveduserid', '=', 'au.id')
            ->select(
                'ar.*',
                'b.Name as branch_name',
                'u.Full_Name as user_full_name',
                'au.Full_Name as approved_by_full_name'
            )
            ->where('ar.status', 1); // Approved status
            
        // Apply branch filtering
        // If not head office (-1), force filter to user's branch only
        if ($user_branch_id != -1) {
            // Non-head office users can only see their own branch data
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            // Head office can filter by selected branch
            $query->where('ar.branch_id', $selectedBranch);
        }
        // If head office and no branch selected, show all branches
        
        // Apply type filtering
        if (!empty($selectedType)) {
            $query->where('ar.type', $selectedType);
        }
        
        // Apply date filtering
        if (!empty($dateFrom)) {
            $query->whereDate('ar.approved_date_time', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('ar.approved_date_time', '<=', $dateTo);
        }
        
        $approvedHistory = $query->orderBy('ar.approved_date_time', 'desc')->get();

        return view('pages.ApprovedHistory', compact('approvedHistory', 'branches', 'branch_access', 'selectedBranch', 'selectedType', 'dateFrom', 'dateTo', 'types'));
    }

    public function rejected_approval(Request $request)
    {
        // Get branch access info
        $branch_access = session('branch_access', 0);
        $user_branch_id = session('branch_id');
        $selectedBranch = $request->get('branch_id', '');
        $selectedType = $request->get('type', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        // Get branches for filter dropdown
        $branches = DB::table('branch')->where('status', 1)->get();
        
        // Types list
        $types = [
            'User Management' => [
                '101' => 'User Creation',
                '102' => 'User Details Update',
                '103' => 'User Privilege Change',
                '104' => 'User Designation Change'
            ],
            'Designation Management' => [
                '201' => 'Designation Privileges Update'
            ],
            'Customer Management' => [
                '301' => 'Customer Creation',
                '302' => 'Customer Details Update',
                '303' => 'Customer Status Change',
                '304' => 'Customer Blacklist',
                '305' => 'Customer Document Update'
            ],
            'Loan Management' => [
                '401' => 'Loan Approval',
                '402' => 'Loan Rejection',
                '403' => 'Loan Modification'
            ],
            'Financial Transactions' => [
                '501' => 'Bank Account Transfer',
                '502' => 'Payment Undo',
                '503' => 'Payment Reversal'
            ],
            'Expenses' => [
                '601' => 'Expense Creation',
                '602' => 'Expense Approval',
                '603' => 'Expense Modification'
            ]
        ];
        
        // Build query for rejected requests
        $query = DB::table('approval_request as ar')
            ->leftJoin('branch as b', 'ar.branch_id', '=', 'b.branch_id')
            ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
            ->leftJoin('user as au', 'ar.approveduserid', '=', 'au.id')
            ->select(
                'ar.*',
                'b.Name as branch_name',
                'u.Full_Name as user_full_name',
                'au.Full_Name as rejected_by_full_name'
            )
            ->where('ar.status', 2); // Rejected status
            
        // Apply branch filtering
        // If not head office (-1), force filter to user's branch only
        if ($user_branch_id != -1) {
            // Non-head office users can only see their own branch data
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            // Head office can filter by selected branch
            $query->where('ar.branch_id', $selectedBranch);
        }
        // If head office and no branch selected, show all branches
        
        // Apply type filtering
        if (!empty($selectedType)) {
            $query->where('ar.type', $selectedType);
        }
        
        // Apply date filtering
        if (!empty($dateFrom)) {
            $query->whereDate('ar.approved_date_time', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('ar.approved_date_time', '<=', $dateTo);
        }
        
        $rejectedApprovals = $query->orderBy('ar.approved_date_time', 'desc')->get();

        return view('pages.RejectedApproval', compact('rejectedApprovals', 'branches', 'branch_access', 'selectedBranch', 'selectedType', 'dateFrom', 'dateTo', 'types'));
    }

    public function approve(Request $request)
    {
        $id = $request->input('id');
        $comment = $request->input('comment');
        
        try {
            // Get approval request
            $approval = DB::table('approval_request')->where('id', $id)->first();
            
            // Handle User Creation (Type 101)
            if ($approval->typeid == 101) {
                $requestData = json_decode($approval->data, true);
                $userData = $requestData['user_data'];
                
                // Create user
                $user = User::create($userData);
                
                // Add branches
                if (!empty($requestData['branches'])) {
                    foreach ($requestData['branches'] as $branch_id) {
                        DB::table('user_has_branches')->insert([
                            'user_id' => $user->id,
                            'branch_id' => $branch_id
                        ]);
                    }
                }
                
                // Apply privileges - Find designation from user's branch
                $designation_branch_id = $userData['branch_id'];
                $designation = DB::table('designation')
                    ->where('branch_id', $designation_branch_id)
                    ->where(function($query) use ($userData) {
                        $query->where('name', $userData['Designation'])
                              ->orWhere('idDesignation', $userData['Designation']);
                    })
                    ->first();
                
                if ($designation && $designation->privileges) {
                    $privileges = json_decode($designation->privileges, true);
                    if (is_array($privileges)) {
                        foreach ($privileges as $permissionKey => $value) {
                            DB::table('user_privileges_has_user')->updateOrInsert(
                                ['user_id' => $user->id, 'permission_key' => $permissionKey],
                                ['value' => $value]
                            );
                        }
                    }
                }
                
                // Create bank account
                $Bank = [
                    'Bank_Type' => "Collector",
                    'code' => $user->id.'/Collector',
                    'Bank_Name' => "Collector",
                    'Account_Name' => $userData['Full_Name'],
                    'Account_No' => $user->id,
                    'Bank_Branch' => '-',
                    'Account_Balance' => "0.00",
                    'type' => "Cash and Bank",
                    'cashflow' => "Non Applicable",
                    'User' => $user->id,
                    'branch_id' => $userData['branch_access'],
                ];
                
                $insertedId = insertWithBranch('company_bank_accounts', $Bank);
                
                $bankLogData = [
                    'Bank_Account_Id' => $insertedId,
                    'Date_Time' => date('Y-m-d H:i:s'),
                    'Type' => "Account Creation",
                    'Description' => "Collector Account",
                    'Note' => "",
                    'Credit' => "0.00",
                    'Debit' => "0.00",
                    'Balance' => "0.00",
                    'User' => $user->id,
                    'branch_id' => $userData['branch_access'],
                ];
                
                insertWithBranch('company_bank_has_log', $bankLogData);
            }
            
            // Handle User Details Update (Type 102)
            if ($approval->typeid == 102) {
                $requestData = json_decode($approval->data, true);
                $updateData = $requestData['update_data'];
                $newBranches = $requestData['new_branches'];
                $branchesChanged = $requestData['branches_changed'];
                
                $userId = $updateData['user_id'];
                
                // Update main user record
                DB::table('user')
                    ->where('id', $userId)
                    ->update([
                        'Epf_no' => $updateData['Epf_no'],
                        'Designation' => $updateData['Designation'],
                        'Nic' => $updateData['Nic'],
                        'Full_Name' => $updateData['Full_Name'],
                        'TP' => $updateData['TP'],
                        'lending_officer' => $updateData['lending_officer'],
                        'collector' => $updateData['collector'],
                        'branch_id' => $updateData['branch_id'],
                        'branch_access' => $updateData['branch_access'],
                        'cashier' => $updateData['cashier'],
                    ]);
                
                // Sync branches if changed
                if ($branchesChanged) {
                    DB::table('user_has_branches')->where('user_id', $userId)->delete();
                    foreach ($newBranches as $branch_id) {
                        DB::table('user_has_branches')->insert([
                            'user_id' => $userId,
                            'branch_id' => (int)$branch_id,
                        ]);
                    }
                }
            }
            
            // Handle User Privilege Change (Type 103)
            if ($approval->typeid == 103) {
                $requestData = json_decode($approval->data, true);
                $userId = $requestData['user_id'];
                $privileges = $requestData['privileges'];
                
                foreach ($privileges as $key => $value) {
                    DB::table('user_privileges_has_user')->updateOrInsert(
                        ['user_id' => $userId, 'permission_key' => $key],
                        ['value' => $value]
                    );
                    
                    // Update special fields in user table
                    if ($key == "payment_delete") {
                        DB::table('user')->where('id', $userId)->update([
                            'payment_delete' => $value
                        ]);
                    }
                    
                    if ($key == "branch_access") {
                        DB::table('user')->where('id', $userId)->update([
                            'branch_access' => $value
                        ]);
                    }
                    
                    if ($key == "collector_access") {
                        DB::table('user')->where('id', $userId)->update([
                            'collector' => $value
                        ]);
                    }
                    
                    if ($key == "cashier_access") {
                        DB::table('user')->where('id', $userId)->update([
                            'cashier' => $value
                        ]);
                    }
                }
            }
            
            // Handle Designation Privileges Update (Type 201)
            if ($approval->typeid == 201) {
                $requestData = json_decode($approval->data, true);
                $designationId = $requestData['designation_id'];
                
                // Check if this is a details update or privileges update
                if (isset($requestData['update_type']) && $requestData['update_type'] === 'details') {
                    // Update designation details (name, max amounts, etc.)
                    $newData = $requestData['new_data'];
                    DB::table('designation')
                        ->where('idDesignation', $designationId)
                        ->update([
                            'name' => $newData['name'],
                            'desi_level' => $newData['desi_level'],
                            'loan_creat' => $newData['loan_creat'],
                            'loan_issue' => $newData['loan_issue'],
                            'max_create_amount' => $newData['max_create_amount'],
                            'max_issue_amount' => $newData['max_issue_amount'],
                        ]);
                } else {
                    // Update designation privileges
                    $privileges = $requestData['privileges'];
                    DB::table('designation')
                        ->where('idDesignation', $designationId)
                        ->update([
                            'privileges' => json_encode($privileges)
                        ]);
                }
            }
            
            // Handle Loan Approval (Type 401)
            if ($approval->typeid == 401) {
                $requestData = json_decode($approval->data, true);
                $loan_id = $requestData['loan_id'];
                
                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', $approval->branch_id)
                    ->update(['Status' => '-1']);
            }
            
            // Handle Loan Rejection (Type 402)
            if ($approval->typeid == 402) {
                $requestData = json_decode($approval->data, true);
                $loan_id = $requestData['loan_id'];
                $reason = $requestData['reason'];
                $customer_id = $requestData['customer_id'];
                
                // Update loan status to rejected
                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', $approval->branch_id)
                    ->update(['Status' => '-2', 'reason' => $reason]);
                
                // Log to customer log
                $customer = DB::table('customer')
                    ->where('idCustomer', $customer_id)
                    ->where('branch_id', $approval->branch_id)
                    ->first();
                
                $logData = [
                    'customer_id' => $customer_id,
                    'customer_name' => ($customer->First_Name ?? '') . ' ' . ($customer->Last_Name ?? ''),
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'description' => "Delete Loan ({$loan_id})\nReason : {$reason}",
                    'description_id' => $loan_id,
                    'comment' => ' ',
                    'type' => 'Delete Loan',
                    'user' => session('userid'),
                ];
                
                insertWithBranch('customer_log', $logData);
            }
            
            // Update approval status
            DB::table('approval_request')
                ->where('id', $id)
                ->update([
                    'status' => 1, // Approved
                    'approveduserid' => session('userid'),
                    'approved_date_time' => now(),
                    'comment' => $comment
                ]);

            return response()->json(['success' => true, 'message' => 'Request approved successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error approving request: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request)
    {
        $id = $request->input('id');
        $reason = $request->input('reason');
        
        try {
            $approval = DB::table('approval_request')->where('id', $id)->first();
            
            // Handle Loan Approval Rejection (Type 401)
            if ($approval->typeid == 401) {
                $requestData = json_decode($approval->data, true);
                $loan_id = $requestData['loan_id'];
                
                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', $approval->branch_id)
                    ->update(['Status' => '-2']);
            }
            
            DB::table('approval_request')
                ->where('id', $id)
                ->update([
                    'status' => 2, // Rejected
                    'approveduserid' => session('userid'),
                    'approved_date_time' => now(),
                    'comment' => $reason
                ]);

            return response()->json(['success' => true, 'message' => 'Request rejected successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error rejecting request: ' . $e->getMessage()]);
        }
    }

    public function callback(Request $request)
    {
        $id = $request->input('id');
        $comment = $request->input('comment');
        
        try {
            DB::table('approval_request')
                ->where('id', $id)
                ->update([
                    'status' => -1, // Callback
                    'approveduserid' => session('userid'),
                    'approved_date_time' => now(),
                    'comment' => $comment
                ]);

            return response()->json(['success' => true, 'message' => 'Callback request sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error processing callback: ' . $e->getMessage()]);
        }
    }

    public function getLoanDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request')->where('id', $approvalId)->first();
            
            if (!$approval || $approval->typeid != 401) {
                return response()->json(['success' => false, 'message' => 'Loan approval request not found.']);
            }
            
            $requestData = json_decode($approval->data, true);
            $loan_id = $requestData['loan_id'];
            $branch_id = $approval->branch_id;
            
            $loan = DB::table('customer_loan')
                ->where('idCustomer_Loan', $loan_id)
                ->where('branch_id', $branch_id)
                ->first();
            
            if (!$loan) {
                return response()->json(['success' => false, 'message' => 'Loan not found.']);
            }
            
            $customer = DB::table('customer')->where('idCustomer', $loan->Customer_idCustomer)->where('branch_id', $branch_id)->first();
            $loanCategory = DB::table('loan_category')->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)->where('branch_id', $branch_id)->first();
            $user = DB::table('user')->where('id', $loan->User_idUser)->first();
            $lendingOfficer = DB::table('user')->where('id', $loan->lending_officer_id)->first();
            
            $installments = DB::table('installments')->where('Customer_Loan_idCustomer_Loan', $loan_id)->where('branch_id', $branch_id)->orderBy('idInstallments')->get();
            $witnesses = DB::table('witness')->where('Customer_Loan_idCustomer_Loan', $loan_id)->where('branch_id', $branch_id)->get();
            $otherCharges = DB::table('loan_other_charges')->where('Customer_Loan_idCustomer_Loan', $loan_id)->where('branch_id', $branch_id)->get();
            $approvalLevels = DB::table('loan_has_approval')->where('loan_id', $loan_id)->where('branch_id', $branch_id)->get();
            
            $html = view('partials.loan_details_modal', compact(
                'loan',
                'customer',
                'loanCategory',
                'user',
                'lendingOfficer',
                'installments',
                'witnesses',
                'otherCharges',
                'approvalLevels'
            ))->render();
            
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getDesignationDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request')->where('id', $approvalId)->first();
            
            if (!$approval || $approval->typeid != 201) {
                return response()->json(['success' => false, 'message' => 'Designation approval request not found.']);
            }
            
            $requestData = json_decode($approval->data, true);
            $designationId = $requestData['designation_id'];
            
            // Get current designation from database
            $currentDesignation = DB::table('designation')
                ->where('idDesignation', $designationId)
                ->first();
            
            $html = '';
            
            // Check if this is a details update or privileges update
            if (isset($requestData['update_type']) && $requestData['update_type'] === 'details') {
                // Details update - show old vs new comparison
                $oldData = $requestData['old_data'];
                $newData = $requestData['new_data'];
                
                $html = '
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i><strong>Designation Details Update Request</strong>
                </div>
                
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width: 33%;">Field</th>
                            <th style="width: 33%;" class="text-danger">Current Value</th>
                            <th style="width: 33%;" class="text-success">Requested Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Designation Name</strong></td>
                            <td class="text-danger">' . htmlspecialchars($oldData['name']) . '</td>
                            <td class="text-success">' . htmlspecialchars($newData['name']) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Max Create Amount</strong></td>
                            <td class="text-danger">' . number_format($oldData['max_create_amount'], 2) . '</td>
                            <td class="text-success">' . number_format($newData['max_create_amount'], 2) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Max Issue Amount</strong></td>
                            <td class="text-danger">' . number_format($oldData['max_issue_amount'], 2) . '</td>
                            <td class="text-success">' . number_format($newData['max_issue_amount'], 2) . '</td>
                        </tr>
                    </tbody>
                </table>
                ';
            } else {
                // Privileges update - show old vs new privileges comparison
                $oldPrivileges = $requestData['old_privileges'] ?? [];
                $newPrivileges = $requestData['privileges'] ?? [];
                
                // Get all unique keys
                $allKeys = array_unique(array_merge(array_keys($oldPrivileges), array_keys($newPrivileges)));
                sort($allKeys);
                
                $html = '
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i><strong>Designation: ' . htmlspecialchars($requestData['designation_name']) . ' - Privileges Update Request</strong>
                </div>
                
                <table class="table table-bordered table-sm">
                    <thead class="table-secondary">
                        <tr>
                            <th>Permission</th>
                            <th class="text-center text-danger">Current</th>
                            <th class="text-center text-success">Requested</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                $changesCount = 0;
                foreach ($allKeys as $key) {
                    $oldVal = $oldPrivileges[$key] ?? 0;
                    $newVal = $newPrivileges[$key] ?? 0;
                    
                    if ($oldVal != $newVal) {
                        $changesCount++;
                        $statusBadge = $newVal == 1 ? '<span class="badge bg-success">Enabled</span>' : '<span class="badge bg-danger">Disabled</span>';
                        $oldIcon = $oldVal == 1 ? '<i class="ri-checkbox-circle-fill text-success"></i>' : '<i class="ri-close-circle-fill text-danger"></i>';
                        $newIcon = $newVal == 1 ? '<i class="ri-checkbox-circle-fill text-success"></i>' : '<i class="ri-close-circle-fill text-danger"></i>';
                        
                        $html .= '
                        <tr>
                            <td>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</td>
                            <td class="text-center">' . $oldIcon . '</td>
                            <td class="text-center">' . $newIcon . '</td>
                            <td class="text-center">' . $statusBadge . '</td>
                        </tr>';
                    }
                }
                
                if ($changesCount == 0) {
                    $html .= '<tr><td colspan="4" class="text-center text-muted">No changes detected</td></tr>';
                }
                
                $html .= '
                    </tbody>
                </table>
                <div class="alert alert-secondary mt-3">
                    <strong>Total Changes:</strong> ' . $changesCount . ' permission(s)
                </div>';
            }
            
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getLoanRejectionDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();
            
            if (!$approval || $approval->typeid != 402) {
                return response()->json(['success' => false, 'message' => 'Loan rejection request not found.']);
            }
            
            $requestData = json_decode($approval->data, true);
            $loan_id = $requestData['loan_id'];
            $customer_name = $requestData['customer_name'];
            $loan_no = $requestData['loan_no'];
            $amount = $requestData['amount'];
            $category_name = $requestData['category_name'];
            $reason = $requestData['reason'];
            
            $html = '
            <div class="alert alert-danger">
                <i class="ri-alert-line me-2"></i><strong>Loan Deletion/Rejection Request</strong>
                <p class="mb-0 mt-2"><small>This action will permanently reject/delete the loan and update its status.</small></p>
            </div>
            
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 35%;">Customer Name</th>
                        <td><strong>' . htmlspecialchars($customer_name) . '</strong></td>
                    </tr>
                    <tr>
                        <th>Loan Number</th>
                        <td>' . htmlspecialchars($loan_no) . '</td>
                    </tr>
                    <tr>
                        <th>Loan Amount</th>
                        <td><strong class="text-primary">' . number_format($amount, 2) . '</strong></td>
                    </tr>
                    <tr>
                        <th>Product/Category</th>
                        <td>' . htmlspecialchars($category_name) . '</td>
                    </tr>
                    <tr>
                        <th>Rejection Reason</th>
                        <td>
                            <div class="alert alert-warning mb-0">
                                <i class="ri-information-line me-2"></i>' . nl2br(htmlspecialchars($reason)) . '
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Requested By</th>
                        <td>' . htmlspecialchars($approval->user_full_name ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Request Date</th>
                        <td>' . date('d/m/Y h:i A', strtotime($approval->data_time)) . '</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="alert alert-info mt-3">
                <i class="ri-information-line me-2"></i>
                <strong>Note:</strong> Approving this request will:
                <ul class="mb-0 mt-2">
                    <li>Set loan status to <strong>Rejected (-2)</strong></li>
                    <li>Create an entry in customer log</li>
                    <li>This action cannot be undone automatically</li>
                </ul>
            </div>
            ';
            
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getUserCreationDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();
            
            if (!$approval || $approval->typeid != 101) {
                return response()->json(['success' => false, 'message' => 'User creation request not found.']);
            }
            
            $requestData = json_decode($approval->data, true);
            $userData = $requestData['user_data'] ?? [];
            
            $html = '
            <div class="alert alert-info">
                <i class="ri-user-add-line me-2"></i><strong>New User Creation Request</strong>
            </div>
            
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 35%;">Field</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Full Name</th>
                        <td><strong>' . htmlspecialchars($userData['Full_Name'] ?? 'N/A') . '</strong></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>' . htmlspecialchars($userData['email'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Designation</th>
                        <td><span class="badge bg-primary">' . htmlspecialchars($userData['Designation'] ?? 'N/A') . '</span></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td>' . htmlspecialchars($userData['TP'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>NIC</th>
                        <td>' . htmlspecialchars($userData['Nic'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>EPF Number</th>
                        <td>' . htmlspecialchars($userData['Epf_no'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Branch Access</th>
                        <td>' . ($userData['branch_access'] == 1 ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>') . '</td>
                    </tr>
                    <tr>
                        <th>Requested By</th>
                        <td>' . htmlspecialchars($approval->user_full_name ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Request Date</th>
                        <td>' . date('d/m/Y h:i A', strtotime($approval->data_time)) . '</td>
                    </tr>
                </tbody>
            </table>
            ';
            
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getUserDetailsUpdateDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();
            
            if (!$approval || $approval->typeid != 102) {
                return response()->json(['success' => false, 'message' => 'User update request not found.']);
            }
            
            $requestData = json_decode($approval->data, true);
            $updateData = $requestData['update_data'] ?? [];
            
            // Get current user data for comparison
            $user = DB::table('user')->where('id', $updateData['user_id'])->first();
            
            $html = '
            <div class="alert alert-warning">
                <i class="ri-user-settings-line me-2"></i><strong>User Details Update Request</strong>
                <p class="mb-0 mt-2"><small>Review the changes before approving</small></p>
            </div>
            
            <h6 class="mb-3">User: <strong>' . htmlspecialchars($updateData['Full_Name'] ?? 'N/A') . '</strong></h6>
            
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Field</th>
                        <th style="width: 37.5%;">Current Value</th>
                        <th style="width: 37.5%;">New Value</th>
                    </tr>
                </thead>
                <tbody>';
            
            $fields = [
                'Full_Name' => 'Full Name',
                'email' => 'Email',
                'TP' => 'Contact',
                'Nic' => 'NIC',
                'Epf_no' => 'EPF Number',
                'Designation' => 'Designation',
            ];
            
            foreach ($fields as $key => $label) {
                if (isset($updateData[$key])) {
                    $oldVal = $user->$key ?? 'N/A';
                    $newVal = $updateData[$key] ?? 'N/A';
                    
                    if ($oldVal != $newVal) {
                        $html .= '
                        <tr>
                            <th>' . htmlspecialchars($label) . '</th>
                            <td>' . htmlspecialchars($oldVal) . '</td>
                            <td><strong class="text-primary">' . htmlspecialchars($newVal) . '</strong></td>
                        </tr>';
                    }
                }
            }
            
            // Check for branch changes
            if (isset($requestData['branches_changed']) && $requestData['branches_changed']) {
                $html .= '
                <tr>
                    <th>Branches</th>
                    <td colspan="2"><span class="badge bg-info">Branch assignments will be updated</span></td>
                </tr>';
            }
            
            $html .= '
                </tbody>
            </table>
            
            <div class="mt-3">
                <p class="text-muted mb-1"><strong>Requested By:</strong> ' . htmlspecialchars($approval->user_full_name ?? 'N/A') . '</p>
                <p class="text-muted mb-0"><strong>Request Date:</strong> ' . date('d/m/Y h:i A', strtotime($approval->data_time)) . '</p>
            </div>
            ';
            
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getUserPrivilegeChangeDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();
            
            if (!$approval || $approval->typeid != 103) {
                return response()->json(['success' => false, 'message' => 'Privilege change request not found.']);
            }
            
            $requestData = json_decode($approval->data, true);
            $userId = $requestData['user_id'];
            $newPrivileges = $requestData['privileges'] ?? [];
            
            // Get current user and their privileges
            $user = DB::table('user')->where('id', $userId)->first();
            $currentPrivileges = DB::table('user_privileges_has_user')
                ->where('user_id', $userId)
                ->pluck('value', 'permission_key')
                ->toArray();
            
            $html = '
            <div class="alert alert-warning">
                <i class="ri-shield-user-line me-2"></i><strong>User Privilege Change Request</strong>
            </div>
            
            <h6 class="mb-3">User: <strong>' . htmlspecialchars($user->Full_Name ?? 'N/A') . '</strong> (' . htmlspecialchars($requestData['user_email'] ?? '') . ')</h6>
            
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40%;">Permission</th>
                        <th style="width: 30%;">Current</th>
                        <th style="width: 30%;">New</th>
                    </tr>
                </thead>
                <tbody>';
            
            $changesCount = 0;
            // Privileges are stored as flat key-value pairs
            foreach ($newPrivileges as $key => $value) {
                if (empty($key)) continue;
                
                $newValue = (int)$value;
                $currentValue = (int)($currentPrivileges[$key] ?? 0);
                
                if ($currentValue != $newValue) {
                    $changesCount++;
                    $currentIcon = $currentValue == 1 ? '<i class="ri-checkbox-circle-fill text-success"></i> Enabled' : '<i class="ri-close-circle-fill text-danger"></i> Disabled';
                    $newIcon = $newValue == 1 ? '<i class="ri-checkbox-circle-fill text-success"></i> Enabled' : '<i class="ri-close-circle-fill text-danger"></i> Disabled';
                    
                    $html .= '
                    <tr>
                        <td>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</td>
                        <td>' . $currentIcon . '</td>
                        <td><strong>' . $newIcon . '</strong></td>
                    </tr>';
                }
            }
            
            if ($changesCount == 0) {
                $html .= '<tr><td colspan="3" class="text-center text-muted">No privilege changes detected</td></tr>';
            }
            
            $html .= '
                </tbody>
            </table>
            
            <div class="mt-3">
                <p class="text-muted mb-1"><strong>Requested By:</strong> ' . htmlspecialchars($approval->user_full_name ?? 'N/A') . '</p>
                <p class="text-muted mb-1"><strong>Request Date:</strong> ' . date('d/m/Y h:i A', strtotime($approval->data_time)) . '</p>
                <p class="text-muted mb-0"><strong>Total Changes:</strong> ' . $changesCount . ' permission(s)</p>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="ri-information-line me-2"></i>
                <strong>Note:</strong> Approving this will update the user\'s access privileges.
            </div>
            ';
            
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function undoRejection($id)
    {
        try {
            $approval = DB::table('approval_request')->where('id', $id)->first();

            if (!$approval) {
                return response()->json(['success' => false, 'message' => 'Approval request not found.']);
            }

            if ($approval->status != 2) {
                return response()->json(['success' => false, 'message' => 'This request is not rejected.']);
            }

            // Update approval_request back to pending
            DB::table('approval_request')
                ->where('id', $id)
                ->update([
                    'status' => 0,
                    'approved_date_time' => null,
                    'approveduserid' => null,
                    'approved_reject_comment' => null
                ]);

            // If Type 401 (Loan Approval), update loan status back to -3
            if ($approval->typeid == 401) {
                $requestData = json_decode($approval->data, true);
                $loan_id = $requestData['loan_id'];

                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', $approval->branch_id)
                    ->update(['Status' => '-3']);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Rejection undone successfully. Request moved back to pending approval.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
