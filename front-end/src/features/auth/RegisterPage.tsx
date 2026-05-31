import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigate, Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Mail, Lock, User } from 'lucide-react';
import { useAuthStore } from '@/store';
import { authApi } from '@/api';
import { useToast } from '@/components/ui/Toast';
import { Button, Input, Card, CardContent } from '@/components/ui';

const registerSchema = z
  .object({
    name: z.string().min(1, 'required'),
    email: z.string().min(1, 'required').email('emailInvalid'),
    password: z.string().min(8, 'passwordMin'),
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'passwordConfirm',
    path: ['password_confirmation'],
  });

type RegisterForm = z.infer<typeof registerSchema>;

export function RegisterPage() {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { setAuth } = useAuthStore();
  const { showToast } = useToast();

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<RegisterForm>({
    resolver: zodResolver(registerSchema),
    defaultValues: { name: '', email: '', password: '', password_confirmation: '' },
  });

  const onSubmit = async (data: RegisterForm) => {
    try {
      const result = await authApi.register(data);
      setAuth(result.user, result.token.access_token);
      showToast('success', t('auth.registerSuccess'));
      navigate('/');
    } catch (err: any) {
      const respErrors = err?.response?.data?.errors;
      if (respErrors) {
        Object.entries(respErrors).forEach(([field, messages]) => {
          setError(field as keyof RegisterForm, {
            message: (messages as string[])[0],
          });
        });
      } else {
        showToast('error', err?.response?.data?.message || t('errors.networkError'));
      }
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-slate-100 px-4">
      <div className="w-full max-w-md">
        {/* Brand */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 mb-4 shadow-lg shadow-blue-200">
            <span className="text-white font-bold text-xl">TM</span>
          </div>
          <h1 className="text-2xl font-bold text-slate-900">Task Management</h1>
          <p className="text-slate-500 text-sm mt-1">{t('auth.register')}</p>
        </div>

        <Card>
          <CardContent className="p-6">
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              <Input
                label={t('auth.name')}
                placeholder="Nguyen Van A"
                icon={<User size={18} />}
                error={errors.name?.message ? t(`errors.${errors.name.message}`) || errors.name.message : undefined}
                {...register('name')}
              />
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
              <Input
                label={t('auth.confirmPassword')}
                type="password"
                placeholder="********"
                icon={<Lock size={18} />}
                error={errors.password_confirmation?.message ? t(`errors.${errors.password_confirmation.message}`) || errors.password_confirmation.message : undefined}
                {...register('password_confirmation')}
              />
              <Button
                type="submit"
                className="w-full"
                size="lg"
                isLoading={isSubmitting}
              >
                {t('auth.register')}
              </Button>
            </form>

            <div className="mt-6 text-center">
              <p className="text-sm text-slate-500">
                {t('auth.alreadyHaveAccount')}{' '}
                <Link to="/login" className="text-blue-600 hover:underline font-medium">
                  {t('auth.loginHere')}
                </Link>
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
