<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function pending_approval()
    {
        return view('pages.PendingApproval');
    }

    public function approved_history()
    {
        return view('pages.ApprovedHistory');
    }

    public function rejected_approval()
    {
        return view('pages.RejectedApproval');
    }
}
