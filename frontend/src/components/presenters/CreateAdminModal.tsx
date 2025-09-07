'use client';

import React from 'react';
import { Modal, Button } from '@/components/atoms';
import { AdminFormFields } from '@/components/organisms';
import { CreateAdminFormData } from '@/schemas/admin.schema';

interface CreateAdminModalProps {
  isOpen: boolean;
  onClose: () => void;
  formData: CreateAdminFormData;
  errors: Partial<Record<keyof CreateAdminFormData, string>>;
  touched: Partial<Record<keyof CreateAdminFormData, boolean>>;
  isLoading: boolean;
  isFormValid: boolean;
  onInputChange: (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => void;
  onInputBlur: (e: React.FocusEvent<HTMLInputElement | HTMLSelectElement>) => void;
  onSubmit: (e: React.FormEvent<HTMLFormElement>) => Promise<void>;
  onRolesChange?: (roles: string[]) => void;
  availableRoles?: Array<{ value: string; label: string; description?: string }>;
  rolesLoading?: boolean;
}

export const CreateAdminModal: React.FC<CreateAdminModalProps> = ({
  isOpen,
  onClose,
  formData,
  errors,
  touched,
  isLoading,
  isFormValid,
  onInputChange,
  onInputBlur,
  onSubmit,
  onRolesChange,
  availableRoles = [],
  rolesLoading = false,
}) => {
  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title="Create New Admin"
      size="lg"
    >
      <form onSubmit={onSubmit} className="space-y-6">
        <div className="mb-6">
          <div className="flex items-center space-x-3 mb-3">
            <div className="flex-shrink-0">
              <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
            </div>
            <div>
              <h4 className="text-lg font-medium text-gray-900">Add New Administrator</h4>
              <p className="text-sm text-gray-500">Create a new admin account with the required permissions</p>
            </div>
          </div>
        </div>

        <AdminFormFields
          formData={formData}
          errors={errors}
          touched={touched}
          onInputChange={onInputChange}
          onInputBlur={onInputBlur}
          onRolesChange={onRolesChange}
          availableRoles={availableRoles}
          rolesLoading={rolesLoading}
          compact={true}
        />


        <div className="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100 bg-gray-50/50 -mx-6 px-6 py-4">
          <Button
            type="button"
            variant="secondary"
            onClick={onClose}
            disabled={isLoading}
            className="px-6 py-2.5 text-sm font-medium"
          >
            Cancel
          </Button>
          
          <Button
            type="submit"
            disabled={!isFormValid || isLoading}
            className="px-6 py-2.5 text-sm font-medium bg-blue-600 hover:bg-blue-700 focus:ring-blue-500"
          >
            {isLoading ? (
              <div className="flex items-center space-x-2">
                <svg className="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Creating...</span>
              </div>
            ) : (
              <div className="flex items-center space-x-2">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Create Admin</span>
              </div>
            )}
          </Button>
        </div>
      </form>
    </Modal>
  );
};
