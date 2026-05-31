import { cn } from '@/lib/utils';
import { URGENCY_COLORS } from '@/lib/constants';

interface BadgeProps {
  variant?: 'default' | 'success' | 'warning' | 'danger' | 'info' | 'urgency';
  size?: 'sm' | 'md';
  className?: string;
  children: React.ReactNode;
  urgency?: string;
}

export function Badge({ variant = 'default', size = 'sm', className, children, urgency }: BadgeProps) {
  const baseClasses = 'inline-flex items-center gap-1 font-medium rounded-full';

  const variantClasses: Record<string, string> = {
    default: 'bg-slate-100 text-slate-700',
    success: 'bg-green-50 text-green-700',
    warning: 'bg-amber-50 text-amber-700',
    danger: 'bg-red-50 text-red-700',
    info: 'bg-blue-50 text-blue-700',
    urgency: '',
  };

  const sizeClasses: Record<string, string> = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-sm',
  };

  const urgencyStyle = urgency ? URGENCY_COLORS[urgency] || URGENCY_COLORS.default : null;

  return (
    <span
      className={cn(
        baseClasses,
        sizeClasses[size],
        variant === 'urgency'
          ? urgencyStyle
            ? `${urgencyStyle.bg} ${urgencyStyle.text}`
            : 'bg-gray-100 text-gray-700'
          : variantClasses[variant],
        className
      )}
    >
      {urgency && urgencyStyle && (
        <span className={cn('w-1.5 h-1.5 rounded-full', urgencyStyle.dot)} />
      )}
      {children}
    </span>
  );
}
