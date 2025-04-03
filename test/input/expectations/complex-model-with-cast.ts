export interface Complex {
  // columns
  id: number
  big_integer: number
  binary: unknown
  boolean: boolean
  char: string
  date_time: string
  immutable_date_time: string
  immutable_custom_date_time: string
  date: string
  immutable_date: string
  decimal: number
  double: number
  enum: string
  float: number
  integer: number
  ip_address: string
  json: Record<string, unknown>
  jsonb: Record<string, unknown>
  long_text: string
  mac_address: string
  medium_integer: number
  medium_text: string
  small_integer: number
  string: string
  casted_uppercase_string: string
  text: string
  time: string
  timestamp: string
  year: number
  uuid: string
  ulid: string
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // relations
  complex_relationships: ComplexRelationship[]
}
