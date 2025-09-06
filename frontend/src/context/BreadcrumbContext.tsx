'use client';

import React, { createContext, useContext, useState, useCallback, ReactNode } from 'react';
import { BreadcrumbItem } from '@/components/atoms/Breadcrumb';

interface BreadcrumbContextType {
  breadcrumbs: BreadcrumbItem[];
  setBreadcrumbs: (breadcrumbs: BreadcrumbItem[]) => void;
  addBreadcrumb: (breadcrumb: BreadcrumbItem) => void;
  removeBreadcrumb: (index: number) => void;
  clearBreadcrumbs: () => void;
  updateBreadcrumb: (index: number, breadcrumb: BreadcrumbItem) => void;
}

const BreadcrumbContext = createContext<BreadcrumbContextType | undefined>(undefined);

interface BreadcrumbProviderProps {
  children: ReactNode;
  initialBreadcrumbs?: BreadcrumbItem[];
}

export const BreadcrumbProvider: React.FC<BreadcrumbProviderProps> = ({
  children,
  initialBreadcrumbs = [],
}) => {
  const [breadcrumbs, setBreadcrumbsState] = useState<BreadcrumbItem[]>(initialBreadcrumbs);

  const setBreadcrumbs = useCallback((newBreadcrumbs: BreadcrumbItem[]) => {
    setBreadcrumbsState(prev => {
      // Only update if the breadcrumbs have actually changed
      // Compare serializable properties since breadcrumbs may contain React elements
      const prevSerialized = prev.map(b => `${b.label}-${b.href}-${b.isActive}-${b.isClickable}`).join('|');
      const newSerialized = newBreadcrumbs.map(b => `${b.label}-${b.href}-${b.isActive}-${b.isClickable}`).join('|');
      
      if (prevSerialized === newSerialized && prev.length === newBreadcrumbs.length) {
        return prev;
      }
      return newBreadcrumbs;
    });
  }, []);

  const addBreadcrumb = useCallback((breadcrumb: BreadcrumbItem) => {
    setBreadcrumbsState(prev => [...prev, breadcrumb]);
  }, []);

  const removeBreadcrumb = useCallback((index: number) => {
    setBreadcrumbsState(prev => prev.filter((_, i) => i !== index));
  }, []);

  const clearBreadcrumbs = useCallback(() => {
    setBreadcrumbsState([]);
  }, []);

  const updateBreadcrumb = useCallback((index: number, breadcrumb: BreadcrumbItem) => {
    setBreadcrumbsState(prev => 
      prev.map((item, i) => i === index ? breadcrumb : item)
    );
  }, []);

  const value: BreadcrumbContextType = {
    breadcrumbs,
    setBreadcrumbs,
    addBreadcrumb,
    removeBreadcrumb,
    clearBreadcrumbs,
    updateBreadcrumb,
  };

  return (
    <BreadcrumbContext.Provider value={value}>
      {children}
    </BreadcrumbContext.Provider>
  );
};

export const useBreadcrumb = (): BreadcrumbContextType => {
  const context = useContext(BreadcrumbContext);
  if (context === undefined) {
    throw new Error('useBreadcrumb must be used within a BreadcrumbProvider');
  }
  return context;
};
