<x-layouts.landing :settings="$settings" title="Precios" :darkNav="false">

<section style="padding:120px 24px 80px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
    <div style="max-width:700px; margin:0 auto; text-align:center;">
        <div class="section-label"><i data-lucide="tag" style="width:12px; height:12px;"></i> Precios</div>
        <h1 class="font-display" style="font-size:clamp(2rem,4vw,3.2rem); font-weight:800; letter-spacing:-.035em; color:#0f172a; margin:0 0 20px; line-height:1.1;">
            Planes simples y transparentes
        </h1>
        <p style="font-size:1.1rem; color:#64748b; line-height:1.75; margin:0;">
            Sin cargos ocultos. Cambia de plan cuando quieras. Cancela en cualquier momento.
        </p>
    </div>
</section>

<section style="padding:80px 24px; background:#fff;">
    <div style="max-width:1100px; margin:0 auto;">

        @php
        $plans = [
            [
                'name'     => 'Starter',
                'price'    => '$49',
                'period'   => '/mes',
                'caption'  => 'Hasta 5 usuarios · 100 activos',
                'color'    => '#6366f1',
                'featured' => false,
                'cta'      => 'Comenzar gratis',
                'ctaHref'  => route('register'),
                'features' => [
                    ['check','OTs ilimitadas'],
                    ['check','Mantenimiento PM básico'],
                    ['check','Gestión de activos'],
                    ['check','App móvil'],
                    ['check','Reportes básicos'],
                    ['check','Soporte por email'],
                    ['x','IoT y sensores'],
                    ['x','IA Predictiva'],
                    ['x','Permisos de trabajo'],
                ],
            ],
            [
                'name'     => 'Professional',
                'price'    => '$149',
                'period'   => '/mes',
                'caption'  => 'Hasta 20 usuarios · 500 activos',
                'color'    => '#6366f1',
                'featured' => true,
                'cta'      => 'Comenzar gratis',
                'ctaHref'  => route('register'),
                'features' => [
                    ['check','Todo lo de Starter'],
                    ['check','Inventario avanzado'],
                    ['check','Órdenes de compra'],
                    ['check','Permisos de trabajo'],
                    ['check','IoT y sensores'],
                    ['check','IA Predictiva básica'],
                    ['check','Reportes y KPIs avanzados'],
                    ['check','Soporte prioritario'],
                    ['check','Acceso API'],
                ],
            ],
            [
                'name'     => 'Enterprise',
                'price'    => 'Custom',
                'period'   => '',
                'caption'  => 'Usuarios y activos ilimitados',
                'color'    => '#6366f1',
                'featured' => false,
                'cta'      => 'Hablar con ventas',
                'ctaHref'  => route('landing.contacto'),
                'features' => [
                    ['check','Todo lo de Professional'],
                    ['check','IA Predictiva completa'],
                    ['check','White label disponible'],
                    ['check','SLA 99.9% garantizado'],
                    ['check','Onboarding dedicado'],
                    ['check','Integración ERP/SAP'],
                    ['check','Data center en LATAM'],
                    ['check','Soporte 24/7'],
                    ['check','Manager de cuenta'],
                ],
            ],
        ];
        @endphp

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; margin-bottom:64px;">
            @foreach($plans as $plan)
            <div class="reveal" style="background:{{ $plan['featured'] ? '#0f172a' : '#fff' }}; border-radius:20px; padding:40px 32px; border:2px solid {{ $plan['featured'] ? 'var(--accent)' : '#f1f5f9' }}; position:relative; transition:transform .2s,box-shadow .2s;"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='{{ $plan['featured'] ? '0 24px 60px rgba(99,102,241,.25)' : '0 24px 60px rgba(0,0,0,.08)' }}'"
                 onmouseout="this.style.transform=''; this.style.boxShadow=''">
                @if($plan['featured'])
                <div style="position:absolute; top:-13px; left:50%; transform:translateX(-50%); padding:4px 16px; border-radius:99px; background:var(--accent); font-size:11px; font-weight:800; color:#fff; white-space:nowrap; text-transform:uppercase; letter-spacing:.06em;">
                    Más popular
                </div>
                @endif

                <p class="font-display" style="font-size:13px; font-weight:700; color:{{ $plan['featured'] ? '#818cf8' : 'var(--accent)' }}; text-transform:uppercase; letter-spacing:.08em; margin:0 0 8px;">{{ $plan['name'] }}</p>
                <div style="display:flex; align-items:baseline; gap:4px; margin-bottom:6px;">
                    <span class="font-display" style="font-size:2.5rem; font-weight:800; color:{{ $plan['featured'] ? '#fff' : '#0f172a' }}; letter-spacing:-.03em;">{{ $plan['price'] }}</span>
                    @if($plan['period'])
                    <span style="font-size:14px; color:{{ $plan['featured'] ? 'rgba(255,255,255,.4)' : '#94a3b8' }};">{{ $plan['period'] }}</span>
                    @endif
                </div>
                <p style="font-size:13px; color:{{ $plan['featured'] ? 'rgba(255,255,255,.4)' : '#94a3b8' }}; margin:0 0 28px;">{{ $plan['caption'] }}</p>

                <div style="height:1px; background:{{ $plan['featured'] ? 'rgba(255,255,255,.1)' : '#f1f5f9' }}; margin-bottom:28px;"></div>

                <div style="display:flex; flex-direction:column; gap:11px; margin-bottom:32px;">
                    @foreach($plan['features'] as [$type,$feat])
                    <div style="display:flex; align-items:center; gap:10px;">
                        @if($type === 'check')
                        <i data-lucide="check" style="width:16px; height:16px; color:{{ $plan['featured'] ? '#818cf8' : '#22c55e' }}; flex-shrink:0;"></i>
                        <span style="font-size:14px; color:{{ $plan['featured'] ? 'rgba(255,255,255,.8)' : '#374151' }};">{{ $feat }}</span>
                        @else
                        <i data-lucide="minus" style="width:16px; height:16px; color:{{ $plan['featured'] ? 'rgba(255,255,255,.2)' : '#cbd5e1' }}; flex-shrink:0;"></i>
                        <span style="font-size:14px; color:{{ $plan['featured'] ? 'rgba(255,255,255,.3)' : '#cbd5e1' }};">{{ $feat }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>

                <a href="{{ $plan['ctaHref'] }}"
                   style="display:flex; align-items:center; justify-content:center; width:100%; padding:13px; border-radius:10px; font-weight:700; font-size:14px; text-decoration:none; transition:background .2s;
                          {{ $plan['featured'] ? 'background:var(--accent); color:#fff; box-shadow:0 4px 16px rgba(99,102,241,.4);' : 'border:1.5px solid #e2e8f0; color:#374151; background:#fff;' }}">
                    {{ $plan['cta'] }}
                </a>
            </div>
            @endforeach
        </div>

        {{-- Feature comparison table --}}
        <div class="reveal">
            <h2 class="font-display" style="font-size:1.4rem; font-weight:800; color:#0f172a; margin:0 0 24px; text-align:center; letter-spacing:-.02em;">Comparativa detallada</h2>
            <div style="border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:16px 20px; text-align:left; font-size:13px; font-weight:700; color:#374151; border-bottom:1px solid #e2e8f0;">Función</th>
                            @foreach(['Starter','Professional','Enterprise'] as $h)
                            <th style="padding:16px 20px; text-align:center; font-size:13px; font-weight:700; color:#374151; border-bottom:1px solid #e2e8f0;">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $rows = [
                            ['OTs ilimitadas',          true,  true,  true],
                            ['Mantenimiento PM',         true,  true,  true],
                            ['Gestión de activos',       true,  true,  true],
                            ['App móvil',                true,  true,  true],
                            ['Inventario',               false, true,  true],
                            ['Órdenes de compra',        false, true,  true],
                            ['Permisos de trabajo',      false, true,  true],
                            ['IoT / Sensores',           false, true,  true],
                            ['IA Predictiva',            false, 'Básica', true],
                            ['White label',              false, false, true],
                            ['SLA 99.9%',                false, false, true],
                            ['Integración ERP/SAP',      false, false, true],
                            ['Soporte',                  'Email','Prioritario','24/7'],
                        ];
                        @endphp
                        @foreach($rows as $row)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:13px 20px; font-size:14px; color:#374151;">{{ $row[0] }}</td>
                            @foreach([1,2,3] as $col)
                            <td style="padding:13px 20px; text-align:center;">
                                @if($row[$col] === true)
                                    <i data-lucide="check" style="width:16px; height:16px; color:#22c55e; display:inline-block;"></i>
                                @elseif($row[$col] === false)
                                    <i data-lucide="minus" style="width:16px; height:16px; color:#e2e8f0; display:inline-block;"></i>
                                @else
                                    <span style="font-size:12px; font-weight:600; color:var(--accent);">{{ $row[$col] }}</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Pricing --}}
