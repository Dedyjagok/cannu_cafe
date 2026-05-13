<?php

use App\Http\Controllers\KasirController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\MenuItemController;
use App\Http\Controllers\Owner\StatisticController;
use App\Http\Controllers\Owner\TableController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Utama ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Customer Routes (Publik, tanpa autentikasi) ──────────────────────────────
// Customer scan QR code → halaman menu cafe
Route::get('/menu/{qrToken}', [MenuController::class, 'show'])
    ->name('menu.show');

// Customer submit pesanan
Route::post('/order', [MenuController::class, 'store'])
    ->name('order.store');

// Customer cek status pesanan (live tracking)
Route::get('/order/{orderCode}/status', [MenuController::class, 'status'])
    ->name('order.status');

// ─── Autentikasi ─────────────────────────────────────────────────────────────
// TODO: Jalankan `php artisan breeze:install` lalu uncomment baris berikut:
// require __DIR__.'/auth.php';

// Route login sementara (redirect ke /kasir/dashboard setelah install Breeze)
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// ─── Kasir Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kasir,owner'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        // Dashboard kasir dengan daftar pesanan realtime
        Route::get('/dashboard', [KasirController::class, 'index'])
            ->name('dashboard');

        // Aksi manajemen pesanan
        Route::post('/order/{id}/confirm', [KasirController::class, 'confirm'])
            ->name('order.confirm');

        Route::post('/order/{id}/complete', [KasirController::class, 'complete'])
            ->name('order.complete');

        Route::post('/order/{id}/cancel', [KasirController::class, 'cancel'])
            ->name('order.cancel');
    });

// ─── Owner Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        // Statistik Penjualan
        Route::get('/statistics', [StatisticController::class, 'index'])
            ->name('statistics');

        // CRUD Kategori Menu
        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->names('categories');

        // CRUD Menu Item
        Route::resource('menu-items', MenuItemController::class)
            ->except(['show'])
            ->names('menu-items')
            ->parameters(['menu-items' => 'menuItem']);

        // CRUD Meja + QR Code
        Route::resource('tables', TableController::class)
            ->except(['show'])
            ->names('tables')
            ->parameters(['tables' => 'table']);

        // Tampilkan QR Code meja (SVG)
        Route::get('/tables/{table}/qr', [TableController::class, 'showQr'])
            ->name('tables.qr');

        // Regenerate QR token meja
        Route::post('/tables/{table}/regenerate-qr', [TableController::class, 'regenerateQr'])
            ->name('tables.regenerate-qr');
    });
