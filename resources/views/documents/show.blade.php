<x-layouts.cmms :title="$document->code" :headerTitle="$document->code . ' – ' . $document->title">

    <div class="p-6 space-y-5">

        {{-- ── Breadcrumb + actions ────────────────────── --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('documents.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                    Documentos
                </a>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-[#002046]">{{ $document->code }}</span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('documents.edit', $document) }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-semibold border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-gray-600">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    Editar
                </a>
            </div>
        </div>

        {{-- ── Header card ─────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-wrap mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700">
                            {{ $document->type->label() }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border {{ $document->status->color() }}">
                            {{ $document->status->label() }}
                        </span>
                        @if ($document->category)
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                {{ ucfirst($document->category) }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">
                        {{ $document->title }}
                    </h1>
                    <p class="text-sm text-gray-400 font-mono mt-1">{{ $document->code }} · v{{ $document->current_version }}</p>
                    @if ($document->description)
                        <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $document->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Metadata ─────────────────────────────── --}}
            <div class="lg:col-span-1 space-y-5">

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Detalles</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Versión</dt>
                            <dd class="font-semibold text-[#002046] font-mono">v{{ $document->current_version }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Creado por</dt>
                            <dd class="font-semibold text-gray-700">{{ optional($document->createdBy)->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Fecha de creación</dt>
                            <dd class="text-gray-600">{{ $document->created_at->format('d/m/Y') }}</dd>
                        </div>
                        @if ($document->approved_at)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Aprobado por</dt>
                                <dd class="font-semibold text-gray-700">{{ optional($document->approvedBy)->name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Fecha aprobación</dt>
                                <dd class="text-gray-600">{{ $document->approved_at->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <dt class="text-gray-500">Fecha de revisión</dt>
                            <dd class="{{ $document->review_date && $document->review_date->isPast() && $document->status->value !== 'obsolete' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                @if ($document->review_date)
                                    {{ $document->review_date->format('d/m/Y') }}
                                    @if ($document->review_date->isPast() && $document->status->value !== 'obsolete')
                                        <span class="text-xs text-red-500">(vencida)</span>
                                    @endif
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Asset --}}
                @if ($document->asset)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Activo Relacionado</h3>
                        <a href="{{ route('assets.show', $document->asset) }}"
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-[#002046]/10 flex items-center justify-center shrink-0">
                                <i data-lucide="cpu" class="w-4 h-4 text-[#002046]"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#002046] truncate group-hover:underline">{{ $document->asset->name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $document->asset->code }}</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 ml-auto shrink-0"></i>
                        </a>
                    </div>
                @endif

            </div>

            {{-- ── Versions history ─────────────────────── --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-[#002046]">Historial de Versiones</h3>
                        <span class="text-xs text-gray-400">{{ $document->versions->count() }} {{ $document->versions->count() === 1 ? 'versión' : 'versiones' }}</span>
                    </div>

                    @if ($document->versions->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <i data-lucide="git-branch" class="w-8 h-8 text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">Sin versiones registradas</p>
                        </div>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/60 border-b border-gray-100">
                                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Versión</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Resumen de cambios</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Autor</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($document->versions as $version)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <span class="font-mono text-xs font-bold text-[#002046] bg-[#002046]/10 px-2 py-0.5 rounded">
                                                v{{ $version->version }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">
                                            {{ $version->change_summary ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">
                                            {{ optional($version->createdBy)->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3.5 text-gray-500 text-xs">
                                            {{ $version->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>

    </div>

</x-layouts.cmms>
