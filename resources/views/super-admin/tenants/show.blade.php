<x-layouts.super-admin title="{{ $tenant->name }}" headerTitle="Super Admin — Tenant">

    <div class="p-6 space-y-5">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('super-admin.tenants.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-extrabold text-[#002046] font-headline">{{ $tenant->name }}</h2>
                    <p class="text-sm text-gray-400 font-mono">{{ $tenant->slug }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('super-admin.tenants.edit', $tenant->id) }}"
                   class="flex items-center gap-2 bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#1b365d] transition-colors">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Editar
                </a>
                @if ($tenant->status === \App\Enums\TenantStatus::Active)
                    <form method="POST" action="{{ route('super-admin.tenants.suspend', $tenant->id) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-100 transition-colors">
                            <i data-lucide="pause-circle" class="w-4 h-4"></i>
                            Suspender
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('super-admin.tenants.activate', $tenant->id) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-100 transition-colors">
                            <i data-lucide="play-circle" class="w-4 h-4"></i>
                            Activar
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-2xl font-extrabold text-[#002046] font-headline">{{ $tenant->users_count }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Usuarios</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-2xl font-extrabold text-blue-600 font-headline">{{ $tenant->assets_count }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Activos</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-2xl font-extrabold text-purple-600 font-headline">{{ $tenant->plan->label() }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Plan</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                @if ($tenant->subscription)
                    <p class="text-2xl font-extrabold text-green-600 font-headline">${{ number_format($tenant->subscription->total_monthly, 0) }}</p>
                @else
                    <p class="text-2xl font-extrabold text-gray-400 font-headline">$0</p>
                @endif
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">MRR</p>
            </div>
        </div>

        {{-- Info + Modules --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Tenant Info --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-[#002046] mb-4">Información</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Estado</dt>
                        <dd><span class="font-semibold {{ $tenant->status === \App\Enums\TenantStatus::Active ? 'text-green-600' : 'text-red-600' }}">{{ $tenant->status->label() }}</span></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email facturación</dt>
                        <dd class="font-medium">{{ $tenant->billing_email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Max usuarios</dt>
                        <dd class="font-medium">{{ number_format($tenant->max_users) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Max activos</dt>
                        <dd class="font-medium">{{ number_format($tenant->max_assets) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Creado</dt>
                        <dd class="font-medium">{{ $tenant->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Modules --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[#002046]">Módulos Activos</h3>
                    <a href="{{ route('super-admin.tenant-modules.index', $tenant->id) }}"
                       class="text-xs font-semibold text-blue-600 hover:underline">Gestionar</a>
                </div>
                @if ($tenant->modules->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tenant->modules->where('is_active', true) as $module)
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $module->module_key }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">Sin módulos adicionales activos.</p>
                @endif
            </div>

        </div>

    </div>

</x-layouts.super-admin>
