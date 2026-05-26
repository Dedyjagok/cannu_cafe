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
    if (auth()->check()) {
        return auth()->user()->isOwner()
            ? redirect()->route('owner.statistics')
            : redirect()->route('kasir.dashboard');
    }
    return redirect()->route('login');
});

// ─── Customer Routes (Publik, tanpa autentikasi) ──────────────────────────────
Route::get('/menu/{qrToken}', [MenuController::class, 'show'])
    ->name('menu.show');

Route::post('/order', [MenuController::class, 'store'])
    ->name('order.store');

Route::get('/order/{orderCode}/status', [MenuController::class, 'status'])
    ->name('order.status');

// ─── Autentikasi (Breeze) ─────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Kasir Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kasir,owner'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('/dashboard', [KasirController::class, 'index'])
            ->name('dashboard');

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
        Route::get('/statistics', [StatisticController::class, 'index'])
            ->name('statistics');

        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->names('categories');

        Route::resource('menu-items', MenuItemController::class)
            ->except(['show', 'create', 'edit'])
            ->names('menu-items')
            ->parameters(['menu-items' => 'menuItem']);

        Route::resource('tables', TableController::class)
            ->except(['show', 'create', 'edit'])
            ->names('tables')
            ->parameters(['tables' => 'table']);

        Route::get('/tables/{table}/qr', [TableController::class, 'showQr'])
            ->name('tables.qr');

        Route::post('/tables/{table}/regenerate-qr', [TableController::class, 'regenerateQr'])
            ->name('tables.regenerate-qr');
    });
