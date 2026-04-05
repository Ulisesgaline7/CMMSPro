<x-layouts.landing :settings="$settings" :darkNav="true">

{{-- ══ HERO ══════════════════════════════════════════════════════════ --}}
<section style="background:#0f172a; min-height:100vh; display:flex; flex-direction:column; justify-content:center; position:relative; overflow:hidden;">
    <div style="position:absolute;inset:0; background-image:linear-gradient(rgba(99,102,241,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.07) 1px,transparent 1px); background-size:64px 64px;"></div>
    <div style="position:absolute;top:-20%;left:50%;transform:translateX(-50%);width:900px;height:600px;border-radius:50%;background:radial-gradient(ellipse,rgba(99,102,241,.22) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="max-width:1200px; margin:0 auto; padding:120px 24px 80px; position:relative; z-index:1; width:100%;">
        <div style="max-width:800px; margin:0 auto; text-align:center;">

            <div class="badge-pill" style="margin-bottom:28px;">
                <i data-lucide="zap" style="width:12px; height:12px;"></i>
                Plataforma CMMS para equipos industriales LATAM
            </div>

            <h1 class="font-display" style="font-size:clamp(2.4rem,5vw,4.2rem); font-weight:800; line-height:1.1; letter-spacing:-.035em; color:#fff; margin:0 0 24px;">
                {!! nl2br(e($settings['hero_title'] ?? "El CMMS que entiende\ncómo trabaja tu equipo")) !!}
            </h1>

            <p style="font-size:1.125rem; color:rgba(255,255,255,.58); line-height:1.75; margin:0 0 40px; max-width:580px; margin-left:auto; margin-right:auto;">
                {{ $settings['hero_subtitle'] ?? 'Reduce costos, aumenta disponibilidad y toma decisiones basadas en datos con la plataforma de mantenimiento más completa del mercado.' }}
            </p>

            <div style="display:flex; align-items:center; justify-content:center; gap:14px; flex-wrap:wrap; margin-bottom:20px;">
                <a href="{{ route('register') }}" class="btn-accent" style="font-size:15px; padding:14px 30px;">
                    {{ $settings['hero_cta_primary'] ?? 'Prueba gratis 14 días' }}
                    <i data-lucide="arrow-right" style="width:17px; height:17px;"></i>
                </a>
                <a href="{{ route('landing.producto') }}" class="btn-ghost-white" style="font-size:15px; padding:14px 30px;">
                    <i data-lucide="play-circle" style="width:16px; height:16px;"></i>
                    {{ $settings['hero_cta_secondary'] ?? 'Ver el producto' }}
                </a>
            </div>
            <p style="font-size:12px; color:rgba(255,255,255,.3); margin-bottom:64px;">Sin tarjeta de crédito · Configuración en 5 minutos</p>

            {{-- Dashboard mockup --}}
            <div class="mockup-wrap reveal">
                <div class="mockup-bar">
                    <div class="mockup-dot" style="background:#ff5f57;"></div>
                    <div class="mockup-dot" style="background:#febc2e;"></div>
                    <div class="mockup-dot" style="background:#28c840;"></div>
                    <div style="flex:1; margin-left:12px; background:rgba(255,255,255,.05); border-radius:4px; height:22px; display:flex; align-items:center; padding:0 10px;">
                        <span style="font-size:11px; color:rgba(255,255,255,.3);">app.cmmspro.com/dashboard</span>
                    </div>
                </div>
                <div style="padding:24px; background:#1e293b;">
                    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px;">
                        @foreach([
                            ['Órdenes activas','24','#6366f1','clipboard-list'],
                            ['Activos operativos','142','#22c55e','cpu'],
                            ['Mantenimientos hoy','7','#f59e0b','calendar'],
                            ['Alertas críticas','3','#ef4444','alert-triangle'],
                        ] as [$lbl,$val,$clr,$ico])
                        <div style="background:#0f172a; border-radius:10px; padding:16px; border:1px solid rgba(255,255,255,.06);">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                                <span style="font-size:11px; color:rgba(255,255,255,.38);">{{ $lbl }}</span>
                                <div style="width:26px; height:26px; border-radius:6px; background:{{ $clr }}18; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="{{ $ico }}" style="width:12px; height:12px; color:{{ $clr }};"></i>
                                </div>
                            </div>
                            <span class="font-display" style="font-size:22px; font-weight:800; color:#fff;">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
                        <div style="background:#0f172a; border-radius:10px; padding:20px; border:1px solid rgba(255,255,255,.06);">
                            <p style="font-size:12px; font-weight:600; color:rgba(255,255,255,.4); margin:0 0 14px;">OTs — últimos 7 días</p>
                            <div style="display:flex; align-items:flex-end; gap:6px; height:68px;">
                                @foreach([40,65,45,80,55,90,70] as $h)
                                <div style="flex:1; border-radius:4px; background:linear-gradient(to top,#6366f1,#818cf8); height:{{ $h }}%; opacity:.85;"></div>
                                @endforeach
                            </div>
                        </div>
                        <div style="background:#0f172a; border-radius:10px; padding:20px; border:1px solid rgba(255,255,255,.06);">
                            <p style="font-size:12px; font-weight:600; color:rgba(255,255,255,.4); margin:0 0 12px;">Por estado</p>
                            @foreach([['Completadas','#22c55e',60],['En progreso','#6366f1',25],['Pendientes','#f59e0b',15]] as [$l,$c,$p])
                            <div style="margin-bottom:8px;">
                                <div style="display:flex; justify-content:space-between; font-size:10px; color:rgba(255,255,255,.4); margin-bottom:3px;"><span>{{ $l }}</span><span>{{ $p }}%</span></div>
                                <div style="height:3px; border-radius:2px; background:rgba(255,255,255,.06);">
                                    <div style="height:100%; border-radius:2px; background:{{ $c }}; width:{{ $p }}%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ LOGOS ══════════════════════════════════════════════════════════ --}}
