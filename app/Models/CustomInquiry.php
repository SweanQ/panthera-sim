<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomInquiry extends Model
{
    protected $fillable = [
        'name',
        'whatsapp_number',
        'product_id',
        'quantity',
        'details',
        'status',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
