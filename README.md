# CMMS Pro

Sistema de Gestión de Mantenimiento (CMMS) SaaS multi-tenant construido con Laravel 13, Blade, Alpine.js y Tailwind CSS v4.

---

## Stack Tecnológico

| Capa         | Tecnología                        |
|--------------|-----------------------------------|
| Backend      | PHP 8.5 + Laravel 13              |
| Frontend     | Blade + Alpine.js + Tailwind v4   |
| Auth         | Laravel Fortify (Inertia/React)   |
| Base de datos | MySQL                            |
| Build        | Vite                              |
| Tests        | PHPUnit 12                        |

> **Nota:** Las páginas de autenticación (login, registro) usan Inertia.js + React. Todas las páginas de la aplicación (dashboard, activos, órdenes de trabajo) usan Blade + Alpine.js.

---

## Módulos Implementados

### Dashboard
- KPIs de órdenes de trabajo (total, pendientes, en progreso, completadas hoy, vencidas)
- Gráficas de distribución por estado, tipo y prioridad
- Estadísticas de activos
- Últimas órdenes de trabajo
- Próximos mantenimientos preventivos
- Alertas de stock bajo

### Órdenes de Trabajo (`/work-orders`)
- **Listado** con filtros por estado, tipo y prioridad — búsqueda por código/título
- **Crear** (`/work-orders/create`) — tipo (CM/PM/PdM), prioridad, activo, técnico asignado, fechas
- **Detalle** (`/work-orders/{id}`) — checklist interactivo, notas, cambio de estado, feed de actividad

### Activos (`/assets`)
- **Listado** con filtros por estado, criticidad y categoría
- **Crear** (`/assets/create`) — identificación, fabricante, ubicación, jerarquía padre-hijo, fechas, costo
- **Detalle** (`/assets/{id}`) — historial de OT asociadas, sub-activos, garantía
- **Editar** (`/assets/{id}/edit`)

---

## Arquitectura

### Multi-tenancy
Row-level tenancy usando el trait `HasTenant` con un `TenantScope` global de Eloquent.
Cada modelo con `use HasTenant` filtra automáticamente por `tenant_id` del usuario autenticado.

```
app/
├── Concerns/HasTenant.php       # Trait que aplica TenantScope
├── Scopes/TenantScope.php       # Global scope por tenant_id
```

### Enums (PHP 8.1+)
Todos los valores de estado/tipo/prioridad se manejan con enums backed:

```
app/Enums/
├── AssetStatus.php         active | inactive | under_maintenance | retired
├── AssetCriticality.php    low | medium | high | critical
├── WorkOrderStatus.php     draft | pending | in_progress | on_hold | completed | cancelled
├── WorkOrderType.php       corrective | preventive | predictive
├── WorkOrderPriority.php   low | medium | high | critical
```

Los enums incluyen métodos `label()` y `color()` para presentación en vistas.

### Frontend
- **Layout principal:** `resources/views/components/layouts/cmms.blade.php`
  Sidebar fijo, header sticky, status bar — compartido por todas las páginas de app.
- **Alpine.js** para interacciones ligeras: selección de tipo/prioridad/estado con botones visuales.
- **Tailwind CSS v4** configurado vía `@theme` en `resources/css/app.css`.

### Vite — Dos entry points
```
resources/js/app.tsx      → Auth pages (React + Inertia)
resources/js/app-blade.js → App pages (Alpine.js only)
```

---

## Estructura de Archivos Clave

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── WorkOrderController.php
│   └── AssetController.php
├── Http/Requests/
│   ├── StoreWorkOrderRequest.php
│   ├── UpdateWorkOrderStatusRequest.php
│   ├── StoreAssetRequest.php
│   └── UpdateAssetRequest.php
├── Models/
│   ├── Asset.php
│   ├── WorkOrder.php
│   ├── WorkOrderActivity.php
│   ├── WorkOrderChecklistItem.php
│   ├── Location.php
│   ├── AssetCategory.php
│   └── Part.php

resources/views/
├── components/layouts/cmms.blade.php   # Layout principal
├── dashboard.blade.php
├── work-orders/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php
└── assets/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    ├── show.blade.php
    └── partials/form.blade.php         # Formulario compartido create/edit
```

---

## Instalación Local

```bash
# 1. Clonar e instalar dependencias
composer install
npm install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Base de datos
php artisan migrate --seed

# 4. Compilar assets
npm run dev      # desarrollo
npm run build    # producción

# 5. Servidor
composer run dev
```

---

## Testing

```bash
# Todos los tests
php artisan test --compact

# Por módulo
php artisan test --compact tests/Feature/AssetTest.php
php artisan test --compact tests/Feature/DashboardTest.php
```

Los tests usan `RefreshDatabase` con factories. El `TenantScope` se respeta automáticamente en tests al crear usuarios con `tenant_id`.

---

## Roadmap

- [ ] Módulo de Inventario (partes y repuestos)
- [ ] Planes de Mantenimiento Preventivo (PM scheduling)
- [ ] Módulo de Sensores / IoT
- [ ] Reportes y exportación PDF/Excel
- [ ] Gestión de usuarios y roles por tenant
- [ ] API REST para integraciones externas
# CMMSPro
