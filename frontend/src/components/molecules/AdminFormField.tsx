'use client';

import React from 'react';
import { Input, Label, Select, Checkbox } from '@/components/atoms';
import { CreateAdminFormData } from '@/schemas/admin.schema';

interface AdminFormFieldProps {
  id: string;
  name: keyof CreateAdminFormData;
  type?: 'text' | 'email' | 'password' | 'select' | 'checkbox';
  label: string;
  value: any;
  placeholder?: string;
  error?: string;
  required?: boolean;
  options?: Array<{ value: string; label: string }>;
  onChange: (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => void;
  onBlur: (e: React.FocusEvent<HTMLInputElement | HTMLSelectElement>) => void;
  className?: string;
}

export const AdminFormField: React.FC<AdminFormFieldProps> = ({
  id,
  name,
  type = 'text',
  label,
  value,
  placeholder,
  error,
  required = false,
  options = [],
  onChange,
  onBlur,
  className = ''
}) => {
  const renderField = () => {
    switch (type) {
      case 'select':
        return (
          <Select
            id={id}
            name={name}
            value={value}
            onChange={onChange as React.ChangeEvent<HTMLSelectElement>}
            onBlur={onBlur as React.FocusEvent<HTMLSelectElement>}
            error={error}
            options={options}
            placeholder={placeholder}
            required={required}
            className={className}
          />
        );
      
      case 'checkbox':
        return (
          <Checkbox
            id={id}
            name={name}
            checked={value}
            onChange={onChange as React.ChangeEvent<HTMLInputElement>}
            onBlur={onBlur as React.FocusEvent<HTMLInputElement>}
            error={error}
            className={className}
          />
        );
      
      default:
        return (
          <Input
            type={type}
            id={id}
            name={name}
            value={value}
            onChange={onChange as React.ChangeEvent<HTMLInputElement>}
            onBlur={onBlur as React.FocusEvent<HTMLInputElement>}
            error={error}
            placeholder={placeholder}
            required={required}
            className={className}
          />
        );
    }
  };

  return (
    <div className={className}>
      {type !== 'checkbox' && (
        <Label htmlFor={id} required={required}>
          {label}
        </Label>
      )}
      {renderField()}
      {type === 'checkbox' && (
        <Label htmlFor={id} className="ml-2">
          {label}
        </Label>
      )}
    </div>
  );
};
