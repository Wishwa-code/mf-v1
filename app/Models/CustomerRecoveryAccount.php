<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRecoveryAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'open_date',
        'balance',
        'status',
        'created_by',
        'customer_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
