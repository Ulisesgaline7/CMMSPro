<x-layouts.super-admin title="Usuarios — Super Admin" headerTitle="Super Admin — Usuarios">

    <div class="p-6 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Todos los Usuarios</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $users->total() }} usuarios en todos los tenants</p>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('super-admin.users.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex gap-3 items-center">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por nombre o email..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Usuario</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Tenant</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Rol</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-[#002046]">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">
                                {{ $user->tenant?->name ?? 'Super Admin' }}
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                @if ($user->is_super_admin)
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-700">Super Admin</span>
                                @elseif ($user->role)
                                    <span class="text-gray-600">{{ $user->role->label() }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($user->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                        {{ $user->status === \App\Enums\UserStatus::Active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ $user->status->label() }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                @if (! $user->is_super_admin && $user->tenant_id)
                                    <form method="POST" action="{{ route('super-admin.users.impersonate', $user->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-orange-600 hover:underline">
                                            Impersonar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-gray-400">No se encontraron usuarios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}</span>
                {{ $users->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.super-admin>
