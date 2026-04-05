<x-layouts.super-admin title="Tenants" headerTitle="Super Admin — Tenants">

    <div class="p-6 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Tenants</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $tenants->total() }} empresas registradas</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('super-admin.tenants.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por nombre o slug..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>
            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none">
                <option value="">Todos los estados</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Empresa</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Plan</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Usuarios</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">MRR</th>
                        <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($tenants as $tenant)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-[#002046]">{{ $tenant->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $tenant->slug }}</div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">{{ $tenant->plan->label() }}</td>
                            <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">{{ $tenant->users_count }}</td>
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                @if ($tenant->subscription)
                                    <span class="font-semibold text-green-600">${{ number_format($tenant->subscription->total_monthly, 0) }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border
                                    {{ $tenant->status === \App\Enums\TenantStatus::Active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                    {{ $tenant->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('super-admin.tenants.show', $tenant->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-[#002046] hover:underline">
                                    Ver <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-gray-400">No se encontraron tenants.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tenants->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $tenants->firstItem() }}–{{ $tenants->lastItem() }} de {{ $tenants->total() }}</span>
                {{ $tenants->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.super-admin>
