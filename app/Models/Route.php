<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'id_route',
        'route_name',
        'root_code',
        'id_officer',
        'branch_id',
        'collection_type',
        'collection_date',
    ];

    public function officer()
    {
        return $this->belongsTo(User::class, 'id_officer');
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

    public function centers()
    {
        return $this->hasMany(Center::class, 'route_id');
    }
}
