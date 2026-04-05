import { Form, Head, Link } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { home } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

interface RoleConfig {
    label: string;
    description: string;
    icon: string;
    gradient: string;
    accent: string;
    features: string[];
}

interface Props {
    role: string;
    roleConfig: RoleConfig;
    canResetPassword: boolean;
    status?: string;
}

function MaterialIcon({ name }: { name: string }) {
    return (
        <span className="material-symbols-outlined select-none" style={{ fontVariationSettings: "'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24" }}>
            {name}
        </span>
    );
}

export default function RoleLogin({ role, roleConfig, canResetPassword, status }: Props) {
    return (
        <div className="min-h-screen flex" style={{ background: '#f4f5f9', fontFamily: "'Inter', sans-serif" }}>
            <Head title={`Iniciar sesión — ${roleConfig.label}`} />

            {/* Left panel — role branding */}
            <div
                className="hidden lg:flex flex-col justify-between w-2/5 p-12 relative overflow-hidden"
                style={{ background: roleConfig.gradient }}
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
                    style={{ background: `radial-gradient(circle, ${roleConfig.accent}, transparent)`, filter: 'blur(70px)' }}
                />

                {/* Logo */}
                <div className="relative">
                    <Link href={home()} className="flex items-center gap-3">
                        <div
                            className="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                            style={{ background: `linear-gradient(135deg, ${roleConfig.accent}cc, ${roleConfig.accent}88)` }}
                        >
                            <svg viewBox="0 0 24 24" fill="none" className="w-5 h-5 text-white" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                        </div>
                        <div>
                            <span className="text-white font-extrabold text-xl leading-none" style={{ fontFamily: "'Manrope', sans-serif" }}>
                                CMMS <span style={{ color: roleConfig.accent }}>Pro</span>
                            </span>
                        </div>
                    </Link>
                </div>

                {/* Role info */}
                <div className="relative">
                    {/* Role icon badge */}
                    <div
                        className="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 text-3xl"
                        style={{ background: `${roleConfig.accent}22`, border: `1px solid ${roleConfig.accent}44` }}
                    >
                        <MaterialIcon name={roleConfig.icon} />
                    </div>

                    <div
                        className="inline-block text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-3"
                        style={{ background: `${roleConfig.accent}22`, color: roleConfig.accent, border: `1px solid ${roleConfig.accent}33` }}
                    >
                        Portal {roleConfig.label}
                    </div>

                    <h2 className="font-extrabold text-3xl leading-tight text-white mb-4" style={{ fontFamily: "'Manrope', sans-serif" }}>
                        Bienvenido,<br />
                        <span style={{ color: roleConfig.accent }}>{roleConfig.label}</span>
                    </h2>
                    <p className="text-sm leading-relaxed" style={{ color: 'rgba(255,255,255,0.6)' }}>
                        {roleConfig.description}
                    </p>

                    {/* Features */}
                    <ul className="mt-6 space-y-3">
                        {roleConfig.features.map((feat) => (
                            <li key={feat} className="flex items-center gap-2.5 text-sm" style={{ color: 'rgba(255,255,255,0.75)' }}>
                                <svg viewBox="0 0 20 20" fill="currentColor" className="w-4 h-4 shrink-0" style={{ color: roleConfig.accent }}>
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                {feat}
                            </li>
                        ))}
                    </ul>
                </div>

                {/* Back to generic login */}
                <div className="relative">
                    <Link
                        href="/login"
                        className="text-xs hover:underline"
                        style={{ color: 'rgba(255,255,255,0.35)' }}
                    >
                        ← Volver al login general
                    </Link>
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
                                style={{ background: roleConfig.gradient }}
                            >
                                <svg viewBox="0 0 24 24" fill="none" className="w-4 h-4 text-white" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                </svg>
                            </div>
                            <span className="font-extrabold text-lg" style={{ color: '#002046', fontFamily: "'Manrope', sans-serif" }}>
                                CMMS <span style={{ color: roleConfig.accent }}>Pro</span>
                            </span>
                        </Link>
                    </div>

                    {/* Form card */}
                    <div
                        className="bg-white rounded-2xl p-8"
                        style={{ boxShadow: '0 4px 24px rgba(0,0,0,0.06)', border: '1px solid #e5e7eb' }}
                    >
                        {/* Role badge */}
                        <div className="flex items-center gap-2 mb-5">
                            <div
                                className="w-8 h-8 rounded-lg flex items-center justify-center text-lg"
                                style={{ background: `${roleConfig.accent}18`, border: `1px solid ${roleConfig.accent}30` }}
                            >
                                <MaterialIcon name={roleConfig.icon} />
                            </div>
                            <span
                                className="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full"
                                style={{ background: `${roleConfig.accent}15`, color: roleConfig.accent }}
                            >
                                Portal {roleConfig.label}
                            </span>
                        </div>

                        <div className="mb-7">
                            <h1
                                className="font-extrabold text-2xl mb-1"
                                style={{ color: '#002046', fontFamily: "'Manrope', sans-serif" }}
                            >
                                Iniciar sesión
                            </h1>
                            <p className="text-sm" style={{ color: '#6b7280' }}>
                                {roleConfig.description}
                            </p>
                        </div>

                        <Form
                            {...store.form()}
                            resetOnSuccess={['password']}
                            className="flex flex-col gap-5"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <div className="grid gap-5">
                                        <div className="grid gap-1.5">
                                            <Label htmlFor="email" style={{ color: '#374151', fontSize: '13px', fontWeight: 600 }}>
                                                Correo electrónico
                                            </Label>
                                            <Input
                                                id="email"
                                                type="email"
                                                name="email"
                                                required
                                                autoFocus
                                                tabIndex={1}
                                                autoComplete="email"
                                                placeholder="tu@empresa.com"
                                                className="h-10"
                                                style={{ fontSize: '14px' }}
                                            />
                                            <InputError message={errors.email} />
                                        </div>

                                        <div className="grid gap-1.5">
                                            <div className="flex items-center justify-between">
                                                <Label htmlFor="password" style={{ color: '#374151', fontSize: '13px', fontWeight: 600 }}>
                                                    Contraseña
                                                </Label>
                                                {canResetPassword && (
                                                    <TextLink href={request()} className="text-xs" tabIndex={5}
                                                              style={{ color: roleConfig.accent }}>
                                                        ¿Olvidaste tu contraseña?
                                                    </TextLink>
                                                )}
                                            </div>
                                            <PasswordInput
                                                id="password"
                                                name="password"
                                                required
                                                tabIndex={2}
                                                autoComplete="current-password"
                                                placeholder="Tu contraseña"
                                                className="h-10"
                                                style={{ fontSize: '14px' }}
                                            />
                                            <InputError message={errors.password} />
                                        </div>

                                        <div className="flex items-center gap-2.5">
                                            <Checkbox id="remember" name="remember" tabIndex={3} />
                                            <Label htmlFor="remember" style={{ color: '#6b7280', fontSize: '13px', fontWeight: 400, cursor: 'pointer' }}>
                                                Mantener sesión iniciada
                                            </Label>
                                        </div>

                                        <Button
                                            type="submit"
                                            className="w-full h-10 font-semibold text-sm mt-1 text-white"
                                            tabIndex={4}
                                            disabled={processing}
                                            style={{ background: roleConfig.gradient }}
                                        >
                                            {processing && <Spinner />}
                                            Iniciar sesión
                                        </Button>
                                    </div>
                                </>
                            )}
                        </Form>
                    </div>

                    {status && (
                        <div className="mt-4 text-center text-sm font-medium" style={{ color: '#16a34a' }}>
                            {status}
                        </div>
                    )}

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
