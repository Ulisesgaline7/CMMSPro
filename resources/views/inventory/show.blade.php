<x-layouts.cmms :title="$part->name" :headerTitle="($part->part_number ? $part->part_number . ' – ' : '') . $part->name">

    <div class="p-6 space-y-5">

        {{-- ── Breadcrumb + actions ────────────────────── --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('inventory.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">Inventario</a>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-[#002046]">{{ $part->part_number ?? $part->name }}</span>
            </div>
            <a href="{{ route('inventory.edit', $part) }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Editar
            </a>
        </div>

        {{-- ── Header card ──────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#002046]/5 flex items-center justify-center shrink-0">
                        <i data-lucide="package" class="w-6 h-6 text-[#002046]"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-[#002046] font-headline">{{ $part->name }}</h2>
                        @if ($part->part_number)
                            <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $part->part_number }}</p>
                        @endif
                        @if ($part->brand)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $part->brand }}</p>
                        @endif
                    </div>
                </div>

                {{-- Stock badge --}}
                @php $belowMin = $part->isBelowMinStock(); @endphp
                <div class="flex flex-col items-end gap-1">
                    <div class="text-3xl font-extrabold {{ $belowMin ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $part->stock_quantity }}
                        <span class="text-base font-semibold text-gray-400">{{ $part->unit }}</span>
                    </div>
                    @if ($belowMin)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-lg">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            STOCK BAJO — mín. {{ $part->min_stock }} {{ $part->unit }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-lg">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Stock OK — mín. {{ $part->min_stock }} {{ $part->unit }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Details grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-5 border-t border-gray-100">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Número de Parte</p>
                    <p class="text-sm font-mono text-gray-700">{{ $part->part_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Marca</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $part->brand ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Ubicación en Almacén</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $part->storage_location ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Costo Unitario</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $part->unit_cost ? '$' . number_format($part->unit_cost, 2) : '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Details + Notes ──────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Left: Stock details --}}
            <div class="space-y-5">

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Control de Stock</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Stock actual</span>
                            <span class="text-sm font-bold {{ $belowMin ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $part->stock_quantity }} {{ $part->unit }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Stock mínimo</span>
                            <span class="text-sm font-semibold text-gray-600">{{ $part->min_stock }} {{ $part->unit }}</span>
                        </div>
                        @if ($part->unit_cost)
                            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                <span class="text-sm text-gray-500">Valor en stock</span>
                                <span class="text-sm font-bold text-gray-800">
                                    ${{ number_format($part->unit_cost * $part->stock_quantity, 2) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Stock bar --}}
                    @if ($part->min_stock > 0)
                        @php
                            $pct = min(100, round($part->stock_quantity / max($part->min_stock, 1) * 100));
                        @endphp
                        <div class="mt-4">
                            <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                                <span>0</span>
                                <span>{{ $pct }}% del mínimo</span>
                                <span>mín {{ $part->min_stock }}</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $belowMin ? 'bg-red-400' : 'bg-green-400' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($part->description)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Descripción</h3>
                        <p class="text-sm text-gray-600">{{ $part->description }}</p>
                    </div>
                @endif

            </div>

            {{-- Right: Usage history placeholder --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Uso en Órdenes de Trabajo</h3>
                    @if ($part->workOrderParts->isEmpty())
                        <div class="flex flex-col items-center py-8 text-center">
                            <i data-lucide="clipboard-list" class="w-8 h-8 text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">No se ha usado en ninguna OT</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($part->workOrderParts as $wop)
                                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">{{ $wop->quantity }} {{ $wop->unit }}</p>
                                        <p class="text-xs text-gray-400">{{ $wop->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    @if ($wop->unit_cost)
                                        <span class="text-xs text-gray-500">${{ number_format($wop->unit_cost * $wop->quantity, 2) }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</x-layouts.cmms>
