<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nim',
        'email',
        'avatar',
        'password',
        'role',
        'is_first_login',
        'password_changed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts atribut Eloquent.
     * Atribut 'password' dikonfigurasi ke 'hashed' agar secara otomatis dienkripsi oleh model
     * saat dibuat atau diperbarui, meminimalkan kelalaian pemanggilan manual Hash::make di controller.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_first_login' => 'boolean',
            'password_changed' => 'boolean',
        ];
    }

    /**
     * Relasi ke profil Kantin yang dimiliki (khusus untuk pengguna dengan peran 'vendor').
     */
    public function canteen(): HasOne
    {
        return $this->hasOne(Canteen::class);
    }

    /**
     * Riwayat seluruh pesanan yang pernah dibuat oleh mahasiswa ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Riwayat ulasan makanan/minuman yang telah ditulis oleh pengguna ini.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Memeriksa apakah pengguna memiliki hak akses Administrator (Admin).
     * Digunakan dalam otentikasi middleware, kebijakan akses (Policies), dan percabangan menu dashboard.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Memeriksa apakah pengguna adalah operator Canteen (Vendor).
     * Digunakan dalam otentikasi middleware, kebijakan akses (Policies), dan percabangan menu dashboard.
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Memeriksa apakah pengguna adalah Mahasiswa umum.
     * Digunakan untuk memfilter menu checkout dan hak belanja makanan.
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }
}
