import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    Activity, AlertTriangle, ArrowLeft, BarChart2, Bell, BookOpen, Building2,
    ChevronRight, CircleCheck, CircleSlash, Clock, CreditCard, FileText,
    Globe, Key, Layout, LayoutDashboard, LogOut, Mail, Package, Palette,
    Plug, Puzzle, Receipt, ScrollText, Server, Settings2, ShieldCheck,
    ToggleLeft, TrendingUp, UserCheck, UserCog, Users, Webhook, Zap,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import type { Auth } from '@/types';

function getInitials(name: string): string {
    return name.split(' ').map((p) => p[0]).slice(0, 2).join('').toUpperCase();
}

interface NavGroup {
    label: string;
    items: NavItem[];
}

interface NavItem {
    href: string;
    label: string;
    icon: React.ElementType;
    matchPrefix?: string;
    badge?: string | number;
    badgeColor?: 'purple' | 'green' | 'orange';
}

interface SuperAdminLayoutProps {
    children: React.ReactNode;
    title?: string;
    breadcrumb?: string;
}

export default function SuperAdminLayout({ children, title, breadcrumb }: SuperAdminLayoutProps) {
    const page = usePage();
    const url = page.url;
    const auth = page.props.auth as Auth;
    const user = auth.user;

    function isActive(href: string, matchPrefix?: string): boolean {
        const prefix = matchPrefix ?? href;
        return url === href || url.startsWith(prefix + '/') || url.startsWith(prefix + '?');
    }

    const navGroups: NavGroup[] = [
        {
            label: 'Panel',
            items: [
                { href: '/super-admin', label: 'Dashboard General', icon: LayoutDashboard, matchPrefix: '/super-admin' },
            ],
        },
        {
            label: 'Gestión de Clientes',
            items: [
                { href: '/super-admin/tenants', label: 'Todos los Clientes', icon: Building2, matchPrefix: '/super-admin/tenants' },
                { href: '/super-admin/tenants?status=active', label: 'Clientes Activos', icon: CircleCheck },
                { href: '/super-admin/tenants?status=trial', label: 'Cuentas Trial', icon: Clock },
                { href: '/super-admin/tenants?status=suspended', label: 'Suspendidos', icon: CircleSlash },
            ],
        },
        {
            label: 'Suscripciones & Ingresos',
            items: [
                { href: '/super-admin/tenants', label: 'Suscripciones', icon: CreditCard },
                { href: '/super-admin/tenants', label: 'Facturas & Pagos', icon: Receipt },
                { href: '/super-admin', label: 'MRR & Analytics', icon: TrendingUp },
                { href: '/super-admin', label: 'Reporte de Ingresos', icon: BarChart2 },
            ],
        },
        {
            label: 'Módulos & Planes',
            items: [
                { href: '/super-admin/tenants', label: 'Catálogo de Módulos', icon: Puzzle },
                { href: '/super-admin/tenants', label: 'Asignación por Cliente', icon: ToggleLeft },
                { href: '/super-admin', label: 'Planes de Precios', icon: Package },
            ],
        },
        {
            label: 'Usuarios del Sistema',
            items: [
                { href: '/super-admin/users', label: 'Todos los Usuarios', icon: Users, matchPrefix: '/super-admin/users' },
                { href: '/super-admin/users', label: 'Admins de Tenant', icon: UserCheck },
                { href: '/super-admin/users', label: 'Impersonar Usuario', icon: UserCog },
            ],
        },
        {
            label: 'Configuración',
            items: [
                { href: '/super-admin', label: 'Ajustes Generales', icon: Settings2 },
                { href: '/super-admin', label: 'Dominios & Subdominios', icon: Globe },
                { href: '/super-admin', label: 'Email & Notificaciones', icon: Mail },
                { href: '/super-admin/site-settings', label: 'Apariencia Global', icon: Palette, matchPrefix: '/super-admin/site-settings' },
            ],
        },
        {
            label: 'Credenciales & API',
            items: [
                { href: '/super-admin', label: 'Claves API', icon: Key },
                { href: '/super-admin', label: 'Webhooks', icon: Webhook },
                { href: '/super-admin', label: 'Integración Stripe', icon: Zap },
                { href: '/super-admin', label: 'Integraciones', icon: Plug },
            ],
        },
        {
            label: 'Contenido & Marketing',
            items: [
                { href: '/super-admin', label: 'Blog & Noticias', icon: FileText },
                { href: '/super-admin/site-settings', label: 'Landing Page', icon: Layout },
                { href: '/super-admin', label: 'Documentación', icon: BookOpen },
            ],
        },
        {
            label: 'Sistema',
            items: [
                { href: '/super-admin', label: 'Logs del Sistema', icon: ScrollText },
                { href: '/super-admin', label: 'Actividad Reciente', icon: Activity },
                { href: '/super-admin', label: 'Estado del Servidor', icon: Server },
                { href: '/super-admin', label: 'Modo Mantenimiento', icon: AlertTriangle },
            ],
        },
    ];

    return (
        <>
            {title && <Head title={`${title} – Super Admin`} />}

            <div className="flex min-h-screen" style={{ fontFamily: "'Inter', sans-serif" }}>

                {/* ── Sidebar ──────────────────────────────────── */}
                <aside
                    className="fixed left-0 top-0 h-screen flex flex-col z-50"
                    style={{ width: 260, background: '#1e293b', borderRight: '1px solid rgba(255,255,255,0.06)' }}
                >
                    {/* Brand */}
                    <div className="flex items-center gap-3 px-5 py-5 border-b" style={{ borderColor: 'rgba(255,255,255,0.06)' }}>
                        <div className="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                            style={{ background: 'linear-gradient(135deg,#6366f1,#8b5cf6)' }}>
                            <ShieldCheck className="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <p className="text-white font-bold text-sm leading-none">CMMS Pro</p>
                            <p className="text-xs mt-0.5" style={{ color: '#6366f1' }}>Super Admin Panel</p>
                        </div>
                    </div>

                    {/* Scrollable nav */}
                    <nav className="flex-1 overflow-y-auto py-2" style={{ scrollbarWidth: 'thin', scrollbarColor: '#334155 transparent' }}>
                        {navGroups.map((group) => (
                            <div key={group.label}>
                                <p style={{
                                    fontSize: 9, fontWeight: 800, letterSpacing: '0.12em',
                                    textTransform: 'uppercase', color: '#64748b',
                                    padding: '16px 20px 6px',
                                }}>
                                    {group.label}
                                </p>
                                {group.items.map((item) => {
                                    const active = isActive(item.href, item.matchPrefix);
                                    const Icon = item.icon;
                                    return (
                                        <Link
                                            key={item.href + item.label}
                                            href={item.href}
                                            className={cn('flex items-center gap-2.5 transition-all duration-150')}
                                            style={{
                                                padding: active ? '8px 20px 8px 17px' : '8px 20px',
                                                fontSize: 12.5, fontWeight: 500,
                                                color: active ? '#a5b4fc' : '#94a3b8',
                                                background: active ? 'rgba(99,102,241,0.15)' : 'transparent',
                                                borderLeft: active ? '3px solid #6366f1' : '3px solid transparent',
                                                textDecoration: 'none',
                                            }}
                                            onMouseEnter={(e) => {
                                                if (!active) {
                                                    (e.currentTarget as HTMLElement).style.background = 'rgba(99,102,241,0.08)';
                                                    (e.currentTarget as HTMLElement).style.color = '#e2e8f0';
                                                }
                                            }}
                                            onMouseLeave={(e) => {
                                                if (!active) {
                                                    (e.currentTarget as HTMLElement).style.background = 'transparent';
                                                    (e.currentTarget as HTMLElement).style.color = '#94a3b8';
                                                }
                                            }}
                                        >
                                            <Icon className="w-4 h-4 shrink-0" />
                                            <span className="flex-1">{item.label}</span>
                                            {item.badge !== undefined && (
                                                <span style={{
                                                    fontSize: 9, fontWeight: 700, padding: '1px 6px',
                                                    borderRadius: 20,
                                                    background: item.badgeColor === 'green' ? 'rgba(34,197,94,0.2)' : item.badgeColor === 'orange' ? 'rgba(249,115,22,0.2)' : 'rgba(99,102,241,0.25)',
                                                    color: item.badgeColor === 'green' ? '#86efac' : item.badgeColor === 'orange' ? '#fdba74' : '#a5b4fc',
                                                }}>
                                                    {item.badge}
                                                </span>
                                            )}
                                        </Link>
                                    );
                                })}
                            </div>
                        ))}
                        <div style={{ height: 24 }} />
                    </nav>

                    {/* Back to app */}
                    <div className="px-4 py-3 border-t" style={{ borderColor: 'rgba(255,255,255,0.06)' }}>
                        <Link
                            href="/dashboard"
                            className="flex items-center gap-2 text-xs font-medium px-3 py-2 rounded-lg transition-colors"
                            style={{ color: '#64748b' }}
                            onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.color = '#e2e8f0'; }}
                            onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.color = '#64748b'; }}
                        >
                            <ArrowLeft className="w-3.5 h-3.5" />
                            <span>Volver al Panel Tenant</span>
                        </Link>
                    </div>
                </aside>

                {/* ── Main area ────────────────────────────────── */}
                <div className="flex-1 flex flex-col min-h-screen" style={{ marginLeft: 260, background: '#f1f5f9' }}>

                    {/* Top header */}
                    <header
                        className="sticky top-0 z-40 flex items-center justify-between px-6 bg-white border-b"
                        style={{ height: 56, borderColor: '#e2e8f0' }}
                    >
                        <div className="flex items-center gap-3">
                            {/* Breadcrumb */}
                            <div className="flex items-center gap-1.5 text-xs">
                                <span className="font-semibold" style={{ color: '#6366f1' }}>Super Admin</span>
                                {breadcrumb && (
                                    <>
                                        <ChevronRight className="w-3 h-3" style={{ color: '#94a3b8' }} />
                                        <span style={{ color: '#475569' }}>{breadcrumb}</span>
                                    </>
                                )}
                            </div>
                            {/* Badge */}
                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                style={{ background: '#ede9fe', color: '#6d28d9' }}>
                                <ShieldCheck className="w-2.5 h-2.5" />
                                Super Admin
                            </span>
                        </div>

                        <div className="flex items-center gap-4">
                            {/* Sistema OK */}
                            <div className="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                                style={{ background: '#f0fdf4', border: '1px solid #bbf7d0' }}>
                                <span className="w-1.5 h-1.5 rounded-full animate-pulse" style={{ background: '#22c55e' }} />
                                <span className="text-[10px] font-bold uppercase tracking-wider" style={{ color: '#16a34a' }}>Sistema OK</span>
                            </div>

                            {/* Bell */}
                            <button className="relative p-1.5 rounded-lg transition-colors hover:bg-slate-100">
                                <Bell className="w-4 h-4" style={{ color: '#64748b' }} />
                            </button>

                            {/* User */}
                            <button
                                className="flex items-center gap-2.5 pl-3 border-l"
                                style={{ borderColor: '#e2e8f0' }}
                                onClick={() => router.post('/super-admin/logout')}
                                title="Cerrar sesión"
                            >
                                <div className="text-right">
                                    <p className="text-xs font-bold" style={{ color: '#0f172a' }}>{user?.name ?? 'Admin'}</p>
                                    <p className="text-[10px]" style={{ color: '#64748b' }}>Super Admin</p>
                                </div>
                                <div className="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                                    style={{ background: 'linear-gradient(135deg,#6366f1,#8b5cf6)' }}>
                                    {getInitials(user?.name ?? 'SA')}
                                </div>
                                <LogOut className="w-3.5 h-3.5" style={{ color: '#94a3b8' }} />
                            </button>
                        </div>
                    </header>

                    {/* Content */}
                    <div className="flex-1 pb-10">{children}</div>

                    {/* Footer */}
                    <footer className="flex items-center justify-between px-6 h-9 border-t bg-white text-[10px] font-semibold uppercase tracking-wider"
                        style={{ borderColor: '#e2e8f0', color: '#94a3b8' }}>
                        <span className="flex items-center gap-2">
                            <span className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse" />
                            Todos los Sistemas Operativos
                        </span>
                        <span>CMMS Pro v1.0.0-BETA</span>
                        <span>Panel de Administración</span>
                    </footer>
                </div>
            </div>
        </>
    );
}
