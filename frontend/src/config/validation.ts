export const VALIDATION_RULES = {
  email: {
    required: 'Email is required',
    email: 'Please enter a valid email address',
    max: 'Email cannot exceed 255 characters',
  },
  password: {
    required: 'Password is required',
    min: 'Password must be at least 6 characters',
    max: 'Password cannot exceed 255 characters',
  },
} as const;
