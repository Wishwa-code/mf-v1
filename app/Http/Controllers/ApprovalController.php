<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function pending_approval()
    {
        // Get pending approvals with branch and user information
        $pendingApprovals = DB::table('approval_request as ar')
            ->leftJoin('branch as b', 'ar.branch_id', '=', 'b.branch_id')
            ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
            ->select(
                'ar.*',
                'b.Name as branch_name',
                'u.Full_Name as user_full_name'
            )
            ->where('ar.status', 0) // Pending status
            ->orderBy('ar.data_time', 'desc')
            ->get();

        return view('pages.PendingApproval', compact('pendingApprovals'));
    }

    public function approved_history()
    {
        // Get approved requests
        $approvedHistory = DB::table('approval_request as ar')
            ->leftJoin('branch as b', 'ar.branch_id', '=', 'b.branch_id')
            ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
            ->leftJoin('user as au', 'ar.approveduserid', '=', 'au.id')
            ->select(
                'ar.*',
                'b.Name as branch_name',
                'u.Full_Name as user_full_name',
                'au.Full_Name as approved_by_full_name'
            )
            ->where('ar.status', 1) // Approved status
            ->orderBy('ar.approved_date_time', 'desc')
            ->get();

        return view('pages.ApprovedHistory', compact('approvedHistory'));
    }

    public function rejected_approval()
    {
        // Get rejected requests
        $rejectedApprovals = DB::table('approval_request as ar')
            ->leftJoin('branch as b', 'ar.branch_id', '=', 'b.branch_id')
            ->leftJoin('user as u', 'ar.userid', '=', 'u.id')
            ->leftJoin('user as au', 'ar.approveduserid', '=', 'au.id')
            ->select(
                'ar.*',
                'b.Name as branch_name',
                'u.Full_Name as user_full_name',
                'au.Full_Name as rejected_by_full_name'
            )
            ->where('ar.status', 2) // Rejected status
            ->orderBy('ar.approved_date_time', 'desc')
            ->get();

        return view('pages.RejectedApproval', compact('rejectedApprovals'));
    }

    public function approve(Request $request)
    {
        $id = $request->input('id');
        $comment = $request->input('comment');
        
        try {
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
