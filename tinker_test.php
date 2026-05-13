<?php

/**
 * ============================================================
 *  CAFE CANNU — TINKER TEST SCRIPT
 *  Menguji seluruh alur sistem dari model hingga logic controller
 *  Jalankan: php artisan tinker --execute="require 'tinker_test.php';"
 * ============================================================
 */

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

// ─── Helper output ────────────────────────────────────────────────────────────
function ok(string $msg): void  { echo "  ✅ {$msg}\n"; }
function fail(string $msg): void { echo "  ❌ FAIL: {$msg}\n"; }
function section(string $title): void { echo "\n══════════════════════════════════════\n  {$title}\n══════════════════════════════════════\n"; }
function assert_true(bool $cond, string $msg): void { $cond ? ok($msg) : fail($msg); }
function assert_eq($a, $b, string $msg): void { ($a == $b) ? ok("{$msg} [{$a}]") : fail("{$msg} (expected: {$b}, got: {$a})"); }

echo "\n🚀  CAFE CANNU — TINKER TEST SUITE\n";

// ══════════════════════════════════════
// PHASE 1: USER
// ══════════════════════════════════════
section('PHASE 1: User Model');

// ─── Bersihkan semua data lama (urutan benar, anak dulu sebelum induk) ────────
\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
OrderItem::query()->delete();
Order::query()->delete();
MenuItem::query()->delete();
Category::query()->delete();
CafeTable::query()->delete();
User::query()->delete();
\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
ok("Data lama dibersihkan");

$owner = User::create([
    'name'     => 'Owner Cannu',
    'email'    => 'owner@cannu.id',
    'password' => bcrypt('password'),
    'role'     => 'owner',
    'is_active' => true,
]);
ok("Owner dibuat: {$owner->name} (id={$owner->id})");
assert_true($owner->isOwner(), 'isOwner() = true');
assert_true(!$owner->isKasir(), 'isKasir() = false untuk owner');

$kasir = User::create([
    'name'     => 'Kasir Budi',
    'email'    => 'kasir@cannu.id',
    'password' => bcrypt('password'),
    'role'     => 'kasir',
    'is_active' => true,
]);
ok("Kasir dibuat: {$kasir->name} (id={$kasir->id})");
assert_true($kasir->isKasir(), 'isKasir() = true');
assert_true(!$kasir->isOwner(), 'isOwner() = false untuk kasir');

// ══════════════════════════════════════
// PHASE 2: CAFE TABLE + QR TOKEN
// ══════════════════════════════════════
section('PHASE 2: CafeTable + QR Token');


$token1 = CafeTable::generateQrToken();
$token2 = CafeTable::generateQrToken();
assert_eq(strlen($token1), 64, 'QR token panjang 64 karakter');
assert_true($token1 !== $token2, 'Setiap token unik (token1 ≠ token2)');

$meja1 = CafeTable::create([
    'table_number' => '1',
    'qr_token'     => $token1,
    'is_available' => true,
]);
$meja2 = CafeTable::create([
    'table_number' => '2',
    'qr_token'     => $token2,
    'is_available' => true,
]);
ok("Meja 1 dibuat (token: " . substr($meja1->qr_token, 0, 12) . "...)");
ok("Meja 2 dibuat (token: " . substr($meja2->qr_token, 0, 12) . "...)");

// Resolve meja dari QR token (simulasi scan QR)
$resolved = CafeTable::where('qr_token', $token1)->where('is_available', true)->first();
assert_true($resolved !== null, 'Resolve meja dari QR token berhasil');
assert_eq($resolved->table_number, '1', 'Meja yang di-resolve = Meja 1');

// ══════════════════════════════════════
// PHASE 3: CATEGORY
// ══════════════════════════════════════
section('PHASE 3: Category');


$catMakan = Category::create(['name' => 'Makanan', 'icon' => '🍛', 'sort_order' => 1, 'is_active' => true]);
$catMinum  = Category::create(['name' => 'Minuman', 'icon' => '☕', 'sort_order' => 2, 'is_active' => true]);
$catHidden = Category::create(['name' => 'Hidden',  'icon' => '🚫', 'sort_order' => 9, 'is_active' => false]);
ok("3 kategori dibuat (Makanan, Minuman, Hidden)");

$activeCategories = Category::active()->ordered()->get();
assert_eq($activeCategories->count(), 2, 'scopeActive() → 2 kategori aktif');
assert_eq($activeCategories->first()->name, 'Makanan', 'scopeOrdered() → urutan pertama = Makanan');

// ══════════════════════════════════════
// PHASE 4: MENU ITEM
// ══════════════════════════════════════
section('PHASE 4: MenuItem');


