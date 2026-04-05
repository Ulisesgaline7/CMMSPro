import { Link, router } from '@inertiajs/react';
import { useCallback, useState } from 'react';
import CmmsLayout from '@/layouts/cmms-layout';
import { cn } from '@/lib/utils';
import type {
    WorkOrderStats,
    WorkOrderSummary,
    WorkOrderPriority,
    WorkOrderStatus,
    WorkOrderType,
    WORK_ORDER_STATUS_LABELS,
    WORK_ORDER_STATUS_COLORS,
    WORK_ORDER_TYPE_LABELS,
    WORK_ORDER_TYPE_ABBREV,
    WORK_ORDER_PRIORITY_LABELS,
    WORK_ORDER_PRIORITY_COLORS,
} from '@/types';

// ─── Helpers ─────────────────────────────────────────────────────────────────

const STATUS_LABELS: Record<WorkOrderStatus, string> = {
    draft: 'Borrador',
    pending: 'Pendiente',
    in_progress: 'En Progreso',
    on_hold: 'En Pausa',
    completed: 'Completada',
    cancelled: 'Cancelada',
};

const STATUS_COLORS: Record<WorkOrderStatus, string> = {
    draft: 'bg-gray-100 text-gray-600 border-gray-200',
    pending: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    in_progress: 'bg-blue-50 text-blue-700 border-blue-200',
    on_hold: 'bg-orange-50 text-orange-700 border-orange-200',
    completed: 'bg-green-50 text-green-700 border-green-200',
    cancelled: 'bg-red-50 text-red-600 border-red-200',
};

const TYPE_COLORS: Record<WorkOrderType, string> = {
    preventive: 'bg-blue-100 text-blue-700',
    corrective: 'bg-red-100 text-red-700',
    predictive: 'bg-purple-100 text-purple-700',
};

const TYPE_ABBREV: Record<WorkOrderType, string> = {
    preventive: 'PM',
    corrective: 'CM',
    predictive: 'PdM',
};

const PRIORITY_COLORS: Record<WorkOrderPriority, string> = {
    low: 'text-gray-400',
    medium: 'text-blue-500',
    high: 'text-orange-500',
    critical: 'text-red-600',
};

const PRIORITY_LABELS: Record<WorkOrderPriority, string> = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    critical: 'Crítica',
};

// ─── Sub-components ───────────────────────────────────────────────────────────

function MaterialIcon({ name, className }: { name: string; className?: string }) {
    return (
        <span className={cn('material-symbols-outlined select-none', className)}>{name}</span>
    );
}

function StatCard({
    label,
    value,
    icon,
    accent,
}: {
    label: string;
    value: number;
    icon: string;
    accent: string;
}) {
    return (
        <div className="bg-white rounded-lg p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div className={cn('w-11 h-11 rounded-lg flex items-center justify-center', accent)}>
                <MaterialIcon name={icon} className="text-xl text-white" />
            </div>
            <div>
                <p className="text-2xl font-extrabold text-[#002046] font-headline">{value}</p>
                <p className="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">
                    {label}
                </p>
            </div>
        </div>
    );
}

// ─── Page ────────────────────────────────────────────────────────────────────

