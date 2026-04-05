import { useForm, Link } from '@inertiajs/react';
import CmmsLayout from '@/layouts/cmms-layout';
import { cn } from '@/lib/utils';

// ─── Types ────────────────────────────────────────────────────────────────────

interface Location {
    id: number;
    name: string;
    type: string;
}

interface AssetCategory {
    id: number;
    name: string;
}

interface AssetParent {
    id: number;
    name: string;
    code: string;
}

interface Props {
    locations: Location[];
    categories: AssetCategory[];
    parents: AssetParent[];
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

export default function CreateAsset({ locations, categories, parents }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        code: '',
        serial_number: '',
        brand: '',
        model: '',
        manufacture_year: '',
        purchase_date: '',
        installation_date: '',
        warranty_expires_at: '',
        purchase_cost: '',
        status: 'active',
        criticality: 'medium',
        location_id: '',
        asset_category_id: '',
        parent_id: '',
        notes: '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/assets');
    }

    const statusOptions = [
        { value: 'active', label: 'Activo', color: 'border-green-200 bg-green-50 text-green-700' },
        { value: 'inactive', label: 'Inactivo', color: 'border-gray-200 bg-gray-50 text-gray-600' },
        { value: 'under_maintenance', label: 'En Mantenimiento', color: 'border-yellow-200 bg-yellow-50 text-yellow-700' },
        { value: 'retired', label: 'Dado de Baja', color: 'border-red-200 bg-red-50 text-red-700' },
    ];

    const criticalityOptions = [
        { value: 'low', label: 'Baja', dot: 'bg-gray-300' },
        { value: 'medium', label: 'Media', dot: 'bg-blue-400' },
        { value: 'high', label: 'Alta', dot: 'bg-orange-400' },
        { value: 'critical', label: 'Crítica', dot: 'bg-red-500' },
    ];

    return (
        <CmmsLayout title="Nuevo Activo" headerTitle="Nuevo Activo">
            <div className="p-8 max-w-4xl mx-auto">
                {/* Header */}
                <div className="flex items-center gap-4 mb-8">
                    <Link
                        href="/assets"
                        className="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600"
                    >
                        <MaterialIcon name="arrow_back" className="text-xl" />
                    </Link>
                    <div>
                        <h2 className="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">
                            Nuevo Activo
                        </h2>
                        <p className="text-sm text-gray-400 mt-0.5">Completa los campos para registrar el activo</p>
                    </div>
                </div>

                <form onSubmit={submit} className="space-y-6">
                    {/* ── Identificación ───────────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">Identificación</h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <Label required>Nombre</Label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="Ej: Compresor de Aire Línea 3"
                                    className={cn(
                                        'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]',
                                        errors.name ? 'border-red-300' : 'border-gray-200',
                                    )}
                                />
                                <FieldError message={errors.name} />
                            </div>

                            <div>
                                <Label required>Código</Label>
                                <input
                                    type="text"
                                    value={data.code}
                                    onChange={(e) => setData('code', e.target.value)}
                                    placeholder="Ej: COMP-001"
                                    className={cn(
                                        'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] font-mono',
                                        errors.code ? 'border-red-300' : 'border-gray-200',
                                    )}
                                />
                                <FieldError message={errors.code} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <Label>Número de Serie</Label>
                                <input
                                    type="text"
                                    value={data.serial_number}
                                    onChange={(e) => setData('serial_number', e.target.value)}
                                    placeholder="Ej: SN-12345AB"
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.serial_number} />
                            </div>

                            <div>
                                <Label>Categoría</Label>
                                <select
                                    value={data.asset_category_id}
                                    onChange={(e) => setData('asset_category_id', e.target.value)}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white"
                                >
                                    <option value="">Sin categoría</option>
                                    {categories.map((c) => (
                                        <option key={c.id} value={c.id}>
                                            {c.name}
                                        </option>
                                    ))}
                                </select>
                                <FieldError message={errors.asset_category_id} />
                            </div>
                        </div>
                    </div>

                    {/* ── Marca y Modelo ────────────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">Fabricante</h3>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <Label>Marca</Label>
                                <input
                                    type="text"
                                    value={data.brand}
                                    onChange={(e) => setData('brand', e.target.value)}
                                    placeholder="Ej: Siemens"
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.brand} />
                            </div>

                            <div>
                                <Label>Modelo</Label>
                                <input
                                    type="text"
                                    value={data.model}
                                    onChange={(e) => setData('model', e.target.value)}
                                    placeholder="Ej: 1LE1503"
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.model} />
                            </div>

                            <div>
                                <Label>Año de Fabricación</Label>
                                <input
                                    type="number"
                                    value={data.manufacture_year}
                                    onChange={(e) => setData('manufacture_year', e.target.value)}
                                    placeholder={String(new Date().getFullYear())}
                                    min={1900}
                                    max={new Date().getFullYear() + 1}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.manufacture_year} />
                            </div>
                        </div>
                    </div>

                    {/* ── Ubicación y Jerarquía ─────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">Ubicación y Jerarquía</h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <Label>Ubicación</Label>
                                <select
                                    value={data.location_id}
                                    onChange={(e) => setData('location_id', e.target.value)}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white"
                                >
                                    <option value="">Sin ubicación</option>
                                    {locations.map((l) => (
                                        <option key={l.id} value={l.id}>
                                            {l.name}
                                        </option>
                                    ))}
                                </select>
                                <FieldError message={errors.location_id} />
                            </div>

                            <div>
                                <Label>Activo Padre</Label>
                                <select
                                    value={data.parent_id}
                                    onChange={(e) => setData('parent_id', e.target.value)}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white"
                                >
                                    <option value="">Sin activo padre</option>
                                    {parents.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            [{p.code}] {p.name}
                                        </option>
                                    ))}
                                </select>
                                <FieldError message={errors.parent_id} />
                            </div>
                        </div>
                    </div>

                    {/* ── Estado y Criticidad ───────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">Estado y Criticidad</h3>

                        {/* Estado */}
                        <div>
                            <Label required>Estado</Label>
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                                {statusOptions.map((opt) => (
                                    <button
                                        key={opt.value}
                                        type="button"
                                        onClick={() => setData('status', opt.value)}
                                        className={cn(
                                            'py-2.5 px-3 rounded-lg border-2 text-xs font-bold transition-all',
                                            data.status === opt.value
                                                ? opt.color + ' border-current'
                                                : 'border-gray-100 text-gray-400 hover:border-gray-200',
                                        )}
                                    >
                                        {opt.label}
                                    </button>
                                ))}
                            </div>
                            <FieldError message={errors.status} />
                        </div>

                        {/* Criticidad */}
                        <div>
                            <Label required>Criticidad</Label>
                            <div className="flex gap-2">
                                {criticalityOptions.map((opt) => (
                                    <button
                                        key={opt.value}
                                        type="button"
                                        onClick={() => setData('criticality', opt.value)}
                                        className={cn(
                                            'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg border-2 text-xs font-bold transition-all',
                                            data.criticality === opt.value
                                                ? 'border-[#002046] bg-[#002046] text-white'
                                                : 'border-gray-100 text-gray-500 hover:border-gray-200',
                                        )}
                                    >
                                        <span className={cn('w-2 h-2 rounded-full', opt.dot)} />
                                        {opt.label}
                                    </button>
                                ))}
                            </div>
                            <FieldError message={errors.criticality} />
                        </div>
                    </div>

                    {/* ── Fechas y Costo ────────────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400">Fechas y Costo</h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <Label>Fecha de Compra</Label>
                                <input
                                    type="date"
                                    value={data.purchase_date}
                                    onChange={(e) => setData('purchase_date', e.target.value)}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.purchase_date} />
                            </div>

                            <div>
                                <Label>Fecha de Instalación</Label>
                                <input
                                    type="date"
                                    value={data.installation_date}
                                    onChange={(e) => setData('installation_date', e.target.value)}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.installation_date} />
                            </div>

                            <div>
                                <Label>Garantía hasta</Label>
                                <input
                                    type="date"
                                    value={data.warranty_expires_at}
                                    onChange={(e) => setData('warranty_expires_at', e.target.value)}
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.warranty_expires_at} />
                            </div>

                            <div>
                                <Label>Costo de Compra</Label>
                                <input
                                    type="number"
                                    value={data.purchase_cost}
                                    onChange={(e) => setData('purchase_cost', e.target.value)}
                                    placeholder="Ej: 25000.00"
                                    min={0}
                                    step="0.01"
                                    className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]"
                                />
                                <FieldError message={errors.purchase_cost} />
                            </div>
                        </div>
                    </div>

                    {/* ── Notas ─────────────────────────────────────────── */}
                    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h3 className="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Notas</h3>
                        <textarea
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                            rows={3}
                            placeholder="Observaciones adicionales sobre el activo..."
                            className="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none"
                        />
                        <FieldError message={errors.notes} />
                    </div>

                    {/* ── Actions ───────────────────────────────────────── */}
                    <div className="flex items-center justify-between pt-2">
                        <Link
                            href="/assets"
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
                                    Crear Activo
                                </>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </CmmsLayout>
    );
}
