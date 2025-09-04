'use client';

import { useState, useCallback } from 'react';
import { LoginFormData, loginSchema } from '@/schemas/auth.schema';

interface UseLoginFormProps {
  onSubmit: (data: LoginFormData) => Promise<void>;
}

export const useLoginForm = ({ onSubmit }: UseLoginFormProps) => {
  const [formData, setFormData] = useState<LoginFormData>({
    email: '',
    password: '',
    remember: false
  });

  const [errors, setErrors] = useState<Partial<Record<keyof LoginFormData, string>>>({});
  const [touched, setTouched] = useState<Partial<Record<keyof LoginFormData, boolean>>>({});
  const [isLoading, setIsLoading] = useState(false);

  const validateField = useCallback((name: keyof LoginFormData, value: string | boolean) => {
    try {
      const fieldSchema = loginSchema.pick({ [name]: true });
      fieldSchema.parse({ [name]: value });
      
      setErrors(prev => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    } catch (error: any) {
      if (error.errors && error.errors[0]) {
        setErrors(prev => ({
          ...prev,
          [name]: error.errors[0].message
        }));
      }
    }
  }, []);

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

  const handleInputChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    const fieldName = name as keyof LoginFormData;
    const fieldValue = type === 'checkbox' ? checked : value;
    
    setFormData(prev => ({ ...prev, [fieldName]: fieldValue }));

    if (touched[fieldName]) {
      validateField(fieldName, fieldValue);
    }
  }, [touched, validateField]);

  const handleInputBlur = useCallback((e: React.FocusEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    const fieldName = name as keyof LoginFormData;
    const fieldValue = type === 'checkbox' ? checked : value;
    
    setTouched(prev => ({ ...prev, [fieldName]: true }));
    validateField(fieldName, fieldValue);
  }, [validateField]);

  const handleSubmit = useCallback(async (e: React.FormEvent) => {
    e.preventDefault();

    setTouched({ email: true, password: true, remember: true });
    const isValid = validateForm();
    
    if (isValid) {
      setIsLoading(true);
      try {
        await onSubmit(formData);
      } catch (error) {
        console.error('Submit error:', error);
      } finally {
        setIsLoading(false);
      }
    }
  }, [formData, validateForm, onSubmit]);

  const isFormValid = Object.keys(errors).length === 0 && formData.email && formData.password;

  return {
    formData,
    errors,
    touched,
    isLoading,
    isFormValid,
    handleInputChange,
    handleInputBlur,
    handleSubmit
  };
};
