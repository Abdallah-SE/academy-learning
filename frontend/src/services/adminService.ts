import { 
  Admin, 
  AdminListResponse, 
  AdminFilters, 
  CreateAdminRequest, 
  UpdateAdminRequest,
  AdminSingleResponse,
  CreateAdminResponse,
  UpdateAdminResponse,
  BackendResponse
} from '@/types/admin';
import api from '@/utils/api';

class AdminService {
  async getAdmins(filters: AdminFilters = {}): Promise<AdminListResponse> {
    const response = await api.get('/admin/admins', { params: filters });
    return response.data;
  }

  async getAdminById(id: number): Promise<AdminSingleResponse> {
    const response = await api.get(`/admin/admins/${id}`);
    return response.data;
  }

  async createAdmin(adminData: CreateAdminRequest): Promise<CreateAdminResponse> {
    const response = await api.post('/admin/admins', adminData);
    return response.data;
  }

  async updateAdmin(id: number, adminData: UpdateAdminRequest): Promise<UpdateAdminResponse> {
    const response = await api.put(`/admin/admins/${id}`, adminData);
    return response.data;
  }

  async deleteAdmin(id: number): Promise<BackendResponse> {
    const response = await api.delete(`/admin/admins/${id}`);
    return response.data;
  }

  async uploadAvatar(id: number, file: File): Promise<AdminSingleResponse> {
    const formData = new FormData();
    formData.append('avatar', file);
    
    const response = await api.post(`/admin/admins/${id}/avatar`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    return response.data;
  }

  async deleteAvatar(id: number): Promise<BackendResponse> {
    const response = await api.delete(`/admin/admins/${id}/avatar`);
    return response.data;
  }
}

export const adminService = new AdminService();
