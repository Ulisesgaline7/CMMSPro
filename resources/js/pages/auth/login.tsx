import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

type Props = {
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
};

export default function Login({ status, canResetPassword, canRegister }: Props) {
    return (
        <AuthLayout
            title="Iniciar sesión"
            description="Ingresa tu correo y contraseña para acceder a tu cuenta"
        >
            <Head title="Iniciar sesión" />

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
                                                  style={{ color: '#e07b30' }}>
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
                                className="w-full h-10 font-semibold text-sm mt-1"
                                tabIndex={4}
                                disabled={processing}
                                data-test="login-button"
                                style={{ background: 'linear-gradient(135deg, #002046, #003580)' }}
                            >
                                {processing && <Spinner />}
                                Iniciar sesión
                            </Button>
                        </div>

                        {canRegister && (
                            <div className="text-center text-sm" style={{ color: '#9ca3af' }}>
                                ¿No tienes cuenta?{' '}
                                <TextLink href={register()} tabIndex={5}
                                          style={{ color: '#e07b30', fontWeight: 600 }}>
                                    Crea una gratis
                                </TextLink>
                            </div>
                        )}
                    </>
                )}
            </Form>

            {status && (
                <div className="mt-4 text-center text-sm font-medium" style={{ color: '#16a34a' }}>
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}
