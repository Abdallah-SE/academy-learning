'use client';

import React, { useMemo, useState } from 'react';
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


export const AdminDataTable: React.FC<AdminDataTableProps> = ({
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
        accessorKey: 'avatar',
        header: '',
        cell: ({ row }) => {
          const admin = row.original;
          const avatarUrl = admin.avatar_url || admin.avatar;
          const initials = admin.name
            .split(' ')
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);

          return (
            <div className="flex items-center justify-center w-12 h-12">
              {avatarUrl ? (
                <Image
                  src={avatarUrl}
                  alt={`${admin.name}'s avatar`}
                  width={48}
                  height={48}
                  className="w-12 h-12 rounded-full object-cover border-2 border-gray-200 hover:border-blue-300 transition-colors"
                  unoptimized={true}
                />
              ) : (
                <div className="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm border-2 border-gray-200 hover:border-blue-300 transition-colors">
                  {initials}
                </div>
              )}
            </div>
          );
        },
        enableSorting: false,
        enableHiding: false,
        size: 80,
      },
      {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => {
          const admin = row.original;
          return (
            <div className="flex flex-col">
              <span className="font-medium text-gray-900">{admin.name}</span>
              <span className="text-sm text-gray-500">{admin.email}</span>
            </div>
          );
        },
        size: 250,
      },
      {
        accessorKey: 'username',
        header: 'Username',
        cell: ({ row }) => {
          const username = row.getValue('username') as string;
          return (
            <span className="text-gray-900">
              {username || '-'}
            </span>
          );
        },
        size: 150,
      },
      {
        accessorKey: 'role',
        header: 'Role',
        cell: ({ row }) => {
          const role = row.getValue('role') as string;
          return (
            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {role}
            </span>
          );
        },
        size: 120,
      },
      {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
          const status = row.getValue('status') as string;
          const statusConfig = {
            active: { bg: 'bg-green-100', text: 'text-green-800', label: 'Active' },
            inactive: { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Inactive' },
            suspended: { bg: 'bg-red-100', text: 'text-red-800', label: 'Suspended' },
          };
          const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.inactive;
          
          return (
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.bg} ${config.text}`}>
              {config.label}
            </span>
          );
        },
        size: 120,
      },
      {
        accessorKey: 'last_login_at',
        header: 'Last Login',
        cell: ({ row }) => {
          const lastLogin = row.getValue('last_login_at') as string;
          return (
            <span className="text-gray-900">
              {lastLogin ? new Date(lastLogin).toLocaleDateString() : 'Never'}
            </span>
          );
        },
        size: 150,
      },
      {
        accessorKey: 'created_at',
        header: 'Created',
        cell: ({ row }) => {
          const createdAt = row.getValue('created_at') as string;
          return (
            <span className="text-gray-900">
              {new Date(createdAt).toLocaleDateString()}
            </span>
          );
        },
        size: 150,
      },
      {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
          const admin = row.original;
          return (
            <div className="flex items-center space-x-2">
              {actions.onView && (
                <button
                  onClick={() => actions.onView(admin)}
                  className="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                  title="View"
                >
                  <EyeIcon className="w-4 h-4" />
                </button>
              )}
              {actions.onEdit && (
                <button
                  onClick={() => actions.onEdit(admin)}
                  className="p-1 text-gray-400 hover:text-green-600 transition-colors"
                  title="Edit"
                >
                  <EditIcon className="w-4 h-4" />
                </button>
              )}
              {actions.onDelete && (
                <button
                  onClick={() => actions.onDelete(admin)}
                  className="p-1 text-gray-400 hover:text-red-600 transition-colors"
                  title="Delete"
                >
                  <TrashIcon className="w-4 h-4" />
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
    [selectedAdmins, onSelectAdmin, onSelectAll, actions]
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
    <div className="bg-white rounded-lg shadow-sm border border-gray-200">
      <AdminTableToolbar
        selectedCount={selectedAdmins.length}
        totalCount={pagination.total}
        onRefresh={onRefresh}
        onExport={() => actions.onExport('csv')}
        onBulkDelete={actions.onBulkDelete}
        onSelectAll={onSelectAll}
        allSelected={selectedAdmins.length === data.length && data.length > 0}
      />
      
      <AdminTableHeader table={table} />
      
      <AdminTableBody
        table={table}
        data={data}
        selectedAdmins={selectedAdmins}
        onSelectAdmin={onSelectAdmin}
      />
      
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
};
