import React, { forwardRef, useState, useRef, useEffect } from 'react';

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  error?: string;
  className?: string;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ error, className = '', type, ...props }, ref) => {
    const [showPassword, setShowPassword] = useState(false);
    const [isFocused, setIsFocused] = useState(false);
    const [hasValue, setHasValue] = useState(false);
    const buttonRef = useRef<HTMLButtonElement>(null);
    const isPassword = type === 'password';

    // Check if input has value
    useEffect(() => {
      if (props.value) {
        setHasValue(String(props.value).length > 0);
      }
    }, [props.value]);

    const togglePassword = () => {
      setShowPassword(!showPassword);
      // Keep focus on input after toggle
      setTimeout(() => {
        if (ref && typeof ref === 'object' && ref.current) {
          ref.current.focus();
        }
      }, 0);
    };

    const handleFocus = (e: React.FocusEvent<HTMLInputElement>) => {
      setIsFocused(true);
      props.onFocus?.(e);
    };

    const handleBlur = (e: React.FocusEvent<HTMLInputElement>) => {
      setIsFocused(false);
      props.onBlur?.(e);
    };

    return (
      <div className="w-full">
        <div className="relative group">
          <input
            ref={ref}
            type={isPassword && showPassword ? 'text' : type}
            className={`
              w-full px-4 py-3 border rounded-lg text-gray-900 placeholder-gray-500
              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
              transition-all duration-300 ease-in-out
              ${isPassword ? 'pr-14' : ''}
              ${error 
                ? 'border-red-300 focus:ring-red-500 bg-red-50/30' 
                : isFocused
                ? 'border-blue-400 shadow-sm'
                : 'border-gray-300 hover:border-gray-400'
              }
              ${className}
            `}
            onFocus={handleFocus}
            onBlur={handleBlur}
            {...props}
          />
          
          {/* Enhanced Password Toggle Button */}
          {isPassword && (
            <div className="absolute inset-y-0 right-0 flex items-center pr-3">
              <button
                ref={buttonRef}
                type="button"
                onClick={togglePassword}
                className={`
                  relative p-1.5 rounded-md transition-all duration-200 ease-in-out
                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1
                  group-hover:bg-gray-100/80
                  ${isFocused ? 'bg-blue-50/80' : 'bg-transparent'}
                  ${hasValue ? 'opacity-100' : 'opacity-60'}
                  hover:opacity-100 hover:scale-105 active:scale-95
                `}
                tabIndex={-1}
                aria-label={showPassword ? 'Hide password' : 'Show password'}
                title={showPassword ? 'Hide password' : 'Show password'}
              >
                {/* Background circle for better visual feedback */}
                <div className={`
                  absolute inset-0 rounded-md transition-all duration-200
                  ${isFocused ? 'bg-blue-100/50' : 'bg-transparent'}
                `} />
                
                {/* Icon with smooth transition */}
                <div className="relative z-10 transition-all duration-300 ease-in-out">
                  {showPassword ? (
                    // Eye with slash (hide password) - more refined icon
                    <svg
                      className="w-5 h-5 text-gray-500 transition-colors duration-200"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      strokeWidth={1.5}
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88"
                      />
                    </svg>
                  ) : (
                    // Eye (show password) - more refined icon
                    <svg
                      className="w-5 h-5 text-gray-500 transition-colors duration-200"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      strokeWidth={1.5}
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                      />
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                    </svg>
                  )}
                </div>

                {/* Subtle pulse animation when toggling */}
                <div className={`
                  absolute inset-0 rounded-md bg-blue-200/30
                  transition-all duration-300 ease-in-out
                  ${showPassword ? 'scale-150 opacity-0' : 'scale-0 opacity-0'}
                `} />
              </button>
            </div>
          )}

          {/* Subtle border highlight on focus */}
          {isFocused && (
            <div className="absolute inset-0 rounded-lg border-2 border-blue-400 pointer-events-none transition-all duration-200" />
          )}
        </div>
        
        {error && (
          <div className="mt-2 flex items-center space-x-1 animate-fadeIn">
            <svg className="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
            </svg>
            <p className="text-sm text-red-600">
              {error}
            </p>
          </div>
        )}
      </div>
    );
  }
);

Input.displayName = 'Input';
