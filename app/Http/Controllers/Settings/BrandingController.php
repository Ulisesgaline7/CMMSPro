<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandingController extends Controller
{
    public function edit(): View
    {
        return view('settings.branding', [
            'tenant' => Auth::user()->tenant,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'logo' => ['nullable', 'image', 'max:2048'],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'brand_name' => ['nullable', 'string', 'max:100'],
            'subdomain' => ['nullable', 'string', 'alpha_dash', 'max:50'],
        ]);

        $tenant = Auth::user()->tenant;

        if ($request->hasFile('logo')) {
            if ($tenant->logo_path) {
                Storage::disk('public')->delete($tenant->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($data['logo']);

        $tenant->update($data);

        return back()->with('success', 'Configuración de marca actualizada correctamente.');
    }
}
