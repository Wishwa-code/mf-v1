<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettingLog extends Model
{
    use HasFactory;

    protected $table = 'app_setting_log';

    public $timestamps = false; // Manually managing changed_at if needed, or enable timestamps if schema supports

    protected $fillable = [
        'user_id',
        'setting_key',
        'old_value',
        'new_value',
        'changed_at',
        'branch_id',
        'ip_address',
        'user_agent',
    ];
}
