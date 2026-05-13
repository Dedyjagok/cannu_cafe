<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'table_id',
        'order_code',
        'status',
        'total_amount',
        'confirmed_by',
        'confirmed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Meja tempat pesanan ini dibuat.
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class, 'table_id');
    }

    /**
     * Item-item dalam pesanan ini.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Kasir yang mengkonfirmasi pesanan.
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Hanya pesanan berstatus pending.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filter pesanan berdasarkan status tertentu.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Pesanan hari ini.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Pesanan dalam rentang tanggal.
     */
    public function scopeBetweenDates(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Generate kode pesanan unik.
     * Format: ORD-YYYYMMDD-XXXX (contoh: ORD-20260424-0023)
     */
    public static function generateOrderCode(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "ORD-{$date}-";

        // Cari nomor urut terakhir pada hari ini
        $lastOrder = static::where('order_code', 'like', "{$prefix}%")
            ->orderByDesc('order_code')
            ->first();

        $sequence = $lastOrder
            ? (int) substr($lastOrder->order_code, -4) + 1
            : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Apakah pesanan masih bisa dikonfirmasi.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Apakah pesanan sudah selesai.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
