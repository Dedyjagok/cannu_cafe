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

        // ── Statistik periode (KPI Cards) ────────────────────────────────────
        $kpiStats = [
            'total_revenue' => Order::betweenDates($from, $to)
                ->where('status', 'completed')->sum('total_amount'),
            'total_orders'  => Order::betweenDates($from, $to)->count(),
            'completed'     => Order::betweenDates($from, $to)
                ->where('status', 'completed')->count(),
            'cancelled'     => Order::betweenDates($from, $to)
                ->where('status', 'cancelled')->count(),
        ];

        // ── Penjualan per hari (untuk chart bar) ────────────────────────────
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

        // ── Riwayat Transaksi Terbaru (tabel) ────────────────────────────────
        $recentTransactions = Order::with('table')
            ->betweenDates($from, $to)
            ->latest()
            ->limit(10)
            ->get();

        return view('owner.statistics', compact(
            'kpiStats',
            'dailySales',
            'topMenus',
            'recentTransactions',
            'from',
            'to'
        ));
    }
}
