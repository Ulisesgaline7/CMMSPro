<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Asset;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = Document::with([
            'createdBy:id,name',
            'asset:id,name,code',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Document::count(),
            'draft' => Document::where('status', DocumentStatus::Draft)->count(),
            'review' => Document::where('status', DocumentStatus::Review)->count(),
            'approved' => Document::where('status', DocumentStatus::Approved)->count(),
        ];

        return view('documents.index', [
            'documents' => $documents,
            'filters' => $request->only(['search', 'status', 'type']),
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $assets = Asset::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('documents.create', [
            'assets' => $assets,
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::id();
        $data['status'] = DocumentStatus::Draft;
        $data['code'] = $this->generateCode();
        $data['current_version'] = $data['current_version'] ?? '1.0';

        $document = Document::create($data);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Documento creado correctamente.');
    }

    public function show(Document $document): View
    {
        $document->load([
            'createdBy:id,name',
            'approvedBy:id,name',
            'asset:id,name,code',
            'versions' => fn ($q) => $q->with('createdBy:id,name')->orderByDesc('created_at'),
        ]);

        return view('documents.show', [
            'document' => $document,
        ]);
    }

    public function edit(Document $document): View
    {
        $assets = Asset::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('documents.edit', [
            'document' => $document,
            'assets' => $assets,
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update($request->validated());

        return redirect()->route('documents.show', $document)
            ->with('success', 'Documento actualizado correctamente.');
    }

    private function generateCode(): string
    {
        $last = Document::withoutGlobalScopes()
            ->where('code', 'like', 'DOC-%')
            ->orderByDesc('id')
            ->value('code');

        $next = $last ? ((int) substr($last, 4)) + 1 : 1;

        return 'DOC-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
