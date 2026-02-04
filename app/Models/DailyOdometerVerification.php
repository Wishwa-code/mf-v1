<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOdometerVerification extends Model
{
    use HasFactory;

    protected $table = 'daily_odometer_verifications';

    protected $fillable = [
        'user_id',
        'image_url',
        'upload_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}