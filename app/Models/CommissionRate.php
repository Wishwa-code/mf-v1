<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRate extends Model
{
    use HasFactory;

    protected $table = 'commission_rates';

    protected $fillable = [
        'branch_id',
        'commission_person_id',
        'product_id',
        'rate',
        'created_by',
        'updated_by',
    ];
}
