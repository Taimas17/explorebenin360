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

export type Review = {
  id: number
  reviewable_type: string
  reviewable_id: number
  user: { id: number; name: string }
  booking_id?: number | null
  rating: number
  title?: string | null
  body: string
  response?: string | null
  responder?: { id: number; name: string } | null
  response_at?: string | null
  helpful_count: number
  verified_purchase: boolean
  status: 'pending' | 'published' | 'rejected'
  is_editable: boolean
  created_at: string
}

export type ReviewsResponse = {
  data: Review[]
  meta: {
    total: number
    current_page: number
    per_page: number
    average_rating: number
    total_reviews: number
  }
}

export type ReviewsSummary = {
  average: number
  total: number
  '5_star': number
  '4_star': number
  '3_star': number
  '2_star': number
  '1_star': number
}
