<div x-data="notifSettings()" x-init="init()" class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════
         HEADER CARD
    ══════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-cafe-200 bg-white p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-amber-100">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-cafe-800">Pengaturan Notifikasi Suara</h2>
                <p class="mt-1 text-sm text-cafe-500">
                    Atur suara, volume, dan preferensi notifikasi pesanan masuk. Pengaturan disimpan di browser ini.
                </p>
            </div>
            {{-- Enable / disable toggle --}}
            <div class="ml-auto flex-shrink-0">
                <button @click="enabled = !enabled; saveToStorage()"
                        :class="enabled ? 'bg-emerald-500' : 'bg-gray-300'"
                        class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-200 focus:outline-none">
                    <span :class="enabled ? 'translate-x-6' : 'translate-x-1'"
                          class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-200"></span>
                </button>
                <p class="mt-1 text-center text-[10px] font-semibold uppercase tracking-wide"
                   :class="enabled ? 'text-emerald-600' : 'text-gray-400'"
                   x-text="enabled ? 'Aktif' : 'Nonaktif'"></p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FEEDBACK MESSAGE (Livewire flash)
    ══════════════════════════════════════════════════════════ --}}
    @if ($feedback)
        <div class="flex items-center gap-3 rounded-xl border px-5 py-3 text-sm font-medium
                    {{ $feedbackType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}">
            {{ $feedback }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         SOUND SELECTION + VOLUME
    ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- Volume Control --}}
        <div class="rounded-2xl border border-cafe-200 bg-white p-6 shadow-sm">
            <p class="mb-4 text-sm font-bold text-cafe-700">🔊 Volume Notifikasi</p>

            <div class="space-y-4">
                {{-- Volume slider --}}
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs text-cafe-500">Volume</span>
                        <span class="rounded-lg bg-cafe-100 px-2 py-0.5 text-xs font-bold text-cafe-700"
                              x-text="volume + '%'"></span>
                    </div>
                    <input type="range" min="0" max="100" step="5"
                           x-model="volume"
                           @input="saveToStorage()"
                           :disabled="!enabled"
                           class="h-2 w-full cursor-pointer appearance-none rounded-full bg-cafe-200 accent-cafe-600 disabled:opacity-40">
                    <div class="mt-1.5 flex justify-between text-[10px] text-cafe-400">
                        <span>0%</span>
                        <span>50%</span>
                        <span>100%</span>
                    </div>
                </div>

                {{-- Test sound button --}}
                <button @click="testSound()"
                        :disabled="!enabled"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-cafe-200 bg-cafe-50 px-4 py-3 text-sm font-semibold text-cafe-700 transition hover:bg-cafe-100 disabled:cursor-not-allowed disabled:opacity-40">
                    <svg class="h-4 w-4 text-cafe-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Preview Suara
                </button>
            </div>
        </div>

        {{-- Repeat & Banner duration --}}
        <div class="rounded-2xl border border-cafe-200 bg-white p-6 shadow-sm">
            <p class="mb-4 text-sm font-bold text-cafe-700">⏱ Preferensi Banner</p>

            <div class="space-y-4">
                {{-- Banner duration --}}
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs text-cafe-500">Durasi Banner</span>
                        <span class="rounded-lg bg-cafe-100 px-2 py-0.5 text-xs font-bold text-cafe-700"
                              x-text="bannerDuration + ' detik'"></span>
                    </div>
                    <input type="range" min="3" max="30" step="1"
                           x-model="bannerDuration"
                           @input="saveToStorage()"
                           :disabled="!enabled"
                           class="h-2 w-full cursor-pointer appearance-none rounded-full bg-cafe-200 accent-cafe-600 disabled:opacity-40">
                    <div class="mt-1.5 flex justify-between text-[10px] text-cafe-400">
                        <span>3 det</span>
                        <span>15 det</span>
                        <span>30 det</span>
                    </div>
                </div>

                {{-- Repeat toggle --}}
                <div class="flex items-center justify-between rounded-xl bg-cafe-50 px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold text-cafe-700">Ulangi Suara</p>
                        <p class="text-xs text-cafe-400">Putar notifikasi beberapa kali</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="number" min="1" max="5"
                               x-model="repeatCount"
                               @input="saveToStorage()"
                               :disabled="!enabled"
                               class="w-14 rounded-lg border border-cafe-200 bg-white px-2 py-1 text-center text-sm font-bold text-cafe-700 focus:outline-none focus:ring-1 focus:ring-cafe-400 disabled:opacity-40">
                        <span class="text-xs text-cafe-500">kali</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         SOUND LIBRARY
    ══════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-cafe-200 bg-white shadow-sm">
        <div class="border-b border-cafe-100 px-6 py-4">
            <p class="text-sm font-bold text-cafe-800">🎵 Perpustakaan Suara</p>
            <p class="mt-0.5 text-xs text-cafe-400">Pilih suara notifikasi yang akan digunakan</p>
        </div>

        <div class="divide-y divide-cafe-50">
            @forelse ($availableSounds as $sound)
                <div class="flex items-center gap-4 px-6 py-4 transition hover:bg-cafe-50/50"
                     wire:key="sound-{{ $sound['filename'] }}">

                    {{-- Radio select --}}
                    <input type="radio" :id="'sound-{{ $sound['filename'] }}'"
                           name="selectedSound"
                           value="{{ $sound['url'] }}"
                           x-model="selectedSound"
                           @change="saveToStorage()"
                           :disabled="!enabled"
                           class="h-4 w-4 cursor-pointer accent-cafe-600 disabled:opacity-40">

                    {{-- Info --}}
                    <label :for="'sound-{{ $sound['filename'] }}'" class="flex-1 cursor-pointer">
                        <p class="text-sm font-semibold text-cafe-800">{{ $sound['name'] }}</p>
                        <p class="text-xs text-cafe-400">{{ $sound['filename'] }} · {{ $sound['size'] }}</p>
                    </label>

                    {{-- Selected badge --}}
                    <span x-show="selectedSound === '{{ $sound['url'] }}'"
                          class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                        Aktif
                    </span>

                    {{-- Preview button --}}
                    <button @click="previewSound('{{ $sound['url'] }}')"
                            title="Preview"
                            class="rounded-lg p-2 text-cafe-400 transition hover:bg-cafe-100 hover:text-cafe-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>

                    {{-- Delete button (hidden for default) --}}
                    @if ($sound['filename'] !== 'notification_order_cashier.mp3')
                        <button wire:click="deleteSound('{{ $sound['filename'] }}')"
                                wire:confirm="Hapus suara '{{ $sound['name'] }}'?"
                                title="Hapus"
                                class="rounded-lg p-2 text-red-400 transition hover:bg-red-50 hover:text-red-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @else
                        <span class="rounded-full bg-cafe-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cafe-500">
                            Default
                        </span>
                    @endif
                </div>
            @empty
                <div class="px-6 py-12 text-center text-cafe-400">
                    <p class="text-sm">Belum ada file suara. Upload MP3 di bawah.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         UPLOAD CUSTOM SOUND
    ══════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-dashed border-cafe-300 bg-white p-6 shadow-sm">
        <p class="mb-4 text-sm font-bold text-cafe-700">⬆️ Upload Suara Kustom</p>

        <form wire:submit.prevent="uploadSound" class="space-y-4">
            {{-- Drop zone --}}
            <div x-data="{ isDragging: false }"
                 @dragover.prevent="isDragging = true"
                 @dragleave="isDragging = false"
                 @drop.prevent="isDragging = false; $wire.uploadedSound = $event.dataTransfer.files[0]"
                 :class="isDragging ? 'border-cafe-500 bg-cafe-50' : 'border-cafe-200 bg-cafe-50/40'"
                 class="flex flex-col items-center gap-3 rounded-xl border-2 border-dashed px-6 py-8 text-center transition-colors">
                <svg class="h-10 w-10 text-cafe-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-cafe-600">Seret file MP3 ke sini</p>
                    <p class="text-xs text-cafe-400">atau klik tombol pilih file · Maks 5 MB</p>
                </div>
                <label class="cursor-pointer rounded-lg bg-cafe-700 px-5 py-2 text-xs font-bold text-white transition hover:bg-cafe-800">
                    Pilih File MP3
                    <input type="file" accept=".mp3,audio/mpeg" wire:model="uploadedSound" class="hidden">
                </label>

                {{-- File selected indicator --}}
                @if ($uploadedSound)
                    <div class="mt-1 flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $uploadedSound->getClientOriginalName() }}
                        ({{ round($uploadedSound->getSize() / 1024, 1) }} KB)
                    </div>
                @endif
            </div>

            {{-- Validation errors --}}
            @error('uploadedSound')
                <p class="text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror

            {{-- Upload button --}}
            <button type="submit"
                    :disabled="{{ $uploadedSound ? 'false' : 'true' }}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-cafe-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-cafe-800 disabled:cursor-not-allowed disabled:opacity-40">
                <div wire:loading wire:target="uploadSound"
                     class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                <svg wire:loading.remove wire:target="uploadSound"
                     class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Suara
            </button>
        </form>
    </div>

    {{-- Hidden audio for preview --}}
    <audio id="preview-audio" style="display:none"></audio>

    {{-- Alpine.js component --}}
    <script>
        function notifSettings() {
            return {
                enabled:        true,
                volume:         80,
                selectedSound:  '{{ asset("storage/sound/notification_order_cashier.mp3") }}',
                bannerDuration: 7,
                repeatCount:    1,

                init() {
                    const saved = localStorage.getItem('cannu_notif_settings');
                    if (saved) {
                        try {
                            const s = JSON.parse(saved);
                            this.enabled        = s.enabled        ?? true;
                            this.volume         = s.volume         ?? 80;
                            this.selectedSound  = s.selectedSound  ?? this.selectedSound;
                            this.bannerDuration = s.bannerDuration ?? 7;
                            this.repeatCount    = s.repeatCount    ?? 1;
                        } catch(e) {}
                    }
                },

                saveToStorage() {
                    localStorage.setItem('cannu_notif_settings', JSON.stringify({
                        enabled:        this.enabled,
                        volume:         parseInt(this.volume),
                        selectedSound:  this.selectedSound,
                        bannerDuration: parseInt(this.bannerDuration),
                        repeatCount:    parseInt(this.repeatCount),
                    }));
                },

                previewSound(url) {
                    const audio = document.getElementById('preview-audio');
                    audio.src    = url;
                    audio.volume = this.volume / 100;
                    audio.currentTime = 0;
                    audio.play().catch(() => {});
                },

                testSound() {
                    this.previewSound(this.selectedSound);
                },
            };
        }
    </script>
</div>
