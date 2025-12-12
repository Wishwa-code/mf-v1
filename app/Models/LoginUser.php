<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginUser extends Model
{
    protected $connection = 'mysql';          // master DB (asipiya_main)
    protected $table      = 'login_users';

    protected $fillable = [
        'email',
        'db_name',
        'active',
    ];

    public $timestamps = true;
}
