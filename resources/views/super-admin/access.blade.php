<x-layouts.super-admin title="Dominios & Acceso" breadcrumb="Dominios & Acceso">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Dominios & Acceso</h1>
            <p class="text-sm mt-1" style="color:#64748b;">Gestión de métodos de acceso multi-tenant y white label</p>
        </div>

        {{-- 4 Access type cards --}}
        <div class="grid grid-cols-4 gap-4">
            @php
                $accessTypes = [
                    ['level' => 0, 'icon' => 'globe', 'title' => 'Subdominio', 'subtitle' => 'empresa.tuplataforma.com', 'color' => '#6366f1', 'bg' => '#ede9fe', 'desc' => 'Modo estándar SaaS. El sistema detecta al cliente por subdominio.'],
                    ['level' => 1, 'icon' => 'link', 'title' => 'Dominio Propio', 'subtitle' => 'app.empresa.com', 'color' => '#0ea5e9', 'bg' => '#e0f2fe', 'desc' => 'El cliente apunta su DNS a la plataforma. SSL obligatorio.'],
                    ['level' => 2, 'icon' => 'palette', 'title' => 'White Label', 'subtitle' => 'Marca completa', 'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'desc' => 'Dominio + branding propio: logo, colores, nombre de app.'],
                    ['level' => 3, 'icon' => 'building-2', 'title' => 'Revendedor', 'subtitle' => 'admin.suempresa.com', 'color' => '#f59e0b', 'bg' => '#fef9c3', 'desc' => 'Panel maestro con múltiples clientes. Cada uno con su propia marca.'],
                ];
            @endphp
            @foreach($accessTypes as $type)
                <div class="bg-white rounded-xl border p-5 space-y-3" style="border-color:{{ $type['color'] }}30;">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $type['bg'] }};">
                        <i data-lucide="{{ $type['icon'] }}" class="w-5 h-5" style="color:{{ $type['color'] }};"></i>
                    </div>
                    <div>
                        <p class="font-black text-sm" style="color:#0f172a;">{{ $type['title'] }}</p>
                        <p class="text-xs font-mono mt-0.5" style="color:{{ $type['color'] }};">{{ $type['subtitle'] }}</p>
                    </div>
                    <p class="text-xs leading-relaxed" style="color:#64748b;">{{ $type['desc'] }}</p>
                    <div class="pt-2 border-t" style="border-color:#f1f5f9;">
                        <p class="text-xs font-bold" style="color:#94a3b8;">
                            {{ $byLevel[$type['level']] ?? 0 }} clientes en este nivel
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Con Subdominio</p>
                <p class="text-3xl font-black" style="color:#6366f1;">{{ $withSubdomain }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Dominios Personalizados</p>
                <p class="text-3xl font-black" style="color:#0ea5e9;">{{ $withCustomDomain }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Dominios Verificados</p>
                <p class="text-3xl font-black" style="color:#22c55e;">{{ $verifiedDomains }}</p>
            </div>
        </div>

        {{-- Access method info --}}
        <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
            <p class="text-sm font-bold mb-3" style="color:#0f172a;">Cómo funciona la resolución de tenant</p>
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-lg p-4" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black text-white" style="background:#6366f1;">1</span>
                        <p class="text-sm font-bold" style="color:#0f172a;">Subdominio</p>
                    </div>
                    <p class="text-xs font-mono" style="color:#64748b;">empresa.tuplataforma.com</p>
                    <p class="text-xs mt-2" style="color:#94a3b8;">El backend detecta "empresa" del Host header y carga el tenant correspondiente.</p>
                </div>
                <div class="rounded-lg p-4" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black text-white" style="background:#0ea5e9;">2</span>
                        <p class="text-sm font-bold" style="color:#0f172a;">JWT / Token</p>
                    </div>
                    <p class="text-xs font-mono" style="color:#64748b;">{ "tenant_id": "abc-123" }</p>
                    <p class="text-xs mt-2" style="color:#94a3b8;">Cada token de sesión incluye el tenant_id. Aislamiento total entre clientes.</p>
                </div>
                <div class="rounded-lg p-4" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black text-white" style="background:#8b5cf6;">3</span>
                        <p class="text-sm font-bold" style="color:#0f172a;">Header</p>
                    </div>
                    <p class="text-xs font-mono" style="color:#64748b;">X-Tenant-ID: abc-123</p>
                    <p class="text-xs mt-2" style="color:#94a3b8;">Para integraciones API donde no hay subdominio disponible.</p>
                </div>
            </div>
        </div>

        {{-- Tenant access table --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Configuración de Acceso por Cliente</p>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:#ede9fe; color:#6d28d9;">
                    {{ $tenants->total() }} clientes
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                            @foreach(['Cliente', 'Subdominio', 'Dominio Personalizado', 'Nivel Acceso', 'Marca', 'Verificado', 'Acción'] as $h)
                                <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $levelLabels = [0 => 'Estándar', 1 => 'Dominio Propio', 2 => 'White Label', 3 => 'Full WL', 4 => 'Revendedor'];
                            $levelColors = [0 => '#6366f1', 1 => '#0ea5e9', 2 => '#8b5cf6', 3 => '#f59e0b', 4 => '#ef4444'];
                        @endphp
                        @forelse($tenants as $tenant)
                            <tr class="border-b transition-colors" style="border-color:#f1f5f9;">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-xs" style="color:#0f172a;">{{ $tenant->name }}</p>
                                    <p class="text-[10px]" style="color:#94a3b8;">{{ $tenant->slug }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($tenant->subdomain)
                                        <code class="text-[11px] px-1.5 py-0.5 rounded" style="background:#f1f5f9; color:#6366f1;">
                                            {{ $tenant->subdomain }}
                                        </code>
                                    @else
                                        <span class="text-xs" style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($tenant->custom_domain)
                                        <code class="text-[11px] px-1.5 py-0.5 rounded" style="background:#e0f2fe; color:#0284c7;">
                                            {{ $tenant->custom_domain }}
                                        </code>
                                    @else
                                        <span class="text-xs" style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php $level = $tenant->white_label_level ?? 0; @endphp
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          style="background:{{ $levelColors[$level] ?? '#6366f1' }}18; color:{{ $levelColors[$level] ?? '#6366f1' }};">
                                        {{ $levelLabels[$level] ?? 'Estándar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs" style="color:#475569;">
                                    {{ $tenant->brand_name ?: '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($tenant->custom_domain_verified)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold" style="color:#16a34a;">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i> Sí
                                        </span>
                                    @elseif($tenant->custom_domain)
                                        <span class="text-[10px]" style="color:#f59e0b;">Pendiente</span>
                                    @else
                                        <span class="text-[10px]" style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button onclick="openEditModal({{ $tenant->id }}, '{{ addslashes($tenant->name) }}', '{{ $tenant->subdomain }}', '{{ $tenant->custom_domain }}', {{ $tenant->white_label_level ?? 0 }}, '{{ addslashes($tenant->brand_name ?? '') }}', '{{ $tenant->primary_color ?? '#3B82F6' }}', {{ $tenant->custom_domain_verified ? 'true' : 'false' }}, '{{ $tenant->reseller_id }}')"
                                            class="text-xs font-bold px-3 py-1 rounded-lg transition-colors"
                                            style="background:#ede9fe; color:#6d28d9;">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-sm" style="color:#94a3b8;">Sin clientes</td>
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

    {{-- Edit modal --}}
    <div id="accessModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background:rgba(15,23,42,0.6);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-5 border-b flex items-center justify-between" style="border-color:#f1f5f9;">
                <div>
                    <p class="font-bold" style="color:#0f172a;">Configurar Acceso</p>
                    <p id="modalTenantName" class="text-xs mt-0.5" style="color:#64748b;"></p>
                </div>
                <button onclick="closeModal()" class="p-1.5 rounded-lg hover:bg-slate-100">
                    <i data-lucide="x" class="w-4 h-4" style="color:#64748b;"></i>
                </button>
            </div>
            <form id="accessForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="p-6 space-y-4">

                    {{-- Nivel de acceso --}}
                    <div>
                        <label class="block text-xs font-bold mb-2" style="color:#475569;">Nivel de Acceso</label>
                        <div class="grid grid-cols-2 gap-2" id="levelButtons">
                            @foreach([0 => 'Estándar (Subdominio)', 1 => 'Dominio Propio', 2 => 'White Label', 3 => 'Full White Label', 4 => 'Revendedor'] as $lvl => $lbl)
                                <button type="button"
                                        onclick="selectLevel({{ $lvl }})"
                                        id="level_btn_{{ $lvl }}"
                                        class="text-xs font-semibold px-3 py-2 rounded-lg border text-left transition-colors level-btn"
                                        data-level="{{ $lvl }}"
                                        style="border-color:#e2e8f0; color:#475569;">
                                    {{ $lbl }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="white_label_level" id="white_label_level" value="0">
                    </div>

                    {{-- Subdominio --}}
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:#475569;">Subdominio</label>
                        <div class="flex items-center gap-0">
                            <input type="text" name="subdomain" id="modal_subdomain"
                                   class="flex-1 px-3 py-2 text-sm border rounded-l-lg outline-none focus:ring-2"
                                   style="border-color:#e2e8f0; focus:ring-color:#6366f1;"
                                   placeholder="empresa">
                            <span class="px-3 py-2 text-xs font-semibold border border-l-0 rounded-r-lg" style="background:#f8fafc; border-color:#e2e8f0; color:#94a3b8;">.tuplataforma.com</span>
                        </div>
                    </div>

                    {{-- Custom domain --}}
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:#475569;">Dominio Personalizado</label>
                        <input type="text" name="custom_domain" id="modal_custom_domain"
                               class="w-full px-3 py-2 text-sm border rounded-lg outline-none"
                               style="border-color:#e2e8f0;"
                               placeholder="app.empresa.com">
                        <div class="flex items-center gap-2 mt-2">
                            <input type="hidden" name="custom_domain_verified" value="0">
                            <input type="checkbox" name="custom_domain_verified" id="modal_verified" value="1" class="rounded">
                            <label for="modal_verified" class="text-xs" style="color:#475569;">Dominio verificado (DNS configurado)</label>
                        </div>
                    </div>

                    {{-- Brand name + color --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color:#475569;">Nombre de Marca</label>
                            <input type="text" name="brand_name" id="modal_brand_name"
                                   class="w-full px-3 py-2 text-sm border rounded-lg outline-none"
                                   style="border-color:#e2e8f0;"
                                   placeholder="Mi Empresa CMMS">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color:#475569;">Color Principal</label>
                            <input type="color" name="primary_color" id="modal_primary_color"
                                   class="w-full h-10 border rounded-lg cursor-pointer"
                                   style="border-color:#e2e8f0;">
                        </div>
                    </div>

                    {{-- Reseller ID --}}
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:#475569;">ID Revendedor (opcional)</label>
                        <input type="text" name="reseller_id" id="modal_reseller_id"
                               class="w-full px-3 py-2 text-sm border rounded-lg outline-none"
                               style="border-color:#e2e8f0;"
                               placeholder="reseller-abc-123">
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3" style="border-color:#f1f5f9; background:#f8fafc;">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-semibold rounded-lg border transition-colors"
                            style="border-color:#e2e8f0; color:#475569;">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-bold rounded-lg text-white transition-colors"
                            style="background:#6366f1;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, subdomain, customDomain, level, brandName, primaryColor, verified, resellerId) {
            document.getElementById('modalTenantName').textContent = name;
            document.getElementById('accessForm').action = '/super-admin/access/' + id;
            document.getElementById('modal_subdomain').value = subdomain || '';
            document.getElementById('modal_custom_domain').value = customDomain || '';
            document.getElementById('modal_brand_name').value = brandName || '';
            document.getElementById('modal_primary_color').value = primaryColor || '#3B82F6';
            document.getElementById('modal_reseller_id').value = resellerId || '';
            document.getElementById('modal_verified').checked = verified;
            selectLevel(level);
            document.getElementById('accessModal').classList.remove('hidden');
            document.getElementById('accessModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('accessModal').classList.add('hidden');
            document.getElementById('accessModal').classList.remove('flex');
        }

        function selectLevel(lvl) {
            document.getElementById('white_label_level').value = lvl;
            const colors = {0:'#6366f1', 1:'#0ea5e9', 2:'#8b5cf6', 3:'#f59e0b', 4:'#ef4444'};
            document.querySelectorAll('.level-btn').forEach(btn => {
                const bl = parseInt(btn.dataset.level);
                if (bl === lvl) {
                    btn.style.borderColor = colors[lvl];
                    btn.style.background = colors[lvl] + '18';
                    btn.style.color = colors[lvl];
                } else {
                    btn.style.borderColor = '#e2e8f0';
                    btn.style.background = 'transparent';
                    btn.style.color = '#475569';
                }
            });
        }
    </script>

</x-layouts.super-admin>