<section style="padding:48px 24px; background:#fff; border-bottom:1px solid #f1f5f9;">
    <div style="max-width:1200px; margin:0 auto; text-align:center;">
        <p style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.1em; margin-bottom:28px;">Empresas que confían en nosotros</p>
        <div style="display:flex; align-items:center; justify-content:center; gap:48px; flex-wrap:wrap;">
            @foreach(['Pemex','GRUMA','Vitro','CEMEX','Bimbo','Grupo Herdez'] as $co)
            <span class="font-display" style="font-weight:800; font-size:17px; color:#cbd5e1; letter-spacing:-.02em;">{{ $co }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ STATS ══════════════════════════════════════════════════════════ --}}
<section style="padding:80px 24px; background:#f8fafc;">
    <div style="max-width:1200px; margin:0 auto;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:2px; background:#e2e8f0; border-radius:20px; overflow:hidden;">
            @foreach([
                [$settings['stat1_number'] ?? '98%', $settings['stat1_label'] ?? 'Disponibilidad de activos',           'trending-up', '#6366f1'],
                [$settings['stat2_number'] ?? '35%', $settings['stat2_label'] ?? 'Reducción en costos de mantenimiento','piggy-bank',  '#22c55e'],
                [$settings['stat3_number'] ?? '2x',  $settings['stat3_label'] ?? 'Vida útil de equipos',                'clock',       '#f59e0b'],
                [$settings['stat4_number'] ?? '500+',$settings['stat4_label'] ?? 'Empresas confían en nosotros',        'building-2',  '#3b82f6'],
            ] as [$num,$lbl,$ico,$clr])
            <div class="reveal" style="background:#fff; padding:40px 32px; text-align:center;">
                <div style="width:48px; height:48px; border-radius:12px; background:{{ $clr }}10; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                    <i data-lucide="{{ $ico }}" style="width:22px; height:22px; color:{{ $clr }};"></i>
                </div>
                <div class="font-display" style="font-size:2.5rem; font-weight:800; color:#0f172a; letter-spacing:-.03em; line-height:1;">{{ $num }}</div>
                <p style="font-size:14px; color:#64748b; margin:8px 0 0; font-weight:500;">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ FEATURES PREVIEW ══════════════════════════════════════════════ --}}
<section style="padding:100px 24px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">
        <div class="reveal" style="text-align:center; max-width:580px; margin:0 auto 56px;">
            <div class="section-label"><i data-lucide="sparkles" style="width:12px; height:12px;"></i> Capacidades</div>
            <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.75rem); font-weight:800; letter-spacing:-.03em; color:#0f172a; margin:0 0 16px; line-height:1.15;">
                {{ $settings['features_title'] ?? 'Todo lo que necesitas para gestionar el mantenimiento' }}
            </h2>
            <p style="font-size:1rem; color:#64748b; line-height:1.7; margin:0 0 24px;">{{ $settings['features_subtitle'] ?? 'Una plataforma unificada para equipos de mantenimiento de cualquier tamaño.' }}</p>
            <a href="{{ route('landing.producto') }}" class="btn-outline">
                Ver todas las funciones <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
            </a>
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px;">
            @foreach([
                ['clipboard-list','#6366f1','#eef2ff','Órdenes de Trabajo','Crea, asigna y da seguimiento a correctivos y preventivos con flujos de aprobación y evidencia fotográfica.'],
                ['calendar-check','#22c55e','#f0fdf4','Mantenimiento Preventivo','Programas PM por tiempo, uso o condición. Genera OTs automáticamente y envía recordatorios.'],
                ['cpu','#f59e0b','#fffbeb','Gestión de Activos','Árbol jerárquico, historial completo, documentos técnicos y QR para trazabilidad total.'],
                ['package','#3b82f6','#eff6ff','Inventario','Control de stock, alertas de nivel mínimo y órdenes de compra integradas.'],
                ['activity','#8b5cf6','#f5f3ff','IoT y Sensores','Monitoreo en tiempo real y OTs automáticas por umbral desde sensores industriales.'],
                ['brain','#ec4899','#fdf2f8','IA Predictiva','Detección de anomalías y cálculo de RUL basados en datos históricos y ML.'],
            ] as [$ico,$clr,$bg,$ttl,$desc])
            <div class="reveal" style="background:#f8fafc; border-radius:16px; padding:32px; border:1px solid #f1f5f9; transition:transform .2s,box-shadow .2s,border-color .2s;"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 48px rgba(0,0,0,.08)'; this.style.borderColor='#e2e8f0'"
                 onmouseout="this.style.transform=''; this.style.boxShadow=''; this.style.borderColor='#f1f5f9'">
                <div style="width:48px; height:48px; border-radius:12px; background:{{ $bg }}; display:flex; align-items:center; justify-content:center; margin-bottom:20px;">
                    <i data-lucide="{{ $ico }}" style="width:22px; height:22px; color:{{ $clr }};"></i>
                </div>
                <h3 class="font-display" style="font-size:1.05rem; font-weight:700; color:#0f172a; margin:0 0 10px;">{{ $ttl }}</h3>
                <p style="font-size:14px; color:#64748b; line-height:1.65; margin:0;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ TESTIMONIAL HIGHLIGHT ═══════════════════════════════════════════ --}}
