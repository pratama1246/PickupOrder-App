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
     * Kantin milik vendor ini (hanya untuk role vendor).
     */
    public function canteen(): HasOne
    {
        return $this->hasOne(Canteen::class);
    }

    /**
     * Semua pesanan yang dibuat user ini (untuk role mahasiswa).
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Semua ulasan yang diberikan oleh user ini.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah vendor kantin.
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Cek apakah user adalah mahasiswa.
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }
}
