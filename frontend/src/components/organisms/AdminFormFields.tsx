'use client';

import React from 'react';
import { AdminFormField, FormSection } from '@/components/molecules';
import { CreateAdminFormData } from '@/schemas/admin.schema';

interface AdminFormFieldsProps {
  formData: CreateAdminFormData;
  errors: Partial<Record<keyof CreateAdminFormData, string>>;
  touched: Partial<Record<keyof CreateAdminFormData, boolean>>;
  onInputChange: (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => void;
  onInputBlur: (e: React.FocusEvent<HTMLInputElement | HTMLSelectElement>) => void;
  compact?: boolean; // For modal use
}

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'suspended', label: 'Suspended' },
];

export const AdminFormFields: React.FC<AdminFormFieldsProps> = ({
  formData,
  errors,
  touched,
  onInputChange,
  onInputBlur,
  compact = false,
}) => {
  if (compact) {
    // Compact version for modal
    return (
      <div className="space-y-5">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          <AdminFormField
            id="name"
            name="name"
            type="text"
            label="Full Name"
            value={formData.name}
            placeholder="Enter full name"
            error={touched.name ? errors.name : undefined}
            required
            onChange={onInputChange}
            onInputBlur={onInputBlur}
          />

          <AdminFormField
            id="email"
            name="email"
            type="email"
            label="Email Address"
            value={formData.email}
            placeholder="Enter email address"
            error={touched.email ? errors.email : undefined}
            required
            onChange={onInputChange}
            onInputBlur={onInputBlur}
          />

          <AdminFormField
            id="username"
            name="username"
            type="text"
            label="Username"
            value={formData.username}
            placeholder="Enter username (optional)"
            error={touched.username ? errors.username : undefined}
            onChange={onInputChange}
            onInputBlur={onInputBlur}
          />

          <AdminFormField
            id="status"
            name="status"
            type="select"
            label="Status"
            value={formData.status}
            error={touched.status ? errors.status : undefined}
            options={statusOptions}
            onChange={onInputChange}
            onInputBlur={onInputBlur}
          />

          <AdminFormField
            id="password"
            name="password"
            type="password"
            label="Password"
            value={formData.password}
            placeholder="Enter password"
            error={touched.password ? errors.password : undefined}
            required
            onChange={onInputChange}
            onInputBlur={onInputBlur}
          />

          <AdminFormField
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            label="Confirm Password"
            value={formData.password_confirmation}
            placeholder="Confirm password"
            error={touched.password_confirmation ? errors.password_confirmation : undefined}
            required
            onChange={onInputChange}
            onInputBlur={onInputBlur}
          />
        </div>

        <div className="mt-6 pt-4 border-t border-gray-100">
          <div className="bg-blue-50 rounded-lg p-4">
            <AdminFormField
              id="two_factor_enabled"
              name="two_factor_enabled"
              type="checkbox"
              label="Enable Two-Factor Authentication"
              value={formData.two_factor_enabled}
              error={touched.two_factor_enabled ? errors.two_factor_enabled : undefined}
              onChange={onInputChange}
              onInputBlur={onInputBlur}
            />
            <p className="text-xs text-blue-600 mt-2 ml-6">
              Recommended for enhanced security
            </p>
          </div>
        </div>
      </div>
    );
  }

  // Full version for page
  return (
    <div className="space-y-8">
      {/* Basic Information Section */}
      <FormSection
        title="Basic Information"
        description="Enter the basic details for the new admin user"
      >
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <AdminFormField
            id="name"
            name="name"
            type="text"
            label="Full Name"
            value={formData.name}
            placeholder="Enter full name"
            error={touched.name ? errors.name : undefined}
            required
            onChange={onInputChange}
            onBlur={onInputBlur}
          />

          <AdminFormField
            id="email"
            name="email"
            type="email"
            label="Email Address"
            value={formData.email}
            placeholder="Enter email address"
            error={touched.email ? errors.email : undefined}
            required
            onChange={onInputChange}
            onBlur={onInputBlur}
          />

          <AdminFormField
            id="username"
            name="username"
            type="text"
            label="Username"
            value={formData.username}
            placeholder="Enter username (optional)"
            error={touched.username ? errors.username : undefined}
            onChange={onInputChange}
            onBlur={onInputBlur}
          />

          <AdminFormField
            id="status"
            name="status"
            type="select"
            label="Status"
            value={formData.status}
            error={touched.status ? errors.status : undefined}
            options={statusOptions}
            onChange={onInputChange}
            onBlur={onInputBlur}
          />
        </div>
      </FormSection>

      {/* Security Section */}
      <FormSection
        title="Security Settings"
        description="Configure password and security options"
      >
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <AdminFormField
            id="password"
            name="password"
            type="password"
            label="Password"
            value={formData.password}
            placeholder="Enter password"
            error={touched.password ? errors.password : undefined}
            required
            onChange={onInputChange}
            onBlur={onInputBlur}
          />

          <AdminFormField
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            label="Confirm Password"
            value={formData.password_confirmation}
            placeholder="Confirm password"
            error={touched.password_confirmation ? errors.password_confirmation : undefined}
            required
            onChange={onInputChange}
            onBlur={onInputBlur}
          />
        </div>

        <div className="mt-6">
          <AdminFormField
            id="two_factor_enabled"
            name="two_factor_enabled"
            type="checkbox"
            label="Enable Two-Factor Authentication"
            value={formData.two_factor_enabled}
            error={touched.two_factor_enabled ? errors.two_factor_enabled : undefined}
            onChange={onInputChange}
            onBlur={onInputBlur}
          />
        </div>
      </FormSection>
    </div>
  );
};
