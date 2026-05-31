export interface User {
  id: number;
  name: string;
  email: string;
  role: 'user' | 'admin' | 'moderator';
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface VNPTOfficeLoginRequest {
  username: string;
  password: string;
  tokenFireBase?: string;
  language?: string;
  type?: string;
  device?: string;
}
