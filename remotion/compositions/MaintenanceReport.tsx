import {
    AbsoluteFill,
    Img,
    interpolate,
    spring,
    useCurrentFrame,
    useVideoConfig,
} from 'remotion';

// ── Types ──────────────────────────────────────────────────────────────────

interface KpiCard {
    label: string;
    value: string;
    subtitle: string;
    color: string;
    icon: string;
}

interface WorkOrderBar {
    status: string;
    count: number;
    color: string;
}

export interface MaintenanceReportProps {
    month?: string;
    companyName?: string;
    kpis?: KpiCard[];
    workOrders?: WorkOrderBar[];
    mtbf?: string;
    mttr?: string;
    availability?: string;
}

// ── Default data ───────────────────────────────────────────────────────────

const DEFAULT_KPIS: KpiCard[] = [
    { label: 'Órdenes Completadas', value: '148', subtitle: '+12% vs mes anterior', color: '#22c55e', icon: '✓' },
    { label: 'Tiempo Promedio (h)', value: '3.2', subtitle: 'MTTR del período', color: '#3b82f6', icon: '⏱' },
    { label: 'Activos Monitoreados', value: '324', subtitle: '98% operativos', color: '#f59e0b', icon: '⚙' },
    { label: 'Alertas IoT', value: '17', subtitle: '3 críticas resueltas', color: '#ef4444', icon: '📡' },
];

const DEFAULT_WORK_ORDERS: WorkOrderBar[] = [
    { status: 'Completadas', count: 148, color: '#22c55e' },
    { status: 'Preventivas', count: 62, color: '#3b82f6' },
    { status: 'Correctivas', count: 51, color: '#f59e0b' },
    { status: 'Pendientes', count: 23, color: '#94a3b8' },
    { status: 'Canceladas', count: 12, color: '#ef4444' },
];

// ── Helper: animated number ────────────────────────────────────────────────

function AnimatedNumber({ target, frame, startFrame, fps }: { target: number; frame: number; startFrame: number; fps: number }) {
    const progress = spring({ frame: frame - startFrame, fps, config: { damping: 40, stiffness: 80 } });
    const current = Math.round(interpolate(progress, [0, 1], [0, target]));
    return <>{current}</>;
}

// ── Scene 1: Title Card ────────────────────────────────────────────────────

