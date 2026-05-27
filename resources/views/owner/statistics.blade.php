<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Penjualan — Owner Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-cafe-50 font-sans antialiased text-cafe-800">
<div class="flex min-h-screen">

    {{-- ═══════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════ --}}
    @include('layouts.sidebar-owner')

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════ --}}
    <div class="ml-60 flex-1 flex flex-col min-h-screen">
        
        {{-- Top Bar --}}
        <header class="sticky top-0 z-10 flex items-center justify-between border-b border-cafe-200 bg-white/90 px-8 py-4 shadow-sm backdrop-blur">
            <div>
                <nav class="flex text-xs font-medium text-cafe-400 mb-1" aria-label="Breadcrumb">
                    <span class="hover:text-cafe-700">Owner</span>
                    <span class="mx-2">/</span>
                    <span class="text-cafe-700 font-semibold">Statistik Penjualan</span>
                </nav>
                <h1 class="text-xl font-bold text-cafe-800">Panel Owner - Statistik Penjualan</h1>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-cafe-200 bg-cafe-50 px-4 py-2 shadow-sm">
                <svg class="h-4 w-4 text-cafe-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A8 8 0 1117.804 5.12 8 8 0 015.12 17.804z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-semibold text-cafe-700">{{ auth()->user()->name }}</span>
            </div>
        </header>

        {{-- Page Body --}}
        <main class="flex-1 px-8 py-6 space-y-6">

            {{-- ── Filter Section ── --}}
            <div class="rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('owner.statistics') }}" class="flex items-end gap-4">
                    <div>
                        <label for="from" class="block text-xs font-semibold uppercase tracking-widest text-cafe-500 mb-1">From Date</label>
                        <input type="date" name="from" id="from" value="{{ $from }}" 
                               class="block w-40 rounded-lg border-cafe-300 py-2 text-sm text-cafe-800 focus:border-cafe-500 focus:ring-cafe-500 shadow-sm">
                    </div>
                    <div>
                        <label for="to" class="block text-xs font-semibold uppercase tracking-widest text-cafe-500 mb-1">To Date</label>
                        <input type="date" name="to" id="to" value="{{ $to }}" 
                               class="block w-40 rounded-lg border-cafe-300 py-2 text-sm text-cafe-800 focus:border-cafe-500 focus:ring-cafe-500 shadow-sm">
                    </div>
                    <button type="submit" class="rounded-lg bg-cafe-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cafe-700">
                        Filter
                    </button>
                    <a href="{{ route('owner.statistics') }}" class="rounded-lg border border-cafe-300 bg-white px-4 py-2.5 text-sm font-semibold text-cafe-600 transition hover:bg-cafe-50">
                        Reset
                    </a>
                </form>
            </div>

            {{-- ── KPI Cards ── --}}
            <div class="grid grid-cols-4 gap-5">
                {{-- Total Pendapatan --}}
                <div class="rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm text-center flex flex-col justify-center items-center">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-cafe-500 mb-2">Total Pendapatan</p>
                    <p class="text-3xl font-extrabold text-emerald-700">Rp {{ number_format($kpiStats['total_revenue'], 0, ',', '.') }}</p>
                </div>
                {{-- Total Order --}}
                <div class="rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm text-center flex flex-col justify-center items-center">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-cafe-500 mb-2">Total Order</p>
                    <p class="text-4xl font-extrabold text-cafe-800">{{ $kpiStats['total_orders'] }}</p>
                </div>
                {{-- Order Selesai --}}
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm text-center flex flex-col justify-center items-center">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-600 mb-2">Order Selesai</p>
                    <p class="text-4xl font-extrabold text-emerald-700">{{ $kpiStats['completed'] }}</p>
                </div>
                {{-- Order Dibatalkan --}}
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm text-center flex flex-col justify-center items-center">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-red-600 mb-2">Order Dibatalkan</p>
                    <p class="text-4xl font-extrabold text-red-700">{{ $kpiStats['cancelled'] }}</p>
                </div>
            </div>

            {{-- ── Charts & Top Menu ── --}}
            <div class="grid grid-cols-12 gap-6">
                
                {{-- Bar Chart --}}
                <div class="col-span-12 lg:col-span-7 rounded-2xl border border-cafe-200 bg-white p-6 shadow-sm flex flex-col">
                    <h3 class="text-sm font-bold text-cafe-800 mb-4">Grafik Penjualan Per Hari</h3>
                    <div class="relative flex-1 w-full" style="min-height: 250px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                {{-- Top Menus --}}
                <div class="col-span-12 lg:col-span-5 rounded-2xl border border-cafe-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-cafe-800 mb-5">Menu Terlaris</h3>
                    <div class="space-y-5">
                        @php
                            $maxQty = $topMenus->max('total_qty') ?: 1;
                        @endphp
                        
                        @forelse ($topMenus as $index => $menu)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-cafe-100 text-xs font-bold text-cafe-700">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="text-sm font-semibold text-cafe-800">{{ $menu->menu_name }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-cafe-600">{{ $menu->total_qty }} terjual</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-cafe-100 overflow-hidden">
                                    <div class="h-full bg-cafe-500 rounded-full transition-all duration-1000" 
                                         style="width: {{ ($menu->total_qty / $maxQty) * 100 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-cafe-400 text-center py-4">Belum ada data penjualan menu.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ── Riwayat Transaksi ── --}}
            <div class="rounded-2xl border border-cafe-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-cafe-100 bg-cafe-50 px-6 py-4">
                    <h3 class="text-sm font-bold text-cafe-800">Riwayat Transaksi (10 Terbaru)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-cafe-100 bg-white">
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">Tanggal</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">Kode Order</th>
                                <th class="px-4 py-3.5 text-center text-[11px] font-bold uppercase tracking-widest text-cafe-400">Meja</th>
                                <th class="px-6 py-3.5 text-right text-[11px] font-bold uppercase tracking-widest text-cafe-400">Total</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cafe-50">
                            @forelse ($recentTransactions as $tx)
                                <tr class="transition-colors hover:bg-cafe-50/60">
                                    <td class="px-6 py-4 text-xs text-cafe-500">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-cafe-700">{{ $tx->order_code }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-cafe-100 text-xs font-bold text-cafe-700">
                                            {{ $tx->table->table_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-cafe-800">
                                        Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($tx->status === 'completed')
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Selesai</span>
                                        @elseif ($tx->status === 'cancelled')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-red-700">Batal</span>
                                        @elseif ($tx->status === 'confirmed')
                                            <span class="inline-flex items-center rounded-full bg-cafe-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-cafe-700">Dikonfirmasi</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-cafe-400">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($dailySales);
        
        const labels = salesData.map(item => item.date);
        const data = salesData.map(item => item.revenue);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    backgroundColor: '#a0522d', // cafe-500
                    borderRadius: 4,
                    hoverBackgroundColor: '#8b4513' // cafe-600
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + ' Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + ' Rb';
                                }
                                return value;
                            }
                        },
                        grid: {
                            color: '#fdf8f2' // cafe-50
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
</body>
</html>
