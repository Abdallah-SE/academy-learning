import React from 'react';
import { flexRender, Table } from '@tanstack/react-table';
import { Admin } from '@/types/admin';

interface AdminTableBodyProps {
  table: Table<Admin>;
  data: Admin[];
  selectedAdmins: number[];
  onSelectAdmin: (adminId: number, selected: boolean) => void;
}

export const AdminTableBody: React.FC<AdminTableBodyProps> = ({
  table,
}) => {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
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
  );
};
