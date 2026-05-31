import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import {
  LayoutDashboard,
  FileText,
  User,
  ChevronLeft,
  ChevronRight,
  LogOut,
  Globe,
  Menu,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { useAuthStore, useUiStore } from '@/store';
import { authApi } from '@/api';
import { useToast } from '@/components/ui/Toast';

export function AppLayout() {
  const { t, i18n } = useTranslation();
  const navigate = useNavigate();
  const { logout: storeLogout } = useAuthStore();
  const { sidebarOpen, toggleSidebar, locale, setLocale } = useUiStore();
  const { showToast } = useToast();

  const handleLogout = async () => {
    try {
      await authApi.logout();
    } catch {}
    storeLogout();
    showToast('success', t('auth.logoutSuccess'));
    navigate('/login');
  };

  const toggleLocale = () => {
    const newLocale = locale === 'vi' ? 'en' : 'vi';
    setLocale(newLocale);
    i18n.changeLanguage(newLocale);
  };

  const navItems = [
    { to: '/', icon: LayoutDashboard, label: t('nav.dashboard') },
    { to: '/documents', icon: FileText, label: t('nav.documents') },
    { to: '/profile', icon: User, label: t('nav.profile') },
  ];

  return (
    <div className="flex h-screen bg-slate-50">
      {/* Sidebar */}
      <aside
        className={cn(
          'flex flex-col bg-white border-r border-slate-200 transition-all duration-300',
          sidebarOpen ? 'w-60' : 'w-16'
        )}
      >
        {/* Logo */}
        <div className="flex items-center h-16 px-4 border-b border-slate-100">
          {sidebarOpen ? (
            <span className="font-bold text-blue-600 text-lg truncate">Task Manager</span>
          ) : (
            <span className="font-bold text-blue-600 text-lg">TM</span>
          )}
        </div>

        {/* Nav */}
        <nav className="flex-1 py-4 px-2 space-y-1">
          {navItems.map(({ to, icon: Icon, label }) => (
            <NavLink
              key={to}
              to={to}
              end={to === '/'}
              className={({ isActive }) =>
                cn(
                  'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                  sidebarOpen ? '' : 'justify-center px-0 w-full',
                  isActive
                    ? 'bg-blue-50 text-blue-600'
                    : 'text-slate-600 hover:bg-slate-100'
                )
              }
            >
              <Icon size={20} />
              {sidebarOpen && <span>{label}</span>}
            </NavLink>
          ))}
        </nav>

        {/* Bottom controls */}
        <div className="p-2 border-t border-slate-100 space-y-1">
          <button
            onClick={toggleLocale}
            className={cn(
              'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-600 hover:bg-slate-100 w-full transition-colors',
              !sidebarOpen && 'justify-center px-0'
            )}
            title={locale === 'vi' ? 'Switch to English' : 'Chuyển sang Tiếng Việt'}
          >
            <Globe size={20} />
            {sidebarOpen && (
              <span className="text-xs font-semibold uppercase">{locale}</span>
            )}
          </button>
          <button
            onClick={handleLogout}
            className={cn(
              'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-600 hover:bg-red-50 w-full transition-colors',
              !sidebarOpen && 'justify-center px-0'
            )}
          >
            <LogOut size={20} />
            {sidebarOpen && <span>{t('nav.logout')}</span>}
          </button>
          <button
            onClick={toggleSidebar}
            className={cn(
              'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-600 hover:bg-slate-100 w-full transition-colors',
              !sidebarOpen && 'justify-center px-0'
            )}
          >
            {sidebarOpen ? <ChevronLeft size={20} /> : <ChevronRight size={20} />}
            {sidebarOpen && <span className="text-xs">Thu nhỏ</span>}
          </button>
        </div>
      </aside>

      {/* Mobile sidebar overlay */}
      {!sidebarOpen && (
        <button
          onClick={toggleSidebar}
          className="fixed top-4 left-4 z-40 p-2 bg-white rounded-lg shadow-md border border-slate-200 lg:hidden"
        >
          <Menu size={20} />
        </button>
      )}

      {/* Main content */}
      <main className="flex-1 overflow-auto">
        <div className="min-h-full">
          <Outlet />
        </div>
      </main>
    </div>
  );
}
