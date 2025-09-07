'use client';

import React from 'react';
import { AdminFormField, FormSection } from '@/components/molecules';
import { MultiSelect } from '@/components/atoms/MultiSelect';
import { CreateAdminFormData } from '@/schemas/admin.schema';

interface AdminFormFieldsProps {
  formData: CreateAdminFormData;
  errors: Partial<Record<keyof CreateAdminFormData, string>>;
  touched: Partial<Record<keyof CreateAdminFormData, boolean>>;
  onInputChange: (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => void;
  onInputBlur: (e: React.FocusEvent<HTMLInputElement | HTMLSelectElement>) => void;
  onRolesChange?: (roles: string[]) => void;
  availableRoles?: Array<{ value: string; label: string; description?: string }>;
  rolesLoading?: boolean;
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
  onRolesChange,
  availableRoles = [],
  rolesLoading = false,
  compact = false,
}) => {
  if (compact) {
    // Compact version for modal
    return (
      <div className="space-y-6">
        {/* Basic Information Section */}
        <div>
          <h4 className="text-sm font-medium text-gray-900 mb-3">Basic Information</h4>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        </div>

        {/* Security Section */}
        <div>
          <h4 className="text-sm font-medium text-gray-900 mb-3">Security</h4>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        </div>


        {/* Roles Section */}
        {onRolesChange && availableRoles.length > 0 && (
          <div>
            <h4 className="text-sm font-medium text-gray-900 mb-3">Role Assignment</h4>
            <MultiSelect
              options={availableRoles}
              value={formData.roles || []}
              onChange={onRolesChange}
              label="Roles"
              placeholder="Select roles for this admin..."
              error={touched.roles ? errors.roles : undefined}
              isLoading={rolesLoading}
            />
            <p className="text-xs text-gray-500 mt-2">
              Select one or more roles to assign to this admin
            </p>
          </div>
        )}
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

      </FormSection>

      {/* Roles Section */}
      {onRolesChange && availableRoles.length > 0 && (
        <FormSection
          title="Role Assignment"
          description="Assign roles to define the admin's permissions and access level"
        >
          <MultiSelect
            options={availableRoles}
            value={formData.roles || []}
            onChange={onRolesChange}
            label="Roles"
            placeholder="Select roles for this admin..."
            error={touched.roles ? errors.roles : undefined}
            isLoading={rolesLoading}
          />
          <p className="text-sm text-gray-500 mt-2">
            Select one or more roles to assign to this admin. Each role provides specific permissions and access levels.
          </p>
        </FormSection>
      )}
    </div>
  );
};
