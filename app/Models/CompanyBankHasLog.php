<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBankHasLog extends Model
{
    use HasFactory;

    protected $table = 'company_bank_has_log';
    public $timestamps = false; // Assuming raw insert previously, checking schema implicitly via usage

    protected $fillable = [
        'Bank_Account_Id',
        'Date_Time',
        'Type',
        'Note',
        'Description',
        'Credit',
        'Debit',
        'Balance',
        'User',
        'branch_id',
    ];
}
