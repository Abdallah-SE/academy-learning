import React from 'react';
import { RefreshCwIcon, TrashIcon } from 'lucide-react';

interface AdminTableToolbarProps {
  selectedCount: number;
  totalCount: number;
  onRefresh: () => void;
  onBulkDelete: () => void;
  onSelectAll: (selected: boolean) => void;
  allSelected: boolean;
}

export const AdminTableToolbar: React.FC<AdminTableToolbarProps> = ({
  selectedCount,
  totalCount,
  onRefresh,
  onBulkDelete,
  onSelectAll,
  allSelected,
}) => {
  return (
    <div className="px-6 py-4 border-b border-gray-200 bg-white">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <div className="flex items-center space-x-3">
            <h3 className="text-lg font-semibold text-gray-900">Admins</h3>
            <div className="flex items-center space-x-2">
              <span className="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                {totalCount} Total
              </span>
              {selectedCount > 0 && (
                <span className="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                  {selectedCount} Selected
                </span>
              )}
            </div>
          </div>
        </div>
        
        <div className="flex items-center space-x-3">
          <div className="flex items-center space-x-1">
            <button
              onClick={onRefresh}
              className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
              title="Refresh data"
            >
              <RefreshCwIcon className="w-5 h-5" />
            </button>
            {selectedCount > 0 && (
              <button
                onClick={onBulkDelete}
                className="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                title="Delete selected"
              >
                <TrashIcon className="w-5 h-5" />
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
