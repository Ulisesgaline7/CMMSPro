import { Head, useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

type Props = {
    status?: string;
};

export default function SuperAdminLogin({ status }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/super-admin/login', { preserveScroll: true });
    }

    return (
        <div
            className="min-h-screen flex"
            style={{
                background: 'linear-gradient(135deg, #05050f 0%, #0a0a1f 50%, #0d0d2a 100%)',
                fontFamily: "'Inter', sans-serif",
            }}
        >
            <Head title="Super Admin · Acceso Restringido" />

            {/* Grid overlay */}
            <div
                className="absolute inset-0 pointer-events-none"
                style={{
                    backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(139,92,246,0.08) 1px, transparent 0)',
                    backgroundSize: '32px 32px',
                }}
            />

            {/* Left panel — branding */}
            <div className="hidden lg:flex flex-col justify-between w-2/5 p-12 relative overflow-hidden"
                style={{ background: 'linear-gradient(180deg, #0d1117 0%, #0d0f1a 100%)', borderRight: '1px solid rgba(139,92,246,0.12)' }}>

                {/* Purple glow */}
                <div className="absolute top-1/3 left-0 w-96 h-96 rounded-full pointer-events-none"
                    style={{ background: 'radial-gradient(circle, rgba(124,58,237,0.15), transparent)', filter: 'blur(80px)' }} />
                <div className="absolute bottom-0 right-0 w-64 h-64 rounded-full pointer-events-none"
                    style={{ background: 'radial-gradient(circle, rgba(79,70,229,0.1), transparent)', filter: 'blur(60px)' }} />

                {/* Logo */}
                <div className="relative">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-black"
                            style={{ background: 'linear-gradient(135deg, #7c3aed, #4f46e5)' }}>
                            SA
                        </div>
                        <div>
                            <span className="text-white font-extrabold text-xl leading-none" style={{ fontFamily: "'Manrope', sans-serif" }}>
                                CMMS <span style={{ color: '#a78bfa' }}>Pro</span>
                            </span>
                            <p className="text-[10px] font-bold uppercase tracking-widest mt-0.5" style={{ color: '#7c3aed' }}>
                                Super Admin Panel
                            </p>
                        </div>
                    </div>
                </div>

                {/* Middle */}
                <div className="relative">
                    <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border mb-6"
                        style={{ background: 'rgba(124,58,237,0.1)', borderColor: 'rgba(124,58,237,0.3)', color: '#a78bfa' }}>
                        <span className="w-1.5 h-1.5 rounded-full animate-pulse" style={{ background: '#7c3aed' }} />
                        <span className="text-[10px] font-bold uppercase tracking-widest">Acceso Interno</span>
                    </div>

                    <h2 className="font-extrabold text-3xl leading-tight text-white mb-4" style={{ fontFamily: "'Manrope', sans-serif" }}>
                        Panel de control<br />
                        <span style={{ color: '#a78bfa' }}>del negocio SaaS</span>
                    </h2>
                    <p className="text-sm leading-relaxed" style={{ color: 'rgba(255,255,255,0.45)' }}>
                        Acceso exclusivo para el equipo interno. Gestión de tenants,
                        métricas MRR, facturación y salud de la plataforma.
                    </p>

                    <ul className="mt-8 space-y-3">
                        {[
                            'Gestión de tenants y planes',
                            'Métricas MRR y churn en tiempo real',
                            'Módulos, facturación y soporte L2',
                            'Salud de plataforma e impersonación',
                        ].map((feat) => (
                            <li key={feat} className="flex items-center gap-3 text-sm" style={{ color: 'rgba(255,255,255,0.6)' }}>
                                <span className="w-1.5 h-1.5 rounded-full shrink-0" style={{ background: '#a78bfa' }} />
                                {feat}
                            </li>
                        ))}
                    </ul>
                </div>

                {/* Footer */}
                <div className="relative">
                    <p className="text-[10px] font-bold uppercase tracking-widest" style={{ color: 'rgba(255,255,255,0.2)' }}>
                        Acceso Restringido — Solo Equipo Interno
                    </p>
                </div>
            </div>

            {/* Right panel — form */}
            <div className="flex-1 flex items-center justify-center p-6 lg:p-12 relative">
                <div className="w-full max-w-sm">

                    {/* Mobile logo */}
                    <div className="flex justify-center mb-8 lg:hidden">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-black"
                                style={{ background: 'linear-gradient(135deg, #7c3aed, #4f46e5)' }}>
                                SA
                            </div>
                            <span className="font-extrabold text-lg text-white" style={{ fontFamily: "'Manrope', sans-serif" }}>
                                Super Admin
                            </span>
                        </div>
                    </div>

                    {/* Card */}
                    <div className="rounded-2xl p-8"
                        style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(139,92,246,0.2)', backdropFilter: 'blur(12px)' }}>

                        <div className="mb-7">
                            <h1 className="font-extrabold text-2xl mb-1 text-white" style={{ fontFamily: "'Manrope', sans-serif" }}>
                                Acceso Super Admin
                            </h1>
                            <p className="text-sm" style={{ color: 'rgba(255,255,255,0.4)' }}>
                                Ingresa tus credenciales de equipo interno
                            </p>
                        </div>

                        <form onSubmit={submit} className="flex flex-col gap-5">
                            <div className="grid gap-5">
                                <div className="grid gap-1.5">
                                    <Label htmlFor="email" style={{ color: 'rgba(255,255,255,0.7)', fontSize: '13px', fontWeight: 600 }}>
                                        Correo electrónico
                                    </Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                        autoFocus
                                        tabIndex={1}
                                        autoComplete="email"
                                        placeholder="admin@empresa.com"
                                        className="h-10 border-white/10 bg-white/5 text-white placeholder:text-white/25 focus:border-purple-500/50"
                                        style={{ fontSize: '14px' }}
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                <div className="grid gap-1.5">
                                    <Label htmlFor="password" style={{ color: 'rgba(255,255,255,0.7)', fontSize: '13px', fontWeight: 600 }}>
                                        Contraseña
                                    </Label>
                                    <PasswordInput
                                        id="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        required
                                        tabIndex={2}
                                        autoComplete="current-password"
                                        placeholder="Tu contraseña"
                                        className="h-10 border-white/10 bg-white/5 text-white placeholder:text-white/25 focus:border-purple-500/50"
                                        style={{ fontSize: '14px' }}
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="flex items-center gap-2.5">
                                    <Checkbox
                                        id="remember"
                                        name="remember"
                                        checked={data.remember}
                                        onCheckedChange={(checked) => setData('remember', checked as boolean)}
                                        tabIndex={3}
                                        className="border-white/20 data-[state=checked]:bg-purple-600 data-[state=checked]:border-purple-600"
                                    />
                                    <Label htmlFor="remember" style={{ color: 'rgba(255,255,255,0.4)', fontSize: '13px', fontWeight: 400, cursor: 'pointer' }}>
                                        Mantener sesión
                                    </Label>
                                </div>

                                <Button
                                    type="submit"
                                    className="w-full h-10 font-semibold text-sm mt-1 text-white border-0"
                                    tabIndex={4}
                                    disabled={processing}
                                    style={{ background: 'linear-gradient(135deg, #7c3aed, #4f46e5)' }}
                                >
                                    {processing && <Spinner />}
                                    Ingresar al panel
                                </Button>
                            </div>
                        </form>

                        {status && (
                            <div className="mt-4 text-center text-sm font-medium" style={{ color: '#22c55e' }}>
                                {status}
                            </div>
                        )}
                    </div>

                    <p className="text-center text-xs mt-6" style={{ color: 'rgba(255,255,255,0.15)' }}>
                        Panel interno — v1.0.0-BETA
                    </p>
                </div>
            </div>
        </div>
    );
}
