import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { ArrowLeft, Eye, EyeOff, Code } from 'lucide-react';
import { Card, CardHeader, CardContent, Badge, Button, Skeleton } from '@/components/ui';
import { formatDateTime, cn } from '@/lib/utils';
import { CONG_VAN_LABELS } from '@/lib/constants';
import type { OfficeDocument } from '@/types';

interface DocumentDetailPageProps {
  document: OfficeDocument | undefined;
  isLoading?: boolean;
}

export function DocumentDetailPage({ document, isLoading }: DocumentDetailPageProps) {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [showRawData, setShowRawData] = useState(false);

  if (isLoading) {
    return (
      <div className="p-6 space-y-4">
        <Skeleton className="h-8 w-48" />
        <Card>
          <CardContent className="p-6 space-y-4">
            {Array.from({ length: 8 }).map((_, i) => (
              <Skeleton key={i} className="h-5 w-full" />
            ))}
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!document) {
    return (
      <div className="p-6">
        <p className="text-slate-500">{t('documents.empty')}</p>
      </div>
    );
  }

  const detailFields = [
    { label: t('documents.soKiHieu'), value: document.so_ki_hieu },
    { label: t('documents.trichYeu'), value: document.trich_yeu },
    { label: t('documents.coQuanBanHanh'), value: document.co_quan_ban_hanh },
    { label: t('documents.ngayVanBan'), value: formatDateTime(document.ngay_van_ban) },
    { label: t('documents.ngayDenDi'), value: formatDateTime(document.ngay_den_di) },
    { label: t('documents.ngayNhan'), value: formatDateTime(document.ngay_nhan) },
    { label: t('documents.hanXuLy'), value: formatDateTime(document.han_xu_ly) },
    {
      label: t('documents.doKhan'),
      value: document.do_khan,
      custom: (
        <Badge variant="urgency" urgency={document.do_khan || ''}>
          {document.do_khan || '-'}
        </Badge>
      ),
    },
    {
      label: t('documents.loaiVanBan'),
      value: CONG_VAN_LABELS[document.cong_van_den_di] || '-',
    },
    {
      label: t('documents.daDoc'),
      value: document.is_read ? t('documents.daDoc') : t('documents.chuaDoc'),
      custom: (
        <span
          className={cn(
            'inline-flex items-center gap-1.5 text-sm font-medium',
            document.is_read ? 'text-green-600' : 'text-amber-600'
          )}
        >
          {document.is_read ? <Eye size={16} /> : <EyeOff size={16} />}
          {document.is_read ? t('documents.daDoc') : t('documents.chuaDoc')}
        </span>
      ),
    },
  ];

  return (
    <div className="p-6 space-y-4 max-w-4xl">
      {/* Header */}
      <div className="flex items-center gap-3">
        <Button variant="outline" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft size={16} />
          {t('common.back')}
        </Button>
        <div className="flex-1">
          <h1 className="text-xl font-bold text-slate-900">{t('documents.detail')}</h1>
        </div>
        {document.raw_data && (
          <Button
            variant="outline"
            size="sm"
            onClick={() => setShowRawData(!showRawData)}
          >
            <Code size={16} />
            {showRawData ? t('documents.hideRawData') : t('documents.showRawData')}
          </Button>
        )}
      </div>

      {/* Main info card */}
      <Card>
        <CardHeader className="flex-row items-center gap-3">
          <h2 className="font-semibold text-slate-900 flex-1">
            {document.trich_yeu || document.so_ki_hieu || t('documents.detail')}
          </h2>
          {document.do_khan && (
            <Badge variant="urgency" urgency={document.do_khan} size="md">
              {document.do_khan}
            </Badge>
          )}
          <span className="text-xs font-medium px-2 py-1 rounded-full bg-slate-100 text-slate-600">
            {CONG_VAN_LABELS[document.cong_van_den_di] || '-'}
          </span>
        </CardHeader>
        <CardContent className="p-0">
          <div className="divide-y divide-slate-100">
            {detailFields.map(({ label, value, custom }) => (
              <div
                key={label}
                className={cn('flex gap-4 px-5 py-3', custom ? 'items-center' : '')}
              >
                <span className="text-sm text-slate-500 w-44 flex-shrink-0">{label}</span>
                {custom ? (
                  <span className="text-sm text-slate-900">{custom}</span>
                ) : (
                  <span className="text-sm text-slate-900 font-medium">{value || '-'}</span>
                )}
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Raw data */}
      {showRawData && document.raw_data && (
        <Card>
          <CardHeader>
            <h3 className="font-semibold text-slate-900">{t('documents.rawData')}</h3>
          </CardHeader>
          <CardContent>
            <pre className="text-xs bg-slate-50 rounded-lg p-4 overflow-auto max-h-96 font-mono text-slate-700">
              {JSON.stringify(document.raw_data, null, 2)}
            </pre>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
