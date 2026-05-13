<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel menu_items untuk daftar menu cafe lengkap dengan harga dan gambar.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Referensi ke tabel categories');
            $table->string('name', 150)->comment('Nama menu');
            $table->text('description')->nullable()->comment('Deskripsi singkat menu');
            $table->decimal('price', 10, 2)->comment('Harga satuan menu');
            $table->string('image', 255)->nullable()->comment('Path gambar menu (storage/app/public)');
            $table->boolean('is_available')->default(true)->comment('Ketersediaan menu / stok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
