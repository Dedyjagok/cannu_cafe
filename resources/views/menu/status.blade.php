<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan - Cannu Cafe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cafe-50 font-sans antialiased text-cafe-800">

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-sm flex flex-col items-center justify-center p-6">
        <div class="w-16 h-16 bg-cafe-100 text-cafe-600 rounded-full flex items-center justify-center mb-6">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-cafe-800 mb-2">Pesanan Diterima!</h1>
        <p class="text-cafe-500 text-center mb-8">
            Pesanan Anda dengan kode <span class="font-bold text-cafe-700">#{{ $order->order_code }}</span> sedang diproses. Mohon tunggu sebentar.
        </p>
        
        <div class="w-full bg-cafe-50 border border-cafe-100 rounded-2xl p-5 mb-8">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm font-semibold text-cafe-500">Meja</span>
                <span class="text-base font-bold text-cafe-800">{{ $order->table->table_number }}</span>
            </div>
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm font-semibold text-cafe-500">Status</span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 uppercase tracking-widest">
                    {{ $order->status }}
                </span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-cafe-200">
                <span class="text-sm font-bold text-cafe-800">Total</span>
                <span class="text-lg font-bold text-cafe-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <button onclick="window.location.reload()" class="w-full py-3 rounded-xl bg-cafe-800 text-white font-bold hover:bg-cafe-700 transition shadow-sm">
            Refresh Status
        </button>
    </div>

</body>
</html>
