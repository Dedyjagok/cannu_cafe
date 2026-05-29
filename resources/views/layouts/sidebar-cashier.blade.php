{{--
    Layout: layouts/sidebar-cashier.blade.php
    Shared Blade layout for all Kasir pages.
    Usage:
      @extends('layouts.sidebar-cashier')
      @section('title', 'Page Title')
      @section('content') ... @endsection
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir') — Cannu Cafe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-cafe-50 font-sans antialiased">
<div class="flex min-h-screen">

    {{-- ═══════════════════════════ SIDEBAR ═══════════════════════════ --}}
    <aside class="fixed inset-y-0 left-0 z-20 flex w-60 flex-col bg-cafe-800 shadow-xl">

        {{-- Brand --}}
        <div class="flex items-center gap-3 border-b border-cafe-700 px-5 py-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-cafe-500 shadow-inner">
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

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 px-3 py-4">

            {{-- Dashboard --}}
            <a href="{{ route('kasir.dashboard') }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                      {{ request()->routeIs('kasir.dashboard') ? 'bg-cafe-700 text-cafe-100 shadow-sm' : 'text-cafe-300 hover:bg-cafe-700/60 hover:text-cafe-100' }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                </svg>
                Dashboard
            </a>

            {{-- Riwayat Pesanan --}}
            <a href="{{ route('kasir.history') }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                      {{ request()->routeIs('kasir.history') ? 'bg-cafe-700 text-cafe-100 shadow-sm' : 'text-cafe-300 hover:bg-cafe-700/60 hover:text-cafe-100' }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Pesanan
            </a>

            {{-- Pengaturan Notifikasi --}}
            <a href="{{ route('kasir.notification-settings') }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                      {{ request()->routeIs('kasir.notification-settings') ? 'bg-cafe-700 text-cafe-100 shadow-sm' : 'text-cafe-300 hover:bg-cafe-700/60 hover:text-cafe-100' }}">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notifikasi
            </a>
        </nav>

        {{-- User Info + Logout --}}
        <div class="border-t border-cafe-700 px-4 py-4 space-y-3">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-cafe-600 text-cafe-200 text-xs font-bold uppercase">
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

    {{-- ═══════════════════════════ MAIN ═══════════════════════════ --}}
    <div class="ml-60 flex flex-1 flex-col min-h-screen">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-10 flex items-center justify-between border-b border-cafe-200 bg-white/90 px-8 py-4 shadow-sm backdrop-blur">
            <div>
                <h1 class="text-xl font-bold text-cafe-800">@yield('page-title', 'Kasir')</h1>
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

        {{-- Page Content --}}
        <main class="flex-1 px-8 py-6 space-y-6">
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
