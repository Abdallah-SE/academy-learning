import { z } from 'zod';

export const createAdminSchema = z.object({
  name: z
    .string()
    .min(1, 'Name is required')
    .max(255, 'Name cannot exceed 255 characters')
    .trim(),
  email: z
    .string()
    .min(1, 'Email is required')
    .email('Please enter a valid email address')
    .max(255, 'Email cannot exceed 255 characters')
    .trim(),
  username: z
    .string()
    .min(1, 'Username is required')
    .max(255, 'Username cannot exceed 255 characters')
    .regex(/^[a-zA-Z0-9_-]+$/, 'Username can only contain letters, numbers, dashes, and underscores')
    .trim()
    .optional(),
  password: z
    .string()
    .min(1, 'Password is required')
    .min(6, 'Password must be at least 6 characters')
    .max(255, 'Password cannot exceed 255 characters'),
  password_confirmation: z
    .string()
    .min(1, 'Password confirmation is required')
    .min(6, 'Password confirmation must be at least 6 characters')
    .max(255, 'Password confirmation cannot exceed 255 characters'),
  status: z
    .enum(['active', 'inactive', 'suspended'])
    .optional()
    .default('active'),
  roles: z
    .array(z.string())
    .optional()
    .default([]),
}).refine((data) => data.password === data.password_confirmation, {
  message: "Password confirmation does not match",
  path: ["password_confirmation"],
});

export type CreateAdminFormData = z.infer<typeof createAdminSchema>;

export const updateAdminSchema = z.object({
  name: z
    .string()
    .min(1, 'Name is required')
    .max(255, 'Name cannot exceed 255 characters')
    .trim()
    .optional(),
  email: z
    .string()
    .min(1, 'Email is required')
    .email('Please enter a valid email address')
    .max(255, 'Email cannot exceed 255 characters')
    .trim()
    .optional(),
  username: z
    .string()
    .min(1, 'Username is required')
    .max(255, 'Username cannot exceed 255 characters')
    .regex(/^[a-zA-Z0-9_-]+$/, 'Username can only contain letters, numbers, dashes, and underscores')
    .trim()
    .optional(),
  password: z
    .string()
    .min(6, 'Password must be at least 6 characters')
    .max(255, 'Password cannot exceed 255 characters')
    .optional(),
  password_confirmation: z
    .string()
    .min(6, 'Password confirmation must be at least 6 characters')
    .max(255, 'Password confirmation cannot exceed 255 characters')
    .optional(),
  status: z
    .enum(['active', 'inactive', 'suspended'])
    .optional(),
  roles: z
    .array(z.string())
    .optional(),
}).refine((data) => {
  if (data.password && data.password_confirmation) {
    return data.password === data.password_confirmation;
  }
  return true;
}, {
  message: "Password confirmation does not match",
  path: ["password_confirmation"],
});

export type UpdateAdminFormData = z.infer<typeof updateAdminSchema>;
