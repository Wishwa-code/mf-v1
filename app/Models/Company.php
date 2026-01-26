<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = "company";
    public $timestamps = false;

    protected $fillable = [
        'company_name',
        'address',
        'contact_no',
        'logo',
        'customer_num_type',
        'customer_seperate_from',
        'customer_num_start_from',
        'customer_format',
        'loan_num_type',
        'loan_seperate_from',
        'loan_format',
        'inv_loan_num_type',
        'inv_loan_seperate_from',
        'inv_loan_format',
        'account_saving_type',
        'saving_seperate_from',
        'saving_format',
        'mask',
        'branch',
        'saturday_sunday',
        'points',
        'points_percentage',
        'company_header',
        'company_footer',
        'product_editable',
        'banner_status',
        'banner',
        'branch_id',
        'provider',
        'customer_format_scope',
        'inv_customer_number',
    ];
    
}
