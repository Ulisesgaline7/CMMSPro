<x-layouts.landing :settings="$settings" title="Contacto" :darkNav="false">

<section style="padding:120px 24px 80px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
    <div style="max-width:700px; margin:0 auto; text-align:center;">
        <div class="section-label"><i data-lucide="mail" style="width:12px; height:12px;"></i> Contacto</div>
        <h1 class="font-display" style="font-size:clamp(2rem,4vw,3.2rem); font-weight:800; letter-spacing:-.035em; color:#0f172a; margin:0 0 20px; line-height:1.1;">
            Hablemos de tu operación
        </h1>
        <p style="font-size:1.1rem; color:#64748b; line-height:1.75; margin:0;">
            Nuestro equipo responde en menos de 24 horas. Sin presión de ventas, solo conversación.
        </p>
    </div>
</section>

<section style="padding:80px 24px; background:#fff;">
    <div style="max-width:1100px; margin:0 auto;">
        <div class="two-col">

            {{-- Form --}}
            <div class="reveal">
                <h2 class="font-display" style="font-size:1.3rem; font-weight:800; color:#0f172a; margin:0 0 28px; letter-spacing:-.02em;">Envíanos un mensaje</h2>
                <form action="#" method="POST" style="display:flex; flex-direction:column; gap:20px;">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;">Nombre</label>
                            <input type="text" name="name" placeholder="Carlos Mendoza" required
                                   style="width:100%; padding:11px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:14px; color:#0f172a; outline:none; transition:border-color .2s;"
                                   onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;">Empresa</label>
                            <input type="text" name="company" placeholder="Tu empresa"
                                   style="width:100%; padding:11px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:14px; color:#0f172a; outline:none; transition:border-color .2s;"
                                   onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;">Email</label>
                        <input type="email" name="email" placeholder="carlos@empresa.com" required
                               style="width:100%; padding:11px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:14px; color:#0f172a; outline:none; transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;">¿En qué podemos ayudarte?</label>
                        <select name="subject" style="width:100%; padding:11px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:14px; color:#374151; outline:none; background:#fff; transition:border-color .2s;"
                                onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='#e2e8f0'">
                            <option value="">Selecciona una opción</option>
                            <option>Demo del producto</option>
                            <option>Información de precios</option>
                            <option>Integración con ERP/SAP</option>
                            <option>White label para mi empresa</option>
                            <option>Soporte técnico</option>
                            <option>Otro</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;">Mensaje</label>
                        <textarea name="message" rows="5" placeholder="Cuéntanos sobre tu operación y en qué podemos ayudarte..." required
                                  style="width:100%; padding:11px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:14px; color:#0f172a; outline:none; transition:border-color .2s; resize:vertical;"
                                  onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
                    </div>
                    <button type="submit" class="btn-accent" style="justify-content:center; font-size:15px; padding:14px;">
                        Enviar mensaje <i data-lucide="send" style="width:16px; height:16px;"></i>
                    </button>
                    <p style="font-size:12px; color:#94a3b8; text-align:center; margin:0;">Respondemos en menos de 24 horas hábiles.</p>
                </form>
            </div>

            {{-- Info --}}
            <div class="reveal">
                <h2 class="font-display" style="font-size:1.3rem; font-weight:800; color:#0f172a; margin:0 0 28px; letter-spacing:-.02em;">También puedes contactarnos por</h2>

                <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:40px;">
                    @foreach([
                        ['mail','Email','hola@cmmspro.com','mailto:'.($settings['contact_email'] ?? 'hola@cmmspro.com'),'#6366f1'],
                        ['phone','Teléfono',$settings['contact_phone'] ?? '+52 55 1234 5678','tel:+525512345678','#22c55e'],
                        ['message-circle','WhatsApp','Chatea con nosotros','#','#25D366'],
                    ] as [$ico,$lbl,$val,$href,$clr])
                    <a href="{{ $href }}" style="display:flex; align-items:center; gap:16px; padding:20px; border-radius:14px; border:1px solid #e2e8f0; text-decoration:none; transition:border-color .2s,box-shadow .2s;"
                       onmouseover="this.style.borderColor='{{ $clr }}40'; this.style.boxShadow='0 4px 16px rgba(0,0,0,.06)'"
                       onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow=''">
                        <div style="width:44px; height:44px; border-radius:12px; background:{{ $clr }}12; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i data-lucide="{{ $ico }}" style="width:20px; height:20px; color:{{ $clr }};"></i>
                        </div>
                        <div>
                            <p style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin:0 0 3px;">{{ $lbl }}</p>
                            <p class="font-display" style="font-size:15px; font-weight:700; color:#0f172a; margin:0;">{{ $val }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>

                <div style="background:#f8fafc; border-radius:16px; padding:24px; border:1px solid #f1f5f9;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                        <i data-lucide="clock" style="width:18px; height:18px; color:var(--accent);"></i>
                        <p class="font-display" style="font-size:14px; font-weight:700; color:#0f172a; margin:0;">Horario de atención</p>
                    </div>
                    <p style="font-size:14px; color:#64748b; margin:0 0 6px;">Lunes – Viernes: <strong style="color:#374151;">9:00 – 18:00 CST</strong></p>
                    <p style="font-size:14px; color:#64748b; margin:0;">Tiempo de respuesta: <strong style="color:#374151;">menos de 24 h</strong></p>
                </div>
            </div>
        </div>
    </div>
</section>

</x-layouts.landing>
