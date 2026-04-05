<x-layouts.landing :settings="$settings" title="Producto" :darkNav="false">

{{-- ══ PAGE HEADER ════════════════════════════════════════════════════ --}}
<section style="padding:120px 24px 80px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
    <div style="max-width:1200px; margin:0 auto; text-align:center; max-width:700px; margin:0 auto;">
        <div class="section-label"><i data-lucide="sparkles" style="width:12px; height:12px;"></i> Producto</div>
        <h1 class="font-display" style="font-size:clamp(2rem,4vw,3.2rem); font-weight:800; letter-spacing:-.035em; color:#0f172a; margin:0 0 20px; line-height:1.1;">
            Todo lo que necesitas para gestionar el mantenimiento
        </h1>
        <p style="font-size:1.1rem; color:#64748b; line-height:1.75; margin:0 0 36px;">
            Una plataforma unificada con los módulos que tu operación necesita. Desde correctivos simples hasta IA predictiva.
        </p>
        <a href="{{ route('register') }}" class="btn-accent">
            Prueba gratis 14 días <i data-lucide="arrow-right" style="width:16px; height:16px;"></i>
        </a>
    </div>
</section>

{{-- ══ FEATURE DEEP-DIVES ══════════════════════════════════════════════ --}}

@php
$features = [
    [
        'icon'    => 'clipboard-list',
        'color'   => '#6366f1',
        'bg'      => '#eef2ff',
        'title'   => 'Órdenes de Trabajo',
        'sub'     => 'Correctivos y preventivos bajo control',
        'desc'    => 'Crea OTs desde cualquier dispositivo, asígnalas al técnico correcto y monitorea su progreso en tiempo real. Flujos de aprobación configurables, listas de verificación, adjuntos y firma digital en campo.',
        'points'  => ['Asignación automática por habilidades','Checklists y evidencia fotográfica','Firma digital en campo','Historial completo por activo','Notificaciones por vencimiento'],
        'imgColor'=> '#6366f1',
    ],
    [
        'icon'    => 'calendar-check',
        'color'   => '#22c55e',
        'bg'      => '#f0fdf4',
        'title'   => 'Mantenimiento Preventivo',
        'sub'     => 'Planificación proactiva del mantenimiento',
        'desc'    => 'Define planes PM por tiempo, horas de uso o condición del equipo. El sistema genera OTs automáticamente y te avisa antes del vencimiento. Nunca más un mantenimiento olvidado.',
        'points'  => ['Disparadores por tiempo, ciclos y condición','Generación automática de OTs','Calendario visual de actividades','Templates reutilizables de PM','KPIs de cumplimiento y desvíos'],
        'imgColor'=> '#22c55e',
    ],
    [
        'icon'    => 'cpu',
        'color'   => '#f59e0b',
        'bg'      => '#fffbeb',
        'title'   => 'Gestión de Activos',
        'sub'     => 'Trazabilidad total de tus equipos',
        'desc'    => 'Organiza tus activos en un árbol jerárquico por planta, área y equipo. Guarda fichas técnicas, manuales, garantías y el historial completo de intervenciones. Etiquetas QR para acceso rápido desde móvil.',
        'points'  => ['Árbol jerárquico de activos','Ficha técnica y documentos adjuntos','Etiquetas QR y código de barras','Historial de intervenciones','Indicadores OEE y disponibilidad'],
        'imgColor'=> '#f59e0b',
    ],
    [
        'icon'    => 'package',
        'color'   => '#3b82f6',
        'bg'      => '#eff6ff',
        'title'   => 'Inventario y Refacciones',
        'sub'     => 'Stock siempre disponible cuando lo necesitas',
        'desc'    => 'Controla el stock de refacciones, herramientas y consumibles. Alertas automáticas de nivel mínimo, trazabilidad de consumo por OT y órdenes de compra integradas para no quedarte sin piezas críticas.',
        'points'  => ['Alertas de stock mínimo','Trazabilidad por OT y activo','Órdenes de compra integradas','Múltiples almacenes','Historial de movimientos'],
        'imgColor'=> '#3b82f6',
    ],
    [
        'icon'    => 'activity',
        'color'   => '#8b5cf6',
        'bg'      => '#f5f3ff',
        'title'   => 'IoT y Sensores',
        'sub'     => 'Monitoreo en tiempo real de tus equipos',
        'desc'    => 'Conecta sensores industriales via OPC-UA, MQTT o API REST. Visualiza variables en tiempo real, configura umbrales de alerta y dispara órdenes de trabajo automáticamente cuando un equipo supera su límite operativo.',
        'points'  => ['Integración OPC-UA / MQTT / Modbus','Dashboards de variables en tiempo real','Alertas por umbral configurable','OTs automáticas por condición','Historial de lecturas y tendencias'],
        'imgColor'=> '#8b5cf6',
    ],
    [
        'icon'    => 'brain',
        'color'   => '#ec4899',
        'bg'      => '#fdf2f8',
        'title'   => 'IA Predictiva',
        'sub'     => 'Predice fallos antes de que ocurran',
        'desc'    => 'Nuestro motor de IA analiza patrones en los datos de sensores e historial de fallas para detectar deterioro temprano. Calcula la vida útil restante (RUL) y recomienda cuándo intervenir, antes de que el equipo falle.',
        'points'  => ['Detección de anomalías en tiempo real','Cálculo de RUL por activo','Recomendaciones automáticas de PM','Modelos ML entrenados por industria','Reducción de correctivos hasta 40%'],
        'imgColor'=> '#ec4899',
    ],
];
@endphp

