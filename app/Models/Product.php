<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_name',
        'product_code',
        'interest_method',
        'loan_period_type',
        'minimum_loan_period',
        'maximum_loan_period',
        'minimum_loan_amount',
        'maximum_loan_amount',
        'interest_apply_type',
        'minimum_interest',
        'maximum_interest',
        'guarantee_count',
        'collection_period_type',
        'minimum_collection_period',
        'maximum_collection_period',
        'collection_date_type',
        'penalty_method',
        'penalty_apply_type',
        'penalty_percentage',
        'penalty_start_after_days',
        'status'
    ];

    public function additional_charges()
    {
        return $this->hasMany(ProductAdditionalCharges::class);
    }

    public function required_documents()
    {
        return $this->hasMany(ProductRequiredDocuments::class);
    }
}
