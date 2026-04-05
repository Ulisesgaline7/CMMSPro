<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdatePartRequest;
use App\Models\Part;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $parts = Part::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('part_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        })
            ->when($request->stock === 'low', fn ($q) => $q->whereColumn('stock_quantity', '<', 'min_stock'))
            ->when($request->stock === 'ok', fn ($q) => $q->whereColumn('stock_quantity', '>=', 'min_stock'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('inventory.index', [
            'parts'   => $parts,
            'filters' => $request->only(['search', 'stock']),
        ]);
    }

    public function create(): View
    {
        return view('inventory.create');
    }

    public function store(StorePartRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;

        $part = Part::create($data);

        return redirect()->route('inventory.show', $part)
            ->with('success', 'Repuesto creado correctamente.');
    }

    public function show(Part $part): View
    {
        $part->load('workOrderParts');

        return view('inventory.show', ['part' => $part]);
    }

    public function edit(Part $part): View
    {
        return view('inventory.edit', ['part' => $part]);
    }

    public function update(UpdatePartRequest $request, Part $part): RedirectResponse
    {
        $part->update($request->validated());

        return redirect()->route('inventory.show', $part)
            ->with('success', 'Repuesto actualizado correctamente.');
    }
}
