<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'product_name',
        'product_code',
        'interest_method',
        'loan_period_type',
        'interest_period_type',
        'collection_period_type',
        'collection_date_type',
        'guarantee_count',
        'saving_amount_type',
        'saving_collection_type',
        'saving_interest_cal_type',
        'saving_account_status',
        'recovery_account_status',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    public function additional_charges()
    {
        return $this->hasMany(ProductAdditionalCharges::class);
    }

    public function required_documents()
    {
        return $this->hasMany(ProductRequiredDocuments::class);
    }

    public function product_has_items()
    {
        return $this->hasMany(ProductHasItem::class);
    }
}
