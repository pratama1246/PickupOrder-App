<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'canteen_id',
        'order_code',
        'status',
        'pickup_time',
        'total_price',
        'notes',
        'payment_method',
        'payment_status',
        'payment_code',
        'snap_token',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Mendaftarkan event model Eloquent.
     * Secara otomatis membuat kode transaksi (order_code) unik sebelum record disimpan.
     */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_code)) {
                $order->order_code = self::generateOrderCode();
            }
        });
    }

    /**
     * Membuat kode transaksi acak dengan format PNC-ORD-[YYYYMMDD]-[RANDOM].
     * Menggunakan perulangan (do-while) untuk memastikan tidak terjadi tabrakan data (collision) di database.
     */
    public static function generateOrderCode(): string
    {
        do {
            $code = 'PNC-ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }

    /**
     * Mengambil 6 karakter acak terakhir dari kode transaksi.
     * Digunakan sebagai kode verifikasi cepat saat pengambilan makanan di kantin oleh mahasiswa.
     */
    public function getPickupCodeAttribute(): string
    {
        $parts = explode('-', $this->order_code);

        return end($parts);
    }

    /**
     * Relasi ke Mahasiswa (User) pembuat pesanan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Kantin tempat pesanan ini ditujukan.
     */
    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    /**
     * Daftar item makanan/minuman yang dipesan dalam transaksi ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ulasan ulasan makanan yang dikaitkan dengan pesanan ini setelah selesai.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Menerjemahkan status teknis di database menjadi label ramah pengguna.
     * Disesuaikan dengan kebutuhan badge status transaksi di frontend.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu',
            'dimasak' => 'Diproses',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => 'Menunggu',
        };
    }

    /**
     * Format total belanja ke dalam Rupiah (IDR).
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Menerjemahkan metode pembayaran database ke label teks UI.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'midtrans' => 'Online (QRIS / E-Wallet)',
            'cash' => 'Bayar di Warung',
            default => 'Bayar di Warung',
        };
    }

    /**
     * Menerjemahkan status transaksi Midtrans / Cash ke label teks UI.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            default => 'Belum Dibayar',
        };
    }

    /**
     * Mengecek status pelunasan pembayaran transaksi.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Menghitung urutan antrean transaksi yang sedang aktif (status 'menunggu' atau 'dimasak').
     * Antrean dihitung berdasarkan jumlah pesanan masuk dengan ID lebih kecil di kantin yang sama.
     */
    public function getQueuePositionAttribute(): int
    {
        if (! in_array($this->status, ['menunggu', 'dimasak'])) {
            return 0;
        }

        $count = self::where('canteen_id', $this->canteen_id)
            ->whereIn('status', ['menunggu', 'dimasak'])
            ->where('id', '<', $this->id)
            ->count();

        return $count + 1;
    }

    /**
     * Menghitung estimasi sisa waktu tunggu penyajian makanan (diasumsikan rata-rata 5 menit per nomor antrean).
     */
    public function getEstimatedTimeAttribute(): int
    {
        return $this->queue_position * 5;
    }

    /**
     * Query scope untuk mempermudah pemfilteran pesanan berdasarkan status operasionalnya.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
