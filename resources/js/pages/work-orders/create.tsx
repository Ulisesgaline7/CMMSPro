import { useForm, Link } from '@inertiajs/react';
import CmmsLayout from '@/layouts/cmms-layout';
import { cn } from '@/lib/utils';

// ─── Types ────────────────────────────────────────────────────────────────────

interface Asset {
    id: number;
    name: string;
    code: string;
    location: { id: number; name: string } | null;
}

interface Technician {
    id: number;
    name: string;
    employee_code: string | null;
}

interface Props {
    assets: Asset[];
    technicians: Technician[];
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function MaterialIcon({ name, className }: { name: string; className?: string }) {
    return <span className={cn('material-symbols-outlined select-none', className)}>{name}</span>;
}

function FieldError({ message }: { message?: string }) {
    if (!message) return null;
    return <p className="text-xs text-red-500 mt-1">{message}</p>;
}

function Label({ children, required }: { children: React.ReactNode; required?: boolean }) {
    return (
        <label className="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
            {children}
            {required && <span className="text-red-400 ml-0.5">*</span>}
        </label>
    );
}

// ─── Page ─────────────────────────────────────────────────────────────────────

export default function CreateWorkOrder({ assets, technicians }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        type: 'corrective',
        priority: 'medium',
        asset_id: '',
        assigned_to: '',
        due_date: '',
        estimated_duration: '',
        failure_cause: '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/work-orders');
    }

    const typeOptions = [
        { value: 'corrective', label: 'Correctivo (CM)', icon: 'build', color: 'border-red-200 bg-red-50 text-red-700' },
        { value: 'preventive', label: 'Preventivo (PM)', icon: 'event_available', color: 'border-blue-200 bg-blue-50 text-blue-700' },
        { value: 'predictive', label: 'Predictivo (PdM)', icon: 'analytics', color: 'border-purple-200 bg-purple-50 text-purple-700' },
    ];

    const priorityOptions = [
        { value: 'low',      label: 'Baja',    dot: 'bg-gray-300' },
        { value: 'medium',   label: 'Media',   dot: 'bg-blue-400' },
        { value: 'high',     label: 'Alta',    dot: 'bg-orange-400' },
        { value: 'critical', label: 'Crítica', dot: 'bg-red-500' },
    ];

    return (
        <CmmsLayout title="Nueva Orden de Trabajo" headerTitle="Nueva Orden de Trabajo">
            <div className="p-8 max-w-4xl mx-auto">
                {/* Header */}
                <div className="flex items-center gap-4 mb-8">
                    <Link
                        href="/work-orders"
                        className="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600"
                    >
                        <MaterialIcon name="arrow_back" className="text-xl" />
                    </Link>
                    <div>
                        <h2 className="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">
                            Nueva Orden de Trabajo
                        </h2>
                        <p className="text-sm text-gray-400 mt-0.5">
                            Completa los campos para crear la orden
                        </p>
                    </div>
                </div>

                <form onSubmit={submit} className="space-y-6">
                    {/* ── Tipo de orden ─────────────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">
                            Tipo de Orden
                        </h3>
                        <div className="grid grid-cols-3 gap-3">
                            {typeOptions.map((opt) => (
                                <button
                                    key={opt.value}
                                    type="button"
                                    onClick={() => setData('type', opt.value)}
                                    className={cn(
                                        'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all',
                                        data.type === opt.value
                                            ? opt.color + ' border-current'
                                            : 'border-gray-100 text-gray-400 hover:border-gray-200',
                                    )}
                                >
                                    <MaterialIcon name={opt.icon} className="text-2xl" />
                                    <span className="text-xs font-bold">{opt.label}</span>
                                </button>
                            ))}
                        </div>
                        <FieldError message={errors.type} />
                    </div>

                    {/* ── Información general ───────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">
                            Información General
                        </h3>

                        {/* Título */}
                        <div>
                            <Label required>Título</Label>
                            <input
                                type="text"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                placeholder="Ej: Cambio de aceite compresor línea 3"
                                className={cn(
                                    'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]',
                                    errors.title ? 'border-red-300' : 'border-gray-200',
                                )}
                            />
                            <FieldError message={errors.title} />
                        </div>

                        {/* Descripción */}
                        <div>
                            <Label>Descripción</Label>
                            <textarea
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                rows={3}
                                placeholder="Describe el trabajo a realizar..."
                                className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none"
                            />
                            <FieldError message={errors.description} />
                        </div>

                        {/* Causa de falla (solo correctivo) */}
                        {data.type === 'corrective' && (
                            <div>
                                <Label>Causa de Falla</Label>
                                <textarea
                                    value={data.failure_cause}
                                    onChange={(e) => setData('failure_cause', e.target.value)}
                                    rows={2}
                                    placeholder="Describe la causa o síntoma de la falla..."
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none"
                                />
                                <FieldError message={errors.failure_cause} />
                            </div>
                        )}
                    </div>

                    {/* ── Activo y asignación ───────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">
                            Activo y Asignación
                        </h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {/* Activo */}
                            <div>
                                <Label required>Activo</Label>
                                <select
                                    value={data.asset_id}
                                    onChange={(e) => setData('asset_id', e.target.value)}
                                    className={cn(
                                        'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white',
                                        errors.asset_id ? 'border-red-300' : 'border-gray-200',
                                    )}
                                >
                                    <option value="">Seleccionar activo...</option>
                                    {assets.map((a) => (
                                        <option key={a.id} value={a.id}>
                                            [{a.code}] {a.name}
                                            {a.location ? ` — ${a.location.name}` : ''}
                                        </option>
                                    ))}
                                </select>
                                <FieldError message={errors.asset_id} />
                            </div>

                            {/* Técnico asignado */}
                            <div>
                                <Label>Asignar a</Label>
                                <select
                                    value={data.assigned_to}
                                    onChange={(e) => setData('assigned_to', e.target.value)}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white"
                                >
                                    <option value="">Sin asignar</option>
                                    {technicians.map((t) => (
                                        <option key={t.id} value={t.id}>
                                            {t.name}
                                            {t.employee_code ? ` (${t.employee_code})` : ''}
                                        </option>
                                    ))}
                                </select>
                                <FieldError message={errors.assigned_to} />
                            </div>
                        </div>
                    </div>

                    {/* ── Prioridad y fechas ────────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">
                            Prioridad y Planificación
                        </h3>

                        {/* Prioridad */}
                        <div>
                            <Label required>Prioridad</Label>
                            <div className="flex gap-2">
                                {priorityOptions.map((opt) => (
                                    <button
                                        key={opt.value}
                                        type="button"
                                        onClick={() => setData('priority', opt.value)}
                                        className={cn(
                                            'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg border-2 text-xs font-bold transition-all',
                                            data.priority === opt.value
                                                ? 'border-[#002046] bg-[#002046] text-white'
                                                : 'border-gray-100 text-gray-500 hover:border-gray-200',
                                        )}
                                    >
                                        <span className={cn('w-2 h-2 rounded-full', opt.dot)} />
                                        {opt.label}
                                    </button>
                                ))}
                            </div>
                            <FieldError message={errors.priority} />
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {/* Fecha límite */}
                            <div>
                                <Label>Fecha Límite</Label>
                                <input
                                    type="date"
                                    value={data.due_date}
                                    onChange={(e) => setData('due_date', e.target.value)}
                                    min={new Date().toISOString().split('T')[0]}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.due_date} />
                            </div>

                            {/* Duración estimada */}
                            <div>
                                <Label>Duración Estimada (minutos)</Label>
                                <input
                                    type="number"
                                    value={data.estimated_duration}
                                    onChange={(e) => setData('estimated_duration', e.target.value)}
                                    placeholder="Ej: 120"
                                    min={1}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.estimated_duration} />
                            </div>
                        </div>
                    </div>

                    {/* ── Actions ───────────────────────────────────────── */}
                    <div className="flex items-center justify-between pt-2">
                        <Link
                            href="/work-orders"
                            className="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex items-center gap-2 bg-[#002046] text-white px-8 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            {processing ? (
                                <>
                                    <MaterialIcon name="sync" className="text-base animate-spin" />
                                    Guardando...
                                </>
                            ) : (
                                <>
                                    <MaterialIcon name="add_circle" className="text-base" />
                                    Crear Orden
                                </>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </CmmsLayout>
    );
}
