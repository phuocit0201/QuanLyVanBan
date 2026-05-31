import { useState } from 'react';
import { useForm } from 'react-hook-form';
import type { SubmitHandler } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useTranslation } from 'react-i18next';
import { KeyRound, CheckCircle, XCircle, Smartphone, Trash2 } from 'lucide-react';
import { Card, CardHeader, CardContent, Button, Input, Select } from '@/components/ui';
import { useVnptCredential, useSaveVnptCredential, useDeleteVnptCredential } from '@/hooks';
import { useToast } from '@/components/ui/Toast';
import { cn } from '@/lib/utils';

const credentialSchema = z.object({
  username: z.string().min(1),
  password: z.string().min(1),
  device_name: z.string().optional(),
  device_type: z.union([z.literal('IOS'), z.literal('ANDROID')]),
});

type CredentialForm = z.infer<typeof credentialSchema>;

export function VNPTCredentialSettings() {
  const { t } = useTranslation();
  const { showToast } = useToast();
  const { data: credential, isLoading } = useVnptCredential();
  const saveMutation = useSaveVnptCredential();
  const deleteMutation = useDeleteVnptCredential();

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<CredentialForm>({
    resolver: zodResolver(credentialSchema),
    defaultValues: {
      username: '',
      password: '',
      device_name: '',
      device_type: 'IOS' as const,
    },
  });

  const [showPassword, setShowPassword] = useState(false);
  const connected = credential !== null && credential !== undefined;

  const onSubmit: SubmitHandler<CredentialForm> = async (formData) => {
    try {
      await saveMutation.mutateAsync({
        username: formData.username,
        password: formData.password,
        device_name: formData.device_name || undefined,
        device_type: formData.device_type,
      });
      showToast('success', t('vnpt.credentialSaved'));
      reset({
        username: formData.username,
        password: '',
        device_name: formData.device_name || '',
        device_type: formData.device_type,
      });
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      showToast('error', e?.response?.data?.message || t('vnpt.loginError'));
    }
  };

  const handleDelete = async () => {
    try {
      await deleteMutation.mutateAsync();
      reset({
        username: '',
        password: '',
        device_name: '',
        device_type: 'IOS' as const,
      });
      showToast('success', t('vnpt.credentialDeleted'));
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      showToast('error', e?.response?.data?.message || t('errors.serverError'));
    }
  };

  return (
    <Card>
      <CardHeader>
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <KeyRound size={20} className="text-blue-600" />
            <h3 className="font-semibold text-slate-900">{t('vnpt.title')}</h3>
          </div>
          <span
            className={cn(
              'inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full',
              connected
                ? 'bg-green-50 text-green-700'
                : 'bg-slate-100 text-slate-600'
            )}
          >
            {connected ? (
              <CheckCircle size={14} className="text-green-500" />
            ) : (
              <XCircle size={14} className="text-slate-400" />
            )}
            {connected ? t('vnpt.connected') : t('vnpt.notConnected')}
          </span>
        </div>
        <p className="text-sm text-slate-500 mt-1">{t('vnpt.description')}</p>
      </CardHeader>
      <CardContent>
        {isLoading ? (
          <div className="flex justify-center py-4">
            <span className="text-sm text-slate-400">{t('common.loading')}</span>
          </div>
        ) : connected ? (
          <div className="flex flex-col gap-4">
            <div className="p-4 bg-green-50 rounded-lg space-y-2">
              <div className="flex items-center gap-2">
                <Smartphone size={16} className="text-green-600" />
                <span className="text-sm font-medium text-green-800">
                  {credential.device_name || 'Unknown Device'}
                </span>
              </div>
              <p className="text-sm text-green-700">
                <strong>{credential.username}</strong>
              </p>
              <p className="text-xs text-green-600">
                {credential.device_type === 'IOS' ? t('vnpt.ios') : t('vnpt.android')}
              </p>
            </div>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-3">
              <Input
                label={t('vnpt.password')}
                type={showPassword ? 'text' : 'password'}
                placeholder="********"
                error={errors.password?.message ? t(`errors.${errors.password.message}`) : undefined}
                {...register('password')}
              />
              <div className="flex items-center gap-2">
                <input
                  type="checkbox"
                  id="showPassword"
                  checked={showPassword}
                  onChange={(e) => setShowPassword(e.target.checked)}
                  className="rounded border-slate-300"
                />
                <label htmlFor="showPassword" className="text-sm text-slate-600">
                  Hiện mật khẩu
                </label>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <Input
                  label={t('vnpt.deviceName')}
                  placeholder={t('vnpt.deviceNamePlaceholder')}
                  {...register('device_name')}
                />
                <Select
                  label={t('vnpt.deviceType')}
                  options={[
                    { value: 'IOS', label: t('vnpt.ios') },
                    { value: 'ANDROID', label: t('vnpt.android') },
                  ]}
                  {...register('device_type')}
                />
              </div>
              <div className="flex gap-2">
                <Button type="submit" className="flex-1" isLoading={saveMutation.isPending}>
                  {t('common.save')}
                </Button>
                <Button
                  type="button"
                  variant="danger"
                  onClick={handleDelete}
                  isLoading={deleteMutation.isPending}
                >
                  <Trash2 size={16} />
                </Button>
              </div>
            </form>
          </div>
        ) : (
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-3">
            <Input
              label={t('vnpt.username')}
              placeholder="Tài khoản VNPT"
              error={errors.username?.message ? t(`errors.${errors.username.message}`) : undefined}
              {...register('username')}
            />
            <Input
              label={t('vnpt.password')}
              type="password"
              placeholder="********"
              error={errors.password?.message ? t(`errors.${errors.password.message}`) : undefined}
              {...register('password')}
            />
            <div className="grid grid-cols-2 gap-3">
              <Input
                label={t('vnpt.deviceName')}
                placeholder={t('vnpt.deviceNamePlaceholder')}
                {...register('device_name')}
              />
              <Select
                label={t('vnpt.deviceType')}
                options={[
                  { value: 'IOS', label: t('vnpt.ios') },
                  { value: 'ANDROID', label: t('vnpt.android') },
                ]}
                {...register('device_type')}
              />
            </div>
            <Button type="submit" className="w-full" isLoading={saveMutation.isPending}>
              {t('common.save')}
            </Button>
          </form>
        )}
      </CardContent>
    </Card>
  );
}
