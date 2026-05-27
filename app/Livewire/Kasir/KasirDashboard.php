<?php

namespace App\Livewire\Kasir;

use App\Models\Order;
use Illuminate\View\View;
use Livewire\Component;

/**
 * KasirDashboard — Unified real-time dashboard component.
 *
 * Polling every 3s handles:
 *   • Stat cards  (pending / confirmed / completed today)
 *   • New-order detection → dispatch browser event for sound + banner
 *   • Order list table with filter tabs
 *   • Confirm / Complete / Cancel actions
 */
class KasirDashboard extends Component
{
    // ── State ──────────────────────────────────────────────────────────

    /** Filter tab: 'all' | 'pending' | 'confirmed' */
    public string $filterStatus = 'all';

    /** Snapshot of pending count on last poll — used to detect new orders */
    public int $previousPendingCount = 0;

    // ── Stats (reactive, updated every poll) ────────────────────────

    public int $pendingCount   = 0;
    public int $confirmedCount = 0;
    public int $completedToday = 0;

    // ── Lifecycle ───────────────────────────────────────────────────

    public function mount(): void
    {
        // Snapshot the current pending count so the first poll
        // doesn't immediately fire a false "new order" notification.
        $this->previousPendingCount = Order::pending()->count();
    }

    /**
     * Called by wire:poll.3000ms in the Livewire view.
     * Detects new incoming orders and dispatches browser event for sound + banner.
     * Stats are always queried fresh in render(), so no need to update them here.
     */
    public function refreshDashboard(): void
    {
        $currentPending = Order::pending()->count();

        // ── Detect new orders ──────────────────────────────────────
        if ($currentPending > $this->previousPendingCount) {
            $newOrders = Order::pending()
                ->with('table')
                ->latest()
                ->limit($currentPending - $this->previousPendingCount)
                ->get()
                ->map(fn ($o) => [
                    'id'           => $o->id,
                    'order_code'   => $o->order_code,
                    'table_number' => $o->table->table_number,
                    'total_amount' => $o->total_amount,
                ])
                ->toArray();

            $this->dispatch('new-orders-arrived', orders: $newOrders);
        }

        $this->previousPendingCount = $currentPending;
    }

    // ── Filter ──────────────────────────────────────────────────────

    public function setFilter(string $status): void
    {
        $this->filterStatus = $status;
    }

    // ── Order Actions ───────────────────────────────────────────────

    public function confirm(int $orderId): void
    {
        $order = Order::where('id', $orderId)
            ->where('status', 'pending')
            ->firstOrFail();

        $order->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        session()->flash('success', "✅ Pesanan #{$order->order_code} dikonfirmasi.");
    }

    public function complete(int $orderId): void
    {
        $order = Order::where('id', $orderId)
            ->where('status', 'confirmed')
            ->firstOrFail();

        $order->update(['status' => 'completed']);

        session()->flash('success', "✅ Pesanan #{$order->order_code} selesai.");
    }

    public function cancel(int $orderId): void
    {
        $order = Order::where('id', $orderId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        $order->update(['status' => 'cancelled']);

        session()->flash('success', "❌ Pesanan #{$order->order_code} dibatalkan.");
    }

    // ── Render ──────────────────────────────────────────────────────

    public function render(): View
    {
        // Stats are always queried here so they're fresh on every render
        // (polling re-renders, action re-renders, filter changes, etc.)
        $this->pendingCount   = Order::pending()->count();
        $this->confirmedCount = Order::byStatus('confirmed')->count();
        $this->completedToday = Order::today()->byStatus('completed')->count();

        $query = Order::with(['table', 'orderItems'])->latest();

        if ($this->filterStatus === 'all') {
            $query->whereIn('status', ['pending', 'confirmed']);
        } else {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.kasir.kasir-dashboard', [
            'orders' => $query->get(),
        ]);
    }
}
