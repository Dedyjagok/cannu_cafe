@extends('layouts.sidebar-cashier')

@section('title', 'Riwayat Pesanan')
@section('page-title', 'Riwayat Pesanan')

{{-- ─── Extra styles ──────────────────────────────────────────── --}}
@push('styles')
<style>
    /* Status badges */
    .badge { display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:700;letter-spacing:.03em; }
    .badge-completed  { background:#d1fae5;color:#065f46; }
    .badge-cancelled  { background:#fee2e2;color:#991b1b; }
    .badge-pending    { background:#fef3c7;color:#92400e; }
    .badge-confirmed  { background:#dbeafe;color:#1e40af; }

    /* Modal */
    #order-modal { transition: opacity .2s ease; }
    #order-modal.hidden { opacity:0;pointer-events:none; }
    #order-modal:not(.hidden) { opacity:1; }
    #modal-box {
        transition: transform .25s ease, opacity .25s ease;
        transform: translateY(12px); opacity:0;
    }
    #order-modal:not(.hidden) #modal-box { transform: translateY(0); opacity:1; }

    /* Table hover */
    .history-row { cursor:pointer; transition:background .12s; }
    .history-row:hover td { background:#f0f9ff; }

    /* Print-ready summary */
    .receipt-item { display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px dashed #e5e7eb;font-size:13px; }
    .receipt-item:last-child { border-bottom:none; }
</style>
@endpush

@section('content')

{{-- ── Summary Stats ──────────────────────────────────────────── --}}
<div class="grid grid-cols-4 gap-5">

    {{-- Total Hari Ini --}}
    <div class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
        <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-emerald-50 opacity-60"></div>
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">Selesai Hari Ini</p>
            <p class="mt-2 text-4xl font-extrabold text-emerald-700">{{ $todayCompleted }}</p>
            <p class="mt-1 text-[11px] text-emerald-500">transaksi</p>
        </div>
    </div>

    {{-- Pendapatan Hari Ini --}}
    <div class="relative overflow-hidden rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm col-span-2">
        <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-cafe-50 opacity-60"></div>
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-widest text-cafe-600">Pendapatan Hari Ini</p>
            <p class="mt-2 text-4xl font-extrabold text-cafe-700">
                Rp {{ number_format($todayRevenue, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-[11px] text-cafe-400">dari pesanan completed</p>
        </div>
    </div>

    {{-- Dibatalkan Hari Ini --}}
    <div class="relative overflow-hidden rounded-2xl border border-red-200 bg-white p-5 shadow-sm">
        <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-red-50 opacity-60"></div>
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Dibatalkan</p>
            <p class="mt-2 text-4xl font-extrabold text-red-600">{{ $todayCancelled }}</p>
            <p class="mt-1 text-[11px] text-red-400">hari ini</p>
        </div>
    </div>
</div>

{{-- ── Filter Bar ─────────────────────────────────────────────── --}}
<div class="rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm">
    <form method="GET" action="{{ route('kasir.history') }}"
          class="flex flex-wrap items-end gap-4">

        {{-- Date From --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-cafe-600 uppercase tracking-wide">Dari Tanggal</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="rounded-lg border border-cafe-200 bg-cafe-50 px-3 py-2 text-sm text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-400">
        </div>

        {{-- Date To --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-cafe-600 uppercase tracking-wide">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="rounded-lg border border-cafe-200 bg-cafe-50 px-3 py-2 text-sm text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-400">
        </div>

        {{-- Status --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-cafe-600 uppercase tracking-wide">Status</label>
            <select name="status"
                    class="rounded-lg border border-cafe-200 bg-cafe-50 px-3 py-2 text-sm text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-400">
                <option value="all"       {{ $statusFilter === 'all'       ? 'selected' : '' }}>Semua</option>
                <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                <option value="pending"   {{ $statusFilter === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $statusFilter === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
            </select>
        </div>

        {{-- Search --}}
        <div class="flex flex-1 flex-col gap-1 min-w-[200px]">
            <label class="text-xs font-semibold text-cafe-600 uppercase tracking-wide">Cari Kode / Meja</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="cth: ORD-20260527 atau Meja 3"
                   class="rounded-lg border border-cafe-200 bg-cafe-50 px-3 py-2 text-sm text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-400">
        </div>

        {{-- Buttons --}}
        <div class="flex items-end gap-2">
            <button type="submit"
                    class="rounded-lg bg-cafe-700 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cafe-800 transition">
                Filter
            </button>
            <a href="{{ route('kasir.history') }}"
               class="rounded-lg border border-cafe-200 bg-white px-4 py-2 text-sm font-semibold text-cafe-600 shadow-sm hover:bg-cafe-50 transition">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- ── Orders Table ───────────────────────────────────────────── --}}
<div class="overflow-hidden rounded-2xl border border-cafe-200 bg-white shadow-sm">

    {{-- Table header info --}}
    <div class="flex items-center justify-between border-b border-cafe-100 px-6 py-4">
        <div>
            <p class="text-sm font-bold text-cafe-800">Daftar Riwayat Pesanan</p>
            <p class="text-xs text-cafe-400 mt-0.5">
                {{ $orders->total() }} transaksi ditemukan
                @if($from === $to)
                    pada {{ \Carbon\Carbon::parse($from)->translatedFormat('d F Y') }}
                @else
                    · {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }}
                    s/d {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                @endif
            </p>
        </div>
        <span class="rounded-full bg-cafe-100 px-3 py-1 text-xs font-bold text-cafe-700">
            Klik baris untuk detail
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-cafe-50 text-left">
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Order Code</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Meja</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Items</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Total</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Status</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Waktu</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-cafe-500">Dikonfirmasi Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cafe-50">
                @forelse ($orders as $order)
                    <tr class="history-row"
                        onclick="openModal({{ json_encode([
                            'id'           => $order->id,
                            'order_code'   => $order->order_code,
                            'table'        => $order->table->table_number,
                            'status'       => $order->status,
                            'total'        => $order->total_amount,
                            'created_at'   => $order->created_at->translatedFormat('d M Y, H:i'),
                            'confirmed_at' => $order->confirmed_at?->translatedFormat('d M Y, H:i') ?? '-',
                            'confirmed_by' => $order->confirmedBy?->name ?? '-',
                            'items'        => $order->orderItems->map(fn($i) => [
                                'name'     => $i->menu_name,
                                'qty'      => $i->quantity,
                                'price'    => $i->menu_price,
                                'subtotal' => $i->subtotal,
                                'notes'    => $i->notes,
                            ])->toArray(),
                        ]) }})">

                        <td class="px-6 py-3.5 font-mono text-cafe-700 font-semibold">
                            {{ $order->order_code }}
                        </td>
                        <td class="px-6 py-3.5 text-cafe-700">
                            <span class="rounded-lg bg-cafe-100 px-2.5 py-1 text-xs font-bold text-cafe-700">
                                Meja {{ $order->table->table_number }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-cafe-600">
                            {{ $order->orderItems->count() }} item
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-cafe-800">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3.5">
                            @php
                                $label = match($order->status) {
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    'confirmed' => 'Dikonfirmasi',
                                    default     => 'Pending',
                                };
                            @endphp
                            <span class="badge badge-{{ $order->status }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-cafe-500 text-xs">
                            {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-3.5 text-cafe-500 text-xs">
                            {{ $order->confirmedBy?->name ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-cafe-400">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-12 w-12 text-cafe-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm font-medium">Tidak ada riwayat pesanan ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($orders->hasPages())
        <div class="border-t border-cafe-100 px-6 py-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════
     ORDER DETAIL MODAL
═══════════════════════════════════════════════════════════════ --}}
<div id="order-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
     onclick="closeModalOutside(event)">

    <div id="modal-box"
         class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl"
         onclick="event.stopPropagation()">

        {{-- Modal Header --}}
        <div class="flex items-start justify-between border-b border-cafe-100 px-6 py-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-cafe-400">Detail Pesanan</p>
                <h2 class="mt-0.5 text-lg font-extrabold text-cafe-800" id="modal-order-code">—</h2>
            </div>
            <button onclick="closeModal()"
                    class="rounded-lg p-1.5 text-cafe-400 hover:bg-cafe-100 hover:text-cafe-700 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Order Meta --}}
        <div class="grid grid-cols-2 gap-3 border-b border-cafe-100 px-6 py-4 text-sm">
            <div>
                <p class="text-xs text-cafe-400 font-medium">Nomor Meja</p>
                <p class="mt-0.5 font-bold text-cafe-800" id="modal-table">—</p>
            </div>
            <div>
                <p class="text-xs text-cafe-400 font-medium">Status</p>
                <p class="mt-0.5" id="modal-status">—</p>
            </div>
            <div>
                <p class="text-xs text-cafe-400 font-medium">Waktu Pesan</p>
                <p class="mt-0.5 font-semibold text-cafe-700" id="modal-created">—</p>
            </div>
            <div>
                <p class="text-xs text-cafe-400 font-medium">Dikonfirmasi Oleh</p>
                <p class="mt-0.5 font-semibold text-cafe-700" id="modal-confirmed-by">—</p>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="px-6 py-4">
            <p class="mb-3 text-xs font-bold uppercase tracking-widest text-cafe-500">Item Pesanan</p>
            <div id="modal-items" class="space-y-0.5 max-h-64 overflow-y-auto pr-1">
                {{-- Filled by JS --}}
            </div>
        </div>

        {{-- Total --}}
        <div class="flex items-center justify-between rounded-xl bg-cafe-50 mx-6 mb-5 px-5 py-3.5">
            <p class="text-sm font-bold text-cafe-700">Total Pembayaran</p>
            <p class="text-lg font-extrabold text-cafe-800" id="modal-total">—</p>
        </div>
    </div>
</div>

@endsection

{{-- ─── Scripts ─────────────────────────────────────────────────── --}}
@push('scripts')
<script>
    const statusLabel = {
        completed: 'Selesai',
        cancelled: 'Dibatalkan',
        pending:   'Pending',
        confirmed: 'Dikonfirmasi',
    };
    const statusClass = {
        completed: 'badge-completed',
        cancelled: 'badge-cancelled',
        pending:   'badge-pending',
        confirmed: 'badge-confirmed',
    };

    function formatRp(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID');
    }

    function openModal(order) {
        // Meta
        document.getElementById('modal-order-code').textContent = order.order_code;
        document.getElementById('modal-table').textContent      = 'Meja ' + order.table;
        document.getElementById('modal-created').textContent    = order.created_at;
        document.getElementById('modal-confirmed-by').textContent = order.confirmed_by;
        document.getElementById('modal-total').textContent      = formatRp(order.total);

        // Status badge
        const statusEl = document.getElementById('modal-status');
        statusEl.innerHTML =
            `<span class="badge ${statusClass[order.status] || ''}">${statusLabel[order.status] || order.status}</span>`;

        // Items
        const itemsEl = document.getElementById('modal-items');
        if (!order.items || order.items.length === 0) {
            itemsEl.innerHTML = '<p class="text-xs text-cafe-400">Tidak ada item.</p>';
        } else {
            itemsEl.innerHTML = order.items.map(item => `
                <div class="receipt-item">
                    <div class="flex-1 pr-2">
                        <span class="font-semibold text-cafe-800">${item.qty}× ${item.name}</span>
                        ${item.notes ? `<br><span class="text-[11px] text-cafe-400 italic">${item.notes}</span>` : ''}
                    </div>
                    <span class="text-cafe-700 font-medium whitespace-nowrap">${formatRp(item.subtotal)}</span>
                </div>
            `).join('');
        }

        // Show modal
        const modal = document.getElementById('order-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('order-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function closeModalOutside(event) {
        if (event.target === document.getElementById('order-modal')) {
            closeModal();
        }
    }

    // ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
