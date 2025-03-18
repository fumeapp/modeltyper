export interface User {
  // columns
  id: number
  name: string
  email: string
  email_verified_at: Date | null
  password?: string
  remember_token?: string | null
  created_at: Date | null
  updated_at: Date | null
  // mutators
  role_traditional: string
  role_new: string
  role_enum: Roles
  role_enum_traditional: Roles
  // relations
  notifications: DatabaseNotification[]
}

const Roles = {
  /** Can do anything */
  ADMIN: 'admin',
  /** Standard readonly */
  USER: 'user',
  /** Value that needs string escaping */
  USERCLASS: 'App\\Models\\User',
} as const;

export type Roles = typeof Roles[keyof typeof Roles]
