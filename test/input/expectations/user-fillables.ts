export interface User {
  // columns
  id: number
  name: string
  email: string
  email_verified_at: string | null
  password?: string
  remember_token?: string | null
  created_at: string | null
  updated_at: string | null
  // mutators
  role_traditional: string
  role_new: string
  role_enum: Roles
  role_enum_traditional: Roles
  // relations
  notifications: DatabaseNotification[]
}
export type UserEditable = Pick<User, 'name' | 'email' | 'password' | 'role_traditional' | 'role_new'>

const Roles = {
  /** Can do anything */
  ADMIN: 'admin',
  /** Standard readonly */
  USER: 'user',
  /** Value that needs string escaping */
  USERCLASS: 'App\\Models\\User',
} as const;

export type Roles = typeof Roles[keyof typeof Roles]
