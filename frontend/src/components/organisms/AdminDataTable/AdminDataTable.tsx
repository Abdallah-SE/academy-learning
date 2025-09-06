'use client';

import React, { useMemo, useState, useCallback, memo } from 'react';
import Image from 'next/image';
import {
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
  ColumnDef,
  SortingState,
  ColumnFiltersState,
  getFacetedRowModel,
  getFacetedUniqueValues,
} from '@tanstack/react-table';
import { Admin, AdminTableConfig, AdminListActions } from '@/types/admin';
import { AdminTableHeader } from './AdminTableHeader';
import { AdminTableBody } from './AdminTableBody';
import { AdminTablePagination } from './AdminTablePagination';
import { AdminTableToolbar } from './AdminTableToolbar';
import { AdminTableEmptyState } from './AdminTableEmptyState';
import { AdminTableLoadingState } from './AdminTableLoadingState';
import { EyeIcon, EditIcon, TrashIcon } from 'lucide-react';

// Memoized Avatar Cell Component
const AvatarCell = memo(({ admin }: { admin: Admin }) => {
  const avatarUrl = admin.avatar_url || admin.avatar;
  const initials = admin.name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);

  return (
    <div className="flex items-center justify-center w-12 h-12 flex-shrink-0">
      {avatarUrl ? (
        <Image
          src={avatarUrl}
          alt={`${admin.name}'s avatar`}
          width={48}
          height={48}
          className="w-12 h-12 rounded-full object-cover border-2 border-gray-200 hover:border-blue-400 transition-all duration-200 shadow-sm hover:shadow-md"
          unoptimized={true}
        />
      ) : (
        <div className="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-sm border-2 border-gray-200 hover:border-blue-400 transition-all duration-200 shadow-sm hover:shadow-md">
          {initials}
        </div>
      )}
    </div>
  );
});

AvatarCell.displayName = 'AvatarCell';

interface AdminDataTableProps {
  data: Admin[];
  loading?: boolean;
  error?: string | null;
  pagination: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    has_more_pages: boolean;
  };
  selectedAdmins: number[];
  onSelectAdmin: (adminId: number, selected: boolean) => void;
  onSelectAll: (selected: boolean) => void;
  onPageChange: (page: number) => void;
  onPerPageChange: (perPage: number) => void;
  onRefresh: () => void;
  actions: AdminListActions;
  config?: Partial<AdminTableConfig>;
}


