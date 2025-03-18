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
  role_enum: RolesEnum
  role_enum_traditional: RolesEnum
  // relations
  notifications: DatabaseNotification[]
}

export const enum Roles {
  /** Can do anything */
  ADMIN = 'admin',
  /** Standard readonly */
  USER = 'user',
  /** Value that needs string escaping */
  USERCLASS = 'App\\Models\\User',
}

export type RolesEnum = `${Roles}`
