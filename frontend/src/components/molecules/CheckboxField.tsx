'use client';

import React from 'react';
import { Checkbox } from '@/components/atoms/Checkbox';

interface CheckboxFieldProps {
  id: string;
  name: string;
  label: string;
  checked: boolean;
  error?: string;
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  className?: string;
}

export const CheckboxField: React.FC<CheckboxFieldProps> = ({
  id,
  name,
  label,
  checked,
  error,
  onChange,
  className = ''
}) => {
  return (
    <div className={className}>
      <Checkbox
        id={id}
        name={name}
        label={label}
        checked={checked}
        onChange={onChange}
        error={error}
      />
    </div>
  );
};
