export interface Complex {
  // columns
  Id: number
  BigInteger: number
  Binary: unknown
  Boolean: boolean
  Char: string
  DateTime: string
  ImmutableDateTime: string
  ImmutableCustomDateTime: string
  Date: string
  ImmutableDate: string
  Decimal: number
  Double: number
  Enum: string
  Float: number
  Integer: number
  IpAddress: string
  Json: Record<string, unknown>
  Jsonb: Record<string, unknown>
  LongText: string
  MacAddress: string
  MediumInteger: number
  MediumText: string
  SmallInteger: number
  String: string
  CastedUppercaseString: unknown
  Text: string
  Time: string
  Timestamp: string
  Year: number
  Uuid: string
  Ulid: string
  CreatedAt: string | null
  UpdatedAt: string | null
  DeletedAt: string | null
  // relations
  ComplexRelationships: ComplexRelationship[]
}
