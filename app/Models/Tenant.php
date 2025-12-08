<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'main';        // 🔹 master DB
    protected $table      = 'tenants';

    protected $fillable = [
        'code',
        'name',
        'db_host',
        'db_port',
        'db_database',
        'db_username',
        'db_password',
        'active',
    ];
}

