<div class="order-cart">
    {{-- Badge jumlah item --}}
    @if ($this->cartCount > 0)
        <div class="cart-badge">
            🛒 <span class="cart-count">{{ $this->cartCount }}</span> item
            — Rp {{ number_format($totalAmount, 0, ',', '.') }}
        </div>
    @endif

    {{-- Flash error --}}
    @if (session()->has('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    {{-- Daftar item di keranjang --}}
    @if (!empty($cart))
        <div class="cart-items">
            @foreach ($cart as $id => $item)
                <div class="cart-item" id="cart-item-{{ $id }}">
                    <div class="item-info">
                        <span class="item-name">{{ $item['name'] }}</span>
                        <span class="item-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                    </div>

                    <div class="item-controls">
                        <button wire:click="decreaseItem({{ $id }})" class="btn-qty">−</button>
                        <span class="item-qty">{{ $item['quantity'] }}</span>
                        <button wire:click="addItem({{ $id }})" class="btn-qty">+</button>
                        <button wire:click="removeItem({{ $id }})" class="btn-remove">🗑</button>
                    </div>

                    {{-- Catatan per item --}}
                    <input type="text"
                           wire:change="updateNotes({{ $id }}, $event.target.value)"
                           value="{{ $item['notes'] }}"
                           placeholder="Catatan (opsional, contoh: tanpa bawang)"
                           class="item-notes-input"
                           maxlength="255">

                    <div class="item-subtotal">
                        Subtotal: Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <strong>Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
            </div>

            <div class="cart-actions">
                <button wire:click="clearCart" wire:confirm="Kosongkan keranjang?"
                        class="btn btn-secondary">
                    Kosongkan
                </button>
                <button wire:click="placeOrder" wire:confirm="Kirim pesanan ke kasir?"
                        class="btn btn-primary">
                    🚀 Pesan Sekarang
                </button>
            </div>

            <p class="cart-note text-sm text-gray-500 mt-2">
                * Pembayaran dilakukan ke kasir setelah selesai makan.
            </p>
        </div>
    @else
        <div class="cart-empty">
            <p>Keranjang kosong. Pilih menu di atas untuk menambahkan.</p>
        </div>
    @endif
</div>