$nasiGoreng = MenuItem::create([
    'category_id'  => $catMakan->id,
    'name'         => 'Nasi Goreng Spesial',
    'description'  => 'Nasi goreng dengan telur dan ayam',
    'price'        => 25000,
    'is_available' => true,
]);
$mieAyam = MenuItem::create([
    'category_id'  => $catMakan->id,
    'name'         => 'Mie Ayam',
    'description'  => 'Mie ayam dengan bakso',
    'price'        => 18000,
    'is_available' => true,
]);
$esTeh = MenuItem::create([
    'category_id'  => $catMinum->id,
    'name'         => 'Es Teh Manis',
    'description'  => null,
    'price'        => 5000,
    'is_available' => true,
]);
$menuHabis = MenuItem::create([
    'category_id'  => $catMakan->id,
    'name'         => 'Menu Habis',
    'price'        => 10000,
    'is_available' => false,
]);
ok("4 menu item dibuat");

$available = MenuItem::available()->get();
assert_eq($available->count(), 3, 'scopeAvailable() → 3 item tersedia');
assert_eq($nasiGoreng->formattedPrice, 'Rp 25.000', 'formattedPrice accessor benar');

// Eager load: kategori dengan availableMenuItems
$cats = Category::active()->ordered()->with('availableMenuItems')->get();
assert_eq($cats->first()->availableMenuItems->count(), 2, 'Makanan punya 2 menu tersedia');

// ══════════════════════════════════════
// PHASE 5: ORDER FLOW — PENDING → CONFIRMED → COMPLETED
// ══════════════════════════════════════
section('PHASE 5: Order Flow (Pending → Confirmed → Completed)');


// 5a. Generate order code
$code1 = Order::generateOrderCode();
$code2 = Order::generateOrderCode(); // sebelum order pertama disimpan → sama (hari ini belum ada)
ok("Order code pertama: {$code1}");

// 5b. Buat order (simulasi customer pesan)
$order = Order::create([
    'table_id'     => $meja1->id,
    'order_code'   => $code1,
    'status'       => 'pending',
    'total_amount' => 0,
]);

// 5c. Tambah order items dengan snapshot harga
$sub1 = OrderItem::calculateSubtotal(2, (float) $nasiGoreng->price); // 2 × 25000 = 50000
$item1 = $order->orderItems()->create([
    'menu_item_id' => $nasiGoreng->id,
    'menu_name'    => $nasiGoreng->name,
    'menu_price'   => $nasiGoreng->price,
    'quantity'     => 2,
    'subtotal'     => $sub1,
    'notes'        => 'Pedas level 2',
]);

$sub2 = OrderItem::calculateSubtotal(1, (float) $esTeh->price); // 1 × 5000 = 5000
$item2 = $order->orderItems()->create([
    'menu_item_id' => $esTeh->id,
    'menu_name'    => $esTeh->name,
    'menu_price'   => $esTeh->price,
    'quantity'     => 1,
    'subtotal'     => $sub2,
    'notes'        => null,
]);

$total = $sub1 + $sub2; // 55000
$order->update(['total_amount' => $total]);
ok("Order dibuat: {$order->order_code} — {$order->orderItems->count()} item — Total: Rp " . number_format($total, 0, ',', '.'));

assert_eq($sub1, 50000, 'calculateSubtotal(2 × 25000) = 50000');
assert_eq($sub2, 5000,  'calculateSubtotal(1 × 5000) = 5000');
assert_eq($total, 55000, 'Total order = 55000');
assert_eq($item1->formattedMenuPrice, 'Rp 25.000', 'formattedMenuPrice accessor');
assert_eq($item2->formattedSubtotal, 'Rp 5.000', 'formattedSubtotal accessor');

// 5d. Cek scopes
$order->refresh();
assert_true($order->isPending(), 'isPending() = true setelah dibuat');
assert_eq(Order::pending()->count(), 1, 'scopePending() → 1 order');

// 5e. Kasir konfirmasi (simulasi KasirController@confirm)
assert_true(
    Order::where('id', $order->id)->where('status', 'pending')->exists(),
    'Order berstatus pending sebelum dikonfirmasi'
);
$order->update([
    'status'       => 'confirmed',
    'confirmed_by' => $kasir->id,
    'confirmed_at' => now(),
]);
$order->refresh();
assert_eq($order->status, 'confirmed', 'Status berubah → confirmed');
assert_eq($order->confirmed_by, $kasir->id, 'confirmed_by = kasir id');
assert_true($order->confirmed_at !== null, 'confirmed_at ter-set');
assert_true(!$order->isPending(), 'isPending() = false setelah dikonfirmasi');

// Relasi confirmedOrders dari kasir
assert_eq($kasir->confirmedOrders()->count(), 1, 'kasir->confirmedOrders() = 1');

