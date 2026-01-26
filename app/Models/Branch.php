<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branch';
    protected $primaryKey = 'branch_id';

    protected $fillable = [
        'Name',
        'Address',
        'Contact_Number',
        'status',
        // Add other fields as necessary from schema
    ];
}
