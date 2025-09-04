import api from "@/utils/api";

export interface AdminLoginData {
  email: string;
  password: string;
  remember?: boolean; // ✅ Add remember me option
}

export interface AdminUser {
  id: number;
  name: string;
  email: string;
  username: string;
  avatar: string | null;
  avatar_url?: string;
  status: string;
  last_login_at?: string;
  email_verified_at?: string;
  two_factor_enabled?: boolean;
}

export interface AdminLoginResponse {
  success: boolean;
  message: string;
  data?: {
    admin: AdminUser;
    // ✅ Remove token fields - not needed with HttpOnly cookies
    permissions: string[];
    roles: string[];
  };
}

export interface AdminProfileResponse {
  success: boolean;
  message: string;
  data?: {
    admin: AdminUser & {
      roles: any[];
      permissions: any[];
    };
  };
}

export const AdminRepository = {
  login: async (data: AdminLoginData): Promise<AdminLoginResponse> => {
    const response = await api.post("/admin/auth/login", data);
    return response.data;
  },
  
  logout: async (): Promise<void> => {
    await api.post("/admin/auth/logout");
  },
  
  getProfile: async (): Promise<AdminProfileResponse> => {
    const response = await api.get("/admin/auth/profile");
    return response.data;
  },
};