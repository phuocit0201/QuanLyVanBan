import axiosClient from './axiosClient';
import { API_ENDPOINTS } from '@/lib/constants';
import type { ApiResponse, AuthResponse, TokenResponse, User } from '@/types';
import type { LoginRequest, RegisterRequest } from '@/types';

export const authApi = {
  register: async (payload: RegisterRequest): Promise<AuthResponse> => {
    const { data } = await axiosClient.post<ApiResponse<AuthResponse>>(API_ENDPOINTS.REGISTER, payload);
    if (data.data) {
      localStorage.setItem('access_token', data.data.token.access_token);
    }
    return data.data!;
  },

  login: async (payload: LoginRequest): Promise<TokenResponse> => {
    const { data } = await axiosClient.post<ApiResponse<{ token: TokenResponse }>>(API_ENDPOINTS.LOGIN, payload);
    if (data.data) {
      localStorage.setItem('access_token', data.data.token.access_token);
    }
    return data.data!.token;
  },

  refresh: async (): Promise<TokenResponse> => {
    const { data } = await axiosClient.post<ApiResponse<{ token: TokenResponse }>>(API_ENDPOINTS.REFRESH);
    if (data.data) {
      localStorage.setItem('access_token', data.data.token.access_token);
    }
    return data.data!.token;
  },

  logout: async (): Promise<void> => {
    try {
      await axiosClient.post(API_ENDPOINTS.LOGOUT);
    } finally {
      localStorage.removeItem('access_token');
    }
  },

  me: async (): Promise<User> => {
    const { data } = await axiosClient.get<ApiResponse<{ user: User }>>(API_ENDPOINTS.ME);
    return data.data!.user;
  },
};
