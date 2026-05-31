import axiosClient from './axiosClient';
import { API_ENDPOINTS } from '@/lib/constants';
import type { ApiResponse, DocumentSyncResponse } from '@/types';
import type { OfficeDocument } from '@/types';

export interface VnptCredentialPayload {
  username: string;
  password: string;
  device_name?: string;
  device_type?: 'IOS' | 'ANDROID';
}

export interface VnptCredentialResponse {
  username: string;
  device_name: string | null;
  device_type: string;
  is_active: boolean;
}

export const officeApi = {
  getVnptCredential: async (): Promise<VnptCredentialResponse | null> => {
    const { data } = await axiosClient.get<ApiResponse<VnptCredentialResponse | null>>(
      API_ENDPOINTS.VNPT_CREDENTIALS
    );
    return data.data ?? null;
  },

  saveVnptCredential: async (payload: VnptCredentialPayload): Promise<VnptCredentialResponse> => {
    const { data } = await axiosClient.post<ApiResponse<VnptCredentialResponse>>(
      API_ENDPOINTS.VNPT_CREDENTIALS,
      payload
    );
    return data.data as VnptCredentialResponse;
  },

  deleteVnptCredential: async (): Promise<void> => {
    await axiosClient.delete<ApiResponse<null>>(API_ENDPOINTS.VNPT_CREDENTIALS);
  },

  getDocuments: async (payload?: {
    param?: string;
    pageNo?: number;
    pageRec?: number;
    kho?: string;
  }): Promise<OfficeDocument[]> => {
    const { data } = await axiosClient.post<ApiResponse<OfficeDocument[]>>(
      API_ENDPOINTS.VNPT_DOCUMENTS,
      payload
    );
    return data.data ?? [];
  },

  syncDocuments: async (payload?: {
    param?: string;
    pageNo?: number;
    pageRec?: number;
    kho?: string;
  }): Promise<DocumentSyncResponse> => {
    const { data } = await axiosClient.post<ApiResponse<OfficeDocument[]> & { saved_count: number }>(
      API_ENDPOINTS.VNPT_DOCUMENTS_SYNC,
      payload ?? {}
    );
    return {
      data: data.data ?? [],
      saved_count: (data as unknown as { saved_count: number }).saved_count ?? data.data?.length ?? 0,
    };
  },
};
