export type Seo = { title: string; description?: string; image?: string|null; path?: string }

export type Place = {
  id: number
  title: string
  slug: string
  type: 'city'|'site'|'museum'|'park'|'beach'|'culture'|'history'|'gastronomy'|'adventure'|'other'
  description: string
  city: string
  country: string
  lat: number
  lng: number
  price_from?: number|null
  opening_hours?: Record<string,string>|null
  tags: string[]
  cover_image_url?: string|null
  rating_avg: number
  review_count: number
  featured: boolean
  status: 'draft'|'published'
  seo?: Seo
}

export type Accommodation = {
  id: number
  title: string
  slug: string
  type: 'hotel'|'guesthouse'|'ecolodge'|'bnb'|'other'
  description: string
  address: string
  city: string
  lat: number
  lng: number
  price_per_night: number
  currency: string
  amenities: string[]
  capacity: number
  rating_avg: number
  review_count: number
  featured: boolean
  status: 'draft'|'published'
  cover_image_url?: string|null
  seo?: Seo
}

export type Guide = {
  id: number
  name: string
  slug: string
  languages: string[]
  specialties: string[]
  bio: string
  avatar_url?: string|null
  city: string
  lat?: number|null
  lng?: number|null
  price_per_day?: number|null
  currency: string
  verified: boolean
  rating_avg: number
  review_count: number
  status: 'draft'|'published'
  seo?: Seo
}

export type Article = {
  id: number
  title: string
  slug: string
  excerpt: string
  body: string
  author_name: string
  category: string
  tags: string[]
  cover_image_url?: string|null
  status: 'draft'|'published'
  published_at?: string|null
  seo?: Seo
}

export type Event = {
  id: number
  title: string
  slug: string
  place_id?: number|null
  city: string
  start_date: string
  end_date: string
  organizer_name?: string|null
  organizer_contact?: string|null
  description: string
  price?: number|null
  currency: string
  category: string
  cover_image_url?: string|null
  status: 'draft'|'published'
  featured: boolean
  seo?: Seo
}
