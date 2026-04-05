<x-layouts.cmms title="Control de Documentos" headerTitle="Control de Documentos">

    <div class="p-6 space-y-5">

        {{-- ── Stats row ────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                $statCards = [
                    ['label' => 'Total',        'value' => $stats['total'],    'color' => 'text-[#002046]'],
                    ['label' => 'Borrador',     'value' => $stats['draft'],    'color' => 'text-gray-500'],
                    ['label' => 'En Revisión',  'value' => $stats['review'],   'color' => 'text-yellow-600'],
                    ['label' => 'Aprobados',    'value' => $stats['approved'], 'color' => 'text-green-600'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 text-center">
                    <p class="text-2xl font-extrabold {{ $card['color'] }} font-headline">{{ $card['value'] }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── Header + search ─────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Documentos</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $documents->total() }} {{ $documents->total() === 1 ? 'documento registrado' : 'documentos registrados' }}</p>
            </div>
            <a href="{{ route('documents.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nuevo Documento
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('documents.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por código, título..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>

            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Borrador</option>
                <option value="review" {{ ($filters['status'] ?? '') === 'review' ? 'selected' : '' }}>En Revisión</option>
                <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Aprobado</option>
                <option value="obsolete" {{ ($filters['status'] ?? '') === 'obsolete' ? 'selected' : '' }}>Obsoleto</option>
            </select>

            <select name="type" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los tipos</option>
                <option value="procedure" {{ ($filters['type'] ?? '') === 'procedure' ? 'selected' : '' }}>Procedimiento</option>
                <option value="manual" {{ ($filters['type'] ?? '') === 'manual' ? 'selected' : '' }}>Manual</option>
                <option value="certificate" {{ ($filters['type'] ?? '') === 'certificate' ? 'selected' : '' }}>Certificado</option>
                <option value="regulation" {{ ($filters['type'] ?? '') === 'regulation' ? 'selected' : '' }}>Reglamento</option>
                <option value="form" {{ ($filters['type'] ?? '') === 'form' ? 'selected' : '' }}>Formato</option>
                <option value="report" {{ ($filters['type'] ?? '') === 'report' ? 'selected' : '' }}>Reporte</option>
                <option value="other" {{ ($filters['type'] ?? '') === 'other' ? 'selected' : '' }}>Otro</option>
            </select>

            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('documents.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($documents->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="file-text" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron documentos</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Crea el primer documento' }}
                    </p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Documento</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Tipo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Versión</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Revisión</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($documents as $document)
                            @php
                                $statusVal = $document->status->value;
                                $typeVal   = $document->type->value;
                                $isOverdue = $document->review_date && $document->review_date->isPast()
                                    && !in_array($statusVal, ['obsolete']);
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $document->title }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $document->code }}</div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $document->type->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600 font-mono text-xs">
                                    v{{ $document->current_version }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($document->review_date)
                                        <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-600' }} flex items-center gap-1">
                                            @if ($isOverdue)
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            @endif
                                            {{ $document->review_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $document->status->color() }}">
                                        {{ $document->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('documents.show', $document) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#002046] hover:underline">
                                        Ver
                                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ── Pagination ───────────────────────────────── --}}
        @if ($documents->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $documents->firstItem() }}–{{ $documents->lastItem() }} de {{ $documents->total() }}</span>
                {{ $documents->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
