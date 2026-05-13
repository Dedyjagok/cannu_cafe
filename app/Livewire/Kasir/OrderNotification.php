<?php

namespace App\Livewire\Kasir;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Polling;

#[Polling('3s')]  // Poll setiap 3 detik untuk pesanan pending baru
class OrderNotification extends Component
{
    /**
     * Jumlah pesanan pending yang terdeteksi sebelumnya.
     * Digunakan untuk mendeteksi pesanan BARU.
     */
    public int $previousPendingCount = 0;

    /**
     * Jumlah pesanan pending saat ini.
     */
    public int $pendingCount = 0;

    /**
     * Data pesanan pending terbaru untuk notifikasi.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $newOrders = [];

    /**
     * Mount: inisialisasi jumlah pending saat komponen pertama kali dimuat.
     */
    public function mount(): void
    {
        $this->previousPendingCount = Order::pending()->count();
        $this->pendingCount         = $this->previousPendingCount;
    }

    /**
     * Dipanggil setiap 3 detik oleh Livewire polling.
     * Deteksi pesanan baru dan dispatch event browser untuk toast notifikasi.
     */
    public function checkNewOrders(): void
    {
        $currentPending = Order::pending()->count();

        if ($currentPending > $this->previousPendingCount) {
            // Ada pesanan baru! Ambil data pesanan terbaru
            $latestOrders = Order::pending()
                ->with('table')
                ->latest()
                ->limit($currentPending - $this->previousPendingCount)
                ->get();

            $this->newOrders = $latestOrders->map(fn ($order) => [
                'id'           => $order->id,
                'order_code'   => $order->order_code,
                'table_number' => $order->table->table_number,
                'total_amount' => $order->total_amount,
            ])->toArray();

            // Dispatch browser event untuk menampilkan toast notifikasi di frontend
            $this->dispatch('new-orders-arrived', orders: $this->newOrders);
        }

        $this->previousPendingCount = $currentPending;
        $this->pendingCount         = $currentPending;

        // Trigger refresh pada OrderList component
        $this->dispatch('refresh-order-list');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.kasir.order-notification');
    }
}
