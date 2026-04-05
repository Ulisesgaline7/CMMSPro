<x-layouts.cmms title="Facturación & Suscripción" headerTitle="Facturación">

    <div class="p-6 space-y-6" x-data="{
        deploymentType: '{{ $subscription?->deployment_type ?? 'cloud_saas' }}',
        selectedModules: @js(array_values($subscription?->modules_json ?? [])),
        adminCount: {{ $subscription?->admin_count ?? 1 }},
        supervisorCount: {{ $subscription?->supervisor_count ?? 0 }},
        technicianCount: {{ $subscription?->technician_count ?? 0 }},
        readerCount: {{ $subscription?->reader_count ?? 0 }},
        deploymentPrices: {
            @foreach ($deploymentTypes as $dt)
            '{{ $dt->value }}': {{ $dt->basePrice() }},
            @endforeach
        },
        modulePrices: {
            @foreach ($modules as $m)
            '{{ $m->value }}': {{ $m->price() }},
            @endforeach
        },
        get basePrice() {
            return this.deploymentPrices[this.deploymentType] || 49;
        },
        get modulesTotal() {
            return this.selectedModules.reduce((sum, key) => sum + (this.modulePrices[key] || 0), 0);
        },
        get totalMonthly() {
            return this.basePrice + this.modulesTotal;
        },
        toggleModule(key) {
            const idx = this.selectedModules.indexOf(key);
            if (idx > -1) {
                this.selectedModules.splice(idx, 1);
            } else {
                this.selectedModules.push(key);
            }
        },
        isSelected(key) {
            return this.selectedModules.includes(key);
        }
    }">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Suscripción & Facturación</h2>
                <p class="text-sm text-gray-400 mt-0.5">Configura tu plan y módulos activos</p>
            </div>
            @if ($subscription && $tenant?->stripe_customer_id)
                <a href="{{ route('billing.portal') }}"
                   class="flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Portal de Facturación
                </a>
            @endif
        </div>

        {{-- Current Subscription Status --}}
        @if ($subscription)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Suscripción Actual</p>
                        <p class="text-2xl font-extrabold text-[#002046] font-headline mt-1">${{ number_format($subscription->total_monthly, 2) }}/mes</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $subscription->status->color() }}">
                        {{ $subscription->status->label() }}
                    </span>
                </div>
                @if ($subscription->current_period_end)
                    <p class="text-xs text-gray-400 mt-2">Próxima renovación: {{ $subscription->current_period_end->format('d/m/Y') }}</p>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('billing.checkout.store') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left column: Deployment + Users --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Deployment Type --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-sm font-bold text-[#002046] uppercase tracking-wider mb-4">Tipo de Despliegue</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($deploymentTypes as $dt)
                                <label
                                    class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                                    :class="deploymentType === '{{ $dt->value }}' ? 'border-[#002046] bg-blue-50' : 'border-gray-100 hover:border-gray-300'"
                                >
                                    <input type="radio" name="deployment_type" value="{{ $dt->value }}"
                                           x-model="deploymentType" class="sr-only">
                                    <span class="font-bold text-sm text-[#002046]">{{ $dt->label() }}</span>
                                    <span class="text-xs text-gray-500 mt-0.5">Desde ${{ $dt->basePrice() }}/mes</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Modules --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-sm font-bold text-[#002046] uppercase tracking-wider mb-4">Módulos Adicionales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($modules as $module)
                                @if ($module->value !== 'core')
                                    <label
                                        class="relative flex items-start p-3 rounded-xl border-2 cursor-pointer transition-all"
                                        :class="isSelected('{{ $module->value }}') ? 'border-[#002046] bg-blue-50' : 'border-gray-100 hover:border-gray-200'"
                                        @click="toggleModule('{{ $module->value }}')"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-[#002046]">{{ $module->label() }}</span>
                                                <span class="text-xs font-bold text-gray-600 ml-2">
                                                    {{ $module->price() > 0 ? '+$' . $module->price() . '/mes' : 'Gratis' }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">{{ $module->description() }}</p>
                                        </div>
                                        <div class="ml-2 mt-0.5 shrink-0 w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                             :class="isSelected('{{ $module->value }}') ? 'border-[#002046] bg-[#002046]' : 'border-gray-300'">
                                            <i data-lucide="check" class="w-2.5 h-2.5 text-white" x-show="isSelected('{{ $module->value }}')"></i>
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                        {{-- Hidden inputs for selected modules --}}
                        <template x-for="moduleKey in selectedModules" :key="moduleKey">
                            <input type="hidden" name="modules[]" :value="moduleKey">
                        </template>
                    </div>

                    {{-- User Seats --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-sm font-bold text-[#002046] uppercase tracking-wider mb-4">Usuarios por Rol</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ([['label' => 'Admins', 'name' => 'admin_count', 'model' => 'adminCount'], ['label' => 'Supervisores', 'name' => 'supervisor_count', 'model' => 'supervisorCount'], ['label' => 'Técnicos', 'name' => 'technician_count', 'model' => 'technicianCount'], ['label' => 'Lectores', 'name' => 'reader_count', 'model' => 'readerCount']] as $seat)
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ $seat['label'] }}</label>
                                    <input type="number" name="{{ $seat['name'] }}" x-model="{{ $seat['model'] }}" min="0"
                                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Límite de Activos</label>
                            <select name="asset_count" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                                @foreach ([200, 500, 1000, 5000, 10000, 999999] as $limit)
                                    <option value="{{ $limit }}" {{ ($subscription?->asset_count ?? 200) == $limit ? 'selected' : '' }}>
                                        {{ $limit >= 999999 ? 'Ilimitado' : number_format($limit) }} activos
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                {{-- Right column: Summary --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sticky top-20">
                        <h3 class="text-sm font-bold text-[#002046] uppercase tracking-wider mb-4">Resumen</h3>

                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Plan base</span>
                                <span class="font-semibold" x-text="'$' + basePrice.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Módulos</span>
                                <span class="font-semibold" x-text="'$' + modulesTotal.toFixed(2)"></span>
                            </div>
                            <div class="border-t border-gray-100 pt-2 mt-2 flex justify-between">
                                <span class="font-bold text-[#002046]">Total mensual</span>
                                <span class="font-extrabold text-xl text-[#002046] font-headline" x-text="'$' + totalMonthly.toFixed(2)"></span>
                            </div>
                        </div>

                        <button type="submit"
                                class="mt-5 w-full bg-[#002046] text-white py-3 rounded-xl text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                            {{ $subscription ? 'Actualizar Plan' : 'Activar Suscripción' }}
                        </button>

                        <p class="text-[10px] text-gray-400 text-center mt-3">
                            {{ config('services.stripe.secret') ? 'Pago seguro via Stripe' : 'Modo demo — sin cobro real' }}
                        </p>
                    </div>
                </div>

            </div>
        </form>

    </div>

</x-layouts.cmms>
