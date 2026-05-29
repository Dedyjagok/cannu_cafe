<div class="relative min-h-screen pb-20">
    {{-- Header & Table Info --}}
    <div class="sticky top-0 z-30 bg-white/90 backdrop-blur-md shadow-sm border-b border-cafe-100">
        <div class="px-5 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-cafe-800">Cannu Cafe</h1>
                <p class="text-xs font-semibold text-cafe-500 mt-0.5">Meja {{ $cafeTable->table_number }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-cafe-100 flex items-center justify-center text-cafe-600 border border-cafe-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
        </div>

        {{-- Categories Horizontal Scroll --}}
        <div class="px-5 py-3 flex gap-3 overflow-x-auto no-scrollbar border-t border-cafe-50">
            @foreach($categories as $category)
                <button wire:click="setActiveCategory({{ $category->id }})"
                        class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all border
                        @if($activeCategoryId === $category->id) 
                            bg-cafe-800 text-white border-cafe-800 shadow-sm
                        @else 
                            bg-white text-cafe-600 border-cafe-200 hover:bg-cafe-50 
                        @endif">
                    {{ $category->icon }} {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Menu List Area --}}
    <div class="px-5 py-6">
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @php
            $activeCategory = $categories->firstWhere('id', $activeCategoryId);
        @endphp

        @if($activeCategory)
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-bold text-cafe-800">{{ $activeCategory->name }}</h2>
                <span class="text-xs font-semibold text-cafe-400">{{ $activeCategory->availableMenuItems->count() }} item</span>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                @forelse($activeCategory->availableMenuItems as $menu)
                    <div class="flex flex-col overflow-hidden rounded-2xl border border-cafe-100 bg-white shadow-sm transition hover:shadow-md">
                        {{-- Image --}}
                        <div class="relative aspect-square bg-cafe-50">
                            @if($menu->image)
                                <img src="{{ Storage::url($menu->image) }}" alt="{{ $menu->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-cafe-300">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            
                            {{-- Add Button (Floating) --}}
                            <button wire:click="addToCart({{ $menu->id }})" 
                                    class="absolute bottom-2 right-2 flex h-8 w-8 items-center justify-center rounded-full bg-cafe-800 text-white shadow-md hover:bg-cafe-700 active:scale-95 transition-transform">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Info --}}
                        <div class="flex flex-1 flex-col p-3">
                            <h3 class="text-sm font-bold leading-tight text-cafe-800 line-clamp-2">{{ $menu->name }}</h3>
                            @if($menu->description)
                                <p class="mt-1 text-[10px] text-cafe-500 line-clamp-1">{{ $menu->description }}</p>
                            @endif
                            <div class="mt-auto pt-2">
                                <p class="font-mono text-sm font-bold text-cafe-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center">
                        <p class="text-sm font-medium text-cafe-400">Belum ada menu di kategori ini.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    {{-- Floating Cart Bottom Bar --}}
    @if($totalItems > 0 && !$isCartOpen)
        <div class="fixed bottom-6 left-0 right-0 z-40 px-5 flex justify-center pointer-events-none">
            <div class="w-full max-w-md bg-cafe-800 rounded-2xl shadow-xl shadow-cafe-900/20 p-4 flex items-center justify-between pointer-events-auto">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <svg class="h-6 w-6 text-cafe-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-[10px] font-bold text-white border-2 border-cafe-800">
                            {{ $totalItems }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-cafe-300 uppercase tracking-wider">Total Harga</p>
                        <p class="text-base font-bold text-white">Rp {{ number_format($totalPrice, 0, ',', '.') }}</p>
                    </div>
                </div>
                <button wire:click="toggleCart" class="bg-white text-cafe-800 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-cafe-50 transition active:scale-95 shadow-sm">
                    Lihat Pesanan
                </button>
            </div>
        </div>
    @endif

    {{-- Cart Modal Overlay --}}
    @if($isCartOpen)
        <div class="fixed inset-0 z-50 flex flex-col bg-cafe-50/50 backdrop-blur-sm sm:items-center sm:justify-center">
            
            {{-- Dim Backdrop (click to close) --}}
            <div class="absolute inset-0 bg-cafe-900/40" wire:click="toggleCart"></div>
            
            {{-- Modal Content (Slide up on mobile, center on desktop) --}}
            <div class="relative mt-auto sm:mt-0 w-full sm:max-w-md h-[85vh] sm:h-[80vh] flex flex-col bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-100">
                    <h3 class="text-lg font-bold text-cafe-800">Pesanan Saya</h3>
                    <button wire:click="toggleCart" class="w-8 h-8 rounded-full bg-cafe-100 flex items-center justify-center text-cafe-500 hover:text-cafe-700 hover:bg-cafe-200 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body (Cart Items) --}}
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5">
                    @forelse($cart as $menuId => $item)
                        <div class="flex flex-col gap-3 pb-5 border-b border-cafe-100 last:border-0 last:pb-0">
                            
                            {{-- Item Primary Info --}}
                            <div class="flex gap-4">
                                {{-- Thumbnail --}}
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-cafe-50 border border-cafe-200 flex-shrink-0">
                                    @if($item['menu']['image'])
                                        <img src="{{ Storage::url($item['menu']['image']) }}" alt="{{ $item['menu']['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-cafe-300">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-cafe-800 leading-tight">{{ $item['menu']['name'] }}</h4>
                                    <p class="text-xs font-semibold text-cafe-600 mt-1">Rp {{ number_format($item['menu']['price'], 0, ',', '.') }}</p>
                                    
                                    {{-- Notes Input --}}
                                    <div class="mt-2 flex items-center gap-2">
                                        <svg class="h-4 w-4 text-cafe-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <input type="text" 
                                               wire:model.live.debounce.500ms="cart.{{ $menuId }}.notes" 
                                               placeholder="Catatan (opsional)..."
                                               class="block w-full border-0 border-b border-cafe-200 bg-transparent py-1 px-1 text-xs text-cafe-700 focus:border-cafe-500 focus:ring-0 placeholder:text-cafe-300">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Item Actions (Qty & Subtotal) --}}
                            <div class="flex items-center justify-between bg-cafe-50 rounded-xl px-3 py-2">
                                <div class="flex items-center gap-3">
                                    <button wire:click="decrementQuantity({{ $menuId }})" class="w-7 h-7 rounded-full bg-white border border-cafe-200 flex items-center justify-center text-cafe-600 hover:bg-cafe-100 transition active:scale-90 shadow-sm">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="text-sm font-bold w-4 text-center text-cafe-800">{{ $item['quantity'] }}</span>
                                    <button wire:click="incrementQuantity({{ $menuId }})" class="w-7 h-7 rounded-full bg-white border border-cafe-200 flex items-center justify-center text-cafe-600 hover:bg-cafe-100 transition active:scale-90 shadow-sm">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-sm font-bold text-cafe-800">
                                    Rp {{ number_format($item['quantity'] * $item['menu']['price'], 0, ',', '.') }}
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="w-16 h-16 bg-cafe-100 text-cafe-300 rounded-full flex items-center justify-center mb-4">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <p class="text-cafe-500 font-medium">Keranjang masih kosong</p>
                            <button wire:click="toggleCart" class="mt-4 text-sm font-bold text-cafe-700 underline">Mulai Pesan</button>
                        </div>
                    @endforelse
                </div>

                {{-- Modal Footer (Checkout) --}}
                @if(!empty($cart))
                    <div class="px-6 py-5 bg-cafe-50 border-t border-cafe-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-cafe-500">Total Pembayaran</span>
                            <span class="text-xl font-black text-cafe-800">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <button wire:click="checkout" wire:loading.attr="disabled" class="w-full py-3.5 rounded-xl bg-cafe-800 text-white text-sm font-bold flex items-center justify-center gap-2 shadow-lg shadow-cafe-900/20 hover:bg-cafe-700 transition active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="checkout">Pesan Sekarang</span>
                            <span wire:loading wire:target="checkout" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                @endif
                
            </div>
        </div>
    @endif
    
    <style>
        /* Hide scrollbar for category tabs */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</div>
