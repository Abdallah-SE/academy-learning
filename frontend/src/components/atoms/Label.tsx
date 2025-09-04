'use client';

import React from 'react';

interface LabelProps extends React.LabelHTMLAttributes<HTMLLabelElement> {
  required?: boolean;
  className?: string;
}

export const Label: React.FC<LabelProps> = ({ 
  children, 
  required = false, 
  className = '',
  ...props 
}) => {
  return (
    <label 
      className={`block text-sm font-medium text-gray-700 mb-2 ${className}`}
      {...props}
    >
      {children}
      {required && <span className="text-red-500 ml-1">*</span>}
    </label>
  );
};