<section style="padding:80px 24px; background:#f8fafc;">
    <div style="max-width:720px; margin:0 auto;">
        <h2 class="font-display" style="font-size:1.6rem; font-weight:800; color:#0f172a; margin:0 0 40px; text-align:center; letter-spacing:-.02em;">Preguntas frecuentes</h2>
        @foreach([
            ['¿El período de prueba es completamente gratis?','Sí. 14 días sin tarjeta de crédito. Acceso completo al plan Professional durante la prueba.'],
            ['¿Puedo cambiar de plan después?','Sí, puedes subir o bajar de plan en cualquier momento. Los cambios se aplican en el siguiente ciclo de facturación.'],
            ['¿Cómo funciona la facturación?','Facturamos mensualmente. Para planes anuales ofrecemos 2 meses gratis.'],
            ['¿Tienen descuentos por volumen?','Sí. Para más de 50 usuarios contáctanos para un plan personalizado con precios especiales.'],
            ['¿Puedo cancelar en cualquier momento?','Sí. Sin penalizaciones. Al cancelar, conservas acceso hasta el final del período pagado.'],
        ] as [$q,$a])
        <div x-data="{ open: false }" style="border-bottom:1px solid #e2e8f0; padding:20px 0;">
            <button onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('i').classList.toggle('rotate-45')"
                    style="display:flex; align-items:center; justify-content:space-between; width:100%; background:none; border:none; cursor:pointer; padding:0; text-align:left;">
                <span class="font-display" style="font-size:15px; font-weight:700; color:#0f172a;">{{ $q }}</span>
                <i data-lucide="plus" style="width:18px; height:18px; color:#94a3b8; flex-shrink:0; transition:transform .2s;"></i>
            </button>
            <div class="hidden" style="padding-top:12px;">
                <p style="font-size:14px; color:#64748b; line-height:1.7; margin:0;">{{ $a }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

</x-layouts.landing>