<section style="padding:100px 24px; background:#0f172a; position:relative; overflow:hidden;">
    <div style="position:absolute;bottom:-50%;left:50%;transform:translateX(-50%);width:800px;height:400px;border-radius:50%;background:radial-gradient(ellipse,rgba(99,102,241,.18) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="max-width:1200px; margin:0 auto; position:relative; z-index:1;">
        <div class="reveal" style="max-width:700px; margin:0 auto; text-align:center;">
            <div style="display:flex; justify-content:center; gap:3px; margin-bottom:24px;">
                @for($i=0;$i<5;$i++)
                <i data-lucide="star" style="width:18px; height:18px; color:#f59e0b; fill:#f59e0b;"></i>
                @endfor
            </div>
            <p class="font-display" style="font-size:clamp(1.3rem,2.5vw,1.9rem); font-weight:700; color:#fff; line-height:1.55; margin:0 0 32px; font-style:italic;">
                "Redujimos el tiempo de respuesta a fallas en un 60%. Antes tardábamos días en asignar y cerrar una OT, ahora es cuestión de horas."
            </p>
            <div style="display:flex; align-items:center; justify-content:center; gap:14px;">
                <div style="width:48px; height:48px; border-radius:50%; background:#6366f1; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:16px; flex-shrink:0;">CM</div>
                <div style="text-align:left;">
                    <p class="font-display" style="font-weight:700; color:#fff; margin:0; font-size:15px;">Carlos Mendoza</p>
                    <p style="font-size:13px; color:rgba(255,255,255,.45); margin:3px 0 0;">Jefe de Mantenimiento · Planta automotriz, Monterrey</p>
                </div>
            </div>
            <a href="{{ route('landing.clientes') }}" style="display:inline-flex; align-items:center; gap:6px; margin-top:32px; font-size:14px; font-weight:600; color:rgba(255,255,255,.5); text-decoration:none; transition:color .2s;"
               onmouseover="this.style.color='rgba(255,255,255,.9)'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
                Ver más historias de clientes <i data-lucide="arrow-right" style="width:14px; height:14px;"></i>
            </a>
        </div>
    </div>
</section>

{{-- ══ CTA ════════════════════════════════════════════════════════════ --}}
<section style="padding:100px 24px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">
        <div class="reveal" style="background:#0f172a; border-radius:24px; padding:64px 48px; text-align:center; position:relative; overflow:hidden;">
            <div style="position:absolute;top:-40%;left:50%;transform:translateX(-50%);width:600px;height:400px;border-radius:50%;background:radial-gradient(ellipse,rgba(99,102,241,.2) 0%,transparent 70%);pointer-events:none;"></div>
            <div style="position:relative; z-index:1;">
                <div class="badge-pill" style="margin:0 auto 24px;">
                    <i data-lucide="rocket" style="width:12px; height:12px;"></i>
                    Empieza hoy sin costo
                </div>
                <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.75rem); font-weight:800; letter-spacing:-.03em; color:#fff; margin:0 0 16px; line-height:1.15;">
                    Tu equipo merece mejores herramientas
                </h2>
                <p style="font-size:1rem; color:rgba(255,255,255,.5); margin:0 auto 36px; max-width:480px; line-height:1.7;">
                    Únete a cientos de empresas que ya optimizaron su mantenimiento. Sin consultores, sin costos de arranque.
                </p>
                <div style="display:flex; align-items:center; justify-content:center; gap:14px; flex-wrap:wrap;">
                    <a href="{{ route('register') }}" class="btn-accent" style="font-size:15px; padding:14px 30px;">
                        Prueba gratis 14 días <i data-lucide="arrow-right" style="width:17px; height:17px;"></i>
                    </a>
                    <a href="{{ route('landing.precios') }}" class="btn-ghost-white" style="font-size:15px; padding:14px 30px;">
                        Ver planes y precios
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

</x-layouts.landing>
