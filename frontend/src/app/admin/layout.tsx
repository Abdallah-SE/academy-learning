import { ClientLayout } from '@/components/layouts/ClientLayout';
import { AdminLayoutContainer } from '@/components/containers/AdminLayoutContainer';

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <ClientLayout>
      <AdminLayoutContainer>
        {children}
      </AdminLayoutContainer>
    </ClientLayout>
  );
}
