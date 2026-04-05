<x-layouts.landing :settings="$settings" title="Módulos" :darkNav="false">

<section style="padding:120px 24px 80px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
    <div style="max-width:700px; margin:0 auto; text-align:center;">
        <div class="section-label"><i data-lucide="layers" style="width:12px; height:12px;"></i> Módulos</div>
        <h1 class="font-display" style="font-size:clamp(2rem,4vw,3.2rem); font-weight:800; letter-spacing:-.035em; color:#0f172a; margin:0 0 20px; line-height:1.1;">
            Activa solo lo que necesitas
        </h1>
        <p style="font-size:1.1rem; color:#64748b; line-height:1.75; margin:0;">
            Modular y escalable. Comienza con lo básico y agrega capacidades conforme crece tu operación.
        </p>
    </div>
</section>

<section style="padding:80px 24px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">

        @php
        $groups = [
            [
                'title'   => 'Core — Operaciones',
                'color'   => '#6366f1',
                'bg'      => '#eef2ff',
                'modules' => [
                    ['clipboard-list','Órdenes de Trabajo','Correctivos, preventivos y solicitudes de servicio. Flujos de aprobación y evidencia fotográfica.'],
                    ['calendar-check','Mantenimiento PM','Programas por tiempo, uso o condición. OTs automáticas y recordatorios.'],
                    ['cpu','Gestión de Activos','Árbol jerárquico, fichas técnicas, historial completo y etiquetas QR.'],
                    ['map-pin','Ubicaciones','Organiza activos por planta, área, línea y equipo.'],
                ],
            ],
            [
                'title'   => 'Inventario y Compras',
                'color'   => '#3b82f6',
                'bg'      => '#eff6ff',
                'modules' => [
                    ['package','Inventario','Control de stock, alertas de mínimos y trazabilidad por OT.'],
                    ['shopping-cart','Órdenes de Compra','Flujo de solicitud, aprobación y recepción de refacciones.'],
                    ['truck','Proveedores','Catálogo de proveedores con historial de compras y evaluación.'],
                    ['barcode','Código de Barras','Lectura QR/barcode desde la app móvil para inventario y activos.'],
                ],
            ],
            [
                'title'   => 'Seguridad y Cumplimiento',
                'color'   => '#22c55e',
                'bg'      => '#f0fdf4',
                'modules' => [
                    ['file-text','Permisos de Trabajo','PTW electrónicos con bloqueo y señalización LOTO integrado.'],
                    ['shield-check','Auditorías','Listas de verificación de seguridad y trazabilidad de hallazgos.'],
                    ['user-check','Certificaciones','Control de vencimiento de licencias y certificaciones del personal.'],
                    ['alert-octagon','Gestión de Incidentes','Reporte, investigación y seguimiento de incidentes y casi-accidentes.'],
                ],
            ],
            [
                'title'   => 'Tecnología Avanzada',
                'color'   => '#8b5cf6',
                'bg'      => '#f5f3ff',
                'modules' => [
                    ['activity','IoT y Sensores','Integración OPC-UA, MQTT. Monitoreo en tiempo real y OTs automáticas.'],
                    ['brain','IA Predictiva','Detección de anomalías, RUL y recomendaciones de mantenimiento.'],
                    ['bar-chart-2','Reportes y KPIs','Dashboards ejecutivos, MTBF, MTTR, OEE y exportación a Excel/PDF.'],
                    ['link','Integraciones','API REST, webhooks y conectores nativos para SAP, Oracle y más.'],
                ],
            ],
        ];
        @endphp

        @foreach($groups as $group)
        <div class="reveal" style="margin-bottom:56px;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid {{ $group['bg'] }};">
                <div style="width:10px; height:10px; border-radius:50%; background:{{ $group['color'] }};"></div>
                <h2 class="font-display" style="font-size:1.1rem; font-weight:800; color:#0f172a; margin:0; letter-spacing:-.01em;">{{ $group['title'] }}</h2>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px;">
                @foreach($group['modules'] as [$ico,$ttl,$desc])
                <div style="background:#f8fafc; border-radius:14px; padding:24px; border:1px solid #f1f5f9; transition:border-color .2s,box-shadow .2s;"
                     onmouseover="this.style.borderColor='{{ $group['color'] }}30'; this.style.boxShadow='0 8px 24px rgba(0,0,0,.06)'"
                     onmouseout="this.style.borderColor='#f1f5f9'; this.style.boxShadow=''">
                    <div style="width:40px; height:40px; border-radius:10px; background:{{ $group['bg'] }}; display:flex; align-items:center; justify-content:center; margin-bottom:14px;">
                        <i data-lucide="{{ $ico }}" style="width:18px; height:18px; color:{{ $group['color'] }};"></i>
                    </div>
                    <h3 class="font-display" style="font-size:.95rem; font-weight:700; color:#0f172a; margin:0 0 8px;">{{ $ttl }}</h3>
                    <p style="font-size:13px; color:#64748b; line-height:1.6; margin:0;">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="reveal" style="background:#0f172a; border-radius:20px; padding:48px 40px; text-align:center;">
            <h3 class="font-display" style="font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 12px;">¿Necesitas algo más específico?</h3>
            <p style="font-size:15px; color:rgba(255,255,255,.5); margin:0 0 28px; max-width:420px; margin-left:auto; margin-right:auto;">
                Desarrollamos integraciones y módulos a medida para industrias con requerimientos especiales.
            </p>
            <a href="{{ route('landing.contacto') }}" class="btn-accent">
                Hablar con un experto <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
            </a>
        </div>
    </div>
</section>

</x-layouts.landing>
