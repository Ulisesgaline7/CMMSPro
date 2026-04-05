import {
    AbsoluteFill,
    interpolate,
    spring,
    useCurrentFrame,
    useVideoConfig,
} from 'remotion';

// ── Types ───────────────────────────────────────────────────────────────────

export interface SaaSMetricsProps {
    mrr?: number;
    totalTenants?: number;
    activeTenants?: number;
    totalUsers?: number;
    newTenantsThisMonth?: number;
    churnCount?: number;
    trialCount?: number;
    pastDueCount?: number;
}

// ── Helpers ─────────────────────────────────────────────────────────────────

function Counter({ target, frame, delay, fps, prefix = '', suffix = '' }: {
    target: number;
    frame: number;
    delay: number;
    fps: number;
    prefix?: string;
    suffix?: string;
}) {
    const p = spring({ frame: Math.max(0, frame - delay), fps, config: { damping: 50, stiffness: 55 } });
    const val = Math.round(interpolate(p, [0, 1], [0, target]));
    return <>{prefix}{val.toLocaleString('es-MX')}{suffix}</>;
}

function MetricCard({ label, value, prefix, suffix, color, accent, frame, delay, fps, note }: {
    label: string;
    value: number;
    prefix?: string;
    suffix?: string;
    color: string;
    accent: string;
    frame: number;
    delay: number;
    fps: number;
    note?: string;
}) {
    const slide = spring({ frame: Math.max(0, frame - delay), fps, config: { damping: 35, stiffness: 70 } });
    const y = interpolate(slide, [0, 1], [40, 0]);
    const opacity = interpolate(slide, [0, 0.15], [0, 1]);

    return (
        <div style={{
            background: 'rgba(255,255,255,0.05)',
            border: `1px solid ${accent}33`,
            borderRadius: 20,
            padding: '28px 32px',
            transform: `translateY(${y}px)`,
            opacity,
            display: 'flex',
            flexDirection: 'column',
            gap: 10,
            position: 'relative',
            overflow: 'hidden',
        }}>
            {/* Glow */}
            <div style={{
                position: 'absolute', top: -30, right: -30,
                width: 120, height: 120,
                background: `radial-gradient(circle, ${accent}22 0%, transparent 70%)`,
                borderRadius: '50%',
            }} />
            <p style={{ color: 'rgba(255,255,255,0.45)', fontSize: 11, fontWeight: 700, letterSpacing: 3, textTransform: 'uppercase' }}>
                {label}
            </p>
            <p style={{ fontSize: 46, fontWeight: 900, color, lineHeight: 1, fontVariantNumeric: 'tabular-nums' }}>
                <Counter target={value} frame={frame} delay={delay + 8} fps={fps} prefix={prefix} suffix={suffix} />
            </p>
            {note && (
                <p style={{ color: 'rgba(255,255,255,0.3)', fontSize: 11, fontWeight: 500, marginTop: 2 }}>{note}</p>
            )}
        </div>
    );
}

function TinyBadge({ label, value, color, frame, delay, fps }: {
    label: string;
    value: number;
    color: string;
    frame: number;
    delay: number;
    fps: number;
}) {
    const p = spring({ frame: Math.max(0, frame - delay), fps, config: { damping: 40, stiffness: 80 } });
    const opacity = interpolate(p, [0, 0.1], [0, 1]);
    const scale = interpolate(p, [0, 1], [0.8, 1]);

    return (
        <div style={{
            display: 'flex', alignItems: 'center', gap: 10,
            background: 'rgba(255,255,255,0.06)',
            border: '1px solid rgba(255,255,255,0.1)',
            borderRadius: 12, padding: '12px 18px',
            opacity, transform: `scale(${scale})`,
        }}>
            <div style={{ width: 8, height: 8, borderRadius: '50%', background: color }} />
            <span style={{ color: 'rgba(255,255,255,0.5)', fontSize: 11, fontWeight: 600 }}>{label}</span>
            <span style={{ color, fontSize: 18, fontWeight: 900, marginLeft: 'auto' }}>
                <Counter target={value} frame={frame} delay={delay + 5} fps={fps} />
            </span>
        </div>
    );
}

// ── Main ────────────────────────────────────────────────────────────────────

