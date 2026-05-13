<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    /**
     * Tampilkan daftar semua menu item.
     *
     * URL: GET /owner/menu-items
     */
    public function index(): View
    {
        $menuItems = MenuItem::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->paginate(20);

        return view('owner.menu-items.index', compact('menuItems'));
    }

    /**
     * Form tambah menu item baru.
     *
     * URL: GET /owner/menu-items/create
     */
    public function create(): View
    {
        $categories = Category::active()->ordered()->get();

        return view('owner.menu-items.create', compact('categories'));
    }

    /**
     * Simpan menu item baru beserta upload gambar.
     *
     * URL: POST /owner/menu-items
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'is_available' => ['boolean'],
        ]);

        // Upload gambar ke storage/app/public/menu-images
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-images', 'public');
        }

        MenuItem::create($validated);

        return redirect()->route('owner.menu-items.index')
            ->with('success', 'Menu item berhasil ditambahkan.');
    }

    /**
     * Form edit menu item.
     *
     * URL: GET /owner/menu-items/{menuItem}/edit
     */
    public function edit(MenuItem $menuItem): View
    {
        $categories = Category::active()->ordered()->get();

        return view('owner.menu-items.edit', compact('menuItem', 'categories'));
    }

    /**
     * Update data menu item.
     *
     * URL: PUT /owner/menu-items/{menuItem}
     */
    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'is_available' => ['boolean'],
        ]);

        // Ganti gambar jika ada upload baru, hapus gambar lama
        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-images', 'public');
        }

        $menuItem->update($validated);

        return redirect()->route('owner.menu-items.index')
            ->with('success', 'Menu item berhasil diperbarui.');
    }

    /**
     * Hapus menu item beserta gambarnya.
     *
     * URL: DELETE /owner/menu-items/{menuItem}
     */
    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        // Hapus gambar dari storage jika ada
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        return redirect()->route('owner.menu-items.index')
            ->with('success', 'Menu item berhasil dihapus.');
    }
}
