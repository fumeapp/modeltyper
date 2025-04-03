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
}
