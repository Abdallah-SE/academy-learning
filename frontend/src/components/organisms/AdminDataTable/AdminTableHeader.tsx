import React from 'react';
import { flexRender, Table } from '@tanstack/react-table';
import { ChevronUpIcon, ChevronDownIcon } from 'lucide-react';
import { Admin } from '@/types/admin';

interface AdminTableHeaderProps {
  table: Table<Admin>;
}

export const AdminTableHeader: React.FC<AdminTableHeaderProps> = ({ table }) => {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-gradient-to-r from-gray-50 to-gray-100">
          {table.getHeaderGroups().map((headerGroup) => (
            <tr key={headerGroup.id}>
              {headerGroup.headers.map((header) => (
                <th
                  key={header.id}
                  className="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r border-gray-200 last:border-r-0"
                  style={{ width: header.getSize() }}
                >
                  {header.isPlaceholder ? null : (
                    <div
                      className={`flex items-center space-x-2 ${
                        header.column.getCanSort() ? 'cursor-pointer select-none hover:text-gray-900 group' : ''
                      }`}
                      onClick={header.column.getToggleSortingHandler()}
                    >
                      <span className="font-semibold">
                        {flexRender(header.column.columnDef.header, header.getContext())}
                      </span>
                      {header.column.getCanSort() && (
                        <span className="ml-1 opacity-60 group-hover:opacity-100 transition-opacity">
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
      </table>
    </div>
  );
};
