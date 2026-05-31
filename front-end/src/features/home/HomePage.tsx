import { useEffect, useMemo, useState } from 'react';
import { DashboardPage } from '@/features/dashboard';
import { DocumentListPage } from '@/features/documents';
import { officeApi } from '@/api';
import { isOverdue } from '@/lib/utils';
import type { DocumentStats, OfficeDocument } from '@/types';

export function HomePage() {
  const [documents, setDocuments] = useState<OfficeDocument[]>([]);

  useEffect(() => {
    officeApi.syncDocuments().then((res) => {
      setDocuments(res.data);
    }).catch(() => {});
  }, []);

  const stats = useMemo<DocumentStats>(() => {
    return {
      total: documents.length,
      den: documents.filter((d) => d.cong_van_den_di === '1').length,
      di: documents.filter((d) => d.cong_van_den_di === '0').length,
      cho_xu_ly: documents.filter((d) => !d.is_read && d.han_xu_ly && !isOverdue(d.han_xu_ly)).length,
      chua_doc: documents.filter((d) => !d.is_read).length,
      qua_han: documents.filter((d) => d.han_xu_ly && isOverdue(d.han_xu_ly)).length,
    };
  }, [documents]);

  const urgencyData = useMemo(() => {
    const counts: Record<string, number> = {};
    documents.forEach((d) => {
      const key = d.do_khan || 'Khác';
      counts[key] = (counts[key] || 0) + 1;
    });
    return Object.entries(counts).map(([name, value]) => ({
      name,
      value,
      color:
        name === 'Hỏa tốc'
          ? '#ef4444'
          : name === 'Khẩn'
          ? '#f59e0b'
          : '#22c55e',
    }));
  }, [documents]);

  const monthlyData = useMemo(() => {
    const months: Record<string, number> = {};
    documents.forEach((d) => {
      if (!d.ngay_nhan) return;
      const month = d.ngay_nhan.substring(0, 7);
      months[month] = (months[month] || 0) + 1;
    });
    return Object.entries(months)
      .sort(([a], [b]) => a.localeCompare(b))
      .map(([month, count]) => ({ month, count }));
  }, [documents]);

  return (
    <div className="p-6 space-y-6">
      <DashboardPage stats={stats} isLoading={false} urgencyData={urgencyData} monthlyData={monthlyData} />
      <DocumentListPage documents={documents} isLoading={false} />
    </div>
  );
}
