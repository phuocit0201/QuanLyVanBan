import { useTranslation } from 'react-i18next';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
  Legend,
} from 'recharts';
import {
  FileText,
  ArrowDownToLine,
  ArrowUpFromLine,
  EyeOff,
  AlertTriangle,
  Clock,
} from 'lucide-react';
import { Card, CardHeader, CardContent, Skeleton } from '@/components/ui';
import type { DocumentStats } from '@/types';

interface DashboardPageProps {
  stats?: DocumentStats;
  isLoading?: boolean;
  urgencyData?: { name: string; value: number; color: string }[];
  monthlyData?: { month: string; count: number }[];
}

export function DashboardPage({
  stats,
  isLoading,
  urgencyData,
  monthlyData,
}: DashboardPageProps) {
  const { t } = useTranslation();

  const statCards = [
    {
      label: t('dashboard.totalDocuments'),
      value: stats?.total ?? 0,
      icon: FileText,
      color: 'text-blue-600',
      bg: 'bg-blue-50',
    },
    {
      label: t('dashboard.incomingDocuments'),
      value: stats?.den ?? 0,
      icon: ArrowDownToLine,
      color: 'text-green-600',
      bg: 'bg-green-50',
    },
    {
      label: t('dashboard.outgoingDocuments'),
      value: stats?.di ?? 0,
      icon: ArrowUpFromLine,
      color: 'text-purple-600',
      bg: 'bg-purple-50',
    },
    {
      label: t('dashboard.unreadDocuments'),
      value: stats?.chua_doc ?? 0,
      icon: EyeOff,
      color: 'text-amber-600',
      bg: 'bg-amber-50',
    },
    {
      label: t('dashboard.overdueDocuments'),
      value: stats?.qua_han ?? 0,
      icon: AlertTriangle,
      color: 'text-red-600',
      bg: 'bg-red-50',
    },
    {
      label: t('dashboard.pendingDocuments'),
      value: stats?.cho_xu_ly ?? 0,
      icon: Clock,
      color: 'text-slate-600',
      bg: 'bg-slate-50',
    },
  ];

  if (isLoading) {
    return (
      <div className="p-6 space-y-6">
        <div className="grid grid-cols-2 lg:grid-cols-3 gap-4">
          {Array.from({ length: 6 }).map((_, i) => (
            <Skeleton key={i} className="h-28 rounded-xl" />
          ))}
        </div>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <Skeleton className="h-72 rounded-xl" />
          <Skeleton className="h-72 rounded-xl" />
        </div>
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6">
      {/* Page header */}
      <div>
        <h1 className="text-2xl font-bold text-slate-900">{t('dashboard.title')}</h1>
        <p className="text-slate-500 text-sm mt-1">{t('dashboard.statistics')}</p>
      </div>

      {/* Stats grid */}
      <div className="grid grid-cols-2 lg:grid-cols-3 gap-4">
        {statCards.map(({ label, value, icon: Icon, color, bg }) => (
          <Card key={label}>
            <CardContent className="p-5">
              <div className="flex items-start justify-between">
                <div>
                  <p className="text-sm text-slate-500">{label}</p>
                  <p className="text-3xl font-bold text-slate-900 mt-1">{value}</p>
                </div>
                <div className={`p-2.5 rounded-xl ${bg}`}>
                  <Icon size={22} className={color} />
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {/* Monthly bar chart */}
        <Card>
          <CardHeader>
            <h3 className="font-semibold text-slate-900">{t('dashboard.documentsByMonth')}</h3>
          </CardHeader>
          <CardContent>
            {monthlyData && monthlyData.length > 0 ? (
              <ResponsiveContainer width="100%" height={250}>
                <BarChart data={monthlyData}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" />
                  <XAxis dataKey="month" tick={{ fontSize: 12 }} stroke="#94a3b8" />
                  <YAxis tick={{ fontSize: 12 }} stroke="#94a3b8" allowDecimals={false} />
                  <Tooltip
                    contentStyle={{
                      borderRadius: '8px',
                      border: '1px solid #e2e8f0',
                      fontSize: '13px',
                    }}
                  />
                  <Bar dataKey="count" fill="#3b82f6" radius={[4, 4, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            ) : (
              <div className="h-[250px] flex items-center justify-center text-slate-400 text-sm">
                {t('documents.empty')}
              </div>
            )}
          </CardContent>
        </Card>

        {/* Urgency pie chart */}
        <Card>
          <CardHeader>
            <h3 className="font-semibold text-slate-900">{t('dashboard.documentsByUrgency')}</h3>
          </CardHeader>
          <CardContent>
            {urgencyData && urgencyData.filter((d) => d.value > 0).length > 0 ? (
              <ResponsiveContainer width="100%" height={250}>
                <PieChart>
                  <Pie
                    data={urgencyData.filter((d) => d.value > 0)}
                    dataKey="value"
                    nameKey="name"
                    cx="50%"
                    cy="50%"
                    outerRadius={80}
                    label={({ name, percent }) => `${name} (${((percent ?? 0) * 100).toFixed(0)}%)`}
                    labelLine={false}
                  >
                    {urgencyData.map((entry) => (
                      <Cell key={entry.name} fill={entry.color} />
                    ))}
                  </Pie>
                  <Tooltip
                    contentStyle={{
                      borderRadius: '8px',
                      border: '1px solid #e2e8f0',
                      fontSize: '13px',
                    }}
                  />
                  <Legend />
                </PieChart>
              </ResponsiveContainer>
            ) : (
              <div className="h-[250px] flex items-center justify-center text-slate-400 text-sm">
                {t('documents.empty')}
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
