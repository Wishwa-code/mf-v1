<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHasItem extends Model
{
    protected $table = 'product_has_items';

    protected $fillable = [
        'product_id',
        'product_item_name',
        'minimum_loan_period',
        'maximum_loan_period',
        'minimum_loan_amount',
        'maximum_loan_amount',
        'interest_apply_type',
        'minimum_interest',
        'maximum_interest',
        'minimum_collection_period',
        'maximum_collection_period',
        'penalty_method',
        'penalty_apply_type',
        'penalty_percentage',
        'penalty_start_after_days',
        'saving_amount',
        'saving_amount_type',
        'saving_payment',
        'saving_account_monthly_interest',
        'saving_interest_cal_type',
        'required_guarantee_count'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }    
}
