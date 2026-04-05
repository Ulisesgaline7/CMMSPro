import { Link, usePage } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import CmmsLayout from '@/layouts/cmms-layout';
import { cn } from '@/lib/utils';
import type { Auth } from '@/types';

// ─── Types ────────────────────────────────────────────────────────────────────

interface WoStats {
    total: number;
    pending: number;
    in_progress: number;
    completed_today: number;
    overdue: number;
}

interface AssetStats {
    total: number;
    active: number;
    under_maintenance: number;
    critical: number;
}

interface RecentWorkOrder {
    id: number;
    code: string;
    title: string;
    type: string;
    status: string;
    priority: string;
    due_date: string | null;
    asset: { id: number; name: string; code: string } | null;
    assigned_to: { id: number; name: string } | null;
}

interface UpcomingMaintenance {
    id: number;
    name: string;
    type: string;
    priority: string;
    next_execution_date: string;
    estimated_duration: number | null;
    asset: { id: number; name: string; code: string } | null;
}

interface CriticalAsset {
    id: number;
    name: string;
    code: string;
    status: string;
    criticality: string;
    location: { id: number; name: string } | null;
}

interface LowStockPart {
    id: number;
    name: string;
    part_number: string | null;
    stock_quantity: number;
    min_stock: number;
    unit: string;
}

interface Reliability {
    mtbf: number;
    mttr: number;
    oee: number;
}

