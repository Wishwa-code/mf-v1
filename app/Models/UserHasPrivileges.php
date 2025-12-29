<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasPrivileges extends Model
{
    protected $table = 'user_privileges_has_user';

    protected $fillable = [
        'permission_key',
        'user_id'
    ];
}
