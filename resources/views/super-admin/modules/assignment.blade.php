<x-layouts.super-admin title="Asignación por Cliente" breadcrumb="Asignación por Cliente">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Asignación por Cliente</h1>
                <p class="text-sm mt-1" style="color:#64748b;">Vista global de módulos activos por cada tenant</p>
            </div>
            <a href="{{ route('super-admin.modules.index') }}"
               class="text-xs font-bold px-3 py-1.5 rounded-lg border transition-colors"
               style="border-color:#e2e8f0; color:#475569;">
                ← Catálogo
            </a>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Módulos por Cliente</p>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:#ede9fe; color:#6d28d9;">
                    {{ $tenants->total() }} clientes
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                            <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Cliente</th>
                            <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Plan</th>
                            <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Módulos Activos</th>
                            <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Módulos</th>
                            <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                            @php
                                $activeKeys = $tenant->modules->pluck('module_key')->toArray();
                            @endphp
                            <tr class="border-b transition-colors hover:bg-slate-50" style="border-color:#f1f5f9;">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold" style="color:#0f172a;">{{ $tenant->name }}</p>
                                    <p class="text-xs" style="color:#94a3b8;">{{ $tenant->slug }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold capitalize" style="color:#475569;">
                                    {{ $tenant->plan?->label() ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-lg font-black" style="color:#6366f1;">{{ $tenant->active_modules_count }}</span>
                                    <span class="text-xs ml-1" style="color:#94a3b8;">/ {{ count($modules) }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($modules as $module)
                                            @if(in_array($module->value, $activeKeys))
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded"
                                                      style="background:#ede9fe; color:#6d28d9;"
                                                      title="{{ $module->label() }}">
                                                    {{ strtoupper(substr($module->value, 0, 3)) }}
                                                </span>
                                            @endif
                                        @endforeach
                                        @if(empty($activeKeys))
                                            <span class="text-xs" style="color:#94a3b8;">Sin módulos</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('super-admin.tenant-modules.index', $tenant->id) }}"
                                       class="text-xs font-bold px-3 py-1 rounded-lg transition-colors"
                                       style="background:#ede9fe; color:#6d28d9;">
                                        Gestionar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-sm" style="color:#94a3b8;">
                                    Sin clientes registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tenants->hasPages())
                <div class="px-5 py-4 border-t" style="border-color:#f1f5f9;">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>

    </div>

</x-layouts.super-admin>
