'use client';

import React, { memo } from 'react';

interface AdminLayoutPresenterProps {
  children: React.ReactNode;
}

/**
 * AdminLayoutPresenter - Pure presentational component
 * This component only handles rendering children without any business logic
 * Following the atomic presenter pattern for better maintainability
 */
export const AdminLayoutPresenter: React.FC<AdminLayoutPresenterProps> = memo(({ children }) => {
  return <>{children}</>;
});

AdminLayoutPresenter.displayName = 'AdminLayoutPresenter';
