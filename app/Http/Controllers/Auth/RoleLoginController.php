<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleLoginController extends Controller
{
    private const ROLE_CONFIG = [
        'admin' => [
            'label'       => 'Administrador',
            'description' => 'Accede al panel completo de administración CMMS.',
            'icon'        => 'admin_panel_settings',
            'gradient'    => 'linear-gradient(135deg, #001433 0%, #002046 50%, #003580 100%)',
            'accent'      => '#e07b30',
            'features'    => [
                'Gestión completa de activos y OTs',
                'Reportes de confiabilidad MTBF/MTTR',
                'Configuración de planes de mantenimiento',
                'Control de inventario y compras',
            ],
        ],
        'supervisor' => [
            'label'       => 'Supervisor',
            'description' => 'Supervisa tu equipo y el estado de las operaciones.',
            'icon'        => 'supervisor_account',
            'gradient'    => 'linear-gradient(135deg, #0c1445 0%, #1e3a8a 50%, #1d4ed8 100%)',
            'accent'      => '#38bdf8',
            'features'    => [
                'Vista Kanban de órdenes de trabajo',
                'Asignación de técnicos',
                'Seguimiento de cumplimiento PM',
                'Alertas IoT y predictivo',
            ],
        ],
        'tecnico' => [
            'label'       => 'Técnico',
            'description' => 'Gestiona tus órdenes de trabajo asignadas.',
            'icon'        => 'handyman',
            'gradient'    => 'linear-gradient(135deg, #042f2e 0%, #134e4a 50%, #0f766e 100%)',
            'accent'      => '#2dd4bf',
            'features'    => [
                'Mis órdenes de trabajo del día',
                'Checklist y reportes de trabajo',
                'Escaneo QR de activos',
                'Acceso a documentos técnicos',
            ],
        ],
        'lector' => [
            'label'       => 'Auditor / Lector',
            'description' => 'Consulta reportes, auditorías e indicadores.',
            'icon'        => 'fact_check',
            'gradient'    => 'linear-gradient(135deg, #451a03 0%, #78350f 50%, #b45309 100%)',
            'accent'      => '#fbbf24',
            'features'    => [
                'Reportes de mantenimiento',
                'Seguimiento de auditorías',
                'KPIs de confiabilidad',
                'Historial de activos',
            ],
        ],
        'solicitante' => [
            'label'       => 'Solicitante',
            'description' => 'Envía solicitudes de servicio y da seguimiento.',
            'icon'        => 'support_agent',
            'gradient'    => 'linear-gradient(135deg, #2e1065 0%, #4c1d95 50%, #6d28d9 100%)',
            'accent'      => '#a78bfa',
            'features'    => [
                'Crear solicitudes de servicio',
                'Seguimiento en tiempo real',
                'Historial de solicitudes',
                'Notificaciones de estado',
            ],
        ],
    ];

    public function show(Request $request, string $role): Response
    {
        if (! array_key_exists($role, self::ROLE_CONFIG)) {
            throw new NotFoundHttpException;
        }

        return Inertia::render('auth/role-login', [
            'role'             => $role,
            'roleConfig'       => self::ROLE_CONFIG[$role],
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'status'           => $request->session()->get('status'),
        ]);
    }
}
