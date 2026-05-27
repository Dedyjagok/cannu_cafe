{{-- ═══════════════════════════════════════════
     SIDEBAR — Owner Panel
     Width & structure matches sidebar-cashier.blade.php (w-60)
═══════════════════════════════════════════ --}}
<aside class="fixed inset-y-0 left-0 z-20 flex w-60 flex-col bg-[#3E1F0F] shadow-xl overflow-y-auto">

    {{-- Brand --}}
    <div class="flex items-center gap-3 border-b border-[#4E2B18] px-5 py-5">
        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-[#8A4D2B] shadow-inner">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 3h6l1 4H8L9 3zM5 7h14l-2 11a2 2 0 01-2 2H9a2 2 0 01-2-2L5 7z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9h1a2 2 0 010 4h-1"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold leading-tight text-white">Cannu Cafe</p>
            <p class="text-[10px] font-medium uppercase tracking-widest text-[#B38766]">Owner Panel</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 space-y-1 px-3 py-4">

        {{-- Statistik --}}
        <a href="{{ route('owner.statistics') }}"
           class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                  {{ request()->routeIs('owner.statistics') ? 'bg-[#64391F] text-[#F5E2D0] shadow-sm' : 'text-[#C69C7E] hover:bg-[#64391F]/60 hover:text-[#F5E2D0]' }}">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Statistik
        </a>

        {{-- Kelola Menu --}}
        <a href="{{ route('owner.menu-items.index') }}"
           class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                  {{ request()->routeIs('owner.menu-items.*') ? 'bg-[#64391F] text-[#F5E2D0] shadow-sm' : 'text-[#C69C7E] hover:bg-[#64391F]/60 hover:text-[#F5E2D0]' }}">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Kelola Menu
        </a>

        {{-- Kelola Kategori --}}
        <a href="{{ route('owner.categories.index') }}"
           class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                  {{ request()->routeIs('owner.categories.*') ? 'bg-[#64391F] text-[#F5E2D0] shadow-sm' : 'text-[#C69C7E] hover:bg-[#64391F]/60 hover:text-[#F5E2D0]' }}">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Kelola Kategori
        </a>

        {{-- Kelola Meja --}}
        <a href="{{ route('owner.tables.index') }}"
           class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                  {{ request()->routeIs('owner.tables.*') ? 'bg-[#64391F] text-[#F5E2D0] shadow-sm' : 'text-[#C69C7E] hover:bg-[#64391F]/60 hover:text-[#F5E2D0]' }}">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Kelola Meja
        </a>
    </nav>

    {{-- User info + Logout --}}
    <div class="border-t border-[#4E2B18] px-4 py-4 space-y-3">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-[#64391F] text-[#E1BCA0] text-xs font-bold uppercase">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="text-[10px] uppercase tracking-wide text-[#B38766]">{{ auth()->user()->role }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-[#64391F] bg-[#64391F]/40 px-3 py-2 text-xs font-semibold text-[#C69C7E] transition hover:bg-[#64391F] hover:text-[#F5E2D0]">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>
