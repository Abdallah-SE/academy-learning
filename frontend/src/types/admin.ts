export interface Admin {
  id: number;
  name: string;
  email: string;
  username?: string;
  avatar?: string;
  avatar_url?: string;
  status: 'active' | 'inactive' | 'suspended';
  role: string;
  permissions: string[];
  last_login_at?: string;
  created_at: string;
  updated_at: string;
}

export interface AdminListResponse {
  data: Admin[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
  has_more_pages: boolean;
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
  role: string;
  permissions?: string[];
}

export interface UpdateAdminRequest {
  name?: string;
  email?: string;
  username?: string;
  role?: string;
  permissions?: string[];
  status?: 'active' | 'inactive' | 'suspended';
}

export interface AdminListState {
  data: Admin[];
  loading: boolean;
  error: string | null;
  pagination: PaginationState;
  filters: AdminFilters;
  selectedAdmins: number[];
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
  onBulkDelete: () => void;
  onExport: (format: 'csv' | 'excel' | 'pdf') => void;
  onRefresh: () => void;
}
