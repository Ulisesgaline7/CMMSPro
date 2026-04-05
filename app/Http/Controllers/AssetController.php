<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use Chillerlan\QRCode\QRCode;
use Chillerlan\QRCode\QROptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $assets = Asset::with([
            'location:id,name',
            'category:id,name',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->criticality, fn ($q, $c) => $q->where('criticality', $c))
            ->when($request->category, fn ($q, $c) => $q->where('asset_category_id', $c))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = AssetCategory::select('id', 'name')->orderBy('name')->get();

        return view('assets.index', [
            'assets'     => $assets,
            'categories' => $categories,
            'filters'    => $request->only(['search', 'status', 'criticality', 'category']),
        ]);
    }

    public function create(): View
    {
        return view('assets.create', $this->formData());
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;

        $asset = Asset::create($data);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Activo creado correctamente.');
    }

    public function show(Asset $asset): View
    {
        $asset->load([
            'location.parent',
            'category:id,name,code',
            'parent:id,name,code',
            'children:id,name,code,status,criticality',
            'workOrders' => fn ($q) => $q->latest()->limit(10)->with('assignedTo:id,name'),
        ]);

        return view('assets.show', [
            'asset' => $asset,
        ]);
    }

    public function edit(Asset $asset): View
    {
        return view('assets.edit', array_merge(
            ['asset' => $asset],
            $this->formData(),
        ));
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $asset->update($request->validated());

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Activo actualizado correctamente.');
    }

    public function qr(Asset $asset): View
    {
        $url = route('assets.show', $asset);

        $options = new QROptions([
            'outputType' => \Chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
            'imageBase64' => false,
            'svgViewBoxSize' => 50,
            'svgWidth' => '100%',
            'svgHeight' => '100%',
            'scale' => 5,
            'quietzoneSize' => 2,
        ]);

        $qrCode = (new QRCode($options))->render($url);

        return view('assets.qr', [
            'asset'  => $asset->load('location'),
            'qrCode' => $qrCode,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        $locations = Location::select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        $categories = AssetCategory::select('id', 'name')
            ->orderBy('name')
            ->get();

        $parents = Asset::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return [
            'locations'  => $locations,
            'categories' => $categories,
            'parents'    => $parents,
        ];
    }
}
