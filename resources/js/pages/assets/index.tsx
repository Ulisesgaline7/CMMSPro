import { Link, router } from '@inertiajs/react';
import { useCallback, useState } from 'react';
import CmmsLayout from '@/layouts/cmms-layout';
import { cn } from '@/lib/utils';

// ─── Types ────────────────────────────────────────────────────────────────────

type AssetStatus = 'active' | 'inactive' | 'under_maintenance' | 'retired';
type AssetCriticality = 'low' | 'medium' | 'high' | 'critical';

interface AssetCategory {
    id: number;
    name: string;
}

interface AssetSummary {
    id: number;
    name: string;
    code: string;
    serial_number: string | null;
    brand: string | null;
    model: string | null;
    status: AssetStatus;
    criticality: AssetCriticality;
    location: { id: number; name: string } | null;
    category: { id: number; name: string } | null;
}

interface PaginatedAssets {
    data: AssetSummary[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
    from: number | null;
    to: number | null;
}

interface Props {
    assets: PaginatedAssets;
    categories: AssetCategory[];
    filters: { search?: string; status?: string; criticality?: string; category?: string };
}

// ─── Constants ────────────────────────────────────────────────────────────────

const STATUS_LABELS: Record<AssetStatus, string> = {
    active: 'Activo',
    inactive: 'Inactivo',
    under_maintenance: 'En Mantenimiento',
    retired: 'Dado de Baja',
};

const STATUS_COLORS: Record<AssetStatus, string> = {
    active: 'bg-green-50 text-green-700 border-green-200',
    inactive: 'bg-gray-100 text-gray-600 border-gray-200',
    under_maintenance: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    retired: 'bg-red-50 text-red-600 border-red-200',
};

const CRITICALITY_LABELS: Record<AssetCriticality, string> = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    critical: 'Crítica',
};

const CRITICALITY_COLORS: Record<AssetCriticality, string> = {
    low: 'text-gray-400',
    medium: 'text-blue-500',
    high: 'text-orange-500',
    critical: 'text-red-600',
};

// ─── Helpers ─────────────────────────────────────────────────────────────────

function MaterialIcon({ name, className }: { name: string; className?: string }) {
    return <span className={cn('material-symbols-outlined select-none', className)}>{name}</span>;
}

function StatusBadge({ status }: { status: AssetStatus }) {
    return (
        <span className={cn('inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border', STATUS_COLORS[status])}>
            {STATUS_LABELS[status]}
        </span>
    );
}

function CriticalityDot({ criticality }: { criticality: AssetCriticality }) {
    return (
        <span className={cn('inline-flex items-center gap-1.5 text-xs font-semibold', CRITICALITY_COLORS[criticality])}>
            <span className="w-1.5 h-1.5 rounded-full bg-current" />
            {CRITICALITY_LABELS[criticality]}
        </span>
    );
}

// ─── Page ─────────────────────────────────────────────────────────────────────