export const AdminDataTable: React.FC<AdminDataTableProps> = memo(({
  data,
  loading = false,
  error = null,
  pagination,
  selectedAdmins,
  onSelectAdmin,
  onSelectAll,
  onPageChange,
  onPerPageChange,
  onRefresh,
  actions,
  config,
}) => {
  const [sorting, setSorting] = useState<SortingState>([]);
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
  const [globalFilter, setGlobalFilter] = useState('');

  const defaultConfig: AdminTableConfig = {
    columns: [
      { key: 'avatar', label: '', sortable: false, filterable: false, width: '80px', align: 'center' },
      { key: 'name', label: 'Name', sortable: true, filterable: true, width: '250px' },
      { key: 'username', label: 'Username', sortable: true, filterable: true, width: '150px' },
      { key: 'role', label: 'Role', sortable: true, filterable: true, width: '120px' },
      { key: 'status', label: 'Status', sortable: true, filterable: true, width: '120px' },
      { key: 'last_login_at', label: 'Last Login', sortable: true, filterable: false, width: '150px' },
      { key: 'created_at', label: 'Created', sortable: true, filterable: false, width: '150px' },
      { key: 'actions', label: 'Actions', sortable: false, filterable: false, width: '120px', align: 'center' },
    ],
    defaultSort: { field: 'created_at', direction: 'desc' },
    pageSizeOptions: [5, 10, 20, 50, 100],
    defaultPageSize: 10,
    ...config,
  };

  // Memoized action handlers to prevent column recreation
  const handleEdit = useCallback((admin: Admin) => {
    actions.onEdit(admin);
  }, [actions]);

  const handleDelete = useCallback((admin: Admin) => {
    actions.onDelete(admin);
  }, [actions]);

  const handleView = useCallback((admin: Admin) => {
    actions.onView(admin);
  }, [actions]);

  const columns = useMemo<ColumnDef<Admin>[]>(
    () => [
      {
        id: 'select',
        header: ({ table }) => (
          <input
            type="checkbox"
            checked={table.getIsAllPageRowsSelected()}
            onChange={(e) => onSelectAll(!!e.target.checked)}
            className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          />
        ),
        cell: ({ row }) => (
          <input
            type="checkbox"
            checked={selectedAdmins.includes(row.original.id)}
            onChange={(e) => onSelectAdmin(row.original.id, !!e.target.checked)}
            className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          />
        ),
        enableSorting: false,
        enableHiding: false,
        size: 50,
      },
      {
        accessorKey: 'name',
        header: 'Admin',
        cell: ({ row }) => {
          const admin = row.original;
          return (
            <div className="flex items-center space-x-3">
              <AvatarCell admin={admin} />
              <div className="flex flex-col min-w-0 flex-1">
                <span className="text-sm font-semibold text-gray-900 truncate">
                  {admin.name}
                </span>
                <span className="text-xs text-gray-500 truncate">
                  {admin.email}
                </span>
              </div>
            </div>
          );
        },
        size: 300,
      },
      {
        accessorKey: 'username',
        header: 'Username',
        cell: ({ row }) => {
          const username = row.getValue('username') as string;
          return (
            <div className="flex items-center">
              <span className="text-sm font-medium text-gray-900">
                {username || '-'}
              </span>
            </div>
          );
        },
        size: 140,
      },
      {
        accessorKey: 'role',
        header: 'Role',
        cell: ({ row }) => {
          const role = row.getValue('role') as string;
          const getRoleConfig = (role: string) => {
            switch (role) {
              case 'super_admin':
                return { 
                  color: 'bg-red-50 text-red-700 border-red-200', 
                  label: 'Super Admin',
                  icon: '👑'
                };
              case 'admin':
                return { 
                  color: 'bg-blue-50 text-blue-700 border-blue-200', 
                  label: 'Admin',
                  icon: '👨‍💼'
                };
              case 'moderator':
                return { 
                  color: 'bg-green-50 text-green-700 border-green-200', 
                  label: 'Moderator',
                  icon: '🛡️'
                };
              default:
                return { 
                  color: 'bg-gray-50 text-gray-700 border-gray-200', 
                  label: 'Unknown',
                  icon: '❓'
                };
            }
          };

          const config = getRoleConfig(role);
          return (
            <div className="flex items-center">
              <span className={`inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold border ${config.color}`}>
                <span className="mr-1.5">{config.icon}</span>
                {config.label}
              </span>
            </div>
          );
        },
        size: 130,
      },
      {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
          const status = row.getValue('status') as string;
          const statusConfig = {
            active: { 
              bg: 'bg-green-50', 
              text: 'text-green-700', 
              border: 'border-green-200',
              label: 'Active',
              icon: '🟢'
            },
            inactive: { 
              bg: 'bg-gray-50', 
              text: 'text-gray-700', 
              border: 'border-gray-200',
              label: 'Inactive',
              icon: '⚪'
            },
            suspended: { 
              bg: 'bg-red-50', 
              text: 'text-red-700', 
              border: 'border-red-200',
              label: 'Suspended',
              icon: '🔴'
            },
          };
          const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.inactive;
          
          return (
            <div className="flex items-center">
              <span className={`inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold border ${config.bg} ${config.text} ${config.border}`}>
                <span className="mr-1.5">{config.icon}</span>
                {config.label}
              </span>
            </div>
          );
        },
        size: 120,
      },
      {
        accessorKey: 'last_login_at',
        header: 'Last Login',
        cell: ({ row }) => {
          const lastLogin = row.getValue('last_login_at') as string;
          const formatDate = (dateString: string) => {
            const date = new Date(dateString);
            const now = new Date();
            const diffInHours = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60));
            
            if (diffInHours < 1) return 'Just now';
            if (diffInHours < 24) return `${diffInHours}h ago`;
            if (diffInHours < 168) return `${Math.floor(diffInHours / 24)}d ago`;
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
          };

          return (
            <div className="flex flex-col">
              <span className="text-sm font-medium text-gray-900">
                {lastLogin ? formatDate(lastLogin) : 'Never'}
              </span>
              {lastLogin && (
                <span className="text-xs text-gray-500">
                  {new Date(lastLogin).toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                  })}
                </span>
              )}
            </div>
          );
        },
        size: 140,
      },
      {
        accessorKey: 'created_at',
        header: 'Created',
        cell: ({ row }) => {
          const createdAt = row.getValue('created_at') as string;
          const date = new Date(createdAt);
          return (
            <div className="flex flex-col">
              <span className="text-sm font-medium text-gray-900">
                {date.toLocaleDateString('en-US', { 
                  month: 'short', 
                  day: 'numeric', 
                  year: 'numeric' 
                })}
              </span>
              <span className="text-xs text-gray-500">
                {date.toLocaleTimeString('en-US', { 
                  hour: '2-digit', 
                  minute: '2-digit' 
                })}
              </span>
            </div>
          );
        },
        size: 140,
      },
      {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
          const admin = row.original;
          return (
            <div className="flex items-center justify-center space-x-1">
              {actions.onView && (
                <button
                  onClick={() => handleView(admin)}
                  className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group"
                  title="View Details"
                >
                  <EyeIcon className="w-4 h-4 group-hover:scale-110 transition-transform" />
                </button>
              )}
              {actions.onEdit && (
                <button
                  onClick={() => handleEdit(admin)}
                  className="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 group"
                  title="Edit Admin"
                >
                  <EditIcon className="w-4 h-4 group-hover:scale-110 transition-transform" />
                </button>
              )}
              {actions.onDelete && (
                <button
                  onClick={() => handleDelete(admin)}
                  className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group"
                  title="Delete Admin"
                >
                  <TrashIcon className="w-4 h-4 group-hover:scale-110 transition-transform" />
                </button>
              )}
            </div>
          );
        },
        enableSorting: false,
        enableHiding: false,
        size: 120,
      },
    ],
    [selectedAdmins, onSelectAdmin, onSelectAll, actions, handleEdit, handleDelete, handleView]
  );

  const table = useReactTable({
    data,
    columns,
    state: {
      sorting,
      columnFilters,
      globalFilter,
      pagination: {
        pageIndex: pagination.current_page - 1,
        pageSize: pagination.per_page,
      },
      rowSelection: {},
    },
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    onGlobalFilterChange: setGlobalFilter,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    enableRowSelection: true,
    enableMultiRowSelection: true,
    manualPagination: true,
    pageCount: pagination.last_page,
    // Disable client-side pagination since we're using server-side
    enablePagination: false,
  });

  if (loading) {
    return <AdminTableLoadingState />;
  }

  if (error) {
    return (
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div className="text-center">
          <div className="text-red-500 mb-2">Error loading admins</div>
          <p className="text-gray-500">{error}</p>
          <button
            onClick={onRefresh}
            className="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
      <AdminTableToolbar
        selectedCount={selectedAdmins.length}
        totalCount={pagination.total}
        onRefresh={onRefresh}
        onBulkDelete={actions.onBulkDelete}
        onSelectAll={onSelectAll}
        allSelected={selectedAdmins.length === data.length && data.length > 0}
      />
      
      <div className="overflow-hidden">
        <AdminTableHeader table={table} />
        
        <AdminTableBody
          table={table}
          data={data}
          selectedAdmins={selectedAdmins}
          onSelectAdmin={onSelectAdmin}
        />
      </div>
      
      {data.length === 0 && (
        <AdminTableEmptyState
          hasFilters={!!(globalFilter || columnFilters.length > 0)}
          onClearFilters={() => {
            setGlobalFilter('');
            setColumnFilters([]);
          }}
        />
      )}
      
      <AdminTablePagination
        pagination={pagination}
        onPageChange={onPageChange}
        onPerPageChange={onPerPageChange}
        pageSizeOptions={defaultConfig.pageSizeOptions}
      />
    </div>
  );
});

AdminDataTable.displayName = 'AdminDataTable';
