export type PlaceFilters = {
  q?: string
  type?: string
  city?: string
  tag?: string
  featured?: boolean
  min_price?: number
  max_price?: number
  bounds?: string
  sort?: 'relevance'|'rating'|'price'|'recent'
  page?: number
  per_page?: number
}

export type AccommodationFilters = {
  q?: string
  type?: string
  city?: string
  min_price?: number
  max_price?: number
  capacity?: number
  featured?: boolean
  sort?: 'price'|'rating'|'recent'
  page?: number
  per_page?: number
}

export type GuideFilters = {
  q?: string
  city?: string
  language?: string
  specialty?: string
  verified?: boolean
  min_price?: number
  max_price?: number
  sort?: 'rating'|'price'|'recent'
  page?: number
  per_page?: number
}

export type ArticleFilters = {
  q?: string
  category?: string
  tag?: string
  sort?: 'recent'|'popular'
  page?: number
  per_page?: number
}

export type EventFilters = {
  q?: string
  city?: string
  category?: string
  from?: string
  to?: string
  featured?: boolean
  sort?: 'date'|'recent'
  page?: number
  per_page?: number
}
