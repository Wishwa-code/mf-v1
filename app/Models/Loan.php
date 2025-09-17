<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'customer_loan';
    protected $primaryKey = 'idCustomer_Loan';
    public $timestamps = false;

    // if you ever mass-assign on Loan, either keep guarded empty or define fillable
    protected $guarded = [];
}
