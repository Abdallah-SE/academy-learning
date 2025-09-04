'use client';

import React from 'react';
import { Logo } from '@/components/atoms/Logo';

interface LoginLayoutProps {
  children: React.ReactNode;
  logo?: {
    src: string;
    alt: string;
    width?: number;
    height?: number;
  };
  className?: string;
}

export const LoginLayout: React.FC<LoginLayoutProps> = ({ 
  children, 
  logo,
  className = '' 
}) => {
  return (
    <div className={`w-full max-w-6xl mx-auto ${className}`}>
      <div className="flex items-center justify-center min-h-screen">
        {/* Left Side - Logo */}
        {logo && (
          <div className="hidden lg:flex lg:w-1/2 lg:justify-center lg:items-center lg:pr-16">
            <Logo
              src={logo.src}
              alt={logo.alt}
              width={logo.width}
              height={logo.height}
              priority
            />
          </div>
        )}

        {/* Right Side - Form Content */}
        <div className="w-full lg:w-1/2 px-8 lg:px-16">
          <div className="max-w-md mx-auto">
            {children}
          </div>
        </div>
      </div>
    </div>
  );
};
