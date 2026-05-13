<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CafeTable extends Model
{
    /**
     * Nama tabel di database (menghindari konvensi default 'cafe_tables').
     */
    protected $table = 'tables';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'table_number',
        'qr_token',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Semua pesanan yang pernah dibuat di meja ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    /**
     * Pesanan aktif (pending / confirmed) di meja ini.
     */
    public function activeOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id')
            ->whereIn('status', ['pending', 'confirmed']);
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Generate token unik (64 karakter hex) untuk QR code meja.
     * URL format: https://domain.com/menu/{qr_token}
     */
    public static function generateQrToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32)); // 64 hex chars
        } while (static::where('qr_token', $token)->exists());

        return $token;
    }

    /**
     * URL lengkap yang di-encode ke dalam QR code meja ini.
     */
    public function getQrUrlAttribute(): string
    {
        return route('menu.show', ['qrToken' => $this->qr_token]);
    }
}