interface Props {
    woStats: WoStats;
    woByStatus: Record<string, number>;
    woByType: Record<string, number>;
    woByPriority: Record<string, number>;
    assetStats: AssetStats;
    criticalAssets: CriticalAsset[];
    lowStockParts: LowStockPart[];
    recentWorkOrders: RecentWorkOrder[];
    myWorkOrders: RecentWorkOrder[];
    upcomingMaintenance: UpcomingMaintenance[];
    reliability: Reliability;
    userRole: string;
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function MaterialIcon({ name, className }: { name: string; className?: string }) {
    return <span className={cn('material-symbols-outlined select-none', className)}>{name}</span>;
}

const STATUS_COLORS: Record<string, string> = {
    draft:       'bg-gray-100 text-gray-600 border-gray-200',
    pending:     'bg-yellow-50 text-yellow-700 border-yellow-200',
    in_progress: 'bg-blue-50 text-blue-700 border-blue-200',
    on_hold:     'bg-orange-50 text-orange-700 border-orange-200',
    completed:   'bg-green-50 text-green-700 border-green-200',
    cancelled:   'bg-red-50 text-red-600 border-red-200',
};

const STATUS_LABELS: Record<string, string> = {
    draft:       'Borrador',
    pending:     'Pendiente',
    in_progress: 'En Progreso',
    on_hold:     'En Pausa',
    completed:   'Completada',
    cancelled:   'Cancelada',
};

const TYPE_COLORS: Record<string, string> = {
    preventive: 'bg-blue-100 text-blue-700',
    corrective: 'bg-red-100 text-red-700',
    predictive: 'bg-purple-100 text-purple-700',
};

const TYPE_ABBREV: Record<string, string> = {
    preventive: 'PM',
    corrective: 'CM',
    predictive: 'PdM',
};

const TYPE_LABELS: Record<string, string> = {
    preventive: 'Preventivo',
    corrective: 'Correctivo',
    predictive: 'Predictivo',
};

const PRIORITY_DOT: Record<string, string> = {
    low:      'bg-gray-300',
    medium:   'bg-blue-400',
    high:     'bg-orange-400',
    critical: 'bg-red-500',
};

const PRIORITY_COLOR_HEX: Record<string, string> = {
    low:      '#d1d5db',
    medium:   '#60a5fa',
    high:     '#fb923c',
    critical: '#ef4444',
};

const PRIORITY_LABELS: Record<string, string> = {
    low:      'Baja',
    medium:   'Media',
    high:     'Alta',
    critical: 'Crítica',
};

// ─── Static Hero Banner ───────────────────────────────────────────────────────

const ROLE_LABEL: Record<string, string> = {
    admin:      'Administrador',
    supervisor: 'Supervisor',
    technician: 'Técnico',
    reader:     'Auditor',
    requester:  'Solicitante',
};

function HeroBanner({ woStats, assetStats, reliability, userRole, userName }: {
    woStats: WoStats;
    assetStats: AssetStats;
    reliability: Reliability;
    userRole: string;
    userName: string;
}) {
    const kpis = [
        { label: 'Total OT',        value: woStats.total,           color: 'text-white',        icon: 'handyman' },
        { label: 'Pendientes',      value: woStats.pending,         color: 'text-yellow-300',   icon: 'schedule' },
        { label: 'En Progreso',     value: woStats.in_progress,     color: 'text-blue-300',     icon: 'construction' },
        { label: 'Completadas Hoy', value: woStats.completed_today, color: 'text-green-300',    icon: 'task_alt' },
        { label: 'Vencidas',        value: woStats.overdue,         color: 'text-red-300',      icon: 'warning' },
    ];

    const showReliability = userRole === 'admin' || userRole === 'supervisor' || userRole === 'reader';
    const today = new Date().toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' });

    return (
        <div className="rounded-2xl overflow-hidden shadow-xl relative"
            style={{ background: 'linear-gradient(135deg, #001830 0%, #002046 50%, #003070 100%)' }}>
            {/* Grid overlay */}
            <div className="absolute inset-0 opacity-[0.07]"
                style={{ backgroundImage: 'radial-gradient(circle at 1px 1px, white 1px, transparent 0)', backgroundSize: '28px 28px' }} />
            {/* Orange glow */}
            <div className="absolute bottom-0 right-1/4 w-80 h-80 rounded-full opacity-10 pointer-events-none"
                style={{ background: 'radial-gradient(circle, #e07b30, transparent)', filter: 'blur(80px)' }} />
            {/* Blue glow */}
            <div className="absolute top-0 left-1/3 w-80 h-80 rounded-full opacity-10 pointer-events-none"
                style={{ background: 'radial-gradient(circle, #3b82f6, transparent)', filter: 'blur(80px)' }} />

            <div className="relative px-10 py-8">
                <div className="flex items-start justify-between mb-6">
                    <div>
                        <p className="text-blue-300/60 text-[10px] uppercase tracking-widest font-bold mb-1">
                            CMMS Pro · {ROLE_LABEL[userRole] ?? userRole}
                        </p>
                        <h2 className="text-white font-extrabold text-2xl leading-tight">
                            Bienvenido, <span style={{ color: '#e07b30' }}>{userName}</span>
                        </h2>
                    </div>
                    <div className="flex items-center gap-2 px-3 py-1.5 rounded-full border"
                        style={{ background: 'rgba(34,197,94,0.1)', borderColor: 'rgba(34,197,94,0.25)', color: '#4ade80' }}>
                        <span className="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse" />
                        <span className="text-[10px] font-bold uppercase tracking-widest">En línea · {today}</span>
                    </div>
                </div>

                {(userRole === 'admin' || userRole === 'supervisor' || userRole === 'reader' || userRole === 'technician') && (
                    <div className="grid grid-cols-5 gap-3 mb-4">
                        {kpis.map((kpi) => (
                            <div key={kpi.label} className="rounded-xl px-4 py-3 border border-white/10 bg-white/5">
                                <MaterialIcon name={kpi.icon} className={cn('text-xl mb-2', kpi.color)} />
                                <p className={cn('text-2xl font-extrabold leading-none', kpi.color)}>{kpi.value}</p>
                                <p className="text-white/40 text-[9px] font-bold uppercase tracking-widest mt-1">{kpi.label}</p>
                            </div>
                        ))}
                    </div>
                )}

                {showReliability && (
                    <div className="grid grid-cols-3 gap-3">
                        {[
                            { label: 'MTBF', value: reliability.mtbf, unit: 'h', color: '#4ade80' },
                            { label: 'MTTR', value: reliability.mttr, unit: 'h', color: '#fbbf24' },
                            { label: 'OEE',  value: `${reliability.oee}%`, unit: '', color: '#60a5fa' },
                        ].map((r) => (
                            <div key={r.label} className="rounded-xl px-5 py-3 border border-white/10 bg-white/5 flex items-center gap-3">
                                <div className="w-8 h-8 rounded-lg flex items-center justify-center border border-white/10"
                                    style={{ background: `${r.color}18` }}>
                                    <MaterialIcon name="analytics" className="text-sm" style={{ color: r.color } as React.CSSProperties} />
                                </div>
                                <div>
                                    <p className="text-white/40 text-[9px] font-bold uppercase tracking-widest">{r.label}</p>
                                    <p className="text-xl font-extrabold leading-none" style={{ color: r.color }}>
                                        {r.value}{r.unit && <span className="text-xs ml-0.5 opacity-60">{r.unit}</span>}
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {userRole === 'requester' && (
                    <div className="flex items-center gap-4 mt-2">
                        <div className="rounded-xl px-5 py-3 border border-white/10 bg-white/5 flex items-center gap-3">
                            <MaterialIcon name="support_agent" className="text-2xl text-purple-300" />
                            <div>
                                <p className="text-white/40 text-[9px] font-bold uppercase tracking-widest">Portal de Solicitudes</p>
                                <p className="text-white font-bold text-sm">Reporta fallos y sigue su estado</p>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

// ─── Maintenance Calendar ─────────────────────────────────────────────────────

const MONTH_NAMES_ES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const DAY_NAMES_ES   = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

interface CalEvent { id: number; title: string; type: string; }

function MaintenanceCalendar({ maintenancePlans, workOrders }: {
    maintenancePlans: UpcomingMaintenance[];
    workOrders: RecentWorkOrder[];
}) {
    const [cur, setCur] = useState(() => {
        const d = new Date();
        return { year: d.getFullYear(), month: d.getMonth() };
    });

    const eventMap = useMemo<Record<number, CalEvent[]>>(() => {
        const map: Record<number, CalEvent[]> = {};
        maintenancePlans.forEach((plan) => {
            const d = new Date(plan.next_execution_date);
            if (d.getFullYear() === cur.year && d.getMonth() === cur.month) {
                const day = d.getDate();
                map[day] = map[day] ?? [];
                map[day].push({ id: plan.id, title: plan.name, type: plan.type });
            }
        });
        workOrders.forEach((wo) => {
            if (!wo.due_date) { return; }
            const d = new Date(wo.due_date);
            if (d.getFullYear() === cur.year && d.getMonth() === cur.month) {
                const day = d.getDate();
                map[day] = map[day] ?? [];
                map[day].push({ id: wo.id, title: wo.title, type: wo.type });
            }
        });
        return map;
    }, [cur, maintenancePlans, workOrders]);

    const firstDow      = new Date(cur.year, cur.month, 1).getDay();
    const daysInMonth   = new Date(cur.year, cur.month + 1, 0).getDate();
    const now           = new Date();
    const isNowMonth    = now.getFullYear() === cur.year && now.getMonth() === cur.month;
    const todayDay      = now.getDate();

    const prevMonth = () => setCur((c) => c.month === 0 ? { year: c.year - 1, month: 11 } : { ...c, month: c.month - 1 });
    const nextMonth = () => setCur((c) => c.month === 11 ? { year: c.year + 1, month: 0 } : { ...c, month: c.month + 1 });

    const EVENT_PILL: Record<string, string> = {
        preventive: 'bg-blue-100 text-blue-700',
        corrective:  'bg-red-100 text-red-700',
        predictive:  'bg-purple-100 text-purple-700',
    };

    const cells: (number | null)[] = [
        ...Array.from({ length: firstDow }, () => null),
        ...Array.from({ length: daysInMonth }, (_, i) => i + 1),
    ];

    return (
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <div className="flex items-center gap-2">
                    <MaterialIcon name="calendar_month" className="text-[#002046] text-base" />
                    <p className="text-xs font-bold uppercase tracking-widest text-gray-400">Calendario de Mantenimientos</p>
                </div>
                <div className="flex items-center gap-3">
                    <span className="text-sm font-bold text-[#002046]">{MONTH_NAMES_ES[cur.month]} {cur.year}</span>
                    <div className="flex items-center">
                        <button onClick={prevMonth} className="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-100 transition-colors">
                            <MaterialIcon name="chevron_left" className="text-gray-400 text-base" />
                        </button>
                        <button onClick={nextMonth} className="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-100 transition-colors">
                            <MaterialIcon name="chevron_right" className="text-gray-400 text-base" />
                        </button>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-7 border-b border-gray-50">
                {DAY_NAMES_ES.map((d) => (
                    <div key={d} className="py-2 text-center text-[10px] font-bold uppercase tracking-widest text-gray-400">{d}</div>
                ))}
            </div>

            <div className="grid grid-cols-7">
                {cells.map((day, i) => {
                    const isToday = isNowMonth && day === todayDay;
                    const evs     = day ? (eventMap[day] ?? []) : [];
                    return (
                        <div key={i} className={cn('min-h-[76px] p-1.5 border-b border-r border-gray-50', !day && 'bg-gray-50/40')}>
                            {day && (
                                <>
                                    <div className={cn('w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold mb-1 mx-auto',
                                        isToday ? 'bg-[#002046] text-white' : 'text-gray-500')}>
                                        {day}
                                    </div>
                                    <div className="flex flex-col gap-0.5">
                                        {evs.slice(0, 2).map((ev) => (
                                            <div key={ev.id} className={cn('text-[9px] font-semibold px-1 py-0.5 rounded truncate', EVENT_PILL[ev.type] ?? 'bg-gray-100 text-gray-600')}>
                                                {ev.title}
                                            </div>
                                        ))}
                                        {evs.length > 2 && (
                                            <span className="text-[9px] font-bold text-gray-400 pl-0.5">+{evs.length - 2} más</span>
                                        )}
                                    </div>
                                </>
                            )}
                        </div>
                    );
                })}
            </div>

            <div className="px-5 py-3 border-t border-gray-50 flex items-center gap-5 flex-wrap">
                {[
                    { label: 'Preventivo',       bg: 'bg-blue-400' },
                    { label: 'Correctivo',        bg: 'bg-red-400' },
                    { label: 'Predictivo',        bg: 'bg-purple-400' },
                    { label: 'Vencimiento OT',    bg: 'bg-gray-300' },
                ].map((item) => (
                    <div key={item.label} className="flex items-center gap-1.5">
                        <span className={cn('w-2 h-2 rounded-full', item.bg)} />
                        <span className="text-[10px] text-gray-400">{item.label}</span>
                    </div>
                ))}
            </div>
        </div>
    );
}

// ─── Shared Components ────────────────────────────────────────────────────────

function WorkOrderRow({ wo }: { wo: RecentWorkOrder }) {
    return (
        <Link
            href={`/work-orders/${wo.id}`}
            className="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50/70 transition-colors group"
        >
            <span className={cn('text-[10px] font-bold px-1.5 py-0.5 rounded shrink-0', TYPE_COLORS[wo.type])}>
                {TYPE_ABBREV[wo.type] ?? wo.type}
            </span>
            <div className="flex-1 min-w-0">
                <p className="text-xs font-mono font-bold text-[#002046]">{wo.code}</p>
                <p className="text-sm text-gray-700 font-medium truncate">{wo.title}</p>
            </div>
            {wo.asset && (
                <p className="text-[10px] text-gray-400 truncate max-w-[100px] hidden md:block">{wo.asset.name}</p>
            )}
            <div className={cn('w-2 h-2 rounded-full shrink-0', PRIORITY_DOT[wo.priority])} />
            <span className={cn('text-[10px] font-bold px-2 py-0.5 rounded-full border shrink-0', STATUS_COLORS[wo.status])}>
                {STATUS_LABELS[wo.status]}
            </span>
            <MaterialIcon name="chevron_right" className="text-gray-300 text-base opacity-0 group-hover:opacity-100 transition-opacity" />
        </Link>
    );
}

function SectionCard({ title, action, actionHref, children }: {
    title: string;
    action?: string;
    actionHref?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <p className="text-xs font-bold uppercase tracking-widest text-gray-400">{title}</p>
                {action && actionHref && (
                    <Link href={actionHref} className="text-xs font-semibold text-[#002046] hover:underline">{action}</Link>
                )}
            </div>
            {children}
        </div>
    );
}

// ─── Reliability Ring ─────────────────────────────────────────────────────────

function ReliabilityRing({ value, max, color, label, unit }: {
    value: number;
    max: number;
    color: string;
    label: string;
    unit: string;
}) {
    const r = 42;
    const circ = 2 * Math.PI * r;
    const pct = Math.min(value / Math.max(max, 1), 1);
    const dash = pct * circ;

    return (
        <div className="flex flex-col items-center gap-2">
            <svg width={100} height={100} viewBox="0 0 100 100">
                <circle cx={50} cy={50} r={r} fill="none" stroke="#f1f5f9" strokeWidth={10} />
                <circle
                    cx={50} cy={50} r={r} fill="none"
                    stroke={color} strokeWidth={10}
                    strokeDasharray={`${dash} ${circ - dash}`}
                    strokeLinecap="round"
                    transform="rotate(-90 50 50)"
                    style={{ transition: 'stroke-dasharray 1s ease' }}
                />
                <text x={50} y={46} textAnchor="middle" fontSize={14} fontWeight={800} fill="#002046">{value}</text>
                <text x={50} y={60} textAnchor="middle" fontSize={9} fill="#94a3b8" fontWeight={600}>{unit}</text>
            </svg>
            <p className="text-[10px] font-bold uppercase tracking-widest text-gray-400">{label}</p>
        </div>
    );
}

// ─── Donut Chart ──────────────────────────────────────────────────────────────

const DONUT_PALETTE: Record<string, string> = {
    pending:     '#eab308',
    in_progress: '#3b82f6',
    on_hold:     '#f97316',
    completed:   '#22c55e',
    cancelled:   '#ef4444',
    draft:       '#9ca3af',
};

function DonutChart({ data, total }: { data: Record<string, number>; total: number }) {
    const r = 54;
    const cx = 64;
    const cy = 64;
    const circumference = 2 * Math.PI * r;
    let offset = 0;
    const slices: { key: string; value: number; dashOffset: number; dashArray: string; color: string }[] = [];
    Object.entries(data).forEach(([key, value]) => {
        const pct = total > 0 ? value / total : 0;
        const dash = pct * circumference;
        slices.push({ key, value, dashOffset: -offset, dashArray: `${dash} ${circumference - dash}`, color: DONUT_PALETTE[key] ?? '#d1d5db' });
        offset += dash;
    });
    return (
        <div className="flex items-center gap-6">
            <svg width="128" height="128" viewBox="0 0 128 128" className="shrink-0">
                {total === 0 ? (
                    <circle cx={cx} cy={cy} r={r} fill="none" stroke="#e5e7eb" strokeWidth="14" />
                ) : (
                    slices.map((s) => (
                        <circle key={s.key} cx={cx} cy={cy} r={r} fill="none"
                            stroke={s.color} strokeWidth="14"
                            strokeDasharray={s.dashArray} strokeDashoffset={s.dashOffset}
                            transform={`rotate(-90 ${cx} ${cy})`} />
                    ))
                )}
                <text x={cx} y={cy - 6} textAnchor="middle" fontSize="20" fontWeight="800" fill="#002046">{total}</text>
                <text x={cx} y={cy + 12} textAnchor="middle" fontSize="9" fill="#9ca3af" fontWeight="600">TOTAL</text>
            </svg>
            <div className="flex flex-col gap-1.5">
                {slices.map((s) => (
                    <div key={s.key} className="flex items-center gap-2">
                        <span className="w-2.5 h-2.5 rounded-full shrink-0" style={{ backgroundColor: s.color }} />
                        <span className="text-[11px] text-gray-500">{STATUS_LABELS[s.key] ?? s.key}</span>
                        <span className="text-[11px] font-bold text-gray-700 ml-auto pl-3">{s.value}</span>
                    </div>
                ))}
            </div>
        </div>
    );
}

// ─── Kanban Column ────────────────────────────────────────────────────────────

function KanbanColumn({ label, count, color, bgColor, workOrders, status }: {
    label: string;
    count: number;
    color: string;
    bgColor: string;
    workOrders: RecentWorkOrder[];
    status: string;
}) {
    const filtered = workOrders.filter((wo) => wo.status === status).slice(0, 5);
    return (
        <div className="flex flex-col gap-2 min-w-0">
            <div className={cn('flex items-center gap-2 px-3 py-2 rounded-lg', bgColor)}>
                <span className={cn('w-2 h-2 rounded-full', color.replace('text-', 'bg-'))} />
                <span className={cn('text-xs font-bold', color)}>{label}</span>
                <span className={cn('ml-auto text-xs font-extrabold', color)}>{count}</span>
            </div>
            <div className="flex flex-col gap-2">
                {filtered.length === 0 ? (
                    <p className="text-[11px] text-gray-400 text-center py-4">Sin órdenes</p>
                ) : (
                    filtered.map((wo) => (
                        <Link key={wo.id} href={`/work-orders/${wo.id}`}
                            className="bg-white border border-gray-100 rounded-lg p-3 hover:border-gray-200 hover:shadow-sm transition-all group">
                            <div className="flex items-start justify-between gap-2 mb-1.5">
                                <span className={cn('text-[9px] font-bold px-1 py-0.5 rounded', TYPE_COLORS[wo.type])}>
                                    {TYPE_ABBREV[wo.type] ?? wo.type}
                                </span>
                                <div className={cn('w-1.5 h-1.5 rounded-full mt-0.5 shrink-0', PRIORITY_DOT[wo.priority])} />
                            </div>
                            <p className="text-xs font-mono text-gray-400">{wo.code}</p>
                            <p className="text-xs font-semibold text-gray-700 truncate mt-0.5">{wo.title}</p>
                            {wo.asset && <p className="text-[10px] text-gray-400 truncate mt-0.5">{wo.asset.name}</p>}
                        </Link>
                    ))
                )}
            </div>
        </div>
    );
}

// ─── Admin Dashboard ──────────────────────────────────────────────────────────

function AdminDashboard({ woByStatus, woByType, woByPriority, assetStats, reliability, criticalAssets, lowStockParts, recentWorkOrders, upcomingMaintenance, woStats }: Omit<Props, 'myWorkOrders' | 'userRole'>) {

    const max = Math.max(...Object.values(woByType), 1);
    const typePalette: Record<string, string> = { preventive: '#3b82f6', corrective: '#ef4444', predictive: '#a855f7' };
    const priorityOrder = ['critical', 'high', 'medium', 'low'];
    const maxPriority = Math.max(...Object.values(woByPriority), 1);

    return (
        <div className="space-y-6">
            {/* Reliability */}
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">Indicadores de Confiabilidad</p>
                <div className="flex items-center justify-around">
                    <ReliabilityRing value={reliability.mtbf} max={reliability.mtbf * 1.5 || 500} color="#22c55e" label="MTBF" unit="horas" />
                    <div className="h-20 w-px bg-gray-100" />
                    <ReliabilityRing value={reliability.mttr} max={Math.max(reliability.mttr * 2, 10)} color="#f59e0b" label="MTTR" unit="horas" />
                    <div className="h-20 w-px bg-gray-100" />
                    <ReliabilityRing value={reliability.oee} max={100} color="#3b82f6" label="OEE" unit="%" />
                    <div className="h-20 w-px bg-gray-100" />
                    <div className="flex flex-col items-center gap-1">
                        <p className="text-3xl font-extrabold text-green-600 font-headline">{assetStats.active}</p>
                        <p className="text-[10px] font-bold uppercase tracking-widest text-gray-400">Activos Op.</p>
                        <p className="text-xs text-gray-400">de {assetStats.total} total</p>
                    </div>
                    <div className="h-20 w-px bg-gray-100" />
                    <div className="flex flex-col items-center gap-1">
                        <p className="text-3xl font-extrabold text-red-500 font-headline">{assetStats.critical}</p>
                        <p className="text-[10px] font-bold uppercase tracking-widest text-gray-400">Críticos</p>
                        <p className="text-xs text-gray-400">requieren atención</p>
                    </div>
                </div>
            </div>

            {/* Charts */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">OT por Estado</p>
                    <DonutChart data={woByStatus} total={woStats.total} />
                </div>
                <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">OT por Tipo</p>
                    <div className="flex flex-col gap-3">
                        {Object.entries(woByType).map(([type, count]) => (
                            <div key={type} className="flex items-center gap-3">
                                <span className="text-[11px] font-bold text-gray-500 w-8">{TYPE_ABBREV[type] ?? type}</span>
                                <div className="flex-1 h-5 bg-gray-100 rounded-full overflow-hidden">
                                    <div className="h-full rounded-full transition-all duration-700"
                                        style={{ width: `${(count / max) * 100}%`, backgroundColor: typePalette[type] ?? '#9ca3af' }} />
                                </div>
                                <span className="text-[11px] font-extrabold text-gray-700 w-5 text-right">{count}</span>
                            </div>
                        ))}
                    </div>
                </div>
                <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">OT por Prioridad</p>
                    <div className="flex flex-col gap-3">
                        {priorityOrder.map((p) => {
                            const count = woByPriority[p] ?? 0;
                            return (
                                <div key={p} className="flex items-center gap-3">
                                    <div className={cn('w-2 h-2 rounded-full shrink-0', PRIORITY_DOT[p])} />
                                    <span className="text-[11px] text-gray-500 w-12">{PRIORITY_LABELS[p]}</span>
                                    <div className="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div className="h-full rounded-full"
                                            style={{ width: `${(count / maxPriority) * 100}%`, backgroundColor: count > 0 ? (PRIORITY_COLOR_HEX[p] ?? '#d1d5db') : 'transparent' }}
                                            data-priority={p} />
                                    </div>
                                    <span className="text-[11px] font-bold text-gray-600 w-4 text-right">{count}</span>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>

            {/* Maintenance Calendar */}
            <MaintenanceCalendar maintenancePlans={upcomingMaintenance} workOrders={recentWorkOrders} />

            {/* Recent OT + Right */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2">
                    <SectionCard title="Últimas Órdenes de Trabajo" action="Ver todas" actionHref="/work-orders">
                        {recentWorkOrders.length === 0 ? (
                            <div className="py-12 text-center text-gray-400 text-sm">Sin órdenes recientes</div>
                        ) : (
                            <div className="divide-y divide-gray-50">
                                {recentWorkOrders.map((wo) => <WorkOrderRow key={wo.id} wo={wo} />)}
                            </div>
                        )}
                    </SectionCard>
                </div>

                <div className="flex flex-col gap-4">
                    <SectionCard title="Próximos Mantenimientos">
                        {upcomingMaintenance.length === 0 ? (
                            <div className="py-8 text-center text-gray-400 text-xs">Sin planes próximos</div>
                        ) : (
                            <div className="divide-y divide-gray-50">
                                {upcomingMaintenance.map((plan) => {
                                    const date = new Date(plan.next_execution_date);
                                    const daysUntil = Math.ceil((date.getTime() - Date.now()) / 86400000);
                                    const urgent = daysUntil <= 3;
                                    return (
                                        <div key={plan.id} className="px-5 py-3 flex items-start gap-3">
                                            <div className={cn('mt-0.5 w-1.5 h-1.5 rounded-full shrink-0', urgent ? 'bg-orange-400' : 'bg-blue-400')} />
                                            <div className="flex-1 min-w-0">
                                                <p className="text-xs font-semibold text-gray-700 truncate">{plan.name}</p>
                                                <p className="text-[10px] text-gray-400 truncate">{plan.asset?.name}</p>
                                            </div>
                                            <div className="text-right shrink-0">
                                                <p className={cn('text-[10px] font-bold', urgent ? 'text-orange-500' : 'text-gray-500')}>
                                                    {daysUntil === 0 ? 'Hoy' : daysUntil === 1 ? 'Mañana' : `${daysUntil}d`}
                                                </p>
                                                <p className="text-[9px] text-gray-300">
                                                    {date.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' })}
                                                </p>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </SectionCard>

                    {lowStockParts.length > 0 && (
                        <div className="bg-white rounded-xl border border-red-100 shadow-sm overflow-hidden">
                            <div className="px-5 py-4 border-b border-red-50 flex items-center gap-2">
                                <MaterialIcon name="warning" className="text-red-400 text-base" />
                                <p className="text-xs font-bold uppercase tracking-widest text-red-400">Stock Bajo</p>
                            </div>
                            <div className="divide-y divide-gray-50">
                                {lowStockParts.map((part) => (
                                    <div key={part.id} className="px-5 py-3 flex items-center gap-3">
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-semibold text-gray-700 truncate">{part.name}</p>
                                            <p className="text-[10px] text-gray-400">{part.part_number}</p>
                                        </div>
                                        <div className="text-right shrink-0">
                                            <p className="text-sm font-extrabold text-red-500">{part.stock_quantity}</p>
                                            <p className="text-[9px] text-gray-400">mín {part.min_stock} {part.unit}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {criticalAssets.length > 0 && (
                        <SectionCard title="Activos Críticos">
                            <div className="divide-y divide-gray-50">
                                {criticalAssets.map((asset) => (
                                    <div key={asset.id} className="px-5 py-3 flex items-center gap-3">
                                        <div className="w-2 h-2 rounded-full bg-red-500 shrink-0" />
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-semibold text-gray-700 truncate">{asset.name}</p>
                                            <p className="text-[10px] text-gray-400">{asset.location?.name}</p>
                                        </div>
                                        <span className="text-[10px] font-mono text-gray-400">{asset.code}</span>
                                    </div>
                                ))}
                            </div>
                        </SectionCard>
                    )}
                </div>
            </div>
        </div>
    );
}

// ─── Supervisor Dashboard ─────────────────────────────────────────────────────

function SupervisorDashboard({ woStats, woByStatus, recentWorkOrders, upcomingMaintenance, reliability }: Pick<Props, 'woStats' | 'woByStatus' | 'recentWorkOrders' | 'upcomingMaintenance' | 'reliability'>) {
    return (
        <div className="space-y-6">
            {/* Today's summary */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {[
                    { label: 'Pendientes', value: woStats.pending, color: 'text-yellow-600', bg: 'bg-yellow-50', icon: 'schedule' },
                    { label: 'En Progreso', value: woStats.in_progress, color: 'text-blue-600', bg: 'bg-blue-50', icon: 'construction' },
                    { label: 'Completadas Hoy', value: woStats.completed_today, color: 'text-green-600', bg: 'bg-green-50', icon: 'task_alt' },
                    { label: 'Vencidas', value: woStats.overdue, color: 'text-red-600', bg: 'bg-red-50', icon: 'warning' },
                ].map((kpi) => (
                    <div key={kpi.label} className={cn('rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4', kpi.bg)}>
                        <MaterialIcon name={kpi.icon} className={cn('text-3xl', kpi.color)} />
                        <div>
                            <p className={cn('text-3xl font-extrabold font-headline leading-none', kpi.color)}>{kpi.value}</p>
                            <p className="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mt-1">{kpi.label}</p>
                        </div>
                    </div>
                ))}
            </div>

            {/* Kanban */}
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div className="flex items-center justify-between mb-5">
                    <p className="text-xs font-bold uppercase tracking-widest text-gray-400">Kanban — Órdenes de Trabajo</p>
                    <Link href="/work-orders" className="text-xs font-semibold text-[#002046] hover:underline">Ver todas →</Link>
                </div>
                <div className="grid grid-cols-3 gap-4">
                    <KanbanColumn label="Pendientes"  count={woStats.pending}     color="text-yellow-600" bgColor="bg-yellow-50"  workOrders={recentWorkOrders} status="pending" />
                    <KanbanColumn label="En Progreso" count={woStats.in_progress} color="text-blue-600"   bgColor="bg-blue-50"    workOrders={recentWorkOrders} status="in_progress" />
                    <KanbanColumn label="Completadas" count={woByStatus['completed'] ?? 0} color="text-green-600" bgColor="bg-green-50" workOrders={recentWorkOrders} status="completed" />
                </div>
            </div>

            {/* Reliability + Upcoming */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">Confiabilidad</p>
                    <div className="flex items-center justify-around">
                        <ReliabilityRing value={reliability.mtbf} max={reliability.mtbf * 1.5 || 500} color="#22c55e" label="MTBF" unit="h" />
                        <ReliabilityRing value={reliability.mttr} max={Math.max(reliability.mttr * 2, 10)} color="#f59e0b" label="MTTR" unit="h" />
                        <ReliabilityRing value={reliability.oee}  max={100} color="#3b82f6" label="OEE" unit="%" />
                    </div>
                </div>
                <SectionCard title="Próximos Mantenimientos">
                    {upcomingMaintenance.length === 0 ? (
                        <div className="py-8 text-center text-gray-400 text-xs">Sin planes próximos</div>
                    ) : (
                        <div className="divide-y divide-gray-50">
                            {upcomingMaintenance.map((plan) => {
                                const date = new Date(plan.next_execution_date);
                                const daysUntil = Math.ceil((date.getTime() - Date.now()) / 86400000);
                                return (
                                    <div key={plan.id} className="px-5 py-3 flex items-center justify-between gap-3">
                                        <div className="min-w-0">
                                            <p className="text-xs font-semibold text-gray-700 truncate">{plan.name}</p>
                                            <p className="text-[10px] text-gray-400 truncate">{plan.asset?.name}</p>
                                        </div>
                                        <span className={cn('text-[11px] font-bold shrink-0', daysUntil <= 3 ? 'text-orange-500' : 'text-gray-400')}>
                                            {daysUntil === 0 ? 'Hoy' : daysUntil === 1 ? 'Mañana' : `${daysUntil}d`}
                                        </span>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </SectionCard>
            </div>
        </div>
    );
}

// ─── Technician Dashboard ─────────────────────────────────────────────────────

function TechnicianDashboard({ myWorkOrders, woStats }: Pick<Props, 'myWorkOrders' | 'woStats'>) {
    const inProgress = myWorkOrders.filter((wo) => wo.status === 'in_progress');
    const pending = myWorkOrders.filter((wo) => wo.status === 'pending');

    return (
        <div className="space-y-6">
            <div className="grid grid-cols-3 gap-4">
                {[
                    { label: 'Mis Pendientes', value: pending.length, color: 'text-yellow-600', bg: 'bg-yellow-50/60 border-yellow-100', icon: 'schedule' },
                    { label: 'En Ejecución', value: inProgress.length, color: 'text-blue-600', bg: 'bg-blue-50/60 border-blue-100', icon: 'construction' },
                    { label: 'Completadas Hoy', value: woStats.completed_today, color: 'text-green-600', bg: 'bg-green-50/60 border-green-100', icon: 'task_alt' },
                ].map((k) => (
                    <div key={k.label} className={cn('rounded-xl border shadow-sm p-5 flex items-center gap-4', k.bg)}>
                        <MaterialIcon name={k.icon} className={cn('text-3xl', k.color)} />
                        <div>
                            <p className={cn('text-3xl font-extrabold font-headline leading-none', k.color)}>{k.value}</p>
                            <p className="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mt-1">{k.label}</p>
                        </div>
                    </div>
                ))}
            </div>

            {inProgress.length > 0 && (
                <div className="bg-blue-600 rounded-xl p-5 text-white shadow-lg">
                    <div className="flex items-center gap-2 mb-4">
                        <MaterialIcon name="construction" className="text-xl" />
                        <p className="text-xs font-bold uppercase tracking-widest opacity-80">En Ejecución Ahora</p>
                    </div>
                    <div className="flex flex-col gap-3">
                        {inProgress.map((wo) => (
                            <Link key={wo.id} href={`/work-orders/${wo.id}`}
                                className="flex items-center gap-3 bg-white/10 hover:bg-white/20 rounded-lg px-4 py-3 transition-colors">
                                <span className="text-[10px] font-bold bg-white/20 px-1.5 py-0.5 rounded">
                                    {TYPE_ABBREV[wo.type] ?? wo.type}
                                </span>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-semibold truncate">{wo.title}</p>
                                    <p className="text-[10px] opacity-70">{wo.asset?.name}</p>
                                </div>
                                <MaterialIcon name="chevron_right" className="text-white/60" />
                            </Link>
                        ))}
                    </div>
                </div>
            )}

            <SectionCard title="Mis Órdenes Asignadas" action="Ver todas" actionHref="/work-orders">
                {myWorkOrders.length === 0 ? (
                    <div className="py-16 text-center">
                        <MaterialIcon name="check_circle" className="text-5xl text-green-300 mb-3" />
                        <p className="text-gray-400 text-sm font-medium">¡Sin órdenes pendientes!</p>
                        <p className="text-gray-300 text-xs mt-1">Todas las tareas están completadas.</p>
                    </div>
                ) : (
                    <div className="divide-y divide-gray-50">
                        {myWorkOrders.map((wo) => {
                            const due = wo.due_date ? new Date(wo.due_date) : null;
                            const overdue = due && due < new Date();
                            return (
                                <Link key={wo.id} href={`/work-orders/${wo.id}`}
                                    className="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-colors group">
                                    <div className={cn('w-1 h-10 rounded-full shrink-0', PRIORITY_DOT[wo.priority])} />
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2 mb-0.5">
                                            <span className={cn('text-[9px] font-bold px-1 py-0.5 rounded', TYPE_COLORS[wo.type])}>
                                                {TYPE_ABBREV[wo.type]}
                                            </span>
                                            <p className="text-xs font-mono text-gray-400">{wo.code}</p>
                                        </div>
                                        <p className="text-sm font-semibold text-gray-700 truncate">{wo.title}</p>
                                        {wo.asset && <p className="text-[10px] text-gray-400 truncate">{wo.asset.name}</p>}
                                    </div>
                                    <div className="text-right shrink-0">
                                        {due && (
                                            <p className={cn('text-[10px] font-bold', overdue ? 'text-red-500' : 'text-gray-400')}>
                                                {overdue ? '⚠ Vencida' : due.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' })}
                                            </p>
                                        )}
                                        <span className={cn('text-[10px] font-bold px-2 py-0.5 rounded-full border', STATUS_COLORS[wo.status])}>
                                            {STATUS_LABELS[wo.status]}
                                        </span>
                                    </div>
                                    <MaterialIcon name="chevron_right" className="text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </Link>
                            );
                        })}
                    </div>
                )}
            </SectionCard>
        </div>
    );
}

// ─── Reader/Auditor Dashboard ─────────────────────────────────────────────────

function AuditorDashboard({ woStats, recentWorkOrders, reliability, assetStats }: Pick<Props, 'woStats' | 'recentWorkOrders' | 'reliability' | 'assetStats'>) {
    return (
        <div className="space-y-6">
            <div className="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 flex items-center gap-3">
                <MaterialIcon name="visibility" className="text-amber-600 text-xl" />
                <p className="text-sm font-semibold text-amber-700">Modo Auditor — Solo lectura. No se pueden realizar cambios.</p>
            </div>

            {/* Summary */}
            <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                {[
                    { label: 'Total OT', value: woStats.total, color: 'text-[#002046]' },
                    { label: 'Pendientes', value: woStats.pending, color: 'text-yellow-600' },
                    { label: 'En Progreso', value: woStats.in_progress, color: 'text-blue-600' },
                    { label: 'Completadas Hoy', value: woStats.completed_today, color: 'text-green-600' },
                    { label: 'Vencidas', value: woStats.overdue, color: 'text-red-600' },
                ].map((k) => (
                    <div key={k.label} className="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
                        <p className={cn('text-2xl font-extrabold font-headline', k.color)}>{k.value}</p>
                        <p className="text-[9px] font-bold uppercase tracking-widest text-gray-400 mt-1">{k.label}</p>
                    </div>
                ))}
            </div>

            {/* Reliability */}
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <p className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">Métricas de Confiabilidad</p>
                <div className="flex items-center justify-around">
                    <ReliabilityRing value={reliability.mtbf} max={reliability.mtbf * 1.5 || 500} color="#22c55e" label="MTBF" unit="horas" />
                    <ReliabilityRing value={reliability.mttr} max={Math.max(reliability.mttr * 2, 10)} color="#f59e0b" label="MTTR" unit="horas" />
                    <ReliabilityRing value={reliability.oee} max={100} color="#3b82f6" label="OEE" unit="%" />
                    <div className="h-16 w-px bg-gray-100" />
                    <div className="text-center">
                        <p className="text-2xl font-extrabold text-[#002046] font-headline">{assetStats.total}</p>
                        <p className="text-[10px] uppercase tracking-widest text-gray-400 mt-1">Activos Totales</p>
                    </div>
                    <div className="text-center">
                        <p className="text-2xl font-extrabold text-green-600 font-headline">{assetStats.active}</p>
                        <p className="text-[10px] uppercase tracking-widest text-gray-400 mt-1">Operativos</p>
                    </div>
                </div>
            </div>

            {/* Read-only OT table */}
            <SectionCard title="Historial de Órdenes de Trabajo" action="Ver todas" actionHref="/work-orders">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-gray-50">
                                <th className="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Código</th>
                                <th className="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Título</th>
                                <th className="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Tipo</th>
                                <th className="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Prioridad</th>
                                <th className="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Estado</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {recentWorkOrders.map((wo) => (
                                <tr key={wo.id} className="hover:bg-gray-50/50">
                                    <td className="px-6 py-3 font-mono text-[11px] text-gray-500">{wo.code}</td>
                                    <td className="px-6 py-3 text-gray-700 font-medium max-w-[200px] truncate">{wo.title}</td>
                                    <td className="px-6 py-3">
                                        <span className={cn('text-[10px] font-bold px-1.5 py-0.5 rounded', TYPE_COLORS[wo.type])}>
                                            {TYPE_LABELS[wo.type] ?? wo.type}
                                        </span>
                                    </td>
                                    <td className="px-6 py-3">
                                        <div className="flex items-center gap-1.5">
                                            <div className={cn('w-1.5 h-1.5 rounded-full', PRIORITY_DOT[wo.priority])} />
                                            <span className="text-[11px] text-gray-500">{PRIORITY_LABELS[wo.priority]}</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-3">
                                        <span className={cn('text-[10px] font-bold px-2 py-0.5 rounded-full border', STATUS_COLORS[wo.status])}>
                                            {STATUS_LABELS[wo.status]}
                                        </span>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </SectionCard>
        </div>
    );
}

// ─── Requester Dashboard ──────────────────────────────────────────────────────

function RequesterDashboard({ recentWorkOrders }: Pick<Props, 'recentWorkOrders'>) {
    return (
        <div className="space-y-6">
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center">
                <MaterialIcon name="build_circle" className="text-6xl text-[#002046] mb-4" />
                <h3 className="text-xl font-bold text-[#002046] mb-2">Reportar una Falla</h3>
                <p className="text-gray-400 text-sm mb-6">Crea una solicitud de servicio y sigue el estado de tu reporte.</p>
                <Link href="/service-requests/create"
                    className="inline-flex items-center gap-2 bg-[#002046] text-white px-6 py-3 rounded-lg font-bold hover:bg-[#1b365d] transition-colors">
                    <MaterialIcon name="add" className="text-xl" />
                    Nueva Solicitud
                </Link>
            </div>

            <SectionCard title="Mis Solicitudes">
                {recentWorkOrders.length === 0 ? (
                    <div className="py-12 text-center text-gray-400 text-sm">Sin solicitudes previas</div>
                ) : (
                    <div className="divide-y divide-gray-50">
                        {recentWorkOrders.map((wo) => <WorkOrderRow key={wo.id} wo={wo} />)}
                    </div>
                )}
            </SectionCard>
        </div>
    );
}

// ─── Page ─────────────────────────────────────────────────────────────────────

export default function Dashboard(props: Props) {
    const {
        woStats, woByStatus, woByType, woByPriority,
        assetStats, criticalAssets, lowStockParts,
        recentWorkOrders, myWorkOrders, upcomingMaintenance,
        reliability, userRole,
    } = props;

    const page = usePage();
    const auth = page.props.auth as Auth;
    const user = auth.user;

    const today = new Date().toLocaleDateString('es-MX', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    });

    const roleLabel: Record<string, string> = {
        admin: 'Administrador',
        supervisor: 'Supervisor',
        technician: 'Técnico',
        reader: 'Auditor',
        requester: 'Solicitante',
    };

    return (
        <CmmsLayout title="Dashboard" headerTitle="Panel de Control">
            <div className="p-8 max-w-[1440px] mx-auto space-y-8">

                {/* ── Hero banner ─────────────────────────────────────── */}
                <HeroBanner
                    woStats={woStats}
                    assetStats={assetStats}
                    reliability={reliability}
                    userRole={userRole}
                    userName={user?.name ?? 'Usuario'}
                />

                {/* ── Page header ─────────────────────────────────────── */}
                <div className="flex items-end justify-between">
                    <div>
                        <div className="flex items-center gap-3 mb-1">
                            <h2 className="text-3xl font-extrabold text-[#002046] font-headline tracking-tight">
                                {userRole === 'technician' ? 'Mis Órdenes' : userRole === 'requester' ? 'Portal de Solicitudes' : 'Panel de Control'}
                            </h2>
                            <span className="text-[10px] font-bold px-2.5 py-1 rounded-full bg-[#002046]/10 text-[#002046] uppercase tracking-widest">
                                {roleLabel[userRole] ?? userRole}
                            </span>
                        </div>
                        <p className="text-sm text-gray-400 capitalize">{today}</p>
                    </div>
                    {(userRole === 'admin' || userRole === 'supervisor') && (
                        <div className="flex items-center gap-3">
                            <Link href="/work-orders/create"
                                className="flex items-center gap-2 bg-white border border-gray-200 text-[#002046] px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-50 transition-colors shadow-sm">
                                <MaterialIcon name="add" className="text-lg" />
                                Nueva OT
                            </Link>
                            <Link href="/work-orders"
                                className="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                                <MaterialIcon name="handyman" className="text-lg" />
                                Ver Órdenes
                            </Link>
                        </div>
                    )}
                </div>

                {/* ── Role-based content ───────────────────────────────── */}
                {userRole === 'admin' && (
                    <AdminDashboard
                        woStats={woStats}
                        woByStatus={woByStatus}
                        woByType={woByType}
                        woByPriority={woByPriority}
                        assetStats={assetStats}
                        reliability={reliability}
                        criticalAssets={criticalAssets}
                        lowStockParts={lowStockParts}
                        recentWorkOrders={recentWorkOrders}
                        upcomingMaintenance={upcomingMaintenance}
                    />
                )}

                {userRole === 'supervisor' && (
                    <SupervisorDashboard
                        woStats={woStats}
                        woByStatus={woByStatus}
                        recentWorkOrders={recentWorkOrders}
                        upcomingMaintenance={upcomingMaintenance}
                        reliability={reliability}
                    />
                )}

                {userRole === 'technician' && (
                    <TechnicianDashboard
                        myWorkOrders={myWorkOrders}
                        woStats={woStats}
                    />
                )}

                {userRole === 'reader' && (
                    <AuditorDashboard
                        woStats={woStats}
                        recentWorkOrders={recentWorkOrders}
                        reliability={reliability}
                        assetStats={assetStats}
                    />
                )}

                {userRole === 'requester' && (
                    <RequesterDashboard recentWorkOrders={recentWorkOrders} />
                )}

            </div>
        </CmmsLayout>
    );
}
