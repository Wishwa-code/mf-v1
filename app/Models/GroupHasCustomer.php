<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupHasCustomer extends Model
{
    protected $table = 'group_has_customers';

    protected $fillable = [
        'group_id',
        'customer_id'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
