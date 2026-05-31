export const API_ENDPOINTS = {
  // Auth
  REGISTER: '/register',
  LOGIN: '/login',
  REFRESH: '/refresh',
  LOGOUT: '/logout',
  ME: '/me',

  // VNPT Office
  VNPT_LOGIN: '/office-vnpt/quangngai/login',
  VNPT_CREDENTIALS: '/office-vnpt/credentials',
  VNPT_DOCUMENTS: '/office-vnpt/quangngai/documents',
  VNPT_DOCUMENTS_SYNC: '/office-vnpt/quangngai/documents/sync',
} as const;

export const URGENCY_LEVELS = {
  HOA_TOC: 'Hỏa tốc',
  KHAN: 'Khẩn',
  BINH_THUONG: 'Bình thường',
} as const;

export const URGENCY_COLORS: Record<string, { bg: string; text: string; dot: string }> = {
  'Hỏa tốc': { bg: 'bg-red-50', text: 'text-red-700', dot: 'bg-red-500' },
  'Khẩn': { bg: 'bg-amber-50', text: 'text-amber-700', dot: 'bg-amber-500' },
  'Bình thường': { bg: 'bg-green-50', text: 'text-green-700', dot: 'bg-green-500' },
  default: { bg: 'bg-gray-50', text: 'text-gray-700', dot: 'bg-gray-400' },
};

export const CONG_VAN_LABELS: Record<string, string> = {
  '1': 'Văn bản đến',
  '0': 'Văn bản đi',
};

export const APP_NAME = import.meta.env.VITE_APP_NAME || 'Task Management';
export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';
