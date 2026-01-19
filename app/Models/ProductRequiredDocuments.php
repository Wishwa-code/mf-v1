<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequiredDocuments extends Model
{
    protected $table = 'product_required_documents';

    protected $fillable = [
        'product_id',
        'name',
        'required_status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
