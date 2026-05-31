export type DoKhan = 'Hỏa tốc' | 'Khẩn' | 'Bình thường' | string;

export interface OfficeDocument {
  id: number;
  external_id: string;
  trich_yeu: string | null;
  so_ki_hieu: string | null;
  co_quan_ban_hanh: string | null;
  ngay_van_ban: string | null;
  han_xu_ly: string | null;
  ngay_den_di: string | null;
  ngay_nhan: string | null;
  do_khan: DoKhan | null;
  cong_van_den_di: string;
  process_definition_id: string | null;
  process_instance_id: string | null;
  is_read: boolean;
  type: string | null;
  raw_data: Record<string, unknown> | null;
  user_id: number | null;
  created_at: string;
  updated_at: string;
}

export interface DocumentListRequest {
  param?: string;
  pageNo?: number;
  pageRec?: number;
  kho?: string;
  credentials?: {
    username: string;
    password: string;
  };
}

export interface DocumentSyncResponse {
  data: OfficeDocument[];
  saved_count: number;
}

export interface DocumentStats {
  total: number;
  den: number;
  di: number;
  cho_xu_ly: number;
  chua_doc: number;
  qua_han: number;
}
