import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface VNPTState {
  credentials: { username: string; password: string } | null;
  setCredentials: (creds: { username: string; password: string } | null) => void;
}

export const useVNPTStore = create<VNPTState>()(
  persist(
    (set) => ({
      credentials: null,
      setCredentials: (credentials) => set({ credentials }),
    }),
    { name: 'vnpt-storage' }
  )
);
