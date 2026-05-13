<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KasirController extends Controller
{
    /**
     * Dashboard kasir: daftar pesanan aktif (pending & confirmed).
     *
     * URL: GET /kasir/dashboard
     */
    public function index(): View
    {
        $pendingOrders = Order::pending()
            ->with(['table', 'orderItems'])
            ->latest()
            ->get();

        $confirmedOrders = Order::byStatus('confirmed')
            ->with(['table', 'orderItems'])
            ->latest()
            ->get();

        return view('kasir.dashboard', compact('pendingOrders', 'confirmedOrders'));
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
