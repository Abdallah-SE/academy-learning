/**
 * Cookie utility functions for client-side cookie management
 * Note: These are for non-HttpOnly cookies only
 */

export const cookieUtils = {
  /**
   * Get a cookie value by name
   */
  get: (name: string): string | null => {
    if (typeof document === 'undefined') return null;
    
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
      return parts.pop()?.split(';').shift() || null;
    }
    return null;
  },

  /**
   * Set a cookie (for non-HttpOnly cookies)
   */
  set: (
    name: string, 
    value: string, 
    options: {
      expires?: Date;
      maxAge?: number;
      path?: string;
      domain?: string;
      secure?: boolean;
      sameSite?: 'strict' | 'lax' | 'none';
    } = {}
  ): void => {
    if (typeof document === 'undefined') return;

    let cookieString = `${name}=${value}`;

    if (options.expires) {
      cookieString += `; expires=${options.expires.toUTCString()}`;
    }
    if (options.maxAge) {
      cookieString += `; max-age=${options.maxAge}`;
    }
    if (options.path) {
      cookieString += `; path=${options.path}`;
    }
    if (options.domain) {
      cookieString += `; domain=${options.domain}`;
    }
    if (options.secure) {
      cookieString += `; secure`;
    }
    if (options.sameSite) {
      cookieString += `; samesite=${options.sameSite}`;
    }

    document.cookie = cookieString;
  },

  /**
   * Remove a cookie
   */
  remove: (name: string, path: string = '/'): void => {
    if (typeof document === 'undefined') return;
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=${path};`;
  },

  /**
   * Check if cookies are enabled
   */
  isEnabled: (): boolean => {
    if (typeof document === 'undefined') return false;
    
    try {
      document.cookie = 'test=1';
      const enabled = document.cookie.indexOf('test=1') !== -1;
      document.cookie = 'test=1; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
      return enabled;
    } catch {
      return false;
    }
  },
};
