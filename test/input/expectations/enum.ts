const Roles = {
  /** Can do anything */
  ADMIN: 'admin',
  /** Standard readonly */
  USER: 'user',
} as const;

export type Roles = typeof Roles[keyof typeof Roles]
