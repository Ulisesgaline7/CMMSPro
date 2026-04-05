import { Link, useForm } from '@inertiajs/react';
import { useRef, useState } from 'react';
import CmmsLayout from '@/layouts/cmms-layout';
import { cn } from '@/lib/utils';
import type { WorkOrder, WorkOrderStatus } from '@/types';

// ─── Helpers ─────────────────────────────────────────────────────────────────

function MaterialIcon({
    name,
    fill = false,
    className,
}: {
    name: string;
    fill?: boolean;
    className?: string;
}) {
    return (
        <span
            className={cn('material-symbols-outlined select-none', className)}
            style={
                fill
                    ? { fontVariationSettings: "'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24" }
                    : undefined
            }
        >
            {name}
        </span>
    );
}

const STATUS_META: Record<
    WorkOrderStatus,
    { label: string; color: string; dot: string }
> = {
    draft: {
        label: 'Borrador',
        color: 'bg-gray-100 text-gray-600',
        dot: 'bg-gray-400',
    },
    pending: {
        label: 'Pendiente',
        color: 'bg-yellow-100 text-yellow-700',
        dot: 'bg-yellow-500',
    },
    in_progress: {
        label: 'En Progreso',
        color: 'bg-blue-100 text-blue-700',
        dot: 'bg-blue-500',
    },
    on_hold: {
        label: 'En Pausa',
        color: 'bg-orange-100 text-orange-700',
        dot: 'bg-orange-500',
    },
    completed: {
        label: 'Completada',
        color: 'bg-green-100 text-green-700',
        dot: 'bg-green-500',
    },
    cancelled: {
        label: 'Cancelada',
        color: 'bg-red-100 text-red-600',
        dot: 'bg-red-500',
    },
};

const STATUS_TRANSITIONS: Partial<Record<WorkOrderStatus, { status: WorkOrderStatus; label: string }[]>> = {
    draft: [
        { status: 'pending', label: 'Enviar para aprobación' },
        { status: 'cancelled', label: 'Cancelar' },
    ],
    pending: [
        { status: 'in_progress', label: 'Iniciar trabajo' },
        { status: 'on_hold', label: 'Pausar' },
        { status: 'cancelled', label: 'Cancelar' },
    ],
    in_progress: [
        { status: 'on_hold', label: 'Pausar' },
        { status: 'completed', label: 'Finalizar y cerrar OT' },
        { status: 'cancelled', label: 'Cancelar' },
    ],
    on_hold: [
        { status: 'in_progress', label: 'Reanudar' },
        { status: 'cancelled', label: 'Cancelar' },
    ],
};

const TYPE_LABELS: Record<string, string> = {
    preventive: 'PREVENTIVO',
    corrective: 'CORRECTIVO',
    predictive: 'PREDICTIVO',
};

const PRIORITY_LABELS: Record<string, { label: string; color: string }> = {
    low: { label: 'BAJA', color: 'text-gray-500' },
    medium: { label: 'MEDIA', color: 'text-blue-600' },
    high: { label: 'ALTA CRITICIDAD', color: 'text-orange-600' },
    critical: { label: 'CRÍTICA', color: 'text-red-600' },
};

const CRITICALITY_DOT: Record<string, string> = {
    low: 'bg-gray-400',
    medium: 'bg-blue-500',
    high: 'bg-orange-500',
    critical: 'bg-red-600',
};

