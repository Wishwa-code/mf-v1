<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLead extends Model
{
    use HasFactory;

    protected $table = 'customer_leads';

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'longitude',
        'latitude',
        'address',
        'notes',
        'periods',
        'type',
        'status',
        'created_at_lead',
        'created_by',
        'updated_by',
        'is_visited',
        'visit_notes',
        'visited_longitude',
        'visited_latitude',
    ];

    public function images()
    {
        return $this->hasMany(LeadHasImages::class, 'lead_id');
    }
}