interface PaginatedWorkOrders {
    data: WorkOrderSummary[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Props {
    workOrders: PaginatedWorkOrders;
    filters: {
        search?: string;
        status?: WorkOrderStatus;
        type?: WorkOrderType;
        priority?: WorkOrderPriority;
    };
    stats: WorkOrderStats;
}

export default function WorkOrdersIndex({ workOrders, filters, stats }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');

    const applyFilter = useCallback(
        (key: string, value: string) => {
            router.get(
                '/work-orders',
                { ...filters, [key]: value || undefined },
                { preserveState: true, replace: true },
            );
        },
        [filters],
    );

    const handleSearch = useCallback(
        (e: React.FormEvent) => {
            e.preventDefault();
            applyFilter('search', search);
        },
        [applyFilter, search],
    );

    const statusOptions: { value: string; label: string }[] = [
        { value: '', label: 'Todos los estados' },
        { value: 'draft', label: 'Borrador' },
        { value: 'pending', label: 'Pendiente' },
        { value: 'in_progress', label: 'En Progreso' },
        { value: 'on_hold', label: 'En Pausa' },
        { value: 'completed', label: 'Completada' },
        { value: 'cancelled', label: 'Cancelada' },
    ];

    const typeOptions = [
        { value: '', label: 'Todos los tipos' },
        { value: 'preventive', label: 'Preventivo' },
        { value: 'corrective', label: 'Correctivo' },
        { value: 'predictive', label: 'Predictivo' },
    ];

    const priorityOptions = [
        { value: '', label: 'Todas las prioridades' },
        { value: 'low', label: 'Baja' },
        { value: 'medium', label: 'Media' },
        { value: 'high', label: 'Alta' },
        { value: 'critical', label: 'Crítica' },
    ];

    return (
        <CmmsLayout title="Órdenes de Trabajo" headerTitle="Órdenes de Trabajo">
            <div className="p-8 max-w-7xl mx-auto space-y-8">
                {/* Page header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-3xl font-extrabold text-[#002046] font-headline tracking-tight">
                            Órdenes de Trabajo
                        </h2>
                        <p className="text-sm text-gray-500 mt-1">
                            {workOrders.total} órdenes registradas
                        </p>
                    </div>
                    <Link
                        href="/work-orders/create"
                        className="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm"
                    >
                        <MaterialIcon name="add" className="text-lg" />
                        Nueva Orden
                    </Link>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <StatCard
                        label="Total"
                        value={stats.total}
                        icon="list_alt"
                        accent="bg-[#002046]"
                    />
                    <StatCard
                        label="Pendientes"
                        value={stats.pending}
                        icon="schedule"
                        accent="bg-yellow-500"
                    />
                    <StatCard
                        label="En Progreso"
                        value={stats.in_progress}
                        icon="construction"
                        accent="bg-blue-600"
                    />
                    <StatCard
                        label="Completadas Hoy"
                        value={stats.completed_today}
                        icon="task_alt"
                        accent="bg-green-600"
                    />
                    <StatCard
                        label="Vencidas"
                        value={stats.overdue}
                        icon="warning"
                        accent="bg-red-500"
                    />
                </div>

                {/* Filters */}
                <div className="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
                    <form
                        onSubmit={handleSearch}
                        className="flex flex-wrap items-center gap-3"
                    >
                        {/* Search */}
                        <div className="relative flex-1 min-w-60">
                            <MaterialIcon
                                name="search"
                                className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"
                            />
                            <input
                                type="text"
                                placeholder="Buscar por código o título..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                            />
                        </div>

                        <button
                            type="submit"
                            className="px-4 py-2 bg-[#002046] text-white text-sm font-semibold rounded-lg hover:bg-[#1b365d] transition-colors"
                        >
                            Buscar
                        </button>

                        {/* Status filter */}
                        <select
                            value={filters.status ?? ''}
                            onChange={(e) => applyFilter('status', e.target.value)}
                            className="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 text-gray-700"
                        >
                            {statusOptions.map((o) => (
                                <option key={o.value} value={o.value}>
                                    {o.label}
                                </option>
                            ))}
                        </select>

                        {/* Type filter */}
                        <select
                            value={filters.type ?? ''}
                            onChange={(e) => applyFilter('type', e.target.value)}
                            className="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 text-gray-700"
                        >
                            {typeOptions.map((o) => (
                                <option key={o.value} value={o.value}>
                                    {o.label}
                                </option>
                            ))}
                        </select>

                        {/* Priority filter */}
                        <select
                            value={filters.priority ?? ''}
                            onChange={(e) => applyFilter('priority', e.target.value)}
                            className="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 text-gray-700"
                        >
                            {priorityOptions.map((o) => (
                                <option key={o.value} value={o.value}>
                                    {o.label}
                                </option>
                            ))}
                        </select>

                        {/* Clear filters */}
                        {(filters.search || filters.status || filters.type || filters.priority) && (
                            <button
                                type="button"
                                onClick={() => {
                                    setSearch('');
                                    router.get('/work-orders', {}, { replace: true });
                                }}
                                className="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700"
                            >
                                <MaterialIcon name="close" className="text-sm" />
                                Limpiar
                            </button>
                        )}
                    </form>
                </div>

                {/* Table */}
                <div className="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
                    {workOrders.data.length === 0 ? (
                        <div className="py-20 text-center">
                            <MaterialIcon
                                name="inbox"
                                className="text-5xl text-gray-200 block mx-auto mb-3"
                            />
                            <p className="text-gray-400 font-medium">
                                No hay órdenes de trabajo
                            </p>
                        </div>
                    ) : (
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/50">
                                    <th className="text-left py-3.5 px-5 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                        Código / Título
                                    </th>
                                    <th className="text-left py-3.5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                        Tipo
                                    </th>
                                    <th className="text-left py-3.5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                        Activo
                                    </th>
                                    <th className="text-left py-3.5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                        Estado
                                    </th>
                                    <th className="text-left py-3.5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                        Prioridad
                                    </th>
                                    <th className="text-left py-3.5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                        Asignado a
                                    </th>
                                    <th className="text-left py-3.5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                        Fecha límite
                                    </th>
                                    <th className="py-3.5 px-4" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {workOrders.data.map((wo) => (
                                    <tr
                                        key={wo.id}
                                        className="hover:bg-gray-50/70 transition-colors group"
                                    >
                                        {/* Code + Title */}
                                        <td className="py-4 px-5">
                                            <div className="flex flex-col">
                                                <span className="font-mono text-xs font-bold text-[#002046]">
                                                    {wo.code}
                                                </span>
                                                <span className="text-gray-700 font-medium text-sm mt-0.5 line-clamp-1">
                                                    {wo.title}
                                                </span>
                                            </div>
                                        </td>

                                        {/* Type badge */}
                                        <td className="py-4 px-4">
                                            <span
                                                className={cn(
                                                    'inline-block px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase',
                                                    TYPE_COLORS[wo.type],
                                                )}
                                            >
                                                {TYPE_ABBREV[wo.type]}
                                            </span>
                                        </td>

                                        {/* Asset */}
                                        <td className="py-4 px-4">
                                            <div className="flex flex-col">
                                                <span className="font-medium text-gray-800 text-xs line-clamp-1">
                                                    {wo.asset.name}
                                                </span>
                                                {wo.asset.location && (
                                                    <span className="text-[10px] text-gray-400 mt-0.5">
                                                        {wo.asset.location.name}
                                                    </span>
                                                )}
                                            </div>
                                        </td>

                                        {/* Status */}
                                        <td className="py-4 px-4">
                                            <span
                                                className={cn(
                                                    'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border',
                                                    STATUS_COLORS[wo.status],
                                                )}
                                            >
                                                {STATUS_LABELS[wo.status]}
                                            </span>
                                        </td>

                                        {/* Priority */}
                                        <td className="py-4 px-4">
                                            <span
                                                className={cn(
                                                    'text-xs font-bold',
                                                    PRIORITY_COLORS[wo.priority],
                                                )}
                                            >
                                                {PRIORITY_LABELS[wo.priority]}
                                            </span>
                                        </td>

                                        {/* Assigned */}
                                        <td className="py-4 px-4">
                                            {wo.assigned_to ? (
                                                <div className="flex items-center gap-2">
                                                    <div className="w-6 h-6 rounded-md bg-[#1b365d] text-white flex items-center justify-center text-[9px] font-bold shrink-0">
                                                        {wo.assigned_to.name
                                                            .split(' ')
                                                            .map((p) => p[0])
                                                            .slice(0, 2)
                                                            .join('')}
                                                    </div>
                                                    <span className="text-xs text-gray-600 font-medium line-clamp-1">
                                                        {wo.assigned_to.name}
                                                    </span>
                                                </div>
                                            ) : (
                                                <span className="text-[10px] text-gray-300 font-medium">
                                                    Sin asignar
                                                </span>
                                            )}
                                        </td>

                                        {/* Due date */}
                                        <td className="py-4 px-4">
                                            {wo.due_date ? (
                                                <span
                                                    className={cn(
                                                        'text-xs font-medium',
                                                        new Date(wo.due_date) < new Date()
                                                            ? 'text-red-500 font-bold'
                                                            : 'text-gray-500',
                                                    )}
                                                >
                                                    {new Date(wo.due_date).toLocaleDateString(
                                                        'es-MX',
                                                        {
                                                            day: '2-digit',
                                                            month: 'short',
                                                            year: 'numeric',
                                                        },
                                                    )}
                                                </span>
                                            ) : (
                                                <span className="text-[10px] text-gray-300">—</span>
                                            )}
                                        </td>

                                        {/* Action */}
                                        <td className="py-4 px-4">
                                            <Link
                                                href={`/work-orders/${wo.id}`}
                                                className="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-[#002046] hover:text-[#1b365d] text-xs font-bold"
                                            >
                                                Ver
                                                <MaterialIcon
                                                    name="arrow_forward"
                                                    className="text-sm"
                                                />
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>

                {/* Pagination */}
                {workOrders.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-sm text-gray-500">
                            Mostrando {workOrders.from}–{workOrders.to} de{' '}
                            {workOrders.total}
                        </p>
                        <div className="flex items-center gap-1">
                            {workOrders.links.map((link, i) => (
                                <button
                                    key={i}
                                    disabled={!link.url}
                                    onClick={() => link.url && router.visit(link.url)}
                                    className={cn(
                                        'px-3 py-1.5 rounded text-xs font-medium transition-colors',
                                        link.active
                                            ? 'bg-[#002046] text-white'
                                            : link.url
                                              ? 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'
                                              : 'bg-gray-50 text-gray-300 cursor-not-allowed',
                                    )}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </CmmsLayout>
    );
}
