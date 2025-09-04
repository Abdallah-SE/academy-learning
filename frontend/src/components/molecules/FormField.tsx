'use client';

import React from 'react';
import { Input } from '@/components/atoms/Input';
import { Label } from '@/components/atoms/Label';

interface FormFieldProps {
  id: string;
  name: string;
  type?: string;
  label: string;
  value: string;
  placeholder?: string;
  error?: string;
  required?: boolean;
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onBlur: (e: React.FocusEvent<HTMLInputElement>) => void;
  className?: string;
}

export const FormField: React.FC<FormFieldProps> = ({
  id,
  name,
  type = 'text',
  label,
  value,
  placeholder,
  error,
  required = false,
  onChange,
  onBlur,
  className = ''
}) => {
  return (
    <div className={className}>
      <Label htmlFor={id} required={required}>
        {label}
      </Label>
      <Input
        type={type}
        id={id}
        name={name}
        value={value}
        onChange={onChange}
        onBlur={onBlur}
        error={error}
        placeholder={placeholder}
        required={required}
      />
    </div>
  );
};
