import { Link } from '@inertiajs/react';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="min-h-screen flex" style={{ background: '#f4f5f9', fontFamily: "'Inter', sans-serif" }}>

            {/* Left panel — branding */}
            <div
                className="hidden lg:flex flex-col justify-between w-2/5 p-12 relative overflow-hidden"
                style={{ background: 'linear-gradient(135deg, #001433 0%, #002046 50%, #003580 100%)' }}
            >
                {/* Grid pattern */}
                <div
                    className="absolute inset-0 opacity-5"
                    style={{
                        backgroundImage: 'radial-gradient(circle at 1px 1px, white 1px, transparent 0)',
                        backgroundSize: '28px 28px',
                    }}
                />
                {/* Glow */}
                <div
                    className="absolute bottom-0 left-0 w-80 h-80 rounded-full opacity-20"
                    style={{ background: 'radial-gradient(circle, #e07b30, transparent)', filter: 'blur(70px)' }}
                />

                {/* Logo */}
                <div className="relative">
                    <Link href={home()} className="flex items-center gap-3">
                        <div
                            className="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                            style={{ background: 'linear-gradient(135deg, #e07b30, #c45c1a)' }}
                        >
                            <svg viewBox="0 0 24 24" fill="none" className="w-5 h-5 text-white" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                        </div>
                        <div>
                            <span className="text-white font-extrabold text-xl leading-none" style={{ fontFamily: "'Manrope', sans-serif" }}>
                                CMMS <span style={{ color: '#e07b30' }}>Pro</span>
                            </span>
                        </div>
                    </Link>
                </div>

                {/* Middle content */}
                <div className="relative">
                    <h2 className="font-extrabold text-3xl leading-tight text-white mb-4" style={{ fontFamily: "'Manrope', sans-serif" }}>
                        Mantenimiento industrial<br />
                        <span style={{ color: '#e07b30' }}>inteligente para LATAM</span>
                    </h2>
                    <p className="text-sm leading-relaxed" style={{ color: 'rgba(255,255,255,0.6)' }}>
                        Gestiona activos, órdenes de trabajo, IoT y análisis predictivo
                        en una sola plataforma. Cumplimiento normativo incluido.
                    </p>

                    {/* Feature list */}
                    <ul className="mt-6 space-y-3">
                        {[
                            'PM, CM y Mantenimiento Predictivo',
                            'IoT en tiempo real + alertas',
                            'IA: MTBF, MTTR y predicción de fallos',
                            'Cumplimiento INVIMA, COFEPRIS, HACCP',
                        ].map((feat) => (
                            <li key={feat} className="flex items-center gap-2.5 text-sm" style={{ color: 'rgba(255,255,255,0.75)' }}>
                                <svg viewBox="0 0 20 20" fill="currentColor" className="w-4 h-4 shrink-0" style={{ color: '#22c55e' }}>
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                {feat}
                            </li>
                        ))}
                    </ul>
                </div>

                {/* Stats */}
                <div className="relative grid grid-cols-3 gap-4">
                    {[
                        ['500+', 'Empresas'],
                        ['98.9%', 'Uptime'],
                        ['6', 'Países'],
                    ].map(([num, label]) => (
                        <div key={label}>
                            <p className="font-black text-2xl text-white" style={{ fontFamily: "'Manrope', sans-serif" }}>{num}</p>
                            <p className="text-xs" style={{ color: 'rgba(255,255,255,0.45)' }}>{label}</p>
                        </div>
                    ))}
                </div>
            </div>

            {/* Right panel — form */}
            <div className="flex-1 flex items-center justify-center p-6 lg:p-12">
                <div className="w-full max-w-sm">

                    {/* Mobile logo */}
                    <div className="flex justify-center mb-8 lg:hidden">
                        <Link href={home()} className="flex items-center gap-2">
                            <div
                                className="w-8 h-8 rounded-lg flex items-center justify-center"
                                style={{ background: 'linear-gradient(135deg, #e07b30, #c45c1a)' }}
                            >
                                <svg viewBox="0 0 24 24" fill="none" className="w-4 h-4 text-white" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                </svg>
                            </div>
                            <span className="font-extrabold text-lg" style={{ color: '#002046', fontFamily: "'Manrope', sans-serif" }}>
                                CMMS <span style={{ color: '#e07b30' }}>Pro</span>
                            </span>
                        </Link>
                    </div>

                    {/* Form card */}
                    <div
                        className="bg-white rounded-2xl p-8"
                        style={{ boxShadow: '0 4px 24px rgba(0,0,0,0.06)', border: '1px solid #e5e7eb' }}
                    >
                        <div className="mb-7">
                            <h1
                                className="font-extrabold text-2xl mb-1"
                                style={{ color: '#002046', fontFamily: "'Manrope', sans-serif" }}
                            >
                                {title}
                            </h1>
                            <p className="text-sm" style={{ color: '#6b7280' }}>
                                {description}
                            </p>
                        </div>
                        {children}
                    </div>

                    {/* Footer note */}
                    <p className="text-center text-xs mt-6" style={{ color: '#9ca3af' }}>
                        Al continuar aceptas nuestros{' '}
                        <a href="#" className="underline hover:text-gray-600">Términos</a>
                        {' '}y{' '}
                        <a href="#" className="underline hover:text-gray-600">Privacidad</a>
                    </p>
                </div>
            </div>

        </div>
    );
}