export default function AssetsIndex({ assets, categories, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');

    const applyFilter = useCallback(
        (key: string, value: string) => {
            router.get('/assets', { ...filters, [key]: value || undefined, page: undefined }, { preserveState: true, replace: true });
        },
        [filters],
    );

    const handleSearch = useCallback(
        (e: React.FormEvent) => {
            e.preventDefault();
            applyFilter('search', search);
        },
        [search, applyFilter],
    );

    return (
        <CmmsLayout title="Activos" headerTitle="Activos">
            <div className="p-6 space-y-5">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">
                            Activos
                        </h2>
                        <p className="text-sm text-gray-400 mt-0.5">
                            {assets.total} {assets.total === 1 ? 'activo registrado' : 'activos registrados'}
                        </p>
                    </div>
                    <Link
                        href="/assets/create"
                        className="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm"
                    >
                        <MaterialIcon name="add_circle" className="text-base" />
                        Nuevo Activo
                    </Link>
                </div>

                {/* Filters */}
                <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
                    <form onSubmit={handleSearch} className="flex gap-2 flex-1 min-w-48">
                        <div className="relative flex-1">
                            <MaterialIcon name="search" className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base" />
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Buscar por código, nombre, serie..."
                                className="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                            />
                        </div>
                        <button
                            type="submit"
                            className="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors"
                        >
                            Buscar
                        </button>
                    </form>

                    <select
                        value={filters.status ?? ''}
                        onChange={(e) => applyFilter('status', e.target.value)}
                        className="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20"
                    >
                        <option value="">Todos los estados</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                        <option value="under_maintenance">En Mantenimiento</option>
                        <option value="retired">Dado de Baja</option>
                    </select>

                    <select
                        value={filters.criticality ?? ''}
                        onChange={(e) => applyFilter('criticality', e.target.value)}
                        className="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20"
                    >
                        <option value="">Toda criticidad</option>
                        <option value="critical">Crítica</option>
                        <option value="high">Alta</option>
                        <option value="medium">Media</option>
                        <option value="low">Baja</option>
                    </select>

                    {categories.length > 0 && (
                        <select
                            value={filters.category ?? ''}
                            onChange={(e) => applyFilter('category', e.target.value)}
                            className="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20"
                        >
                            <option value="">Todas las categorías</option>
                            {categories.map((c) => (
                                <option key={c.id} value={c.id}>
                                    {c.name}
                                </option>
                            ))}
                        </select>
                    )}

                    {(filters.search || filters.status || filters.criticality || filters.category) && (
                        <button
                            onClick={() => router.get('/assets', {}, { replace: true })}
                            className="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1"
                        >
                            <MaterialIcon name="close" className="text-sm" />
                            Limpiar
                        </button>
                    )}
                </div>

                {/* Table */}
                <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    {assets.data.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <MaterialIcon name="precision_manufacturing" className="text-5xl text-gray-200 mb-3" />
                            <p className="text-gray-500 font-medium">No se encontraron activos</p>
                            <p className="text-gray-400 text-sm mt-1">
                                {filters.search || filters.status || filters.criticality || filters.category
                                    ? 'Intenta con otros filtros'
                                    : 'Crea el primer activo'}
                            </p>
                            {!filters.search && !filters.status && !filters.criticality && !filters.category && (
                                <Link
                                    href="/assets/create"
                                    className="mt-4 flex items-center gap-1.5 text-sm font-semibold text-[#002046] hover:underline"
                                >
                                    <MaterialIcon name="add_circle" className="text-base" />
                                    Nuevo Activo
                                </Link>
                            )}
                        </div>
                    ) : (
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/60">
                                    <th className="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Activo
                                    </th>
                                    <th className="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">
                                        Marca / Modelo
                                    </th>
                                    <th className="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">
                                        Ubicación
                                    </th>
                                    <th className="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">
                                        Criticidad
                                    </th>
                                    <th className="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">
                                        Estado
                                    </th>
                                    <th className="px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {assets.data.map((asset) => (
                                    <tr key={asset.id} className="hover:bg-gray-50/50 transition-colors">
                                        <td className="px-5 py-3.5">
                                            <div className="font-semibold text-[#002046]">{asset.name}</div>
                                            <div className="text-xs text-gray-400 mt-0.5 font-mono">{asset.code}</div>
                                            {asset.category && (
                                                <div className="text-xs text-gray-400 mt-0.5">{asset.category.name}</div>
                                            )}
                                        </td>
                                        <td className="px-4 py-3.5 hidden md:table-cell">
                                            {asset.brand || asset.model ? (
                                                <div>
                                                    <span className="text-gray-700">{asset.brand}</span>
                                                    {asset.brand && asset.model && <span className="text-gray-300 mx-1">·</span>}
                                                    <span className="text-gray-500">{asset.model}</span>
                                                </div>
                                            ) : (
                                                <span className="text-gray-300">—</span>
                                            )}
                                            {asset.serial_number && (
                                                <div className="text-xs text-gray-400 mt-0.5 font-mono">{asset.serial_number}</div>
                                            )}
                                        </td>
                                        <td className="px-4 py-3.5 hidden lg:table-cell">
                                            {asset.location ? (
                                                <span className="text-gray-600">{asset.location.name}</span>
                                            ) : (
                                                <span className="text-gray-300">—</span>
                                            )}
                                        </td>
                                        <td className="px-4 py-3.5 hidden lg:table-cell">
                                            <CriticalityDot criticality={asset.criticality} />
                                        </td>
                                        <td className="px-4 py-3.5">
                                            <StatusBadge status={asset.status} />
                                        </td>
                                        <td className="px-4 py-3.5 text-right">
                                            <Link
                                                href={`/assets/${asset.id}`}
                                                className="inline-flex items-center gap-1 text-xs font-semibold text-[#002046] hover:underline"
                                            >
                                                Ver
                                                <MaterialIcon name="chevron_right" className="text-sm" />
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>

                {/* Pagination */}
                {assets.last_page > 1 && (
                    <div className="flex items-center justify-between text-sm text-gray-500">
                        <span>
                            {assets.from}–{assets.to} de {assets.total}
                        </span>
                        <div className="flex gap-2">
                            {assets.current_page > 1 && (
                                <button
                                    onClick={() => applyFilter('page', String(assets.current_page - 1))}
                                    className="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                                >
                                    Anterior
                                </button>
                            )}
                            {assets.current_page < assets.last_page && (
                                <button
                                    onClick={() => applyFilter('page', String(assets.current_page + 1))}
                                    className="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                                >
                                    Siguiente
                                </button>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </CmmsLayout>
    );
}
