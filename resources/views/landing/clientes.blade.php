<x-layouts.landing :settings="$settings" title="Clientes" :darkNav="false">

<section style="padding:120px 24px 80px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
    <div style="max-width:700px; margin:0 auto; text-align:center;">
        <div class="section-label"><i data-lucide="users" style="width:12px; height:12px;"></i> Clientes</div>
        <h1 class="font-display" style="font-size:clamp(2rem,4vw,3.2rem); font-weight:800; letter-spacing:-.035em; color:#0f172a; margin:0 0 20px; line-height:1.1;">
            Empresas que ya transformaron su mantenimiento
        </h1>
        <p style="font-size:1.1rem; color:#64748b; line-height:1.75; margin:0;">
            Descubre cómo equipos de mantenimiento industrial en toda LATAM usan CMMS Pro.
        </p>
    </div>
</section>

{{-- Stats --}}
<section style="padding:64px 24px; background:#fff; border-bottom:1px solid #f1f5f9;">
    <div style="max-width:1200px; margin:0 auto;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:32px; text-align:center;">
            @foreach([
                ['500+','Empresas activas','building-2','#6366f1'],
                ['15k+','Técnicos en campo','hard-hat','#22c55e'],
                ['2M+','OTs completadas','clipboard-list','#f59e0b'],
                ['98%','Satisfacción del cliente','star','#ec4899'],
            ] as [$num,$lbl,$ico,$clr])
            <div class="reveal">
                <div style="width:48px; height:48px; border-radius:12px; background:{{ $clr }}10; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                    <i data-lucide="{{ $ico }}" style="width:22px; height:22px; color:{{ $clr }};"></i>
                </div>
                <div class="font-display" style="font-size:2.2rem; font-weight:800; color:#0f172a; letter-spacing:-.03em;">{{ $num }}</div>
                <p style="font-size:14px; color:#64748b; margin:6px 0 0; font-weight:500;">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Logos --}}
<section style="padding:64px 24px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
    <div style="max-width:1200px; margin:0 auto; text-align:center;">
        <p style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.1em; margin-bottom:32px;">Presentes en las principales industrias</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px;">
            @foreach([
                ['Manufactura','factory'],
                ['Alimentaria','wheat'],
                ['Automotriz','car'],
                ['Petróleo y Gas','flame'],
                ['Farmacéutica','pill'],
                ['Minería','pickaxe'],
                ['Energía','zap'],
                ['Construcción','hard-hat'],
                ['Logística','truck'],
                ['Hospitalaria','heart-pulse'],
                ['Textil','shirt'],
                ['Papel y Celulosa','book'],
            ] as [$ind,$ico])
            <div class="reveal" style="background:#fff; border-radius:12px; padding:20px; border:1px solid #e2e8f0; text-align:center; transition:border-color .2s;"
                 onmouseover="this.style.borderColor='var(--accent)30'" onmouseout="this.style.borderColor='#e2e8f0'">
                <i data-lucide="{{ $ico }}" style="width:24px; height:24px; color:var(--accent); display:block; margin:0 auto 8px;"></i>
                <span style="font-size:13px; font-weight:600; color:#374151;">{{ $ind }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section style="padding:80px 24px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">
        <h2 class="font-display" style="font-size:1.6rem; font-weight:800; color:#0f172a; margin:0 0 48px; text-align:center; letter-spacing:-.02em;">Lo que dicen nuestros clientes</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:20px;">
            @foreach([
                ['Carlos Mendoza','Jefe de Mantenimiento','Planta automotriz, Monterrey','CM','#6366f1',5,'Redujimos el tiempo de respuesta a fallas en un 60%. Antes tardábamos días en asignar y cerrar una OT, ahora es cuestión de horas. El módulo de móvil fue el cambio más grande para nuestro equipo en campo.'],
                ['Ana Ruiz','Directora de Operaciones','Industria alimentaria, CDMX','AR','#22c55e',5,'El módulo de IoT nos permite ver en tiempo real el estado de nuestros compresores. Tuvimos ROI en menos de 3 meses y eliminamos prácticamente los paros no programados.'],
                ['Roberto Vega','Gerente de Facility','Corporativo, Guadalajara','RV','#f59e0b',5,'Implementamos en dos días. El soporte es excelente y la app móvil es muy fácil de usar para nuestros técnicos. La curva de aprendizaje fue mínima.'],
                ['Patricia Leal','Gerente de Mantenimiento','Planta farmacéutica, Querétaro','PL','#3b82f6',5,'La trazabilidad que ofrece CMMS Pro es fundamental para nuestras auditorías de calidad. Todo queda documentado y podemos generar reportes regulatorios en minutos.'],
                ['Diego Morales','Director Técnico','Minera en Sonora','DM','#8b5cf6',5,'Antes usábamos Excel para todo. Migrar a CMMS Pro fue un antes y un después. Ahora tenemos KPIs reales y podemos planificar el mantenimiento con datos.'],
                ['Lucía Hernández','Coordinadora de Planta','Empresa textil, Puebla','LH','#ec4899',5,'El precio es muy competitivo para lo que ofrece. Hemos probado otras herramientas del mercado y ninguna tiene la misma relación calidad-precio para el mercado LATAM.'],
            ] as [$name,$role,$company,$ini,$clr,$stars,$quote])
            <div class="reveal" style="background:#fff; border-radius:16px; padding:32px; border:1px solid #f1f5f9; box-shadow:0 2px 12px rgba(0,0,0,.03);">
                <div style="display:flex; gap:3px; margin-bottom:16px;">
                    @for($i=0;$i<$stars;$i++)
                    <i data-lucide="star" style="width:13px; height:13px; color:#f59e0b; fill:#f59e0b;"></i>
                    @endfor
                </div>
                <p style="font-size:15px; color:#374151; line-height:1.7; margin:0 0 24px; font-style:italic;">"{{ $quote }}"</p>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; border-radius:50%; background:{{ $clr }}; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:13px; flex-shrink:0;">{{ $ini }}</div>
                    <div>
                        <p class="font-display" style="font-size:14px; font-weight:700; color:#0f172a; margin:0;">{{ $name }}</p>
                        <p style="font-size:12px; color:#94a3b8; margin:2px 0 0;">{{ $role }} · {{ $company }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section style="padding:80px 24px; background:#0f172a;">
    <div style="max-width:640px; margin:0 auto; text-align:center;">
        <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.5rem); font-weight:800; letter-spacing:-.03em; color:#fff; margin:0 0 16px; line-height:1.15;">
            Únete a los equipos que ya avanzan
        </h2>
        <p style="font-size:1rem; color:rgba(255,255,255,.5); margin:0 0 32px; line-height:1.7;">
            14 días gratis, sin tarjeta de crédito. Configuración en minutos.
        </p>
        <a href="{{ route('register') }}" class="btn-accent" style="font-size:15px; padding:14px 30px;">
            Empezar ahora <i data-lucide="arrow-right" style="width:17px; height:17px;"></i>
        </a>
    </div>
</section>

</x-layouts.landing>
