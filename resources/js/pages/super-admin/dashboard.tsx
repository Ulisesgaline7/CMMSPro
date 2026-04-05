import { Link } from '@inertiajs/react';
import SuperAdminLayout from '@/layouts/super-admin-layout';
import { cn } from '@/lib/utils';

// ─── Types ────────────────────────────────────────────────────────────────────

interface TenantRow {
    id: number;
    name: string;
    slug: string;
    plan: string;
    plan_label: string;
    status: string;
    status_label: string;
    users_count: number;
    assets_count: number;
    work_orders_count: number;
    max_users: number;
    max_assets: number;
    trial_ends_at: string | null;
    created_at: string;
    billing_email: string | null;
    subscription: {
        status: string;
        status_label: string;
        total_monthly: number;
    } | null;
}

interface AdoptionAlert {
    id: number;
    name: string;
    work_orders_count: number;
    assets_count: number;
    created_at: string;
}

interface Props {
    mrr: number;
    mrrGrowth: number;
    totalTenants: number;
    activeTenants: number;
    trialTenants: number;
    suspendedTenants: number;
    newTenantsThisMonth: number;
    churnCount: number;
    totalUsers: number;
    pastDueCount: number;
    pastDueRevenue: number;
    planDistribution: Record<string, number>;
    totalWorkOrders: number;
    totalAssets: number;
    recentTenants: TenantRow[];
    adoptionAlerts: AdoptionAlert[];
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function MaterialIcon({ name, className, style }: { name: string; className?: string; style?: React.CSSProperties }) {
    return <span className={cn('material-symbols-outlined select-none', className)} style={style}>{name}</span>;
}

function formatCurrency(n: number) {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(n);
}

// ─── Status/Plan badges ───────────────────────────────────────────────────────

const TENANT_STATUS_COLORS: Record<string, string> = {
    active:    'bg-green-500/10 text-green-400 border-green-500/20',
    trial:     'bg-blue-500/10 text-blue-400 border-blue-500/20',
    inactive:  'bg-gray-500/10 text-gray-400 border-gray-500/20',
    suspended: 'bg-red-500/10 text-red-400 border-red-500/20',
};

const PLAN_COLORS: Record<string, string> = {
    starter:      'bg-slate-500/10 text-slate-300 border-slate-500/20',
    professional: 'bg-purple-500/10 text-purple-300 border-purple-500/20',
    enterprise:   'bg-amber-500/10 text-amber-300 border-amber-500/20',
};

const SUB_STATUS_COLORS: Record<string, string> = {
    active:    'text-green-400',
    trialing:  'text-blue-400',
    past_due:  'text-orange-400',
    canceled:  'text-red-400',
    suspended: 'text-red-400',
    incomplete: 'text-gray-400',
};

// ─── Metric card ──────────────────────────────────────────────────────────────

function MetricCard({ label, value, sub, color, icon, accent, trend }: {
    label: string;
    value: string | number;
    sub?: string;
    color: string;
    icon: string;
    accent: string;
    trend?: { value: number; positive: boolean };
}) {
    return (
        <div className="rounded-2xl p-6 flex flex-col gap-3 relative overflow-hidden"
            style={{ background: 'rgba(255,255,255,0.03)', border: `1px solid ${accent}22` }}>
            <div style={{
                position: 'absolute', top: -20, right: -20,
                width: 100, height: 100,
                background: `radial-gradient(circle, ${accent}18 0%, transparent 70%)`,
                borderRadius: '50%',
            }} />
            <div className="flex items-center justify-between">
                <p className="text-[10px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.4)' }}>{label}</p>
                <div className="w-8 h-8 rounded-lg flex items-center justify-center"
                    style={{ background: `${accent}18`, border: `1px solid ${accent}30` }}>
                    <MaterialIcon name={icon} className="text-sm" style={{ color: accent }} />
                </div>
            </div>
            <p className="text-4xl font-black leading-none" style={{ color, fontVariantNumeric: 'tabular-nums' }}>{value}</p>
            {sub && <p className="text-[11px]" style={{ color: 'rgba(255,255,255,0.3)' }}>{sub}</p>}
            {trend && (
                <div className={cn('flex items-center gap-1 text-[10px] font-bold')}>
                    <MaterialIcon name={trend.positive ? 'trending_up' : 'trending_down'} className="text-sm"
                        style={{ color: trend.positive ? '#22c55e' : '#ef4444' }} />
                    <span style={{ color: trend.positive ? '#22c55e' : '#ef4444' }}>
                        {trend.positive ? '+' : ''}{trend.value}%
                    </span>
                    <span style={{ color: 'rgba(255,255,255,0.2)' }}>vs mes anterior</span>
                </div>
            )}
        </div>
    );
}

// ─── Section card ─────────────────────────────────────────────────────────────

function SectionCard({ title, action, actionHref, children }: {
    title: string;
    action?: string;
    actionHref?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="rounded-2xl overflow-hidden"
            style={{ background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(255,255,255,0.07)' }}>
            <div className="flex items-center justify-between px-6 py-4"
                style={{ borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
                <p className="text-[10px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.35)' }}>{title}</p>
                {action && actionHref && (
                    <Link href={actionHref} className="text-[10px] font-bold uppercase tracking-wider hover:underline"
                        style={{ color: '#7c3aed' }}>{action} →</Link>
                )}
            </div>
            {children}
        </div>
    );
}

// ─── Usage bar ────────────────────────────────────────────────────────────────

function UsageBar({ used, max, color }: { used: number; max: number; color: string }) {
    const pct = max > 0 ? Math.min((used / max) * 100, 100) : 0;
    const isHigh = pct >= 80;
    return (
        <div className="flex items-center gap-2">
            <div className="flex-1 h-1.5 rounded-full overflow-hidden" style={{ background: 'rgba(255,255,255,0.06)' }}>
                <div className="h-full rounded-full transition-all" style={{ width: `${pct}%`, backgroundColor: isHigh ? '#f59e0b' : color }} />
            </div>
            <span className="text-[10px] font-bold shrink-0" style={{ color: 'rgba(255,255,255,0.3)' }}>{used}/{max}</span>
        </div>
    );
}

// ─── Page ─────────────────────────────────────────────────────────────────────

export default function SuperAdminDashboard({
    mrr, mrrGrowth, totalTenants, activeTenants, trialTenants, suspendedTenants,
    newTenantsThisMonth, churnCount, totalUsers, pastDueCount, pastDueRevenue,
    planDistribution, totalWorkOrders, totalAssets, recentTenants, adoptionAlerts,
}: Props) {
    const today = new Date().toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

    return (
        <SuperAdminLayout title="Dashboard" headerTitle="Panel de Negocio">
            <div className="p-8 max-w-[1440px] mx-auto space-y-8">

                {/* ── Hero banner ─────────────────────────────── */}
                <div className="rounded-2xl overflow-hidden relative"
                    style={{ background: 'linear-gradient(135deg, #0a0a1f 0%, #1a0a3f 50%, #0d1a3f 100%)', boxShadow: '0 0 60px rgba(139,92,246,0.15)', minHeight: 160 }}>
                    {/* Grid overlay */}
                    <div className="absolute inset-0 opacity-5"
                        style={{ backgroundImage: 'linear-gradient(rgba(255,255,255,0.2) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.2) 1px, transparent 1px)', backgroundSize: '40px 40px' }} />
                    {/* Glow accents */}
                    <div className="absolute -top-20 -right-20 w-96 h-96 rounded-full opacity-20"
                        style={{ background: 'radial-gradient(circle, #7c3aed 0%, transparent 70%)' }} />
                    <div className="absolute -bottom-20 -left-20 w-64 h-64 rounded-full opacity-10"
                        style={{ background: 'radial-gradient(circle, #4f46e5 0%, transparent 70%)' }} />

                    <div className="relative z-10 p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-3 mb-2">
                                <div className="w-10 h-10 rounded-xl flex items-center justify-center"
                                    style={{ background: 'rgba(124,58,237,0.25)', border: '1px solid rgba(124,58,237,0.4)' }}>
                                    <MaterialIcon name="admin_panel_settings" className="text-xl" style={{ color: '#a78bfa' }} />
                                </div>
                                <div>
                                    <p className="text-[10px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.4)' }}>Super Admin</p>
                                    <h2 className="text-xl font-black leading-none" style={{ color: 'white' }}>Panel de Negocio</h2>
                                </div>
                            </div>
                            <p className="text-sm" style={{ color: 'rgba(255,255,255,0.35)' }}>
                                {activeTenants} clientes activos · {trialTenants} en prueba · {totalUsers} usuarios totales
                            </p>
                        </div>
                        <div className="flex items-center gap-6">
                            <div className="text-center">
                                <p className="text-3xl font-black" style={{ color: '#22c55e', fontVariantNumeric: 'tabular-nums' }}>
                                    {new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(mrr)}
                                </p>
                                <p className="text-[10px] font-bold uppercase tracking-widest mt-1" style={{ color: 'rgba(255,255,255,0.3)' }}>MRR</p>
                            </div>
                            <div className="text-center">
                                <p className="text-3xl font-black" style={{ color: '#a78bfa', fontVariantNumeric: 'tabular-nums' }}>{totalTenants}</p>
                                <p className="text-[10px] font-bold uppercase tracking-widest mt-1" style={{ color: 'rgba(255,255,255,0.3)' }}>Clientes</p>
                            </div>
                            <div className="text-center">
                                <p className="text-3xl font-black" style={{ color: '#60a5fa', fontVariantNumeric: 'tabular-nums' }}>{newTenantsThisMonth}</p>
                                <p className="text-[10px] font-bold uppercase tracking-widest mt-1" style={{ color: 'rgba(255,255,255,0.3)' }}>Nuevos / mes</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* ── Header ─────────────────────────────────── */}
                <div className="flex items-end justify-between">
                    <div>
                        <h2 className="text-3xl font-black leading-none mb-1" style={{ color: 'white' }}>
                            Panel de Negocio
                        </h2>
                        <p className="text-sm capitalize" style={{ color: 'rgba(255,255,255,0.3)' }}>{today}</p>
                    </div>
                    <Link href="/super-admin/tenants"
                        className="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:opacity-90"
                        style={{ background: 'linear-gradient(135deg, #7c3aed, #4f46e5)' }}>
                        <MaterialIcon name="add" className="text-lg" />
                        Nuevo Cliente
                    </Link>
                </div>

                {/* ── KPI grid ───────────────────────────────── */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <MetricCard
                        label="MRR"
                        value={formatCurrency(mrr)}
                        sub="Ingreso mensual recurrente"
                        color="#22c55e"
                        icon="payments"
                        accent="#22c55e"
                        trend={{ value: mrrGrowth, positive: mrrGrowth >= 0 }}
                    />
                    <MetricCard
                        label="Clientes Activos"
                        value={activeTenants}
                        sub={`${totalTenants} total · ${trialTenants} en trial`}
                        color="#a78bfa"
                        icon="corporate_fare"
                        accent="#a78bfa"
                    />
                    <MetricCard
                        label="Usuarios"
                        value={totalUsers}
                        sub="En toda la plataforma"
                        color="#38bdf8"
                        icon="group"
                        accent="#38bdf8"
                    />
                    <MetricCard
                        label="Pagos Vencidos"
                        value={pastDueCount}
                        sub={pastDueCount > 0 ? `${formatCurrency(pastDueRevenue)} en riesgo` : 'Todo al corriente'}
                        color={pastDueCount > 0 ? '#f59e0b' : '#22c55e'}
                        icon={pastDueCount > 0 ? 'warning' : 'check_circle'}
                        accent={pastDueCount > 0 ? '#f59e0b' : '#22c55e'}
                    />
                </div>

                {/* ── Secondary metrics ───────────────────────── */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {[
                        { label: 'Nuevos este mes', value: newTenantsThisMonth, icon: 'person_add', color: '#22c55e' },
                        { label: 'Churn este mes',  value: churnCount,          icon: 'person_remove', color: churnCount > 0 ? '#ef4444' : '#22c55e' },
                        { label: 'OTs en plataforma', value: totalWorkOrders.toLocaleString(), icon: 'handyman', color: '#38bdf8' },
                        { label: 'Activos registrados', value: totalAssets.toLocaleString(), icon: 'factory', color: '#a78bfa' },
                    ].map((m) => (
                        <div key={m.label} className="rounded-xl p-5 flex items-center gap-4"
                            style={{ background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(255,255,255,0.07)' }}>
                            <div className="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                style={{ background: `${m.color}18`, border: `1px solid ${m.color}30` }}>
                                <MaterialIcon name={m.icon} className="text-xl" style={{ color: m.color }} />
                            </div>
                            <div>
                                <p className="text-2xl font-black leading-none" style={{ color: m.color }}>{m.value}</p>
                                <p className="text-[10px] font-semibold uppercase tracking-wider mt-0.5" style={{ color: 'rgba(255,255,255,0.3)' }}>{m.label}</p>
                            </div>
                        </div>
                    ))}
                </div>

                {/* ── Plan distribution + adoption alerts ──────── */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Plan distribution */}
                    <SectionCard title="Distribución de Planes">
                        <div className="p-6 space-y-4">
                            {Object.entries(planDistribution).length === 0 ? (
                                <p className="text-center py-4 text-sm" style={{ color: 'rgba(255,255,255,0.3)' }}>Sin datos</p>
                            ) : (
                                Object.entries(planDistribution).map(([plan, count]) => {
                                    const total = Object.values(planDistribution).reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? Math.round((count / total) * 100) : 0;
                                    const planColors: Record<string, string> = { starter: '#64748b', professional: '#a78bfa', enterprise: '#f59e0b' };
                                    const color = planColors[plan] ?? '#64748b';
                                    return (
                                        <div key={plan}>
                                            <div className="flex items-center justify-between mb-1.5">
                                                <span className="text-xs font-bold capitalize" style={{ color: 'rgba(255,255,255,0.7)' }}>
                                                    {plan}
                                                </span>
                                                <span className="text-xs font-black" style={{ color }}>{count} ({pct}%)</span>
                                            </div>
                                            <div className="h-2 rounded-full overflow-hidden" style={{ background: 'rgba(255,255,255,0.06)' }}>
                                                <div className="h-full rounded-full" style={{ width: `${pct}%`, backgroundColor: color }} />
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </SectionCard>

                    {/* Adoption alerts */}
                    <SectionCard title="Alertas de Adopción" action="Ver todos" actionHref="/super-admin/tenants">
                        {adoptionAlerts.length === 0 ? (
                            <div className="py-10 text-center">
                                <MaterialIcon name="check_circle" className="text-5xl mb-2" style={{ color: '#22c55e' }} />
                                <p className="text-sm font-semibold" style={{ color: '#22c55e' }}>Todos los clientes activos</p>
                                <p className="text-xs mt-1" style={{ color: 'rgba(255,255,255,0.3)' }}>Ninguno sin actividad</p>
                            </div>
                        ) : (
                            <div className="divide-y" style={{ borderColor: 'rgba(255,255,255,0.05)' }}>
                                {adoptionAlerts.map((alert) => (
                                    <Link key={alert.id} href={`/super-admin/tenants/${alert.id}`}
                                        className="flex items-center gap-3 px-5 py-3.5 transition-all group"
                                        style={{ color: 'white' }}
                                        onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.background = 'rgba(245,158,11,0.05)'; }}
                                        onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.background = 'transparent'; }}>
                                        <div className="w-1.5 h-1.5 rounded-full shrink-0 bg-amber-400" />
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-bold text-white truncate">{alert.name}</p>
                                            <p className="text-[10px]" style={{ color: 'rgba(255,255,255,0.35)' }}>
                                                {alert.assets_count} activos · {alert.work_orders_count} OTs
                                            </p>
                                        </div>
                                        <span className="text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Sin uso
                                        </span>
                                    </Link>
                                ))}
                            </div>
                        )}
                    </SectionCard>

                    {/* Status overview */}
                    <SectionCard title="Estado de Clientes">
                        <div className="p-6 space-y-4">
                            {[
                                { label: 'Activos',    value: activeTenants,    color: '#22c55e', icon: 'check_circle' },
                                { label: 'Trial',      value: trialTenants,     color: '#38bdf8', icon: 'hourglass_top' },
                                { label: 'Suspendidos', value: suspendedTenants, color: '#ef4444', icon: 'block' },
                                { label: 'Nuevos (mes)', value: newTenantsThisMonth, color: '#a78bfa', icon: 'fiber_new' },
                                { label: 'Churn (mes)', value: churnCount,       color: churnCount > 0 ? '#f87171' : '#22c55e', icon: 'logout' },
                            ].map((s) => (
                                <div key={s.label} className="flex items-center gap-3">
                                    <MaterialIcon name={s.icon} className="text-lg" style={{ color: s.color }} />
                                    <span className="text-sm flex-1" style={{ color: 'rgba(255,255,255,0.6)' }}>{s.label}</span>
                                    <span className="text-lg font-black" style={{ color: s.color }}>{s.value}</span>
                                </div>
                            ))}
                        </div>
                    </SectionCard>
                </div>

                {/* ── Tenant table ────────────────────────────── */}
                <SectionCard title="Todos los Clientes" action="Gestionar" actionHref="/super-admin/tenants">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr style={{ borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
                                    {['Cliente', 'Plan', 'Estado', 'Usuarios', 'Activos', 'OTs', 'MRR', 'Suscripción', 'Acciones'].map((h) => (
                                        <th key={h} className="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest"
                                            style={{ color: 'rgba(255,255,255,0.3)' }}>{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {recentTenants.length === 0 ? (
                                    <tr>
                                        <td colSpan={9} className="text-center py-12 text-sm" style={{ color: 'rgba(255,255,255,0.2)' }}>
                                            Sin clientes registrados
                                        </td>
                                    </tr>
                                ) : (
                                    recentTenants.map((tenant) => (
                                        <tr key={tenant.id} style={{ borderBottom: '1px solid rgba(255,255,255,0.04)' }}
                                            className="transition-colors"
                                            onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.background = 'rgba(255,255,255,0.02)'; }}
                                            onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.background = 'transparent'; }}>
                                            <td className="px-5 py-3.5">
                                                <p className="text-sm font-bold text-white">{tenant.name}</p>
                                                <p className="text-[10px]" style={{ color: 'rgba(255,255,255,0.3)' }}>{tenant.slug}</p>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <span className={cn('text-[10px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wide', PLAN_COLORS[tenant.plan])}>
                                                    {tenant.plan_label}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <span className={cn('text-[10px] font-bold px-2 py-0.5 rounded-full border', TENANT_STATUS_COLORS[tenant.status])}>
                                                    {tenant.status_label}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <UsageBar used={tenant.users_count} max={tenant.max_users} color="#a78bfa" />
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <UsageBar used={tenant.assets_count} max={tenant.max_assets} color="#38bdf8" />
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <span className="text-sm font-bold" style={{ color: 'rgba(255,255,255,0.7)' }}>
                                                    {tenant.work_orders_count}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <span className="text-sm font-bold" style={{ color: '#22c55e' }}>
                                                    {tenant.subscription ? formatCurrency(tenant.subscription.total_monthly) : '—'}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                {tenant.subscription ? (
                                                    <span className={cn('text-[11px] font-bold', SUB_STATUS_COLORS[tenant.subscription.status])}>
                                                        {tenant.subscription.status_label}
                                                    </span>
                                                ) : (
                                                    <span className="text-[11px]" style={{ color: 'rgba(255,255,255,0.2)' }}>Sin plan</span>
                                                )}
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-2">
                                                    <Link href={`/super-admin/tenants/${tenant.id}`}
                                                        className="text-[10px] font-bold px-2 py-1 rounded-lg transition-colors hover:opacity-80"
                                                        style={{ background: 'rgba(124,58,237,0.2)', color: '#a78bfa', border: '1px solid rgba(124,58,237,0.3)' }}>
                                                        Ver
                                                    </Link>
                                                    {tenant.status === 'active' && (
                                                        <Link href={`/super-admin/tenants/${tenant.id}/edit`}
                                                            className="text-[10px] font-bold px-2 py-1 rounded-lg transition-colors hover:opacity-80"
                                                            style={{ background: 'rgba(255,255,255,0.05)', color: 'rgba(255,255,255,0.5)', border: '1px solid rgba(255,255,255,0.1)' }}>
                                                            Editar
                                                        </Link>
                                                    )}
                                                    {tenant.status === 'suspended' && (
                                                        <span className="text-[10px] font-bold px-2 py-1 rounded-lg"
                                                            style={{ background: 'rgba(239,68,68,0.1)', color: '#f87171', border: '1px solid rgba(239,68,68,0.2)' }}>
                                                            Suspendido
                                                        </span>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </SectionCard>

                {/* ── Past due warning ────────────────────────── */}
                {pastDueCount > 0 && (
                    <div className="rounded-2xl p-6 flex items-center gap-5"
                        style={{ background: 'rgba(245,158,11,0.06)', border: '1px solid rgba(245,158,11,0.2)' }}>
                        <div className="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                            style={{ background: 'rgba(245,158,11,0.15)', border: '1px solid rgba(245,158,11,0.3)' }}>
                            <MaterialIcon name="payment" className="text-2xl" style={{ color: '#f59e0b' }} />
                        </div>
                        <div className="flex-1">
                            <p className="font-bold text-white">{pastDueCount} cliente{pastDueCount > 1 ? 's' : ''} con pago vencido</p>
                            <p className="text-sm mt-0.5" style={{ color: 'rgba(255,255,255,0.4)' }}>
                                {formatCurrency(pastDueRevenue)} en riesgo de churn. Gestionar antes de la suspensión automática (10 días).
                            </p>
                        </div>
                        <Link href="/super-admin/tenants?billing=past_due"
                            className="px-4 py-2 rounded-lg text-sm font-bold text-white shrink-0 transition-colors hover:opacity-90"
                            style={{ background: 'rgba(245,158,11,0.3)', border: '1px solid rgba(245,158,11,0.4)' }}>
                            Gestionar cobros
                        </Link>
                    </div>
                )}

            </div>
        </SuperAdminLayout>
    );
}
