<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreUpdateRequest as Req;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $cats = Category::when($q, fn($w) =>
                    $w->where('name','ilike',"%$q%")
                      ->orWhere('slug','ilike',"%$q%"))
                ->orderBy('sort_order')->orderBy('name')
                ->paginate(12)->withQueryString();

        return view('admin.categories.index', compact('cats','q'));
    }

    public function store(Req $request)
    {
        $data = $request->validated();

        // upload opcional
        if ($request->hasFile('icon')) {
            $data['icon_path'] = $request->file('icon')->store('categorias', 'public');
        }

        Category::create($data);

        return back()->with('ok','Categoria criada com sucesso.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Req $request, Category $category)
    {
        $data = $request->validated();

        if ($request->hasFile('icon')) {
            // remove o antigo se existir
            if ($category->icon_path && Storage::disk('public')->exists($category->icon_path)) {
                Storage::disk('public')->delete($category->icon_path);
            }
            $data['icon_path'] = $request->file('icon')->store('categorias', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('ok','Categoria atualizada.');
    }

    public function destroy(Category $category)
    {
        if ($category->icon_path && Storage::disk('public')->exists($category->icon_path)) {
            Storage::disk('public')->delete($category->icon_path);
        }
        $category->delete();
        return back()->with('ok','Categoria removida.');
    }
}
