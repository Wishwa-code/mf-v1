<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ApprovalController extends Controller
{
    public function pending_approval(Request $request)
    {
        // Get branch access info
        $branch_access   = session('branch_access', 0);
        $user_branch_id  = session('branch_id');
        $selectedBranch  = $request->get('branch_id', '');
        $selectedType    = $request->get('type', '');

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
            ->where('ar.status', 0); // Pending

        // Branch filtering
        if ($user_branch_id != -1) {
            // Non-head office: only own branch
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            // Head office: filter by selected branch
            $query->where('ar.branch_id', $selectedBranch);
        }

        // Type filtering
        if (!empty($selectedType)) {
            $query->where('ar.typeid', $selectedType);
        }

        $pendingApprovals = $query->orderBy('ar.data_time', 'desc')->get();

        return view('pages.PendingApproval', compact(
            'pendingApprovals',
            'branches',
            'branch_access',
            'selectedBranch',
            'selectedType',
            'types'
        ));
    }

    public function approved_history(Request $request)
    {
        $branch_access   = session('branch_access', 0);
        $user_branch_id  = session('branch_id');
        $selectedBranch  = $request->get('branch_id', '');
        $selectedType    = $request->get('type', '');
        $dateFrom        = $request->get('date_from', '');
        $dateTo          = $request->get('date_to', '');

        $branches = DB::table('branch')->where('status', 1)->get();

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
            ->where('ar.status', 1); // Approved

        if ($user_branch_id != -1) {
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            $query->where('ar.branch_id', $selectedBranch);
        }

        if (!empty($selectedType)) {
            $query->where('ar.typeid', $selectedType);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('ar.approved_date_time', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('ar.approved_date_time', '<=', $dateTo);
        }

        $approvedHistory = $query->orderBy('ar.approved_date_time', 'desc')->get();

        return view('pages.ApprovedHistory', compact(
            'approvedHistory',
            'branches',
            'branch_access',
            'selectedBranch',
            'selectedType',
            'dateFrom',
            'dateTo',
            'types'
        ));
    }

    public function rejected_approval(Request $request)
    {
        $branch_access   = session('branch_access', 0);
        $user_branch_id  = session('branch_id');
        $selectedBranch  = $request->get('branch_id', '');
        $selectedType    = $request->get('type', '');
        $dateFrom        = $request->get('date_from', '');
        $dateTo          = $request->get('date_to', '');

        $branches = DB::table('branch')->where('status', 1)->get();

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
            ->where('ar.status', 2); // Rejected

        if ($user_branch_id != -1) {
            $query->where('ar.branch_id', $user_branch_id);
        } elseif (!empty($selectedBranch)) {
            $query->where('ar.branch_id', $selectedBranch);
        }

        if (!empty($selectedType)) {
            $query->where('ar.typeid', $selectedType);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('ar.approved_date_time', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('ar.approved_date_time', '<=', $dateTo);
        }

        $rejectedApprovals = $query->orderBy('ar.approved_date_time', 'desc')->get();

        return view('pages.RejectedApproval', compact(
            'rejectedApprovals',
            'branches',
            'branch_access',
            'selectedBranch',
            'selectedType',
            'dateFrom',
            'dateTo',
            'types'
        ));
    }

    public function approve(Request $request)
    {
        $id      = $request->input('id');
        $comment = trim($request->input('comment', ''));

        try {
            DB::beginTransaction();

            // Lock the approval row for safety
            $approval = DB::table('approval_request')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$approval) {
                throw new \Exception('Approval request not found.');
            }

            // -----------------------------------------------------------------
            // TYPE 101: USER CREATION
            // -----------------------------------------------------------------
            if ($approval->typeid == 101) {
                $requestData = json_decode($approval->data, true);
                $userData    = $requestData['user_data'];

                // Create user
                $user = User::create($userData);

                // Add branches
                if (!empty($requestData['branches'])) {
                    foreach ($requestData['branches'] as $branch_id) {
                        DB::table('user_has_branches')->insert([
                            'user_id'   => $user->id,
                            'branch_id' => (int)$branch_id,
                        ]);
                    }
                }

                // Apply privileges from designation (branch-wise)
                $designation_branch_id = $userData['branch_id'];
                $designation = DB::table('designation')
                    ->where('branch_id', $designation_branch_id)
                    ->where(function ($q) use ($userData) {
                        $q->where('name', $userData['Designation'])
                            ->orWhere('idDesignation', $userData['Designation']);
                    })
                    ->first();

                if ($designation && $designation->privileges) {
                    $privileges = json_decode($designation->privileges, true);
                    if (is_array($privileges)) {
                        foreach ($privileges as $permissionKey => $value) {
                            DB::table('user_privileges_has_user')->updateOrInsert(
                                ['user_id' => $user->id, 'permission_key' => $permissionKey],
                                ['value'   => $value]
                            );
                        }
                    }
                }

                // Create bank account
                $Bank = [
                    'Bank_Type'       => "Collector",
                    'code'            => $user->id . '/Collector',
                    'Bank_Name'       => "Collector",
                    'Account_Name'    => $userData['Full_Name'],
                    'Account_No'      => $user->id,
                    'Bank_Branch'     => '-',
                    'Account_Balance' => "0.00",
                    'type'            => "Cash and Bank",
                    'cashflow'        => "Non Applicable",
                    'User'            => $user->id,
                    'branch_id'       => $userData['branch_access'],
                ];

                $insertedId = insertWithBranch('company_bank_accounts', $Bank);

                $bankLogData = [
                    'Bank_Account_Id' => $insertedId,
                    'Date_Time'       => date('Y-m-d H:i:s'),
                    'Type'            => "Account Creation",
                    'Description'     => "Collector Account",
                    'Note'            => "",
                    'Credit'          => "0.00",
                    'Debit'           => "0.00",
                    'Balance'         => "0.00",
                    'User'            => $user->id,
                    'branch_id'       => $userData['branch_access'],
                ];

                insertWithBranch('company_bank_has_log', $bankLogData);
            }

            // -----------------------------------------------------------------
            // TYPE 102: USER DETAILS UPDATE / STATUS CHANGE
            // -----------------------------------------------------------------
            if ($approval->typeid == 102) {
                $requestData = json_decode($approval->data, true);

                // Status-only change
                if (isset($requestData['new_data']) && isset($requestData['new_data']['Status'])) {
                    $userId    = $requestData['user_id'];
                    $newStatus = $requestData['new_data']['Status'];

                    DB::table('user')
                        ->where('id', $userId)
                        ->update(['Status' => $newStatus]);
                } else {
                    // Full details update
                    $updateData      = $requestData['update_data'];
                    $newBranches     = $requestData['new_branches'];
                    $branchesChanged = $requestData['branches_changed'];

                    $userId = $updateData['user_id'];

                    DB::table('user')
                        ->where('id', $userId)
                        ->update([
                            'Epf_no'          => $updateData['Epf_no'],
                            'Designation'     => $updateData['Designation'],
                            'Nic'             => $updateData['Nic'],
                            'Full_Name'       => $updateData['Full_Name'],
                            'TP'              => $updateData['TP'],
                            'lending_officer' => $updateData['lending_officer'],
                            'collector'       => $updateData['collector'],
                            'branch_id'       => $updateData['branch_id'],
                            'branch_access'   => $updateData['branch_access'],
                            'cashier'         => $updateData['cashier'],
                        ]);

                    // 🔁 Update related bank account name
                    DB::table('company_bank_accounts')
                        ->where('Account_No', $userId)  // Account_No = user.id
                        ->update([
                            'Account_Name' => $updateData['Full_Name'],
                        ]);

                    if ($branchesChanged) {
                        DB::table('user_has_branches')
                            ->where('user_id', $userId)
                            ->delete();

                        foreach ($newBranches as $branch_id) {
                            DB::table('user_has_branches')->insert([
                                'user_id'   => $userId,
                                'branch_id' => (int)$branch_id,
                            ]);
                        }
                    }
                }
            }

            // -----------------------------------------------------------------
            // TYPE 103: USER PRIVILEGE CHANGE
            // -----------------------------------------------------------------
            if ($approval->typeid == 103) {
                $requestData = json_decode($approval->data, true);
                $userId      = $requestData['user_id'];
                $privileges  = $requestData['privileges'];

                foreach ($privileges as $key => $value) {
                    DB::table('user_privileges_has_user')->updateOrInsert(
                        ['user_id' => $userId, 'permission_key' => $key],
                        ['value'   => $value]
                    );

                    if ($key === "payment_delete") {
                        DB::table('user')->where('id', $userId)->update(['payment_delete' => $value]);
                    }
                    if ($key === "branch_access") {
                        DB::table('user')->where('id', $userId)->update(['branch_access' => $value]);
                    }
                    if ($key === "collector_access") {
                        DB::table('user')->where('id', $userId)->update(['collector' => $value]);
                    }
                    if ($key === "cashier_access") {
                        DB::table('user')->where('id', $userId)->update(['cashier' => $value]);
                    }
                }
            }

            // -----------------------------------------------------------------
            // TYPE 201: DESIGNATION DETAILS / PRIVILEGES UPDATE
            // -----------------------------------------------------------------
            if ($approval->typeid == 201) {
                $requestData   = json_decode($approval->data, true);
                $designationId = $requestData['designation_id'];

                if (isset($requestData['update_type']) && $requestData['update_type'] === 'details') {
                    $newData = $requestData['new_data'];
                    DB::table('designation')
                        ->where('idDesignation', $designationId)
                        ->update([
                            'name'              => $newData['name'],
                            'desi_level'        => $newData['desi_level'],
                            'loan_creat'        => $newData['loan_creat'],
                            'loan_issue'        => $newData['loan_issue'],
                            'max_create_amount' => $newData['max_create_amount'],
                            'max_issue_amount'  => $newData['max_issue_amount'],
                        ]);
                } else {
                    $privileges = $requestData['privileges'];
                    DB::table('designation')
                        ->where('idDesignation', $designationId)
                        ->update([
                            'privileges' => json_encode($privileges),
                        ]);
                }
            }

            // -----------------------------------------------------------------
            // TYPE 301: CUSTOMER CREATION
            // -----------------------------------------------------------------
            if ($approval->typeid == 301) {
                $requestData  = json_decode($approval->data, true);
                $customerData = $requestData['customer_data'];

                // Safety: branch_id from approval
                $branchId = $approval->branch_id;

                // Prevent duplicates at approval time
                $exists = DB::table('customer')
                    ->where('branch_id', $branchId)
                    ->where(function ($q) use ($customerData) {
                        if (!empty($customerData['Nic'])) {
                            $q->orWhere('Nic', $customerData['Nic']);
                        }
                        if (!empty($customerData['cus_number'])) {
                            $q->orWhere('cus_number', $customerData['cus_number']);
                        }
                        if (!empty($customerData['Contact_No'])) {
                            $q->orWhere('Contact_No', $customerData['Contact_No']);
                        }
                    })
                    ->exists();

                if ($exists) {
                    throw new \Exception("Customer already exists in this branch. Approval aborted.");
                }

                // Create customer via model
                $customer = new \App\Models\Customer();
                foreach ($customerData as $key => $value) {
                    $customer->$key = $value;
                }

                if (!$customer->save()) {
                    throw new \Exception("Failed to save customer record.");
                }

                $customerId = $customer->idCustomer ?? $customer->id;

                // Log BEFORE customer_number()
                DB::table('customer_log')->insert([
                    'customer_id'    => $customerId,
                    'customer_name'  => ($customerData['First_Name'] ?? '') . ' ' . ($customerData['Last_Name'] ?? ''),
                    'date'           => date('Y-m-d'),
                    'time'           => date('H:i:s'),
                    'description'    => 'Customer registration for ' . (($customerData['First_Name'] ?? '') . ' ' . ($customerData['Last_Name'] ?? '')),
                    'description_id' => $customerId,
                    'comment'        => ' ',
                    'type'           => 'Customer Registration',
                    'user'           => session('user_data')["idUser"],
                    'branch_id'      => $branchId,
                ]);

                // Generate customer number
                customer_number($customerId);

                // Optional: SMS logic (kept from your code)
                $sms_template = DB::table('sms_template')
                    ->where('type', 'customer_registration')
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->first();

                if ($sms_template) {
                    $customer_table = DB::table('customer')->where('idCustomer', $customerId)->first();
                    if ($customer_table) {
                        $placeholders = [
                            '@Member_No@'   => $customer_table->cus_number,
                            '@Member_Name@' => $customer_table->First_Name . ' ' . $customer_table->Last_Name,
                        ];

                        $sms_text = $sms_template->template;
                        foreach ($placeholders as $placeholder => $value) {
                            $sms_text = str_replace($placeholder, $value, $sms_text);
                        }

                        // SMS log call can be kept commented or used:
                        // $this->smsLogController->index($customerId, $sms_text, "Customer Registration");
                    }
                }
            }

            // -----------------------------------------------------------------
            // TYPE 302: CUSTOMER DETAILS UPDATE
            // -----------------------------------------------------------------
            if ($approval->typeid == 302) {
                $requestData = json_decode($approval->data, true);
                $customerId  = $requestData['customer_id'];
                $newData     = $requestData['new_data'];

                DB::table('customer')
                    ->where('idCustomer', $customerId)
                    ->where('branch_id', $approval->branch_id)
                    ->update($newData);

                customer_number($customerId);

                DB::table('customer_log')->insert([
                    'customer_id'    => $customerId,
                    'customer_name'  => ($newData['First_Name'] ?? '') . ' ' . ($newData['Last_Name'] ?? ''),
                    'date'           => date('Y-m-d'),
                    'time'           => date('H:i:s'),
                    'description'    => 'Customer Update',
                    'description_id' => $customerId,
                    'comment'        => ' ',
                    'type'           => 'Customer Update',
                    'user'           => session('user_data')["idUser"],
                    'branch_id'      => $approval->branch_id,
                ]);
            }

            // -----------------------------------------------------------------
            // TYPE 304: CUSTOMER STATUS CHANGE / BLACKLIST
            // -----------------------------------------------------------------
            if ($approval->typeid == 304) {
                $requestData     = json_decode($approval->data, true);
                $customerId      = $requestData['customer_id'];
                $newStatus       = $requestData['new_status'];
                $note            = $requestData['note'];
                $actionType      = $requestData['action_type'];
                $actionDesc      = $requestData['action_description'];

                DB::table('customer')
                    ->where('idCustomer', $customerId)
                    ->where('branch_id', $approval->branch_id)
                    ->update([
                        'Status'  => $newStatus,
                        'Comment' => $note,
                    ]);

                $customer = DB::table('customer')
                    ->where('idCustomer', $customerId)
                    ->where('branch_id', $approval->branch_id)
                    ->first();

                if ($customer) {
                    DB::table('customer_log')->insert([
                        'customer_id'    => $customerId,
                        'customer_name'  => $customer->First_Name . ' ' . $customer->Last_Name,
                        'date'           => date('Y-m-d'),
                        'time'           => date('H:i:s'),
                        'description'    => $note ?: $actionDesc,
                        'description_id' => $customerId,
                        'comment'        => ' ',
                        'type'           => $actionType,
                        'user'           => session('user_data')["idUser"],
                        'branch_id'      => $approval->branch_id,
                    ]);
                }
            }

            // -----------------------------------------------------------------
            // TYPE 305: CUSTOMER DOCUMENT UPLOAD / DELETE
            // -----------------------------------------------------------------
            if ($approval->typeid == 305) {
                $requestData = json_decode($approval->data, true);

                // Delete
                if (isset($requestData['document_id'])) {
                    $documentId = $requestData['document_id'];

                    DB::table('customer_documents')
                        ->where('idCustomer_Documents', $documentId)
                        ->where('branch_id', $approval->branch_id)
                        ->delete();

                    // Upload
                } elseif (isset($requestData['document_path'])) {
                    $customerId   = $requestData['customer_id'];
                    $description  = $requestData['description'];
                    $documentPath = $requestData['document_path'];

                    DB::table('customer_documents')->insert([
                        'Customer_idCustomer' => $customerId,
                        'Description'         => $description,
                        'Path'                => $documentPath,
                        'branch_id'           => $approval->branch_id,
                    ]);
                }
            }

            // -----------------------------------------------------------------
            // TYPE 401: LOAN APPROVAL (HO)  (Status: -3 -> -1)
            // -----------------------------------------------------------------
            if ($approval->typeid == 401) {
                $requestData = json_decode($approval->data, true);
                $loan_id     = $requestData['loan_id'];

                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', $approval->branch_id)
                    ->update(['Status' => '-1']);
            }

            // -----------------------------------------------------------------
            // TYPE 402: LOAN REJECTION
            // -----------------------------------------------------------------
            if ($approval->typeid == 402) {
                $requestData = json_decode($approval->data, true);
                $loan_id     = $requestData['loan_id'];
                $reason      = $requestData['reason'];
                $customer_id = $requestData['customer_id'];

                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', $approval->branch_id)
                    ->update(['Status' => '-2', 'reason' => $reason]);

                $customer = DB::table('customer')
                    ->where('idCustomer', $customer_id)
                    ->where('branch_id', $approval->branch_id)
                    ->first();

                $logData = [
                    'customer_id'    => $customer_id,
                    'customer_name'  => ($customer->First_Name ?? '') . ' ' . ($customer->Last_Name ?? ''),
                    'date'           => date('Y-m-d'),
                    'time'           => date('H:i:s'),
                    'description'    => "Delete Loan ({$loan_id})\nReason : {$reason}",
                    'description_id' => $loan_id,
                    'comment'        => ' ',
                    'type'           => 'Delete Loan',
                    'user'           => session('user_data')["idUser"],
                ];

                insertWithBranch('customer_log', $logData);
            }

            // -----------------------------------------------------------------
            // TYPE 403: LOAN INSTALLMENT MODIFICATION
            // -----------------------------------------------------------------
            if ($approval->typeid == 403) {
                $requestData  = json_decode($approval->data, true);
                $loanId       = $requestData['loan_id'];
                $installments = $requestData['installments'];

                foreach ($installments as $installment) {
                    DB::table('installments')
                        ->where('Customer_Loan_idCustomer_Loan', $loanId)
                        ->where('No', $installment['no'])
                        ->where('branch_id', $approval->branch_id)
                        ->update([
                            'Installment_Date' => $installment['installment_date'],
                            'Panelty_date'     => $installment['penalty_date'],
                        ]);
                }
            }

            // -----------------------------------------------------------------
            // TYPE 603: EXPENSE DELETE
            // -----------------------------------------------------------------
            if ($approval->typeid == 603) {
                $requestData = json_decode($approval->data, true);
                $expenseId   = $requestData['expense_id'];
                $expenseData = $requestData['expense_data'];
                $bankIdData  = $requestData['bank_id_data'];

                $last_expenses = DB::table('expences')
                    ->where('id', $expenseId)
                    ->where('branch_id', $approval->branch_id)
                    ->first();

                if ($last_expenses) {
                    $bank_id = DB::table('company_bank_accounts')
                        ->where('acc_type_group', 'Expenses')
                        ->where('Idbank', $last_expenses->category_id)
                        ->first();

                    if ($bank_id) {
                        $reason = 'Delete Expense : (' . $last_expenses->reason . ')';

                        DB::table('bank_log')->insert([
                            'bank_id'       => $last_expenses->bank_id,
                            'type'          => 'Expenses',
                            'reason'        => $reason,
                            'cheque_no'     => '-',
                            'date_time'     => now(),
                            'debit_credit'  => 'debit',
                            'amount'        => $last_expenses->amount,
                            'other_bank_id' => $bank_id->Idbank,
                            'user_id'       => session('user_data')["idUser"],
                            'branch_id'     => $approval->branch_id,
                        ]);

                        DB::table('bank_log')->insert([
                            'bank_id'       => $bank_id->Idbank,
                            'type'          => 'Expenses',
                            'reason'        => $reason,
                            'cheque_no'     => '-',
                            'date_time'     => now(),
                            'debit_credit'  => 'credit',
                            'amount'        => $last_expenses->amount,
                            'other_bank_id' => $last_expenses->bank_id,
                            'user_id'       => session('user_data')["idUser"],
                            'branch_id'     => $approval->branch_id,
                        ]);
                    }

                    DB::table('expences')
                        ->where('id', $expenseId)
                        ->where('branch_id', $approval->branch_id)
                        ->delete();
                }
            }

            // -----------------------------------------------------------------
            // FINAL: UPDATE APPROVAL REQUEST AS APPROVED
            // -----------------------------------------------------------------
            DB::table('approval_request')
                ->where('id', $id)
                ->update([
                    'status'             => 1,
                    'approveduserid'     => session('user_data')["idUser"],
                    'approved_date_time' => now(),
                    'comment'            => $comment,
                ]);

            // -----------------------------------------------------------------
            // NEW: BRANCH NOTIFICATION FOR APPROVED REQUEST
            // -----------------------------------------------------------------
            // Only create notification if approval has a valid branch
            if (!is_null($approval->branch_id)) {
                $typeText = $approval->type ? $approval->type : ('Type ' . $approval->typeid);

                DB::table('approval_notifications')->insert([
                    'approval_id' => $approval->id,
                    'branch_id'   => $approval->branch_id,
                    'type'        => $approval->type,
                    'typeid'      => $approval->typeid,
                    'status'      => 1, // 1 = Approved
                    'title'       => 'Approval Request Approved',
                    'message'     => 'Your request for ' . $typeText . ' has been approved.',
                    'is_read'     => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error approving request: ' . $e->getMessage(),
            ]);
        }
    }


    public function reject(Request $request)
    {
        $id     = $request->input('id');
        $reason = $request->input('reason');

        try {
            $approval = DB::table('approval_request')->where('id', $id)->first();

            if (!$approval) {
                return response()->json(['success' => false, 'message' => 'Approval request not found.']);
            }

            // Loan approval rejection (Type 401) -> set loan as rejected
            if ($approval->typeid == 401) {
                $requestData = json_decode($approval->data, true);
                $loan_id     = $requestData['loan_id'];

                DB::table('customer_loan')
                    ->where('idCustomer_Loan', $loan_id)
                    ->where('branch_id', $approval->branch_id)
                    ->update(['Status' => '-2']);
            }

            DB::table('approval_request')
                ->where('id', $id)
                ->update([
                    'status'             => 2,
                    'approveduserid'     => session('user_data')["idUser"],
                    'approved_date_time' => now(),
                    'comment'            => $reason,
                ]);

            // -------------------------------------------------------------
            // NEW: BRANCH NOTIFICATION FOR REJECTED REQUEST
            // -------------------------------------------------------------
            if (!is_null($approval->branch_id)) {
                $typeText = $approval->type ? $approval->type : ('Type ' . $approval->typeid);

                DB::table('approval_notifications')->insert([
                    'approval_id' => $approval->id,
                    'branch_id'   => $approval->branch_id,
                    'type'        => $approval->type,
                    'typeid'      => $approval->typeid,
                    'status'      => 2, // 2 = Rejected
                    'title'       => 'Approval Request Rejected',
                    'message'     => 'Your request for ' . $typeText . ' has been rejected. Reason: ' . $reason,
                    'is_read'     => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting request: ' . $e->getMessage(),
            ]);
        }
    }



    public function callback(Request $request)
    {
        $id      = $request->input('id');
        $comment = $request->input('comment');

        try {
            $approval = DB::table('approval_request')->where('id', $id)->first();

            if (!$approval) {
                return response()->json(['success' => false, 'message' => 'Approval request not found.']);
            }

            DB::table('approval_request')
                ->where('id', $id)
                ->update([
                    'status'             => -1,
                    'approveduserid'     => session('user_data')["idUser"],
                    'approved_date_time' => now(),
                    'comment'            => $comment,
                ]);

            // -------------------------------------------------------------
            // NEW: BRANCH NOTIFICATION FOR CALLBACK
            // -------------------------------------------------------------
            if (!is_null($approval->branch_id)) {
                $typeText = $approval->type ? $approval->type : ('Type ' . $approval->typeid);

                DB::table('approval_notifications')->insert([
                    'approval_id' => $approval->id,
                    'branch_id'   => $approval->branch_id,
                    'type'        => $approval->type,
                    'typeid'      => $approval->typeid,
                    'status'      => 3, // 3 = Callback
                    'title'       => 'Approval Request Sent for Callback',
                    'message'     => 'Your request for ' . $typeText . ' was sent back with comments: ' . $comment,
                    'is_read'     => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Callback request sent successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing callback: ' . $e->getMessage(),
            ]);
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
            $witnesses = DB::table('witness')
                ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                ->where('branch_id', $branch_id)
                ->get()
                ->map(function ($witness) use ($branch_id) {
                    $displayType = $witness->type ?? 'Witness';

                    if (($witness->type === 'Guarantor' || $witness->type === 'Guardian') && $witness->cus_id) {
                        $guardian = DB::table('guardian')
                            ->where('idGuardian', $witness->cus_id)
                            ->where('branch_id', $branch_id)
                            ->first();

                        if ($guardian) {
                            $witness->Name = $this->buildPersonName($guardian->First_Name ?? null, $guardian->Last_Name ?? null) ?: ($witness->Name ?? null);
                            $witness->Nic = $guardian->Nic ?? $guardian->nic ?? ($witness->Nic ?? $witness->NIC ?? null);
                            $witness->Mobile = $guardian->Contact_No ?? $guardian->Mobile ?? ($witness->Mobile ?? null);
                            $witness->Address = $this->buildAddress([
                                $guardian->Address ?? null,
                                $guardian->Address_2 ?? $guardian->Address_02 ?? null,
                                $guardian->Address_3 ?? $guardian->Address_03 ?? null,
                            ]) ?? ($witness->Address ?? null);
                        }
                    } elseif ($witness->cus_id) {
                        $customer = DB::table('customer')
                            ->where('idCustomer', $witness->cus_id)
                            ->where('branch_id', $branch_id)
                            ->first();

                        if ($customer) {
                            $displayType = $witness->type === 'Customer' ? 'Cross Customer' : ($witness->type ?? 'Witness');
                            $witness->Name = $this->buildPersonName($customer->First_Name ?? null, $customer->Last_Name ?? null) ?: ($witness->Name ?? null);
                            $witness->Nic = $customer->Nic ?? ($witness->Nic ?? $witness->NIC ?? null);
                            $witness->Mobile = $customer->Contact_No ?? $customer->Mobile_No ?? $customer->Mobile ?? ($witness->Mobile ?? null);
                            $witness->Address = $this->buildAddress([
                                $customer->Address ?? null,
                                $customer->Address_02 ?? null,
                                $customer->Address_03 ?? null,
                            ]) ?? ($witness->Address ?? null);
                        }
                    }

                    foreach (['Name', 'Nic', 'Mobile', 'Address'] as $field) {
                        $value = $witness->$field ?? null;
                        if (is_string($value)) {
                            $value = trim($value);
                        }
                        $witness->$field = $value !== '' ? $value : null;
                    }

                    if (!isset($witness->Nic) && isset($witness->NIC)) {
                        $witness->Nic = $witness->NIC;
                    }

                    $witness->display_type = $displayType === 'Customer' ? 'Cross Customer' : $displayType;

                    return $witness;
                })
                ->values();
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

            // Check if this is a status-only change or full update
            $isStatusChange = isset($requestData['new_data']) && isset($requestData['new_data']['Status']);

            if ($isStatusChange) {
                // Status-only change (active/inactive toggle)
                $userId = $requestData['user_id'];
                $oldData = $requestData['old_data'];
                $newStatus = $requestData['new_data']['Status'];
                $actionType = $requestData['action_type'] ?? 'Status Change';

                $user = DB::table('user')->where('id', $userId)->first();
                $userName = $user ? $user->Full_Name : ($oldData['Full_Name'] ?? 'Unknown');
                $statusText = $newStatus == '1' ? 'Active' : 'Inactive';
                $oldStatusText = ($oldData['Status'] ?? '0') == '1' ? 'Active' : 'Inactive';

                $html = '
                <div class="alert alert-info">
                    <i class="ri-user-settings-line me-2"></i><strong>' . htmlspecialchars($actionType) . '</strong>
                    <p class="mb-0 mt-2"><small>Review the status change before approving</small></p>
                </div>
                
                <h6 class="mb-3">User: <strong>' . htmlspecialchars($userName) . '</strong></h6>
                
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%;">Field</th>
                            <th style="width: 37.5%;">Current Value</th>
                            <th style="width: 37.5%;">New Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge ' . ($oldStatusText == 'Active' ? 'bg-success' : 'bg-danger') . '">' . $oldStatusText . '</span></td>
                            <td><strong><span class="badge ' . ($statusText == 'Active' ? 'bg-success' : 'bg-danger') . '">' . $statusText . '</span></strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="mt-3">
                    <p class="text-muted mb-1"><strong>Requested By:</strong> ' . htmlspecialchars($approval->user_full_name ?? 'N/A') . '</p>
                    <p class="text-muted mb-0"><strong>Request Date:</strong> ' . date('d/m/Y h:i A', strtotime($approval->data_time)) . '</p>
                </div>
                ';

                return response()->json(['success' => true, 'html' => $html]);
            }

            // Full user detail update
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

            // Add checkbox fields with checkmark icons
            $checkboxFields = [
                'lending_officer' => 'Lending Officer',
                'collector' => 'Collecting Officer',
                'cashier' => 'Cashier',
                'branch_access' => 'Branch Access',
            ];

            foreach ($checkboxFields as $key => $label) {
                if (isset($updateData[$key])) {
                    $oldVal = ($user->$key ?? 0) == 1;
                    $newVal = ($updateData[$key] ?? 0) == 1;

                    if ($oldVal != $newVal) {
                        $oldDisplay = $oldVal ? '<i class="ri-checkbox-circle-fill text-success"></i> Yes' : '<i class="ri-close-circle-fill text-danger"></i> No';
                        $newDisplay = $newVal ? '<i class="ri-checkbox-circle-fill text-success"></i> Yes' : '<i class="ri-close-circle-fill text-danger"></i> No';

                        $html .= '
                        <tr>
                            <th>' . htmlspecialchars($label) . '</th>
                            <td>' . $oldDisplay . '</td>
                            <td><strong class="text-primary">' . $newDisplay . '</strong></td>
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

    public function getCustomerCreationDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();

            if (!$approval || $approval->typeid != 301) {
                return response()->json(['success' => false, 'message' => 'Customer creation request not found.']);
            }

            $requestData = json_decode($approval->data, true);
            $customerData = $requestData['customer_data'] ?? [];

            $html = '
            <div class="alert alert-info">
                <i class="ri-user-add-line me-2"></i><strong>New Customer Creation Request</strong>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3"><i class="ri-user-line me-2"></i>Personal Information</h6>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <th style="width: 45%;">Full Name</th>
                                <td><strong>' . htmlspecialchars(($customerData['Title'] ?? '') . ' ' . ($customerData['First_Name'] ?? '') . ' ' . ($customerData['Last_Name'] ?? '')) . '</strong></td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td>' . htmlspecialchars($customerData['Nic'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td>' . htmlspecialchars($customerData['Gender'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Date of Birth</th>
                                <td>' . htmlspecialchars($customerData['Dob'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Civil Status</th>
                                <td>' . htmlspecialchars($customerData['civil_status'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Risk Level</th>
                                <td><span class="badge bg-warning">' . htmlspecialchars($customerData['Customer_Risk_Level'] ?? 'N/A') . '</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="mb-3"><i class="ri-contacts-line me-2"></i>Contact Information</h6>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <th style="width: 45%;">Email</th>
                                <td>' . htmlspecialchars($customerData['Email'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Contact No</th>
                                <td>' . htmlspecialchars($customerData['Contact_No'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Contact No 2</th>
                                <td>' . htmlspecialchars($customerData['contact_number_2'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Landline</th>
                                <td>' . htmlspecialchars($customerData['Landline'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Current Address</th>
                                <td>' . htmlspecialchars(($customerData['Address'] ?? '') . ' ' . ($customerData['Address_02'] ?? '') . ' ' . ($customerData['Address_03'] ?? '')) . '</td>
                            </tr>
                            <tr>
                                <th>City / State</th>
                                <td>' . htmlspecialchars(($customerData['City'] ?? '') . ' / ' . ($customerData['State'] ?? '')) . '</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6 class="mb-3"><i class="ri-briefcase-line me-2"></i>Occupation Information</h6>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <th style="width: 45%;">Job Position</th>
                                <td>' . htmlspecialchars($customerData['occu_job_position'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Monthly Salary</th>
                                <td><strong>' . htmlspecialchars($customerData['occu_monthly_salary'] ?? 'N/A') . '</strong></td>
                            </tr>
                            <tr>
                                <th>Office Address</th>
                                <td>' . htmlspecialchars(($customerData['occu_address_01'] ?? '') . ' ' . ($customerData['occu_address_02'] ?? '')) . '</td>
                            </tr>
                            <tr>
                                <th>Office Contact</th>
                                <td>' . htmlspecialchars($customerData['occu_contact_no'] ?? 'N/A') . '</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="mb-3"><i class="ri-shield-user-line me-2"></i>Guardian Information</h6>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <th style="width: 45%;">Guardian Name</th>
                                <td>' . htmlspecialchars(($customerData['Gua_title'] ?? '') . ' ' . ($customerData['Gua_name'] ?? 'N/A')) . '</td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td>' . htmlspecialchars($customerData['Gua_nic'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Relation</th>
                                <td>' . htmlspecialchars($customerData['Gua_relation'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Occupation</th>
                                <td>' . htmlspecialchars($customerData['Gua_occu'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Contact</th>
                                <td>' . htmlspecialchars($customerData['Gua_contact'] ?? 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>' . htmlspecialchars($customerData['Gua_address'] ?? 'N/A') . '</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
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

    public function getCustomerDetailsUpdateDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();

            if (!$approval || $approval->typeid != 302) {
                return response()->json(['success' => false, 'message' => 'Customer update request not found.']);
            }

            $requestData = json_decode($approval->data, true);
            $oldData = $requestData['old_data'] ?? [];
            $newData = $requestData['new_data'] ?? [];

            $html = '
            <div class="alert alert-warning">
                <i class="ri-user-settings-line me-2"></i><strong>Customer Details Update Request</strong>
                <p class="mb-0 mt-2"><small>Review the changes before approving</small></p>
            </div>
            
            <h6 class="mb-3">Customer: <strong>' . htmlspecialchars(($newData['First_Name'] ?? '') . ' ' . ($newData['Last_Name'] ?? '')) . '</strong></h6>
            
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
                'First_Name' => 'First Name',
                'Last_Name' => 'Last Name',
                'Email' => 'Email',
                'Contact_No' => 'Contact Number',
                'Nic' => 'NIC',
                'Gender' => 'Gender',
                'Dob' => 'Date of Birth',
                'Address' => 'Address Line 1',
                'City' => 'City',
                'State' => 'State',
                'Landline' => 'Landline',
                'Gua_name' => 'Guardian Name',
                'Gua_contact' => 'Guardian Contact',
                'occu_job_position' => 'Job Position',
                'occu_monthly_salary' => 'Monthly Salary',
                'Cus_phto' => 'Customer Photo',
            ];

            $changesCount = 0;
            foreach ($fields as $key => $label) {
                if (isset($newData[$key])) {
                    $oldVal = $oldData[$key] ?? 'N/A';
                    $newVal = $newData[$key] ?? 'N/A';

                    if ($oldVal != $newVal) {
                        $changesCount++;

                        // Special handling for photo field
                        if ($key === 'Cus_phto') {
                            $oldDisplay = '<span class="text-muted">No photo</span>';
                            if ($oldVal && $oldVal !== 'N/A') {
                                $oldPhotoUrl = '/storage/' . $oldVal;
                                $oldDisplay = '<img src="' . htmlspecialchars($oldPhotoUrl) . '" alt="Current Photo" style="max-width: 150px; max-height: 150px; border: 2px solid #ddd; border-radius: 5px; cursor: pointer;" onclick="window.open(this.src, \'_blank\')"><br><small class="text-muted">Click to enlarge</small>';
                            }

                            $newDisplay = '<span class="badge bg-success">📷 New photo</span>';
                            if ($newVal && $newVal !== 'N/A') {
                                $newPhotoUrl = '/storage/' . $newVal;
                                $newDisplay = '<img src="' . htmlspecialchars($newPhotoUrl) . '" alt="New Photo" style="max-width: 150px; max-height: 150px; border: 2px solid #28a745; border-radius: 5px; cursor: pointer;" onclick="window.open(this.src, \'_blank\')"><br><small class="text-success">📷 New photo - Click to enlarge</small>';
                            }

                            $html .= '
                            <tr>
                                <th>' . htmlspecialchars($label) . '</th>
                                <td style="padding: 10px;">' . $oldDisplay . '</td>
                                <td style="padding: 10px;">' . $newDisplay . '</td>
                            </tr>';
                        } else {
                            $html .= '
                            <tr>
                                <th>' . htmlspecialchars($label) . '</th>
                                <td>' . htmlspecialchars($oldVal) . '</td>
                                <td><strong class="text-primary">' . htmlspecialchars($newVal) . '</strong></td>
                            </tr>';
                        }
                    }
                }
            }

            if ($changesCount == 0) {
                $html .= '<tr><td colspan="3" class="text-center text-muted">No changes detected</td></tr>';
            }

            $html .= '
                </tbody>
            </table>
            
            <div class="mt-3">
                <p class="text-muted mb-1"><strong>Requested By:</strong> ' . htmlspecialchars($approval->user_full_name ?? 'N/A') . '</p>
                <p class="text-muted mb-1"><strong>Request Date:</strong> ' . date('d/m/Y h:i A', strtotime($approval->data_time)) . '</p>
                <p class="text-muted mb-0"><strong>Total Changes:</strong> ' . $changesCount . ' field(s)</p>
            </div>
            ';

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getExpenseDeleteDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();

            if (!$approval || $approval->typeid != 603) {
                return response()->json(['success' => false, 'message' => 'Expense delete request not found.']);
            }

            $requestData = json_decode($approval->data, true);
            $expenseData = $requestData['expense_data'] ?? [];
            $bankIdData = $requestData['bank_id_data'] ?? [];

            $html = '
            <div class="alert alert-danger">
                <i class="ri-delete-bin-line me-2"></i><strong>Expense Delete Request</strong>
                <p class="mb-0 mt-2"><small>Review the expense details before approving deletion</small></p>
            </div>
            
            <h6 class="mb-3">Expense Details</h6>
            
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 35%;">Category</th>
                        <td><strong>' . htmlspecialchars($bankIdData['Bank_Name'] ?? 'N/A') . '</strong></td>
                    </tr>
                    <tr>
                        <th>Reason/Description</th>
                        <td>' . htmlspecialchars($expenseData['reason'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td><strong class="text-danger">Rs. ' . number_format($expenseData['amount'] ?? 0, 2) . '</strong></td>
                    </tr>
                    <tr>
                        <th>Payment Type</th>
                        <td>' . htmlspecialchars($expenseData['payment_type'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>' . htmlspecialchars($expenseData['date'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Cheque Number</th>
                        <td>' . htmlspecialchars($expenseData['cheque_no'] ?? '-') . '</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="alert alert-warning mt-3">
                <strong>⚠️ Warning:</strong> Approving this request will permanently delete this expense entry and create reversal bank log entries.
            </div>
            
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

    public function getCustomerStatusChangeDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();

            if (!$approval || $approval->typeid != 304) {
                return response()->json(['success' => false, 'message' => 'Status change request not found.']);
            }

            $requestData = json_decode($approval->data, true);
            $customerData = $requestData['customer_data'] ?? [];
            $oldStatus = $requestData['old_status'];
            $newStatus = $requestData['new_status'];
            $note = $requestData['note'] ?? '';
            $actionType = $requestData['action_type'];
            $actionDescription = $requestData['action_description'];

            $oldStatusText = $oldStatus == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Blacklisted</span>';
            $newStatusText = $newStatus == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Blacklisted</span>';

            $alertClass = $newStatus == 0 ? 'alert-danger' : 'alert-success';
            $iconClass = $newStatus == 0 ? 'ri-user-unfollow-line' : 'ri-user-follow-line';

            $html = '
            <div class="alert ' . $alertClass . '">
                <i class="' . $iconClass . ' me-2"></i><strong>' . htmlspecialchars($actionType) . ' Request</strong>
                <p class="mb-0 mt-2"><small>Review customer status change before approving</small></p>
            </div>
            
            <h6 class="mb-3">Customer Information</h6>
            
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 35%;">Customer Name</th>
                        <td><strong>' . htmlspecialchars(($customerData['First_Name'] ?? '') . ' ' . ($customerData['Last_Name'] ?? '')) . '</strong></td>
                    </tr>
                    <tr>
                        <th>Customer Number</th>
                        <td>' . htmlspecialchars($customerData['cus_number'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>NIC</th>
                        <td>' . htmlspecialchars($customerData['Nic'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Contact Number</th>
                        <td>' . htmlspecialchars($customerData['Contact_No'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Current Status</th>
                        <td>' . $oldStatusText . '</td>
                    </tr>
                    <tr>
                        <th>New Status</th>
                        <td><strong>' . $newStatusText . '</strong></td>
                    </tr>
                    <tr>
                        <th>Reason/Note</th>
                        <td>' . htmlspecialchars($note) . '</td>
                    </tr>
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

    public function getCustomerDocumentDeleteDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();

            if (!$approval || $approval->typeid != 305) {
                return response()->json(['success' => false, 'message' => 'Document request not found.']);
            }

            $requestData = json_decode($approval->data, true);

            // Check if this is upload or delete
            $isUpload = isset($requestData['document_path']);
            $isDelete = isset($requestData['document_id']);

            if ($isDelete) {
                $documentData = $requestData['document_data'] ?? [];
                $customerId = $requestData['customer_id'] ?? null;
            } else {
                // For upload, create documentData from request
                $documentData = [
                    'Description' => $requestData['description'] ?? 'N/A',
                    'Path' => $requestData['document_path'] ?? 'N/A',
                ];
                $customerId = $requestData['customer_id'] ?? null;
            }

            // Get customer details
            $customer = DB::table('customer')
                ->where('idCustomer', $customerId)
                ->where('branch_id', $approval->branch_id)
                ->first();

            $customerName = $customer ? ($customer->First_Name . ' ' . $customer->Last_Name) : 'Unknown';
            $customerNumber = $customer->cus_number ?? 'N/A';

            $alertClass = $isDelete ? 'alert-danger' : 'alert-info';
            $iconClass = $isDelete ? 'ri-file-damage-line' : 'ri-file-upload-line';
            $title = $isDelete ? 'Customer Document Delete Request' : 'Customer Document Upload Request';
            $subtitle = $isDelete ? 'Review document details before approving deletion' : 'Review document details before approving upload';

            $html = '
            <div class="' . $alertClass . '">
                <i class="' . $iconClass . ' me-2"></i><strong>' . $title . '</strong>
                <p class="mb-0 mt-2"><small>' . $subtitle . '</small></p>
            </div>
            
            <h6 class="mb-3">Document Information</h6>
            
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 35%;">Customer</th>
                        <td><strong>' . htmlspecialchars($customerName) . '</strong> (' . htmlspecialchars($customerNumber) . ')</td>
                    </tr>
                    <tr>
                        <th>Document Description</th>
                        <td>' . htmlspecialchars($documentData['Description'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <th>Document Path</th>
                        <td><small class="text-muted">' . htmlspecialchars($documentData['Path'] ?? 'N/A') . '</small></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="mt-3">
                <h6 class="mb-2">Document Preview</h6>
                <div class="border rounded p-3 bg-light text-center">
                    ' . ($documentData['Path'] ? '
                        <a href="/storage/' . htmlspecialchars($documentData['Path']) . '" target="_blank" class="btn btn-primary btn-sm me-2">
                            <i class="ri-eye-line me-1"></i>View Document
                        </a>
                        <a href="/storage/' . htmlspecialchars($documentData['Path']) . '" download class="btn btn-outline-secondary btn-sm">
                            <i class="ri-download-line me-1"></i>Download
                        </a>
                    ' : '<p class="text-muted mb-0">No document available</p>') . '
                </div>
            </div>
            
            <div class="alert alert-warning mt-3">
                <strong>⚠️ Warning:</strong> Approving this request will ' . ($isDelete ? 'permanently delete this document from the system' : 'upload this document to the customer profile') . '.
            </div>
            
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

    public function getLoanInstallmentModificationDetails($approvalId)
    {
        try {
            $approval = DB::table('approval_request as ar')
                ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
                ->select('ar.*', 'u.Full_Name as user_full_name')
                ->where('ar.id', $approvalId)
                ->first();

            if (!$approval || $approval->typeid != 403) {
                return response()->json(['success' => false, 'message' => 'Loan installment modification request not found.']);
            }

            $requestData = json_decode($approval->data, true);
            $loanId = $requestData['loan_id'];
            $customerId = $requestData['customer_id'];
            $changes = $requestData['changes'] ?? [];

            // Get loan details
            $loan = DB::table('customer_loan')
                ->where('idCustomer_Loan', $loanId)
                ->where('branch_id', $approval->branch_id)
                ->first();

            // Get customer details
            $customer = DB::table('customer')
                ->where('idCustomer', $customerId)
                ->where('branch_id', $approval->branch_id)
                ->first();

            $customerName = $customer ? ($customer->First_Name . ' ' . $customer->Last_Name) : 'Unknown';
            $loanNumber = $loan->Loan_No ?? 'N/A';

            $html = '
            <div class="alert alert-warning">
                <i class="ri-calendar-schedule-line me-2"></i><strong>Loan Installment Modification Request</strong>
                <p class="mb-0 mt-2"><small>Review installment date changes before approving</small></p>
            </div>
            
            <h6 class="mb-3">Loan Information</h6>
            
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 35%;">Loan Number</th>
                        <td><strong>' . htmlspecialchars($loanNumber) . '</strong></td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td>' . htmlspecialchars($customerName) . '</td>
                    </tr>
                    <tr>
                        <th>Modified Installments</th>
                        <td><strong class="text-primary">' . count($changes) . ' installment(s)</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <h6 class="mb-2 mt-4">Installment Changes</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Installment #</th>
                            <th>Old Date</th>
                            <th>New Date</th>
                            <th>Old Penalty Date</th>
                            <th>New Penalty Date</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($changes as $change) {
                $html .= '
                        <tr>
                            <td><strong>#' . htmlspecialchars($change['no']) . '</strong></td>
                            <td>' . htmlspecialchars($change['old_installment_date']) . '</td>
                            <td><strong class="text-primary">' . htmlspecialchars($change['new_installment_date']) . '</strong></td>
                            <td>' . htmlspecialchars($change['old_penalty_date']) . '</td>
                            <td><strong class="text-primary">' . htmlspecialchars($change['new_penalty_date']) . '</strong></td>
                        </tr>';
            }

            $html .= '
                    </tbody>
                </table>
            </div>
            
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

    private function buildPersonName(?string $first, ?string $last): ?string
    {
        $first = trim((string)($first ?? ''));
        $last = trim((string)($last ?? ''));

        $fullName = trim($first . ' ' . $last);

        return $fullName === '' ? null : $fullName;
    }

    private function buildAddress(array $parts): ?string
    {
        $formatted = [];

        foreach ($parts as $part) {
            if (is_string($part)) {
                $part = trim($part);
            }

            if (!empty($part)) {
                $formatted[] = $part;
            }
        }

        return empty($formatted) ? null : implode(', ', $formatted);
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

    /**
     * HEAD OFFICE – new PENDING approvals from all branches
     */
    public function notifications(Request $request)
    {
        // Only HO can use this
        if (session('branch_id') != -1) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized.',
            ]);
        }

        $sinceId = (int) $request->get('since_id', 0);
        $init    = (bool) $request->get('init', false);

        $lastPendingId = DB::table('approval_request')
            ->where('status', 0)
            ->max('id') ?? 0;

        // First sync – no popup
        if ($init) {
            return response()->json([
                'success'   => true,
                'init'      => true,
                'last_id'   => $lastPendingId,
                'approvals' => [],
            ]);
        }

        $query = DB::table('approval_request as ar')
            ->leftJoin('branch as b', 'ar.branch_id', '=', 'b.branch_id')
            ->select(
                'ar.id',
                'ar.type',
                'ar.typeid',
                'ar.data_time',
                'b.Name as branch_name'
            )
            ->where('ar.status', 0);

        if ($sinceId > 0) {
            $query->where('ar.id', '>', $sinceId);
        }

        $approvals = $query
            ->orderBy('ar.id', 'asc')
            ->limit(20)
            ->get();

        return response()->json([
            'success'   => true,
            'init'      => false,
            'last_id'   => $lastPendingId,
            'approvals' => $approvals,
        ]);
    }

    /**
     * BRANCH – notifications when their own requests are
     * Approved / Rejected / Callback (status != 0)
     */
    public function branchNotifications(Request $request)
    {
        $branchId = session('branch_id');

        // Only branches (not HO)
        if (!$branchId || $branchId == -1) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized.',
            ]);
        }

        $items = DB::table('approval_notifications')
            ->where('branch_id', $branchId)
            ->where('is_read', 0)
            ->orderBy('id', 'asc')
            ->limit(20)
            ->get();

        if ($items->count() > 0) {
            DB::table('approval_notifications')
                ->whereIn('id', $items->pluck('id'))
                ->update(['is_read' => 1, 'updated_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'items'   => $items,
        ]);
    }
}
