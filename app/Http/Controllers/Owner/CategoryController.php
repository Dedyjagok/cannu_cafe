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
     * Tampilkan daftar kategori + data untuk modal (tambah/edit).
     * URL: GET /owner/categories
     */
    public function index(Request $request): View
    {
        $search     = $request->input('search', '');
        $categories = Category::ordered()
            ->withCount('menuItems')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(10)
            ->withQueryString();

        $nextSortOrder = Category::max('sort_order') + 1;

        return view('owner.category-management', compact('categories', 'search', 'nextSortOrder'));
    }

    // create() & edit() tidak dipakai — semua lewat modal di index
    public function create(): View   { return $this->index(request()); }
    public function edit(Category $category): View { return $this->index(request()); }

    /**
     * Simpan kategori baru.
     * URL: POST /owner/categories
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:categories,name'],
            'icon'       => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Category::create($validated);

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Update kategori.
     * URL: PUT /owner/categories/{category}
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', "unique:categories,name,{$category->id}"],
            'icon'       => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('owner.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui.");
    }

    /**
     * Hapus kategori (guard: tidak bisa hapus jika masih punya menu).
     * URL: DELETE /owner/categories/{category}
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->menuItems()->exists()) {
            return back()->with('error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih memiliki menu item.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('owner.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }
}
