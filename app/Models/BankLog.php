<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankLog extends Model
{
    use HasFactory;
    protected $table = "company_bank_has_log";
    public $timestamps = false;
}