function formatDate(dateStr: string | null): string {
    if (!dateStr) {
        return '—';
    }

    return new Date(dateStr).toLocaleDateString('es-MX', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatDuration(minutes: number | null): string {
    if (!minutes) {
        return '—';
    }

    const h = Math.floor(minutes / 60);
    const m = minutes % 60;

    if (h === 0) {
        return `${m}min`;
    }

    return m === 0 ? `${h}h` : `${h}h ${m}min`;
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((p) => p[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

const ACTIVITY_ICONS: Record<string, { icon: string; fill: boolean }> = {
    created: { icon: 'add_circle', fill: true },
    status_changed: { icon: 'sync', fill: false },
    assigned: { icon: 'person_add', fill: true },
    approved: { icon: 'verified', fill: true },
    started: { icon: 'play_circle', fill: true },
    paused: { icon: 'pause_circle', fill: true },
    resumed: { icon: 'play_circle', fill: true },
    completed: { icon: 'check_circle', fill: true },
    cancelled: { icon: 'cancel', fill: true },
    note_added: { icon: 'comment', fill: true },
    part_added: { icon: 'inventory', fill: false },
    part_removed: { icon: 'remove_circle', fill: false },
    checklist_item_completed: { icon: 'task_alt', fill: true },
    photo_added: { icon: 'add_a_photo', fill: false },
};

const ACTIVITY_LABELS: Record<string, string> = {
    created: 'Orden creada',
    status_changed: 'Estado cambiado',
    assigned: 'Técnico asignado',
    approved: 'Orden aprobada',
    started: 'Trabajo iniciado',
    paused: 'Trabajo pausado',
    resumed: 'Trabajo reanudado',
    completed: 'Orden completada',
    cancelled: 'Orden cancelada',
    note_added: 'Nota agregada',
    part_added: 'Repuesto agregado',
    part_removed: 'Repuesto removido',
    checklist_item_completed: 'Ítem de checklist completado',
    photo_added: 'Foto adjuntada',
};

// ─── Page ────────────────────────────────────────────────────────────────────

interface Props {
    workOrder: WorkOrder;
}

export default function WorkOrderShow({ workOrder }: Props) {
    const [noteText, setNoteText] = useState('');
    const [signatureMode, setSignatureMode] = useState(false);
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const [isDrawing, setIsDrawing] = useState(false);

    const asset = workOrder.asset;
    const statusMeta = STATUS_META[workOrder.status];
    const priorityMeta = PRIORITY_LABELS[workOrder.priority] ?? {
        label: workOrder.priority,
        color: 'text-gray-500',
    };
    const transitions = STATUS_TRANSITIONS[workOrder.status] ?? [];

    const totalItems = workOrder.checklists.reduce(
        (acc, cl) => acc + cl.items.length,
        0,
    );
    const completedItems = workOrder.checklists.reduce(
        (acc, cl) => acc + cl.items.filter((i) => i.is_completed).length,
        0,
    );

    // Forms
    const completeItemForm = useForm<{ checklist_item_id: number | null }>({
        checklist_item_id: null,
    });
    const noteForm = useForm<{ notes: string }>({ notes: '' });
    const statusForm = useForm<{ status: string }>({ status: '' });

    function handleCompleteItem(itemId: number) {
        completeItemForm.setData('checklist_item_id', itemId);
        completeItemForm.post(`/work-orders/${workOrder.id}/complete-item`, {
            preserveScroll: true,
            onSuccess: () => completeItemForm.reset(),
        });
    }

    function handleAddNote(e: React.FormEvent) {
        e.preventDefault();
        noteForm.setData('notes', noteText);
        noteForm.post(`/work-orders/${workOrder.id}/notes`, {
            preserveScroll: true,
            onSuccess: () => setNoteText(''),
        });
    }

    function handleStatusChange(newStatus: WorkOrderStatus) {
        statusForm.setData('status', newStatus);
        statusForm.patch(`/work-orders/${workOrder.id}/status`, {
            preserveScroll: true,
        });
    }

    // Canvas drawing
    function startDrawing(e: React.MouseEvent<HTMLCanvasElement>) {
        const canvas = canvasRef.current;
        if (!canvas) { return; }
        const ctx = canvas.getContext('2d');
        if (!ctx) { return; }
        setIsDrawing(true);
        const rect = canvas.getBoundingClientRect();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    function draw(e: React.MouseEvent<HTMLCanvasElement>) {
        if (!isDrawing) { return; }
        const canvas = canvasRef.current;
        if (!canvas) { return; }
        const ctx = canvas.getContext('2d');
        if (!ctx) { return; }
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.strokeStyle = '#002046';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.stroke();
    }

    function stopDrawing() {
        setIsDrawing(false);
    }

    function clearSignature() {
        const canvas = canvasRef.current;
        if (!canvas) { return; }
        const ctx = canvas.getContext('2d');
        if (!ctx) { return; }
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    const isClosed = workOrder.status === 'completed' || workOrder.status === 'cancelled';

    return (
        <CmmsLayout
            title={`${workOrder.code} — Orden de Trabajo`}
            headerTitle="Ejecución de Orden"
        >
            <div className="p-8 max-w-7xl mx-auto space-y-10 pb-16">
                {/* ── Breadcrumb ── */}
                <nav className="flex items-center gap-2 text-xs text-gray-400">
                    <Link href="/work-orders" className="hover:text-[#002046] transition-colors">
                        Órdenes de Trabajo
                    </Link>
                    <span>/</span>
                    <span className="text-[#002046] font-semibold">{workOrder.code}</span>
                </nav>

                {/* ── 1. Header + Asset (Asymmetric) ── */}
                <section className="grid grid-cols-12 gap-8 items-start">
                    <div className="col-span-12 lg:col-span-7 space-y-4">
                        {/* Title row */}
                        <div className="flex items-center gap-4">
                            <span className="bg-[#002046] text-white px-3 py-1 text-[10px] font-bold tracking-[0.12em] rounded-sm uppercase">
                                {TYPE_LABELS[workOrder.type] ?? workOrder.type}
                            </span>
                            <h2 className="text-4xl font-extrabold text-[#002046] font-headline tracking-tight">
                                {workOrder.code}
                            </h2>
                            <span
                                className={cn(
                                    'flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold',
                                    statusMeta.color,
                                )}
                            >
                                <span
                                    className={cn(
                                        'w-1.5 h-1.5 rounded-full',
                                        statusMeta.dot,
                                    )}
                                />
                                {statusMeta.label}
                            </span>
                        </div>

                        {/* Title */}
                        <p className="text-lg font-semibold text-gray-700">{workOrder.title}</p>

                        {/* Asset card */}
                        <div className="bg-[#f3f3f7] p-6 rounded-lg relative overflow-hidden group">
                            <div className="absolute top-0 right-0 w-32 h-32 bg-[#002046]/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110" />
                            <div className="relative flex flex-col md:flex-row gap-6">
                                {/* Asset icon */}
                                <div className="w-28 h-28 flex-shrink-0 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-center">
                                    <MaterialIcon
                                        name="precision_manufacturing"
                                        className="text-5xl text-[#002046]/30"
                                    />
                                </div>
                                <div className="flex-1">
                                    <h3 className="text-xl font-bold text-[#002046] font-headline mb-1">
                                        {asset.name}
                                    </h3>
                                    <p className="text-gray-400 font-medium mb-4 flex items-center gap-1 text-sm">
                                        <MaterialIcon name="location_on" className="text-sm" />
                                        {asset.location
                                            ? `${asset.location.parent?.name ?? ''} ${asset.location.parent ? '•' : ''} ${asset.location.name}`
                                            : 'Sin ubicación'}
                                    </p>
                                    <div className="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p className="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-1">
                                                ID Activo
                                            </p>
                                            <p className="font-mono text-[#002046] font-semibold text-xs">
                                                {asset.code}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-1">
                                                Prioridad
                                            </p>
                                            <div className="flex items-center gap-1.5">
                                                <span
                                                    className={cn(
                                                        'w-2 h-2 rounded-full',
                                                        CRITICALITY_DOT[asset.criticality] ??
                                                            'bg-gray-400',
                                                    )}
                                                />
                                                <span
                                                    className={cn(
                                                        'font-bold text-xs',
                                                        priorityMeta.color,
                                                    )}
                                                >
                                                    {priorityMeta.label}
                                                </span>
                                            </div>
                                        </div>
                                        {asset.brand && (
                                            <div>
                                                <p className="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-1">
                                                    Fabricante
                                                </p>
                                                <p className="font-medium text-gray-700 text-xs">
                                                    {asset.brand}{' '}
                                                    {asset.model ? `• ${asset.model}` : ''}
                                                </p>
                                            </div>
                                        )}
                                        {workOrder.assigned_to && (
                                            <div>
                                                <p className="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-1">
                                                    Técnico Asignado
                                                </p>
                                                <p className="font-medium text-gray-700 text-xs">
                                                    {workOrder.assigned_to.name}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right column: stats */}
                    <div className="col-span-12 lg:col-span-5 grid grid-cols-2 gap-4">
                        <div className="bg-[#e7e8eb] p-5 rounded-lg flex flex-col justify-between">
                            <p className="text-[9px] font-bold tracking-widest uppercase text-gray-500">
                                Fecha Creación
                            </p>
                            <div>
                                <p className="text-xl font-bold text-[#002046]">
                                    {formatDate(workOrder.created_at)}
                                </p>
                                <p className="text-xs text-gray-400">
                                    {workOrder.requested_by?.name ?? 'Sistema'}
                                </p>
                            </div>
                        </div>

                        <div className="bg-[#002046] text-white p-5 rounded-lg flex flex-col justify-between">
                            <MaterialIcon
                                name="timer"
                                className="text-blue-300"
                            />
                            <div>
                                <p className="text-xl font-bold">
                                    {formatDuration(workOrder.estimated_duration)}
                                </p>
                                <p className="text-[9px] font-bold tracking-widest uppercase opacity-60">
                                    Duración estimada
                                </p>
                            </div>
                        </div>

                        {/* Due date */}
                        <div
                            className={cn(
                                'p-5 rounded-lg flex flex-col justify-between',
                                workOrder.due_date &&
                                    new Date(workOrder.due_date) < new Date() &&
                                    !isClosed
                                    ? 'bg-red-50 border border-red-200'
                                    : 'bg-[#f3f3f7]',
                            )}
                        >
                            <p className="text-[9px] font-bold tracking-widest uppercase text-gray-500">
                                Fecha Límite
                            </p>
                            <div>
                                <p className="text-xl font-bold text-[#002046]">
                                    {formatDate(workOrder.due_date)}
                                </p>
                                {workOrder.due_date &&
                                    new Date(workOrder.due_date) < new Date() &&
                                    !isClosed && (
                                        <p className="text-xs text-red-600 font-bold">
                                            Vencida
                                        </p>
                                    )}
                            </div>
                        </div>

                        {/* Safety instructions */}
                        <div className="bg-white p-5 rounded-lg border-l-4 border-[#002046]">
                            <p className="text-[9px] font-bold tracking-widest uppercase text-[#002046] mb-2">
                                Seguridad
                            </p>
                            <div className="space-y-1.5">
                                <div className="flex items-center gap-1.5 text-xs font-semibold text-gray-700">
                                    <MaterialIcon name="verified" fill className="text-[#002046] text-sm" />
                                    EPP Nivel 3
                                </div>
                                <div className="flex items-center gap-1.5 text-xs font-semibold text-gray-700">
                                    <MaterialIcon name="verified" fill className="text-[#002046] text-sm" />
                                    Bloqueo LOTO
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* ── 2. Description ── */}
                {workOrder.description && (
                    <section className="bg-[#f3f3f7] rounded-lg p-6">
                        <p className="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-2">
                            Descripción
                        </p>
                        <p className="text-sm text-gray-700 leading-relaxed">
                            {workOrder.description}
                        </p>
                        {workOrder.failure_cause && (
                            <>
                                <p className="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-2 mt-4">
                                    Causa de falla
                                </p>
                                <p className="text-sm text-gray-700">{workOrder.failure_cause}</p>
                            </>
                        )}
                    </section>
                )}

                {/* ── 3. Checklist ── */}
                {workOrder.checklists.length > 0 && (
                    <section className="space-y-5">
                        <div className="flex justify-between items-end">
                            <h3 className="text-xl font-extrabold text-[#002046] font-headline uppercase tracking-tight">
                                Checklist de Ejecución
                            </h3>
                            <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">
                                {completedItems} de {totalItems} completadas
                            </p>
                        </div>

                        {/* Progress bar */}
                        {totalItems > 0 && (
                            <div className="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div
                                    className="h-full bg-[#002046] rounded-full transition-all duration-500"
                                    style={{
                                        width: `${Math.round((completedItems / totalItems) * 100)}%`,
                                    }}
                                />
                            </div>
                        )}

                        {workOrder.checklists.map((checklist) => (
                            <div key={checklist.id} className="space-y-1">
                                <p className="text-[10px] font-bold uppercase tracking-widest text-gray-400 px-1 mb-2">
                                    {checklist.name}
                                </p>
                                <div className="bg-[#f3f3f7] rounded-lg p-1 space-y-1">
                                    {checklist.items.map((item, idx) => {
                                        const isCurrentActive =
                                            !item.is_completed &&
                                            checklist.items
                                                .slice(0, idx)
                                                .every((i) => i.is_completed);

                                        return (
                                            <div
                                                key={item.id}
                                                className={cn(
                                                    'p-5 flex items-center gap-5 transition-all',
                                                    item.is_completed
                                                        ? 'bg-white/60'
                                                        : isCurrentActive
                                                          ? 'bg-[#002046]/5 border-l-4 border-[#002046] shadow-sm'
                                                          : 'bg-white/30 opacity-60',
                                                )}
                                            >
                                                {/* Status icon */}
                                                <div
                                                    className={cn(
                                                        'w-10 h-10 rounded-full flex items-center justify-center shrink-0',
                                                        item.is_completed
                                                            ? 'bg-teal-50'
                                                            : isCurrentActive
                                                              ? 'bg-[#002046]/10'
                                                              : 'bg-gray-100',
                                                    )}
                                                >
                                                    {item.is_completed ? (
                                                        <MaterialIcon
                                                            name="check_circle"
                                                            fill
                                                            className="text-teal-600 text-xl"
                                                        />
                                                    ) : isCurrentActive ? (
                                                        <MaterialIcon
                                                            name="pending"
                                                            className="text-[#002046] text-xl animate-pulse"
                                                        />
                                                    ) : (
                                                        <MaterialIcon
                                                            name="radio_button_unchecked"
                                                            className="text-gray-300 text-xl"
                                                        />
                                                    )}
                                                </div>

                                                {/* Description */}
                                                <div className="flex-1">
                                                    <h4
                                                        className={cn(
                                                            'font-bold text-sm',
                                                            item.is_completed
                                                                ? 'text-gray-400 line-through'
                                                                : 'text-[#002046]',
                                                        )}
                                                    >
                                                        {item.description}
                                                    </h4>
                                                    {item.is_completed && item.completed_by && (
                                                        <p className="text-[10px] text-gray-400 mt-0.5">
                                                            Completado por {item.completed_by.name}{' '}
                                                            •{' '}
                                                            {item.completed_at
                                                                ? new Date(
                                                                      item.completed_at,
                                                                  ).toLocaleTimeString('es-MX', {
                                                                      hour: '2-digit',
                                                                      minute: '2-digit',
                                                                  })
                                                                : ''}
                                                        </p>
                                                    )}
                                                </div>

                                                {/* Action */}
                                                {item.is_completed ? (
                                                    <span className="text-[9px] font-bold bg-[#e7e8eb] px-2 py-1 rounded-sm uppercase text-gray-500">
                                                        Hecho
                                                    </span>
                                                ) : isCurrentActive && !isClosed ? (
                                                    <button
                                                        onClick={() =>
                                                            handleCompleteItem(item.id)
                                                        }
                                                        disabled={
                                                            completeItemForm.processing
                                                        }
                                                        className="px-4 py-2 bg-[#002046] text-white text-xs font-bold rounded shadow-sm hover:bg-[#1b365d] active:scale-95 transition-all disabled:opacity-50"
                                                    >
                                                        COMPLETAR
                                                    </button>
                                                ) : (
                                                    <span className="text-[9px] font-bold text-gray-300 uppercase tracking-widest">
                                                        Pendiente
                                                    </span>
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        ))}
                    </section>
                )}

                {/* ── 4. Parts used ── */}
                {workOrder.parts.length > 0 && (
                    <section className="space-y-4">
                        <h3 className="text-xl font-extrabold text-[#002046] font-headline uppercase tracking-tight">
                            Repuestos Utilizados
                        </h3>
                        <div className="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-gray-100 bg-gray-50/50">
                                        <th className="text-left py-3 px-5 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                            Repuesto
                                        </th>
                                        <th className="text-right py-3 px-5 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                            Cantidad
                                        </th>
                                        <th className="text-right py-3 px-5 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                            Costo unit.
                                        </th>
                                        <th className="text-right py-3 px-5 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {workOrder.parts.map((p) => (
                                        <tr key={p.id}>
                                            <td className="py-3.5 px-5 font-medium text-gray-800">
                                                {p.part_name}
                                            </td>
                                            <td className="py-3.5 px-5 text-right text-gray-600">
                                                {p.quantity} {p.unit}
                                            </td>
                                            <td className="py-3.5 px-5 text-right text-gray-600">
                                                {p.unit_cost != null
                                                    ? `$${Number(p.unit_cost).toLocaleString('es-MX')}`
                                                    : '—'}
                                            </td>
                                            <td className="py-3.5 px-5 text-right font-bold text-[#002046]">
                                                {p.unit_cost != null
                                                    ? `$${(Number(p.quantity) * Number(p.unit_cost)).toLocaleString('es-MX')}`
                                                    : '—'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </section>
                )}

                {/* ── 5. Notes / Evidence panel ── */}
                <section className="space-y-5">
                    <div className="flex justify-between items-center">
                        <h3 className="text-xl font-extrabold text-[#002046] font-headline uppercase tracking-tight">
                            Notas de Campo
                        </h3>
                    </div>

                    <div className="grid grid-cols-12 gap-6">
                        {/* Note form */}
                        {!isClosed && (
                            <div className="col-span-12 md:col-span-6 bg-[#e7e8eb] p-6 rounded-lg flex flex-col gap-3">
                                <p className="text-[10px] font-bold tracking-widest uppercase text-gray-500">
                                    Agregar observación técnica
                                </p>
                                <form onSubmit={handleAddNote} className="flex flex-col gap-3">
                                    <textarea
                                        value={noteText}
                                        onChange={(e) => setNoteText(e.target.value)}
                                        rows={4}
                                        placeholder="Ingrese observaciones técnicas adicionales aquí..."
                                        className="bg-white/70 border border-gray-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] placeholder:text-gray-300 placeholder:italic resize-none"
                                    />
                                    <button
                                        type="submit"
                                        disabled={!noteText.trim() || noteForm.processing}
                                        className="self-end flex items-center gap-2 px-4 py-2 bg-[#002046] text-white text-xs font-bold rounded hover:bg-[#1b365d] transition-colors disabled:opacity-40"
                                    >
                                        <MaterialIcon name="send" className="text-sm" />
                                        Guardar nota
                                    </button>
                                </form>
                            </div>
                        )}

                        {/* Activity feed */}
                        <div
                            className={cn(
                                'col-span-12 space-y-1',
                                isClosed ? 'md:col-span-12' : 'md:col-span-6',
                            )}
                        >
                            <p className="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-3">
                                Historial de actividad
                            </p>
                            {workOrder.activities.length === 0 ? (
                                <p className="text-sm text-gray-300 italic">Sin actividad registrada.</p>
                            ) : (
                                <div className="space-y-2 max-h-80 overflow-y-auto pr-1">
                                    {workOrder.activities.map((act) => {
                                        const meta = ACTIVITY_ICONS[act.action] ?? {
                                            icon: 'info',
                                            fill: false,
                                        };

                                        return (
                                            <div
                                                key={act.id}
                                                className="flex items-start gap-3 p-3 bg-white rounded-lg border border-gray-50"
                                            >
                                                <div className="w-7 h-7 rounded-full bg-[#002046]/10 flex items-center justify-center shrink-0 mt-0.5">
                                                    <MaterialIcon
                                                        name={meta.icon}
                                                        fill={meta.fill}
                                                        className="text-[#002046] text-sm"
                                                    />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <p className="text-xs font-bold text-gray-700">
                                                        {ACTIVITY_LABELS[act.action] ??
                                                            act.action}
                                                    </p>
                                                    {act.notes && (
                                                        <p className="text-xs text-gray-500 mt-0.5 italic">
                                                            &ldquo;{act.notes}&rdquo;
                                                        </p>
                                                    )}
                                                    {act.metadata?.item_description && (
                                                        <p className="text-xs text-gray-500 mt-0.5">
                                                            {
                                                                act.metadata
                                                                    .item_description as string
                                                            }
                                                        </p>
                                                    )}
                                                    <p className="text-[10px] text-gray-300 mt-1">
                                                        {act.user?.name ?? 'Sistema'} •{' '}
                                                        {new Date(
                                                            act.created_at,
                                                        ).toLocaleString('es-MX', {
                                                            day: '2-digit',
                                                            month: 'short',
                                                            hour: '2-digit',
                                                            minute: '2-digit',
                                                        })}
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                    </div>
                </section>

                {/* ── 6. ALCOA+ Signature + Actions ── */}
                {!isClosed && transitions.length > 0 && (
                    <section className="bg-[#f3f3f7] p-8 rounded-lg flex flex-col md:flex-row justify-between items-center gap-8">
                        {/* ALCOA+ info */}
                        <div className="space-y-3 max-w-md">
                            <div className="flex items-center gap-2">
                                <MaterialIcon
                                    name="security"
                                    fill
                                    className="text-[#002046]"
                                />
                                <h3 className="text-base font-bold text-[#002046] font-headline uppercase">
                                    Protocolo ALCOA+
                                </h3>
                            </div>
                            <p className="text-xs text-gray-500 leading-relaxed">
                                Al cambiar el estado de esta orden, usted garantiza que los datos
                                son Atribuibles, Legibles, Contemporáneos, Originales y Precisos
                                bajo la normativa ISO 9001:2015.
                            </p>
                        </div>

                        <div className="flex flex-col md:flex-row gap-5 w-full md:w-auto items-center">
                            {/* Digital signature */}
                            <div className="flex flex-col items-center gap-2">
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                    Firma Digital
                                </p>
                                {signatureMode ? (
                                    <div className="relative">
                                        <canvas
                                            ref={canvasRef}
                                            width={200}
                                            height={80}
                                            className="border-2 border-[#002046]/30 rounded-lg bg-white cursor-crosshair"
                                            onMouseDown={startDrawing}
                                            onMouseMove={draw}
                                            onMouseUp={stopDrawing}
                                            onMouseLeave={stopDrawing}
                                        />
                                        <button
                                            onClick={clearSignature}
                                            className="absolute top-1 right-1 w-5 h-5 bg-gray-100 rounded text-gray-400 hover:text-gray-600 flex items-center justify-center"
                                        >
                                            <MaterialIcon name="close" className="text-xs" />
                                        </button>
                                    </div>
                                ) : (
                                    <button
                                        onClick={() => setSignatureMode(true)}
                                        className="w-48 h-20 border-2 border-dashed border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors flex flex-col items-center justify-center gap-1 cursor-pointer"
                                    >
                                        <MaterialIcon
                                            name="draw"
                                            className="text-3xl text-gray-200"
                                        />
                                        <p className="text-[10px] text-[#002046] underline">
                                            Toque para firmar
                                        </p>
                                    </button>
                                )}
                            </div>

                            {/* Action buttons */}
                            <div className="flex flex-col gap-2">
                                {transitions.map((t) => {
                                    const isPrimary =
                                        t.status === 'completed' || t.status === 'in_progress';

                                    return (
                                        <button
                                            key={t.status}
                                            onClick={() => handleStatusChange(t.status)}
                                            disabled={statusForm.processing}
                                            className={cn(
                                                'flex items-center gap-2 px-6 py-3 rounded-lg text-xs font-extrabold tracking-widest uppercase transition-all disabled:opacity-50',
                                                isPrimary
                                                    ? 'bg-[#002046] text-white shadow-lg shadow-[#002046]/20 hover:scale-[1.02] active:scale-95'
                                                    : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50',
                                            )}
                                        >
                                            {t.label}
                                            {isPrimary && (
                                                <MaterialIcon name="send" className="text-sm" />
                                            )}
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    </section>
                )}

                {/* ── Closed state banner ── */}
                {isClosed && (
                    <section
                        className={cn(
                            'p-6 rounded-lg flex items-center gap-4',
                            workOrder.status === 'completed'
                                ? 'bg-green-50 border border-green-200'
                                : 'bg-gray-100 border border-gray-200',
                        )}
                    >
                        <MaterialIcon
                            name={workOrder.status === 'completed' ? 'check_circle' : 'cancel'}
                            fill
                            className={cn(
                                'text-3xl',
                                workOrder.status === 'completed'
                                    ? 'text-green-600'
                                    : 'text-gray-400',
                            )}
                        />
                        <div>
                            <p
                                className={cn(
                                    'font-bold',
                                    workOrder.status === 'completed'
                                        ? 'text-green-700'
                                        : 'text-gray-600',
                                )}
                            >
                                {workOrder.status === 'completed'
                                    ? 'Orden Completada'
                                    : 'Orden Cancelada'}
                            </p>
                            <p className="text-xs text-gray-500 mt-0.5">
                                {workOrder.completed_at
                                    ? `Cerrada el ${formatDate(workOrder.completed_at)}`
                                    : ''}
                                {workOrder.actual_duration
                                    ? ` • Duración real: ${formatDuration(workOrder.actual_duration)}`
                                    : ''}
                            </p>
                            {workOrder.resolution_notes && (
                                <p className="text-xs text-gray-600 mt-1 italic">
                                    &ldquo;{workOrder.resolution_notes}&rdquo;
                                </p>
                            )}
                        </div>
                    </section>
                )}
            </div>
        </CmmsLayout>
    );
}
