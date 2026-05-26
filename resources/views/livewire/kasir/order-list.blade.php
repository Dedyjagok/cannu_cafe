<div>
    {{-- Card wrapper --}}
    <div class="overflow-hidden rounded-2xl border border-cafe-200 bg-white shadow-sm">

        {{-- Card Header --}}
        <div class="flex items-center justify-between border-b border-cafe-100 bg-cafe-50 px-6 py-4">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-cafe-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h2 class="text-sm font-bold text-cafe-800">Daftar Pesanan Aktif</h2>
            </div>

            {{-- Filter Tabs --}}
            <div class="flex gap-1.5">
                <button wire:click="setFilter('all')"
                        class="rounded-full px-3.5 py-1 text-xs font-semibold transition-all duration-150
                               {{ $filterStatus === 'all'
                                    ? 'bg-cafe-700 text-white shadow-sm'
                                    : 'border border-cafe-200 text-cafe-500 hover:border-cafe-400 hover:text-cafe-700' }}">
                    Semua Aktif
                </button>
                <button wire:click="setFilter('pending')"
                        class="rounded-full px-3.5 py-1 text-xs font-semibold transition-all duration-150
                               {{ $filterStatus === 'pending'
                                    ? 'bg-amber-500 text-white shadow-sm'
                                    : 'border border-cafe-200 text-cafe-500 hover:border-amber-400 hover:text-amber-600' }}">
                    Pending
                </button>
                <button wire:click="setFilter('confirmed')"
                        class="rounded-full px-3.5 py-1 text-xs font-semibold transition-all duration-150
                               {{ $filterStatus === 'confirmed'
                                    ? 'bg-cafe-500 text-white shadow-sm'
                                    : 'border border-cafe-200 text-cafe-500 hover:border-cafe-400 hover:text-cafe-700' }}">
                    Dikonfirmasi
                </button>
            </div>
        </div>

        {{-- Flash inside component --}}
        @if (session()->has('success'))
            <div class="mx-6 mt-4 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-800">
                <svg class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-cafe-100">
                        <th class="px-6 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">
                            Order Code
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">
                            Meja
                        </th>
                        <th class="px-4 py-3.5 text-center text-[11px] font-bold uppercase tracking-widest text-cafe-400">
                            Item
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">
                            Total
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">
                            Status
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cafe-50">

                    @forelse ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}"
                            class="group transition-colors duration-100 hover:bg-cafe-50/60">

                            {{-- Order Code --}}
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-semibold text-cafe-600 tracking-wide">
                                    {{ $order->order_code }}
                                </span>
                                <p class="mt-0.5 text-[10px] text-cafe-300">
                                    {{ $order->created_at->diffForHumans() }}
                                </p>
                            </td>

                            {{-- Meja --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full
                                             bg-cafe-100 text-xs font-bold text-cafe-700">
                                    {{ $order->table->table_number }}
                                </span>
                            </td>

                            {{-- Item Count --}}
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center justify-center rounded-full bg-cafe-50
                                             border border-cafe-200 px-2.5 py-0.5 text-xs font-semibold text-cafe-600">
                                    {{ $order->orderItems->count() }}
                                </span>
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-4">
                                <span class="font-semibold text-cafe-800">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-4">
                                @if ($order->status === 'pending')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100
                                                 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @elseif ($order->status === 'confirmed')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-cafe-100
                                                 px-2.5 py-1 text-[11px] font-semibold text-cafe-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-cafe-500"></span>
                                        Dikonfirmasi
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    @if ($order->status === 'pending')
                                        {{-- Konfirmasi --}}
                                        <button
                                            wire:click="confirm({{ $order->id }})"
                                            wire:confirm="Konfirmasi pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-semibold
                                                   text-emerald-700 transition hover:bg-emerald-200
                                                   active:scale-95 disabled:opacity-60">
                                            ✓ Konfirmasi
                                        </button>
                                        {{-- Batal --}}
                                        <button
                                            wire:click="cancel({{ $order->id }})"
                                            wire:confirm="Batalkan pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold
                                                   text-red-600 border border-red-100 transition hover:bg-red-100
                                                   active:scale-95 disabled:opacity-60">
                                            ✗ Batal
                                        </button>

                                    @elseif ($order->status === 'confirmed')
                                        {{-- Selesai --}}
                                        <button
                                            wire:click="complete({{ $order->id }})"
                                            wire:confirm="Tandai selesai pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-cafe-100 px-3 py-1.5 text-xs font-semibold
                                                   text-cafe-700 border border-cafe-200 transition hover:bg-cafe-200
                                                   active:scale-95 disabled:opacity-60">
                                            ✓ Selesai
                                        </button>
                                        {{-- Batal --}}
                                        <button
                                            wire:click="cancel({{ $order->id }})"
                                            wire:confirm="Batalkan pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold
                                                   text-red-600 border border-red-100 transition hover:bg-red-100
                                                   active:scale-95 disabled:opacity-60">
                                            ✗ Batal
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-cafe-100">
                                        <svg class="h-7 w-7 text-cafe-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-cafe-400">
                                        Tidak ada pesanan
                                        {{ $filterStatus !== 'all' ? $filterStatus : 'aktif' }}
                                        saat ini
                                    </p>
                                    <p class="text-xs text-cafe-300">Pesanan baru akan muncul otomatis</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Footer count --}}
        @if ($orders->count() > 0)
            <div class="border-t border-cafe-100 bg-cafe-50 px-6 py-2.5 text-right">
                <span class="text-xs text-cafe-400">
                    Menampilkan {{ $orders->count() }} pesanan
                </span>
            </div>
        @endif
    </div>
</div>
