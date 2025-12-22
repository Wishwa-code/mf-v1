<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = 'route';

    protected $fillable = [
        'id_route',
        'name',
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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
