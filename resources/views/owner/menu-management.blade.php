<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu — Owner Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-cafe-50 font-sans antialiased text-cafe-800">
<div class="flex min-h-screen">

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
                    <span class="text-cafe-700 font-semibold">Kelola Menu</span>
                </nav>
                <h1 class="text-xl font-bold text-cafe-800">Kelola Menu</h1>
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

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-800">
                    <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-medium text-red-800">
                    <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-medium text-red-800">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Action Bar --}}
            <div class="flex items-center justify-between">
                <form method="GET" action="{{ route('owner.menu-items.index') }}" class="relative w-72">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari menu..."
                           class="w-full rounded-lg border-cafe-200 pl-10 pr-4 py-2 text-sm text-cafe-800 focus:border-cafe-500 focus:ring-cafe-500 shadow-sm placeholder:text-cafe-300">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-cafe-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>
                
                <button type="button" onclick="openAddModal()"
                        class="flex items-center gap-2 rounded-lg bg-cafe-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-cafe-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Menu
                </button>
            </div>

            {{-- Data Table --}}
            <div class="overflow-hidden rounded-2xl border border-cafe-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-cafe-200 bg-cafe-50">
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-500">Gambar</th>
                                <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-500">Kategori</th>
                                <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-500">Nama Menu</th>
                                <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-widest text-cafe-500">Harga</th>
                                <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-cafe-500">Status</th>
                                <th class="px-6 py-3.5 text-right text-[11px] font-bold uppercase tracking-widest text-cafe-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cafe-100">
                            @forelse ($menuItems as $item)
                                <tr class="transition-colors hover:bg-cafe-50/50">
                                    <td class="px-6 py-4">
                                        @if($item->image)
                                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="h-12 w-12 rounded-lg object-cover border border-cafe-200">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cafe-100 text-cafe-400 border border-cafe-200">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-cafe-100 px-2 py-0.5 text-xs font-semibold text-cafe-600">
                                            {{ $item->category->icon }} {{ $item->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-cafe-800">{{ $item->name }}</p>
                                        <p class="text-[11px] text-cafe-500 mt-0.5 max-w-[200px] truncate" title="{{ $item->description }}">{{ $item->description ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="font-mono font-bold text-cafe-700">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($item->is_available)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-semibold text-red-700 border border-red-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Habis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" 
                                                onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->category_id }}, '{{ addslashes($item->description) }}', {{ $item->price }}, {{ $item->is_available ? 'true' : 'false' }})"
                                                class="rounded-lg border border-cafe-200 bg-white p-2 text-cafe-500 hover:bg-cafe-50 hover:text-cafe-700 transition" title="Edit">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            
                                            <form method="POST" action="{{ route('owner.menu-items.destroy', $item) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="rounded-lg border border-red-200 bg-white p-2 text-red-500 hover:bg-red-50 hover:text-red-700 transition" title="Hapus">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-cafe-400">
                                        Tidak ada menu yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if ($menuItems->hasPages())
                    <div class="border-t border-cafe-200 bg-cafe-50 px-6 py-3">
                        {{ $menuItems->links() }}
                    </div>
                @endif
            </div>
            
        </main>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL TAMBAH / EDIT MENU
═══════════════════════════════════════════ --}}
<div id="menuModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-cafe-900/50 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop" onclick="closeModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            {{-- Modal Panel --}}
            <div id="modalPanel" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <form id="modalForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="bg-white px-6 pb-6 pt-5 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold leading-6 text-cafe-800 mb-5" id="modalTitle">Tambah Menu</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Kategori --}}
                            <div class="sm:col-span-2">
                                <label for="category_id" class="block text-sm font-semibold text-cafe-700 mb-1">Kategori</label>
                                <select name="category_id" id="category_id" required
                                        class="block w-full rounded-lg border-cafe-300 py-2 pl-3 pr-10 text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-500 sm:text-sm">
                                    <option value="" disabled selected>Pilih Kategori...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Nama Menu --}}
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-cafe-700 mb-1">Nama Menu</label>
                                <input type="text" name="name" id="name" required placeholder="Contoh: Kopi Susu Aren"
                                       class="block w-full rounded-lg border-cafe-300 py-2 pl-3 pr-10 text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-500 sm:text-sm">
                            </div>

                            {{-- Harga --}}
                            <div>
                                <label for="price" class="block text-sm font-semibold text-cafe-700 mb-1">Harga (Rp)</label>
                                <input type="number" name="price" id="price" required min="0" placeholder="15000"
                                       class="block w-full rounded-lg border-cafe-300 py-2 pl-3 pr-10 text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-500 sm:text-sm">
                            </div>

                            {{-- Status Tersedia --}}
                            <div class="flex flex-col justify-center pt-5">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_available" id="is_available" value="1" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-cafe-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-cafe-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    <span class="ml-3 text-sm font-semibold text-cafe-700">Tersedia</span>
                                </label>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-semibold text-cafe-700 mb-1">Deskripsi</label>
                                <textarea name="description" id="description" rows="3" placeholder="Opsional"
                                          class="block w-full rounded-lg border-cafe-300 py-2 pl-3 pr-10 text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-500 sm:text-sm"></textarea>
                            </div>

                            {{-- Gambar --}}
                            <div class="sm:col-span-2">
                                <label for="image" class="block text-sm font-semibold text-cafe-700 mb-1">Gambar Menu</label>
                                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
                                       class="block w-full text-sm text-cafe-500 file:mr-4 file:rounded-full file:border-0 file:bg-cafe-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-cafe-700 hover:file:bg-cafe-200 border border-cafe-200 rounded-lg p-1">
                                <p class="mt-1 text-[11px] text-cafe-400" id="imageHelp">Kosongkan jika tidak ingin mengubah gambar.</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Modal Footer --}}
                    <div class="bg-cafe-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-cafe-200">
                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-cafe-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cafe-700 sm:ml-3 sm:w-auto transition">
                            Simpan
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-cafe-700 shadow-sm ring-1 ring-inset ring-cafe-300 hover:bg-cafe-50 sm:mt-0 sm:w-auto transition">
                            Batal
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('menuModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalPanel = document.getElementById('modalPanel');
    const form = document.getElementById('modalForm');
    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalTitle');
    
    // Inputs
    const categoryInput = document.getElementById('category_id');
    const nameInput = document.getElementById('name');
    const priceInput = document.getElementById('price');
    const descriptionInput = document.getElementById('description');
    const isActiveInput = document.getElementById('is_available');
    const imageInput = document.getElementById('image');
    const imageHelp = document.getElementById('imageHelp');

    function showModalAnimations() {
        modal.classList.remove('hidden');
        // Small delay to allow display:block to apply before animating opacity/transform
        setTimeout(() => {
            modalBackdrop.classList.remove('opacity-0');
            modalBackdrop.classList.add('opacity-100');
            modalPanel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            modalPanel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }
    
    function hideModalAnimations() {
        modalBackdrop.classList.remove('opacity-100');
        modalBackdrop.classList.add('opacity-0');
        modalPanel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        modalPanel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        
        // Wait for transition to finish
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openAddModal() {
        modalTitle.textContent = 'Tambah Menu';
        form.action = "{{ route('owner.menu-items.store') }}";
        formMethod.value = "POST";
        
        categoryInput.value = '';
        nameInput.value = '';
        priceInput.value = '';
        descriptionInput.value = '';
        isActiveInput.checked = true;
        imageInput.value = '';
        imageHelp.textContent = 'Maksimal 2MB (JPG/PNG).';
        
        showModalAnimations();
    }

    function openEditModal(id, name, categoryId, description, price, isAvailable) {
        modalTitle.textContent = 'Edit Menu';
        form.action = "/owner/menu-items/" + id;
        formMethod.value = "PUT";
        
        nameInput.value = name;
        categoryInput.value = categoryId;
        descriptionInput.value = description || '';
        priceInput.value = price;
        isActiveInput.checked = isAvailable;
        imageInput.value = ''; // Always clear file input
        imageHelp.textContent = 'Biarkan kosong jika tidak ingin mengganti gambar.';
        
        showModalAnimations();
    }

    function closeModal() {
        hideModalAnimations();
    }
</script>

</body>
</html>
