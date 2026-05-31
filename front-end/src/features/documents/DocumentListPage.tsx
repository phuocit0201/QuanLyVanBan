import { useState, useMemo } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import {
  useReactTable,
  getCoreRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  type ColumnDef,
  type SortingState,
  type ColumnFiltersState,
} from '@tanstack/react-table';
import {
  Search,
  RefreshCw,
  Eye,
  FileText,
} from 'lucide-react';
import { Card, CardContent, Badge, Button, Input, Pagination, SkeletonTable } from '@/components/ui';
import { useSyncDocuments } from '@/hooks';
import { useToast } from '@/components/ui/Toast';
import { formatDate } from '@/lib/utils';
import { CONG_VAN_LABELS } from '@/lib/constants';
import type { OfficeDocument } from '@/types';

interface DocumentListPageProps {
  documents: OfficeDocument[];
  isLoading?: boolean;
}

const URGENCY_OPTIONS = ['all', 'Hỏa tốc', 'Khẩn', 'Bình thường'];
const TYPE_OPTIONS = ['all', '1', '0'];
const STATUS_OPTIONS = ['all', 'read', 'unread'];

export function DocumentListPage({ documents, isLoading }: DocumentListPageProps) {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { showToast } = useToast();
  const syncMutation = useSyncDocuments();

  const [sorting, setSorting] = useState<SortingState>([{ id: 'ngay_nhan', desc: true }]);
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
  const [globalFilter, setGlobalFilter] = useState('');
  const [urgencyFilter, setUrgencyFilter] = useState('all');
  const [typeFilter, setTypeFilter] = useState('all');
  const [statusFilter, setStatusFilter] = useState('all');
  const [currentPage, setCurrentPage] = useState(1);
  const pageSize = 10;

  const handleSync = async () => {
    try {
      const result = await syncMutation.mutateAsync({});
      showToast('success', t('documents.syncSuccess', { count: result.saved_count }));
    } catch (err: any) {
      showToast('error', err?.response?.data?.message || t('documents.syncError'));
    }
  };

  const filteredData = useMemo(() => {
    let data = documents;

    if (urgencyFilter !== 'all') {
      data = data.filter((d) => d.do_khan === urgencyFilter);
    }
    if (typeFilter !== 'all') {
      data = data.filter((d) => d.cong_van_den_di === typeFilter);
    }
    if (statusFilter !== 'all') {
      data = data.filter((d) => (statusFilter === 'read' ? d.is_read : !d.is_read));
    }

    return data;
  }, [documents, urgencyFilter, typeFilter, statusFilter]);

  const columns: ColumnDef<OfficeDocument>[] = useMemo(
    () => [
      {
        accessorKey: 'trich_yeu',
        header: t('documents.trichYeu'),
        cell: ({ row }) => (
          <div className="max-w-md">
            <p className="text-sm text-slate-900 line-clamp-2 font-medium">
              {row.original.trich_yeu || '-'}
            </p>
            {row.original.so_ki_hieu && (
              <p className="text-xs text-slate-500 mt-0.5">{row.original.so_ki_hieu}</p>
            )}
          </div>
        ),
      },
      {
        accessorKey: 'co_quan_ban_hanh',
        header: t('documents.coQuanBanHanh'),
        cell: ({ getValue }) => (
          <span className="text-sm text-slate-600">{getValue() as string || '-'}</span>
        ),
      },
      {
        accessorKey: 'ngay_nhan',
        header: t('documents.ngayNhan'),
        cell: ({ getValue }) => (
          <span className="text-sm text-slate-600">{formatDate(getValue() as string)}</span>
        ),
      },
      {
        accessorKey: 'do_khan',
        header: t('documents.doKhan'),
        cell: ({ getValue }) => {
          const val = getValue() as string;
          return (
            <Badge variant="urgency" urgency={val}>
              {val || '-'}
            </Badge>
          );
        },
      },
      {
        accessorKey: 'cong_van_den_di',
        header: t('documents.loaiVanBan'),
        cell: ({ getValue }) => (
          <span className="text-xs font-medium px-2 py-1 rounded-full bg-slate-100 text-slate-600">
            {CONG_VAN_LABELS[getValue() as string] || '-'}
          </span>
        ),
      },
      {
        accessorKey: 'is_read',
        header: '',
        cell: ({ row }) => (
          <div className="flex items-center gap-2">
            {!row.original.is_read && (
              <span className="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" title="Chưa đọc" />
            )}
            <Button
              variant="ghost"
              size="sm"
              onClick={() => navigate(`/documents/${row.original.id}`)}
              className="p-1.5 h-8"
            >
              <Eye size={16} />
            </Button>
          </div>
        ),
      },
    ],
    [t, navigate]
  );

  const table = useReactTable({
    data: filteredData,
    columns,
    state: { sorting, columnFilters, globalFilter },
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    onGlobalFilterChange: setGlobalFilter,
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    manualPagination: false,
    initialState: { pagination: { pageSize } },
  });

  const totalPages = Math.ceil(filteredData.length / pageSize);
  const paginatedData = filteredData.slice((currentPage - 1) * pageSize, currentPage * pageSize);

  const displayData = table.getFilteredRowModel().rows.length <= pageSize
    ? table.getRowModel().rows.map((r) => r.original)
    : paginatedData;

  return (
    <div className="p-6 space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">{t('documents.title')}</h1>
          <p className="text-slate-500 text-sm mt-1">
            {filteredData.length} {t('documents.title').toLowerCase()}
          </p>
        </div>
        <Button
          onClick={handleSync}
          isLoading={syncMutation.isPending}
          variant="primary"
          size="md"
        >
          <RefreshCw size={16} />
          {syncMutation.isPending ? t('documents.syncing') : t('documents.syncDocuments')}
        </Button>
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="p-4">
          <div className="flex flex-wrap gap-3 items-center">
            {/* Search */}
            <div className="flex-1 min-w-48">
              <Input
                placeholder={t('documents.searchPlaceholder')}
                icon={<Search size={16} />}
                value={globalFilter}
                onChange={(e) => {
                  setGlobalFilter(e.target.value);
                  setCurrentPage(1);
                }}
              />
            </div>

            {/* Urgency filter */}
            <select
              value={urgencyFilter}
              onChange={(e) => { setUrgencyFilter(e.target.value); setCurrentPage(1); }}
              className="px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="all">{t('documents.filterByUrgency')}</option>
              {URGENCY_OPTIONS.filter((o) => o !== 'all').map((opt) => (
                <option key={opt} value={opt}>{opt}</option>
              ))}
            </select>

            {/* Type filter */}
            <select
              value={typeFilter}
              onChange={(e) => { setTypeFilter(e.target.value); setCurrentPage(1); }}
              className="px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="all">{t('documents.filterByType')}</option>
              {TYPE_OPTIONS.filter((o) => o !== 'all').map((opt) => (
                <option key={opt} value={opt}>{CONG_VAN_LABELS[opt]}</option>
              ))}
            </select>

            {/* Status filter */}
            <select
              value={statusFilter}
              onChange={(e) => { setStatusFilter(e.target.value); setCurrentPage(1); }}
              className="px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="all">{t('documents.filterByStatus')}</option>
              {STATUS_OPTIONS.filter((o) => o !== 'all').map((opt) => (
                <option key={opt} value={opt}>
                  {opt === 'read' ? t('documents.daDoc') : t('documents.chuaDoc')}
                </option>
              ))}
            </select>
          </div>
        </CardContent>
      </Card>

      {/* Table */}
      <Card>
        {isLoading ? (
          <CardContent className="p-0">
            <SkeletonTable rows={8} />
          </CardContent>
        ) : filteredData.length === 0 ? (
          <CardContent className="py-16 text-center">
            <div className="flex flex-col items-center gap-3">
              <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                <FileText size={28} className="text-slate-400" />
              </div>
              <h3 className="font-semibold text-slate-700">{t('documents.empty')}</h3>
              <p className="text-sm text-slate-500 max-w-sm">{t('documents.emptyDesc')}</p>
            </div>
          </CardContent>
        ) : (
          <>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-slate-100">
                    <th className="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                      {t('documents.trichYeu')}
                    </th>
                    <th className="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                      {t('documents.coQuanBanHanh')}
                    </th>
                    <th className="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                      {t('documents.ngayNhan')}
                    </th>
                    <th className="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                      {t('documents.doKhan')}
                    </th>
                    <th className="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                      {t('documents.loaiVanBan')}
                    </th>
                    <th className="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-20">
                      &nbsp;
                    </th>
                  </tr>
                </thead>
                <tbody>
                  {displayData.map((doc) => (
                    <tr
                      key={doc.id}
                      onClick={() => navigate(`/documents/${doc.id}`)}
                      className="border-b border-slate-50 hover:bg-blue-50/50 cursor-pointer transition-colors"
                    >
                      {/* trich_yeu */}
                      <td className="px-5 py-3.5">
                        <div className="max-w-md">
                          <p className="text-sm text-slate-900 font-medium line-clamp-2">
                            {doc.trich_yeu || '-'}
                          </p>
                          {doc.so_ki_hieu && (
                            <p className="text-xs text-slate-500 mt-0.5">{doc.so_ki_hieu}</p>
                          )}
                        </div>
                      </td>
                      {/* co_quan */}
                      <td className="px-5 py-3.5 text-sm text-slate-600 max-w-40 truncate">
                        {doc.co_quan_ban_hanh || '-'}
                      </td>
                      {/* ngay_nhan */}
                      <td className="px-5 py-3.5 text-sm text-slate-600 whitespace-nowrap">
                        {formatDate(doc.ngay_nhan)}
                      </td>
                      {/* do_khan */}
                      <td className="px-5 py-3.5">
                        <Badge variant="urgency" urgency={doc.do_khan || ''}>
                          {doc.do_khan || '-'}
                        </Badge>
                      </td>
                      {/* loai */}
                      <td className="px-5 py-3.5">
                        <span className="text-xs font-medium px-2 py-1 rounded-full bg-slate-100 text-slate-600">
                          {CONG_VAN_LABELS[doc.cong_van_den_di] || '-'}
                        </span>
                      </td>
                      {/* actions */}
                      <td className="px-5 py-3.5">
                        <div className="flex items-center gap-2">
                          {!doc.is_read && (
                            <span className="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" title="Chưa đọc" />
                          )}
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={(e) => { e.stopPropagation(); navigate(`/documents/${doc.id}`); }}
                            className="p-1.5 h-8"
                          >
                            <Eye size={16} />
                          </Button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            <div className="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
              <p className="text-sm text-slate-500">
                {(currentPage - 1) * pageSize + 1}–{Math.min(currentPage * pageSize, filteredData.length)} / {filteredData.length}
              </p>
              <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={setCurrentPage}
              />
            </div>
          </>
        )}
      </Card>
    </div>
  );
}
