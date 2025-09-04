'use client';

import React from 'react';
import { Button } from '@/components/atoms/Button';
import { ErrorMessage } from '@/components/atoms/ErrorMessage';
import { FormHeader } from '@/components/molecules/FormHeader';
import { LoginFormFields } from '@/components/organisms/LoginFormFields';
import { LoginLayout } from '@/components/organisms/LoginLayout';
import { useLoginForm } from '@/hooks/useLoginForm';
import { LoginFormData } from '@/schemas/auth.schema';

interface LoginFormProps {
  onSubmit: (data: LoginFormData) => Promise<void>;
  error: string | null;
}

export const LoginForm: React.FC<LoginFormProps> = ({ onSubmit, error }) => {
  const {
    formData,
    errors,
    touched,
    isLoading,
    isFormValid,
    handleInputChange,
    handleInputBlur,
    handleSubmit
  } = useLoginForm({ onSubmit });

  return (
    <LoginLayout
      logo={{
        src: "/images/arabic-academic-logo.png",
        alt: "Arabic Academic",
        width: 400,
        height: 400
      }}
    >
      <FormHeader
        title="Welcome Back"
        subtitle="Sign in to your admin account"
      />

      <form onSubmit={handleSubmit} className="space-y-6">
        <LoginFormFields
          formData={formData}
          errors={errors}
          touched={touched}
          onInputChange={handleInputChange}
          onInputBlur={handleInputBlur}
        />

        {error && (
          <ErrorMessage
            title="Login Error"
            message={error}
          />
        )}

        <Button
          type="submit"
          disabled={!isFormValid || isLoading}
          className="w-full"
        >
          {isLoading ? 'Signing in...' : 'Sign In'}
        </Button>
      </form>
    </LoginLayout>
  );
};
