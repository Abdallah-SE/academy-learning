'use client';

import React, { useCallback, useState, useEffect } from 'react';
import { CreateAdminModal } from '@/components/presenters';
import { useCreateAdminForm } from '@/hooks/useCreateAdminForm';
import { AdminRepository } from '@/repositories/adminRepository';
import { CreateAdminFormData } from '@/schemas/admin.schema';
import { Role } from '@/types/admin';
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
  const [availableRoles, setAvailableRoles] = useState<Role[]>([]);
  const [rolesLoading, setRolesLoading] = useState(false);

  // Fetch available roles when modal opens
  useEffect(() => {
    if (isOpen && availableRoles.length === 0) {
      const fetchRoles = async () => {
        try {
          setRolesLoading(true);
          const roles = await AdminRepository.getAvailableRoles();
          setAvailableRoles(roles);
        } catch (error) {
          console.error('Error fetching roles:', error);
          toast.error('Failed to load available roles');
        } finally {
          setRolesLoading(false);
        }
      };
      
      fetchRoles();
    }
  }, [isOpen, availableRoles.length]);

  const handleCreateAdmin = useCallback(async (data: CreateAdminFormData) => {
    try {
      setError(null);
      
      // Clean up data before sending
      const cleanData = {
        ...data,
        username: data.username?.trim() || null, // Convert empty string to null
        roles: data.roles || [], // Ensure roles is always an array
      };

      console.log('🚀 Creating admin with data:', cleanData);
      
      const response = await AdminRepository.create(cleanData);
      
      if (response.data && response.message) {
        // Success flow
        toast.success(`Admin Created Successfully! ${data.name} has been added to the system.`);
        
        // Reset form and close modal
        resetForm();
        setError(null);
        onClose();
        onSuccess?.();
      } else {
        const errorMessage = response.message || 'Failed to create admin';
        setError(errorMessage);
        toast.error(`Creation Failed: ${errorMessage}`);
      }
    } catch (err: any) {
      console.error('❌ Error creating admin:', err);
      
      // Professional error handling with detailed validation error display
      let errorMessage = 'An unexpected error occurred';
      let validationErrors: Record<string, string[]> = {};
      
      if (err.response?.status === 422) {
        // Validation errors from backend
        const backendErrors = err.response.data?.errors;
        if (backendErrors) {
          validationErrors = backendErrors;
          // Display first validation error
          const firstError = Object.values(backendErrors)[0] as string[];
          errorMessage = firstError[0] || 'Validation error occurred';
          
          // Show all validation errors in console for debugging
          console.group('🔍 Validation Errors:');
          Object.entries(backendErrors).forEach(([field, messages]) => {
            console.error(`${field}:`, messages);
          });
          console.groupEnd();
        } else {
          errorMessage = err.response.data?.message || 'Validation error occurred';
        }
      } else if (err.response?.status === 409) {
        errorMessage = 'Email or username already exists';
      } else if (err.response?.status === 403) {
        errorMessage = 'You do not have permission to create admins';
      } else if (err.response?.status >= 500) {
        errorMessage = 'Server error occurred. Please try again later.';
      } else if (!err.response) {
        errorMessage = 'Network error. Please check your connection and try again.';
      } else {
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
    handleRolesChange,
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

  // Transform roles for MultiSelect component
  const roleOptions = availableRoles.map(role => ({
    value: role.name,
    label: role.name.charAt(0).toUpperCase() + role.name.slice(1).replace(/_/g, ' '),
    description: `Role: ${role.name}`
  }));

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
      onRolesChange={handleRolesChange}
      availableRoles={roleOptions}
      rolesLoading={rolesLoading}
    />
  );
};
