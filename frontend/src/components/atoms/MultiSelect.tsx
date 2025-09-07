import React from 'react';
import Select, { MultiValue, StylesConfig } from 'react-select';

export interface MultiSelectOption {
  value: string;
  label: string;
  description?: string;
}

interface MultiSelectProps {
  options: MultiSelectOption[];
  value: string[];
  onChange: (values: string[]) => void;
  placeholder?: string;
  label?: string;
  error?: string;
  disabled?: boolean;
  className?: string;
  isLoading?: boolean;
}

export const MultiSelect: React.FC<MultiSelectProps> = ({
  options,
  value,
  onChange,
  placeholder = 'Select roles...',
  label,
  error,
  disabled = false,
  className = '',
  isLoading = false,
}) => {
  // Transform options for react-select
  const selectOptions = options.map(option => ({
    value: option.value,
    label: option.label,
    description: option.description,
  }));

  // Get selected options
  const selectedOptions = selectOptions.filter(option => 
    value.includes(option.value)
  );

  const handleChange = (selectedOptions: MultiValue<{value: string; label: string; description?: string}>) => {
    const values = selectedOptions ? selectedOptions.map(option => option.value) : [];
    onChange(values);
  };

  // Custom styles for react-select
  const customStyles: StylesConfig = {
    control: (provided, state) => ({
      ...provided,
      minHeight: '42px',
      borderColor: error ? '#ef4444' : state.isFocused ? '#3b82f6' : '#d1d5db',
      boxShadow: state.isFocused 
        ? error 
          ? '0 0 0 1px #ef4444' 
          : '0 0 0 1px #3b82f6'
        : 'none',
      '&:hover': {
        borderColor: error ? '#ef4444' : '#9ca3af',
      },
    }),
    multiValue: (provided) => ({
      ...provided,
      backgroundColor: '#dbeafe',
      borderRadius: '6px',
    }),
    multiValueLabel: (provided) => ({
      ...provided,
      color: '#1e40af',
      fontSize: '12px',
      fontWeight: '500',
    }),
    multiValueRemove: (provided) => ({
      ...provided,
      color: '#1e40af',
      '&:hover': {
        backgroundColor: '#bfdbfe',
        color: '#1e40af',
      },
    }),
    placeholder: (provided) => ({
      ...provided,
      color: '#6b7280',
      fontSize: '14px',
    }),
    option: (provided, state) => ({
      ...provided,
      backgroundColor: state.isSelected 
        ? '#3b82f6' 
        : state.isFocused 
          ? '#f3f4f6' 
          : 'white',
      color: state.isSelected ? 'white' : '#374151',
      fontSize: '14px',
    }),
  };

  // Custom option component to show description
  const Option = ({ innerProps, label, data, isSelected }: any) => (
    <div
      {...innerProps}
      className={`px-3 py-2 cursor-pointer hover:bg-gray-50 ${
        isSelected ? 'bg-blue-50' : ''
      }`}
    >
      <div className="font-medium text-gray-900">{label}</div>
      {data.description && (
        <div className="text-xs text-gray-500 mt-1">{data.description}</div>
      )}
    </div>
  );

  return (
    <div className={className}>
      {label && (
        <label className="block text-sm font-medium text-gray-700 mb-2">
          {label}
        </label>
      )}
      
      <Select
        isMulti
        options={selectOptions}
        value={selectedOptions}
        onChange={handleChange}
        placeholder={placeholder}
        isDisabled={disabled}
        isLoading={isLoading}
        styles={customStyles}
        components={{ Option }}
        className="react-select-container"
        classNamePrefix="react-select"
        noOptionsMessage={() => 'No roles found'}
        loadingMessage={() => 'Loading roles...'}
      />

      {/* Selected count indicator */}
      {value.length > 0 && (
        <p className="mt-2 text-xs text-gray-500">
          {value.length} role{value.length !== 1 ? 's' : ''} selected
        </p>
      )}

      {/* Error Message */}
      {error && (
        <p className="mt-1 text-sm text-red-600">{error}</p>
      )}
    </div>
  );
};
