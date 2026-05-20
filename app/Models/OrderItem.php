<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'menu_id',
        'qty',
        'price',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'qty' => 'integer',
    ];

    /**
     * Pesanan induk item ini.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Menu yang dipesan (snapshot nama & gambar via relasi).
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Subtotal item ini (price snapshot * qty).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->price * $this->qty;
    }

    /**
     * Subtotal dalam format rupiah.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->getSubtotalAttribute(), 0, ',', '.');
    }
}
