<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /**
     * Menonaktifkan timestamps bawaan Eloquent.
     * Karena data item pesanan bersifat statis setelah di-checkout dan terikat pada daur hidup transaksi induk.
     */
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
     * Relasi ke Transaksi Utama (Order) induk.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Menu makanan/minuman asli.
     * Digunakan untuk mengambil informasi gambar, nama, dan detail hidangan.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Menghitung subtotal nominal item ini.
     * Menggunakan harga riwayat (price snapshot) yang tersimpan saat pesanan dibuat,
     * untuk menjaga integritas pembukuan jika vendor mengubah harga menu di kemudian hari.
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->price * $this->qty;
    }

    /**
     * Format subtotal belanja ke Rupiah (IDR).
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->getSubtotalAttribute(), 0, ',', '.');
    }
}
