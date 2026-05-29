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
    /* ── Read notification settings from localStorage ─────────────────
       Set in /kasir/notification-settings page via Alpine.js.
    ─────────────────────────────────────────────────────────────────── */
    const NOTIF_DEFAULT_SOUND = '{{ asset("storage/sound/notification_order_cashier.mp3") }}';
    const NOTIF_KEY           = 'cannu_notif_settings';

    function _getNotifSettings() {
        try {
            const saved = localStorage.getItem(NOTIF_KEY);
            if (saved) return { ...{ enabled: true, volume: 80, selectedSound: NOTIF_DEFAULT_SOUND, bannerDuration: 7, repeatCount: 1 }, ...JSON.parse(saved) };
        } catch (e) {}
        return { enabled: true, volume: 80, selectedSound: NOTIF_DEFAULT_SOUND, bannerDuration: 7, repeatCount: 1 };
    }

    /* ── Browser Autoplay Unlock ─────────────────────────────────────── */
    let _audioUnlocked = false;
    function _unlockAudio() {
        if (_audioUnlocked) return;
        const s = _getNotifSettings();
        const audio = new Audio(s.selectedSound);
        audio.volume = 0;
        audio.play().then(() => { audio.pause(); _audioUnlocked = true; }).catch(() => {});
    }
    document.addEventListener('click',   _unlockAudio, { once: true });
    document.addEventListener('keydown', _unlockAudio, { once: true });

    /* ── Play notification sound with settings ───────────────────────── */
    function _playNotifSound() {
        const s = _getNotifSettings();
        if (!s.enabled) return;

        let played = 0;
        function playOnce() {
            if (played >= s.repeatCount) return;
            const audio = new Audio(s.selectedSound);
            audio.volume = Math.min(1, Math.max(0, s.volume / 100));
            audio.play().then(() => {
                played++;
                audio.addEventListener('ended', playOnce, { once: true });
            }).catch(() => {});
        }
        playOnce();
    }

    /* ── Listen for Livewire event dispatched by KasirDashboard ─────── */
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('new-orders-arrived', (data) => {
            const orders = Array.isArray(data) ? data : (data.orders ?? []);
            if (!orders.length) return;

            const s = _getNotifSettings();

            // 🔔 Play sound
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
            }, (s.bannerDuration ?? 7) * 1000);
        });
    });
</script>
@endpush
