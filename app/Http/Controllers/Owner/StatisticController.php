<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticController extends Controller
{
    /**
     * Dashboard statistik penjualan owner.
     *
     * URL: GET /owner/statistics
     */
    public function index(Request $request): View
    {
        // Periode default: 30 hari terakhir
        $from = $request->get('from', now()->subDays(29)->toDateString());
        $to   = $request->get('to', now()->toDateString());

        // ── Ringkasan hari ini ───────────────────────────────────────────────
        $todayStats = [
            'total_orders'    => Order::today()->whereIn('status', ['confirmed', 'completed'])->count(),
            'total_revenue'   => Order::today()->where('status', 'completed')->sum('total_amount'),
            'pending_orders'  => Order::today()->pending()->count(),
        ];

        // ── Statistik periode ────────────────────────────────────────────────
        $periodStats = [
            'total_orders'  => Order::betweenDates($from, $to)
                ->whereIn('status', ['confirmed', 'completed'])->count(),
            'total_revenue' => Order::betweenDates($from, $to)
                ->where('status', 'completed')->sum('total_amount'),
        ];

        // ── Penjualan per hari (untuk chart) ────────────────────────────────
        $dailySales = Order::betweenDates($from, $to)
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as order_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Top 5 menu terlaris berdasarkan quantity ─────────────────────────
        $topMenus = OrderItem::with('menuItem')
            ->whereBetween('created_at', [
                \Carbon\Carbon::parse($from)->startOfDay(),
                \Carbon\Carbon::parse($to)->endOfDay(),
            ])
            ->selectRaw('menu_item_id, menu_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('menu_item_id', 'menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ── Distribusi penjualan per kategori ───────────────────────────────
        $categorySales = OrderItem::join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->whereBetween('order_items.created_at', [
                \Carbon\Carbon::parse($from)->startOfDay(),
                \Carbon\Carbon::parse($to)->endOfDay(),
            ])
            ->selectRaw('categories.name as category_name, SUM(order_items.subtotal) as total_revenue')
            ->groupBy('categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return view('owner.statistics.index', compact(
            'todayStats',
            'periodStats',
            'dailySales',
            'topMenus',
            'categorySales',
            'from',
            'to'
        ));
    }
}
