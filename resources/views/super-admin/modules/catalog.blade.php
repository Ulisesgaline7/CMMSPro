<x-layouts.super-admin title="Catálogo de Módulos" breadcrumb="Catálogo de Módulos">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Catálogo de Módulos</h1>
                <p class="text-sm mt-1" style="color:#64748b;">Todos los módulos disponibles en la plataforma y su adopción</p>
            </div>
            <a href="{{ route('super-admin.modules.assignment') }}"
               class="text-sm font-bold px-4 py-2 rounded-lg transition-colors"
               style="background:#6366f1; color:#fff;">
                Asignación por Cliente →
            </a>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Módulos Disponibles</p>
                <p class="text-3xl font-black" style="color:#6366f1; font-variant-numeric:tabular-nums;">{{ count($modules) }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Total Activaciones</p>
                <p class="text-3xl font-black" style="color:#0f172a; font-variant-numeric:tabular-nums;">{{ $activationCounts->sum() }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Ingresos por Módulos</p>
                <p class="text-3xl font-black" style="color:#16a34a; font-variant-numeric:tabular-nums;">
                    ${{ number_format($totalModuleRevenue, 0) }}/mes
                </p>
            </div>
        </div>

        {{-- Grid de módulos --}}
        <div class="grid grid-cols-3 gap-4">
            @foreach($modules as $module)
                @php
                    $count = $activationCounts[$module->value] ?? 0;
                    $adoptionPct = $totalTenants > 0 ? round($count / $totalTenants * 100) : 0;
                    $isCore = $module->value === 'core';
                @endphp
                <div class="bg-white rounded-xl border p-5 space-y-3" style="border-color:{{ $count > 0 ? '#c7d2fe' : '#e2e8f0' }};">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-sm" style="color:#0f172a;">{{ $module->label() }}</span>
                                @if($isCore)
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded" style="background:#dcfce7; color:#166534;">CORE</span>
                                @endif
                            </div>
                            <p class="text-[11px] mt-1 leading-relaxed" style="color:#64748b;">{{ $module->description() }}</p>
                        </div>
                        <div class="ml-3 text-right shrink-0">
                            @if($module->price() > 0)
                                <p class="text-sm font-black" style="color:#16a34a;">${{ $module->price() }}</p>
                                <p class="text-[10px]" style="color:#94a3b8;">/mes</p>
                            @else
                                <p class="text-xs font-bold" style="color:#94a3b8;">Incluido</p>
                            @endif
                        </div>
                    </div>

                    {{-- Adopción --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-semibold" style="color:#94a3b8;">Adopción</span>
                            <span class="text-[10px] font-bold" style="color:#6366f1;">{{ $count }}/{{ $totalTenants }} clientes ({{ $adoptionPct }}%)</span>
                        </div>
                        <div class="h-1.5 rounded-full" style="background:#f1f5f9;">
                            <div class="h-1.5 rounded-full transition-all" style="width:{{ $adoptionPct }}%; background:{{ $adoptionPct > 50 ? '#22c55e' : '#6366f1' }};"></div>
                        </div>
                    </div>

                    {{-- Revenue from module --}}
                    @if($module->price() > 0 && $count > 0)
                        <p class="text-[11px] font-semibold" style="color:#16a34a;">
                            + ${{ number_format($module->price() * $count, 0) }}/mes en ingresos
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

    </div>

</x-layouts.super-admin>
