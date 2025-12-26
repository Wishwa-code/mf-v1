<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table      = 'user';

    protected $fillable = [
        'Full_Name',
        'email',
        'Designation',
        'Epf_no',
        'Nic',
        'password',
        'TP',
        'lending_officer',
        'Status',
        'branch_access',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];


    public function user_has_privileges(){
        return $this->hasMany(UserHasPrivileges::class,'user_id','id');
    }
}
