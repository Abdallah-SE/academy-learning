import React, { memo } from 'react';
import { flexRender, Table } from '@tanstack/react-table';
import { Admin } from '@/types/admin';

interface AdminTableBodyProps {
  table: Table<Admin>;
  data: Admin[];
}

export const AdminTableBody: React.FC<AdminTableBodyProps> = memo(({
  table,
}) => {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <tbody className="bg-white divide-y divide-gray-100">
          {table.getRowModel().rows.map((row, index) => (
            <tr 
              key={row.id} 
              className={`hover:bg-blue-50/50 transition-all duration-200 group ${
                index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30'
              }`}
            >
              {row.getVisibleCells().map((cell) => (
                <td 
                  key={cell.id} 
                  className="px-6 py-5 whitespace-nowrap border-r border-gray-100 last:border-r-0 group-hover:bg-blue-50/30 transition-colors"
                >
                  {flexRender(cell.column.columnDef.cell, cell.getContext())}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
});

AdminTableBody.displayName = 'AdminTableBody';
