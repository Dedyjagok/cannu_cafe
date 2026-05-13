<?php

namespace App\Livewire\Customer;

use App\Models\CafeTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\View\View;
use Livewire\Component;

class OrderCart extends Component
{
    /**
     * ID meja yang sedang aktif.
     */
    public int $tableId;

    /**
     * Nomor meja (untuk display).
     */
    public string $tableNumber = '';

    /**
     * Isi keranjang belanja.
     * Format: [ menu_item_id => ['name', 'price', 'quantity', 'notes'] ]
     *
     * @var array<int, array<string, mixed>>
     */
    public array $cart = [];

    /**
     * Total harga keranjang.
     */
    public float $totalAmount = 0;

    /**
     * Status order yang baru dibuat (untuk redirect ke halaman status).
     */
    public ?string $orderCode = null;

    /**
     * Inisialisasi komponen dengan data meja.
     */
    public function mount(CafeTable $cafeTable): void
    {
        $this->tableId     = $cafeTable->id;
        $this->tableNumber = $cafeTable->table_number;
    }

    /**
     * Tambah satu item ke keranjang.
     */
    public function addItem(int $menuItemId): void
    {
        $menuItem = MenuItem::available()->find($menuItemId);

        if (! $menuItem) {
            session()->flash('error', 'Menu tidak tersedia.');
            return;
        }

        if (isset($this->cart[$menuItemId])) {
            $this->cart[$menuItemId]['quantity']++;
        } else {
            $this->cart[$menuItemId] = [
                'menu_item_id' => $menuItem->id,
                'name'         => $menuItem->name,
                'price'        => (float) $menuItem->price,
                'quantity'     => 1,
                'notes'        => '',
            ];
        }

        $this->recalculateTotal();
    }

    /**
     * Kurangi quantity item di keranjang.
     */
    public function decreaseItem(int $menuItemId): void
    {
        if (! isset($this->cart[$menuItemId])) {
            return;
        }

        if ($this->cart[$menuItemId]['quantity'] <= 1) {
            $this->removeItem($menuItemId);
            return;
        }

        $this->cart[$menuItemId]['quantity']--;
        $this->recalculateTotal();
    }

    /**
     * Hapus item dari keranjang.
     */
    public function removeItem(int $menuItemId): void
    {
        unset($this->cart[$menuItemId]);
        $this->recalculateTotal();
    }

    /**
     * Update catatan per item.
     */
    public function updateNotes(int $menuItemId, string $notes): void
    {
        if (isset($this->cart[$menuItemId])) {
            $this->cart[$menuItemId]['notes'] = substr($notes, 0, 255);
        }
    }

    /**
     * Kosongkan seluruh keranjang.
     */
    public function clearCart(): void
    {
        $this->cart        = [];
        $this->totalAmount = 0;
    }

    /**
     * Submit pesanan ke database.
     * Membuat Order dan OrderItems, lalu redirect ke halaman status.
     */
    public function placeOrder(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja masih kosong.');
            return;
        }

        // Buat order header
        $order = Order::create([
            'table_id'     => $this->tableId,
            'order_code'   => Order::generateOrderCode(),
            'status'       => 'pending',
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        // Buat order items
        foreach ($this->cart as $item) {
            $menuItem = MenuItem::where('id', $item['menu_item_id'])
                ->where('is_available', true)
                ->first();

            if (! $menuItem) {
                continue; // Skip jika menu sudah tidak tersedia
            }

            $subtotal = OrderItem::calculateSubtotal($item['quantity'], (float) $menuItem->price);

            $order->orderItems()->create([
                'menu_item_id' => $menuItem->id,
                'menu_name'    => $menuItem->name,
                'menu_price'   => $menuItem->price,
                'quantity'     => $item['quantity'],
                'subtotal'     => $subtotal,
                'notes'        => $item['notes'] ?? null,
            ]);

            $totalAmount += $subtotal;
        }

        $order->update(['total_amount' => $totalAmount]);

        $this->orderCode = $order->order_code;
        $this->clearCart();

        // Redirect ke halaman status pesanan
        $this->redirect(route('order.status', ['orderCode' => $order->order_code]));
    }

    /**
     * Hitung ulang total keranjang.
     */
    private function recalculateTotal(): void
    {
        $this->totalAmount = collect($this->cart)
            ->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    /**
     * Jumlah total item dalam keranjang.
     */
    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function render(): View
    {
        return view('livewire.customer.order-cart');
    }
}
