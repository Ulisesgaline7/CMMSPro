<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetCategoryRequest;
use App\Http\Requests\UpdateAssetCategoryRequest;
use App\Models\AssetCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssetCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = AssetCategory::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        })
            ->withCount('assets')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('asset-categories.index', [
            'categories' => $categories,
            'filters'    => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        return view('asset-categories.create');
    }

    public function store(StoreAssetCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        AssetCategory::create($data);

        return redirect()->route('asset-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(AssetCategory $assetCategory): View
    {
        return view('asset-categories.edit', ['category' => $assetCategory]);
    }

    public function update(UpdateAssetCategoryRequest $request, AssetCategory $assetCategory): RedirectResponse
    {
        $assetCategory->update($request->validated());

        return redirect()->route('asset-categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }
}
