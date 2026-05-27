@extends('layouts.sidebar-cashier')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('content')
    {{-- Single unified Livewire component:
         • Polls every 3s (stat cards + new-order detection)
         • Dispatches 'new-orders-arrived' browser event on new order
         • Renders order table with filter + action buttons
    --}}
    @livewire('kasir.kasir-dashboard')
@endsection

@push('scripts')
<script>
    /* ── Browser Autoplay Unlock ───────────────────────────────────────
       Browsers block audio until user has interacted with the page.
       Silently unlock on first click/keydown so sound plays later.
    ─────────────────────────────────────────────────────────────────── */
    let _audioUnlocked = false;

    function _unlockAudio() {
        if (_audioUnlocked) return;
        const sound = document.getElementById('notif-sound');
        if (!sound) return;
        sound.volume = 0;
        sound.play()
            .then(() => { sound.pause(); sound.currentTime = 0; sound.volume = 1; _audioUnlocked = true; })
            .catch(() => {});
    }
    document.addEventListener('click',   _unlockAudio, { once: true });
    document.addEventListener('keydown', _unlockAudio, { once: true });

    /* ── Play notification sound ────────────────────────────────────── */
    function _playNotifSound() {
        const sound = document.getElementById('notif-sound');
        if (!sound) return;
        sound.currentTime = 0;
        sound.play().catch(() => {});
    }

    /* ── Listen for Livewire event dispatched by KasirDashboard ──────── */
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('new-orders-arrived', (data) => {
            const orders = Array.isArray(data) ? data : (data.orders ?? []);
            if (!orders.length) return;

            // Play sound 🔔
            _playNotifSound();

            // Show banner
            const n     = orders.length;
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
@endpush