export function SaaSMetrics({
    mrr = 0,
    totalTenants = 0,
    activeTenants = 0,
    totalUsers = 0,
    newTenantsThisMonth = 0,
    churnCount = 0,
    trialCount = 0,
    pastDueCount = 0,
}: SaaSMetricsProps) {
    const frame = useCurrentFrame();
    const { fps } = useVideoConfig();

    const headerY = interpolate(
        spring({ frame, fps, config: { damping: 28, stiffness: 60 } }),
        [0, 1], [25, 0]
    );
    const headerOpacity = interpolate(frame, [0, 18], [0, 1], { extrapolateRight: 'clamp' });
    const lineW = interpolate(frame, [12, 50], [0, 100], { extrapolateRight: 'clamp' });

    return (
        <AbsoluteFill style={{
            background: 'linear-gradient(135deg, #0a0a1a 0%, #0d1b3e 35%, #1a0a3e 70%, #0a0a1a 100%)',
            fontFamily: "'Inter', 'Segoe UI', sans-serif",
            overflow: 'hidden',
        }}>
            {/* Grid */}
            <svg style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', opacity: 0.05 }}>
                <defs>
                    <pattern id="sg" width="50" height="50" patternUnits="userSpaceOnUse">
                        <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#a78bfa" strokeWidth="0.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#sg)" />
            </svg>

            {/* Purple glow */}
            <div style={{
                position: 'absolute', top: -80, left: '30%',
                width: 500, height: 300,
                background: 'radial-gradient(ellipse, rgba(139,92,246,0.12) 0%, transparent 70%)',
            }} />
            {/* Green glow (MRR) */}
            <div style={{
                position: 'absolute', bottom: -60, right: '10%',
                width: 300, height: 300,
                background: 'radial-gradient(circle, rgba(34,197,94,0.10) 0%, transparent 70%)',
                borderRadius: '50%',
            }} />

            <div style={{ padding: '36px 56px', display: 'flex', flexDirection: 'column', gap: 28, height: '100%' }}>

                {/* Header */}
                <div style={{ transform: `translateY(${headerY}px)`, opacity: headerOpacity, display: 'flex', alignItems: 'center', gap: 16 }}>
                    <div style={{
                        background: 'linear-gradient(135deg, #7c3aed, #4f46e5)',
                        borderRadius: 12, padding: '7px 16px',
                        fontSize: 12, fontWeight: 900, color: 'white', letterSpacing: 2.5,
                    }}>
                        SUPER ADMIN
                    </div>
                    <div style={{
                        height: 1, width: `${lineW}%`,
                        background: 'linear-gradient(90deg, rgba(139,92,246,0.6), transparent)',
                        maxWidth: 250,
                    }} />
                    <h1 style={{ fontSize: 28, fontWeight: 900, color: 'white', margin: 0, letterSpacing: -0.5, marginLeft: 8 }}>
                        Panel de Negocio
                    </h1>
                </div>

                {/* Big metrics */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 18, flex: 1 }}>
                    <MetricCard
                        label="MRR"
                        value={mrr}
                        prefix="$"
                        color="#22c55e"
                        accent="#22c55e"
                        frame={frame} delay={15} fps={fps}
                        note="Ingreso mensual recurrente"
                    />
                    <MetricCard
                        label="Clientes Activos"
                        value={activeTenants}
                        color="#a78bfa"
                        accent="#a78bfa"
                        frame={frame} delay={25} fps={fps}
                        note={`de ${totalTenants} total`}
                    />
                    <MetricCard
                        label="Usuarios"
                        value={totalUsers}
                        color="#38bdf8"
                        accent="#38bdf8"
                        frame={frame} delay={35} fps={fps}
                        note="en toda la plataforma"
                    />
                    <div style={{
                        background: 'rgba(255,255,255,0.04)',
                        border: '1px solid rgba(255,255,255,0.08)',
                        borderRadius: 20, padding: '20px 22px',
                        display: 'flex', flexDirection: 'column', gap: 10,
                    }}>
                        <p style={{ color: 'rgba(255,255,255,0.4)', fontSize: 11, fontWeight: 700, letterSpacing: 3, textTransform: 'uppercase', marginBottom: 4 }}>
                            Estado
                        </p>
                        <TinyBadge label="Nuevos" value={newTenantsThisMonth} color="#22c55e" frame={frame} delay={40} fps={fps} />
                        <TinyBadge label="Trial"  value={trialCount}           color="#38bdf8" frame={frame} delay={50} fps={fps} />
                        <TinyBadge label="Vencidos" value={pastDueCount}       color="#f59e0b" frame={frame} delay={60} fps={fps} />
                        <TinyBadge label="Churn"  value={churnCount}           color="#ef4444" frame={frame} delay={70} fps={fps} />
                    </div>
                </div>

            </div>
        </AbsoluteFill>
    );
}
