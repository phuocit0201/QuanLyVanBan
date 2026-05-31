import { useTranslation } from 'react-i18next';
import { User, Mail, Shield, Calendar, LogOut } from 'lucide-react';
import { Card, CardContent, CardFooter, Button } from '@/components/ui';
import { useAuthStore } from '@/store';
import { useToast } from '@/components/ui/Toast';
import { authApi } from '@/api';
import { useNavigate } from 'react-router-dom';
import { formatDate } from '@/lib/utils';
import { VNPTCredentialSettings } from '@/features/vnpt/VNPTCredentialSettings';

export function ProfilePage() {
  const { t } = useTranslation();
  const { user, logout } = useAuthStore();
  const { showToast } = useToast();
  const navigate = useNavigate();

  const handleLogout = async () => {
    try {
      await authApi.logout();
    } catch {}
    logout();
    showToast('success', t('auth.logoutSuccess'));
    navigate('/login');
  };

  const roleLabel = user?.role === 'admin'
    ? t('profile.roleAdmin')
    : user?.role === 'moderator'
    ? t('profile.roleModerator')
    : t('profile.roleUser');

  return (
    <div className="p-6 max-w-2xl">
      <h1 className="text-2xl font-bold text-slate-900 mb-6">{t('profile.title')}</h1>

      <div className="space-y-6">
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center gap-4 mb-6">
              <div className="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center">
                <span className="text-white font-bold text-xl">
                  {user?.name?.charAt(0)?.toUpperCase() || 'U'}
                </span>
              </div>
              <div>
                <h2 className="text-lg font-semibold text-slate-900">{user?.name}</h2>
                <span className="text-sm text-slate-500">{roleLabel}</span>
              </div>
            </div>

            <div className="space-y-4">
              <div className="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                <User size={18} className="text-slate-400" />
                <div>
                  <p className="text-xs text-slate-500">{t('profile.name')}</p>
                  <p className="text-sm font-medium text-slate-900">{user?.name}</p>
                </div>
              </div>
              <div className="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                <Mail size={18} className="text-slate-400" />
                <div>
                  <p className="text-xs text-slate-500">{t('profile.email')}</p>
                  <p className="text-sm font-medium text-slate-900">{user?.email}</p>
                </div>
              </div>
              <div className="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                <Shield size={18} className="text-slate-400" />
                <div>
                  <p className="text-xs text-slate-500">{t('profile.role')}</p>
                  <p className="text-sm font-medium text-slate-900">{roleLabel}</p>
                </div>
              </div>
              <div className="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                <Calendar size={18} className="text-slate-400" />
                <div>
                  <p className="text-xs text-slate-500">{t('profile.createdAt')}</p>
                  <p className="text-sm font-medium text-slate-900">{formatDate(user?.created_at)}</p>
                </div>
              </div>
            </div>
          </CardContent>

          <CardFooter className="flex justify-end">
            <Button variant="danger" onClick={handleLogout}>
              <LogOut size={16} />
              {t('nav.logout')}
            </Button>
          </CardFooter>
        </Card>

        <VNPTCredentialSettings />
      </div>
    </div>
  );
}
