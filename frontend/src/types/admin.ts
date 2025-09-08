export interface Role {
  id: number;
  name: string;
  guard_name: string;
  created_at: string;
  updated_at: string;
}

export interface Admin {
  id: number;
  name: string;
  email: string;
  username?: string;
  avatar?: string;
  avatar_url?: string;
  status: 'active' | 'inactive' | 'suspended';
  role: string; // Primary role for data table
  roles: string[]; // All roles as array
  permissions: string[];
  last_login_at?: string;
  last_login_ip?: string;
  email_verified_at?: string;
  two_factor_enabled?: boolean;
  created_at: string;
  updated_at: string;
}

// Backend Response Types
export interface BackendResponse<T = any> {
  success: boolean;
  message: string;
  timestamp: string;
  status_code: number;
  data?: T;
  errors?: Record<string, string[]>;
}

export interface AdminListResponse {
  data: Admin[];
  pagination: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    has_more_pages: boolean;
  };
  message: string;
}

export interface AdminSingleResponse {
  data: Admin;
  message: string;
}

export interface AdminFilters {
  search?: string;
  status?: string;
  role?: string;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  page?: number;
  per_page?: number;
}

export interface CreateAdminRequest {
  name: string;
  email: string;
  username?: string;
  password: string;
  password_confirmation: string;
  status?: 'active' | 'inactive' | 'suspended';
  roles?: string[];
}

export interface UpdateAdminRequest {
  name?: string;
  email?: string;
  username?: string;
  password?: string;
  password_confirmation?: string;
  status?: 'active' | 'inactive' | 'suspended';
  roles?: string[];
}

export interface AdminListState {
  data: Admin[];
  loading: boolean;
  error: string | null;
  pagination: PaginationState;
  filters: AdminFilters;
  isRefreshing?: boolean;
}

export interface PaginationState {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
  has_more_pages: boolean;
}

export interface AdminTableColumn {
  key: keyof Admin | 'actions';
  label: string;
  sortable: boolean;
  filterable: boolean;
  width?: string;
  align?: 'left' | 'center' | 'right';
}

export interface AdminTableConfig {
  columns: AdminTableColumn[];
  defaultSort: {
    field: keyof Admin;
    direction: 'asc' | 'desc';
  };
  pageSizeOptions: number[];
  defaultPageSize: number;
}

export interface AdminListActions {
  onCreate: () => void;
  onEdit: (admin: Admin) => void;
  onDelete: (admin: Admin) => void;
  onView: (admin: Admin) => void;
  onRefresh: () => void;
}

export interface CreateAdminResponse {
  data: Admin;
  message: string;
}

export interface UpdateAdminResponse {
  data: Admin;
  message: string;
}
