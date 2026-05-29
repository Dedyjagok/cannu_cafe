<?php

namespace App\Livewire\Kasir;

use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * NotificationSettings — Livewire component for cashier notification management.
 *
 * Settings are stored in localStorage on the browser (volume, enabled, selectedSound).
 * Custom uploaded sounds are stored in storage/app/public/sound/.
 */
class NotificationSettings extends Component
{
    use WithFileUploads;

    /** Uploaded sound file (temp Livewire upload) */
    public $uploadedSound = null;

    /** Feedback message after save/test/delete */
    public string $feedback = '';
    public string $feedbackType = 'success'; // 'success' | 'error'

    /** List of available sound files in storage/public/sound/ */
    public array $availableSounds = [];

    public function mount(): void
    {
        $this->loadSounds();
    }

    /**
     * Reload list of .mp3 files in public sound directory.
     */
    public function loadSounds(): void
    {
        $files = Storage::disk('public')->files('sound');

        $this->availableSounds = collect($files)
            ->filter(fn ($f) => str_ends_with($f, '.mp3'))
            ->map(fn ($f) => [
                'path'     => $f,
                'name'     => basename($f, '.mp3'),
                'filename' => basename($f),
                'url'      => asset('storage/' . $f),
                'size'     => $this->formatBytes(Storage::disk('public')->size($f)),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Upload a new sound file.
     */
    public function uploadSound(): void
    {
        $this->validate([
            'uploadedSound' => [
                'required',
                'file',
                'mimes:mp3,audio/mpeg',
                'max:5120', // 5 MB
            ],
        ], [
            'uploadedSound.required' => 'Pilih file MP3 terlebih dahulu.',
            'uploadedSound.mimes'    => 'File harus berformat MP3.',
            'uploadedSound.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $originalName = $this->uploadedSound->getClientOriginalName();
        // Sanitize filename
        $safeName = preg_replace('/[^a-z0-9_\-\.]/i', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $filename  = $safeName . '.mp3';

        // Prevent overwriting the default sound
        if ($filename === 'notification_order_cashier.mp3') {
            $filename = $safeName . '_custom.mp3';
        }

        $this->uploadedSound->storeAs('sound', $filename, 'public');

        $this->uploadedSound = null;
        $this->loadSounds();
        $this->setFeedback("✅ Suara \"{$filename}\" berhasil diunggah.", 'success');
    }

    /**
     * Delete a custom sound file (default cannot be deleted).
     */
    public function deleteSound(string $filename): void
    {
        if ($filename === 'notification_order_cashier.mp3') {
            $this->setFeedback('❌ Suara default tidak dapat dihapus.', 'error');
            return;
        }

        $path = 'sound/' . $filename;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            $this->loadSounds();
            $this->setFeedback("🗑️ Suara \"{$filename}\" berhasil dihapus.", 'success');
        } else {
            $this->setFeedback('❌ File tidak ditemukan.', 'error');
        }
    }

    /**
     * Dispatch a browser event that tells the JS layer to save settings to localStorage.
     * Settings: selectedSound, volume, enabled.
     */
    public function saveSettings(string $selectedSound, int $volume, bool $enabled): void
    {
        $this->dispatch('save-notification-settings', [
            'selectedSound' => $selectedSound,
            'volume'        => $volume,
            'enabled'       => $enabled,
        ]);

        $this->setFeedback('✅ Pengaturan notifikasi disimpan.', 'success');
    }

    private function setFeedback(string $message, string $type): void
    {
        $this->feedback     = $message;
        $this->feedbackType = $type;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }

    public function render(): View
    {
        return view('livewire.kasir.notification-settings');
    }
}
