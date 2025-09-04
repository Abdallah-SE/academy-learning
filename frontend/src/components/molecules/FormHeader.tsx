'use client';

import React from 'react';

interface FormHeaderProps {
  title: string;
  subtitle?: string;
  className?: string;
}

export const FormHeader: React.FC<FormHeaderProps> = ({ 
  title, 
  subtitle, 
  className = '' 
}) => {
  return (
    <div className={`text-center mb-8 ${className}`}>
      <h1 className="text-3xl font-bold text-gray-900 mb-2">
        {title}
      </h1>
      {subtitle && (
        <p className="text-gray-600">
          {subtitle}
        </p>
      )}
    </div>
  );
};
