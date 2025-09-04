'use client';

import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { demoLoginSchema, type DemoLoginFormData } from '@/schemas/auth.schema';
import { Input } from '@/components/atoms/Input';
import { Button } from '@/components/atoms/Button';

interface DemoLoginFormProps {
  onSubmit: (data: DemoLoginFormData) => Promise<void>;
  isLoading: boolean;
  error: string | null;
}

export const DemoLoginForm: React.FC<DemoLoginFormProps> = ({
  onSubmit,
  isLoading,
  error,
}) => {
  const [showPassword, setShowPassword] = useState(false);

  const {
    register,
    handleSubmit,
    formState: { errors, isValid, isDirty },
    setError: setFormError,
    clearErrors,
  } = useForm<DemoLoginFormData>({
    resolver: zodResolver(demoLoginSchema),
    mode: 'onChange',
    defaultValues: {
      email: '',
      password: '',
    },
  });

  const onFormSubmit = async (data: DemoLoginFormData) => {
    try {
      clearErrors();
      await onSubmit(data);
    } catch (err: any) {
      setFormError('root', {
        type: 'manual',
        message: err.message || 'An unexpected error occurred',
      });
    }
  };

  const handleInputChange = () => {
    if (errors.root) {
      clearErrors('root');
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
      <div className="max-w-md w-full space-y-8">
        {/* Header */}
        <div className="text-center">
          <div className="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg">
            <span className="text-white text-2xl font-bold">A</span>
          </div>
          <h2 className="mt-6 text-3xl font-bold text-gray-900">
            Admin Login
          </h2>
          <p className="mt-2 text-sm text-gray-600">
            Access your admin dashboard
          </p>
        </div>

        {/* Form */}
        <form className="mt-8 space-y-6" onSubmit={handleSubmit(onFormSubmit)}>
          <div className="space-y-4">
            <Input
              id="email"
              label="Email Address"
              type="email"
              placeholder="admin@example.com"
              autoComplete="email"
              required
              error={errors.email?.message}
              {...register('email', { onChange: handleInputChange })}
            />

            <div>
              <Input
                id="password"
                label="Password"
                type={showPassword ? 'text' : 'password'}
                placeholder="••••••••"
                autoComplete="current-password"
                required
                error={errors.password?.message}
                helperText="Must be at least 6 characters"
                {...register('password', { onChange: handleInputChange })}
              />
              <button
                type="button"
                className="mt-1 text-sm text-indigo-600 hover:text-indigo-500 transition-colors"
                onClick={() => setShowPassword(!showPassword)}
              >
                {showPassword ? 'Hide' : 'Show'} password
              </button>
            </div>
          </div>

          {/* Error Messages */}
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm flex items-center">
              <span className="mr-2">❌</span>
              {error}
            </div>
          )}

          {errors.root && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm flex items-center">
              <span className="mr-2">❌</span>
              {errors.root.message}
            </div>
          )}

          {/* Submit Button */}
          <Button
            type="submit"
            isLoading={isLoading}
            fullWidth
            disabled={isLoading || !isValid || !isDirty}
            className="mt-6"
          >
            {isLoading ? 'Signing in...' : 'Sign in'}
          </Button>

          {/* Demo Credentials */}
          <div className="mt-6 p-4 bg-gray-50 rounded-md border border-gray-200">
            <h4 className="text-sm font-medium text-gray-700 mb-3">Demo Credentials:</h4>
            <div className="text-xs text-gray-600 space-y-2">
              <div className="flex items-center justify-between">
                <span><strong>Super Admin:</strong></span>
                <code className="bg-gray-100 px-2 py-1 rounded">superadmin@arabicacademy.com</code>
                <span>/</span>
                <code className="bg-gray-100 px-2 py-1 rounded">password</code>
              </div>
              <div className="flex items-center justify-between">
                <span><strong>Admin:</strong></span>
                <code className="bg-gray-100 px-2 py-1 rounded">admin@arabicacademy.com</code>
                <span>/</span>
                <code className="bg-gray-100 px-2 py-1 rounded">password</code>
              </div>
              <div className="flex items-center justify-between">
                <span><strong>Moderator:</strong></span>
                <code className="bg-gray-100 px-2 py-1 rounded">moderator@arabicacademy.com</code>
                <span>/</span>
                <code className="bg-gray-100 px-2 py-1 rounded">password</code>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
};
