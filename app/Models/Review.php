<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'menu_id',
        'order_id',
        'rating',
        'comment',
    ];

    /**
     * User yang memberikan ulasan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menu yang diulas.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Order asal ulasan ini.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
