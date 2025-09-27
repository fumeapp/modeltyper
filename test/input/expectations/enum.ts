const Roles = {
  /** Can do anything */
  ADMIN: 'admin',
  /** Standard readonly */
  USER: 'user',
  /** Value that needs string escaping */
  USERCLASS: 'App\\Models\\User',
} as const;

export type Roles = typeof Roles[keyof typeof Roles]
