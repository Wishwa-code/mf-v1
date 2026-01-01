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
        'loan_amount',
        'business_category_id',
        'verification_image',
        'source',
        'route_id',
        'district',
        'city',
        'recovery_officer_id',
    ];

    public function images()
    {
        return $this->hasMany(LeadHasImages::class, 'lead_id');
    }

    public function recoveryOfficer()
    {
        return $this->belongsTo(User::class, 'recovery_officer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class, 'lead_id')->orderByDesc('created_at');
    }

    public function logActivity($action, $description, $properties = [])
    {
        $this->activities()->create([
            'user_id' => session('user_data')["idUser"] ?? session('user_data')["idUser"], // Fallback to auth() if session not set
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id_route');
    }
}
