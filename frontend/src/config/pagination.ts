export const PAGINATION_CONFIG = {
  DEFAULT_PER_PAGE: 10,
  MAX_PER_PAGE: 100,
  MIN_PER_PAGE: 1,
  OPTIONS: [5, 10, 25, 50, 100],
  
  // Module specific configurations
  MODULES: {
    ADMIN: {
      DEFAULT_PER_PAGE: 10,
    },
    USER: {
      DEFAULT_PER_PAGE: 10,
    },
    MEMBERSHIP: {
      DEFAULT_PER_PAGE: 10,
    },
  },
} as const;

export type PaginationConfig = typeof PAGINATION_CONFIG;