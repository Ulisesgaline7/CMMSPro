import { Head, Link, usePage } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import type { Auth } from '@/types';

interface NavItem {
    href: string;
    label: string;
    icon: string;
    matchPrefix?: string;
    roles?: string[];
}

// Navigation items grouped by role access
const mainNavItems: NavItem[] = [
    { href: '/dashboard',    label: 'Dashboard',          icon: 'dashboard' },
    { href: '/assets',       label: 'Activos',            icon: 'factory',      roles: ['admin', 'supervisor', 'technician', 'reader'] },
    { href: '/work-orders',  label: 'Órdenes de Trabajo', icon: 'handyman',     matchPrefix: '/work-orders', roles: ['admin', 'supervisor', 'technician', 'reader'] },
    { href: '/inventory',    label: 'Inventario',         icon: 'inventory_2',  roles: ['admin', 'supervisor'] },
    { href: '/maintenance-plans', label: 'Planes PM',     icon: 'event_repeat', roles: ['admin', 'supervisor'] },
    { href: '/sensors',      label: 'IoT / Sensores',     icon: 'sensors',      roles: ['admin', 'supervisor', 'reader'] },
    { href: '/predictive',   label: 'Predictivo',         icon: 'auto_graph',   roles: ['admin', 'supervisor', 'reader'] },
    { href: '/audits',       label: 'Auditorías',         icon: 'fact_check',   roles: ['admin', 'supervisor', 'reader'] },
    { href: '/permits',      label: 'Permisos LOTO',      icon: 'lock',         roles: ['admin', 'supervisor'] },
    { href: '/documents',    label: 'Documentos',         icon: 'folder_open',  roles: ['admin', 'supervisor', 'reader'] },
];

const technicianNavItems: NavItem[] = [
    { href: '/dashboard',   label: 'Mis Órdenes',        icon: 'dashboard' },
    { href: '/work-orders', label: 'Órdenes de Trabajo', icon: 'handyman', matchPrefix: '/work-orders' },
    { href: '/assets',      label: 'Activos',            icon: 'factory' },
    { href: '/documents',   label: 'Documentos',         icon: 'folder_open' },
];

const requesterNavItems: NavItem[] = [
    { href: '/dashboard',       label: 'Inicio',             icon: 'dashboard' },
    { href: '/service-requests', label: 'Mis Solicitudes',   icon: 'support_agent', matchPrefix: '/service-requests' },
];

function MaterialIcon({ name, fill = false, className }: { name: string; fill?: boolean; className?: string }) {
    return (
        <span
            className={cn('material-symbols-outlined select-none', className)}
            style={fill ? { fontVariationSettings: "'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24" } : undefined}
        >
            {name}
        </span>
    );
}

function getInitials(name: string): string {
    return name.split(' ').map((p) => p[0]).slice(0, 2).join('').toUpperCase();
}

interface CmmsLayoutProps {
    children: React.ReactNode;
    title?: string;
    headerTitle?: string;
}

