<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel orders untuk header pesanan per meja.
     *
     * Status flow:
     *   pending   → pesanan masuk dari customer, notif ke kasir
     *   confirmed → kasir mengkonfirmasi pesanan
     *   completed → pesanan selesai dilayani
     *   cancelled → pesanan dibatalkan
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')
                  ->constrained('tables')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Referensi ke tabel tables (meja)');
            $table->string('order_code', 20)->unique()->comment('Kode unik pesanan, contoh: ORD-20260424-001');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                  ->default('pending')
                  ->comment('Status alur pesanan');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('Total harga seluruh item pesanan');
            $table->foreignId('confirmed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('ID kasir yang mengkonfirmasi pesanan');
            $table->timestamp('confirmed_at')->nullable()->comment('Waktu konfirmasi oleh kasir');
            $table->timestamps();

            // Index untuk query statistik penjualan owner
            $table->index(['status', 'created_at']);
            $table->index(['table_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
