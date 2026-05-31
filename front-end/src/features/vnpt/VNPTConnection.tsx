import { useState } from 'react';
import { useForm } from 'react-hook-form';
import type { SubmitHandler } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useTranslation } from 'react-i18next';
import { KeyRound, CheckCircle, XCircle, RefreshCw } from 'lucide-react';
import { Card, CardHeader, CardContent, Button, Input } from '@/components/ui';
import { useSaveVnptCredential, useSyncDocuments } from '@/hooks';
import { useToast } from '@/components/ui/Toast';
import { cn } from '@/lib/utils';

const vnptLoginSchema = z.object({
  username: z.string().min(1, 'required'),
  password: z.string().min(1, 'required'),
});

type VNPTOfficeForm = z.infer<typeof vnptLoginSchema>;

interface VNPTConnectionProps {
  credentials: { username: string; password: string } | null;
  onCredentialsChange: (creds: { username: string; password: string } | null) => void;
}

export function VNPTConnection({ credentials, onCredentialsChange }: VNPTConnectionProps) {
  const { t } = useTranslation();
  const { showToast } = useToast();
  const loginMutation = useSaveVnptCredential();
  const syncMutation = useSyncDocuments();

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors, isSubmitting },
  } = useForm<VNPTOfficeForm>({
    resolver: zodResolver(vnptLoginSchema),
    defaultValues: { username: credentials?.username || '', password: credentials?.password || '' },
  });

  const [connected, setConnected] = useState(!!credentials);

  const onSubmit: SubmitHandler<VNPTOfficeForm> = async (data) => {
    try {
      await loginMutation.mutateAsync({
        username: data.username,
        password: data.password,
      });
      setConnected(true);
      onCredentialsChange({ username: data.username, password: data.password });
      showToast('success', t('vnpt.loginSuccess'));
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      showToast('error', e?.response?.data?.message || t('vnpt.loginError'));
    }
  };

  const handleSync = async () => {
    try {
      const result = await syncMutation.mutateAsync({});
      showToast('success', t('documents.syncSuccess', { count: result.saved_count }));
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      showToast('error', e?.response?.data?.message || t('documents.syncError'));
    }
  };

  const handleDisconnect = () => {
    setConnected(false);
    onCredentialsChange(null);
    reset({ username: '', password: '' });
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
        {!connected ? (
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
            <Button type="submit" className="w-full" isLoading={isSubmitting}>
              {t('vnpt.login')}
            </Button>
          </form>
        ) : (
          <div className="flex flex-col gap-3">
            <div className="p-3 bg-green-50 rounded-lg">
              <p className="text-sm text-green-700">
                <strong>{credentials?.username}</strong> — {t('vnpt.connected')}
              </p>
            </div>
            <div className="flex gap-2">
              <Button
                variant="primary"
                className="flex-1"
                onClick={handleSync}
                isLoading={syncMutation.isPending}
              >
                <RefreshCw size={16} />
                {syncMutation.isPending ? t('documents.syncing') : t('documents.syncDocuments')}
              </Button>
              <Button variant="danger" onClick={handleDisconnect}>
                {t('vnpt.logout')}
              </Button>
            </div>
          </div>
        )}
      </CardContent>
    </Card>
  );
}
