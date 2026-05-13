<div class="order-list">
    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-4">
        <button wire:click="setFilter('pending')"
                class="{{ $filterStatus === 'pending' ? 'btn-active' : 'btn-outline' }}">
            Pending
        </button>
        <button wire:click="setFilter('confirmed')"
                class="{{ $filterStatus === 'confirmed' ? 'btn-active' : 'btn-outline' }}">
            Confirmed
        </button>
        <button wire:click="setFilter('all')"
                class="{{ $filterStatus === 'all' ? 'btn-active' : 'btn-outline' }}">
            Semua Aktif
        </button>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Daftar Order --}}
    @forelse ($orders as $order)
        <div class="order-card" id="order-{{ $order->id }}">
            <div class="order-header">
                <span class="order-code">{{ $order->order_code }}</span>
                <span class="badge badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span>
                <span class="table-number">Meja {{ $order->table->table_number }}</span>
                <span class="total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>

            {{-- Detail Item --}}
            <ul class="order-items">
                @foreach ($order->orderItems as $item)
                    <li>
                        {{ $item->quantity }}× {{ $item->menu_name }}
                        <span class="text-gray-400">({{ $item->formatted_menu_price }})</span>
                        @if ($item->notes)
                            <em class="text-sm text-gray-500"> — {{ $item->notes }}</em>
                        @endif
                    </li>
                @endforeach
            </ul>

            {{-- Action Buttons --}}
            <div class="order-actions">
                @if ($order->status === 'pending')
                    <button wire:click="confirm({{ $order->id }})" wire:confirm="Konfirmasi pesanan ini?"
                            class="btn btn-confirm">
                        ✓ Konfirmasi
                    </button>
                    <button wire:click="cancel({{ $order->id }})" wire:confirm="Batalkan pesanan ini?"
                            class="btn btn-cancel">
                        ✗ Batalkan
                    </button>
                @elseif ($order->status === 'confirmed')
                    <button wire:click="complete({{ $order->id }})" wire:confirm="Tandai pesanan selesai?"
                            class="btn btn-complete">
                        ✓ Selesai
                    </button>
                    <button wire:click="cancel({{ $order->id }})" wire:confirm="Batalkan pesanan ini?"
                            class="btn btn-cancel">
                        ✗ Batalkan
                    </button>
                @endif
            </div>

            <div class="text-xs text-gray-400 mt-1">
                {{ $order->created_at->diffForHumans() }}
            </div>
        </div>
    @empty
        <div class="empty-state">
            <p>Tidak ada pesanan {{ $filterStatus === 'all' ? 'aktif' : $filterStatus }} saat ini.</p>
        </div>
    @endforelse
</div>
