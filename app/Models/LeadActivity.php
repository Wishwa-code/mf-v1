<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'action',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(CustomerLead::class, 'lead_id');
    }

    public function user()
    {
        // Assuming User model is in App\Models\User or similar
        return $this->belongsTo(User::class, 'user_id');
    }
}
