import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from './Button';
import { cn } from '@/lib/utils';

interface PaginationProps {
  currentPage: number;
  totalPages: number;
  onPageChange: (page: number) => void;
  className?: string;
}

export function Pagination({ currentPage, totalPages, onPageChange, className }: PaginationProps) {
  if (totalPages <= 1) return null;

  const pages = Array.from({ length: totalPages }, (_, i) => i + 1);
  const visiblePages = pages.filter((p) => {
    if (totalPages <= 7) return true;
    if (p === 1 || p === totalPages) return true;
    if (Math.abs(p - currentPage) <= 1) return true;
    return false;
  });

  return (
    <div className={cn('flex items-center gap-1 justify-center', className)}>
      <Button
        variant="outline"
        size="sm"
        disabled={currentPage === 1}
        onClick={() => onPageChange(currentPage - 1)}
        className="w-8 h-8 p-0"
      >
        <ChevronLeft size={16} />
      </Button>

      {visiblePages.map((page, idx) => {
        const prev = visiblePages[idx - 1] as number;
        const showEllipsisBefore = idx > 0 && (page as number) - prev > 1;

        return (
          <div key={page} className="flex items-center">
            {showEllipsisBefore && (
              <span className="px-2 text-slate-400">...</span>
            )}
            <Button
              variant={currentPage === page ? 'primary' : 'outline'}
              size="sm"
              onClick={() => onPageChange(page as number)}
              className="w-8 h-8 p-0"
            >
              {page}
            </Button>
          </div>
        );
      })}

      <Button
        variant="outline"
        size="sm"
        disabled={currentPage === totalPages}
        onClick={() => onPageChange(currentPage + 1)}
        className="w-8 h-8 p-0"
      >
        <ChevronRight size={16} />
      </Button>
    </div>
  );
}
