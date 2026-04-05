<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Branding
            ['key' => 'site_name',         'label' => 'Nombre del sitio',              'type' => 'text',     'group' => 'branding', 'value' => 'CMMS Pro'],
            ['key' => 'site_tagline',       'label' => 'Eslogan',                       'type' => 'text',     'group' => 'branding', 'value' => 'Gestión de mantenimiento inteligente'],
            ['key' => 'primary_color',      'label' => 'Color primario',                'type' => 'color',    'group' => 'branding', 'value' => '#002046'],
            ['key' => 'accent_color',       'label' => 'Color de acento',               'type' => 'color',    'group' => 'branding', 'value' => '#f97316'],
            ['key' => 'logo_url',           'label' => 'URL del logo',                  'type' => 'image',    'group' => 'branding', 'value' => null],

            // Hero section
            ['key' => 'hero_title',         'label' => 'Título del hero',               'type' => 'text',     'group' => 'hero',     'value' => 'Mantenimiento Industrial Inteligente'],
            ['key' => 'hero_subtitle',      'label' => 'Subtítulo del hero',            'type' => 'textarea', 'group' => 'hero',     'value' => 'Reduce costos, aumenta disponibilidad y toma decisiones basadas en datos con la plataforma CMMS más completa del mercado.'],
            ['key' => 'hero_cta_primary',   'label' => 'CTA principal',                 'type' => 'text',     'group' => 'hero',     'value' => 'Prueba gratis 14 días'],
            ['key' => 'hero_cta_secondary', 'label' => 'CTA secundario',                'type' => 'text',     'group' => 'hero',     'value' => 'Ver demostración'],

            // Stats
            ['key' => 'stat1_number',       'label' => 'Estadística 1 — número',        'type' => 'text',     'group' => 'stats',    'value' => '98%'],
            ['key' => 'stat1_label',        'label' => 'Estadística 1 — etiqueta',      'type' => 'text',     'group' => 'stats',    'value' => 'Disponibilidad de activos'],
            ['key' => 'stat2_number',       'label' => 'Estadística 2 — número',        'type' => 'text',     'group' => 'stats',    'value' => '35%'],
            ['key' => 'stat2_label',        'label' => 'Estadística 2 — etiqueta',      'type' => 'text',     'group' => 'stats',    'value' => 'Reducción en costos de mantenimiento'],
            ['key' => 'stat3_number',       'label' => 'Estadística 3 — número',        'type' => 'text',     'group' => 'stats',    'value' => '2x'],
            ['key' => 'stat3_label',        'label' => 'Estadística 3 — etiqueta',      'type' => 'text',     'group' => 'stats',    'value' => 'Vida útil de equipos'],
            ['key' => 'stat4_number',       'label' => 'Estadística 4 — número',        'type' => 'text',     'group' => 'stats',    'value' => '500+'],
            ['key' => 'stat4_label',        'label' => 'Estadística 4 — etiqueta',      'type' => 'text',     'group' => 'stats',    'value' => 'Empresas confían en nosotros'],

            // Features
            ['key' => 'features_title',     'label' => 'Título de funciones',           'type' => 'text',     'group' => 'features', 'value' => 'Todo lo que necesitas para gestionar el mantenimiento'],
            ['key' => 'features_subtitle',  'label' => 'Subtítulo de funciones',        'type' => 'textarea', 'group' => 'features', 'value' => 'Una plataforma unificada para equipos de mantenimiento de cualquier tamaño.'],

            // Contact / footer
            ['key' => 'contact_email',      'label' => 'Email de contacto',             'type' => 'text',     'group' => 'contact',  'value' => 'hola@cmmspro.com'],
            ['key' => 'contact_phone',      'label' => 'Teléfono de contacto',          'type' => 'text',     'group' => 'contact',  'value' => '+52 55 1234 5678'],
            ['key' => 'footer_text',        'label' => 'Texto del footer',              'type' => 'textarea', 'group' => 'contact',  'value' => '© 2026 CMMS Pro. Todos los derechos reservados.'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
