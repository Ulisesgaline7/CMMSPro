import {
    AbsoluteFill,
    interpolate,
    spring,
    useCurrentFrame,
    useVideoConfig,
} from 'remotion';

// ── Types ───────────────────────────────────────────────────────────────────

export interface DashboardHeroProps {
    totalOt?: number;
    pending?: number;
    inProgress?: number;
    completedToday?: number;
    overdue?: number;
    totalAssets?: number;
    activeAssets?: number;
    mtbf?: number;
    mttr?: number;
    oee?: number;
    companyName?: string;
    userRole?: string;
    userName?: string;
}

// ── Animated counter ────────────────────────────────────────────────────────

function Counter({ target, frame, delay, fps }: { target: number; frame: number; delay: number; fps: number }) {
    const p = spring({ frame: Math.max(0, frame - delay), fps, config: { damping: 50, stiffness: 60 } });
    return <>{Math.round(interpolate(p, [0, 1], [0, target]))}</>;
}

// ── Animated ring ───────────────────────────────────────────────────────────

function Ring({ value, max, color, label, frame, delay, fps }: {
    value: number;
    max: number;
    color: string;
    label: string;
    frame: number;
    delay: number;
    fps: number;
}) {
    const r = 38;
    const circ = 2 * Math.PI * r;
    const p = spring({ frame: Math.max(0, frame - delay), fps, config: { damping: 40, stiffness: 50 } });
    const pct = Math.min(value / max, 1);
    const dash = interpolate(p, [0, 1], [0, pct * circ]);
    const opacity = interpolate(Math.max(0, frame - delay), [0, 15], [0, 1], { extrapolateRight: 'clamp' });

    return (
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 8, opacity }}>
            <svg width={96} height={96} viewBox="0 0 96 96">
                <circle cx={48} cy={48} r={r} fill="none" stroke="rgba(255,255,255,0.1)" strokeWidth={8} />
                <circle
                    cx={48} cy={48} r={r} fill="none"
                    stroke={color} strokeWidth={8}
                    strokeDasharray={`${dash} ${circ - dash}`}
                    strokeLinecap="round"
                    transform="rotate(-90 48 48)"
                />
                <text x={48} y={44} textAnchor="middle" fill="white" fontSize={14} fontWeight={800}>{value}</text>
                <text x={48} y={58} textAnchor="middle" fill="rgba(255,255,255,0.5)" fontSize={8} fontWeight={600}>{label}</text>
            </svg>
        </div>
    );
}

// ── KPI card ────────────────────────────────────────────────────────────────

