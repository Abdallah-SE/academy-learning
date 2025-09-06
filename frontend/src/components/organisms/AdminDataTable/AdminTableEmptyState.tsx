import React from 'react';
import { UserIcon } from 'lucide-react';

interface AdminTableEmptyStateProps {
  hasFilters: boolean;
  onClearFilters: () => void;
}

export const AdminTableEmptyState: React.FC<AdminTableEmptyStateProps> = ({
  hasFilters,
  onClearFilters,
}) => {
  return (
    <div className="text-center py-12">
      <UserIcon className="mx-auto h-12 w-12 text-gray-400" />
      <h3 className="mt-2 text-sm font-medium text-gray-900">No admins found</h3>
      <p className="mt-1 text-sm text-gray-500">
        {hasFilters ? 'Try adjusting your search criteria.' : 'Get started by adding a new admin.'}
      </p>
      {hasFilters && (
        <button
          onClick={onClearFilters}
          className="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Clear Filters
        </button>
      )}
    </div>
  );
};

