<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Laravel\Fortify\Features;

class LandingController extends Controller
{
    private function settings(): array
    {
        return SiteSetting::allKeyed();
    }

    public function home(): View
    {
        return view('landing.home', [
            'settings' => $this->settings(),
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    }

    public function producto(): View
    {
        return view('landing.producto', ['settings' => $this->settings()]);
    }

    public function modulos(): View
    {
        return view('landing.modulos', ['settings' => $this->settings()]);
    }

    public function precios(): View
    {
        return view('landing.precios', ['settings' => $this->settings()]);
    }

    public function clientes(): View
    {
        return view('landing.clientes', ['settings' => $this->settings()]);
    }

    public function contacto(): View
    {
        return view('landing.contacto', ['settings' => $this->settings()]);
    }
}
