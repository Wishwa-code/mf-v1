<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOdometerVerification extends Model
{
    use HasFactory;

    protected $table = 'daily_odometer_verifications';

    protected $fillable = [
        'collector_id',
        'collector_name',
        'start_reading_value',
        'start_photo',
        'start_upload_time',
        'date',
        'start_location',
        'end_location',
        'end_photo',
        'end_reading_value',
        'end_upload_time',
        'manager_id',
        'manager_name',
        'manager_comment',
        'verified_status',
        'verified_user_id',
    ];

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }
}