function TitleScene({ month, companyName, frame, fps }: { month: string; companyName: string; frame: number; fps: number }) {
    const logoScale = spring({ frame, fps, config: { damping: 20, stiffness: 60 } });
    const titleOpacity = interpolate(frame, [20, 40], [0, 1], { extrapolateRight: 'clamp' });
    const titleY = interpolate(frame, [20, 40], [30, 0], { extrapolateRight: 'clamp' });
    const subtitleOpacity = interpolate(frame, [40, 60], [0, 1], { extrapolateRight: 'clamp' });
    const lineScale = interpolate(frame, [50, 70], [0, 1], { extrapolateRight: 'clamp' });

    return (
        <AbsoluteFill style={{
            background: 'linear-gradient(135deg, #001433 0%, #002046 50%, #003580 100%)',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            justifyContent: 'center',
            fontFamily: "'Inter', sans-serif",
        }}>
            {/* Grid pattern */}
            <div style={{
                position: 'absolute', inset: 0,
                backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(255,255,255,0.08) 1px, transparent 0)',
                backgroundSize: '32px 32px',
            }} />

            {/* Orange glow */}
            <div style={{
                position: 'absolute', bottom: -100, left: -100,
                width: 500, height: 500,
                borderRadius: '50%',
                background: 'radial-gradient(circle, rgba(224,123,48,0.3), transparent)',
                filter: 'blur(60px)',
            }} />

            {/* Logo badge */}
            <div style={{
                transform: `scale(${logoScale})`,
                width: 80, height: 80,
                borderRadius: 20,
                background: 'linear-gradient(135deg, #e07b30, #c45c1a)',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                marginBottom: 32,
                boxShadow: '0 8px 32px rgba(224,123,48,0.4)',
            }}>
                <span style={{ fontSize: 36 }}>⚙</span>
            </div>

            {/* Company name */}
            <div style={{
                opacity: titleOpacity,
                transform: `translateY(${titleY}px)`,
                textAlign: 'center',
            }}>
                <div style={{
                    color: '#e07b30',
                    fontSize: 14,
                    fontWeight: 700,
                    letterSpacing: '0.3em',
                    textTransform: 'uppercase',
                    marginBottom: 12,
                }}>
                    {companyName}
                </div>
                <div style={{
                    color: 'white',
                    fontSize: 52,
                    fontWeight: 800,
                    lineHeight: 1.1,
                    letterSpacing: '-0.02em',
                }}>
                    Reporte Mensual
                </div>
                <div style={{
                    color: 'white',
                    fontSize: 52,
                    fontWeight: 800,
                    lineHeight: 1.1,
                    letterSpacing: '-0.02em',
                }}>
                    de <span style={{ color: '#e07b30' }}>Mantenimiento</span>
                </div>
            </div>

            {/* Divider line */}
            <div style={{
                width: `${lineScale * 240}px`,
                height: 2,
                background: 'linear-gradient(90deg, transparent, #e07b30, transparent)',
                margin: '32px 0',
            }} />

            {/* Month badge */}
            <div style={{
                opacity: subtitleOpacity,
                background: 'rgba(255,255,255,0.08)',
                border: '1px solid rgba(255,255,255,0.15)',
                borderRadius: 100,
                padding: '10px 28px',
                color: 'rgba(255,255,255,0.8)',
                fontSize: 18,
                fontWeight: 600,
                letterSpacing: '0.05em',
            }}>
                {month}
            </div>

            {/* CMMS Pro label */}
            <div style={{
                position: 'absolute', bottom: 40,
                color: 'rgba(255,255,255,0.3)',
                fontSize: 12,
                fontWeight: 600,
                letterSpacing: '0.2em',
                textTransform: 'uppercase',
            }}>
                CMMS Pro · Sistema de Gestión de Mantenimiento
            </div>
        </AbsoluteFill>
    );
}

// ── Scene 2: KPI Cards ─────────────────────────────────────────────────────

