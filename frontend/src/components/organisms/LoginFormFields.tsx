'use client';

import React from 'react';
import { FormField } from '@/components/molecules/FormField';
import { CheckboxField } from '@/components/molecules/CheckboxField';
import { LoginFormData } from '@/schemas/auth.schema';

interface LoginFormFieldsProps {
  formData: LoginFormData;
  errors: Partial<Record<keyof LoginFormData, string>>;
  touched: Partial<Record<keyof LoginFormData, boolean>>;
  onInputChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onInputBlur: (e: React.FocusEvent<HTMLInputElement>) => void;
  className?: string;
}

export const LoginFormFields: React.FC<LoginFormFieldsProps> = ({
  formData,
  errors,
  touched,
  onInputChange,
  onInputBlur,
  className = ''
}) => {
  return (
    <div className={`space-y-6 ${className}`}>
      <FormField
        id="email"
        name="email"
        type="email"
        label="Email Address"
        value={formData.email}
        placeholder="Enter your email"
        error={touched.email ? errors.email : undefined}
        required
        onChange={onInputChange}
        onBlur={onInputBlur}
      />

      <FormField
        id="password"
        name="password"
        type="password"
        label="Password"
        value={formData.password}
        placeholder="Enter your password"
        error={touched.password ? errors.password : undefined}
        required
        onChange={onInputChange}
        onBlur={onInputBlur}
      />

      <CheckboxField
        id="remember"
        name="remember"
        label="Remember me"
        checked={formData.remember}
        onChange={onInputChange}
      />
    </div>
  );
};
