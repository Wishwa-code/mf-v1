<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Center extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'center_name',
        'center_code',
        'contact_no',
        'adress',
        'longitude',
        'latitude',
        'status',
        'customer_id',
        'route_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
