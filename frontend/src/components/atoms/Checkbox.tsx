'use client';

import React, { forwardRef } from 'react';

interface CheckboxProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  className?: string;
}

export const Checkbox = forwardRef<HTMLInputElement, CheckboxProps>(
  ({ label, error, className = '', ...props }, ref) => {
    return (
      <div className="w-full">
        <div className="flex items-center">
          <input
            ref={ref}
            type="checkbox"
            className={`
              h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded
              transition-colors duration-200
              ${error ? 'border-red-300' : ''}
              ${className}
            `}
            {...props}
          />
          {label && (
            <label htmlFor={props.id} className="ml-2 block text-sm text-gray-900">
              {label}
            </label>
          )}
        </div>
        {error && (
          <p className="mt-1 text-sm text-red-600 animate-fadeIn">
            {error}
          </p>
        )}
      </div>
    );
  }
);

Checkbox.displayName = 'Checkbox';
