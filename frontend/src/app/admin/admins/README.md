# Admin Management Page

A modern, feature-rich admin management page built with Next.js, TypeScript, and TanStack Table.

## Features

### 🎯 Core Functionality
- **Admin List Display**: View all admins in a paginated, sortable table
- **Search & Filter**: Real-time search and filtering by status, role, and other criteria
- **CRUD Operations**: Create, read, update, and delete admin accounts
- **Bulk Actions**: Select multiple admins for batch operations

### 🚀 Advanced Features
- **Modern Data Table**: Built with TanStack Table for optimal performance
- **Server-Side Pagination**: Efficient handling of large datasets
- **Real-time Updates**: React Query for automatic data synchronization
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Loading States**: Smooth loading indicators and skeleton screens
- **Error Handling**: Comprehensive error management and user feedback

### 🎨 UI/UX Features
- **Clean Interface**: Modern, professional design with Tailwind CSS
- **Interactive Elements**: Hover effects, smooth transitions, and intuitive controls
- **Status Indicators**: Visual status badges (Active, Inactive, Suspended)
- **Avatar Support**: Profile pictures with fallback to initials
- **Action Buttons**: Quick access to view, edit, and delete actions

### 🔧 Technical Features
- **TypeScript**: Full type safety throughout the application
- **React Query**: Efficient data fetching and caching
- **Custom Hooks**: Reusable logic for admin operations
- **Error Boundaries**: Graceful error handling
- **Accessibility**: WCAG compliant components

## File Structure

```
src/app/admin/admins/
├── page.tsx                 # Main admin list page
├── demo-data.ts            # Sample data for testing
└── README.md               # This documentation

src/components/organisms/
└── AdminTable.tsx          # Reusable data table component

src/hooks/
└── useAdmins.ts            # Custom hooks for admin operations

src/services/
└── adminService.ts         # API service layer

src/types/
└── admin.ts                # TypeScript type definitions
```

## Usage

### Basic Implementation

```tsx
import { AdminTable } from '@/components/organisms/AdminTable';
import { useAdmins } from '@/hooks/useAdmins';

function AdminsPage() {
  const { data, isLoading, error } = useAdmins();
  
  return (
    <AdminTable
      data={data?.data || []}
      loading={isLoading}
      onEdit={handleEdit}
      onDelete={handleDelete}
      onView={handleView}
    />
  );
}
```

### Advanced Filtering

```tsx
const [filters, setFilters] = useState({
  search: '',
  status: 'active',
  role: 'admin',
  page: 1,
  per_page: 10
});

const { data } = useAdmins(filters);
```

## API Integration

The page integrates with the Laravel backend API endpoints:

- `GET /api/v1/admin/admins` - List admins with pagination and filtering
- `POST /api/v1/admin/admins` - Create new admin
- `PUT /api/v1/admin/admins/{id}` - Update admin
- `DELETE /api/v1/admin/admins/{id}` - Delete admin
- `POST /api/v1/admin/admins/{id}/avatar` - Upload avatar

## Customization

### Adding New Columns

```tsx
const columns = [
  // ... existing columns
  {
    accessorKey: 'custom_field',
    header: 'Custom Field',
    cell: ({ row }) => <span>{row.getValue('custom_field')}</span>,
  },
];
```

### Custom Actions

```tsx
const handleCustomAction = (admin: Admin) => {
  // Your custom logic here
};

<AdminTable
  // ... other props
  onCustomAction={handleCustomAction}
/>
```

## Performance Optimizations

- **Virtual Scrolling**: For large datasets (future enhancement)
- **Debounced Search**: Prevents excessive API calls
- **Memoized Components**: Optimized re-renders
- **Lazy Loading**: Load data only when needed
- **Caching**: React Query handles intelligent caching

## Future Enhancements

- [ ] Bulk operations (delete, activate, deactivate)
- [ ] Advanced filtering with date ranges
- [ ] Export functionality (CSV, Excel, PDF)
- [ ] Real-time notifications
- [ ] Audit trail for admin actions
- [ ] Role-based permissions
- [ ] Advanced search with multiple criteria
- [ ] Drag and drop for reordering
- [ ] Column customization
- [ ] Dark mode support

## Dependencies

- `@tanstack/react-table` - Data table functionality
- `@tanstack/react-query` - Data fetching and caching
- `lucide-react` - Icon library
- `tailwindcss` - Styling framework
- `typescript` - Type safety

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Contributing

When adding new features or modifying existing ones:

1. Follow the existing code patterns
2. Add proper TypeScript types
3. Include error handling
4. Write comprehensive tests
5. Update this documentation
