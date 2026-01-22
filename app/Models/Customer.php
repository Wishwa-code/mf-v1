<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'title',
        'customer_code',
        'first_name',
        'last_name',
        'email',
        'contact_no',
        'new_nic',
        'old_nic',
        'acc_center_cus_id',
        'route_id',
        'branch_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function recoveryAccounts()
    {
        return $this->hasMany(CustomerRecoveryAccount::class);
    }

    public function savingAccounts()
    {
        return $this->hasMany(CustomerSavingAccount::class);
    }

    public function group_has_customers()
    {
        return $this->hasMany(GroupHasCustomer::class);
    }

    public function ledGroups()
    {
        return $this->hasMany(Group::class, 'leader_id');
    }

    public function ledCenters()
    {
        return $this->hasMany(Center::class, 'customer_id');
    }

    public function getBranchAttribute()
    {
        return Cache::remember('branch_' . $this->branch_id, 3600, function () {
            try {
                $response = Http::timeout(5)
                    ->get(rtrim(env('ACCOUNT_CENTER_BACKEND_URL', 'https://accountcenterserver.asipbook.com'), '/') . '/api/branches/' . $this->branch_id);

                if ($response->successful()) {
                    return (object) $response->json();
                }
            } catch (\Exception $e) {
                // Log error if needed: \Log::error('Failed to fetch branch: ' . $e->getMessage());
            }
            return null;
        });
    }
}
