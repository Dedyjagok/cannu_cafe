{{-- ═══════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════ --}}
<aside class="fixed inset-y-0 left-0 z-20 flex w-48 flex-col bg-[#3E1F0F] shadow-xl overflow-y-auto">
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 py-8">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-[#8A4D2B]">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 3h6l1 4H8L9 3zM5 7h14l-2 11a2 2 0 01-2 2H9a2 2 0 01-2-2L5 7z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9h1a2 2 0 010 4h-1"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold leading-tight text-white">Cannu Cafe</p>
            <p class="text-[9px] font-bold uppercase tracking-widest text-[#B38766]">Owner Panel</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 space-y-8 px-4 py-4">
        
        {{-- Statistik (Active) --}}
        <a href="{{ route('owner.statistics') }}"
           class="flex items-center gap-3 rounded-2xl {{ request()->routeIs('owner.statistics') ? 'bg-[#64391F] shadow-lg' : '' }} p-4 transition hover:opacity-80">
            <svg class="h-12 w-12 flex-shrink-0 {{ request()->routeIs('owner.statistics') ? 'text-[#F5E2D0]' : 'text-[#C69C7E]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-xs font-bold {{ request()->routeIs('owner.statistics') ? 'text-[#F5E2D0]' : 'text-[#E1BCA0]' }}">Statistik</span>
        </a>
        
        {{-- Kelola Menu --}}
        <a href="{{ route('owner.menu-items.index') }}"
           class="flex items-center gap-3 rounded-2xl {{ request()->routeIs('owner.menu-items.*') ? 'bg-[#64391F] shadow-lg' : '' }} p-4 transition hover:opacity-80">
            <svg class="h-12 w-12 flex-shrink-0 {{ request()->routeIs('owner.menu-items.*') ? 'text-[#F5E2D0]' : 'text-[#C69C7E]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-xs font-bold {{ request()->routeIs('owner.menu-items.*') ? 'text-[#F5E2D0]' : 'text-[#E1BCA0]' }} leading-tight">Kelola<br>Menu</span>
        </a>

        {{-- Kelola Kategori --}}
        <a href="{{ route('owner.categories.index') }}"
           class="flex items-center gap-3 rounded-2xl {{ request()->routeIs('owner.categories.*') ? 'bg-[#64391F] shadow-lg' : '' }} p-4 transition hover:opacity-80">
            <svg class="h-12 w-12 flex-shrink-0 {{ request()->routeIs('owner.categories.*') ? 'text-[#F5E2D0]' : 'text-[#C69C7E]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="text-xs font-bold {{ request()->routeIs('owner.categories.*') ? 'text-[#F5E2D0]' : 'text-[#E1BCA0]' }} leading-tight">Kelola<br>Kategori</span>
        </a>

        {{-- Kelola Meja --}}
        <a href="{{ route('owner.tables.index') }}"
           class="flex items-center gap-3 rounded-2xl {{ request()->routeIs('owner.tables.*') ? 'bg-[#64391F] shadow-lg' : '' }} p-4 transition hover:opacity-80">
            <svg class="h-12 w-12 flex-shrink-0 {{ request()->routeIs('owner.tables.*') ? 'text-[#F5E2D0]' : 'text-[#C69C7E]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs font-bold {{ request()->routeIs('owner.tables.*') ? 'text-[#F5E2D0]' : 'text-[#E1BCA0]' }} leading-tight">Kelola<br>Meja</span>
        </a>
    </nav>

    {{-- Logout icon --}}
    <div class="px-6 py-6 border-t border-[#4E2B18]">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#64391F]/40 py-2.5 text-xs font-bold text-[#E1BCA0] transition hover:bg-[#64391F] hover:text-[#F5E2D0]" title="Logout">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>
