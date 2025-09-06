import React from 'react';
import { 
  ChevronLeftIcon, 
  ChevronRightIcon, 
  ChevronsLeftIcon, 
  ChevronsRightIcon 
} from 'lucide-react';
import { PaginationState } from '@/types/admin';

interface AdminTablePaginationProps {
  pagination: PaginationState;
  onPageChange: (page: number) => void;
  onPerPageChange: (perPage: number) => void;
  pageSizeOptions: number[];
}

export const AdminTablePagination: React.FC<AdminTablePaginationProps> = ({
  pagination,
  onPageChange,
  onPerPageChange,
  pageSizeOptions,
}) => {
  const { current_page, last_page, per_page, total, from, to } = pagination;

  const renderPageNumbers = () => {
    const pages = [];
    const maxVisiblePages = 5;
    
    // Always show first page
    if (current_page > 2) {
      pages.push(
        <button
          key={1}
          onClick={() => onPageChange(1)}
          className="px-3 py-1.5 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-200 rounded-md transition-colors"
        >
          1
        </button>
      );
      if (current_page > 3) {
        pages.push(
          <span key="ellipsis1" className="px-2 text-gray-400">
            ...
          </span>
        );
      }
    }
    
    // Show pages around current page
    const start = Math.max(1, current_page - 1);
    const end = Math.min(last_page, current_page + 1);
    
    for (let i = start; i <= end; i++) {
      pages.push(
        <button
          key={i}
          onClick={() => onPageChange(i)}
          className={`px-3 py-1.5 text-sm rounded-md transition-colors ${
            i === current_page
              ? 'bg-blue-600 text-white font-medium'
              : 'text-gray-700 hover:text-gray-900 hover:bg-gray-200'
          }`}
        >
          {i}
        </button>
      );
    }
    
    // Always show last page
    if (current_page < last_page - 2) {
      if (current_page < last_page - 3) {
        pages.push(
          <span key="ellipsis2" className="px-2 text-gray-400">
            ...
          </span>
        );
      }
      pages.push(
        <button
          key={last_page}
          onClick={() => onPageChange(last_page)}
          className="px-3 py-1.5 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-200 rounded-md transition-colors"
        >
          {last_page}
        </button>
      );
    }
    
    return pages;
  };

  return (
    <div className="px-6 py-4 border-t border-gray-200 bg-gray-50">
      <div className="flex items-center justify-between">
        {/* Left side - Info and page size */}
        <div className="flex items-center space-x-4">
          <div className="flex items-center space-x-2">
            <span className="text-sm text-gray-700">
              Showing{' '}
              <span className="font-medium text-gray-900">
                {from || 0}
              </span>
              {' '}to{' '}
              <span className="font-medium text-gray-900">
                {to || 0}
              </span>
              {' '}of{' '}
              <span className="font-medium text-gray-900">
                {total}
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
              value={per_page}
              onChange={(e) => onPerPageChange(Number(e.target.value))}
              className="px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm"
            >
              {pageSizeOptions.map((pageSize) => (
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
            onClick={() => onPageChange(1)}
            disabled={current_page === 1}
            className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
            title="First page"
          >
            <ChevronsLeftIcon className="w-4 h-4" />
          </button>
          
          {/* Previous page */}
          <button
            onClick={() => onPageChange(current_page - 1)}
            disabled={current_page === 1}
            className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
            title="Previous page"
          >
            <ChevronLeftIcon className="w-4 h-4" />
          </button>
          
          {/* Page numbers */}
          <div className="flex items-center space-x-1 mx-2">
            {renderPageNumbers()}
          </div>
          
          {/* Next page */}
          <button
            onClick={() => onPageChange(current_page + 1)}
            disabled={current_page === last_page}
            className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
            title="Next page"
          >
            <ChevronRightIcon className="w-4 h-4" />
          </button>
          
          {/* Last page */}
          <button
            onClick={() => onPageChange(last_page)}
            disabled={current_page === last_page}
            className="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors rounded-md hover:bg-gray-200"
            title="Last page"
          >
            <ChevronsRightIcon className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  );
};
