import { Admin, AdminListResponse, AdminFilters, CreateAdminRequest, UpdateAdminRequest } from '@/types/admin';
import api from '@/utils/api';

class AdminService {
  async getAdmins(filters: AdminFilters = {}): Promise<AdminListResponse> {
    const response = await api.get('/admin/admins', { params: filters });
    return response.data;
  }

  async getAdminById(id: number): Promise<Admin> {
    const response = await api.get(`/admin/admins/${id}`);
    return response.data;
  }

  async createAdmin(adminData: CreateAdminRequest): Promise<Admin> {
    const response = await api.post('/admin/admins', adminData);
    return response.data;
  }

  async updateAdmin(id: number, adminData: UpdateAdminRequest): Promise<Admin> {
    const response = await api.put(`/admin/admins/${id}`, adminData);
    return response.data;
  }

  async deleteAdmin(id: number): Promise<void> {
    await api.delete(`/admin/admins/${id}`);
  }

  async uploadAvatar(id: number, file: File): Promise<Admin> {
    const formData = new FormData();
    formData.append('avatar', file);
    
    const response = await api.post(`/admin/admins/${id}/avatar`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    return response.data;
  }

  async deleteAvatar(id: number): Promise<void> {
    await api.delete(`/admin/admins/${id}/avatar`);
  }
}

export const adminService = new AdminService();
