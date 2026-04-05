<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    public function index(): Response
    {
        $settings = SiteSetting::all()
            ->groupBy('group')
            ->map(fn ($group) => $group->keyBy('key'));

        return Inertia::render('super-admin/site-settings', [
            'settings' => $settings,
            'groups'   => [
                'branding' => 'Marca y colores',
                'hero'     => 'Sección hero',
                'stats'    => 'Estadísticas',
                'features' => 'Funciones',
                'contact'  => 'Contacto y footer',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings'         => ['required', 'array'],
            'settings.*.key'   => ['required', 'string'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        foreach ($request->input('settings', []) as $item) {
            SiteSetting::where('key', $item['key'])->update(['value' => $item['value']]);
        }

        return back()->with('success', 'Configuración del sitio actualizada correctamente.');
    }
}