function KpiCard({ label, value, color, icon, frame, delay, fps }: {
    label: string;
    value: number;
    color: string;
    icon: string;
    frame: number;
    delay: number;
    fps: number;
}) {
    const slideUp = spring({ frame: Math.max(0, frame - delay), fps, config: { damping: 35, stiffness: 80 } });
    const y = interpolate(slideUp, [0, 1], [30, 0]);
    const opacity = interpolate(slideUp, [0, 0.1], [0, 1]);

    return (
        <div style={{
            background: 'rgba(255,255,255,0.07)',
            border: '1px solid rgba(255,255,255,0.12)',
            borderRadius: 16,
            padding: '20px 24px',
            display: 'flex',
            flexDirection: 'column',
            gap: 6,
            transform: `translateY(${y}px)`,
            opacity,
            backdropFilter: 'blur(10px)',
        }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                <div style={{
                    width: 36, height: 36, borderRadius: 10,
                    background: color + '22',
                    border: `1px solid ${color}44`,
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    fontSize: 18,
                }}>
                    {icon}
                </div>
                <span style={{ color: 'rgba(255,255,255,0.5)', fontSize: 10, fontWeight: 700, letterSpacing: 2, textTransform: 'uppercase' }}>
                    {label}
                </span>
            </div>
            <div style={{ fontSize: 40, fontWeight: 900, color, lineHeight: 1, fontVariantNumeric: 'tabular-nums' }}>
                <Counter target={value} frame={frame} delay={delay + 5} fps={fps} />
            </div>
        </div>
    );
}

// ── Main composition ────────────────────────────────────────────────────────

export function DashboardHero({
    totalOt = 0,
    pending = 0,
    inProgress = 0,
    completedToday = 0,
    overdue = 0,
    totalAssets = 0,
    activeAssets = 0,
    mtbf = 0,
    mttr = 0,
    oee = 0,
    companyName = 'CMMS Pro',
    userRole = 'admin',
    userName = 'Usuario',
}: DashboardHeroProps) {
    const frame = useCurrentFrame();
    const { fps } = useVideoConfig();

    // Title animation
    const titleScale = spring({ frame, fps, config: { damping: 30, stiffness: 60 } });
    const titleY = interpolate(titleScale, [0, 1], [20, 0]);
    const titleOpacity = interpolate(frame, [0, 20], [0, 1], { extrapolateRight: 'clamp' });

    // Subtitle
    const subOpacity = interpolate(frame, [15, 35], [0, 1], { extrapolateRight: 'clamp' });

    // Divider line
    const lineWidth = interpolate(frame, [20, 55], [0, 100], { extrapolateRight: 'clamp' });

    return (
        <AbsoluteFill style={{
            background: 'linear-gradient(135deg, #001830 0%, #002046 40%, #003070 70%, #001830 100%)',
            fontFamily: "'Inter', 'Segoe UI', sans-serif",
            overflow: 'hidden',
        }}>
            {/* Grid pattern */}
            <svg style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', opacity: 0.04 }}>
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" strokeWidth="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>

            {/* Orange glow */}
            <div style={{
                position: 'absolute', top: -100, right: 200,
                width: 400, height: 400,
                background: 'radial-gradient(circle, rgba(234,88,12,0.15) 0%, transparent 70%)',
                borderRadius: '50%',
            }} />

            {/* Blue glow */}
            <div style={{
                position: 'absolute', bottom: -80, left: 100,
                width: 300, height: 300,
                background: 'radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%)',
                borderRadius: '50%',
            }} />

            <div style={{ padding: '40px 60px', display: 'flex', flexDirection: 'column', gap: 32, height: '100%' }}>

                {/* Header */}
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                    <div>
                        <div style={{
                            transform: `translateY(${titleY}px)`,
                            opacity: titleOpacity,
                        }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: 14, marginBottom: 8 }}>
                                <div style={{
                                    background: 'linear-gradient(135deg, #ea580c, #f97316)',
                                    borderRadius: 12, padding: '8px 16px',
                                    fontSize: 13, fontWeight: 900, color: 'white',
                                    letterSpacing: 2, textTransform: 'uppercase',
                                }}>
                                    CMMS Pro
                                </div>
                                <div style={{
                                    height: 1, width: `${lineWidth}%`,
                                    background: 'linear-gradient(90deg, rgba(255,255,255,0.3), transparent)',
                                    maxWidth: 200,
                                }} />
                            </div>
                            <h1 style={{
                                fontSize: 36, fontWeight: 900, color: 'white',
                                margin: 0, lineHeight: 1.1, letterSpacing: -1,
                            }}>
                                Panel de Control
                            </h1>
                            <p style={{ opacity: subOpacity, color: 'rgba(255,255,255,0.5)', fontSize: 14, marginTop: 6, fontWeight: 500 }}>
                                {companyName} · {userRole === 'admin' ? 'Administrador' : userRole === 'supervisor' ? 'Supervisor' : userRole === 'technician' ? 'Técnico' : 'Lector'}
                            </p>
                        </div>
                    </div>

                    {/* Reliability rings */}
                    <div style={{ display: 'flex', gap: 24, alignItems: 'center' }}>
                        <Ring value={mtbf} max={Math.max(mtbf * 1.5, 500)} color="#22c55e" label="MTBF h" frame={frame} delay={30} fps={fps} />
                        <Ring value={mttr} max={Math.max(mttr * 2, 10)} color="#f59e0b" label="MTTR h" frame={frame} delay={40} fps={fps} />
                        <Ring value={oee} max={100} color="#3b82f6" label="OEE %" frame={frame} delay={50} fps={fps} />
                    </div>
                </div>

                {/* KPI cards */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(5, 1fr)', gap: 16, flex: 1 }}>
                    <KpiCard label="Total OT"      value={totalOt}        color="#ffffff"   icon="📋" frame={frame} delay={25} fps={fps} />
                    <KpiCard label="Pendientes"    value={pending}        color="#eab308"   icon="⏳" frame={frame} delay={35} fps={fps} />
                    <KpiCard label="En Progreso"   value={inProgress}     color="#3b82f6"   icon="⚙️" frame={frame} delay={45} fps={fps} />
                    <KpiCard label="Completadas"   value={completedToday} color="#22c55e"   icon="✅" frame={frame} delay={55} fps={fps} />
                    <KpiCard label="Vencidas"      value={overdue}        color="#ef4444"   icon="🚨" frame={frame} delay={65} fps={fps} />
                </div>

            </div>
        </AbsoluteFill>
    );
}
