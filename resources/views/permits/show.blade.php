<x-layouts.cmms :title="$permit->code" :headerTitle="$permit->code . ' — ' . $permit->title">

    <div class="p-6 space-y-5">

        {{-- Breadcrumb + actions --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('permits.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">Permisos</a>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-[#002046]">{{ $permit->code }}</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Submit for approval --}}
                @if ($permit->status->value === 'draft')
                    <form method="POST" action="{{ route('permits.submit', $permit) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold border border-yellow-300 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Enviar para Aprobación
                        </button>
                    </form>
                    <a href="{{ route('permits.edit', $permit) }}"
                       class="flex items-center gap-2 bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors shadow-sm">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        Editar
                    </a>
                @endif

                {{-- Approve / Reject --}}
                @if ($permit->status->value === 'pending_approval')
                    <form method="POST" action="{{ route('permits.approve', $permit) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Aprobar
                        </button>
                    </form>
                @endif

                {{-- Activate --}}
                @if ($permit->status->value === 'approved')
                    <form method="POST" action="{{ route('permits.activate', $permit) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                            Activar Permiso
                        </button>
                    </form>
                @endif

                {{-- Close --}}
                @if ($permit->status->value === 'active')
                    <form method="POST" action="{{ route('permits.close', $permit) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors"
                                onclick="return confirm('¿Confirmar cierre del permiso?')">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            Cerrar Permiso
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Left: main details --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#002046]/5 flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $permit->type->icon() }}" class="w-6 h-6 text-[#002046]"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-[#002046] font-headline">{{ $permit->title }}</h2>
                                <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $permit->code }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $permit->type->label() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded border text-xs font-medium {{ $permit->risk_level->color() }}">
                                {{ $permit->risk_level->label() }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-xs font-semibold {{ $permit->status->color() }}">
                                {{ $permit->status->label() }}
                            </span>
                        </div>
                    </div>

                    @if ($permit->description)
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Descripción del Trabajo</p>
                            <p class="text-sm text-gray-600">{{ $permit->description }}</p>
                        </div>
                    @endif

                    @if ($permit->lockout_points)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Puntos de Bloqueo (LOTO)</p>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $permit->lockout_points }}</p>
                        </div>
                    @endif

                    @if ($permit->required_ppe)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">EPP Requerido</p>
                            <p class="text-sm text-gray-600">{{ $permit->required_ppe }}</p>
                        </div>
                    @endif

                    @if ($permit->precautions)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Precauciones</p>
                            <p class="text-sm text-gray-600">{{ $permit->precautions }}</p>
                        </div>
                    @endif
                </div>

                {{-- OT vinculada --}}
                @if ($permit->workOrder)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Orden de Trabajo Vinculada</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('work-orders.show', $permit->workOrder) }}"
                                   class="text-sm font-bold text-[#002046] hover:underline">
                                    {{ $permit->workOrder->title }}
                                </a>
                                <p class="text-xs text-gray-400 font-mono">{{ $permit->workOrder->code }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                {{ $permit->workOrder->status->label() }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Rechazo --}}
                @if ($permit->rejection_reason)
                    <div class="bg-red-50 rounded-xl border border-red-200 p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-red-400 mb-2">Motivo de Rechazo</h3>
                        <p class="text-sm text-red-700">{{ $permit->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Right: timeline --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Trazabilidad</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Solicitado por</span>
                            <span class="font-semibold text-gray-700">{{ $permit->requester?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fecha solicitud</span>
                            <span class="font-semibold">{{ $permit->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($permit->approved_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Aprobado por</span>
                                <span class="font-semibold text-gray-700">{{ $permit->approver?->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Fecha aprobación</span>
                                <span class="font-semibold">{{ $permit->approved_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if ($permit->activated_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Activado</span>
                                <span class="font-semibold text-green-700">{{ $permit->activated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if ($permit->expires_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Vence</span>
                                <span class="font-semibold {{ $permit->isExpired() ? 'text-red-600' : '' }}">
                                    {{ $permit->expires_at->format('d/m/Y H:i') }}
                                    @if ($permit->isExpired())
                                        <span class="text-[10px] font-bold bg-red-100 text-red-700 px-1 py-0.5 rounded ml-1">VENCIDO</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                        @if ($permit->closed_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Cerrado</span>
                                <span class="font-semibold">{{ $permit->closed_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($permit->closure_notes)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Notas de Cierre</h3>
                        <p class="text-sm text-gray-600">{{ $permit->closure_notes }}</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

</x-layouts.cmms>
