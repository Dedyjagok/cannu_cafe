<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class KasirController extends Controller
{
    /**
     * Dashboard kasir: daftar pesanan aktif (pending & confirmed).
     *
     * URL: GET /kasir/dashboard
     */
    public function index(): View
    {
        // Stat counts are handled reactively by the KasirDashboard Livewire component.
        return view('kasir.dashboard');
    }

    /**
     * Riwayat semua pesanan dengan filter tanggal, status, dan search.
     *
     * URL: GET /kasir/history
     */
    public function history(Request $request): View
    {
        // ── Filter params ──────────────────────────────────────────
        $from         = $request->input('from', today()->toDateString());
        $to           = $request->input('to',   today()->toDateString());
        $statusFilter = $request->input('status', 'all');
        $search       = $request->input('search', '');

        // ── Query ──────────────────────────────────────────────────
        $query = Order::with(['table', 'orderItems', 'confirmedBy'])
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->latest();

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('table', fn ($tq) =>
                        $tq->where('table_number', 'like', "%{$search}%")
                  );
            });
        }

        $orders = $query->paginate(15);

        // ── Today summary stats (always today, regardless of filter) ──
        $todayCompleted = Order::today()->byStatus('completed')->count();
        $todayRevenue   = Order::today()->byStatus('completed')->sum('total_amount');
        $todayCancelled = Order::today()->byStatus('cancelled')->count();

        return view('kasir.order-history-page', compact(
            'orders',
            'from',
            'to',
            'statusFilter',
            'search',
            'todayCompleted',
            'todayRevenue',
            'todayCancelled',
        ));
    }

    /**
     * Kasir mengkonfirmasi pesanan (pending → confirmed).
     *
     * URL: POST /kasir/order/{id}/confirm
     */
    public function confirm(int $id): RedirectResponse
    {
        $order = Order::pending()->where('id', $id)->firstOrFail();

        $order->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', "Pesanan #{$order->order_code} berhasil dikonfirmasi.");
    }

    /**
     * Kasir menandai pesanan selesai (confirmed → completed).
     *
     * URL: POST /kasir/order/{id}/complete
     */
    public function complete(int $id): RedirectResponse
    {
        $order = Order::where('id', $id)->where('status', 'confirmed')->firstOrFail();

        $order->update(['status' => 'completed']);

        return back()->with('success', "Pesanan #{$order->order_code} selesai.");
    }

    /**
     * Kasir membatalkan pesanan (pending/confirmed → cancelled).
     *
     * URL: POST /kasir/order/{id}/cancel
     */
    public function cancel(Request $request, int $id): RedirectResponse
    {
        $order = Order::where('id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        $order->update(['status' => 'cancelled']);

        return back()->with('success', "Pesanan #{$order->order_code} dibatalkan.");
    }
}
