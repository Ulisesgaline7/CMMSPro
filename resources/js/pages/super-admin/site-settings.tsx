import { useState } from 'react';
import { router } from '@inertiajs/react';
import SuperAdminLayout from '@/layouts/super-admin-layout';
import { cn } from '@/lib/utils';

// ─── Types ────────────────────────────────────────────────────────────────────

interface SettingRow {
    id: number;
    key: string;
    label: string;
    type: 'text' | 'textarea' | 'color' | 'image' | 'boolean';
    group: string;
    value: string | null;
}

interface Props {
    settings: Record<string, Record<string, SettingRow>>;
    groups: Record<string, string>;
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

function MaterialIcon({ name, className, style }: { name: string; className?: string; style?: React.CSSProperties }) {
    return <span className={cn('material-symbols-outlined select-none', className)} style={style}>{name}</span>;
}

const GROUP_ICONS: Record<string, string> = {
    branding: 'palette',
    hero:     'web_asset',
    stats:    'bar_chart',
    features: 'featured_play_list',
    contact:  'contact_mail',
};

// ─── Field Components ─────────────────────────────────────────────────────────

function TextField({ setting, value, onChange }: { setting: SettingRow; value: string; onChange: (v: string) => void }) {
    return (
        <div className="flex flex-col gap-1.5">
            <label className="text-[11px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.5)' }}>
                {setting.label}
            </label>
            <input
                type="text"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                className="w-full rounded-lg px-3 py-2 text-sm text-white outline-none transition-all"
                style={{
                    background: 'rgba(255,255,255,0.04)',
                    border: '1px solid rgba(139,92,246,0.15)',
                }}
                onFocus={(e) => (e.target.style.borderColor = 'rgba(124,58,237,0.6)')}
                onBlur={(e) => (e.target.style.borderColor = 'rgba(139,92,246,0.15)')}
            />
        </div>
    );
}

function TextareaField({ setting, value, onChange }: { setting: SettingRow; value: string; onChange: (v: string) => void }) {
    return (
        <div className="flex flex-col gap-1.5">
            <label className="text-[11px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.5)' }}>
                {setting.label}
            </label>
            <textarea
                rows={3}
                value={value}
                onChange={(e) => onChange(e.target.value)}
                className="w-full rounded-lg px-3 py-2 text-sm text-white outline-none transition-all resize-none"
                style={{
                    background: 'rgba(255,255,255,0.04)',
                    border: '1px solid rgba(139,92,246,0.15)',
                }}
                onFocus={(e) => (e.target.style.borderColor = 'rgba(124,58,237,0.6)')}
                onBlur={(e) => (e.target.style.borderColor = 'rgba(139,92,246,0.15)')}
            />
        </div>
    );
}

function ColorField({ setting, value, onChange }: { setting: SettingRow; value: string; onChange: (v: string) => void }) {
    return (
        <div className="flex flex-col gap-1.5">
            <label className="text-[11px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.5)' }}>
                {setting.label}
            </label>
            <div className="flex items-center gap-3">
                <input
                    type="color"
                    value={value || '#000000'}
                    onChange={(e) => onChange(e.target.value)}
                    className="w-10 h-10 rounded-lg cursor-pointer border-0 bg-transparent"
                />
                <input
                    type="text"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    placeholder="#000000"
                    className="flex-1 rounded-lg px-3 py-2 text-sm text-white outline-none transition-all font-mono"
                    style={{
                        background: 'rgba(255,255,255,0.04)',
                        border: '1px solid rgba(139,92,246,0.15)',
                    }}
                    onFocus={(e) => (e.target.style.borderColor = 'rgba(124,58,237,0.6)')}
                    onBlur={(e) => (e.target.style.borderColor = 'rgba(139,92,246,0.15)')}
                />
                {value && (
                    <div className="w-8 h-8 rounded-md border border-white/10 shrink-0" style={{ background: value }} />
                )}
            </div>
        </div>
    );
}

function ImageField({ setting, value, onChange }: { setting: SettingRow; value: string; onChange: (v: string) => void }) {
    return (
        <div className="flex flex-col gap-1.5">
            <label className="text-[11px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.5)' }}>
                {setting.label}
            </label>
            <div className="flex items-center gap-3">
                <input
                    type="text"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    placeholder="https://..."
                    className="flex-1 rounded-lg px-3 py-2 text-sm text-white outline-none transition-all"
                    style={{
                        background: 'rgba(255,255,255,0.04)',
                        border: '1px solid rgba(139,92,246,0.15)',
                    }}
                    onFocus={(e) => (e.target.style.borderColor = 'rgba(124,58,237,0.6)')}
                    onBlur={(e) => (e.target.style.borderColor = 'rgba(139,92,246,0.15)')}
                />
                {value && (
                    <img src={value} alt="preview" className="w-10 h-10 rounded-md object-cover border border-white/10" />
                )}
            </div>
        </div>
    );
}

function SettingField({ setting, value, onChange }: { setting: SettingRow; value: string; onChange: (v: string) => void }) {
    if (setting.type === 'textarea') return <TextareaField setting={setting} value={value} onChange={onChange} />;
    if (setting.type === 'color')    return <ColorField    setting={setting} value={value} onChange={onChange} />;
    if (setting.type === 'image')    return <ImageField    setting={setting} value={value} onChange={onChange} />;
    return <TextField setting={setting} value={value} onChange={onChange} />;
}

// ─── Main Page ────────────────────────────────────────────────────────────────

export default function SiteSettings({ settings, groups }: Props) {
    const groupKeys = Object.keys(groups);
    const [activeGroup, setActiveGroup] = useState(groupKeys[0] ?? 'branding');
    const [saving, setSaving] = useState(false);
    const [saved, setSaved] = useState(false);

    // Flatten all settings into a mutable state: key → value
    const initialValues = Object.values(settings).flatMap((group) =>
        Object.values(group).map((s) => [s.key, s.value ?? ''])
    );
    const [values, setValues] = useState<Record<string, string>>(Object.fromEntries(initialValues));

    function handleChange(key: string, value: string) {
        setValues((prev) => ({ ...prev, [key]: value }));
    }

    function handleSave() {
        setSaving(true);
        const payload = Object.entries(values).map(([key, value]) => ({ key, value }));
        router.post(
            '/super-admin/site-settings',
            { settings: payload },
            {
                onSuccess: () => {
                    setSaved(true);
                    setTimeout(() => setSaved(false), 3000);
                },
                onFinish: () => setSaving(false),
            },
        );
    }

    const groupSettings = settings[activeGroup] ? Object.values(settings[activeGroup]) : [];

    return (
        <SuperAdminLayout title="Landing Page" headerTitle="Configuración del sitio">
            <div className="px-8 py-6 space-y-6">

                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-lg font-extrabold text-white tracking-tight">Landing Page</h2>
                        <p className="text-xs mt-0.5" style={{ color: 'rgba(255,255,255,0.4)' }}>
                            Personaliza el contenido y la apariencia de tu página de inicio pública.
                        </p>
                    </div>
                    <a
                        href="/"
                        target="_blank"
                        rel="noopener noreferrer"
                        className="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
                        style={{ background: 'rgba(255,255,255,0.05)', color: 'rgba(255,255,255,0.6)', border: '1px solid rgba(255,255,255,0.1)' }}
                    >
                        <MaterialIcon name="open_in_new" className="text-[16px]" />
                        Ver landing
                    </a>
                    <button
                        onClick={handleSave}
                        disabled={saving}
                        className="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
                        style={{
                            background: saving ? 'rgba(124,58,237,0.3)' : saved ? 'rgba(34,197,94,0.15)' : 'linear-gradient(135deg, #7c3aed, #4f46e5)',
                            color: saved ? '#22c55e' : 'white',
                            border: saved ? '1px solid rgba(34,197,94,0.3)' : 'none',
                        }}
                    >
                        <MaterialIcon name={saved ? 'check_circle' : saving ? 'sync' : 'save'} className={cn('text-[16px]', saving && 'animate-spin')} />
                        {saved ? 'Guardado' : saving ? 'Guardando…' : 'Guardar cambios'}
                    </button>
                </div>

                {/* Layout */}
                <div className="flex gap-6">

                    {/* Sidebar tabs */}
                    <aside className="w-48 shrink-0 flex flex-col gap-1">
                        {groupKeys.map((key) => {
                            const isActive = key === activeGroup;
                            return (
                                <button
                                    key={key}
                                    onClick={() => setActiveGroup(key)}
                                    className="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left text-[11px] font-bold uppercase tracking-widest transition-all"
                                    style={isActive
                                        ? { background: 'rgba(124,58,237,0.15)', color: 'white', borderLeft: '2px solid #7c3aed' }
                                        : { color: 'rgba(255,255,255,0.4)' }
                                    }
                                >
                                    <MaterialIcon name={GROUP_ICONS[key] ?? 'settings'} className="text-[16px]"
                                        style={{ color: isActive ? '#a78bfa' : 'rgba(255,255,255,0.3)' }} />
                                    {groups[key]}
                                </button>
                            );
                        })}
                    </aside>

                    {/* Fields panel */}
                    <div
                        className="flex-1 rounded-xl p-6 space-y-5"
                        style={{ background: 'rgba(255,255,255,0.02)', border: '1px solid rgba(139,92,246,0.1)' }}
                    >
                        <div className="flex items-center gap-2 mb-1">
                            <MaterialIcon name={GROUP_ICONS[activeGroup] ?? 'settings'} className="text-[20px]" style={{ color: '#a78bfa' }} />
                            <h3 className="text-sm font-extrabold text-white tracking-wide">{groups[activeGroup]}</h3>
                        </div>

                        {groupSettings.length === 0 && (
                            <p className="text-xs" style={{ color: 'rgba(255,255,255,0.3)' }}>Sin campos en este grupo.</p>
                        )}

                        {groupSettings.map((setting) => (
                            <SettingField
                                key={setting.key}
                                setting={setting}
                                value={values[setting.key] ?? ''}
                                onChange={(v) => handleChange(setting.key, v)}
                            />
                        ))}
                    </div>

                </div>

                {/* Live preview hint */}
                <div
                    className="flex items-center gap-3 px-4 py-3 rounded-lg"
                    style={{ background: 'rgba(139,92,246,0.05)', border: '1px solid rgba(139,92,246,0.1)' }}
                >
                    <MaterialIcon name="info" className="text-[18px] shrink-0" style={{ color: '#a78bfa' }} />
                    <p className="text-[11px]" style={{ color: 'rgba(255,255,255,0.45)' }}>
                        Los cambios se reflejan en la <strong className="text-white/60">página de inicio pública</strong> al guardar.
                        Visita <span className="font-mono text-purple-400">/</span> para previsualizar.
                    </p>
                </div>

            </div>
        </SuperAdminLayout>
    );
}
