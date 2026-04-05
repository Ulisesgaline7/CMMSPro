<x-layouts.cmms title="Permisos de Trabajo" headerTitle="LOTO / Permisos de Trabajo">

    <div class="p-6 space-y-5">

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                $statCards = [
                    ['label' => 'Total',              'value' => $stats['total'],            'color' => 'text-[#002046]'],
                    ['label' => 'Pend. Aprobación',   'value' => $stats['pending_approval'], 'color' => 'text-yellow-600'],
                    ['label' => 'Activos',             'value' => $stats['active'],           'color' => 'text-green-600'],
                    ['label' => 'Próx. a Vencer',     'value' => $stats['expiring_soon'],    'color' => 'text-red-600'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 text-center">
                    <p class="text-2xl font-extrabold {{ $card['color'] }} font-headline">{{ $card['value'] }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Permisos de Trabajo</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $permits->total() }} {{ $permits->total() === 1 ? 'permiso registrado' : 'permisos registrados' }}</p>
            </div>
            <a href="{{ route('permits.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nuevo Permiso
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('permits.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por código o título..."
                       class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s->value }}" @selected(($filters['status'] ?? '') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
            <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los tipos</option>
                @foreach ($types as $t)
                    <option value="{{ $t->value }}" @selected(($filters['type'] ?? '') === $t->value)>{{ $t->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#1b365d] transition-colors">
                Filtrar
            </button>
            @if (array_filter($filters))
                <a href="{{ route('permits.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Limpiar</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @forelse ($permits as $permit)
                @php
                    $isExpiring = $permit->expires_at && $permit->expires_at->diffInHours(now()) <= 2 && $permit->status->value === 'active';
                @endphp
                <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                    {{-- Icon --}}
                    <div class="w-9 h-9 rounded-lg bg-[#002046]/5 flex items-center justify-center shrink-0">
                        <i data-lucide="{{ $permit->type->icon() }}" class="w-4 h-4 text-[#002046]"></i>
                    </div>

                    {{-- Main info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('permits.show', $permit) }}"
                               class="text-sm font-bold text-[#002046] hover:underline truncate">
                                {{ $permit->title }}
                            </a>
                            @if ($isExpiring)
                                <span class="text-[10px] font-bold bg-red-100 text-red-700 px-1.5 py-0.5 rounded">¡VENCE PRONTO!</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-0.5">
                            <span class="text-xs text-gray-400 font-mono">{{ $permit->code }}</span>
                            <span class="text-xs text-gray-400">{{ $permit->type->label() }}</span>
                            @if ($permit->workOrder)
                                <span class="text-xs text-gray-400">OT: {{ $permit->workOrder->code }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Risk + Status --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded border text-xs font-medium {{ $permit->risk_level->color() }}">
                            {{ $permit->risk_level->label() }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-xs font-semibold {{ $permit->status->color() }}">
                            {{ $permit->status->label() }}
                        </span>
                    </div>

                    {{-- Date --}}
                    <div class="text-right shrink-0 hidden md:block">
                        <p class="text-xs text-gray-400">{{ $permit->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-300">{{ $permit->requester?->name }}</p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center py-12 text-center">
                    <i data-lucide="shield-off" class="w-10 h-10 text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-400">No hay permisos de trabajo registrados</p>
                    <a href="{{ route('permits.create') }}" class="mt-2 text-xs font-semibold text-[#002046] hover:underline">
                        Crear primer permiso →
                    </a>
                </div>
            @endforelse
        </div>

        {{ $permits->links() }}

    </div>

</x-layouts.cmms>
