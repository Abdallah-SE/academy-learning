import { z } from 'zod';

export const loginSchema = z.object({
  email: z
    .string()
    .min(1, 'Email is required')
    .email('Please enter a valid email address')
    .max(255, 'Email cannot exceed 255 characters')
    .trim(),
  password: z
    .string()
    .min(1, 'Password is required')
    .min(6, 'Password must be at least 6 characters')
    .max(255, 'Password cannot exceed 255 characters'),
  remember: z
    .boolean()
    .optional()
    .default(false),
});

export type LoginFormData = z.infer<typeof loginSchema>;

// Optional: Create a more lenient schema for demo purposes
export const demoLoginSchema = z.object({
  email: z
    .string()
    .min(1, 'Email is required')
    .email('Please enter a valid email address')
    .max(255, 'Email cannot exceed 255 characters'),
  password: z
    .string()
    .min(1, 'Password is required')
    .min(6, 'Password must be at least 6 characters')
    .max(255, 'Password cannot exceed 255 characters'),
});

export type DemoLoginFormData = z.infer<typeof demoLoginSchema>;