// 5f. Kasir tandai selesai (simulasi KasirController@complete)
assert_true(
    Order::where('id', $order->id)->where('status', 'confirmed')->exists(),
    'Order berstatus confirmed sebelum diselesaikan'
);
$order->update(['status' => 'completed']);
$order->refresh();
assert_eq($order->status, 'completed', 'Status berubah → completed');
assert_true($order->isCompleted(), 'isCompleted() = true');

// ══════════════════════════════════════
// PHASE 6: ORDER CANCELLATION
// ══════════════════════════════════════
section('PHASE 6: Order Cancellation');

$orderCode2 = Order::generateOrderCode();
$orderCancel = Order::create([
    'table_id'     => $meja2->id,
    'order_code'   => $orderCode2,
    'status'       => 'pending',
    'total_amount' => 18000,
]);
$orderCancel->orderItems()->create([
    'menu_item_id' => $mieAyam->id,
    'menu_name'    => $mieAyam->name,
    'menu_price'   => $mieAyam->price,
    'quantity'     => 1,
    'subtotal'     => 18000,
    'notes'        => null,
]);
ok("Order cancel dibuat: {$orderCancel->order_code}");

// Simulasi KasirController@cancel
$cancelled = Order::where('id', $orderCancel->id)
    ->whereIn('status', ['pending', 'confirmed'])
    ->first();
$cancelled->update(['status' => 'cancelled']);
$cancelled->refresh();
assert_eq($cancelled->status, 'cancelled', 'Status berubah → cancelled');

// ══════════════════════════════════════
// PHASE 7: SCOPE QUERY (Statistik)
// ══════════════════════════════════════
section('PHASE 7: Scope Queries & Statistik');

assert_eq(Order::today()->count(), 2, 'scopeToday() → 2 order hari ini');
assert_eq(Order::byStatus('completed')->count(), 1, 'scopeByStatus(completed) → 1');
assert_eq(Order::byStatus('cancelled')->count(), 1, 'scopeByStatus(cancelled) → 1');
assert_eq(Order::pending()->count(), 0, 'scopePending() → 0 (semua sudah diproses)');

// Simulasi query StatisticController
$todayRevenue = Order::today()->where('status', 'completed')->sum('total_amount');
assert_eq((int)$todayRevenue, 55000, 'Revenue hari ini (completed only) = 55000');

$topMenu = OrderItem::selectRaw('menu_item_id, menu_name, SUM(quantity) as total_qty')
    ->groupBy('menu_item_id', 'menu_name')
    ->orderByDesc('total_qty')
    ->first();
assert_eq($topMenu->menu_name, 'Nasi Goreng Spesial', 'Top menu = Nasi Goreng Spesial (qty: 2)');

// betweenDates scope
$periodOrders = Order::betweenDates(now()->subDays(1)->toDateString(), now()->toDateString())->count();
assert_eq($periodOrders, 2, 'scopeBetweenDates() → 2 order dalam periode 2 hari');

// ══════════════════════════════════════
// PHASE 8: RELASI LENGKAP
// ══════════════════════════════════════
section('PHASE 8: Relasi Eloquent');

$orderWithRelations = Order::with(['table', 'orderItems.menuItem', 'confirmedBy'])->find($order->id);
assert_true($orderWithRelations->table instanceof CafeTable, 'order->table = CafeTable instance');
assert_true($orderWithRelations->confirmedBy instanceof User, 'order->confirmedBy = User instance');
assert_eq($orderWithRelations->orderItems->count(), 2, 'order->orderItems = 2 item');
assert_true($orderWithRelations->orderItems->first()->menuItem instanceof MenuItem, 'orderItem->menuItem relasi OK');

$meja1WithOrders = CafeTable::with('orders')->find($meja1->id);
assert_eq($meja1WithOrders->orders->count(), 1, 'meja1->orders = 1 order');

$catWithMenus = Category::with('menuItems')->find($catMakan->id);
assert_eq($catWithMenus->menuItems->count(), 3, 'Makanan->menuItems = 3 item (termasuk yang habis)');

// ══════════════════════════════════════
// RINGKASAN
// ══════════════════════════════════════
echo "\n══════════════════════════════════════\n";
echo "  🎉  SEMUA TEST SELESAI\n";
echo "══════════════════════════════════════\n";
echo "  Data test di database:\n";
echo "  • Users   : " . User::count() . " (1 owner, 1 kasir)\n";
echo "  • Tables  : " . CafeTable::count() . " meja\n";
echo "  • Categories: " . Category::count() . " (2 aktif, 1 non-aktif)\n";
echo "  • MenuItems : " . MenuItem::count() . " (3 available, 1 habis)\n";
echo "  • Orders    : " . Order::count() . " (1 completed, 1 cancelled)\n";
echo "  • OrderItems: " . OrderItem::count() . " item\n\n";
