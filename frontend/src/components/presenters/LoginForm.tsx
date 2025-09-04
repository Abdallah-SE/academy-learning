'use client';

import React, { useState, useCallback } from 'react';
import { Button } from '@/components/atoms/Button';
import { Input } from '@/components/atoms/Input';
import { LoginFormData, loginSchema } from '@/schemas/auth.schema';
import Image from 'next/image';

interface LoginFormProps {
  onSubmit: (data: LoginFormData) => Promise<void>;
  isLoading: boolean;
  error: string | null;
}

export const LoginForm: React.FC<LoginFormProps> = ({ onSubmit, isLoading, error }) => {
  const [formData, setFormData] = useState<LoginFormData>({
    email: '',
    password: '',
    remember: false
  });

  const [errors, setErrors] = useState<Partial<Record<keyof LoginFormData, string>>>({});
  const [touched, setTouched] = useState<Partial<Record<keyof LoginFormData, boolean>>>({});

  // ✅ Use Zod for validation
  const validateField = useCallback((name: keyof LoginFormData, value: string | boolean) => {
    try {
      // Create a partial object with the field being validated
      const fieldData = { ...formData, [name]: value };
      
      // Validate the specific field using Zod
      const fieldSchema = loginSchema.pick({ [name]: true });
      fieldSchema.parse({ [name]: value });
      
      // If validation passes, remove the error
      setErrors(prev => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    } catch (error: any) {
      // If validation fails, set the error
      if (error.errors && error.errors[0]) {
        setErrors(prev => ({
          ...prev,
          [name]: error.errors[0].message
        }));
      }
    }
  }, [formData]);

  // ✅ Validate entire form
  const validateForm = useCallback(() => {
    try {
      loginSchema.parse(formData);
      setErrors({});
      return true;
    } catch (error: any) {
      const newErrors: Partial<Record<keyof LoginFormData, string>> = {};
      
      if (error.errors) {
        error.errors.forEach((err: any) => {
          if (err.path && err.path[0]) {
            newErrors[err.path[0] as keyof LoginFormData] = err.message;
          }
        });
      }
      
      setErrors(newErrors);
      return false;
    }
  }, [formData]);

  // ✅ Fix: Handle input change events properly
  const handleInputChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    const fieldName = name as keyof LoginFormData;
    const fieldValue = type === 'checkbox' ? checked : value;
    
    setFormData(prev => ({ ...prev, [fieldName]: fieldValue }));

    // Validate on change for better UX
    if (touched[fieldName]) {
      validateField(fieldName, fieldValue);
    }
  }, [touched, validateField]);

  // ✅ Fix: Handle input blur events properly
  const handleInputBlur = useCallback((e: React.FocusEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    const fieldName = name as keyof LoginFormData;
    const fieldValue = type === 'checkbox' ? checked : value;
    
    setTouched(prev => ({ ...prev, [fieldName]: true }));
    validateField(fieldName, fieldValue);
  }, [validateField]);

  const handleSubmit = useCallback(async (e: React.FormEvent) => {
    e.preventDefault();

    // Mark all fields as touched
    setTouched({ email: true, password: true, remember: true });

    // Validate entire form
    const isValid = validateForm();
    
    if (isValid) {
      try {
        await onSubmit(formData);
      } catch (error) {
        console.error('Submit error:', error);
      }
    }
  }, [formData, validateForm, onSubmit]);

  // Check if form is valid
  const isFormValid = Object.keys(errors).length === 0 && formData.email && formData.password;

  return (
    <div className="w-full max-w-6xl mx-auto">
      <div className="flex items-center justify-center min-h-screen">
        {/* Left Side - Logo Only */}
        <div className="hidden lg:flex lg:w-1/2 lg:justify-center lg:items-center lg:pr-16">
          <div className="text-center">
            <Image
              src="/images/arabic-academic-logo.png"
              alt="Arabic Academic"
              width={400}
              height={400}
              className="mx-auto"
              priority // ✅ Fix: Add priority for LCP
            />
          </div>
        </div>

        {/* Right Side - Login Form */}
        <div className="w-full lg:w-1/2 px-8 lg:px-16">
          <div className="max-w-md mx-auto">
            <div className="text-center mb-8">
              <h1 className="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
              <p className="text-gray-600">Sign in to your admin account</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Email Field */}
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                  Email Address
                </label>
                <Input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleInputChange}
                  onBlur={handleInputBlur}
                  error={touched.email ? errors.email : undefined}
                  placeholder="Enter your email"
                  required
                />
              </div>

              {/* Password Field */}
              <div>
                <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
                  Password
                </label>
                <Input
                  type="password"
                  id="password"
                  name="password"
                  value={formData.password}
                  onChange={handleInputChange}
                  onBlur={handleInputBlur}
                  error={touched.password ? errors.password : undefined}
                  placeholder="Enter your password"
                  required
                />
              </div>

              {/* Remember Me Checkbox */}
              <div className="flex items-center">
                <input
                  type="checkbox"
                  id="remember"
                  name="remember"
                  checked={formData.remember}
                  onChange={handleInputChange}
                  className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label htmlFor="remember" className="ml-2 block text-sm text-gray-900">
                  Remember me
                </label>
              </div>

              {/* Error Display */}
              {error && (
                <div className="rounded-md bg-red-50 p-4">
                  <div className="flex">
                    <div className="ml-3">
                      <h3 className="text-sm font-medium text-red-800">
                        Login Error
                      </h3>
                      <div className="mt-2 text-sm text-red-700">
                        <p>{ error  }</p>
                      </div>
                
                    </div>
                  </div> 
                </div>
              )}

              {/* Submit Button */}
              <Button
                type="submit"
                disabled={!isFormValid || isLoading}
                className="w-full"
              >
                {isLoading ? 'Signing in...' : 'Sign In'}
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};
