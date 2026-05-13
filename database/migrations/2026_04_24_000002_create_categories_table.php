<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel categories untuk kategori menu (Makanan, Minuman, Dessert, dll).
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nama kategori, contoh: Makanan, Minuman, Dessert');
            $table->string('icon', 50)->nullable()->comment('Emoji atau nama icon untuk tampilan menu');
            $table->tinyInteger('sort_order')->unsigned()->default(0)->comment('Urutan tampil di halaman menu');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
