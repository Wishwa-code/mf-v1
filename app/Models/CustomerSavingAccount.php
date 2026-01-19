<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSavingAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_no',
        'open_date',
        'balance',
        'interest_rate',
        'interest_type',
        'account_status',
        'created_by',
        'customer_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