export default function CmmsLayout({ children, title, headerTitle }: CmmsLayoutProps) {
    const page = usePage();
    const url = page.url;
    const auth = page.props.auth as Auth;
    const user = auth.user;
    const role = (user as { role?: string })?.role ?? 'reader';

    // Select nav items based on role
    let navItems: NavItem[];
    if (role === 'technician') {
        navItems = technicianNavItems;
    } else if (role === 'requester') {
        navItems = requesterNavItems;
    } else {
        navItems = mainNavItems.filter(
            (item) => !item.roles || item.roles.includes(role)
        );
    }

    function isActive(item: NavItem): boolean {
        const prefix = item.matchPrefix ?? item.href;
        return url === item.href || url.startsWith(prefix + '/') || url.startsWith(prefix + '?');
    }

    const roleLabel: Record<string, string> = {
        admin:      'Administrador',
        supervisor: 'Supervisor',
        technician: 'Técnico',
        reader:     'Auditor',
        requester:  'Solicitante',
    };

    const roleBadgeColor: Record<string, string> = {
        admin:      'bg-orange-500/20 text-orange-300',
        supervisor: 'bg-blue-500/20 text-blue-300',
        technician: 'bg-teal-500/20 text-teal-300',
        reader:     'bg-amber-500/20 text-amber-300',
        requester:  'bg-purple-500/20 text-purple-300',
    };

    return (
        <>
            {title && <Head title={title} />}

            <div className="min-h-screen bg-[#f9f9fd] text-[#191c1e]">
                {/* ── Sidebar ──────────────────────────────────── */}
                <aside className="fixed left-0 top-0 h-screen w-64 flex flex-col bg-[#002046] shadow-xl z-50">
                    {/* Logo + user */}
                    <div className="py-6 px-6 mb-1 border-b border-white/10">
                        <h1 className="text-white text-lg font-extrabold tracking-wider leading-none font-headline">
                            CMMS Pro
                        </h1>
                        <p className="text-blue-100/50 text-xs font-medium mt-1 truncate">
                            {user?.name ?? 'Usuario'}
                        </p>
                        <span className={cn('inline-block mt-2 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider', roleBadgeColor[role] ?? 'bg-white/10 text-white/60')}>
                            {roleLabel[role] ?? role}
                        </span>
                    </div>

                    {/* Main nav */}
                    <nav className="flex flex-col flex-1 overflow-y-auto py-2">
                        {navItems.map((item) => {
                            const active = isActive(item);
                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    className={cn(
                                        'py-3 px-6 flex items-center gap-3 text-[10.5px] font-semibold tracking-[0.07em] uppercase transition-all duration-200',
                                        active
                                            ? 'bg-white/10 text-white border-l-4 border-[#904d00] pl-5'
                                            : 'text-blue-100/60 hover:bg-white/5 hover:text-white hover:translate-x-1',
                                    )}
                                >
                                    <MaterialIcon name={item.icon} className={cn('text-[18px]', active ? 'text-white' : '')} />
                                    <span>{item.label}</span>
                                </Link>
                            );
                        })}

                        <div className="flex-1" />

                        {/* Reports link for admin/supervisor */}
                        {(role === 'admin' || role === 'supervisor') && (
                            <Link
                                href="/reports"
                                className={cn(
                                    'py-3 px-6 flex items-center gap-3 text-[10.5px] font-semibold tracking-[0.07em] uppercase transition-all duration-200',
                                    url.startsWith('/reports')
                                        ? 'bg-white/10 text-white border-l-4 border-[#904d00] pl-5'
                                        : 'text-blue-100/60 hover:bg-white/5 hover:text-white hover:translate-x-1',
                                )}
                            >
                                <MaterialIcon name="assessment" className="text-[18px]" />
                                <span>Reportes</span>
                            </Link>
                        )}

                        <Link
                            href="/settings/profile"
                            className={cn(
                                'py-3 px-6 flex items-center gap-3 text-[10.5px] font-semibold tracking-[0.07em] uppercase transition-all duration-200 mb-2',
                                url.startsWith('/settings')
                                    ? 'bg-white/10 text-white border-l-4 border-[#904d00] pl-5'
                                    : 'text-blue-100/60 hover:bg-white/5 hover:text-white hover:translate-x-1',
                            )}
                        >
                            <MaterialIcon name="settings" className="text-[18px]" />
                            <span>Configuración</span>
                        </Link>
                    </nav>
                </aside>

                {/* ── Main ─────────────────────────────────────── */}
                <main className="ml-64 min-h-screen flex flex-col">
                    {/* Top header */}
                    <header className="h-16 bg-[#f9f9fd] flex justify-between items-center px-8 sticky top-0 z-40 border-b border-gray-200/70 shrink-0">
                        <div className="flex items-center gap-5">
                            <span className="text-base font-bold tracking-wider text-[#002046] font-headline">
                                {headerTitle ?? 'Industrial Precision CMMS'}
                            </span>
                            <div className="h-5 w-px bg-gray-300" />
                            <div className="flex items-center gap-1.5 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-200">
                                <MaterialIcon name="wifi" fill className="text-sm text-teal-600" />
                                <span className="text-[10px] font-bold text-teal-700 tracking-tighter uppercase">En línea</span>
                            </div>
                        </div>

                        <div className="flex items-center gap-5">
                            <div className="flex items-center gap-1">
                                <button className="p-1.5 rounded hover:bg-gray-100 transition-colors">
                                    <MaterialIcon name="notifications" className="text-gray-400 text-xl" />
                                </button>
                                <button className="p-1.5 rounded hover:bg-gray-100 transition-colors">
                                    <MaterialIcon name="search" className="text-gray-400 text-xl" />
                                </button>
                            </div>
                            <div className="flex items-center gap-3 pl-4 border-l border-gray-200">
                                <div className="text-right">
                                    <p className="text-xs font-bold text-[#002046]">{user?.name ?? 'Usuario'}</p>
                                    <p className="text-[10px] text-gray-400 uppercase tracking-widest">
                                        {roleLabel[role] ?? role}
                                    </p>
                                </div>
                                <div className="w-8 h-8 rounded-lg bg-[#1b365d] flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {getInitials(user?.name ?? 'U')}
                                </div>
                            </div>
                        </div>
                    </header>

                    {/* Page content */}
                    <div className="flex-1 pb-10">{children}</div>
                </main>

                {/* ── Status bar ───────────────────────────────── */}
                <footer className="fixed bottom-0 left-64 right-0 h-7 bg-[#e7e8eb] px-8 flex items-center justify-between z-30 border-t border-gray-300/50">
                    <div className="flex items-center gap-4 text-[9px] font-bold text-gray-500 uppercase tracking-widest">
                        <span className="flex items-center gap-1">
                            <span className="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse" />
                            Sistema Sincronizado
                        </span>
                        <span className="text-gray-300">|</span>
                        <span>Latencia: 12ms</span>
                    </div>
                    <div className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                        v1.0.0-BETA
                    </div>
                </footer>
            </div>
        </>
    );
}
