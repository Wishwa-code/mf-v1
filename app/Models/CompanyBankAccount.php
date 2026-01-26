<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
    use HasFactory;

    protected $table = 'company_bank_accounts';

    protected $fillable = [
        'Bank_Type',
        'code',
        'Bank_Name',
        'Account_Name',
        'Account_No',
        'Bank_Branch',
        'Account_Balance',
        'type',
        'cashflow',
        'acc_type_group',
        'User',
        'branch_id',
    ];
}
