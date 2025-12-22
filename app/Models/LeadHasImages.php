<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadHasImages extends Model
{
    use HasFactory;

    protected $table = 'lead_has_images';

    protected $fillable = [
        'lead_id',
        'image_path',
        'image_type',
        'latitude',
        'longitude',
    ];
}
