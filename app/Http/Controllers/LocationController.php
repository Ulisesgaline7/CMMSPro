<?php

namespace App\Http\Controllers;

use App\Enums\LocationType;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $locations = Location::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        })
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->with('parent:id,name')
            ->withCount('assets')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('locations.index', [
            'locations' => $locations,
            'filters'   => $request->only(['search', 'type']),
            'types'     => LocationType::cases(),
        ]);
    }

    public function create(): View
    {
        $parents = Location::select('id', 'name', 'code', 'type')->orderBy('name')->get();

        return view('locations.create', ['parents' => $parents]);
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        Location::create($data);

        return redirect()->route('locations.index')
            ->with('success', 'Ubicación creada correctamente.');
    }

    public function edit(Location $location): View
    {
        $parents = Location::select('id', 'name', 'code', 'type')
            ->where('id', '!=', $location->id)
            ->orderBy('name')
            ->get();

        return view('locations.edit', [
            'location' => $location,
            'parents'  => $parents,
        ]);
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()->route('locations.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }
}
