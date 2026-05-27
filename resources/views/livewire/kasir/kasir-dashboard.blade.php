<div wire:poll.3000ms="refreshDashboard">

    {{-- ══════════════════════════════════════════════════════════
         STAT CARDS — reactive, updated every 3s via polling
    ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-5">

        {{-- Pending --}}
        <div class="relative overflow-hidden rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition-all duration-300">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-amber-50 opacity-70"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-widest text-amber-600">Pesanan Pending</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-700">
                        {{ $pendingCount }}
                    </span>
                </div>
                <p class="mt-2 text-4xl font-extrabold text-amber-700">{{ $pendingCount }}</p>
                <p class="mt-1 text-[11px] text-amber-500">menunggu konfirmasi</p>
            </div>
            <div class="mt-3 h-1 w-full rounded-full bg-amber-100">
                <div class="h-1 rounded-full bg-amber-400 transition-all duration-500"
                     style="width: {{ min(100, $pendingCount * 10) }}%"></div>
            </div>
        </div>

        {{-- Dikonfirmasi --}}
        <div class="relative overflow-hidden rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm transition-all duration-300">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-cafe-50 opacity-70"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-widest text-cafe-600">Dikonfirmasi</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cafe-100 text-sm font-bold text-cafe-700">
                        {{ $confirmedCount }}
                    </span>
                </div>
                <p class="mt-2 text-4xl font-extrabold text-cafe-700">{{ $confirmedCount }}</p>
                <p class="mt-1 text-[11px] text-cafe-400">sedang diproses</p>
            </div>
            <div class="mt-3 h-1 w-full rounded-full bg-cafe-100">
                <div class="h-1 rounded-full bg-cafe-500 transition-all duration-500"
                     style="width: {{ min(100, $confirmedCount * 10) }}%"></div>
            </div>
        </div>

        {{-- Selesai Hari Ini --}}
        <div class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition-all duration-300">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-emerald-50 opacity-70"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">Selesai Hari Ini</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                        {{ $completedToday }}
                    </span>
                </div>
                <p class="mt-2 text-4xl font-extrabold text-emerald-700">{{ $completedToday }}</p>
                <p class="mt-1 text-[11px] text-emerald-500">transaksi selesai</p>
            </div>
            <div class="mt-3 h-1 w-full rounded-full bg-emerald-100">
                <div class="h-1 rounded-full bg-emerald-500 transition-all duration-500"
                     style="width: {{ min(100, $completedToday * 5) }}%"></div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         NOTIFICATION BANNER — shown via JS on new-orders-arrived
    ══════════════════════════════════════════════════════════ --}}
    <div id="notif-banner"
         class="hidden items-center gap-3 rounded-xl border border-amber-300 bg-amber-50 px-5 py-3.5 shadow-sm">
        <span class="animate-bounce text-xl">🔔</span>
        <span id="notif-text" class="flex-1 text-sm font-semibold text-amber-800"></span>
        <button onclick="document.getElementById('notif-banner').classList.replace('flex','hidden')"
                class="rounded-lg p-1 text-amber-400 transition hover:bg-amber-100 hover:text-amber-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FLASH MESSAGE
    ══════════════════════════════════════════════════════════ --}}
    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-800">
            <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         ORDER TABLE CARD
    ══════════════════════════════════════════════════════════ --}}
    <div class="overflow-hidden rounded-2xl border border-cafe-200 bg-white shadow-sm">

        {{-- Table header with filter tabs --}}
        <div class="flex items-center justify-between border-b border-cafe-100 px-6 py-4">
            <div>
                <p class="text-sm font-bold text-cafe-800">Daftar Pesanan Aktif</p>
                <p class="mt-0.5 text-xs text-cafe-400">
                    Live update setiap 3 detik
                    <span class="ml-1 inline-flex h-1.5 w-1.5 animate-ping rounded-full bg-emerald-400"></span>
                </p>
            </div>
            <div class="flex gap-2">
                <button wire:click="setFilter('all')"
                        class="rounded-full border px-4 py-1.5 text-xs font-semibold transition
                               {{ $filterStatus === 'all' ? 'border-cafe-800 bg-cafe-800 text-white' : 'border-cafe-200 bg-white text-cafe-600 hover:border-cafe-400' }}">
                    Semua Aktif
                </button>
                <button wire:click="setFilter('pending')"
                        class="rounded-full border px-4 py-1.5 text-xs font-semibold transition
                               {{ $filterStatus === 'pending' ? 'border-amber-600 bg-amber-600 text-white' : 'border-cafe-200 bg-white text-cafe-600 hover:border-amber-400' }}">
                    Pending
                </button>
                <button wire:click="setFilter('confirmed')"
                        class="rounded-full border px-4 py-1.5 text-xs font-semibold transition
                               {{ $filterStatus === 'confirmed' ? 'border-cafe-600 bg-cafe-600 text-white' : 'border-cafe-200 bg-white text-cafe-600 hover:border-cafe-400' }}">
                    Dikonfirmasi
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-cafe-50 text-left">
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Order Code</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Meja</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Item Count</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Total</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Status</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Waktu</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cafe-50">
                    @forelse ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}"
                            class="group transition-colors duration-150 hover:bg-cafe-50/60">

                            {{-- Order Code --}}
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-cafe-700">
                                {{ $order->order_code }}
                            </td>

                            {{-- Meja --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-lg bg-cafe-100 px-2.5 py-1 text-xs font-bold text-cafe-700">
                                    Meja {{ $order->table->table_number }}
                                </span>
                            </td>

                            {{-- Item Count --}}
                            <td class="px-6 py-4 text-cafe-600">
                                {{ $order->orderItems->count() }} item
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4 font-semibold text-cafe-800">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = match($order->status) {
                                        'pending'   => ['label' => 'Pending',       'class' => 'bg-amber-100 text-amber-700'],
                                        'confirmed' => ['label' => 'Dikonfirmasi',  'class' => 'bg-cafe-100 text-cafe-700'],
                                        'completed' => ['label' => 'Selesai',       'class' => 'bg-emerald-100 text-emerald-700'],
                                        'cancelled' => ['label' => 'Dibatalkan',    'class' => 'bg-red-100 text-red-700'],
                                        default     => ['label' => $order->status,  'class' => 'bg-gray-100 text-gray-700'],
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>

                            {{-- Waktu --}}
                            <td class="px-6 py-4 text-xs text-cafe-400">
                                {{ $order->created_at->diffForHumans() }}
                            </td>

                            {{-- Action Buttons --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if ($order->status === 'pending')
                                        <button
                                            wire:click="confirm({{ $order->id }})"
                                            wire:confirm="Konfirmasi pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-200 active:scale-95 disabled:opacity-50">
                                            ✓ Konfirmasi
                                        </button>
                                        <button
                                            wire:click="cancel({{ $order->id }})"
                                            wire:confirm="Batalkan pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-200 active:scale-95 disabled:opacity-50">
                                            ✕ Batal
                                        </button>

                                    @elseif ($order->status === 'confirmed')
                                        <button
                                            wire:click="complete({{ $order->id }})"
                                            wire:confirm="Tandai selesai pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-cafe-100 px-3 py-1.5 text-xs font-bold text-cafe-700 transition hover:bg-cafe-200 active:scale-95 disabled:opacity-50">
                                            ✓ Selesai
                                        </button>
                                        <button
                                            wire:click="cancel({{ $order->id }})"
                                            wire:confirm="Batalkan pesanan {{ $order->order_code }}?"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-200 active:scale-95 disabled:opacity-50">
                                            ✕ Batal
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-cafe-300">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm font-medium text-cafe-400">
                                        Tidak ada pesanan
                                        @if ($filterStatus !== 'all') {{ $filterStatus }} @else aktif @endif
                                        saat ini
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Notification sound element --}}
    <audio id="notif-sound" preload="auto" style="display:none">
        <source src="{{ asset('storage/sound/notification_order_cashier.mp3') }}" type="audio/mpeg">
    </audio>

</div>

