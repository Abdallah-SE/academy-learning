'use client';

import React, { useState, useMemo } from 'react';
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
  ColumnDef,
  SortingState,
  ColumnFiltersState,
  PaginationState,
  getFacetedRowModel,
  getFacetedUniqueValues,
} from '@tanstack/react-table';
import { Admin } from '@/types/admin';
import { 
  ChevronUpIcon, 
  ChevronDownIcon, 
  SearchIcon, 
  FilterIcon,
  MoreHorizontalIcon,
  EditIcon,
  TrashIcon,
  EyeIcon,
  UserIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronsLeftIcon,
  ChevronsRightIcon,
  DownloadIcon,
  RefreshCwIcon
} from 'lucide-react';

interface AdminDataTableProps {
  data: Admin[];
  loading?: boolean;
  onEdit?: (admin: Admin) => void;
  onDelete?: (admin: Admin) => void;
  onView?: (admin: Admin) => void;
  onRefresh?: () => void;
  onExport?: () => void;
}

const columnHelper = createColumnHelper<Admin>();

export const AdminDataTable: React.FC<AdminDataTableProps> = ({
  data,
  loading = false,
  onEdit,
  onDelete,
  onView,
  onRefresh,
  onExport,
}) => {
  const [sorting, setSorting] = useState<SortingState>([]);
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
  const [globalFilter, setGlobalFilter] = useState('');
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 10,
  });
  const [rowSelection, setRowSelection] = useState({});

  const columns = useMemo<ColumnDef<Admin>[]>(
    () => [
      {
        id: 'select',
        header: ({ table }) => (
          <input
            type="checkbox"
            checked={table.getIsAllPageRowsSelected()}
            onChange={(e) => table.toggleAllPageRowsSelected(!!e.target.checked)}
            className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          />
        ),
        cell: ({ row }) => (
          <input
            type="checkbox"
            checked={row.getIsSelected()}
            onChange={(e) => row.toggleSelected(!!e.target.checked)}
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
                <img
                  src={avatarUrl}
                  alt={`${admin.name}'s avatar`}
                  className="w-12 h-12 rounded-full object-cover border-2 border-gray-200 hover:border-blue-300 transition-colors"
                  onError={(e) => {
                    e.currentTarget.style.display = 'none';
                    e.currentTarget.nextElementSibling?.classList.remove('hidden');
                  }}
                />
              ) : null}
              <div className={`w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm border-2 border-gray-200 hover:border-blue-300 transition-colors ${avatarUrl ? 'hidden' : ''}`}>
                {initials}
              </div>
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
              {onView && (
                <button
                  onClick={() => onView(admin)}
                  className="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                  title="View"
                >
                  <EyeIcon className="w-4 h-4" />
                </button>
              )}
              {onEdit && (
                <button
                  onClick={() => onEdit(admin)}
                  className="p-1 text-gray-400 hover:text-green-600 transition-colors"
                  title="Edit"
                >
                  <EditIcon className="w-4 h-4" />
                </button>
              )}
              {onDelete && (
                <button
                  onClick={() => onDelete(admin)}
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
    [onEdit, onDelete, onView]
  );

  const table = useReactTable({
    data,
    columns,
    state: {
      sorting,
      columnFilters,
      globalFilter,
      pagination,
      rowSelection,
    },
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    onGlobalFilterChange: setGlobalFilter,
    onPaginationChange: setPagination,
    onRowSelectionChange: setRowSelection,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    enableRowSelection: true,
    enableMultiRowSelection: true,
  });

  if (loading) {
    return (
      <div className="bg-white rounded-lg shadow-sm border border-gray-200">
        <div className="p-6">
          <div className="animate-pulse">
            <div className="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
            <div className="space-y-3">
              {[...Array(5)].map((_, i) => (
                <div key={i} className="h-12 bg-gray-200 rounded"></div>
              ))}
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200">
      {/* Header */}
      <div className="px-6 py-4 border-b border-gray-200 bg-white">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4">
            <div className="flex items-center space-x-3">
              <h3 className="text-lg font-semibold text-gray-900">Admins</h3>
              <div className="flex items-center space-x-2">
                <span className="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                  {table.getFilteredRowModel().rows.length} Total
                </span>
                {Object.keys(rowSelection).length > 0 && (
                  <span className="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                    {Object.keys(rowSelection).length} Selected
                  </span>
                )}
              </div>
            </div>
          </div>
          
          <div className="flex items-center space-x-3">
            <div className="relative">
              <SearchIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
              <input
                type="text"
                placeholder="Search admins..."
                value={globalFilter}
                onChange={(e) => setGlobalFilter(e.target.value)}
                className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64 shadow-sm"
              />
            </div>
            
            <div className="flex items-center space-x-1">
              <button
                onClick={onRefresh}
                className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                title="Refresh data"
              >
                <RefreshCwIcon className="w-5 h-5" />
              </button>
              <button
                onClick={onExport}
                className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                title="Export data"
              >
                <DownloadIcon className="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Table */}
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            {table.getHeaderGroups().map((headerGroup) => (
              <tr key={headerGroup.id}>
                {headerGroup.headers.map((header) => (
                  <th
                    key={header.id}
                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200 last:border-r-0"
                    style={{ width: header.getSize() }}
                  >
                    {header.isPlaceholder ? null : (
                      <div
                        className={`flex items-center space-x-1 ${
                          header.column.getCanSort() ? 'cursor-pointer select-none hover:text-gray-700' : ''
                        }`}
                        onClick={header.column.getToggleSortingHandler()}
                      >
                        <span>
                          {flexRender(header.column.columnDef.header, header.getContext())}
                        </span>
                        {header.column.getCanSort() && (
                          <span className="ml-1">
                            {header.column.getIsSorted() === 'asc' ? (
                              <ChevronUpIcon className="w-4 h-4 text-blue-600" />
                            ) : header.column.getIsSorted() === 'desc' ? (
                              <ChevronDownIcon className="w-4 h-4 text-blue-600" />
                            ) : (
                              <div className="w-4 h-4" />
                            )}
                          </span>
                        )}
                      </div>
                    )}
                  </th>
                ))}
              </tr>
            ))}
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {table.getRowModel().rows.map((row, index) => (
              <tr 
                key={row.id} 
                className={`hover:bg-gray-50 transition-colors ${
                  index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'
                }`}
              >
                {row.getVisibleCells().map((cell) => (
                  <td 
                    key={cell.id} 
                    className="px-6 py-4 whitespace-nowrap border-r border-gray-100 last:border-r-0"
                  >
                    {flexRender(cell.column.columnDef.cell, cell.getContext())}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Empty State */}
      {table.getFilteredRowModel().rows.length === 0 && (
        <div className="text-center py-12">
          <UserIcon className="mx-auto h-12 w-12 text-gray-400" />
          <h3 className="mt-2 text-sm font-medium text-gray-900">No admins found</h3>
          <p className="mt-1 text-sm text-gray-500">
            {globalFilter ? 'Try adjusting your search criteria.' : 'Get started by adding a new admin.'}
          </p>
        </div>
      )}

      {/* Pagination */}
      <div className="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div className="flex items-center justify-between">
          {/* Left side - Info and page size */}
          <div className="flex items-center space-x-4">
            <div className="flex items-center space-x-2">
              <span className="text-sm text-gray-700">
                Showing{' '}
                <span className="font-medium text-gray-900">
                  {table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1}
                </span>
                {' '}to{' '}
                <span className="font-medium text-gray-900">
                  {Math.min(
                    (table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize,
                    table.getFilteredRowModel().rows.length
                  )}
                </span>
                {' '}of{' '}
                <span className="font-medium text-gray-900">
                  {table.getFilteredRowModel().rows.length}
                </span>
                {' '}results
              </span>
            </div>
            
            <div className="flex items-center space-x-2">
              <label htmlFor="page-size" className="text-sm text-gray-700">
                Rows per page:
              </label>
              <select
                id="page-size"
                value={table.getState().pagination.pageSize}
                onChange={(e) => table.setPageSize(Number(e.target.value))}
                className="px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm"
              >
                {[5, 10, 20, 50, 100].map((pageSize) => (
                  <option key={pageSize} value={pageSize}>
                    {pageSize}
                  </option>
                ))}
              </select>
            </div>
          </div>
          
          {/* Right side - Pagination controls */}
          <div className="flex items-center space-x-1">
            {/* First page */}
            <button
              onClick={() => table.setPageIndex(0)}
              disabled={!table.getCanPreviousPage()}
              className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
              title="First page"
            >
              <ChevronsLeftIcon className="w-4 h-4" />
            </button>
            
            {/* Previous page */}
            <button
              onClick={() => table.previousPage()}
              disabled={!table.getCanPreviousPage()}
              className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
              title="Previous page"
            >
              <ChevronLeftIcon className="w-4 h-4" />
            </button>
            
            {/* Page numbers */}
            <div className="flex items-center space-x-1 mx-2">
              {(() => {
                const currentPage = table.getState().pagination.pageIndex;
                const totalPages = table.getPageCount();
                const pages = [];
                
                // Always show first page
                if (currentPage > 2) {
                  pages.push(
                    <button
                      key={0}
                      onClick={() => table.setPageIndex(0)}
                      className="px-3 py-1.5 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-200 rounded-md transition-colors"
                    >
                      1
                    </button>
                  );
                  if (currentPage > 3) {
                    pages.push(
                      <span key="ellipsis1" className="px-2 text-gray-400">
                        ...
                      </span>
                    );
                  }
                }
                
                // Show pages around current page
                const start = Math.max(0, currentPage - 1);
                const end = Math.min(totalPages - 1, currentPage + 1);
                
                for (let i = start; i <= end; i++) {
                  pages.push(
                    <button
                      key={i}
                      onClick={() => table.setPageIndex(i)}
                      className={`px-3 py-1.5 text-sm rounded-md transition-colors ${
                        i === currentPage
                          ? 'bg-blue-600 text-white font-medium'
                          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-200'
                      }`}
                    >
                      {i + 1}
                    </button>
                  );
                }
                
                // Always show last page
                if (currentPage < totalPages - 3) {
                  if (currentPage < totalPages - 4) {
                    pages.push(
                      <span key="ellipsis2" className="px-2 text-gray-400">
                        ...
                      </span>
                    );
                  }
                  pages.push(
                    <button
                      key={totalPages - 1}
                      onClick={() => table.setPageIndex(totalPages - 1)}
                      className="px-3 py-1.5 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-200 rounded-md transition-colors"
                    >
                      {totalPages}
                    </button>
                  );
                }
                
                return pages;
              })()}
            </div>
            
            {/* Next page */}
            <button
              onClick={() => table.nextPage()}
              disabled={!table.getCanNextPage()}
              className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
              title="Next page"
            >
              <ChevronRightIcon className="w-4 h-4" />
            </button>
            
            {/* Last page */}
            <button
              onClick={() => table.setPageIndex(table.getPageCount() - 1)}
              disabled={!table.getCanNextPage()}
              className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
              title="Last page"
            >
              <ChevronsRightIcon className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
