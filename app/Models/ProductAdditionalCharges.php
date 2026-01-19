<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAdditionalCharges extends Model
{
    protected $table = 'product_additional_charges';

    protected $fillable = [
        'product_id',
        'description',
        'value_type',
        'value',
        'deduction_type'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
