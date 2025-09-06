# Logout Components

This directory contains reusable logout components that can be used across the application.

## Components

### 1. LogoutButton

A simple, reusable logout button component with multiple variants and customization options.

#### Usage

```tsx
import { LogoutButton } from '@/components/atoms/LogoutButton';

// Basic usage
<LogoutButton />

// With custom styling
<LogoutButton 
  variant="destructive"
  size="lg"
  showIcon={false}
  className="custom-class"
/>

// With custom text
<LogoutButton 
  text="Sign Out"
  translationNamespace="admin"
/>
```

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | `'default' \| 'ghost' \| 'outline' \| 'destructive'` | `'default'` | Button style variant |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` | Button size |
| `showIcon` | `boolean` | `true` | Whether to show logout icon |
| `showLoading` | `boolean` | `true` | Whether to show loading state |
| `className` | `string` | - | Custom CSS classes |
| `text` | `string` | - | Custom button text (overrides translation) |
| `disabled` | `boolean` | `false` | Whether button is disabled |
| `onBeforeLogout` | `() => void \| Promise<void>` | - | Callback before logout |
| `onAfterLogout` | `() => void` | - | Callback after logout |
| `translationNamespace` | `'common' \| 'admin' \| 'dashboard'` | `'common'` | Translation namespace |

### 2. LogoutDropdown

A dropdown component that shows user information and logout option.

#### Usage

```tsx
import { LogoutDropdown } from '@/components/atoms/LogoutDropdown';

// Basic usage
<LogoutDropdown />

// With user info
<LogoutDropdown 
  user={{
    name: "John Doe",
    email: "john@example.com"
  }}
  variant="profile"
/>

// Minimal variant
<LogoutDropdown 
  variant="minimal"
  showUserInfo={false}
/>
```

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `user` | `{ name?: string; email?: string; avatar?: string }` | - | User information to display |
| `variant` | `'default' \| 'minimal' \| 'profile'` | `'default'` | Dropdown style variant |
| `className` | `string` | - | Custom CSS classes |
| `showUserInfo` | `boolean` | `true` | Whether to show user information |
| `showLoading` | `boolean` | `true` | Whether to show loading state |
| `translationNamespace` | `'common' \| 'admin' \| 'dashboard'` | `'common'` | Translation namespace |
| `onBeforeLogout` | `() => void \| Promise<void>` | - | Callback before logout |
| `onAfterLogout` | `() => void` | - | Callback after logout |

## Examples

### Header with Logout Button

```tsx
<header className="bg-white border-b border-gray-200 shadow-sm">
  <div className="flex items-center justify-between h-16 px-6">
    <h1 className="text-xl font-semibold text-gray-900">Admin Panel</h1>
    
    <div className="flex items-center space-x-4">
      <LanguageSelector />
      <div className="h-6 w-px bg-gray-300"></div>
      <LogoutButton 
        variant="default"
        size="md"
        translationNamespace="admin"
      />
    </div>
  </div>
</header>
```

### User Profile Dropdown

```tsx
<LogoutDropdown 
  user={{
    name: user?.name,
    email: user?.email,
    avatar: user?.avatar
  }}
  variant="profile"
  translationNamespace="admin"
/>
```

### Minimal Logout in Sidebar

```tsx
<LogoutButton 
  variant="ghost"
  size="sm"
  showIcon={false}
  className="w-full justify-start"
/>
```

## Features

- ✅ **Multi-language support** - Uses translation system
- ✅ **Loading states** - Shows loading spinner during logout
- ✅ **Error handling** - Graceful error handling with fallbacks
- ✅ **Accessibility** - Proper ARIA labels and keyboard navigation
- ✅ **Customizable** - Multiple variants and styling options
- ✅ **TypeScript** - Full type safety
- ✅ **Responsive** - Works on all screen sizes
- ✅ **Consistent** - Follows design system patterns

## Translation Keys

The components use the following translation keys:

- `common.logout` - "Logout" / "تسجيل الخروج" / "Abmelden"
- `admin.logout` - "Logout" / "تسجيل الخروج" / "Abmelden"  
- `dashboard.logout` - "Logout" / "تسجيل الخروج" / "Abmelden"

## Styling

The components use Tailwind CSS classes and follow the application's design system. You can customize the appearance using:

1. **Variant props** - Pre-defined style variants
2. **Size props** - Pre-defined size options
3. **Custom className** - Override with custom CSS classes
4. **CSS variables** - Use CSS custom properties for theming
