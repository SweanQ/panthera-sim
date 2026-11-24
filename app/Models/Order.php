<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'customer_id', 'product_id', 'user_id',
        'size', 'color', 'quantity', 'total_price', 'design_file',
        'revision_count', 'payment_status', 'production_status', 'order_source', 'notes',
        'production_started_at', 'cancelled_at', 'cancelled_by', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'production_started_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function canBeCancelled(): bool
    {
        return $this->cancelled_at === null
            && $this->production_started_at === null
            && in_array($this->production_status, ['konsultasi', 'desain_disetujui'], true);
    }
}