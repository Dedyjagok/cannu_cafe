<?php

namespace App\Http\Controllers;


use App\Models\CafeTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Tampilkan halaman menu untuk customer berdasarkan QR token meja.
     * Dipanggil saat customer scan QR code.
     *
     * URL: GET /menu/{qrToken}
     */
    public function show(string $qrToken): View
    {
        $cafeTable = CafeTable::where('qr_token', $qrToken)
            ->where('is_available', true)
            ->firstOrFail();

        // Note: categories are fetched fresh inside the OrdersPage Livewire component
        // to avoid dehydration/serialization issues in production.
        return view('menu.show', compact('cafeTable'));
    }

    /**
     * Simpan pesanan baru dari customer.
     *
     * URL: POST /order
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_id'        => ['required', 'exists:tables,id'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes'        => ['nullable', 'string', 'max:255'],
        ]);

        // Buat order header
        $order = Order::create([
            'table_id'     => $validated['table_id'],
            'order_code'   => Order::generateOrderCode(),
            'status'       => 'pending',
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        // Buat order items dengan snapshot harga & nama
        foreach ($validated['items'] as $item) {
            $menuItem = MenuItem::where('id', $item['menu_item_id'])
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

            $totalAmount += $subtotal;
        }

        // Update total amount di order header
        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('order.status', ['orderCode' => $order->order_code])
            ->with('success', 'Pesanan berhasil dikirim! Silakan tunggu konfirmasi kasir.');
    }

    /**
     * Tampilkan status pesanan customer berdasarkan order code.
     *
     * URL: GET /order/{orderCode}/status
     */
    public function status(string $orderCode): View
    {
        $order = Order::where('order_code', $orderCode)
            ->with(['orderItems', 'table'])
            ->firstOrFail();

        return view('menu.status', compact('order'));
    }
}
