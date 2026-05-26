<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Meja — Owner Panel</title>
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
    <div class="ml-48 flex-1 flex flex-col min-h-screen">
        
        {{-- Top Bar --}}
        <header class="sticky top-0 z-10 flex items-center justify-between border-b border-cafe-200 bg-white/90 px-8 py-4 shadow-sm backdrop-blur">
            <div>
                <nav class="flex text-xs font-medium text-cafe-400 mb-1" aria-label="Breadcrumb">
                    <span class="hover:text-cafe-700">Owner</span>
                    <span class="mx-2">/</span>
                    <span class="text-cafe-700 font-semibold">Kelola Meja</span>
                </nav>
                <h1 class="text-xl font-bold text-cafe-800">Kelola Meja</h1>
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
            <div class="flex items-center justify-end">
                <button type="button" onclick="openAddModal()"
                        class="flex items-center gap-2 rounded-lg bg-cafe-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cafe-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Meja
                </button>
            </div>

            {{-- Grid Meja --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($tables as $table)
                    <div class="rounded-2xl border border-cafe-200 bg-white p-5 shadow-sm flex flex-col items-center">
                        {{-- Title --}}
                        <h3 class="text-lg font-bold text-cafe-800 mb-4 text-center">Meja {{ $table->table_number }}</h3>
                        
                        {{-- QR Code Image --}}
                        <div class="mb-4 rounded-xl border border-cafe-100 p-2 bg-white shadow-sm">
                            <img src="{{ route('owner.tables.qr', $table->id) }}" alt="QR Meja {{ $table->table_number }}" class="w-32 h-32 object-contain pointer-events-none">
                        </div>
                        
                        {{-- Status Pill --}}
                        <div class="mb-5">
                            @if($table->is_available)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Tersedia
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-red-300 bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                    <span class="h-2 w-2 rounded-full bg-red-500"></span> Tidak Tersedia
                                </span>
                            @endif
                        </div>
                        
                        {{-- Actions Footer --}}
                        <div class="mt-auto w-full flex items-center justify-between gap-2 border-t border-cafe-100 pt-4">
                            {{-- Lihat QR --}}
                            <button type="button" 
                                onclick="openQrModal({{ $table->id }}, '{{ addslashes($table->table_number) }}', '{{ route('menu.show', ['qrToken' => $table->qr_token]) }}', '{{ route('owner.tables.qr', $table->id) }}')"
                                class="flex-1 flex flex-col items-center justify-center rounded-lg border border-cafe-200 bg-cafe-50 py-2 text-cafe-600 hover:bg-cafe-100 hover:text-cafe-800 transition">
                                <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span class="text-[10px] font-bold">Lihat QR</span>
                            </button>
                            
                            {{-- Edit --}}
                            <button type="button" 
                                onclick="openEditModal({{ $table->id }}, '{{ addslashes($table->table_number) }}', {{ $table->is_available ? 'true' : 'false' }})"
                                class="flex-1 flex flex-col items-center justify-center rounded-lg border border-cafe-200 bg-cafe-50 py-2 text-cafe-600 hover:bg-cafe-100 hover:text-cafe-800 transition">
                                <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span class="text-[10px] font-bold">Edit</span>
                            </button>
                            
                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('owner.tables.destroy', $table) }}" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus meja ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="w-full flex flex-col items-center justify-center rounded-lg border border-red-200 bg-red-50 py-2 text-red-500 hover:bg-red-100 hover:text-red-700 transition">
                                    <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="text-[10px] font-bold">Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-cafe-100 text-cafe-300 mb-4">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <p class="text-cafe-500 font-medium">Belum ada meja yang terdaftar.</p>
                        <p class="text-sm text-cafe-400 mt-1">Tambahkan meja pertama Anda sekarang.</p>
                    </div>
                @endforelse
            </div>
            
        </main>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL TAMBAH / EDIT MEJA
═══════════════════════════════════════════ --}}
<div id="tableModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-cafe-900/50 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdropTable" onclick="closeTableModal()"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="modalPanelTable" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <form id="modalFormTable" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="formMethodTable" value="POST">
                    
                    <div class="bg-white px-6 pb-6 pt-5 sm:p-6 sm:pb-4">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-bold leading-6 text-cafe-800 mb-5" id="modalTitleTable">Tambah Meja</h3>
                            
                            <div class="space-y-5">
                                {{-- Nomor Meja --}}
                                <div>
                                    <label for="table_number" class="block text-sm font-semibold text-cafe-700 mb-1">Nomor Meja</label>
                                    <input type="text" name="table_number" id="table_number" required placeholder="Contoh: 1, 2A, VIP1"
                                           class="mt-1 block w-full rounded-lg border-cafe-300 py-2.5 pl-3 pr-10 text-cafe-800 focus:border-cafe-500 focus:outline-none focus:ring-1 focus:ring-cafe-500 sm:text-sm">
                                </div>
                                
                                {{-- Status Aktif --}}
                                <div class="flex items-center gap-3 pt-2">
                                    <span class="block text-sm font-semibold text-cafe-700">Status</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_available" id="is_available" value="1" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-cafe-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-cafe-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                        <span class="ml-3 text-sm font-medium text-cafe-600">Aktif/Nonaktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-cafe-50 px-4 py-3 sm:flex sm:flex-col sm:gap-2 sm:px-6 border-t border-cafe-200">
                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-cafe-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cafe-700 transition">
                            Simpan
                        </button>
                        <button type="button" onclick="closeTableModal()" class="inline-flex w-full justify-center rounded-lg bg-transparent px-4 py-2.5 text-sm font-semibold text-cafe-700 hover:bg-cafe-100 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL QR PREVIEW
