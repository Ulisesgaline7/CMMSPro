<x-layouts.super-admin title="Editar Tenant" headerTitle="Super Admin — Editar Tenant">

    <div class="p-6 space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.tenants.show', $tenant->id) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline">Editar: {{ $tenant->name }}</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('super-admin.tenants.update', $tenant->id) }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5 max-w-2xl">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Plan</label>
                    <select name="plan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->value }}" {{ $tenant->plan->value === $plan->value ? 'selected' : '' }}>
                                {{ $plan->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Estado</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ $tenant->status->value === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Email de Facturación</label>
                    <input type="email" name="billing_email" value="{{ old('billing_email', $tenant->billing_email) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Max Usuarios</label>
                    <input type="number" name="max_users" value="{{ old('max_users', $tenant->max_users) }}" min="1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Max Activos</label>
                    <input type="number" name="max_assets" value="{{ old('max_assets', $tenant->max_assets) }}" min="1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="bg-[#002046] text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors">
                    Guardar Cambios
                </button>
                <a href="{{ route('super-admin.tenants.show', $tenant->id) }}"
                   class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>

    </div>

</x-layouts.super-admin>
