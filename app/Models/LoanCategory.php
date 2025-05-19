<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCategory extends Model
{
    use HasFactory;
    protected $primaryKey = 'idLoan_Category';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = "loan_category";
    public $timestamps = false;
}
