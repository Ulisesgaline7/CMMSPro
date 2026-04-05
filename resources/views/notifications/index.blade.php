<x-layouts.cmms title="Notificaciones" headerTitle="Notificaciones">

    <div class="p-6 max-w-3xl mx-auto space-y-5">

        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Notificaciones</h2>
            @if (auth()->user()->unreadNotifications()->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-sm font-semibold hover:bg-blue-100 transition-colors">
                        <i data-lucide="check-check" class="w-4 h-4"></i>
                        Marcar todas como leídas
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">
            @forelse ($notifications as $notif)
                <div class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition-colors {{ is_null($notif->read_at) ? 'bg-blue-50/30' : '' }}">
                    <div class="w-2.5 h-2.5 mt-1.5 rounded-full shrink-0 {{ is_null($notif->read_at) ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $notif->data['title'] ?? 'Notificación' }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $notif->data['message'] ?? '' }}</p>
                        @if (!empty($notif->data['url']))
                            <a href="{{ $notif->data['url'] }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Ver detalles →</a>
                        @endif
                        <p class="text-[11px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if (is_null($notif->read_at))
                        <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-300 hover:text-gray-500 transition-colors mt-1">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="px-5 py-16 text-center">
                    <i data-lucide="bell-off" class="w-10 h-10 mx-auto text-gray-300 mb-3"></i>
                    <p class="text-gray-400 font-medium">No hay notificaciones</p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div>{{ $notifications->links() }}</div>
        @endif

    </div>

</x-layouts.cmms>
