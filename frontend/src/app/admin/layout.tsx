import { ClientLayout } from '@/components/layouts/ClientLayout';
import { MainLayout } from '@/components/organisms/MainLayout';

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <ClientLayout>
      <MainLayout>
        {children}
      </MainLayout>
    </ClientLayout>
  );
}
