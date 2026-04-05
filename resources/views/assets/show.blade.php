<x-layouts.cmms :title="$asset->name" :headerTitle="$asset->code . ' – ' . $asset->name">

    @php
        $statusLabels = ['active' => 'Activo', 'inactive' => 'Inactivo', 'under_maintenance' => 'En Mantenimiento', 'retired' => 'Dado de Baja'];
        $statusColors = ['active' => 'bg-green-50 text-green-700 border-green-200', 'inactive' => 'bg-gray-100 text-gray-600 border-gray-200', 'under_maintenance' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'retired' => 'bg-red-50 text-red-600 border-red-200'];
        $criticalityLabels = ['low' => 'Baja', 'medium' => 'Media', 'high' => 'Alta', 'critical' => 'Crítica'];
        $criticalityColors = ['low' => 'text-gray-400', 'medium' => 'text-blue-500', 'high' => 'text-orange-500', 'critical' => 'text-red-600'];
        $typeAbbrev = ['corrective' => 'CM', 'preventive' => 'PM', 'predictive' => 'PdM'];
        $typeColors = ['corrective' => 'bg-red-100 text-red-700', 'preventive' => 'bg-blue-100 text-blue-700', 'predictive' => 'bg-purple-100 text-purple-700'];
        $woStatusColors = ['draft' => 'bg-gray-100 text-gray-600', 'pending' => 'bg-yellow-50 text-yellow-700', 'in_progress' => 'bg-blue-50 text-blue-700', 'on_hold' => 'bg-orange-50 text-orange-700', 'completed' => 'bg-green-50 text-green-700', 'cancelled' => 'bg-red-50 text-red-600'];
        $woStatusLabels = ['draft' => 'Borrador', 'pending' => 'Pendiente', 'in_progress' => 'En Progreso', 'on_hold' => 'En Pausa', 'completed' => 'Completada', 'cancelled' => 'Cancelada'];
        $sv = $asset->status->value;
        $cv = $asset->criticality->value;
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Breadcrumb + actions ────────────────────── --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('assets.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">Activos</a>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-[#002046]">{{ $asset->code }}</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('assets.qr', $asset) }}" target="_blank"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-semibold border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-gray-600">
                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                    QR
                </a>
                <a href="{{ route('work-orders.create') }}?asset_id={{ $asset->id }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-semibold border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-gray-600">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                    Nueva OT
                </a>
                <a href="{{ route('assets.edit', $asset) }}"
                   class="flex items-center gap-2 bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors shadow-sm">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    Editar
                </a>
            </div>
        </div>

        {{-- ── Header card ──────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#002046]/5 flex items-center justify-center shrink-0">
                        <i data-lucide="cpu" class="w-6 h-6 text-[#002046]"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-[#002046] font-headline">{{ $asset->name }}</h2>
                        <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $asset->code }}</p>
                        @if ($asset->category)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $asset->category->name }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $criticalityColors[$cv] }}">
                        <span class="w-2 h-2 rounded-full bg-current"></span>
                        {{ $criticalityLabels[$cv] ?? $cv }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $statusColors[$sv] ?? '' }}">
                        {{ $statusLabels[$sv] ?? $sv }}
                    </span>
                </div>
            </div>

            {{-- Details grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-5 border-t border-gray-100">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Marca / Modelo</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $asset->brand ?? '—' }}
                        @if ($asset->brand && $asset->model) · @endif
                        {{ $asset->model ?? '' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">N° de Serie</p>
                    <p class="text-sm font-mono text-gray-700">{{ $asset->serial_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Ubicación</p>
                    <p class="text-sm font-semibold text-gray-700">{{ optional($asset->location)->name ?? '—' }}</p>
                    @if ($asset->location?->parent)
                        <p class="text-xs text-gray-400">{{ $asset->location->parent->name }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Año Fabricación</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $asset->manufacture_year ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Left: Dates + Children + Notes ─────────── --}}
            <div class="space-y-5">

                {{-- Dates --}}
                @if ($asset->purchase_date || $asset->installation_date || $asset->warranty_expires_at || $asset->purchase_cost)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Fechas y Costo</h3>
                        <div class="space-y-2 text-sm">
                            @if ($asset->purchase_date)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Compra</span>
                                    <span class="font-semibold">{{ $asset->purchase_date->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if ($asset->installation_date)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Instalación</span>
                                    <span class="font-semibold">{{ $asset->installation_date->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if ($asset->warranty_expires_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Garantía</span>
                                    <span class="font-semibold {{ $asset->warranty_expires_at->isPast() ? 'text-red-600' : '' }}">
                                        {{ $asset->warranty_expires_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            @endif
                            @if ($asset->purchase_cost)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Costo</span>
                                    <span class="font-semibold">${{ number_format($asset->purchase_cost, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Parent asset --}}
                @if ($asset->parent)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Activo Padre</h3>
                        <a href="{{ route('assets.show', $asset->parent) }}"
                           class="text-sm font-semibold text-[#002046] hover:underline">
                            {{ $asset->parent->name }}
                        </a>
                        <p class="text-xs text-gray-400 font-mono">{{ $asset->parent->code }}</p>
                    </div>
                @endif

                {{-- Children --}}
                @if ($asset->children->isNotEmpty())
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Sub-activos ({{ $asset->children->count() }})</h3>
                        <div class="space-y-2">
                            @foreach ($asset->children as $child)
                                @php $csv = $child->status->value; @endphp
                                <div class="flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('assets.show', $child) }}"
                                           class="text-xs font-semibold text-[#002046] hover:underline">{{ $child->name }}</a>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $child->code }}</p>
                                    </div>
                                    <span class="text-[10px] font-medium {{ $statusColors[$csv] ?? '' }} px-1.5 py-0.5 rounded border">
                                        {{ $statusLabels[$csv] ?? $csv }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notes --}}
                @if ($asset->notes)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Notas</h3>
                        <p class="text-sm text-gray-600">{{ $asset->notes }}</p>
                    </div>
                @endif

            </div>

            {{-- ── Right: Work Orders ───────────────────── --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Órdenes de Trabajo</h3>
                        <a href="{{ route('work-orders.index') }}?search={{ $asset->code }}"
                           class="text-xs font-semibold text-[#002046] hover:underline">Ver todas →</a>
                    </div>

                    @forelse ($asset->workOrders as $wo)
                        @php
                            $wosv = $wo->status->value;
                            $wotv = $wo->type->value;
                        @endphp
                        <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $typeColors[$wotv] ?? 'bg-gray-100 text-gray-600' }} shrink-0">
                                    {{ $typeAbbrev[$wotv] ?? $wotv }}
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('work-orders.show', $wo) }}"
                                       class="text-sm font-semibold text-[#002046] hover:underline truncate block">
                                        {{ $wo->title }}
                                    </a>
                                    <span class="text-xs text-gray-400">{{ $wo->code }}</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded shrink-0 {{ $woStatusColors[$wosv] ?? '' }}">
                                {{ $woStatusLabels[$wosv] ?? $wosv }}
                            </span>
                        </div>
                    @empty
                        <div class="flex flex-col items-center py-8 text-center">
                            <i data-lucide="clipboard-list" class="w-8 h-8 text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">Sin órdenes de trabajo</p>
                            <a href="{{ route('work-orders.create') }}"
                               class="mt-2 text-xs font-semibold text-[#002046] hover:underline">
                                Crear primera OT →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</x-layouts.cmms>
