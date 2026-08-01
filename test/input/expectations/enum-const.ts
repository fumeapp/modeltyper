export const enum Roles {
  /** Can do anything */
  ADMIN = 'admin',
  /** Standard readonly */
  USER = 'user',
  /** Value that needs string escaping */
  USERCLASS = 'App\\Models\\User',
}

export type RolesEnum = `${Roles}`
