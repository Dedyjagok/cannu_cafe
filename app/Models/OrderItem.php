<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_name',
        'menu_price',
        'quantity',
        'subtotal',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'menu_price' => 'decimal:2',
            'subtotal'   => 'decimal:2',
            'quantity'   => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Pesanan induk dari item ini.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Menu item yang direferensikan.
     * CATATAN: menu_name dan menu_price sudah di-snapshot saat order dibuat,
     * sehingga data historis laporan tetap valid meskipun menu diubah.
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Kalkulasi subtotal dari quantity × menu_price.
     */
    public static function calculateSubtotal(int $quantity, float $menuPrice): float
    {
        return $quantity * $menuPrice;
    }

    /**
     * Harga per item terformat Rupiah.
     */
    public function getFormattedMenuPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->menu_price, 0, ',', '.');
    }

    /**
     * Subtotal terformat Rupiah.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
