import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import { store } from '@/routes/register';

export default function Register() {
    return (
        <AuthLayout
            title="Crear cuenta gratis"
            description="14 días de prueba gratuita. Sin tarjeta de crédito."
        >
            <Head title="Crear cuenta" />
            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-5"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-5">
                            <div className="grid gap-1.5">
                                <Label htmlFor="name" style={{ color: '#374151', fontSize: '13px', fontWeight: 600 }}>
                                    Nombre completo
                                </Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="name"
                                    placeholder="Juan García"
                                    className="h-10"
                                    style={{ fontSize: '14px' }}
                                />
                                <InputError message={errors.name} className="mt-1" />
                            </div>

                            <div className="grid gap-1.5">
                                <Label htmlFor="email" style={{ color: '#374151', fontSize: '13px', fontWeight: 600 }}>
                                    Correo electrónico
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="tu@empresa.com"
                                    className="h-10"
                                    style={{ fontSize: '14px' }}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-1.5">
                                <Label htmlFor="password" style={{ color: '#374151', fontSize: '13px', fontWeight: 600 }}>
                                    Contraseña
                                </Label>
                                <PasswordInput
                                    id="password"
                                    required
                                    tabIndex={3}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Mínimo 8 caracteres"
                                    className="h-10"
                                    style={{ fontSize: '14px' }}
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-1.5">
                                <Label htmlFor="password_confirmation" style={{ color: '#374151', fontSize: '13px', fontWeight: 600 }}>
                                    Confirmar contraseña
                                </Label>
                                <PasswordInput
                                    id="password_confirmation"
                                    required
                                    tabIndex={4}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Repite tu contraseña"
                                    className="h-10"
                                    style={{ fontSize: '14px' }}
                                />
                                <InputError message={errors.password_confirmation} />
                            </div>

                            {/* Trial notice */}
                            <div className="rounded-xl px-4 py-3 flex items-start gap-2.5"
                                 style={{ background: 'rgba(0,32,70,0.04)', border: '1px solid rgba(0,32,70,0.08)' }}>
                                <svg viewBox="0 0 20 20" fill="currentColor" className="w-4 h-4 shrink-0 mt-0.5" style={{ color: '#22c55e' }}>
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                <p className="text-xs leading-relaxed" style={{ color: '#374151' }}>
                                    <strong>14 días gratis</strong> con acceso completo a todos los módulos.
                                    Sin tarjeta de crédito requerida.
                                </p>
                            </div>

                            <Button
                                type="submit"
                                className="w-full h-10 font-semibold text-sm mt-1"
                                tabIndex={5}
                                data-test="register-user-button"
                                style={{ background: 'linear-gradient(135deg, #e07b30, #c45c1a)' }}
                            >
                                {processing && <Spinner />}
                                Crear cuenta gratis
                            </Button>
                        </div>

                        <div className="text-center text-sm" style={{ color: '#9ca3af' }}>
                            ¿Ya tienes cuenta?{' '}
                            <TextLink href={login()} tabIndex={6}
                                      style={{ color: '#002046', fontWeight: 600 }}>
                                Inicia sesión
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
