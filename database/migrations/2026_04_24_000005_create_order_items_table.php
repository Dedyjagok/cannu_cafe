<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel order_items untuk detail item dalam satu pesanan.
     *
     * PENTING: menu_name dan menu_price di-snapshot saat pemesanan
     * agar integritas historis laporan penjualan owner terjaga
     * meskipun harga/nama menu diubah di kemudian hari.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete()
                  ->comment('Referensi ke tabel orders');
            $table->foreignId('menu_item_id')
                  ->constrained('menu_items')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Referensi ke tabel menu_items');
            $table->string('menu_name', 150)->comment('Snapshot nama menu saat pesanan dibuat');
            $table->decimal('menu_price', 10, 2)->comment('Snapshot harga menu saat pesanan dibuat');
            $table->tinyInteger('quantity')->unsigned()->default(1)->comment('Jumlah item yang dipesan');
            $table->decimal('subtotal', 10, 2)->comment('quantity × menu_price');
            $table->string('notes', 255)->nullable()->comment('Catatan per item, contoh: tanpa bawang, pedas level 2');
            $table->timestamps();

            // Index untuk efisiensi query laporan statistik per menu
            $table->index(['menu_item_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
