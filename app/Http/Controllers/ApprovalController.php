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
        if ($branch_access == 0) {
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            $query->where('ar.branch_id', $selectedBranch);
        }
        
        // Apply type filtering
        if (!empty($selectedType)) {
            $query->where('ar.type', $selectedType);
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
        if ($branch_access == 0) {
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            $query->where('ar.branch_id', $selectedBranch);
        }
        
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
        if ($branch_access == 0) {
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            $query->where('ar.branch_id', $selectedBranch);
        }
        
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
