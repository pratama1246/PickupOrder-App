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
        'is_anonymous',
    ];

    /**
     * Relasi ke Mahasiswa (User) yang menulis ulasan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Menu hidangan spesifik yang dinilai.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Relasi ke Transaksi (Order) asal transaksi ulasan ini dibuat.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Menyusun nama reviewer dengan masking sensor (misalnya J***n) jika opsi anonim dipilih.
     * Hal ini bertujuan untuk melindungi privasi mahasiswa agar bisa memberikan ulasan jujur
     * tanpa khawatir akan tindakan diskriminatif.
     */
    public function getReviewerNameAttribute()
    {
        if (! $this->user) {
            return 'Anonim';
        }

        $name = $this->user->name;
        if ($this->is_anonymous) {
            $length = mb_strlen($name);
            if ($length <= 2) {
                return $name[0].'*';
            }
            $firstChar = mb_substr($name, 0, 1);
            $lastChar = mb_substr($name, -1);

            return $firstChar.str_repeat('*', $length - 2).$lastChar;
        }

        return $name;
    }

    /**
     * Mengembalikan URL avatar reviewer.
     * Jika ulasan bersifat anonim atau user tidak memiliki foto profil kustom,
     * akan dikembalikan avatar inisial default generator (UI Avatars) demi alasan privasi.
     */
    public function getReviewerAvatarAttribute()
    {
        if ($this->is_anonymous || ! $this->user) {
            return 'https://ui-avatars.com/api/?name=Anonim&background=random&color=fff';
        }

        return $this->user->avatar
            ? asset('storage/'.$this->user->avatar)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->user->name).'&background=random';
    }
}
