export type BookingStatus = 'pending'|'authorized'|'confirmed'|'cancelled'|'refunded'

export type Booking = {
  id: number
  offering: { id: number; title: string; slug?: string }
  user?: { id: number; name: string; email?: string } | null
  start_date: string
  end_date?: string | null
  guests: number
  status: BookingStatus
  currency: string
  amount: number
  commission_amount?: number
  receipt_url?: string | null
  created_at?: string
}

export type OfferingStatus = 'draft'|'published'

export type Offering = {
  id: number
  title: string
  slug: string
  description?: string
  price: number
  currency: string
  capacity?: number
  cover_image_url?: string | null
  gallery?: { src: string; alt: string; width?: number; height?: number }[]
  status: OfferingStatus
  availability_json?: Record<string, any> | null
}

export type ProviderUser = {
  id: number
  name: string
  company?: string | null
  email: string
  phone?: string | null
  status: 'pending'|'approved'|'rejected'
  kyc_submitted?: boolean
  kyc_verified?: boolean
  created_at?: string
}

export type Payout = {
  id: number
  date: string
  amount: number
  currency: string
  status: 'pending'|'paid'|'failed'
  reference: string
}

export type Thread = {
  id: number
  subject: string
  unread_count: number
  last_message_preview: string
  updated_at: string
}

export type Message = {
  id: number
  thread_id: number
  author: { id: number; name: string }
  body: string
  created_at: string
}

export type User = {
  id?: number
  name: string
  email: string
  phone?: string | null
  roles?: Array<{ name: string }>|string[]
  provider_status?: 'none'|'pending'|'approved'|'rejected'|'suspended'
  business_name?: string | null
  bio?: string | null

  avatar_url?: string | null
  cover_image_url?: string | null
  date_of_birth?: string | null
  age?: number | null
  gender?: 'male' | 'female' | 'other' | 'prefer_not_to_say' | null
  country?: string | null
  city?: string | null
  full_location?: string
  address?: string | null
  postal_code?: string | null
  website_url?: string | null
  social_links?: {
    facebook?: string
    instagram?: string
    twitter?: string
    linkedin?: string
  } | null
  preferences?: {
    language?: 'fr' | 'en'
    currency?: 'XOF' | 'EUR' | 'USD'
    notifications_enabled?: boolean
  } | null
  about_me?: string | null
}
