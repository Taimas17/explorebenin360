export type PaginationMeta = { total: number; current_page: number; per_page: number }
export type Paginated<T> = { data: T[]; meta: PaginationMeta }