function KpiScene({ kpis, frame, fps }: { kpis: KpiCard[]; frame: number; fps: number }) {
    const titleOpacity = interpolate(frame, [0, 20], [0, 1], { extrapolateRight: 'clamp' });

    return (
        <AbsoluteFill style={{
            background: '#f4f5f9',
            fontFamily: "'Inter', sans-serif",
            padding: 60,
        }}>
            {/* Header */}
            <div style={{ opacity: titleOpacity, marginBottom: 48 }}>
                <div style={{ color: '#e07b30', fontSize: 12, fontWeight: 700, letterSpacing: '0.25em', textTransform: 'uppercase', marginBottom: 8 }}>
                    Indicadores Clave
                </div>
                <div style={{ color: '#002046', fontSize: 36, fontWeight: 800 }}>
                    KPIs del Período
                </div>
            </div>

            {/* Cards grid */}
            <div style={{ display: 'flex', gap: 24, flexWrap: 'wrap' }}>
                {kpis.map((kpi, i) => {
                    const cardOpacity = spring({ frame: frame - i * 12, fps, config: { damping: 30, stiffness: 100 } });
                    const cardY = interpolate(cardOpacity, [0, 1], [40, 0]);

                    return (
                        <div key={i} style={{
                            opacity: cardOpacity,
                            transform: `translateY(${cardY}px)`,
                            flex: '1 1 calc(50% - 12px)',
                            background: 'white',
                            borderRadius: 20,
                            padding: '32px 36px',
                            boxShadow: '0 4px 24px rgba(0,0,0,0.06)',
                            borderLeft: `5px solid ${kpi.color}`,
                        }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                <div>
                                    <div style={{ color: '#6b7280', fontSize: 13, fontWeight: 600, marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                                        {kpi.label}
                                    </div>
                                    <div style={{ color: '#002046', fontSize: 52, fontWeight: 800, lineHeight: 1 }}>
                                        {isNaN(parseFloat(kpi.value)) ? kpi.value : (
                                            <AnimatedNumber
                                                target={parseFloat(kpi.value)}
                                                frame={frame}
                                                startFrame={i * 12}
                                                fps={fps}
                                            />
                                        )}
                                    </div>
                                    <div style={{ color: kpi.color, fontSize: 13, fontWeight: 600, marginTop: 8 }}>
                                        {kpi.subtitle}
                                    </div>
                                </div>
                                <div style={{
                                    width: 56, height: 56,
                                    borderRadius: 14,
                                    background: `${kpi.color}18`,
                                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                                    fontSize: 26,
                                }}>
                                    {kpi.icon}
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </AbsoluteFill>
    );
}

// ── Scene 3: Work Orders Bar Chart ─────────────────────────────────────────

function WorkOrdersScene({ workOrders, frame, fps }: { workOrders: WorkOrderBar[]; frame: number; fps: number }) {
    const maxCount = Math.max(...workOrders.map(w => w.count));
    const titleOpacity = interpolate(frame, [0, 20], [0, 1], { extrapolateRight: 'clamp' });

    return (
        <AbsoluteFill style={{
            background: 'linear-gradient(135deg, #001433 0%, #002046 50%, #003580 100%)',
            fontFamily: "'Inter', sans-serif",
            padding: 60,
        }}>
            {/* Grid */}
            <div style={{
                position: 'absolute', inset: 0,
                backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(255,255,255,0.05) 1px, transparent 0)',
                backgroundSize: '32px 32px',
            }} />

            <div style={{ position: 'relative', zIndex: 1 }}>
                <div style={{ opacity: titleOpacity, marginBottom: 48 }}>
                    <div style={{ color: '#e07b30', fontSize: 12, fontWeight: 700, letterSpacing: '0.25em', textTransform: 'uppercase', marginBottom: 8 }}>
                        Resumen Operativo
                    </div>
                    <div style={{ color: 'white', fontSize: 36, fontWeight: 800 }}>
                        Órdenes de Trabajo
                    </div>
                </div>

                <div style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>
                    {workOrders.map((wo, i) => {
                        const barProgress = spring({ frame: frame - i * 10, fps, config: { damping: 40, stiffness: 60 } });
                        const barWidth = interpolate(barProgress, [0, 1], [0, (wo.count / maxCount) * 100]);
                        const numOpacity = interpolate(barProgress, [0.6, 1], [0, 1]);

                        return (
                            <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 20 }}>
                                <div style={{ color: 'rgba(255,255,255,0.6)', fontSize: 13, fontWeight: 600, width: 120, textAlign: 'right' }}>
                                    {wo.status}
                                </div>
                                <div style={{ flex: 1, background: 'rgba(255,255,255,0.08)', borderRadius: 8, height: 44, position: 'relative', overflow: 'hidden' }}>
                                    <div style={{
                                        width: `${barWidth}%`,
                                        height: '100%',
                                        background: `linear-gradient(90deg, ${wo.color}cc, ${wo.color})`,
                                        borderRadius: 8,
                                        display: 'flex', alignItems: 'center', paddingLeft: 16,
                                        transition: 'none',
                                    }}>
                                        <span style={{ opacity: numOpacity, color: 'white', fontSize: 18, fontWeight: 800 }}>
                                            {wo.count}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </AbsoluteFill>
    );
}

// ── Scene 4: Reliability Metrics ───────────────────────────────────────────

function ReliabilityScene({ mtbf, mttr, availability, frame, fps }: {
    mtbf: string; mttr: string; availability: string; frame: number; fps: number;
}) {
    const metrics = [
        { label: 'MTBF', value: mtbf, unit: 'horas', desc: 'Tiempo Medio Entre Fallas', color: '#22c55e', pct: 85 },
        { label: 'MTTR', value: mttr, unit: 'horas', desc: 'Tiempo Medio de Reparación', color: '#3b82f6', pct: 60 },
        { label: 'OEE', value: availability, unit: '%', desc: 'Disponibilidad de Activos', color: '#e07b30', pct: parseFloat(availability) },
    ];

    return (
        <AbsoluteFill style={{
            background: '#f4f5f9',
            fontFamily: "'Inter', sans-serif",
            padding: 60,
        }}>
            <div style={{ opacity: interpolate(frame, [0, 20], [0, 1], { extrapolateRight: 'clamp' }), marginBottom: 48 }}>
                <div style={{ color: '#e07b30', fontSize: 12, fontWeight: 700, letterSpacing: '0.25em', textTransform: 'uppercase', marginBottom: 8 }}>
                    Análisis de Confiabilidad
                </div>
                <div style={{ color: '#002046', fontSize: 36, fontWeight: 800 }}>
                    MTBF · MTTR · Disponibilidad
                </div>
            </div>

            <div style={{ display: 'flex', gap: 24 }}>
                {metrics.map((m, i) => {
                    const cardSpr = spring({ frame: frame - i * 15, fps, config: { damping: 30 } });
                    const ringProgress = spring({ frame: frame - i * 15 - 10, fps, config: { damping: 50, stiffness: 40 } });
                    const circumference = 2 * Math.PI * 54;
                    const offset = circumference - (ringProgress * m.pct / 100) * circumference;

                    return (
                        <div key={i} style={{
                            flex: 1,
                            background: 'white',
                            borderRadius: 24,
                            padding: '36px 28px',
                            boxShadow: '0 4px 24px rgba(0,0,0,0.06)',
                            opacity: cardSpr,
                            transform: `scale(${interpolate(cardSpr, [0, 1], [0.8, 1])})`,
                            display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 20,
                        }}>
                            {/* Ring chart */}
                            <svg width={128} height={128} viewBox="0 0 128 128">
                                <circle cx={64} cy={64} r={54} fill="none" stroke="#f3f4f6" strokeWidth={12} />
                                <circle
                                    cx={64} cy={64} r={54}
                                    fill="none"
                                    stroke={m.color}
                                    strokeWidth={12}
                                    strokeLinecap="round"
                                    strokeDasharray={circumference}
                                    strokeDashoffset={offset}
                                    transform="rotate(-90 64 64)"
                                    style={{ filter: `drop-shadow(0 0 6px ${m.color}80)` }}
                                />
                                <text x={64} y={60} textAnchor="middle" fill="#002046" fontSize={22} fontWeight={800} fontFamily="Inter">
                                    {m.value}
                                </text>
                                <text x={64} y={80} textAnchor="middle" fill="#9ca3af" fontSize={12} fontFamily="Inter">
                                    {m.unit}
                                </text>
                            </svg>

                            <div style={{ textAlign: 'center' }}>
                                <div style={{ color: m.color, fontSize: 22, fontWeight: 800, marginBottom: 4 }}>{m.label}</div>
                                <div style={{ color: '#6b7280', fontSize: 12, fontWeight: 500 }}>{m.desc}</div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </AbsoluteFill>
    );
}

// ── Scene 5: Closing ───────────────────────────────────────────────────────

function ClosingScene({ companyName, frame, fps }: { companyName: string; frame: number; fps: number }) {
    const scale = spring({ frame, fps, config: { damping: 20, stiffness: 50 } });
    const textOpacity = interpolate(frame, [20, 45], [0, 1], { extrapolateRight: 'clamp' });
    const lineW = interpolate(frame, [40, 65], [0, 300], { extrapolateRight: 'clamp' });

    return (
        <AbsoluteFill style={{
            background: 'linear-gradient(135deg, #001433 0%, #002046 50%, #003580 100%)',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            justifyContent: 'center',
            fontFamily: "'Inter', sans-serif",
        }}>
            <div style={{ position: 'absolute', inset: 0, backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(255,255,255,0.06) 1px, transparent 0)', backgroundSize: '32px 32px' }} />
            <div style={{ position: 'absolute', top: -100, right: -100, width: 600, height: 600, borderRadius: '50%', background: 'radial-gradient(circle, rgba(224,123,48,0.15), transparent)', filter: 'blur(80px)' }} />

            <div style={{ transform: `scale(${scale})`, width: 72, height: 72, borderRadius: 18, background: 'linear-gradient(135deg, #e07b30, #c45c1a)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 34, marginBottom: 32, boxShadow: '0 8px 32px rgba(224,123,48,0.4)' }}>⚙</div>

            <div style={{ opacity: textOpacity, textAlign: 'center' }}>
                <div style={{ color: '#e07b30', fontSize: 13, fontWeight: 700, letterSpacing: '0.25em', textTransform: 'uppercase', marginBottom: 12 }}>CMMS Pro</div>
                <div style={{ color: 'white', fontSize: 40, fontWeight: 800, marginBottom: 8 }}>Gestión Inteligente</div>
                <div style={{ color: 'rgba(255,255,255,0.6)', fontSize: 18, fontWeight: 500 }}>{companyName}</div>
            </div>

            <div style={{ width: lineW, height: 2, background: 'linear-gradient(90deg, transparent, #e07b30, transparent)', margin: '32px 0' }} />

            <div style={{ opacity: textOpacity, color: 'rgba(255,255,255,0.4)', fontSize: 12, fontWeight: 600, letterSpacing: '0.15em', textTransform: 'uppercase' }}>
                Generado por CMMS Pro · {new Date().getFullYear()}
            </div>
        </AbsoluteFill>
    );
}

// ── Main Composition ───────────────────────────────────────────────────────

const SCENE_DURATIONS = {
    title: 90,
    kpis: 100,
    workOrders: 100,
    reliability: 110,
    closing: 80,
};

export const MaintenanceReport: React.FC<MaintenanceReportProps> = ({
    month = 'Marzo 2026',
    companyName = 'Empresa Industrial S.A.',
    kpis = DEFAULT_KPIS,
    workOrders = DEFAULT_WORK_ORDERS,
    mtbf = '312',
    mttr = '3.2',
    availability = '98.4',
}) => {
    const frame = useCurrentFrame();
    const { fps } = useVideoConfig();

    const scenes = [
        { start: 0, duration: SCENE_DURATIONS.title },
        { start: SCENE_DURATIONS.title, duration: SCENE_DURATIONS.kpis },
        { start: SCENE_DURATIONS.title + SCENE_DURATIONS.kpis, duration: SCENE_DURATIONS.workOrders },
        { start: SCENE_DURATIONS.title + SCENE_DURATIONS.kpis + SCENE_DURATIONS.workOrders, duration: SCENE_DURATIONS.reliability },
        { start: SCENE_DURATIONS.title + SCENE_DURATIONS.kpis + SCENE_DURATIONS.workOrders + SCENE_DURATIONS.reliability, duration: SCENE_DURATIONS.closing },
    ];

    function sceneOpacity(index: number) {
        const { start, duration } = scenes[index];
        const localFrame = frame - start;
        if (localFrame < 0 || localFrame >= duration) return 0;
        // Fade in / fade out
        return interpolate(
            localFrame,
            [0, 8, duration - 10, duration],
            [0, 1, 1, 0],
            { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' }
        );
    }

    function localFrame(index: number) {
        return frame - scenes[index].start;
    }

    return (
        <AbsoluteFill>
            {/* Scene 1: Title */}
            <AbsoluteFill style={{ opacity: sceneOpacity(0) }}>
                <TitleScene month={month} companyName={companyName} frame={localFrame(0)} fps={fps} />
            </AbsoluteFill>

            {/* Scene 2: KPIs */}
            <AbsoluteFill style={{ opacity: sceneOpacity(1) }}>
                <KpiScene kpis={kpis} frame={localFrame(1)} fps={fps} />
            </AbsoluteFill>

            {/* Scene 3: Work Orders */}
            <AbsoluteFill style={{ opacity: sceneOpacity(2) }}>
                <WorkOrdersScene workOrders={workOrders} frame={localFrame(2)} fps={fps} />
            </AbsoluteFill>

            {/* Scene 4: Reliability */}
            <AbsoluteFill style={{ opacity: sceneOpacity(3) }}>
                <ReliabilityScene mtbf={mtbf} mttr={mttr} availability={availability} frame={localFrame(3)} fps={fps} />
            </AbsoluteFill>

            {/* Scene 5: Closing */}
            <AbsoluteFill style={{ opacity: sceneOpacity(4) }}>
                <ClosingScene companyName={companyName} frame={localFrame(4)} fps={fps} />
            </AbsoluteFill>
        </AbsoluteFill>
    );
};
