import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { officeApi } from '@/api';
import type { VnptCredentialPayload } from '@/api/officeApi';

export function useVnptCredential() {
  return useQuery({
    queryKey: ['vnpt-credential'],
    queryFn: () => officeApi.getVnptCredential(),
    staleTime: 1000 * 60 * 5,
  });
}

export function useSaveVnptCredential() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (payload: VnptCredentialPayload) => officeApi.saveVnptCredential(payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['vnpt-credential'] });
    },
  });
}

export function useDeleteVnptCredential() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: () => officeApi.deleteVnptCredential(),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['vnpt-credential'] });
    },
  });
}

export function useSyncDocuments() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (payload?: { param?: string; pageNo?: number; pageRec?: number; kho?: string }) =>
      officeApi.syncDocuments(payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['documents'] });
    },
  });
}
