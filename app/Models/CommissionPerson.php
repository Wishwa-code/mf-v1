<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPerson extends Model
{
    use HasFactory;

    protected $table = 'commission_people';

    protected $fillable = [
        'branch_id',
        'type',
        'user_id',
        'full_name',
        'contact_number',
        'nic_number',
        'brief_description',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_name',
        'status',
        'created_by',
        'updated_by',
    ];
}
