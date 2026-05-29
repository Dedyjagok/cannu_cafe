<?php

namespace App\Livewire\Customer;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrdersPage extends Component
{
    // ── Store only primitive IDs, NOT Eloquent models ─────────────────
    // Livewire serializes public properties between requests.
    // Eloquent models / collections with relationships do NOT survive
    // dehydration correctly in production — store IDs and re-query in render().

    public int   $tableId;
    public ?int  $activeCategoryId = null;

    // Cart: [menu_id => ['quantity' => 1, 'notes' => '', 'menu' => [...]]]
    public array $cart = [];

    // UI State
    public bool $isCartOpen = false;

    // ── Lifecycle ──────────────────────────────────────────────────────

    public function mount(CafeTable $cafeTable): void
    {
        $this->tableId = $cafeTable->id;

        // Set first category as default active
        $firstCategory = Category::whereHas('menuItems', fn ($q) => $q->where('is_available', true))
            ->orderBy('name')
            ->first();

        if ($firstCategory) {
            $this->activeCategoryId = $firstCategory->id;
        }
    }

    // ── Actions ───────────────────────────────────────────────────────

    public function setActiveCategory(int $categoryId): void
    {
        $this->activeCategoryId = $categoryId;
    }

    public function toggleCart(): void
    {
        $this->isCartOpen = !$this->isCartOpen;
    }

    public function addToCart(int $menuId): void
    {
        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['quantity']++;
        } else {
            $menu = MenuItem::where('id', $menuId)->where('is_available', true)->first();
            if ($menu) {
                $this->cart[$menuId] = [
                    'quantity' => 1,
                    'notes'    => '',
                    'menu'     => [
                        'id'    => $menu->id,
                        'name'  => $menu->name,
                        'price' => (float) $menu->price,
                        'image' => $menu->image,
                    ],
                ];
            }
        }
    }

    public function incrementQuantity(int $menuId): void
    {
        if (isset($this->cart[$menuId]) && $this->cart[$menuId]['quantity'] < 99) {
            $this->cart[$menuId]['quantity']++;
        }
    }

    public function decrementQuantity(int $menuId): void
    {
        if (!isset($this->cart[$menuId])) return;

        if ($this->cart[$menuId]['quantity'] > 1) {
            $this->cart[$menuId]['quantity']--;
        } else {
            unset($this->cart[$menuId]);
            if (empty($this->cart)) {
                $this->isCartOpen = false;
            }
        }
    }

    public function updateNotes(int $menuId, string $notes): void
    {
        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['notes'] = $notes;
        }
    }

    // ── Computed Properties ───────────────────────────────────────────

    public function getTotalPriceProperty(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['quantity'] * $item['menu']['price']);
    }

    public function getTotalItemsProperty(): int
    {
        return collect($this->cart)->sum(fn ($item) => $item['quantity']);
    }

    // ── Checkout ──────────────────────────────────────────────────────

    public function checkout(): mixed
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang Anda kosong.');
            return null;
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'table_id'     => $this->tableId,
                'order_code'   => Order::generateOrderCode(),
                'status'       => 'pending',
                'total_amount' => $this->total_price,
            ]);

            foreach ($this->cart as $menuId => $item) {
                $menuItem = MenuItem::where('id', $menuId)
                    ->where('is_available', true)
                    ->firstOrFail();

                $order->orderItems()->create([
                    'menu_item_id' => $menuItem->id,
                    'menu_name'    => $menuItem->name,
                    'menu_price'   => $menuItem->price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => OrderItem::calculateSubtotal($item['quantity'], (float) $menuItem->price),
                    'notes'        => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            $this->cart       = [];
            $this->isCartOpen = false;

            return redirect()->route('order.status', ['orderCode' => $order->order_code])
                ->with('success', 'Pesanan berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
            return null;
        }
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        // Re-query fresh on every render — do NOT rely on stored model properties
        $cafeTable  = CafeTable::findOrFail($this->tableId);
        $categories = Category::with(['menuItems' => fn ($q) => $q->where('is_available', true)->orderBy('name')])
            ->whereHas('menuItems', fn ($q) => $q->where('is_available', true))
            ->orderBy('name')
            ->get();

        return view('livewire.customer.orders-page', compact('cafeTable', 'categories'));
    }
}
