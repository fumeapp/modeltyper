export interface Complex {
  // columns
  id: number
  bigInteger: number
  binary: unknown
  boolean: boolean
  char: string
  dateTime: string
  immutableDateTime: string
  immutableCustomDateTime: string
  date: string
  immutableDate: string
  decimal: number
  double: number
  enum: string
  float: number
  integer: number
  ipAddress: string
  json: Record<string, unknown>
  jsonb: Record<string, unknown>
  longText: string
  macAddress: string
  mediumInteger: number
  mediumText: string
  smallInteger: number
  string: string
  castedUppercaseString: unknown
  stringWithMutatorAndNoAccessor: string
  enumWithMutatorAndNoAccessor: Roles
  text: string
  time: string
  timestamp: string
  year: number
  uuid: string
  ulid: string
  createdAt: string | null
  updatedAt: string | null
  deletedAt: string | null
  // relations
  complexRelationships: ComplexRelationship[]
  // counts
  complexRelationshipsCount: number
  // exists
  complexRelationshipsExists: boolean
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
