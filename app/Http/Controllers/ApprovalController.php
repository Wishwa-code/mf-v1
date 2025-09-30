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
        
        // Get branches for filter dropdown (exclude branch_id = -1)
        $branches = DB::table('branch')->where('status', 1)->where('branch_id', '!=', -1)->get();
        
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
        
        // Get branches for filter dropdown (exclude branch_id = -1)
        $branches = DB::table('branch')->where('status', 1)->where('branch_id', '!=', -1)->get();
        
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
        
        // Get branches for filter dropdown (exclude branch_id = -1)
        $branches = DB::table('branch')->where('status', 1)->where('branch_id', '!=', -1)->get();
        
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
            if ($approval->type == '101') {
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
            if ($approval->type == '102') {
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
}
