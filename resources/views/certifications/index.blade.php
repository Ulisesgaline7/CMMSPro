<x-layouts.cmms title="Certificaciones" headerTitle="Certificaciones">

    <div class="p-6 space-y-5">

        {{-- ── Stats row ────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                $statCards = [
                    ['label' => 'Total',       'value' => $stats['total'],         'color' => 'text-[#002046]'],
                    ['label' => 'Activas',     'value' => $stats['active'],        'color' => 'text-green-600'],
                    ['label' => 'Vencidas',    'value' => $stats['expired'],       'color' => 'text-red-600'],
                    ['label' => 'Por Vencer',  'value' => $stats['expiring_soon'], 'color' => 'text-yellow-600'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 text-center">
                    <p class="text-2xl font-extrabold {{ $card['color'] }} font-headline">{{ $card['value'] }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── Header + button ──────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Certificaciones</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $certifications->total() }} {{ $certifications->total() === 1 ? 'certificación registrada' : 'certificaciones registradas' }}</p>
            </div>
            <a href="{{ route('certifications.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nueva Certificación
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('certifications.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                @foreach (\App\Enums\CertificationStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            <select name="user_id" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los técnicos</option>
                @foreach ($technicians as $technician)
                    <option value="{{ $technician->id }}" {{ ($filters['user_id'] ?? '') == $technician->id ? 'selected' : '' }}>
                        {{ $technician->name }}
                    </option>
                @endforeach
            </select>

            @if (array_filter($filters))
                <a href="{{ route('certifications.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($certifications->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="award" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron certificaciones</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Registra la primera certificación' }}
                    </p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Técnico</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Certificación</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Entidad</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Emisión</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Vencimiento</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($certifications as $cert)
                            @php
                                $isExpiringSoon = $cert->status === \App\Enums\CertificationStatus::Active
                                    && $cert->expires_at
                                    && $cert->expires_at->lte(now()->addDays(30));
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ optional($cert->user)->name ?? '—' }}</div>
                                    @if(optional($cert->user)->employee_code)
                                        <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $cert->user->employee_code }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-gray-800">{{ $cert->name }}</div>
                                    @if ($cert->certificate_number)
                                        <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $cert->certificate_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">{{ $cert->issuing_body }}</td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">
                                    {{ $cert->issued_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($cert->expires_at)
                                        <span class="{{ $isExpiringSoon ? 'text-yellow-600 font-semibold' : ($cert->status === \App\Enums\CertificationStatus::Expired ? 'text-red-600 font-semibold' : 'text-gray-600') }}">
                                            {{ $cert->expires_at->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">Sin vencimiento</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $cert->status->color() }}">
                                        {{ $cert->status->label() }}
                                    </span>
                                    @if ($isExpiringSoon)
                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-orange-50 text-orange-700 border-orange-200">
                                            Por vencer
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('certifications.show', $cert) }}"
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
        @if ($certifications->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $certifications->firstItem() }}–{{ $certifications->lastItem() }} de {{ $certifications->total() }}</span>
                {{ $certifications->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
