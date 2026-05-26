<?php

namespace App\Livewire\Kasir;

use App\Models\Order;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderList extends Component
{
    /**
     * Filter status yang ditampilkan: 'pending', 'confirmed', 'all'.
     */
    public string $filterStatus = 'pending';

    /**
     * Mount: set filter default ke pending.
     */
    public function mount(string $filterStatus = 'pending'): void
    {
        $this->filterStatus = $filterStatus;
    }

    /**
     * Refresh daftar order (dipanggil oleh OrderNotification setiap 3 detik).
     */
    #[On('refresh-order-list')]
    public function refresh(): void
    {
        // Re-render otomatis karena Livewire reaktif
    }

    /**
     * Kasir konfirmasi pesanan langsung dari list.
     */
    public function confirm(int $orderId): void
    {
        $order = Order::where('id', $orderId)->where('status', 'pending')->firstOrFail();

        $order->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $this->dispatch('order-confirmed', orderCode: $order->order_code);
        $this->dispatch('refresh-stats');
        session()->flash('success', "Pesanan #{$order->order_code} dikonfirmasi.");
    }

    /**
     * Kasir tandai pesanan selesai langsung dari list.
     */
    public function complete(int $orderId): void
    {
        $order = Order::where('id', $orderId)->where('status', 'confirmed')->firstOrFail();

        $order->update(['status' => 'completed']);

        $this->dispatch('refresh-stats');
        session()->flash('success', "Pesanan #{$order->order_code} selesai.");
    }

    /**
     * Kasir batalkan pesanan langsung dari list.
     */
    public function cancel(int $orderId): void
    {
        $order = Order::where('id', $orderId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        $order->update(['status' => 'cancelled']);

        $this->dispatch('refresh-stats');
        session()->flash('success', "Pesanan #{$order->order_code} dibatalkan.");
    }

    /**
     * Ganti filter status.
     */
    public function setFilter(string $status): void
    {
        $this->filterStatus = $status;
    }

    public function render(): View
    {
        $query = Order::with(['table', 'orderItems'])
            ->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        } else {
            // 'all' hanya tampilkan yang aktif (pending + confirmed)
            $query->whereIn('status', ['pending', 'confirmed']);
        }

        $orders = $query->get();

        return view('livewire.kasir.order-list', compact('orders'));
    }
}
