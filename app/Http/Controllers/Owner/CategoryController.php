<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar semua kategori.
     *
     * URL: GET /owner/categories
     */
    public function index(): View
    {
        $categories = Category::ordered()->withCount('menuItems')->get();

        return view('owner.categories.index', compact('categories'));
    }

    /**
     * Form tambah kategori baru.
     *
     * URL: GET /owner/categories/create
     */
    public function create(): View
    {
        return view('owner.categories.create');
    }

    /**
     * Simpan kategori baru.
     *
     * URL: POST /owner/categories
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:categories,name'],
            'icon'       => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
            'is_active'  => ['boolean'],
        ]);

        Category::create($validated);

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Form edit kategori.
     *
     * URL: GET /owner/categories/{category}/edit
     */
    public function edit(Category $category): View
    {
        return view('owner.categories.edit', compact('category'));
    }

    /**
     * Update data kategori.
     *
     * URL: PUT /owner/categories/{category}
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', "unique:categories,name,{$category->id}"],
            'icon'       => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
            'is_active'  => ['boolean'],
        ]);

        $category->update($validated);

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori.
     * Tidak bisa dihapus jika masih ada menu item (restrictOnDelete di migration).
     *
     * URL: DELETE /owner/categories/{category}
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->menuItems()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu item.');
        }

        $category->delete();

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
