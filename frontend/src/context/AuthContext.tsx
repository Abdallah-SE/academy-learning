'use client';

import React, { createContext, useContext, ReactNode, useMemo, useRef } from 'react';
import { useAuth } from '@/hooks/useAuth';

const AuthContext = createContext<ReturnType<typeof useAuth> | undefined>(undefined);

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const auth = useAuth();
  const renderCount = useRef(0);
  
  renderCount.current += 1;
  console.log(`🏗️ AuthProvider render #${renderCount.current}`);
  
  // Memoize the context value to prevent unnecessary re-renders
  const contextValue = useMemo(() => {
    console.log('🔒 AuthContext value memoized');
    return auth;
  }, [auth]);
  
  return (
    <AuthContext.Provider value={contextValue}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuthContext = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuthContext must be used within an AuthProvider');
  }
  return context;
};
