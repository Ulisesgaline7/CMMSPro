import { Composition } from 'remotion';
import { MaintenanceReport } from './compositions/MaintenanceReport';
import { DashboardHero } from './compositions/DashboardHero';
import { SaaSMetrics } from './compositions/SaaSMetrics';

// MaintenanceReport: 90+100+100+110+80 = 480 frames @ 30fps = 16s
// DashboardHero: 150 frames @ 30fps = 5s

export const RemotionRoot: React.FC = () => {
    return (
        <>
            <Composition
                id="MaintenanceReport"
                component={MaintenanceReport}
                durationInFrames={480}
                fps={30}
                width={1280}
                height={720}
                defaultProps={{
                    month: 'Marzo 2026',
                    companyName: 'Empresa Industrial S.A.',
                    mtbf: '312',
                    mttr: '3.2',
                    availability: '98.4',
                    kpis: [
                        { label: 'Órdenes Completadas', value: '148', subtitle: '+12% vs mes anterior', color: '#22c55e', icon: '✓' },
                        { label: 'Tiempo Promedio (h)', value: '3.2', subtitle: 'MTTR del período', color: '#3b82f6', icon: '⏱' },
                        { label: 'Activos Monitoreados', value: '324', subtitle: '98% operativos', color: '#f59e0b', icon: '⚙' },
                        { label: 'Alertas IoT', value: '17', subtitle: '3 críticas resueltas', color: '#ef4444', icon: '📡' },
                    ],
                    workOrders: [
                        { status: 'Completadas', count: 148, color: '#22c55e' },
                        { status: 'Preventivas', count: 62, color: '#3b82f6' },
                        { status: 'Correctivas', count: 51, color: '#f59e0b' },
                        { status: 'Pendientes', count: 23, color: '#94a3b8' },
                        { status: 'Canceladas', count: 12, color: '#ef4444' },
                    ],
                }}
            />
            <Composition
                id="DashboardHero"
                component={DashboardHero}
                durationInFrames={150}
                fps={30}
                width={1280}
                height={360}
                defaultProps={{
                    totalOt: 148,
                    pending: 23,
                    inProgress: 12,
                    completedToday: 8,
                    overdue: 5,
                    totalAssets: 324,
                    activeAssets: 318,
                    mtbf: 312,
                    mttr: 3,
                    oee: 98,
                    companyName: 'Empresa Industrial S.A.',
                    userRole: 'admin',
                    userName: 'Administrador',
                }}
            />
            <Composition
                id="SaaSMetrics"
                component={SaaSMetrics}
                durationInFrames={140}
                fps={30}
                width={1280}
                height={320}
                defaultProps={{
                    mrr: 12500,
                    totalTenants: 48,
                    activeTenants: 41,
                    totalUsers: 386,
                    newTenantsThisMonth: 6,
                    churnCount: 2,
                    trialCount: 5,
                    pastDueCount: 3,
                }}
            />
        </>
    );
};
