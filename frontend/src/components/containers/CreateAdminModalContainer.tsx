'use client';

import React, { useCallback, useState } from 'react';
import { CreateAdminModal } from '@/components/presenters';
import { useCreateAdminForm } from '@/hooks/useCreateAdminForm';
import { AdminRepository } from '@/repositories/adminRepository';
import { CreateAdminFormData } from '@/schemas/admin.schema';
import toast from 'react-hot-toast';

interface CreateAdminModalContainerProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess?: () => void;
}

export const CreateAdminModalContainer: React.FC<CreateAdminModalContainerProps> = ({
  isOpen,
  onClose,
  onSuccess,
}) => {
  const [error, setError] = useState<string | null>(null);

  const handleCreateAdmin = useCallback(async (data: CreateAdminFormData) => {
    try {
      setError(null);
      
      const response = await AdminRepository.create(data);
      
      // Laravel Resource response structure: { data: {...}, message: "..." }
      // If we have data and message, it's a success
      if (response.data && response.message) {
        // Show success toast
        toast.success(`Admin Created Successfully! ${data.name} has been added to the system.`);
        
        // Close modal and refresh data
        onClose();
        onSuccess?.();
      } else {
        const errorMessage = response.message || 'Failed to create admin';
        setError(errorMessage);
        toast.error(`Creation Failed: ${errorMessage}`);
      }
    } catch (err: any) {
      console.error('Error creating admin:', err);
      
      // Handle different types of errors
      let errorMessage = 'An unexpected error occurred';
      
      if (err.response?.status === 422) {
        // Validation errors from backend
        const validationErrors = err.response.data?.errors;
        if (validationErrors) {
          const firstError = Object.values(validationErrors)[0] as string[];
          errorMessage = firstError[0] || 'Validation error occurred';
        } else {
          errorMessage = err.response.data?.message || 'Validation error occurred';
        }
      } else if (err.response?.status === 409) {
        // Conflict - email or username already exists
        errorMessage = 'Email or username already exists';
      } else if (err.response?.status === 403) {
        // Forbidden - insufficient permissions
        errorMessage = 'You do not have permission to create admins';
      } else if (err.response?.status >= 500) {
        // Server error
        errorMessage = 'Server error occurred. Please try again later.';
      } else if (!err.response) {
        // Network error
        errorMessage = 'Network error. Please check your connection and try again.';
      } else {
        // Other errors
        errorMessage = err.response.data?.message || err.message || 'An unexpected error occurred';
      }
      
      setError(errorMessage);
      toast.error(`Creation Failed: ${errorMessage}`);
    }
  }, [onClose, onSuccess]);

  const {
    formData,
    errors,
    touched,
    isLoading,
    isFormValid,
    handleInputChange,
    handleInputBlur,
    handleSubmit,
    resetForm,
  } = useCreateAdminForm({
    onSubmit: handleCreateAdmin,
  });

  // Reset form when modal closes
  const handleClose = useCallback(() => {
    resetForm();
    setError(null);
    onClose();
  }, [resetForm, onClose]);

  // Don't render if modal is not open (performance optimization)
  if (!isOpen) {
    return null;
  }

  return (
    <CreateAdminModal
      isOpen={isOpen}
      onClose={handleClose}
      formData={formData}
      errors={errors}
      touched={touched}
      isLoading={isLoading}
      isFormValid={isFormValid}
      onInputChange={handleInputChange}
      onInputBlur={handleInputBlur}
      onSubmit={handleSubmit}
    />
  );
};