@foreach($features as $idx => $feat)
<section style="padding:80px 24px; background:{{ $idx % 2 === 0 ? '#fff' : '#f8fafc' }};">
    <div style="max-width:1200px; margin:0 auto;">
        <div class="two-col reveal" style="{{ $idx % 2 !== 0 ? 'direction:rtl;' : '' }}">
            <div style="{{ $idx % 2 !== 0 ? 'direction:ltr;' : '' }}">
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 14px; border-radius:99px; background:{{ $feat['bg'] }}; margin-bottom:16px;">
                    <i data-lucide="{{ $feat['icon'] }}" style="width:14px; height:14px; color:{{ $feat['color'] }};"></i>
                    <span style="font-size:12px; font-weight:700; color:{{ $feat['color'] }}; text-transform:uppercase; letter-spacing:.08em;">{{ $feat['sub'] }}</span>
                </div>
                <h2 class="font-display" style="font-size:clamp(1.6rem,3vw,2.2rem); font-weight:800; letter-spacing:-.03em; color:#0f172a; margin:0 0 16px; line-height:1.15;">{{ $feat['title'] }}</h2>
                <p style="font-size:1rem; color:#64748b; line-height:1.75; margin:0 0 28px;">{{ $feat['desc'] }}</p>
                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:32px;">
                    @foreach($feat['points'] as $pt)
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:20px; height:20px; border-radius:6px; background:{{ $feat['color'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i data-lucide="check" style="width:11px; height:11px; color:#fff;"></i>
                        </div>
                        <span style="font-size:14px; color:#374151; font-weight:500;">{{ $pt }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}" class="btn-accent">
                    Explorar módulo <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
                </a>
            </div>

            {{-- Visual mockup --}}
            <div style="{{ $idx % 2 !== 0 ? 'direction:ltr;' : '' }}">
                <div class="mockup-wrap">
                    <div class="mockup-bar">
                        <div class="mockup-dot" style="background:#ff5f57;"></div>
                        <div class="mockup-dot" style="background:#febc2e;"></div>
                        <div class="mockup-dot" style="background:#28c840;"></div>
                        <span style="margin-left:10px; font-size:11px; color:rgba(255,255,255,.3);">{{ $feat['title'] }}</span>
                    </div>
                    <div style="padding:28px; background:#0f172a; min-height:220px; display:flex; flex-direction:column; justify-content:center;">
                        {{-- Generic card grid --}}
                        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:10px;">
                            @foreach(array_slice($feat['points'],0,4) as $pt)
                            <div style="background:#1e293b; border-radius:10px; padding:16px; border:1px solid rgba(255,255,255,.06);">
                                <div style="width:28px; height:28px; border-radius:7px; background:{{ $feat['color'] }}18; display:flex; align-items:center; justify-content:center; margin-bottom:10px;">
                                    <i data-lucide="{{ $feat['icon'] }}" style="width:13px; height:13px; color:{{ $feat['color'] }};"></i>
                                </div>
                                <p style="font-size:12px; color:rgba(255,255,255,.6); margin:0; line-height:1.4;">{{ $pt }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endforeach

{{-- ══ CTA ════════════════════════════════════════════════════════════ --}}
<section style="padding:80px 24px; background:#0f172a;">
    <div style="max-width:700px; margin:0 auto; text-align:center; position:relative; z-index:1;">
        <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.5rem); font-weight:800; letter-spacing:-.03em; color:#fff; margin:0 0 16px; line-height:1.15;">
            ¿Listo para transformar tu mantenimiento?
        </h2>
        <p style="font-size:1rem; color:rgba(255,255,255,.5); margin:0 0 32px; line-height:1.7;">
            14 días gratis, sin tarjeta de crédito. Tu equipo puede estar operando hoy mismo.
        </p>
        <div style="display:flex; align-items:center; justify-content:center; gap:14px; flex-wrap:wrap;">
            <a href="{{ route('register') }}" class="btn-accent" style="font-size:15px; padding:14px 30px;">
                Comenzar gratis <i data-lucide="arrow-right" style="width:17px; height:17px;"></i>
            </a>
            <a href="{{ route('landing.precios') }}" class="btn-ghost-white" style="font-size:15px; padding:14px 30px;">Ver precios</a>
        </div>
    </div>
</section>

</x-layouts.landing>
