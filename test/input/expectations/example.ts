export interface User {
  // mutators
  role_traditional: string
  role_new: string
  role_enum: Roles
  role_enum_traditional: Roles
}

const Roles = {
  /** Can do anything */
  ADMIN: 'admin',
  /** Standard readonly */
  USER: 'user',
} as const;

export type Roles = typeof Roles[keyof typeof Roles]
