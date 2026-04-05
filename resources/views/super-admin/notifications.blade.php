<x-layouts.super-admin title="Notificaciones" breadcrumb="Notificaciones">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Notificaciones</h1>
                <p class="text-sm mt-1" style="color:#64748b;">
                    {{ $unreadCount }} sin leer · {{ $totalCount }} en total
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('super-admin.notifications.mark-all-read') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="text-xs font-bold px-3 py-2 rounded-lg border transition-colors"
                                style="border-color:#e2e8f0; color:#475569;">
                            Marcar todas como leídas
                        </button>
                    </form>
                @endif
                @if($totalCount > 0)
                    <form method="POST" action="{{ route('super-admin.notifications.destroy-all') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('¿Eliminar todas las notificaciones?')"
                                class="text-xs font-bold px-3 py-2 rounded-lg transition-colors"
                                style="background:#fee2e2; color:#ef4444;">
                            Limpiar todo
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Panel de prueba push --}}
        <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#ede9fe;">
                    <i data-lucide="zap" class="w-3.5 h-3.5" style="color:#6366f1;"></i>
                </div>
                <p class="text-sm font-bold" style="color:#0f172a;">Probar Notificaciones Push en Tiempo Real</p>
                <span class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider" style="background:#fef9c3; color:#854d0e;">Reverb WebSocket</span>
            </div>
            <p class="text-xs mb-4" style="color:#64748b;">Dispara una notificación ahora. Aparecerá como toast en pantalla y como notificación nativa del navegador (si está permitido).</p>
            <div class="flex flex-wrap gap-2">
                @foreach([
                    ['type' => 'new_tenant',     'label' => '🏢 Nuevo Tenant',      'style' => 'background:#dcfce7; color:#166534;'],
                    ['type' => 'past_due',        'label' => '💳 Pago Vencido',      'style' => 'background:#fee2e2; color:#991b1b;'],
                    ['type' => 'suspended',       'label' => '🚫 Tenant Suspendido', 'style' => 'background:#fef9c3; color:#854d0e;'],
                    ['type' => 'system_error',    'label' => '🔴 Error Crítico',     'style' => 'background:#fee2e2; color:#991b1b;'],
                    ['type' => 'system_warning',  'label' => '🟡 Advertencia',       'style' => 'background:#fef9c3; color:#854d0e;'],
                    ['type' => 'system_success',  'label' => '🟢 Éxito',             'style' => 'background:#dcfce7; color:#166534;'],
                    ['type' => 'system_info',     'label' => '🔵 Información',       'style' => 'background:#ede9fe; color:#6d28d9;'],
                ] as $btn)
                    <form method="POST" action="{{ route('super-admin.notifications.test') }}">
                        @csrf
                        <input type="hidden" name="type" value="{{ $btn['type'] }}">
                        <button type="submit"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg transition-opacity hover:opacity-80"
                                style="{{ $btn['style'] }}">
                            {{ $btn['label'] }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        {{-- Filtros --}}
        <div class="flex items-center gap-2">
            @foreach(['all' => 'Todas', 'unread' => 'Sin leer', 'read' => 'Leídas'] as $key => $label)
                <a href="{{ route('super-admin.notifications.index') }}?filter={{ $key }}"
                   class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                   style="{{ $filter === $key ? 'background:#6366f1; color:#fff;' : 'background:#f1f5f9; color:#64748b;' }}">
                    {{ $label }}
                    @if($key === 'unread' && $unreadCount > 0)
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px] font-black"
                              style="background:rgba(255,255,255,0.3);">{{ $unreadCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Lista --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            @forelse($notifications as $notif)
                @php
                    $data    = $notif->data;
                    $isRead  = ! is_null($notif->read_at);
                    $icon    = $data['icon']  ?? 'bell';
                    $color   = $data['color'] ?? '#6366f1';
                    $title   = $data['title']   ?? 'Notificación';
                    $message = $data['message'] ?? '';
                    $url     = $data['url']     ?? '#';
                @endphp
                <div class="flex items-start gap-4 px-5 py-4 border-b transition-colors {{ $isRead ? 'opacity-60' : '' }} hover:bg-slate-50"
                     style="border-color:#f1f5f9;">

                    {{-- Icono --}}
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                         style="background:{{ $color }}18;">
                        <i data-lucide="{{ $icon }}" class="w-4 h-4" style="color:{{ $color }};"></i>
                    </div>

                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold {{ $isRead ? '' : '' }}" style="color:#0f172a;">
                                    @if(!$isRead)
                                        <span class="inline-block w-1.5 h-1.5 rounded-full mr-1.5 align-middle" style="background:#6366f1;"></span>
                                    @endif
                                    {{ $title }}
                                </p>
                                @if($message)
                                    <p class="text-xs mt-0.5 leading-relaxed" style="color:#64748b;">{{ $message }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-[10px]" style="color:#94a3b8;">{{ $notif->created_at->diffForHumans() }}</span>
                                    @if($url && $url !== '#')
                                        <a href="{{ $url }}"
                                           class="text-[10px] font-bold transition-colors"
                                           style="color:#6366f1;">
                                            Ver detalles →
                                        </a>
                                    @endif
                                </div>
                            </div>
                            {{-- Acciones --}}
                            <div class="flex items-center gap-1 shrink-0">
                                @if(!$isRead)
                                    <form method="POST" action="{{ route('super-admin.notifications.mark-read', $notif->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Marcar como leída"
                                                class="p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                                            <i data-lucide="check" class="w-3.5 h-3.5" style="color:#22c55e;"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('super-admin.notifications.destroy', $notif->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Eliminar"
                                            class="p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                        <i data-lucide="x" class="w-3.5 h-3.5" style="color:#94a3b8;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#f1f5f9;">
                        <i data-lucide="bell-off" class="w-6 h-6" style="color:#94a3b8;"></i>
                    </div>
                    <p class="text-sm font-semibold" style="color:#475569;">Sin notificaciones</p>
                    <p class="text-xs" style="color:#94a3b8;">
                        {{ $filter === 'unread' ? 'No tienes notificaciones pendientes' : 'El buzón está vacío' }}
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        @if($notifications->hasPages())
            <div>{{ $notifications->appends(['filter' => $filter])->links() }}</div>
        @endif

    </div>

</x-layouts.super-admin>
