<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_code',
        'contact_no',
        'leader_id',
        'center_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function leader()
    {
        return $this->belongsTo(Customer::class, 'leader_id');
    }

    public function group_has_customers()
    {
        return $this->hasMany(GroupHasCustomer::class);
    }
}
