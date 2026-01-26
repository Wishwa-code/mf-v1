<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $table = 'approval_request';

    protected $fillable = [
        'data_time',
        'branch_id',
        'userid',
        'type',
        'typeid',
        'description',
        'data',
        'comment',
        'approveduserid',
        'status',
        'approved_date_time',
        'approved_reject_comment',
        'callback_comment'
    ];
}

