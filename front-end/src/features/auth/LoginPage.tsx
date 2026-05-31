import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigate, Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Mail, Lock } from 'lucide-react';
import { useAuthStore } from '@/store';
import { authApi } from '@/api';
import { useToast } from '@/components/ui/Toast';
import { Button, Input, Card, CardContent } from '@/components/ui';

const loginSchema = z.object({
  email: z.string().min(1, 'emailRequired').email('emailInvalid'),
  password: z.string().min(8, 'passwordMin'),
});

type LoginForm = z.infer<typeof loginSchema>;

export function LoginPage() {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { setAuth } = useAuthStore();
  const { showToast } = useToast();

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<LoginForm>({
    resolver: zodResolver(loginSchema),
    defaultValues: { email: '', password: '' },
  });

  const onSubmit = async (data: LoginForm) => {
    try {
      const token = await authApi.login(data);
      const user = await authApi.me();
      setAuth(user, token.access_token);
      showToast('success', t('auth.loginSuccess'));
      navigate('/');
    } catch (err: any) {
      const msg = err?.response?.data?.message || t('errors.networkError');
      showToast('error', msg);
      if (msg.includes('email') || msg.includes('password')) {
        setError('email', { message: '' });
        setError('password', { message: msg });
      }
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-slate-100 px-4">
      <div className="w-full max-w-md">
        {/* Logo / Brand */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 mb-4 shadow-lg shadow-blue-200">
            <span className="text-white font-bold text-xl">TM</span>
          </div>
          <h1 className="text-2xl font-bold text-slate-900">Task Management</h1>
          <p className="text-slate-500 text-sm mt-1">{t('auth.login')}</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              <Input
                label={t('auth.email')}
                type="email"
                placeholder="email@example.com"
                icon={<Mail size={18} />}
                error={errors.email?.message ? t(`errors.${errors.email.message}`) || errors.email.message : undefined}
                {...register('email')}
              />
              <Input
                label={t('auth.password')}
                type="password"
                placeholder="********"
                icon={<Lock size={18} />}
                error={errors.password?.message ? t(`errors.${errors.password.message}`) || errors.password.message : undefined}
                {...register('password')}
              />
              <Button
                type="submit"
                className="w-full"
                size="lg"
                isLoading={isSubmitting}
              >
                {t('auth.login')}
              </Button>
            </form>

            <div className="mt-6 text-center">
              <p className="text-sm text-slate-500">
                {t('auth.dontHaveAccount')}{' '}
                <Link to="/register" className="text-blue-600 hover:underline font-medium">
                  {t('auth.registerHere')}
                </Link>
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
