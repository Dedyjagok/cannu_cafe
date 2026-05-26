<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir — Cannu Cafe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-cafe-50 font-sans antialiased">
<div class="flex min-h-screen">

    {{-- ═══════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════ --}}
    <aside class="fixed inset-y-0 left-0 z-20 flex w-60 flex-col bg-cafe-800 shadow-xl">

        {{-- Brand --}}
        <div class="flex items-center gap-3 border-b border-cafe-700 px-5 py-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-cafe-500 shadow-inner">
                {{-- Coffee cup icon --}}
                <svg class="h-5 w-5 text-cafe-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 3h6l1 4H8L9 3zM5 7h14l-2 11a2 2 0 01-2 2H9a2 2 0 01-2-2L5 7z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9h1a2 2 0 010 4h-1"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold leading-tight text-cafe-100">Cannu Cafe</p>
                <p class="text-[10px] font-medium uppercase tracking-widest text-cafe-400">Kasir Panel</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 space-y-1 px-3 py-4">
            <a href="{{ route('kasir.dashboard') }}"
               class="group flex items-center gap-3 rounded-lg bg-cafe-700 px-3 py-2.5 text-sm font-semibold text-cafe-100 shadow-sm transition-all duration-150">
                <svg class="h-4.5 w-4.5 text-cafe-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                </svg>
                Dashboard
            </a>
        </nav>

        {{-- User info + Logout --}}
        <div class="border-t border-cafe-700 px-4 py-4 space-y-3">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cafe-600 text-cafe-200 text-xs font-bold uppercase">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-cafe-100">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] uppercase tracking-wide text-cafe-400">{{ auth()->user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-cafe-600 bg-cafe-700/50 px-3 py-2 text-xs font-semibold text-cafe-300 transition hover:bg-cafe-600 hover:text-cafe-100">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════ --}}
    <div class="ml-60 flex-1 flex flex-col min-h-screen">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-10 flex items-center justify-between border-b border-cafe-200 bg-white/90 px-8 py-4 shadow-sm backdrop-blur">
            <div>
                <h1 class="text-xl font-bold text-cafe-800">Dashboard Kasir</h1>
                <p class="mt-0.5 text-xs text-cafe-400">
                    {{ now()->translatedFormat('l, d F Y • H:i') }} WIB
                </p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-cafe-200 bg-cafe-50 px-4 py-2 shadow-sm">
                <svg class="h-4 w-4 text-cafe-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A8 8 0 1117.804 5.12 8 8 0 015.12 17.804z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-semibold text-cafe-700">{{ auth()->user()->name }}</span>
                <span class="rounded-full bg-cafe-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cafe-700">
                    {{ auth()->user()->role }}
                </span>
            </div>
        </header>

        {{-- Page Body --}}
        <main class="flex-1 px-8 py-6 space-y-6">

            {{-- ── Stat Cards ── --}}
            <div class="grid grid-cols-3 gap-5">

                {{-- Pending --}}
                <div class="relative overflow-hidden rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
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
                <div class="relative overflow-hidden rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm">
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
                <div class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
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

            {{-- ── Notif Banner (JS controlled) ── --}}
            <div id="notif-banner"
                 class="hidden items-center gap-3 rounded-xl border border-amber-300 bg-amber-50 px-5 py-3.5 shadow-sm">
                <span class="animate-bounce text-xl">🔔</span>
                <span id="notif-text" class="flex-1 text-sm font-semibold text-amber-800"></span>
                <button onclick="document.getElementById('notif-banner').classList.replace('flex','hidden')"
                        class="text-amber-400 hover:text-amber-700 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- ── Flash Message ── --}}
            @if (session('success'))
                <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-800">
                    <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- ── Order Table (Livewire) ── --}}
            @livewire('kasir.order-list')

        </main>
    </div>
</div>

{{-- Livewire polling --}}
@livewire('kasir.order-notification')

@livewireScripts
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('new-orders-arrived', (data) => {
            const orders = Array.isArray(data) ? data : (data.orders ?? []);
            if (!orders.length) return;

            const n = orders.length;
            const names = orders.map(o => `Meja ${o.table_number}`).join(', ');
            document.getElementById('notif-text').textContent =
                `${n} pesanan baru masuk dari ${names}!`;

            const banner = document.getElementById('notif-banner');
            banner.classList.remove('hidden');
            banner.classList.add('flex');

            clearTimeout(window._notifTimer);
            window._notifTimer = setTimeout(() => {
                banner.classList.replace('flex', 'hidden');
            }, 7000);
        });
    });
</script>
</body>
</html>
