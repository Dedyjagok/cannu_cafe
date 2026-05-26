<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    /**
     * Tampilkan daftar semua meja.
     *
     * URL: GET /owner/tables
     */
    public function index(): View
    {
        $tables = CafeTable::withCount(['orders', 'activeOrders'])
            ->orderBy('table_number')
            ->get();

        return view('owner.table-management', compact('tables'));
    }

    /**
     * Simpan meja baru dan generate QR token otomatis.
     *
     * URL: POST /owner/tables
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:10', 'unique:tables,table_number'],
        ]);
        
        $validated['is_available'] = $request->has('is_available');
        $validated['qr_token'] = CafeTable::generateQrToken();

        CafeTable::create($validated);

        return redirect()->route('owner.tables.index')
            ->with('success', "Meja {$validated['table_number']} berhasil ditambahkan.");
    }

    /**
     * Update data meja (nomor dan ketersediaan).
     *
     * URL: PUT /owner/tables/{table}
     */
    public function update(Request $request, CafeTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:10', "unique:tables,table_number,{$table->id}"],
        ]);
        
        $validated['is_available'] = $request->has('is_available');

        $table->update($validated);

        return redirect()->route('owner.tables.index')
            ->with('success', "Meja {$table->table_number} berhasil diperbarui.");
    }

    /**
     * Hapus meja (hanya jika tidak ada pesanan aktif).
     *
     * URL: DELETE /owner/tables/{table}
     */
    public function destroy(CafeTable $table): RedirectResponse
    {
        if ($table->activeOrders()->exists()) {
            return back()->with('error', 'Meja tidak dapat dihapus karena masih memiliki pesanan aktif.');
        }

        $table->delete();

        return redirect()->route('owner.tables.index')
            ->with('success', "Meja {$table->table_number} berhasil dihapus.");
    }

    /**
     * Tampilkan / download QR code SVG untuk meja tertentu.
     * URL yang di-encode: /menu/{qr_token}
     *
     * URL: GET /owner/tables/{table}/qr
     */
    public function showQr(CafeTable $table): \Illuminate\Http\Response
    {
        $qrUrl = route('menu.show', ['qrToken' => $table->qr_token]);

        // Generate QR code sebagai SVG (tidak butuh ext-gd)
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')  // High error correction
            ->margin(2)
            ->generate($qrUrl);

        return response($qrCode, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => "inline; filename=\"meja-{$table->table_number}.svg\"",
        ]);
    }

    /**
     * Regenerate QR token meja (jika token lama bocor/hilang).
     *
     * URL: POST /owner/tables/{table}/regenerate-qr
     */
    public function regenerateQr(CafeTable $table): RedirectResponse
    {
        $table->update(['qr_token' => CafeTable::generateQrToken()]);

        return redirect()->route('owner.tables.index')
            ->with('success', "QR Code meja {$table->table_number} berhasil di-regenerate.");
    }
}
