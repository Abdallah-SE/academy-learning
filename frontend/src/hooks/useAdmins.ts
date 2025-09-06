import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { adminService } from '@/services/adminService';
import { Admin, AdminFilters, CreateAdminRequest, UpdateAdminRequest } from '@/types/admin';

export const useAdmins = (filters: AdminFilters = {}) => {
  return useQuery({
    queryKey: ['admins', filters],
    queryFn: () => adminService.getAdmins(filters),
    keepPreviousData: true,
  });
};

export const useAdmin = (id: number) => {
  return useQuery({
    queryKey: ['admin', id],
    queryFn: () => adminService.getAdminById(id),
    enabled: !!id,
  });
};

export const useCreateAdmin = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (adminData: CreateAdminRequest) => adminService.createAdmin(adminData),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['admins'] });
    },
  });
};

export const useUpdateAdmin = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, data }: { id: number; data: UpdateAdminRequest }) =>
      adminService.updateAdmin(id, data),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['admins'] });
      queryClient.invalidateQueries({ queryKey: ['admin', id] });
    },
  });
};

export const useDeleteAdmin = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number) => adminService.deleteAdmin(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['admins'] });
    },
  });
};

export const useUploadAvatar = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, file }: { id: number; file: File }) =>
      adminService.uploadAvatar(id, file),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['admins'] });
      queryClient.invalidateQueries({ queryKey: ['admin', id] });
    },
  });
};
