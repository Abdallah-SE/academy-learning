import { useState, useCallback } from 'react';
import { CreateAdminFormData, createAdminSchema } from '@/schemas/admin.schema';

interface UseCreateAdminFormProps {
  onSubmit: (data: CreateAdminFormData) => Promise<void>;
  initialData?: Partial<CreateAdminFormData>;
}

interface UseCreateAdminFormReturn {
  formData: CreateAdminFormData;
  errors: Partial<Record<keyof CreateAdminFormData, string>>;
  touched: Partial<Record<keyof CreateAdminFormData, boolean>>;
  isLoading: boolean;
  isFormValid: boolean;
  handleInputChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  handleInputBlur: (e: React.FocusEvent<HTMLInputElement>) => void;
  handleSubmit: (e: React.FormEvent<HTMLFormElement>) => Promise<void>;
  resetForm: () => void;
  setFieldValue: (field: keyof CreateAdminFormData, value: any) => void;
  setFieldError: (field: keyof CreateAdminFormData, error: string) => void;
  clearFieldError: (field: keyof CreateAdminFormData) => void;
}

const initialFormData: CreateAdminFormData = {
  name: '',
  email: '',
  username: '',
  password: '',
  password_confirmation: '',
  status: 'active',
  two_factor_enabled: false,
};

export const useCreateAdminForm = ({ 
  onSubmit, 
  initialData = {} 
}: UseCreateAdminFormProps): UseCreateAdminFormReturn => {
  const [formData, setFormData] = useState<CreateAdminFormData>({
    ...initialFormData,
    ...initialData,
  });
  
  const [errors, setErrors] = useState<Partial<Record<keyof CreateAdminFormData, string>>>({});
  const [touched, setTouched] = useState<Partial<Record<keyof CreateAdminFormData, boolean>>>({});
  const [isLoading, setIsLoading] = useState(false);

  // Validate form data
  const validateForm = useCallback((data: CreateAdminFormData): Partial<Record<keyof CreateAdminFormData, string>> => {
    try {
      createAdminSchema.parse(data);
      return {};
    } catch (error: any) {
      const validationErrors: Partial<Record<keyof CreateAdminFormData, string>> = {};
      
      if (error.errors) {
        error.errors.forEach((err: any) => {
          const field = err.path[0] as keyof CreateAdminFormData;
          validationErrors[field] = err.message;
        });
      }
      
      return validationErrors;
    }
  }, []);

  // Check if form is valid
  const isFormValid = Object.keys(validateForm(formData)).length === 0;

  // Handle input change
  const handleInputChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    const fieldName = name as keyof CreateAdminFormData;
    
    setFormData(prev => ({
      ...prev,
      [fieldName]: type === 'checkbox' ? checked : value,
    }));

    // Clear error when user starts typing
    if (errors[fieldName]) {
      setErrors(prev => ({
        ...prev,
        [fieldName]: undefined,
      }));
    }
  }, [errors]);

  // Handle input blur
  const handleInputBlur = useCallback((e: React.FocusEvent<HTMLInputElement>) => {
    const { name } = e.target;
    const fieldName = name as keyof CreateAdminFormData;
    
    setTouched(prev => ({
      ...prev,
      [fieldName]: true,
    }));

    // Validate field on blur
    const fieldErrors = validateForm(formData);
    if (fieldErrors[fieldName]) {
      setErrors(prev => ({
        ...prev,
        [fieldName]: fieldErrors[fieldName],
      }));
    }
  }, [formData, validateForm]);

  // Handle form submission
  const handleSubmit = useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    
    setIsLoading(true);
    
    try {
      // Mark all fields as touched
      const allTouched = Object.keys(formData).reduce((acc, key) => {
        acc[key as keyof CreateAdminFormData] = true;
        return acc;
      }, {} as Partial<Record<keyof CreateAdminFormData, boolean>>);
      
      setTouched(allTouched);

      // Validate form
      const validationErrors = validateForm(formData);
      if (Object.keys(validationErrors).length > 0) {
        setErrors(validationErrors);
        return;
      }

      // Clear errors and submit
      setErrors({});
      await onSubmit(formData);
      
    } catch (error) {
      console.error('Form submission error:', error);
    } finally {
      setIsLoading(false);
    }
  }, [formData, onSubmit, validateForm]);

  // Reset form
  const resetForm = useCallback(() => {
    setFormData({ ...initialFormData, ...initialData });
    setErrors({});
    setTouched({});
    setIsLoading(false);
  }, [initialData]);

  // Set field value programmatically
  const setFieldValue = useCallback((field: keyof CreateAdminFormData, value: any) => {
    setFormData(prev => ({
      ...prev,
      [field]: value,
    }));
  }, []);

  // Set field error programmatically
  const setFieldError = useCallback((field: keyof CreateAdminFormData, error: string) => {
    setErrors(prev => ({
      ...prev,
      [field]: error,
    }));
  }, []);

  // Clear field error
  const clearFieldError = useCallback((field: keyof CreateAdminFormData) => {
    setErrors(prev => ({
      ...prev,
      [field]: undefined,
    }));
  }, []);

  return {
    formData,
    errors,
    touched,
    isLoading,
    isFormValid,
    handleInputChange,
    handleInputBlur,
    handleSubmit,
    resetForm,
    setFieldValue,
    setFieldError,
    clearFieldError,
  };
};
