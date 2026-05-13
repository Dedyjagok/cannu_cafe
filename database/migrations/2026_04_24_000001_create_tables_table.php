<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel tables untuk data meja fisik cafe beserta QR token unik.
     * URL QR format: https://domain.com/menu/{qr_token}
     */
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number', 10)->unique()->comment('Nomor meja, contoh: 1, 2, 3');
            $table->string('qr_token', 64)->unique()->comment('Token unik untuk URL QR code scan');
            $table->boolean('is_available')->default(true)->comment('Status ketersediaan meja');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
