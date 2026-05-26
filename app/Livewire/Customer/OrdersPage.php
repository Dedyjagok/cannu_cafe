<?php

namespace App\Livewire\Customer;

use App\Models\CafeTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrdersPage extends Component
{
    public $cafeTable;
    public $categories;
    
    // Keranjang belanja: array of items
    // format: [menu_id => ['quantity' => 1, 'notes' => '', 'menu' => [id, name, price, image]]]
    public $cart = [];
    
    // State UI
    public $isCartOpen = false;
    public $activeCategoryId = null;

    public function mount($cafeTable, $categories)
    {
        $this->cafeTable = $cafeTable;
        $this->categories = $categories;
        
        if ($this->categories->isNotEmpty()) {
            $this->activeCategoryId = $this->categories->first()->id;
        }
    }

    public function setActiveCategory($categoryId)
    {
        $this->activeCategoryId = $categoryId;
    }

    public function toggleCart()
    {
        $this->isCartOpen = !$this->isCartOpen;
    }

    public function addToCart($menuId)
    {
        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['quantity']++;
        } else {
            // Fetch menu item data to store in cart state
            $menu = MenuItem::find($menuId);
            if ($menu && $menu->is_available) {
                $this->cart[$menuId] = [
                    'quantity' => 1,
                    'notes' => '',
                    'menu' => [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'price' => $menu->price,
                        'image' => $menu->image,
                    ]
                ];
            }
        }
    }

    public function incrementQuantity($menuId)
    {
        if (isset($this->cart[$menuId])) {
            if ($this->cart[$menuId]['quantity'] < 99) {
                $this->cart[$menuId]['quantity']++;
            }
        }
    }

    public function decrementQuantity($menuId)
    {
        if (isset($this->cart[$menuId])) {
            if ($this->cart[$menuId]['quantity'] > 1) {
                $this->cart[$menuId]['quantity']--;
            } else {
                unset($this->cart[$menuId]);
                if (empty($this->cart)) {
                    $this->isCartOpen = false; // Tutup cart jika kosong
                }
            }
        }
    }

    public function updateNotes($menuId, $notes)
    {
        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['notes'] = $notes;
        }
    }

    public function getTotalPriceProperty()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['quantity'] * $item['menu']['price'];
        }
        return $total;
    }

    public function getTotalItemsProperty()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang Anda kosong.');
            return;
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'table_id'     => $this->cafeTable->id,
                'order_code'   => Order::generateOrderCode(),
                'status'       => 'pending',
                'total_amount' => $this->total_price, // Gunakan computed property
            ]);

            foreach ($this->cart as $menuId => $item) {
                // Pastikan item masih tersedia dan harga valid
                $menuItem = MenuItem::where('id', $menuId)
                    ->where('is_available', true)
                    ->firstOrFail();

                $subtotal = OrderItem::calculateSubtotal($item['quantity'], (float) $menuItem->price);

                $order->orderItems()->create([
                    'menu_item_id' => $menuItem->id,
                    'menu_name'    => $menuItem->name,       // snapshot
                    'menu_price'   => $menuItem->price,      // snapshot
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $subtotal,
                    'notes'        => $item['notes'] ?? null,
                ]);
            }

            // Optional: update total amount in header based on actual calculated sum to be safe
            // Tapi kita sudah set di awal. Kita abaikan update lagi kecuali ada logika tambahan.

            DB::commit();

            // Clear cart
            $this->cart = [];
            
            // Redirect to status page
            return redirect()->route('order.status', ['orderCode' => $order->order_code])
                ->with('success', 'Pesanan berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.customer.orders-page');
    }
}
