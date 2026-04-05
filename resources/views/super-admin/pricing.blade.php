<x-layouts.super-admin title="Planes de Precios" breadcrumb="Planes de Precios">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Planes de Precios</h1>
            <p class="text-sm mt-1" style="color:#64748b;">Estructura de planes, límites y clientes por plan</p>
        </div>

        {{-- Plan cards --}}
        @php
            $planConfig = [
                'starter'      => ['color' => '#6366f1', 'bg' => '#ede9fe', 'price' => 99,  'icon' => 'zap'],
                'professional' => ['color' => '#0ea5e9', 'bg' => '#e0f2fe', 'price' => 249, 'icon' => 'trending-up'],
                'enterprise'   => ['color' => '#f59e0b', 'bg' => '#fef9c3', 'price' => 599, 'icon' => 'crown'],
            ];
        @endphp

        <div class="grid grid-cols-3 gap-6">
            @foreach($plans as $plan)
                @php
                    $cfg = $planConfig[$plan->value] ?? ['color' => '#94a3b8', 'bg' => '#f1f5f9', 'price' => 0, 'icon' => 'package'];
                    $count = $tenantsByPlan[$plan->value] ?? 0;
                @endphp
                <div class="bg-white rounded-xl border-2 p-6 space-y-5" style="border-color:{{ $cfg['color'] }}20;">
                    {{-- Plan header --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $cfg['bg'] }};">
                                <i data-lucide="{{ $cfg['icon'] }}" class="w-5 h-5" style="color:{{ $cfg['color'] }};"></i>
                            </div>
                            <div>
                                <p class="font-black text-lg" style="color:#0f172a;">{{ $plan->label() }}</p>
                                <p class="text-xs" style="color:#94a3b8;">{{ $count }} {{ $count === 1 ? 'cliente' : 'clientes' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black" style="color:{{ $cfg['color'] }};">${{ $cfg['price'] }}</p>
                            <p class="text-xs" style="color:#94a3b8;">/mes base</p>
                        </div>
                    </div>

                    {{-- Límites --}}
                    <div class="space-y-2 py-4 border-t border-b" style="border-color:#f1f5f9;">
                        <div class="flex items-center justify-between text-sm">
                            <span style="color:#64748b;">Usuarios máximos</span>
                            <span class="font-bold" style="color:#0f172a;">
                                {{ $plan->maxUsers() === PHP_INT_MAX ? 'Ilimitado' : number_format($plan->maxUsers()) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span style="color:#64748b;">Activos máximos</span>
                            <span class="font-bold" style="color:#0f172a;">
                                {{ $plan->maxAssets() === PHP_INT_MAX ? 'Ilimitado' : number_format($plan->maxAssets()) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span style="color:#64748b;">MRR total de este plan</span>
                            <span class="font-bold" style="color:#16a34a;">
                                ${{ number_format($cfg['price'] * $count, 0) }}/mes
                            </span>
                        </div>
                    </div>

                    {{-- Features --}}
                    <div class="space-y-1.5">
                        @if($plan->value === 'starter')
                            @foreach(['Core CMMS incluido', 'Soporte por email', 'Subdomain access', '1 ubicación'] as $f)
                                <div class="flex items-center gap-2 text-xs" style="color:#475569;">
                                    <i data-lucide="check" class="w-3 h-3 shrink-0" style="color:#22c55e;"></i>
                                    {{ $f }}
                                </div>
                            @endforeach
                        @elseif($plan->value === 'professional')
                            @foreach(['Todo Starter +', 'Módulos adicionales', 'Soporte prioritario', 'Custom domain', 'Multi-sitio'] as $f)
                                <div class="flex items-center gap-2 text-xs" style="color:#475569;">
                                    <i data-lucide="check" class="w-3 h-3 shrink-0" style="color:#22c55e;"></i>
                                    {{ $f }}
                                </div>
                            @endforeach
                        @else
                            @foreach(['Todo Professional +', 'White label completo', 'SLA garantizado', 'Onboarding dedicado', 'API personalizada', 'Reseller disponible'] as $f)
                                <div class="flex items-center gap-2 text-xs" style="color:#475569;">
                                    <i data-lucide="check" class="w-3 h-3 shrink-0" style="color:#22c55e;"></i>
                                    {{ $f }}
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- CTA --}}
                    <a href="{{ route('super-admin.tenants.index') }}?plan={{ $plan->value }}"
                       class="flex items-center justify-center gap-2 w-full py-2 rounded-lg text-sm font-bold transition-colors"
                       style="background:{{ $cfg['bg'] }}; color:{{ $cfg['color'] }};">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        Ver {{ $count }} cliente{{ $count !== 1 ? 's' : '' }}
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Módulos adicionales --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-5 py-4 border-b" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Add-ons Disponibles</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">Módulos que se suman al precio base del plan</p>
            </div>
            <div class="grid grid-cols-3 gap-px" style="background:#f1f5f9;">
                @foreach($modules as $module)
                    @if($module->price() > 0)
                        <div class="bg-white px-5 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold" style="color:#0f172a;">{{ $module->label() }}</p>
                                <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $module->description() }}</p>
                            </div>
                            <div class="text-right ml-4 shrink-0">
                                <p class="font-black" style="color:#16a34a;">+${{ $module->price() }}</p>
                                <p class="text-[10px]" style="color:#94a3b8;">/mes</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>

</x-layouts.super-admin>