═══════════════════════════════════════════ --}}
<div id="qrModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-cafe-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdropQr" onclick="closeQrModal()"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div id="modalPanelQr" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-sm opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-cafe-100 px-5 py-4 bg-white">
                    <h3 class="text-base font-bold text-cafe-800">QR Preview</h3>
                    <button type="button" onclick="closeQrModal()" class="text-cafe-400 hover:text-cafe-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 flex flex-col items-center bg-cafe-50/50">
                    <div class="rounded-xl border-2 border-cafe-200 bg-white p-3 shadow-sm mb-4">
                        <img id="qrPreviewImage" src="" alt="QR Preview" class="w-48 h-48 object-contain pointer-events-none">
                    </div>
                    
                    <h4 class="text-xl font-bold text-cafe-800 mb-1" id="qrTableName">Meja 3</h4>
                    <a id="qrTableUrl" href="#" target="_blank" class="text-xs text-cafe-500 hover:text-cafe-700 underline mb-6 truncate max-w-full block">https://kopikita.id/m3</a>
                    
                    <div class="w-full space-y-3">
                        <a id="downloadQrBtn" href="#" download="qr-meja.svg"
                           class="flex w-full items-center justify-center gap-2 rounded-lg border border-cafe-300 bg-white py-2.5 text-sm font-bold text-cafe-700 shadow-sm hover:bg-cafe-50 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download QR
                        </a>
                        
                        <form id="regenerateQrForm" method="POST" action="">
                            @csrf
                            <button type="submit" onclick="return confirm('Peringatan: Jika QR di-regenerate, QR code fisik yang lama tidak akan bisa dipakai lagi. Yakin ingin melanjutkan?');"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-amber-300 bg-amber-50 py-2.5 text-sm font-bold text-amber-700 shadow-sm hover:bg-amber-100 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Regenerate QR
                            </button>
                        </form>
                    </div>

                    <div class="mt-5 flex items-start gap-2 rounded-lg bg-amber-50 px-3 py-2 border border-amber-200">
                        <svg class="h-4 w-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-[10px] leading-tight text-amber-800">
                            <strong>Warning:</strong> Pendaurulangan token QR menyebabkan pemindaian pelanggan menggunakan QR lama tidak akan berfungsi lagi.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // ── Table Modal Logic ──
    const tableModal = document.getElementById('tableModal');
    const modalBackdropTable = document.getElementById('modalBackdropTable');
    const modalPanelTable = document.getElementById('modalPanelTable');
    const formTable = document.getElementById('modalFormTable');
    const formMethodTable = document.getElementById('formMethodTable');
    const modalTitleTable = document.getElementById('modalTitleTable');
    const tableNumberInput = document.getElementById('table_number');
    const isAvailableInput = document.getElementById('is_available');

    function openAddModal() {
        modalTitleTable.textContent = 'Tambah Meja';
        formTable.action = "{{ route('owner.tables.store') }}";
        formMethodTable.value = "POST";
        tableNumberInput.value = '';
        isAvailableInput.checked = true;
        
        tableModal.classList.remove('hidden');
        setTimeout(() => {
            modalBackdropTable.classList.remove('opacity-0');
            modalBackdropTable.classList.add('opacity-100');
            modalPanelTable.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            modalPanelTable.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function openEditModal(id, number, isAvailable) {
        modalTitleTable.textContent = 'Edit Meja';
        formTable.action = "/owner/tables/" + id;
        formMethodTable.value = "PUT";
        tableNumberInput.value = number;
        isAvailableInput.checked = isAvailable;
        
        tableModal.classList.remove('hidden');
        setTimeout(() => {
            modalBackdropTable.classList.remove('opacity-0');
            modalBackdropTable.classList.add('opacity-100');
            modalPanelTable.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            modalPanelTable.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeTableModal() {
        modalBackdropTable.classList.remove('opacity-100');
        modalBackdropTable.classList.add('opacity-0');
        modalPanelTable.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        modalPanelTable.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        setTimeout(() => {
            tableModal.classList.add('hidden');
        }, 300);
    }

    // ── QR Modal Logic ──
    const qrModal = document.getElementById('qrModal');
    const modalBackdropQr = document.getElementById('modalBackdropQr');
    const modalPanelQr = document.getElementById('modalPanelQr');
    
    function openQrModal(id, number, url, qrSrc) {
        document.getElementById('qrTableName').textContent = 'Meja ' + number;
        document.getElementById('qrTableUrl').textContent = url;
        document.getElementById('qrTableUrl').href = url;
        document.getElementById('qrPreviewImage').src = qrSrc;
        
        // Update form action for regenerate
        document.getElementById('regenerateQrForm').action = "/owner/tables/" + id + "/regenerate-qr";
        // Update download link
        const downloadBtn = document.getElementById('downloadQrBtn');
        downloadBtn.href = qrSrc;
        downloadBtn.download = "QR-Meja-" + number + ".svg";
        
        qrModal.classList.remove('hidden');
        setTimeout(() => {
            modalBackdropQr.classList.remove('opacity-0');
            modalBackdropQr.classList.add('opacity-100');
            modalPanelQr.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            modalPanelQr.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeQrModal() {
        modalBackdropQr.classList.remove('opacity-100');
        modalBackdropQr.classList.add('opacity-0');
        modalPanelQr.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        modalPanelQr.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        setTimeout(() => {
            qrModal.classList.add('hidden');
        }, 300);
    }
</script>

</body>
</html>
